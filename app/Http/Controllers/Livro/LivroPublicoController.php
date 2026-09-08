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
        // 1. Livros em destaque para o Carrossel (sempre exibido no topo)
        $destaques = Livro::disponiveis()
            ->with(['autor', 'genero', 'vendedor'])
            ->latest()
            ->take(12)
            ->get();

        if ($destaques->isEmpty()) {
            $destaques = Livro::with(['autor', 'genero', 'vendedor'])->take(12)->get();
        }

        // 2. Consulta de livros do catálogo aplicando filtros (busca, gênero, faixa de preço, estoque, ordem)
        $livros = Livro::query()
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
        $totalLivros = Livro::count();
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
}

