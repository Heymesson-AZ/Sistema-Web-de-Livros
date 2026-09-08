<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class VendedorController extends Controller
{
    /**
     * Listar vendedores com métricas (KPIs), busca e filtros.
     */
    public function listar(Request $request): View
    {
        $query = Vendedor::with('user')->withCount(['livros', 'pedidos']);

        // Busca por termo (nome do responsável, email, cnpj, razão social ou nome fantasia)
        if ($search = $request->input('busca')) {
            $query->where(function ($q) use ($search) {
                $q->where('cnpj', 'like', "%{$search}%")
                  ->orWhere('razao_social', 'like', "%{$search}%")
                  ->orWhere('nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('telefone_comercial', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por Status de Aprovação
        if ($status = $request->input('status')) {
            $query->where('status_aprovacao', $status);
        }

        $vendedores = $query->latest()->paginate(10)->withQueryString();

        // Indicadores (KPIs)
        $totalVendedores = Vendedor::count();
        $totalAprovados = Vendedor::where('status_aprovacao', 'aprovado')->count();
        $totalPendentes = Vendedor::where('status_aprovacao', 'pendente')->count();
        $totalRejeitados = Vendedor::where('status_aprovacao', 'rejeitado')->count();

        return view('admin.vendedores.listar', [
            'vendedores' => $vendedores,
            'totalVendedores' => $totalVendedores,
            'totalAprovados' => $totalAprovados,
            'totalPendentes' => $totalPendentes,
            'totalRejeitados' => $totalRejeitados,
            'statusSelecionado' => $status,
        ]);
    }

    /**
     * Exibir formulário de cadastro de novo vendedor.
     */
    public function cadastrar(): View
    {
        return view('admin.vendedores.cadastrar');
    }

    /**
     * Salvar um novo vendedor no banco de dados.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cnpj' => ['required', 'string', 'max:20', 'unique:' . Vendedor::class . ',cnpj'],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'inscricao_estadual' => ['required', 'string', 'max:50'],
            'telefone_comercial' => ['nullable', 'string', 'max:20'],
            'status_aprovacao' => ['required', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado'])],
        ], [
            'name.required' => 'O nome do responsável é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'inscricao_estadual.required' => 'A inscrição estadual é obrigatória.',
            'status_aprovacao.in' => 'Selecione um status válido.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => 'vendedor',
                'email_verified_at' => now(),
            ]);

            Vendedor::create([
                'user_id' => $user->id,
                'cnpj' => $request->cnpj,
                'telefone_comercial' => $request->telefone_comercial,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'inscricao_estadual' => $request->inscricao_estadual,
                'status_aprovacao' => $request->status_aprovacao,
            ]);
        });

        return redirect()->route('admin.vendedores.index')
            ->with('status', 'Vendedor cadastrado com sucesso!');
    }

    /**
     * Exibir os detalhes de um vendedor específico.
     */
    public function detalhes(Vendedor $vendedor): View
    {
        $vendedor->load(['user', 'livros', 'pedidos', 'avaliacoes']);

        return view('admin.vendedores.detalhes', [
            'vendedor' => $vendedor,
        ]);
    }

    /**
     * Exibir formulário de edição do vendedor.
     */
    public function editar(Vendedor $vendedor): View
    {
        $vendedor->load('user');

        return view('admin.vendedores.editar', [
            'vendedor' => $vendedor,
        ]);
    }

    /**
     * Atualizar as informações do vendedor.
     */
    public function atualizar(Request $request, Vendedor $vendedor): RedirectResponse
    {
        $user = $vendedor->user;

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'cnpj' => ['required', 'string', 'max:20', Rule::unique(Vendedor::class, 'cnpj')->ignore($vendedor->id)],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'inscricao_estadual' => ['required', 'string', 'max:50'],
            'telefone_comercial' => ['nullable', 'string', 'max:20'],
            'status_aprovacao' => ['required', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado'])],
        ], [
            'name.required' => 'O nome do responsável é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado por outro vendedor.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'inscricao_estadual.required' => 'A inscrição estadual é obrigatória.',
            'status_aprovacao.in' => 'Selecione um status válido.',
        ]);

        DB::transaction(function () use ($request, $vendedor, $user) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user?->update($userData);

            $vendedor->update([
                'cnpj' => $request->cnpj,
                'telefone_comercial' => $request->telefone_comercial,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'inscricao_estadual' => $request->inscricao_estadual,
                'status_aprovacao' => $request->status_aprovacao,
            ]);
        });

        return redirect()->route('admin.vendedores.index')
            ->with('status', 'Dados do vendedor atualizados com sucesso!');
    }

    /**
     * Alterar rapidamente o status de aprovação do vendedor.
     */
    public function alterarStatus(Request $request, Vendedor $vendedor): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado'])],
        ]);

        $vendedor->update([
            'status_aprovacao' => $request->status,
        ]);

        $mensagens = [
            'aprovado' => 'Vendedor aprovado com sucesso! Agora ele pode publicar livros e realizar vendas.',
            'rejeitado' => 'O cadastro do vendedor foi rejeitado.',
            'pendente' => 'O status do vendedor foi retornado para pendente de análise.',
        ];

        return back()->with('status', $mensagens[$request->status] ?? 'Status alterado com sucesso!');
    }

    /**
     * Deletar o vendedor do sistema com travas de segurança.
     */
    public function deletar(Vendedor $vendedor): RedirectResponse
    {
        $user = $vendedor->user;

        // Trava: Verificar se possui pedidos ou livros ativos vinculados
        if ($vendedor->pedidos()->exists()) {
            return back()->withErrors([
                'erro' => 'Não é possível excluir este vendedor pois ele possui pedidos registrados no sistema.'
            ]);
        }

        DB::transaction(function () use ($vendedor, $user) {
            $vendedor->delete();
            $user?->delete();
        });

        return redirect()->route('admin.vendedores.index')
            ->with('status', 'Vendedor excluído com sucesso.');
    }

    /**
     * Alias em português: excluir.
     */
    public function excluir(Vendedor $vendedor): RedirectResponse
    {
        return $this->deletar($vendedor);
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

    public function show(Vendedor $vendedor): View
    {
        return $this->detalhes($vendedor);
    }

    public function edit(Vendedor $vendedor): View
    {
        return $this->editar($vendedor);
    }

    public function update(Request $request, Vendedor $vendedor): RedirectResponse
    {
        return $this->atualizar($request, $vendedor);
    }

    public function destroy(Vendedor $vendedor): RedirectResponse
    {
        return $this->deletar($vendedor);
    }
}
