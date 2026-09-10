<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendedor;
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

class VendedorController extends Controller
{
    // =========================================================================
    // CRUD DE VENDEDORES (GESTÃO ADMINISTRATIVA)
    // =========================================================================

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

        // Filtros específicos e independentes
        $statusAprovacao = $request->input('status_aprovacao') ?? $request->input('aprovacao');
        if ($statusAprovacao && in_array($statusAprovacao, ['aprovado', 'pendente', 'rejeitado'])) {
            $query->where('status_aprovacao', $statusAprovacao);
        }

        $statusConta = $request->input('status_conta') ?? $request->input('conta');
        if ($statusConta && in_array($statusConta, ['ativo', 'inativo', 'banido'])) {
            $query->whereHas('user', fn ($u) => $u->where('status', $statusConta));
        }

        // Filtro por Aptidão Operacional / Situação Unificada
        $situacao = $request->input('situacao') ?? $request->input('status');
        if ($situacao && in_array($situacao, ['apto', 'inapto', 'aprovado', 'pendente', 'inativo', 'rejeitado', 'banido'])) {
            if ($situacao === 'inapto') {
                $query->where(function ($q) {
                    $q->where('status_aprovacao', '!=', 'aprovado')
                      ->orWhereHas('user', fn ($u) => $u->where('status', '!=', 'ativo'));
                });
            } else {
                $query->comSituacao($situacao);
            }
        }

        $vendedores = $query->latest()->paginate(10)->withQueryString();

        // Indicadores (KPIs)
        $totalVendedores = Vendedor::count();
        $totalAprovados = Vendedor::where('status_aprovacao', 'aprovado')->count();
        $totalAptos = Vendedor::where('status_aprovacao', 'aprovado')
            ->whereHas('user', fn ($u) => $u->where('status', 'ativo'))
            ->count();
        $totalPendentes = Vendedor::where('status_aprovacao', 'pendente')->count();
        $totalRejeitados = Vendedor::where('status_aprovacao', 'rejeitado')->count();
        $totalBanidos = Vendedor::whereHas('user', fn ($u) => $u->where('status', 'banido'))->count();

