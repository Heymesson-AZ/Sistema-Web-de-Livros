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
use Illuminate\Support\Facades\Storage;
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

        // Filtro por Status
        if ($status = $request->input('status')) {
            $query->whereHas('user', function ($u) use ($status) {
                $u->where('status', $status);
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
            'status' => ['required', 'string', Rule::in(['ativo', 'inativo', 'banido'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'cpf' => ['required', 'string', 'max:20', 'unique:' . Cliente::class . ',cpf'],
            'celular_contato' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before:today'],
        ], [
            'name.required' => 'O nome do cliente é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'status.in' => 'Selecione um status válido para a conta.',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'A foto de perfil deve ter entre 50x50px e 4000x4000px.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before' => 'A data de nascimento deve ser uma data válida anterior a hoje.',
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
                'tipo' => 'cliente',
                'status' => $request->status,
                'foto_perfil' => $fotoPath,
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
            'status' => ['required', 'string', Rule::in(['ativo', 'inativo', 'banido'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remover_foto' => ['nullable', 'boolean'],
            'cpf' => ['required', 'string', 'max:20', Rule::unique(Cliente::class, 'cpf')->ignore($cliente->id)],
            'celular_contato' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'senha_confirmacao_admin' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('Sua senha de administrador está incorreta para confirmar esta alteração crítica.');
                    }
                },
            ],
        ], [
            'senha_confirmacao_admin.required' => 'Informe sua senha de administrador para autorizar esta alteração crítica.',
            'name.required' => 'O nome do cliente é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'status.in' => 'Selecione um status válido para a conta.',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'A foto de perfil deve ter entre 50x50px e 4000x4000px.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado por outro cliente.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before' => 'A data de nascimento deve ser uma data válida anterior a hoje.',
        ]);

        DB::transaction(function () use ($request, $cliente, $user) {
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
            if ($user?->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
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
        $request->merge(['tab' => 'perfil']);
        return app(\App\Http\Controllers\Painel\PainelController::class)->index($request);
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
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remover_foto' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'telefone.required' => 'O telefone é obrigatório.',
            'foto_perfil.image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'A foto de perfil deve ter entre 50x50px e 4000x4000px.',
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

        if ($user->cliente) {
            $user->cliente()->update([
                'celular_contato' => $request->telefone,
            ]);
        }

        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), 'painel')) {
            return redirect()->route('painel', ['tab' => 'perfil'])->with('status', 'perfil-atualizado');
        }

        return redirect()->route('cliente.perfil.editar')->with('status', 'perfil-atualizado');
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
