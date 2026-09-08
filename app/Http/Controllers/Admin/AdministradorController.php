<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdministradorController extends Controller
{
    /**
     * Listar administradores com busca, filtros e indicadores (KPIs).
     */
    public function listar(Request $request): View
    {
        $query = Admin::with('user');

        // Busca por termo (nome, email ou telefone)
        if ($search = $request->input('busca')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('telefone_urgencia', 'like', "%{$search}%");
            });
        }

        // Filtro por Departamento
        if ($departamento = $request->input('departamento')) {
            $query->where('departamento', $departamento);
        }

        // Filtro por Cargo
        if ($cargo = $request->input('cargo')) {
            $query->where('cargo', $cargo);
        }

        $administradores = $query->latest()->paginate(10)->withQueryString();

        // Indicadores (KPIs)
        $totalAdmins = Admin::count();
        $totalDepartamentos = count(Admin::getDepartamentos());
        $totalSuperAdmins = Admin::where('cargo', Admin::CARGO_SUPER_ADMIN)->count();

        return view('admin.administradores.listar', [
            'administradores' => $administradores,
            'cargos' => Admin::getCargos(),
            'departamentos' => Admin::getDepartamentos(),
            'totalAdmins' => $totalAdmins,
            'totalDepartamentos' => $totalDepartamentos,
            'totalSuperAdmins' => $totalSuperAdmins,
        ]);
    }

    /**
     * Exibir formulário para cadastrar novo administrador.
     */
    public function cadastrar(): View
    {
        return view('admin.administradores.cadastrar', [
            'cargos' => Admin::getCargos(),
            'departamentos' => Admin::getDepartamentos(),
        ]);
    }

    /**
     * Salvar um novo administrador no banco de dados.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefone_urgencia' => ['nullable', 'string', 'max:20'],
            'cargo' => ['required', 'string', Rule::in(Admin::getCargos())],
            'departamento' => ['required', 'string', Rule::in(Admin::getDepartamentos())],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cargo.in' => 'Selecione um cargo válido.',
            'departamento.in' => 'Selecione um departamento válido.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => 'admin',
                'email_verified_at' => now(),
            ]);

            Admin::create([
                'user_id' => $user->id,
                'telefone_urgencia' => $request->telefone_urgencia,
                'cargo' => $request->cargo,
                'departamento' => $request->departamento,
            ]);
        });

        return redirect()->route('admin.administradores.index')
            ->with('status', 'Administrador cadastrado com sucesso!');
    }

    /**
     * Exibir os detalhes de um administrador específico.
     */
    public function detalhes(Admin $administrador): View
    {
        $administrador->load('user');

        return view('admin.administradores.detalhes', [
            'admin' => $administrador,
        ]);
    }

    /**
     * Exibir formulário de edição de um administrador.
     */
    public function editar(Admin $administrador): View
    {
        $administrador->load('user');

        return view('admin.administradores.editar', [
            'admin' => $administrador,
            'cargos' => Admin::getCargos(),
            'departamentos' => Admin::getDepartamentos(),
        ]);
    }

    /**
     * Atualizar as informações do administrador.
     */
    public function atualizar(Request $request, Admin $administrador): RedirectResponse
    {
        $user = $administrador->user;

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'telefone_urgencia' => ['nullable', 'string', 'max:20'],
            'cargo' => ['required', 'string', Rule::in(Admin::getCargos())],
            'departamento' => ['required', 'string', Rule::in(Admin::getDepartamentos())],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já pertence a outro usuário.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cargo.in' => 'Selecione um cargo válido.',
            'departamento.in' => 'Selecione um departamento válido.',
        ]);

        DB::transaction(function () use ($request, $user, $administrador) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            $administrador->update([
                'telefone_urgencia' => $request->telefone_urgencia,
                'cargo' => $request->cargo,
                'departamento' => $request->departamento,
            ]);
        });

        return redirect()->route('admin.administradores.index')
            ->with('status', 'Administrador atualizado com sucesso!');
    }

    /**
     * Deletar/excluir um administrador do sistema com travas de segurança.
     */
    public function deletar(Admin $administrador): RedirectResponse
    {
        // 1. Não permitir auto-exclusão
        if ($administrador->user_id === Auth::id()) {
            return redirect()->route('admin.administradores.index')
                ->with('error', 'Operação negada: Você não pode excluir sua própria conta de administrador.');
        }

        // 2. Não permitir exclusão se for o único Super Admin
        if ($administrador->cargo === Admin::CARGO_SUPER_ADMIN) {
            $totalSuperAdmins = Admin::where('cargo', Admin::CARGO_SUPER_ADMIN)->count();
            if ($totalSuperAdmins <= 1) {
                return redirect()->route('admin.administradores.index')
                    ->with('error', 'Operação negada: O sistema deve ter pelo menos um Super Administrador ativo.');
            }
        }

        DB::transaction(function () use ($administrador) {
            $user = $administrador->user;
            $administrador->delete();
            $user?->delete();
        });

        return redirect()->route('admin.administradores.index')
            ->with('status', 'Administrador excluído com sucesso.');
    }

    /**
     * Alias em português: excluir.
     */
    public function excluir(Admin $administrador): RedirectResponse
    {
        return $this->deletar($administrador);
    }

    // =========================================================================
    // MÉTODOS DO RESOURCE PADRÃO DO LARAVEL (DELEGAÇÃO TRANSPARENTE)
    // =========================================================================

    public function index(Request $request): View
    {
        return $this->listar($request);
    }

    public function create(): View
    {
        return $this->cadastrar();
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->salvar($request);
    }

    public function show(Admin $administrador): View
    {
        return $this->detalhes($administrador);
    }

    public function edit(Admin $administrador): View
    {
        return $this->editar($administrador);
    }

    public function update(Request $request, Admin $administrador): RedirectResponse
    {
        return $this->atualizar($request, $administrador);
    }

    public function destroy(Admin $administrador): RedirectResponse
    {
        return $this->deletar($administrador);
    }
}
