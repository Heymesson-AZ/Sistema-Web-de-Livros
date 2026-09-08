<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmarSenhaController extends Controller
{
    /**
     * Exibe a tela de confirmação de senha.
     */
    public function exibirConfirmacao(): View
    {
        return view('auth.confirmar-senha');
    }

    /**
     * Valida e confirma a senha do usuário.
     */
    public function confirmar(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function show(): View
    {
        return $this->exibirConfirmacao();
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->confirmar($request);
    }
}
