<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificacaoVerificacaoEmailController extends Controller
{
    /**
     * Reenvia a notificação com link para verificação de e-mail.
     */
    public function reenviar(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function store(Request $request): RedirectResponse
    {
        return $this->reenviar($request);
    }
}