        return view('vendedor.listar', [
            'vendedores' => $vendedores,
            'totalVendedores' => $totalVendedores,
            'totalAprovados' => $totalAprovados,
            'totalAptos' => $totalAptos,
            'totalPendentes' => $totalPendentes,
            'totalRejeitados' => $totalRejeitados,
            'totalBanidos' => $totalBanidos,
            'situacaoSelecionada' => $situacao,
            'statusAprovacaoSelecionado' => $statusAprovacao,
            'statusContaSelecionado' => $statusConta,
        ]);
    }

    /**
     * Exibir formulário de cadastro de novo vendedor.
     */
    public function cadastrar(): View
    {
        return view('vendedor.cadastrar');
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
            'situacao' => ['nullable', 'string', Rule::in(['aprovado', 'pendente', 'inativo', 'rejeitado', 'banido'])],
            'status' => ['nullable', 'string', Rule::in(['ativo', 'inativo', 'banido'])],
            'status_aprovacao' => ['nullable', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'cnpj' => ['required', 'string', 'max:20', 'unique:' . Vendedor::class . ',cnpj'],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'inscricao_estadual' => ['required', 'string', 'max:50'],
            'telefone_comercial' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'O nome do responsável é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'status.in' => 'Selecione um status de conta válido.',
            'foto_perfil.image' => 'O logotipo ou foto deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'O logotipo ou foto deve ter entre 50x50px e 4000x4000px.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'inscricao_estadual.required' => 'A inscrição estadual é obrigatória.',
            'status_aprovacao.in' => 'Selecione um status de aprovação válido.',
        ]);

        $userStatus = $request->input('status', 'ativo');
        $statusAprovacao = $request->input('status_aprovacao', 'pendente');

        if ($request->filled('situacao')) {
            [$userStatus, $statusAprovacao] = match ($request->input('situacao')) {
                'banido' => ['banido', 'rejeitado'],
                'rejeitado' => ['inativo', 'rejeitado'],
                'pendente' => ['ativo', 'pendente'],
                'inativo' => ['inativo', 'aprovado'],
                default => ['ativo', 'aprovado'],
            };
        }

        DB::transaction(function () use ($request, $userStatus, $statusAprovacao) {
            $fotoPath = null;
            if ($request->hasFile('foto_perfil')) {
                $fotoPath = $request->file('foto_perfil')->store('perfis', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => 'vendedor',
                'status' => $userStatus,
                'foto_perfil' => $fotoPath,
                'email_verified_at' => now(),
            ]);

            Vendedor::create([
                'user_id' => $user->id,
                'cnpj' => $request->cnpj,
                'telefone_comercial' => $request->telefone_comercial,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'inscricao_estadual' => $request->inscricao_estadual,
                'status_aprovacao' => $statusAprovacao,
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
        $vendedor->load(['user', 'livros.autor', 'livros.genero', 'pedidos', 'avaliacoes']);

        return view('vendedor.detalhes', [
            'vendedor' => $vendedor,
        ]);
    }

    /**
     * Exibir formulário de edição do vendedor.
     */
    public function editar(Vendedor $vendedor): View
    {
        $vendedor->load('user');

        return view('vendedor.editar', [
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
            'situacao' => ['nullable', 'string', Rule::in(['aprovado', 'pendente', 'inativo', 'rejeitado', 'banido'])],
            'status' => ['nullable', 'string', Rule::in(['ativo', 'inativo', 'banido'])],
            'status_aprovacao' => ['nullable', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado'])],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remover_foto' => ['nullable', 'boolean'],
            'cnpj' => ['required', 'string', 'max:20', Rule::unique(Vendedor::class, 'cnpj')->ignore($vendedor->id)],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'inscricao_estadual' => ['required', 'string', 'max:50'],
            'telefone_comercial' => ['nullable', 'string', 'max:20'],
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
            'name.required' => 'O nome do responsável é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'status.in' => 'Selecione um status de conta válido.',
            'foto_perfil.image' => 'O logotipo ou foto deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'O logotipo ou foto deve ter entre 50x50px e 4000x4000px.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado por outro vendedor.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'inscricao_estadual.required' => 'A inscrição estadual é obrigatória.',
            'status_aprovacao.in' => 'Selecione um status válido.',
        ]);

        $userStatus = $request->input('status', $user?->status ?? 'ativo');
        $statusAprovacao = $request->input('status_aprovacao', $vendedor->status_aprovacao ?? 'pendente');

        if ($request->filled('situacao')) {
            [$userStatus, $statusAprovacao] = match ($request->input('situacao')) {
                'banido' => ['banido', 'rejeitado'],
                'rejeitado' => ['inativo', 'rejeitado'],
                'pendente' => ['ativo', 'pendente'],
                'inativo' => ['inativo', 'aprovado'],
                default => ['ativo', 'aprovado'],
            };
        }

        DB::transaction(function () use ($request, $vendedor, $user, $userStatus, $statusAprovacao) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $userStatus,
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

            $vendedor->update([
                'cnpj' => $request->cnpj,
                'telefone_comercial' => $request->telefone_comercial,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'inscricao_estadual' => $request->inscricao_estadual,
                'status_aprovacao' => $statusAprovacao,
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
        if (!Auth::user()->podeGerenciarVendedores()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para aprovar ou rejeitar vendedores.');
        }

        $request->validate([
            'status' => ['required', 'string', Rule::in(['pendente', 'aprovado', 'rejeitado', 'banido'])],
        ]);

        $statusAlvo = $request->status;
        [$userStatus, $statusAprovacao] = match ($statusAlvo) {
            'banido' => ['banido', 'rejeitado'],
            'rejeitado' => ['inativo', 'rejeitado'],
            'pendente' => ['ativo', 'pendente'],
            default => ['ativo', 'aprovado'],
        };

        DB::transaction(function () use ($vendedor, $statusAprovacao, $userStatus) {
            $vendedor->update([
                'status_aprovacao' => $statusAprovacao,
            ]);

            $vendedor->user?->update([
                'status' => $userStatus,
            ]);
        });

        $mensagens = [
            'aprovado' => 'Vendedor aprovado com sucesso! Agora a loja está autorizada e ativa para vendas.',
            'rejeitado' => 'O cadastro do vendedor foi rejeitado e o acesso foi suspenso.',
            'banido' => 'O vendedor foi banido por infração.',
            'pendente' => 'O status do vendedor foi retornado para pendente de análise.',
        ];

        return back()->with('status', $mensagens[$request->status] ?? 'Situação alterada com sucesso!');
    }

    /**
     * Deletar o vendedor do sistema com travas de segurança.
     */
    public function deletar(Vendedor $vendedor): RedirectResponse
    {
        $user = $vendedor->user;

        if ($vendedor->pedidos()->exists()) {
            return back()->withErrors([
                'erro' => 'Não é possível excluir este vendedor pois ele possui pedidos registrados no sistema.'
            ]);
        }

        DB::transaction(function () use ($vendedor, $user) {
            if ($user?->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
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
    // GESTÃO DO PRÓPRIO PERFIL DO VENDEDOR
    // =========================================================================

    /**
     * Exibir formulário de edição do perfil do próprio vendedor logado.
     */
    public function editarPerfil(Request $request): View
    {
        $request->merge(['tab' => 'perfil']);
        return app(\App\Http\Controllers\Painel\PainelController::class)->index($request);
    }

    /**
     * Atualizar o próprio perfil do vendedor conectado.
     */
    public function atualizarPerfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $telefone = $request->input('telefone', $request->input('telefone_comercial'));
        $request->merge(['telefone' => $telefone]);

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'telefone' => ['required', 'string', 'max:20'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'inscricao_estadual' => ['nullable', 'string', 'max:50'],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remover_foto' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'O nome do responsável é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'telefone.required' => 'O telefone comercial é obrigatório.',
            'nome_fantasia.required' => 'O nome fantasia da loja é obrigatório.',
            'foto_perfil.image' => 'O logotipo ou foto selecionado deve ser uma imagem válida.',
            'foto_perfil.max' => 'A imagem não pode ultrapassar 2MB.',
            'foto_perfil.dimensions' => 'O logotipo ou foto deve ter entre 50x50px e 4000x4000px.',
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

        if ($user->vendedor) {
            $vendorData = [
                'telefone_comercial' => $telefone,
                'nome_fantasia' => $request->nome_fantasia,
            ];
            if ($request->filled('razao_social')) {
                $vendorData['razao_social'] = $request->razao_social;
            }
            if ($request->filled('inscricao_estadual')) {
                $vendorData['inscricao_estadual'] = $request->inscricao_estadual;
            }
            $user->vendedor()->update($vendorData);
        }

        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), 'painel')) {
            return redirect()->route('painel', ['tab' => 'perfil'])->with('status', 'perfil-atualizado');
        }

        return redirect()->route('vendedor.perfil.editar')->with('status', 'perfil-atualizado');
    }

    /**
     * Deletar a própria conta de vendedor.
     */
    public function deletarConta(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->temPedidosAtivos()) {
            return back()
                ->withErrors([
                    'DeleteUsuario' => 'Não é possível excluir sua conta: existem pedidos ativos, em processamento ou com pendências vinculadas à sua loja. Conclua ou cancele todos os pedidos antes de prosseguir.'
                ], 'userDeletion')
                ->with('error', 'Não é possível excluir sua conta de vendedor: existem pedidos ativos ou pendências vinculadas.');
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // =========================================================================
    // SOLICITAÇÃO PÚBLICA DE CADASTRO E PAINEL DO VENDEDOR
    // =========================================================================

    /**
     * Exibir formulário para visitante ou cliente solicitar se tornar vendedor.
     */
    public function solicitarCadastro(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isVendedor()) {
            return redirect()->route('vendedor.painel');
        }

        return view('vendedor.solicitar', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Processar a solicitação de cadastro do vendedor (gerando status_aprovacao = 'pendente').
     */
    public function enviarSolicitacao(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = $request->user();

            if ($user->isVendedor() && $user->vendedor) {
                return redirect()->route('vendedor.painel');
            }

            $request->validate([
                'cnpj' => ['required', 'string', 'max:20', 'unique:' . Vendedor::class . ',cnpj'],
                'razao_social' => ['required', 'string', 'max:255'],
                'nome_fantasia' => ['required', 'string', 'max:255'],
                'inscricao_estadual' => ['nullable', 'string', 'max:50'],
                'telefone_comercial' => ['required', 'string', 'max:20'],
            ], [
                'cnpj.required' => 'O CNPJ é obrigatório.',
                'cnpj.unique' => 'Este CNPJ já está cadastrado por outro vendedor.',
                'razao_social.required' => 'A razão social é obrigatória.',
                'nome_fantasia.required' => 'O nome fantasia da loja é obrigatório.',
                'telefone_comercial.required' => 'O telefone comercial é obrigatório.',
            ]);

            DB::transaction(function () use ($request, $user) {
                $user->update(['tipo' => 'vendedor']);

                Vendedor::create([
                    'user_id' => $user->id,
                    'cnpj' => $request->cnpj,
                    'telefone_comercial' => $request->telefone_comercial,
                    'razao_social' => $request->razao_social,
                    'nome_fantasia' => $request->nome_fantasia,
                    'inscricao_estadual' => $request->inscricao_estadual,
                    'status_aprovacao' => Vendedor::STATUS_PENDENTE,
                ]);
            });
        } else {
            $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'cnpj' => ['required', 'string', 'max:20', 'unique:' . Vendedor::class . ',cnpj'],
                'razao_social' => ['required', 'string', 'max:255'],
                'nome_fantasia' => ['required', 'string', 'max:255'],
                'inscricao_estadual' => ['nullable', 'string', 'max:50'],
                'telefone_comercial' => ['required', 'string', 'max:20'],
            ], [
                'name.required' => 'O nome do responsável é obrigatório.',
                'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.unique' => 'Este e-mail já está sendo utilizado.',
                'password.required' => 'A senha é obrigatória.',
                'password.confirmed' => 'A confirmação de senha não confere.',
                'cnpj.required' => 'O CNPJ é obrigatório.',
                'cnpj.unique' => 'Este CNPJ já está cadastrado por outro vendedor.',
                'razao_social.required' => 'A razão social é obrigatória.',
                'nome_fantasia.required' => 'O nome fantasia da loja é obrigatório.',
                'telefone_comercial.required' => 'O telefone comercial é obrigatório.',
            ]);

            $user = DB::transaction(function () use ($request) {
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'tipo' => 'vendedor',
                    'status' => 'ativo',
                    'email_verified_at' => now(),
                ]);

                Vendedor::create([
                    'user_id' => $newUser->id,
                    'cnpj' => $request->cnpj,
                    'telefone_comercial' => $request->telefone_comercial,
                    'razao_social' => $request->razao_social,
                    'nome_fantasia' => $request->nome_fantasia,
                    'inscricao_estadual' => $request->inscricao_estadual,
                    'status_aprovacao' => Vendedor::STATUS_PENDENTE,
                ]);

                return $newUser;
            });

            Auth::login($user);
        }

        return redirect()->route('vendedor.painel')
            ->with('status', 'solicitacao-enviada');
    }

    /**
     * Exibir painel da loja com status da aprovação, métricas e avisos.
     */
    public function painel(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $vendedor = $user->vendedor;

        if (!$vendedor) {
            return redirect()->route('vendedor.solicitar');
        }

        $livrosCount = $vendedor->livros()->count();
        $pedidosCount = $vendedor->pedidos()->count();
        $avaliacoesCount = $vendedor->avaliacoes()->count();
        $clientesCount = \App\Models\Cliente::whereHas('pedidos', fn($q) => $q->where('vendedor_id', $vendedor->id))->count();
        $cuponsCount = $vendedor->cupons()->count();

        return view('vendedor.painel', [
            'user' => $user,
            'vendedor' => $vendedor,
            'livrosCount' => $livrosCount,
            'pedidosCount' => $pedidosCount,
            'avaliacoesCount' => $avaliacoesCount,
            'clientesCount' => $clientesCount,
            'cuponsCount' => $cuponsCount,
        ]);
    }

    /**
     * Exibir lista de clientes que compraram livros desta loja.
     */
    public function clientes(Request $request): View|RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || !$vendedor->isAprovado()) {
            return redirect()->route('vendedor.painel')->with('error', 'Sua loja precisa estar aprovada para visualizar clientes.');
        }

        $query = \App\Models\Cliente::whereHas('pedidos', function ($q) use ($vendedor) {
            $q->where('vendedor_id', $vendedor->id);
        })->with(['user', 'pedidos' => function ($q) use ($vendedor) {
            $q->where('vendedor_id', $vendedor->id)->with('entrega');
        }])->withCount(['pedidos' => function ($q) use ($vendedor) {
            $q->where('vendedor_id', $vendedor->id);
        }]);

        if ($request->filled('busca')) {
            $busca = trim($request->busca);
            $query->whereHas('user', function ($u) use ($busca) {
                $u->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        $clientes = $query->paginate(12)->withQueryString();

        return view('vendedor.clientes.index', compact('vendedor', 'clientes'));
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
