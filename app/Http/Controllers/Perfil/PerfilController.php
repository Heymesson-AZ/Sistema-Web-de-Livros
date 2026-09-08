<?php

namespace App\Http\Controllers\Perfil;

use App\Http\Controllers\Controller;
use App\Http\Requests\Perfil\AtualizarPerfilRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PerfilController extends Controller
{
    /**
     * Exibir formulário de edição de perfil do usuário (Cliente, Vendedor ou Administrador).
     */
    public function editar(Request $request)
    {
        $user = $request->user();

        if ($user->tipo === 'cliente') {
            return view('cliente.perfil-editar', [
                'user' => $user,
            ]);
        } elseif ($user->tipo === 'vendedor') {
            return view('vendedor.perfil-editar', [
                'user' => $user,
            ]);
        } elseif ($user->tipo === 'admin') {
            return view('admin.perfil-editar', [
                'user' => $user,
                'admin' => $user->admin,
            ]);
        }

        abort(403, 'Acesso negado.');
    }

    /**
     * Atualizar as informações do perfil do usuário.
     */
    public function atualizar(AtualizarPerfilRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Atualiza os dados básicos do usuário
        $user->fill($request->validated());

        // Se o e-mail foi alterado, reseta a verificação e envia notificação
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        // 2. Atualiza os dados específicos do papel (Cliente, Vendedor ou Admin)
        if ($user->tipo === 'vendedor') {
            $user->vendedor()->update([
                'telefone_comercial' => $request->telefone ?? $request->telefone_comercial,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'inscricao_estadual' => $request->inscricao_estadual,
            ]);
        } elseif ($user->tipo === 'cliente') {
            $user->cliente()->update([
                'celular_contato' => $request->telefone,
            ]);
        } elseif ($user->tipo === 'admin') {
            if ($user->admin) {
                $user->admin()->update($request->only([
                    'telefone_urgencia',
                ]));
            }
        }

        return Redirect::route($user->tipo . '.perfil.editar')->with('status', 'perfil-atualizado');
    }

    /**
     * Deletar a conta do usuário com travas de segurança.
     */
    public function deletar(Request $request): RedirectResponse
    {
        // 1. Validação de segurança (exige senha atual)
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Trava: Não permitir que o último administrador delete sua conta
        if ($user->tipo === 'admin' && User::where('tipo', 'admin')->count() <= 1) {
            return back()->withErrors([
                'DeleteUsuario' => 'Você é o único administrador do sistema e não pode excluir sua própria conta.'
            ]);
        }

        // Trava: Pedidos ativos
        if ($user->temPedidosAtivos()) {
            return back()->withErrors([
                'DeleteUsuario' => 'Você possui pedidos em andamento (pendentes, processando ou enviados) e não pode excluir sua conta agora.'
            ]);
        }

        // 2. Realiza o logout e remoção
        Auth::logout();
        $user->delete();

        // 3. Invalida a sessão
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Alias em português: excluir.
     */
    public function excluir(Request $request): RedirectResponse
    {
        return $this->deletar($request);
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (LEGACY)
    // =========================================================================

    public function edit(Request $request)
    {
        return $this->editar($request);
    }

    public function update(AtualizarPerfilRequest $request): RedirectResponse
    {
        return $this->atualizar($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        return $this->deletar($request);
    }
}
