<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    /**
     * Alternar livro nos favoritos do usuário (adiciona se não existir, remove se já existir).
     */
    public function toggle(Request $request, Livro $livro): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'sucesso' => false,
                    'requer_login' => true,
                    'mensagem' => 'Faça login para salvar seus livros favoritos.',
                ], 401);
            }
            return redirect()->route('entrar')->with('error', 'Faça login para salvar seus favoritos.');
        }

        $favoritoExistente = Favorito::where('user_id', $user->id)
            ->where('livro_id', $livro->id)
            ->first();

        if ($favoritoExistente) {
            $favoritoExistente->delete();
            $favoritado = false;
            $mensagem = "'{$livro->titulo}' foi removido dos seus favoritos.";
        } else {
            Favorito::create([
                'user_id' => $user->id,
                'livro_id' => $livro->id,
            ]);
            $favoritado = true;
            $mensagem = "'{$livro->titulo}' foi adicionado aos seus favoritos!";
        }

        $totalFavoritos = Favorito::where('user_id', $user->id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'favoritado' => $favoritado,
                'mensagem' => $mensagem,
                'total_favoritos' => $totalFavoritos,
            ]);
        }

        return back()->with('status', $mensagem);
    }

    /**
     * Remover diretamente um exemplar dos favoritos.
     */
    public function remover(Request $request, Livro $livro): RedirectResponse
    {
        Favorito::where('user_id', $request->user()->id)
            ->where('livro_id', $livro->id)
            ->delete();

        return back()->with('status', 'Item removido da sua lista de favoritos.');
    }
}

