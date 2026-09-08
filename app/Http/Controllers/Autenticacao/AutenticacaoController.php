<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autenticacao\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutenticacaoController extends Controller
{
    /**
     * Redireciona para a tela inicial (onde o modal de login está disponível).
     */
    public function exibirLogin(): RedirectResponse
    {
        return redirect('/');
    }

    /**
     * Autentica o usuário com base nas credenciais fornecidas.
     */
    public function autenticar(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Encerra a sessão do usuário (Logout).
     */
    public function encerrarSessao(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function create(): RedirectResponse
    {
        return $this->exibirLogin();
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        return $this->autenticar($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        return $this->encerrarSessao($request);
    }
}
