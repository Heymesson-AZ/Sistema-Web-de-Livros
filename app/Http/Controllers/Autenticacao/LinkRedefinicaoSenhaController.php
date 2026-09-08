<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class LinkRedefinicaoSenhaController extends Controller
{
    /**
     * Redireciona para a tela inicial (onde o modal de esqueci a senha está disponível).
     */
    public function exibirFormulario(): RedirectResponse
    {
        return redirect('/');
    }

    /**
     * Envia o link seguro de redefinição de senha para o e-mail solicitado.
     */
    public function enviarLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
                    ->with('form_sucesso', 'recuperar_senha')
                    ->with('email_enviado', $request->email)
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function create(): RedirectResponse
    {
        return $this->exibirFormulario();
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->enviarLink($request);
    }
}
