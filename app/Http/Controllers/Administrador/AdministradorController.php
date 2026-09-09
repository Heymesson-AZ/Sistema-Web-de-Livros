<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdministradorController extends Controller
{
    // =========================================================================
    // CRUD DE ADMINISTRADORES
    // =========================================================================

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

        // Filtro por Status
        if ($status = $request->input('status')) {
            $query->whereHas('user', function ($u) use ($status) {
                $u->where('status', $status);
            });
        }

        $administradores = $query->latest()->paginate(10)->withQueryString();

        // Indicadores (KPIs)
        $totalAdmins = Admin::count();
        $totalDepartamentos = count(Admin::getDepartamentos());
        $totalSuperAdmins = Admin::where('cargo', Admin::CARGO_SUPER_ADMIN)->count();

        return view('administrador.listar', [
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
        return view('administrador.cadastrar', [
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
            'status' => ['required', 'string', Rule::in(['ativo', 'inativo'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
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
            'status.in' => 'Selecione um status válido (ativo ou inativo).',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'cargo.in' => 'Selecione um cargo válido.',
            'departamento.in' => 'Selecione um departamento válido.',
        ]);

        DB::transaction(function () use ($request) {
            $fotoPath = null;
            if ($request->hasFile('foto_perfil')) {
                $fotoPath = $request->file('foto_perfil')->store('perfis', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => 'admin',
                'status' => $request->status,
                'foto_perfil' => $fotoPath,
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

        return view('administrador.detalhes', [
            'admin' => $administrador,
        ]);
    }

    /**
     * Exibir formulário de edição de um administrador.
     */
    public function editar(Admin $administrador): View
    {
        $administrador->load('user');

        return view('administrador.editar', [
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'string', Rule::in(['ativo', 'inativo'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remover_foto' => ['nullable', 'boolean'],
            'telefone_urgencia' => ['nullable', 'string', 'max:20'],
            'cargo' => ['required', 'string', Rule::in(Admin::getCargos())],
            'departamento' => ['required', 'string', Rule::in(Admin::getDepartamentos())],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'status.in' => 'Selecione um status válido (ativo ou inativo).',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'cargo.in' => 'Selecione um cargo válido.',
            'departamento.in' => 'Selecione um departamento válido.',
        ]);

        DB::transaction(function () use ($request, $administrador, $user) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('foto_perfil')) {
                if ($user?->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                    Storage::disk('public')->delete($user->foto_perfil);
                }
                $userData['foto_perfil'] = $request->file('foto_perfil')->store('perfis', 'public');
            } elseif ($request->boolean('remover_foto')) {
                if ($user?->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                    Storage::disk('public')->delete($user->foto_perfil);
                }
                $userData['foto_perfil'] = null;
            }

            $user?->update($userData);

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
     * Deletar o administrador do sistema.
     */
    public function deletar(Admin $administrador): RedirectResponse
    {
        // Trava de Segurança 1: Não permitir que o usuário logado exclua a si mesmo por esta rota
        if (Auth::id() === $administrador->user_id) {
            return redirect()->route('admin.administradores.index')
                ->with('error', 'Você não pode excluir seu próprio cadastro de administrador por esta listagem.');
        }

        // Trava de Segurança 2: Garantir que sempre reste pelo menos um administrador no sistema
        if (Admin::count() <= 1) {
            return redirect()->route('admin.administradores.index')
                ->with('error', 'Operação cancelada: O sistema deve possuir pelo menos um administrador cadastrado.');
        }

        $user = $administrador->user;

        DB::transaction(function () use ($administrador, $user) {
            if ($user?->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
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
    // GESTÃO DO PRÓPRIO PERFIL DO ADMINISTRADOR
    // =========================================================================

    /**
     * Exibir formulário de edição do perfil do administrador conectado.
     */
    public function editarPerfil(Request $request): View
    {
        $request->merge(['tab' => 'perfil']);
        return app(\App\Http\Controllers\Painel\PainelController::class)->index($request);
    }

    /**
     * Atualizar os dados do perfil do administrador conectado.
     */
    public function atualizarPerfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'telefone_urgencia' => ['nullable', 'string', 'max:20'],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remover_foto' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
        ]);

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $user->foto_perfil = $request->file('foto_perfil')->store('perfis', 'public');
        } elseif ($request->boolean('remover_foto')) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $user->foto_perfil = null;
        }

        $user->save();

        if ($user->admin) {
            $user->admin()->update($request->only('telefone_urgencia'));
        }

        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), 'painel')) {
            return redirect()->route('painel', ['tab' => 'perfil'])->with('status', 'perfil-atualizado');
        }

        return redirect()->route('admin.perfil.editar')->with('status', 'perfil-atualizado');
    }

    /**
     * Deletar a própria conta de administrador.
     */
    public function deletarConta(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (User::where('tipo', 'admin')->count() <= 1) {
            return back()->withErrors([
                'DeleteUsuario' => 'Você é o único administrador do sistema e não pode excluir sua própria conta.'
            ]);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // =========================================================================
    // MÉTODOS DE RESOURCE PADRÃO DO LARAVEL
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
