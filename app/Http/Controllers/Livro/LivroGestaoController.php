<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\Vendedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivroGestaoController extends Controller
{
    /**
     * Define se a instância do controlador está operando em contexto administrativo
     */
    protected bool $isAdmin = false;

    /**
     * Retorna o perfil de vendedor autenticado ou aborta com 403 se não autorizado.
     */
    protected function getVendedorAutenticado(): ?Vendedor
    {
        if ($this->isAdmin) {
            return null;
        }

        $user = Auth::user();
        $vendedor = $user?->vendedor;

        if (!$vendedor || !$vendedor->isAprovado()) {
            abort(403, 'Acesso restrito. Sua loja precisa estar ativa e aprovada pela administração.');
        }

        return $vendedor;
    }

    /**
     * Retorna o prefixo de rota correspondente ao perfil (admin.livros ou vendedor.livros)
     */
    protected function getRotaPrefix(): string
    {
        return $this->isAdmin ? 'admin.livros' : 'vendedor.livros';
    }

    /**
     * Listar os livros com busca, filtros, ordenação e KPIs analíticos.
     */
    public function index(Request $request): View
    {
        return $this->listar($request);
    }

    public function listar(Request $request): View
    {
        $vendedor = $this->getVendedorAutenticado();

        $query = Livro::with(['autor', 'genero', 'editora']);

        if ($this->isAdmin) {
            $query->with('vendedor.user');
        } else {
            $query->where('vendedor_id', $vendedor->id);
        }

        // Busca textual
        if ($termo = trim($request->input('busca', ''))) {
            $query->where(function ($q) use ($termo) {
                $q->where('titulo', 'like', "%{$termo}%")
                  ->orWhere('isbn', 'like', "%{$termo}%")
                  ->orWhere('sinopse', 'like', "%{$termo}%")
                  ->orWhereHas('autor', function ($sub) use ($termo) {
                      $sub->where('nome', 'like', "%{$termo}%");
                  });
            });
        }

        // Filtro por Gênero
        if ($generoId = $request->input('genero_id')) {
            $query->where('genero_id', $generoId);
        }

        // Filtro por Editora
        if ($editoraId = $request->input('editora_id')) {
            $query->where('editora_id', $editoraId);
        }

        // Filtro por Vendedor (Apenas Administrador)
        if ($this->isAdmin && ($vendedorId = $request->input('vendedor_id'))) {
            $query->where('vendedor_id', $vendedorId);
        }

        // Filtro por Estoque
        if ($statusEstoque = $request->input('status_estoque')) {
            if ($statusEstoque === 'com_estoque') {
                $query->where('quantidade', '>', 0);
            } elseif ($statusEstoque === 'sem_estoque') {
                $query->where('quantidade', '<=', 0);
            }
        }

        // Ordenação
        $ordem = $request->input('ordem', 'recentes');
        match ($ordem) {
            'antigos'      => $query->oldest(),
            'preco_asc'    => $query->orderBy('preco', 'asc'),
            'preco_desc'   => $query->orderBy('preco', 'desc'),
            'titulo_asc'   => $query->orderBy('titulo', 'asc'),
            'estoque_desc' => $query->orderBy('quantidade', 'desc'),
            default        => $query->latest(),
        };

        $livros = $query->paginate(12)->withQueryString();

        // Cálculo de KPIs
        $baseKpiQuery = $this->isAdmin ? Livro::query() : Livro::where('vendedor_id', $vendedor->id);
        $totalLivros = (clone $baseKpiQuery)->count();
        $totalComEstoque = (clone $baseKpiQuery)->where('quantidade', '>', 0)->count();
        $totalSemEstoque = (clone $baseKpiQuery)->where('quantidade', '<=', 0)->count();
        $valorTotalEstoque = (clone $baseKpiQuery)->sum(DB::raw('preco * quantidade'));

        $generos = Genero::orderBy('nome')->get();
        $editoras = Editora::orderBy('nome')->get();
        $vendedores = $this->isAdmin ? Vendedor::with('user')->orderBy('nome_fantasia')->get() : collect();

        return view('livros.gestao.listar', [
            'ehAdmin' => $this->isAdmin,
            'rotaPrefix' => $this->getRotaPrefix(),
            'vendedor' => $vendedor,
            'livros' => $livros,
            'totalLivros' => $totalLivros,
            'totalComEstoque' => $totalComEstoque,
            'totalSemEstoque' => $totalSemEstoque,
            'valorTotalEstoque' => $valorTotalEstoque,
            'generos' => $generos,
            'editoras' => $editoras,
            'vendedores' => $vendedores,
        ]);
    }

    /**
     * Exibir formulário de cadastro de livro.
     * Regra de Negócio: Administradores gerenciam o sistema e usuários; publicação de livros é exclusiva de vendedores.
     */
    public function create(): View|RedirectResponse
    {
        return $this->cadastrar();
    }

    public function cadastrar(): View|RedirectResponse
    {
        $vendedor = $this->isAdmin ? null : $this->getVendedorAutenticado();
        $vendedores = $this->isAdmin ? Vendedor::with('user')->where('status_aprovacao', 'aprovado')->orderBy('nome_fantasia')->get() : collect();

        return view('livros.gestao.cadastrar', [
            'ehAdmin' => $this->isAdmin,
            'rotaPrefix' => $this->getRotaPrefix(),
            'vendedor' => $vendedor,
            'vendedores' => $vendedores,
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
        ]);
    }

    /**
     * Salvar um novo livro no catálogo.
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->salvar($request);
    }

    public function salvar(Request $request): RedirectResponse
    {
        $vendedor = $this->isAdmin ? null : $this->getVendedorAutenticado();

        // Sanitização de preço
        if ($request->has('preco')) {
            $precoLimpo = str_replace(['R$', ' '], '', (string) $request->input('preco'));
            if (str_contains($precoLimpo, ',') && str_contains($precoLimpo, '.')) {
                $precoLimpo = str_replace('.', '', $precoLimpo);
                $precoLimpo = str_replace(',', '.', $precoLimpo);
            } elseif (str_contains($precoLimpo, ',')) {
                $precoLimpo = str_replace(',', '.', $precoLimpo);
            }
            $request->merge(['preco' => $precoLimpo]);
        }

        // Sanitização de ISBN
        if ($request->has('isbn')) {
            $request->merge(['isbn' => strtoupper(trim(preg_replace('/[^0-9Xx]/', '', (string) $request->input('isbn'))))]);
        }

        $regras = [
            'titulo' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('livros', 'titulo')
                    ->where(fn ($q) => $q->where('autor_id', $request->input('autor_id'))->whereNull('deleted_at')),
            ],
            'isbn' => ['required', 'string', 'max:30', 'unique:livros,isbn'],
            'data_publicacao' => ['required', 'date'],
            'preco' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'quantidade' => ['required', 'integer', 'min:0', 'max:999999'],
            'autor_id' => ['required', 'exists:autores,id'],
            'genero_id' => ['required', 'exists:generos,id'],
            'editora_id' => ['required', 'exists:editoras,id'],
            'sinopse' => ['nullable', 'string', 'max:5000'],
            'capa' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        if ($this->isAdmin) {
            $regras['vendedor_id'] = ['required', 'exists:vendedores,id'];
        }

        $mensagens = [
            'titulo.required' => 'O título do livro é obrigatório.',
            'titulo.unique' => 'Já existe um livro cadastrado com este título para o autor selecionado.',
            'isbn.required' => 'O código ISBN é obrigatório.',
            'isbn.unique' => 'Já existe um livro cadastrado com este ISBN na plataforma.',
            'data_publicacao.required' => 'A data de publicação é obrigatória.',
            'preco.required' => 'O preço do livro é obrigatório.',
            'preco.min' => 'O preço deve ser superior a zero.',
            'quantidade.required' => 'Informe a quantidade em estoque.',
            'autor_id.required' => 'Selecione o autor da obra.',
            'genero_id.required' => 'Selecione o gênero literário.',
            'editora_id.required' => 'Selecione a editora.',
            'vendedor_id.required' => 'Selecione o vendedor responsável.',
            'capa.image' => 'O arquivo da capa deve ser uma imagem válida.',
            'capa.max' => 'A imagem da capa não pode ultrapassar 2MB.',
        ];

        $dados = $request->validate($regras, $mensagens);

        if (!$this->isAdmin) {
            $dados['vendedor_id'] = $vendedor->id;
        }

        if ($request->hasFile('capa')) {
            $dados['capa'] = $request->file('capa')->store('capas', 'public');
        }

        Livro::create($dados);

        $destino = $this->isAdmin ? 'admin.livros.index' : 'vendedor.livros.index';

        return redirect()->route($destino)
            ->with('status', $this->isAdmin ? 'Livro cadastrado com sucesso no catálogo!' : 'Livro publicado com sucesso na sua loja!');
    }

    /**
     * Exibir detalhes de um livro no painel de gestão.
     */
    public function show(Livro $livro): View
    {
        return $this->detalhes($livro);
    }

    public function detalhes(Livro $livro): View
    {
        if (!$this->isAdmin) {
            $vendedor = $this->getVendedorAutenticado();
            abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para visualizar este livro.');
        } else {
            $vendedor = null;
        }

        $livro->load(['autor', 'genero', 'editora', 'vendedor.user', 'itensPedido']);

        $totalVendido = (int) $livro->itensPedido()->sum('quantidade_itens');
        $receitaGerada = (float) $livro->itensPedido()->sum(DB::raw('valor_unitario * quantidade_itens'));

        return view('livros.gestao.detalhes', [
            'ehAdmin' => $this->isAdmin,
            'rotaPrefix' => $this->getRotaPrefix(),
            'vendedor' => $vendedor,
            'livro' => $livro,
            'totalVendido' => $totalVendido,
            'receitaGerada' => $receitaGerada,
        ]);
    }

    /**
     * Exibir formulário de edição de um livro.
     */
    public function edit(Livro $livro): View
    {
        return $this->editar($livro);
    }

    public function editar(Livro $livro): View
    {
        if (!$this->isAdmin) {
            $vendedor = $this->getVendedorAutenticado();
            abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para editar este livro.');
        } else {
            $vendedor = null;
        }

        return view('livros.gestao.editar', [
            'ehAdmin' => $this->isAdmin,
            'rotaPrefix' => $this->getRotaPrefix(),
            'vendedor' => $vendedor,
            'livro' => $livro,
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
            'vendedores' => $this->isAdmin ? Vendedor::with('user')->where('status_aprovacao', 'aprovado')->orderBy('nome_fantasia')->get() : collect(),
        ]);
    }

    /**
     * Atualizar dados do livro.
     */
    public function update(Request $request, Livro $livro): RedirectResponse
    {
        return $this->atualizar($request, $livro);
    }

    public function atualizar(Request $request, Livro $livro): RedirectResponse
    {
        if (!$this->isAdmin) {
            $vendedor = $this->getVendedorAutenticado();
            abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para atualizar este livro.');
        }

        // Sanitização de preço
        if ($request->has('preco')) {
            $precoLimpo = str_replace(['R$', ' '], '', (string) $request->input('preco'));
            if (str_contains($precoLimpo, ',') && str_contains($precoLimpo, '.')) {
                $precoLimpo = str_replace('.', '', $precoLimpo);
                $precoLimpo = str_replace(',', '.', $precoLimpo);
            } elseif (str_contains($precoLimpo, ',')) {
                $precoLimpo = str_replace(',', '.', $precoLimpo);
            }
            $request->merge(['preco' => $precoLimpo]);
        }

        // Sanitização de ISBN
        if ($request->has('isbn')) {
            $request->merge(['isbn' => strtoupper(trim(preg_replace('/[^0-9Xx]/', '', (string) $request->input('isbn'))))]);
        }

        $regras = [
            'titulo' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('livros', 'titulo')
                    ->where(fn ($q) => $q->where('autor_id', $request->input('autor_id'))->whereNull('deleted_at'))
                    ->ignore($livro->id),
            ],
            'isbn' => ['required', 'string', 'max:30', Rule::unique('livros', 'isbn')->ignore($livro->id)],
            'data_publicacao' => ['required', 'date'],
            'preco' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'quantidade' => ['required', 'integer', 'min:0', 'max:999999'],
            'autor_id' => ['required', 'exists:autores,id'],
            'genero_id' => ['required', 'exists:generos,id'],
            'editora_id' => ['required', 'exists:editoras,id'],
            'sinopse' => ['nullable', 'string', 'max:5000'],
            'capa' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        if ($this->isAdmin) {
            $regras['vendedor_id'] = ['required', 'exists:vendedores,id'];
        }

        $mensagens = [
            'titulo.required' => 'O título do livro é obrigatório.',
            'titulo.unique' => 'Já existe um livro cadastrado com este título para o autor selecionado.',
            'isbn.required' => 'O código ISBN é obrigatório.',
            'isbn.unique' => 'Já existe um livro cadastrado com este ISBN.',
            'data_publicacao.required' => 'A data de publicação é obrigatória.',
            'preco.required' => 'O preço do livro é obrigatório.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'quantidade.required' => 'A quantidade em estoque é obrigatória.',
            'autor_id.required' => 'Selecione um autor válido.',
            'genero_id.required' => 'Selecione um gênero válido.',
            'editora_id.required' => 'Selecione uma editora válida.',
            'capa.image' => 'O arquivo de capa deve ser uma imagem válida.',
            'capa.max' => 'A capa não pode ultrapassar 2MB.',
        ];

        $dados = $request->validate($regras, $mensagens);

        if ($request->hasFile('capa')) {
            if ($livro->capa && Storage::disk('public')->exists($livro->capa)) {
                Storage::disk('public')->delete($livro->capa);
            }
            $dados['capa'] = $request->file('capa')->store('capas', 'public');
        }

        $livro->update($dados);

        $destino = $this->isAdmin ? 'admin.livros.index' : 'vendedor.livros.index';

        return redirect()->route($destino)
            ->with('status', 'Livro atualizado com sucesso!');
    }

    /**
     * Excluir livro (Soft Delete).
     */
    public function destroy(Livro $livro): RedirectResponse
    {
        return $this->excluir($livro);
    }

    public function excluir(Livro $livro): RedirectResponse
    {
        if (!$this->isAdmin) {
            $vendedor = $this->getVendedorAutenticado();
            abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para excluir este livro.');
        }

        $livro->delete();

        $destino = $this->isAdmin ? 'admin.livros.index' : 'vendedor.livros.index';

        return redirect()->route($destino)
            ->with('status', 'Livro removido do catálogo com sucesso!');
    }
}

