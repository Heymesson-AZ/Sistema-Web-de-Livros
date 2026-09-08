<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RedefinicaoSenhaController extends Controller
{
    /**
     * Exibe o formulário de definição de nova senha.
     */
    public function exibirFormulario(Request $request): View
    {
        return view('auth.redefinir-senha', ['request' => $request]);
    }

    /**
     * Processa a redefinição de senha com o token recebido.
     */
    public function redefinirSenha(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function create(Request $request): View
    {
        return $this->exibirFormulario($request);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->redefinirSenha($request);
    }
}
