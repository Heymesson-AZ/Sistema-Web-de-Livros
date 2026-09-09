<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\Vendedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LivroAdminController extends Controller
{
    /**
     * Listar todos os livros com busca, filtros e indicadores analíticos (KPIs).
     */
    public function listar(Request $request): View
    {
        $query = Livro::with(['autor', 'genero', 'editora', 'vendedor']);

        // Busca textual (título, ISBN ou nome do autor)
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

        // Filtro por Vendedor
        if ($vendedorId = $request->input('vendedor_id')) {
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

        // Indicadores (KPIs)
        $totalLivros = Livro::count();
        $totalComEstoque = Livro::disponiveis()->count();
        $totalSemEstoque = Livro::where('quantidade', '<=', 0)->count();
        $valorTotalEstoque = Livro::sum(DB::raw('preco * quantidade'));

        // Dados auxiliares para filtros
        $generos = Genero::orderBy('nome')->get();
        $editoras = Editora::orderBy('nome')->get();
        $vendedores = Vendedor::with('user')->orderBy('nome_fantasia')->get();

        return view('administrador.livros.listar', [
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
     * Exibir formulário de cadastro de novo livro.
     */
    public function cadastrar(): View
    {
        return view('administrador.livros.cadastrar', [
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
            'vendedores' => Vendedor::with('user')->where('status_aprovacao', 'aprovado')->orderBy('nome_fantasia')->get(),
        ]);
    }

    /**
     * Salvar um novo livro no banco de dados.
     */
    public function salvar(Request $request): RedirectResponse
    {
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
            'vendedor_id' => ['required', 'exists:vendedores,id'],
            'sinopse' => ['nullable', 'string', 'max:5000'],
            'capa' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'titulo.required' => 'O título do livro é obrigatório.',
            'titulo.unique' => 'Já existe um livro cadastrado com este título para o autor selecionado.',
            'isbn.required' => 'O código ISBN é obrigatório.',
            'isbn.unique' => 'Já existe um livro cadastrado com este ISBN.',
            'data_publicacao.required' => 'A data de publicação é obrigatória.',
            'preco.required' => 'O preço do livro é obrigatório.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'quantidade.required' => 'A quantidade em estoque é obrigatória.',
            'autor_id.required' => 'Selecione um autor válido.',
            'genero_id.required' => 'Selecione um gênero literário válido.',
            'editora_id.required' => 'Selecione uma editora válida.',
            'vendedor_id.required' => 'Selecione o vendedor responsável.',
            'capa.image' => 'O arquivo de capa deve ser uma imagem válida.',
            'capa.max' => 'A imagem da capa não pode ultrapassar 2MB.',
        ]);

        if ($request->hasFile('capa')) {
            $dados['capa'] = $request->file('capa')->store('capas', 'public');
        }

        Livro::create($dados);

        return redirect()->route('admin.livros.index')
            ->with('status', 'Livro cadastrado com sucesso no catálogo!');
    }

    /**
     * Exibir os detalhes completos de um livro no painel administrativo.
     */
    public function detalhes(Livro $livro): View
    {
        $livro->load(['autor', 'genero', 'editora', 'vendedor.user', 'itensPedido']);

        $totalVendido = (int) $livro->itensPedido()->sum('quantidade_itens');
        $receitaGerada = (float) $livro->itensPedido()->sum(DB::raw('valor_unitario * quantidade_itens'));

        return view('administrador.livros.detalhes', [
            'livro' => $livro,
            'totalVendido' => $totalVendido,
            'receitaGerada' => $receitaGerada,
        ]);
    }

    /**
     * Exibir o formulário de edição de um livro existente.
     */
    public function editar(Livro $livro): View
    {
        return view('administrador.livros.editar', [
            'livro' => $livro,
            'autores' => Autor::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
            'vendedores' => Vendedor::with('user')->where('status_aprovacao', 'aprovado')->orderBy('nome_fantasia')->get(),
        ]);
    }

    /**
     * Atualizar os dados do livro no banco de dados.
     */
    public function atualizar(Request $request, Livro $livro): RedirectResponse
    {
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
            'vendedor_id' => ['required', 'exists:vendedores,id'],
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
            'autor_id.required' => 'Selecione um autor válido.',
            'genero_id.required' => 'Selecione um gênero literário válido.',
            'editora_id.required' => 'Selecione uma editora válida.',
            'vendedor_id.required' => 'Selecione o vendedor responsável.',
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

        return redirect()->route('admin.livros.index')
            ->with('status', 'Livro atualizado com sucesso!');
    }

    /**
     * Excluir (Soft Delete) o livro do catálogo.
     */
    public function deletar(Livro $livro): RedirectResponse
    {
        $titulo = $livro->titulo;
        $livro->delete();

        return redirect()->route('admin.livros.index')
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

