<?php

// Controlador responsável por lidar com a autenticação de usuários,
// incluindo a exibição do formulário de login, o processamento dos dados de login e o encerramento da sessão do usuário.
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrando a tela de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     *  Nessa função, autenticamos o usuário usando os dados fornecidos 
     * na solicitação de login.
     */
    
    public function store(LoginRequest $request): RedirectResponse
    {
        // Antes de tentar autenticar o usuário, verificamos se a solicitação não está sendo
        $request->authenticate();
        // Se a autenticação for bem-sucedida, limpamos as tentativas de login para o usuário.
        $request->session()->regenerate();
        // Redirecionamos o usuário para a página pretendida ou para a rota 'dashboard' após o login bem-sucedido.
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Aqui, encerramos a sessão do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
