<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\Livro;
use App\Models\Genero;
use App\Models\Editora;
use Illuminate\Http\Request;

class LivroPublicoController extends Controller
{
    /**
     * Exibe a vitrine de livros e o catálogo filtrável estilo Amazon na página inicial.
     */
    public function index(Request $request)
    {
        // 1. Livros em destaque para o Carrossel (somente disponíveis em estoque)
        $destaques = Livro::disponiveis()
            ->with(['autor', 'genero', 'vendedor'])
            ->latest()
            ->take(12)
            ->get();

        // 2. Consulta de livros do catálogo aplicando filtros (somente títulos disponíveis em estoque)
        $livros = Livro::disponiveis()
            ->with(['autor', 'genero', 'editora', 'vendedor'])
            ->filtrar($request->all())
            ->paginate(12)
            ->withQueryString();

        // 3. Gêneros com contagem de livros para a barra lateral de filtros
        $generos = Genero::withCount('livros')
            ->having('livros_count', '>', 0)
            ->orderBy('nome')
            ->get();

        // 4. Editoras principais para os filtros
        $editoras = Editora::withCount('livros')
            ->having('livros_count', '>', 0)
            ->orderBy('nome')
            ->take(10)
            ->get();

        // Estatísticas para os filtros
        $totalLivros = Livro::ativos()->count();
        $totalDisponiveis = Livro::disponiveis()->count();

        return view('paginas.inicio', compact(
            'destaques',
            'livros',
            'generos',
            'editoras',
            'totalLivros',
            'totalDisponiveis'
        ));
    }

    /**
     * Exibe os detalhes completos de um livro para o usuário/visitante.
     */
    public function show(Livro $livro)
    {
        // Se o livro estiver sob análise, bloqueado ou banido, bloqueia acesso público
        if (!$livro->isAtivo() && !(auth()->check() && auth()->user()->isAdmin())) {
            abort(404, 'Este título não está disponível no catálogo da loja.');
        }

        $livro->load(['autor', 'genero', 'editora', 'vendedor.user']);

        // Livros relacionados do mesmo gênero ou autor
        $relacionados = Livro::disponiveis()
            ->where('id', '!=', $livro->id)
            ->where(function ($q) use ($livro) {
                if ($livro->genero_id) {
                    $q->where('genero_id', $livro->genero_id);
                }
                if ($livro->autor_id) {
                    $q->orWhere('autor_id', $livro->autor_id);
                }
            })
            ->with(['autor', 'genero', 'vendedor'])
            ->take(6)
            ->get();

        return view('livros.detalhes', compact('livro', 'relacionados'));
    }

    /**
     * Endpoint de busca dinâmica para sugestões rápidas via fetch/AJAX.
     */
    public function buscaRapida(Request $request)
    {
        $termo = trim($request->input('q', $request->input('busca', '')));

        if (mb_strlen($termo) < 2) {
            return response()->json([]);
        }

        $resultados = Livro::query()
            ->ativos()
            ->with(['autor', 'genero'])
            ->where(function ($q) use ($termo) {
                $q->where('titulo', 'like', "%{$termo}%")
                  ->orWhere('isbn', 'like', "%{$termo}%")
                  ->orWhereHas('autor', function ($qa) use ($termo) {
                      $qa->where('nome', 'like', "%{$termo}%");
                  });
            })
            ->latest()
            ->take(6)
            ->get();

        $dados = $resultados->map(function ($livro) {
            return [
                'id' => $livro->id,
                'titulo' => $livro->titulo,
                'autor' => $livro->autor?->nome ?? 'Autor Desconhecido',
                'genero' => $livro->genero?->nome ?? '',
                'preco_formatado' => $livro->preco_formatado,
                'capa_url' => $livro->url_capa,
                'url' => route('livros.detalhes', $livro),
            ];
        });

        return response()->json($dados);
    }
}

