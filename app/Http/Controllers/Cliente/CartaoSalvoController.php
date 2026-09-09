<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CartaoSalvo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartaoSalvoController extends Controller
{
    /**
     * Salvar um novo cartão de crédito seguro e simulado para o usuário.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'numero_cartao' => ['required', 'string', 'min:14', 'max:23'],
            'nome_titular' => ['required', 'string', 'max:100'],
            'validade' => ['required', 'string', 'max:7'],
            'padrao' => ['nullable', 'boolean'],
        ], [
            'numero_cartao.required' => 'Informe o número do cartão.',
            'nome_titular.required' => 'Informe o nome impresso no cartão.',
            'validade.required' => 'Informe a validade do cartão.',
        ]);

        $user = $request->user();
        $numLimpo = preg_replace('/\D/', '', $dados['numero_cartao']);
        $ultimosDigitos = substr($numLimpo, -4) ?: '0000';

        // Detecção de bandeira
        $bandeira = 'visa';
        if (str_starts_with($numLimpo, '5')) {
            $bandeira = 'mastercard';
        } elseif (str_starts_with($numLimpo, '4')) {
            $bandeira = 'visa';
        } elseif (str_starts_with($numLimpo, '6') || str_starts_with($numLimpo, '50')) {
            $bandeira = 'elo';
        } elseif (str_starts_with($numLimpo, '3')) {
            $bandeira = 'amex';
        }

        $definirPadrao = $request->boolean('padrao') || $user->cartoesSalvos()->count() === 0;

        DB::transaction(function () use ($user, $ultimosDigitos, $bandeira, $definirPadrao) {
            if ($definirPadrao) {
                $user->cartoesSalvos()->update(['cartao_padrao' => false]);
            }

            CartaoSalvo::create([
                'user_id' => $user->id,
                'token_cartao' => 'tok_' . bin2hex(random_bytes(12)),
                'ultimos_digitos' => $ultimosDigitos,
                'bandeira_cartao' => $bandeira,
                'cartao_padrao' => $definirPadrao,
            ]);
        });

        return redirect()->route('painel', ['tab' => 'cartoes'])
            ->with('status', 'Cartão salvo com sucesso para compras rápidas!');
    }

    /**
     * Definir cartão como padrão do usuário.
     */
    public function definirPadrao(Request $request, CartaoSalvo $cartao): RedirectResponse
    {
        if ($cartao->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        DB::transaction(function () use ($request, $cartao) {
            $request->user()->cartoesSalvos()->update(['cartao_padrao' => false]);
            $cartao->update(['cartao_padrao' => true]);
        });

        return redirect()->route('painel', ['tab' => 'cartoes'])
            ->with('status', 'Cartão padrão atualizado com sucesso.');
    }

    /**
     * Excluir cartão salvo.
     */
    public function deletar(Request $request, CartaoSalvo $cartao): RedirectResponse
    {
        if ($cartao->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $eraPadrao = $cartao->cartao_padrao;
        $user = $request->user();

        DB::transaction(function () use ($user, $cartao, $eraPadrao) {
            $cartao->delete();

            if ($eraPadrao) {
                $proximo = $user->cartoesSalvos()->first();
                if ($proximo) {
                    $proximo->update(['cartao_padrao' => true]);
                }
            }
        });

        return redirect()->route('painel', ['tab' => 'cartoes'])
            ->with('status', 'Cartão removido da sua conta.');
    }
}

