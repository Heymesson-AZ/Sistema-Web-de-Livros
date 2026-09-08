<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SenhaController extends Controller
{
    /**
     * Atualiza a senha do usuário autenticado.
     */
    public function atualizar(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'senha atualizada');
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function update(Request $request): RedirectResponse
    {
        return $this->atualizar($request);
    }
}
