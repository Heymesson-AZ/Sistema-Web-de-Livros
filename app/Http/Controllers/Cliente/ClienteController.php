<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ClienteController extends Controller
{
    // =========================================================================
    // CRUD DE CLIENTES (GESTÃO ADMINISTRATIVA)
    // =========================================================================

    /**
     * Listar clientes com métricas, busca e paginação.
     */
    public function listar(Request $request): View
    {
        $query = Cliente::with('user')->withCount(['pedidos', 'avaliacoes']);

        // Busca por termo (nome do cliente, email, CPF ou celular)
        if ($search = $request->input('busca')) {
            $query->where(function ($q) use ($search) {
                $q->where('cpf', 'like', "%{$search}%")
                  ->orWhere('celular_contato', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $clientes = $query->latest()->paginate(10)->withQueryString();

        // Indicadores (KPIs)
        $totalClientes = Cliente::count();
        $totalComPedidos = Cliente::has('pedidos')->count();
        $totalNovosMes = Cliente::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('cliente.listar', [
            'clientes' => $clientes,
            'totalClientes' => $totalClientes,
            'totalComPedidos' => $totalComPedidos,
            'totalNovosMes' => $totalNovosMes,
        ]);
    }

    /**
     * Exibir formulário para cadastrar novo cliente.
     */
    public function cadastrar(): View
    {
        return view('cliente.cadastrar');
    }

    /**
     * Salvar um novo cliente no banco de dados.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'max:20', 'unique:' . Cliente::class . ',cpf'],
            'celular_contato' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before:today'],
        ], [
            'name.required' => 'O nome do cliente é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before' => 'A data de nascimento deve ser uma data válida anterior a hoje.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => 'cliente',
                'email_verified_at' => now(),
            ]);

            Cliente::create([
                'user_id' => $user->id,
                'cpf' => $request->cpf,
                'celular_contato' => $request->celular_contato,
                'data_nascimento' => $request->data_nascimento,
            ]);
        });

        return redirect()->route('admin.clientes.index')
            ->with('status', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Exibir detalhes do cliente.
     */
    public function detalhes(Cliente $cliente): View
    {
        $cliente->load(['user', 'pedidos', 'avaliacoes']);

        return view('cliente.detalhes', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Exibir formulário de edição do cliente.
     */
    public function editar(Cliente $cliente): View
    {
        $cliente->load('user');

        return view('cliente.editar', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Atualizar as informações do cliente.
     */
    public function atualizar(Request $request, Cliente $cliente): RedirectResponse
    {
        $user = $cliente->user;

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'max:20', Rule::unique(Cliente::class, 'cpf')->ignore($cliente->id)],
            'celular_contato' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before:today'],
        ], [
            'name.required' => 'O nome do cliente é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado por outro cliente.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before' => 'A data de nascimento deve ser uma data válida anterior a hoje.',
        ]);

        DB::transaction(function () use ($request, $cliente, $user) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user?->update($userData);

            $cliente->update([
                'cpf' => $request->cpf,
                'celular_contato' => $request->celular_contato,
                'data_nascimento' => $request->data_nascimento,
            ]);
        });

        return redirect()->route('admin.clientes.index')
            ->with('status', 'Dados do cliente atualizados com sucesso!');
    }

    /**
     * Deletar o cliente com travas de segurança.
     */
    public function deletar(Cliente $cliente): RedirectResponse
    {
        $user = $cliente->user;

        if ($user && $user->temPedidosAtivos()) {
            return back()->withErrors([
                'erro' => 'Não é possível excluir este cliente pois ele possui pedidos em andamento no sistema.'
            ]);
        }

        DB::transaction(function () use ($cliente, $user) {
            $cliente->delete();
            $user?->delete();
        });

        return redirect()->route('admin.clientes.index')
            ->with('status', 'Cliente excluído com sucesso.');
    }

    /**
     * Alias em português: excluir.
     */
    public function excluir(Cliente $cliente): RedirectResponse
    {
        return $this->deletar($cliente);
    }

    // =========================================================================
    // GESTÃO DO PRÓPRIO PERFIL DO CLIENTE
    // =========================================================================

    /**
     * Exibir formulário de edição do perfil do próprio cliente logado.
     */
    public function editarPerfil(Request $request): View
    {
        $user = $request->user();

        return view('cliente.perfil-editar', [
            'user' => $user,
        ]);
    }

    /**
     * Atualizar o próprio perfil do cliente conectado.
     */
    public function atualizarPerfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'telefone' => ['required', 'string', 'max:20'],
        ]);

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        if ($user->cliente) {
            $user->cliente()->update([
                'celular_contato' => $request->telefone,
            ]);
        }

        return Redirect::route('cliente.perfil.editar')->with('status', 'perfil-atualizado');
    }

    /**
     * Deletar a própria conta de cliente.
     */
    public function deletarConta(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->temPedidosAtivos()) {
            return back()->withErrors([
                'DeleteUsuario' => 'Você possui pedidos em andamento e não pode excluir sua conta agora.'
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

    public function show(Cliente $cliente): View
    {
        return $this->detalhes($cliente);
    }

    public function edit(Cliente $cliente): View
    {
        return $this->editar($cliente);
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        return $this->atualizar($request, $cliente);
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        return $this->deletar($cliente);
    }
}
