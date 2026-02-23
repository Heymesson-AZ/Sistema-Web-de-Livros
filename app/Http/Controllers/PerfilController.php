<?php

// Controlador responsável por lidar com as ações relacionadas ao perfil do usuário,
// incluindo a exibição do formulário de edição de perfil, a atualização das informações do perfil e
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;


class PerfilController extends Controller
{
    /**
     * View para editar o perfil do usuário.
     */
    public function edit(Request $request): View
    {
        if ($request->user()->tipo === 'cliente') {
            return view('cliente.perfil-editar', [
                'user' => $request->user(),
            ]);
        } elseif ($request->user()->tipo === 'vendedor') {
            return view('vendedor.perfil-editar', [
                'user' => $request->user(),
            ]);
        } else {
            abort(403, 'Acesso negado.');
        }
    }

    /**
     * Alterar as informações do perfil do usuário.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Atualiza os dados básicos
        $user->fill($request->validated());

        // Se o email foi alterado, precisamos resetar a verificação de email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        // 2. Atualiza os dados específicos (Cliente ou Vendedor)
        if ($user->tipo === 'vendedor') {

            $user->vendedor()->update($request->only([
                'telefone',
                'razao_social',
                'nome_fantasia',
                'inscricao_estadual'
            ]));
        } elseif ($user->tipo === 'cliente') {
            // Buscamos o registro do cliente e atualizamos apenas o telefone
            $user->cliente()->update($request->only([
                'telefone'
            ]));
        }

        return Redirect::route($user->tipo . '.perfil.editar')->with('status', 'perfil-atualizado');
    }

    /**
     * deletar a conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Validação de segurança (exige a senha atual para deletar)
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->temPedidosAtivos()) {
            return back()->withErrors([
                'DeleteUsuario' => 'Você possui pedidos em andamento (pendentes, processando ou enviados) e não pode excluir sua conta agora.'
            ]);
        }

        // 2. Realiza o Logout
        Auth::logout();
        // 3. Deleta o usuário
        // Graças ao "onDelete('cascade')" nas suas migrações, 
        // o Cliente ou Vendedor ligado a ele também será apagado!
        $user->delete();
        // 4. Invalida a sessão e redireciona
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}
