<?php

namespace App\Http\Controllers\Vendedor;

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

class LivroVendedorController extends Controller
{
    /**
     * Recupera o perfil de vendedor autenticado ou aborta se não autorizado.
     */
    protected function getVendedorAutenticado(): Vendedor
    {
        $user = Auth::user();
        $vendedor = $user?->vendedor;

        if (!$vendedor || !$vendedor->isAprovado()) {
            abort(403, 'Acesso restrito. Sua loja precisa estar ativa e aprovada pela administração.');
        }

        return $vendedor;
    }

    /**
     * Listar os livros do vendedor autenticado com busca, filtros e KPIs.
     */
    public function listar(Request $request): View
    {
        $vendedor = $this->getVendedorAutenticado();

        $query = Livro::where('vendedor_id', $vendedor->id)
            ->with(['autor', 'genero', 'editora']);

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

        // Indicadores do vendedor
        $totalLivros = Livro::where('vendedor_id', $vendedor->id)->count();
        $totalComEstoque = Livro::where('vendedor_id', $vendedor->id)->where('quantidade', '>', 0)->count();
        $totalSemEstoque = Livro::where('vendedor_id', $vendedor->id)->where('quantidade', '<=', 0)->count();
        $valorTotalEstoque = Livro::where('vendedor_id', $vendedor->id)->sum(DB::raw('preco * quantidade'));

        $generos = Genero::orderBy('nome')->get();
        $editoras = Editora::orderBy('nome')->get();

        return view('vendedor.livros.listar', [
            'vendedor' => $vendedor,
            'livros' => $livros,
            'totalLivros' => $totalLivros,
            'totalComEstoque' => $totalComEstoque,
            'totalSemEstoque' => $totalSemEstoque,
            'valorTotalEstoque' => $valorTotalEstoque,
            'generos' => $generos,
            'editoras' => $editoras,
        ]);
    }

    /**
     * Exibir formulário de cadastro de novo livro na loja do vendedor.
     */
    public function cadastrar(): View
    {
        $vendedor = $this->getVendedorAutenticado();

        return view('vendedor.livros.cadastrar', [
            'vendedor' => $vendedor,
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
        ]);
    }

    /**
     * Salvar um novo livro pertencente à loja do vendedor.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $vendedor = $this->getVendedorAutenticado();

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
        if ($request->has('isbn')) {
            $request->merge(['isbn' => strtoupper(trim(preg_replace('/[^0-9Xx]/', '', (string) $request->input('isbn'))))]);
        }

        $dados = $request->validate([
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
        ], [
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
            'capa.image' => 'O arquivo da capa deve ser uma imagem válida.',
            'capa.max' => 'A imagem da capa não pode ultrapassar 2MB.',
        ]);

        $dados['vendedor_id'] = $vendedor->id;

        if ($request->hasFile('capa')) {
            $dados['capa'] = $request->file('capa')->store('capas', 'public');
        }

        Livro::create($dados);

        return redirect()->route('vendedor.livros.index')
            ->with('status', 'Livro publicado com sucesso na sua loja!');
    }

    /**
     * Exibir detalhes do livro pertencente ao vendedor.
     */
    public function detalhes(Livro $livro): View
    {
        $vendedor = $this->getVendedorAutenticado();
        abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para visualizar este livro.');

        $livro->load(['autor', 'genero', 'editora', 'itensPedido']);

        $totalVendido = (int) $livro->itensPedido()->sum('quantidade_itens');
        $receitaGerada = (float) $livro->itensPedido()->sum(DB::raw('valor_unitario * quantidade_itens'));

        return view('vendedor.livros.detalhes', [
            'vendedor' => $vendedor,
            'livro' => $livro,
            'totalVendido' => $totalVendido,
            'receitaGerada' => $receitaGerada,
        ]);
    }

    /**
     * Exibir formulário de edição do livro da loja do vendedor.
     */
    public function editar(Livro $livro): View
    {
        $vendedor = $this->getVendedorAutenticado();
        abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para editar este livro.');

        return view('vendedor.livros.editar', [
            'vendedor' => $vendedor,
            'livro' => $livro,
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
        ]);
    }

    /**
     * Atualizar os dados do livro da loja do vendedor.
     */
    public function atualizar(Request $request, Livro $livro): RedirectResponse
    {
        $vendedor = $this->getVendedorAutenticado();
        abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para atualizar este livro.');

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
        if ($request->has('isbn')) {
            $request->merge(['isbn' => strtoupper(trim(preg_replace('/[^0-9Xx]/', '', (string) $request->input('isbn'))))]);
        }

        $dados = $request->validate([
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
        ], [
            'titulo.required' => 'O título do livro é obrigatório.',
            'titulo.unique' => 'Já existe um livro cadastrado com este título para o autor selecionado.',
            'isbn.required' => 'O código ISBN é obrigatório.',
            'isbn.unique' => 'Já existe um livro cadastrado com este ISBN.',
            'data_publicacao.required' => 'A data de publicação é obrigatória.',
            'preco.required' => 'O preço do livro é obrigatório.',
            'quantidade.required' => 'A quantidade em estoque é obrigatória.',
            'autor_id.required' => 'Selecione um autor.',
            'genero_id.required' => 'Selecione um gênero.',
            'editora_id.required' => 'Selecione uma editora.',
            'capa.image' => 'O arquivo de capa deve ser uma imagem válida.',
            'capa.max' => 'A imagem da capa não pode ultrapassar 2MB.',
        ]);

        if ($request->hasFile('capa')) {
            if ($livro->capa && Storage::disk('public')->exists($livro->capa)) {
                Storage::disk('public')->delete($livro->capa);
            }
            $dados['capa'] = $request->file('capa')->store('capas', 'public');
        } elseif ($request->boolean('remover_capa')) {
            if ($livro->capa && Storage::disk('public')->exists($livro->capa)) {
                Storage::disk('public')->delete($livro->capa);
            }
            $dados['capa'] = null;
        }

        $livro->update($dados);

        return redirect()->route('vendedor.livros.index')
            ->with('status', 'Livro atualizado com sucesso!');
    }

    /**
     * Excluir (Soft Delete) o livro da loja do vendedor.
     */
    public function deletar(Livro $livro): RedirectResponse
    {
        $vendedor = $this->getVendedorAutenticado();
        abort_if($livro->vendedor_id !== $vendedor->id, 403, 'Você não tem permissão para excluir este livro.');

        $titulo = $livro->titulo;
        $livro->delete();

        return redirect()->route('vendedor.livros.index')
            ->with('status', "Livro '{$titulo}' foi removido do catálogo com sucesso.");
    }

    // =========================================================================
    // WRAPPERS DE RECURSO PADRÃO (RESOURCE METHODS)
    // =========================================================================

    public function index(Request $request): View
    {
        return $this->listar($request);
    }

    public function create(): View
    {
        return $this->cadastrar();
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->salvar($request);
    }

    public function show(Livro $livro): View
    {
        return $this->detalhes($livro);
    }

    public function edit(Livro $livro): View
    {
        return $this->editar($livro);
    }

    public function update(Request $request, Livro $livro): RedirectResponse
    {
        return $this->atualizar($request, $livro);
    }

    public function destroy(Livro $livro): RedirectResponse
    {
        return $this->deletar($livro);
    }
}

