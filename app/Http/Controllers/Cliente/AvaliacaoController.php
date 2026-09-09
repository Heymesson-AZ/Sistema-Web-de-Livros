<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvaliacaoController extends Controller
{
    /**
     * Enviar ou atualizar avaliação de um pedido e vendedor.
     */
    public function salvar(Request $request, Pedido $pedido): RedirectResponse
    {
        $user = $request->user();
        $cliente = $user->cliente;

        if (!$cliente || $pedido->cliente_id !== $cliente->id) {
            abort(403, 'Apenas o comprador do pedido pode registrar uma avaliação.');
        }

        $dados = $request->validate([
            'avaliacao' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'recomenda' => ['nullable', 'boolean'],
        ], [
            'avaliacao.required' => 'Escolha uma nota de 1 a 5 estrelas.',
            'avaliacao.between' => 'A nota de avaliação deve ser entre 1 e 5 estrelas.',
        ]);

        Avaliacao::updateOrCreate(
            [
                'cliente_id' => $cliente->id,
                'pedido_id' => $pedido->id,
            ],
            [
                'vendedor_id' => $pedido->vendedor_id,
                'avaliacao' => (int) $dados['avaliacao'],
                'comentario' => $dados['comentario'] ? trim($dados['comentario']) : null,
                'recomenda' => $request->boolean('recomenda', true),
            ]
        );

        return redirect()->route('pedidos.show', $pedido)->with('status', 'Avaliação enviada com sucesso! Muito obrigado pelo feedback.');
    }

    /**
     * Excluir avaliação.
     */
    public function deletar(Request $request, Avaliacao $avaliacao): RedirectResponse
    {
        $user = $request->user();
        if ($avaliacao->cliente?->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $avaliacao->delete();

        return back()->with('status', 'Avaliação removida com sucesso.');
    }
}
