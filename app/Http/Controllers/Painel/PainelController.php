<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Livro;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PainelController extends Controller
{
    /**
     * Exibe o ambiente centralizado do Painel e Perfil do Usuário.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $notificacoes = self::obterNotificacoes($user);
        $kpis = [];

        if ($user->isAdmin()) {
            $user->load('admin');
            $vendedoresPendentes = Vendedor::where('status_aprovacao', Vendedor::STATUS_PENDENTE)->count();
            $kpis = [
                'total_livros' => Livro::count(),
                'total_vendedores' => Vendedor::count(),
                'total_clientes' => Cliente::count(),
                'vendedores_pendentes' => $vendedoresPendentes,
            ];
        } elseif ($user->isVendedor()) {
            $user->load('vendedor.livros');
            $vendedor = $user->vendedor;
            if ($vendedor) {
                $kpis = [
                    'total_livros' => $vendedor->livros()->count(),
                    'em_estoque' => $vendedor->livros()->where('quantidade', '>', 0)->count(),
                    'esgotados' => $vendedor->livros()->where('quantidade', 0)->count(),
                ];
            }
        } else {
            $user->load('cliente');
        }

        $enderecos = $user->enderecos()->orderByDesc('principal')->latest()->get();
        $cartoesSalvos = $user->cartoesSalvos()->orderByDesc('cartao_padrao')->latest()->get();
        $favoritos = $user->favoritos()->with('livro.autor', 'livro.editora')->latest()->get();

        // Pedidos contextuais
        if ($user->isAdmin()) {
            $pedidos = \App\Models\Pedido::with(['cliente.user', 'vendedor', 'itens.livro', 'entrega'])->latest()->take(30)->get();
            $avaliacoes = \App\Models\Avaliacao::with(['cliente.user', 'vendedor', 'pedido'])->latest()->take(30)->get();
        } elseif ($user->isVendedor() && $user->vendedor) {
            $pedidos = \App\Models\Pedido::where('vendedor_id', $user->vendedor->id)
                ->with(['cliente.user', 'itens.livro', 'entrega'])
                ->latest()->get();
            $avaliacoes = $user->vendedor->avaliacoes()->with(['cliente.user', 'pedido'])->latest()->get();
        } else {
            $clienteId = $user->cliente?->id;
            $pedidos = $clienteId
                ? \App\Models\Pedido::where('cliente_id', $clienteId)
                    ->with(['vendedor', 'itens.livro', 'entrega', 'avaliacao'])
                    ->latest()->get()
                : collect();
            $avaliacoes = $clienteId
                ? \App\Models\Avaliacao::where('cliente_id', $clienteId)
                    ->with(['vendedor', 'pedido'])
                    ->latest()->get()
                : collect();
        }

        $tab = $request->query('tab', 'visao-geral');

        return view('paginas.painel', compact(
            'user',
            'notificacoes',
            'kpis',
            'tab',
            'enderecos',
            'cartoesSalvos',
            'favoritos',
            'pedidos',
            'avaliacoes'
        ));
    }

    /**
     * Retorna a lista de notificações relevantes do usuário autenticado.
     */
    public static function obterNotificacoes(User $user): array
    {
        $notificacoes = [];

        // Notificação universal: verificação de e-mail
        if (!$user->hasVerifiedEmail()) {
            $notificacoes[] = [
                'tipo' => 'warning',
                'icone' => 'bi-envelope-exclamation-fill',
                'titulo' => 'E-mail não verificado',
                'mensagem' => 'Seu endereço de e-mail ainda não foi confirmado. Verifique sua caixa de entrada.',
                'link' => route('verification.notice'),
                'link_texto' => 'Reenviar verificação',
            ];
        }

        if ($user->isAdmin()) {
            // 1. Vendedores pendentes de moderação/aprovação (Prioridade Alta)
            $vendedoresPendentes = Vendedor::where('status_aprovacao', Vendedor::STATUS_PENDENTE)->count();
            if ($vendedoresPendentes > 0) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-hourglass-split',
                    'titulo' => 'Solicitações de Vendedores Pendentes',
                    'mensagem' => "Existem {$vendedoresPendentes} solicitação(ões) de vendedores pendentes de análise administrativa.",
                    'link' => route('admin.vendedores.index', ['status_aprovacao' => 'pendente']),
                    'link_texto' => 'Avaliar Vendedores',
                ];
            }

            // 2. Livros sob análise preventiva
            $livrosSobAnalise = Livro::where('status_moderacao', Livro::STATUS_MODERACAO_SOB_ANALISE)->count();
            if ($livrosSobAnalise > 0) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-shield-exclamation',
                    'titulo' => 'Livros Sob Análise',
                    'mensagem' => "Existem {$livrosSobAnalise} livro(s) sob análise por suspeita de dados inválidos ou irregularidade.",
                    'link' => route('admin.livros.index', ['status_moderacao' => Livro::STATUS_MODERACAO_SOB_ANALISE]),
                    'link_texto' => 'Moderar Obras',
                ];
            }

            // 3. Livros com bloqueio temporário
            $livrosBloqueados = Livro::where('status_moderacao', Livro::STATUS_MODERACAO_BLOQUEADO)->count();
            if ($livrosBloqueados > 0) {
                $notificacoes[] = [
                    'tipo' => 'danger',
                    'icone' => 'bi-slash-circle-fill',
                    'titulo' => 'Livros Bloqueados Temporariamente',
                    'mensagem' => "Existem {$livrosBloqueados} livro(s) com suspensão temporária aguardando regularização.",
                    'link' => route('admin.livros.index', ['status_moderacao' => Livro::STATUS_MODERACAO_BLOQUEADO]),
                    'link_texto' => 'Ver Bloqueados',
                ];
            }

            // 3. Novos usuários cadastrados nas últimas 24 horas
            $novosUsuarios = User::where('created_at', '>=', now()->subDay())->count();
            if ($novosUsuarios > 0) {
                $notificacoes[] = [
                    'tipo' => 'info',
                    'icone' => 'bi-people-fill',
                    'titulo' => 'Novos Usuários na Plataforma',
                    'mensagem' => "{$novosUsuarios} novo(s) usuário(s) cadastrado(s) nas últimas 24 horas no sistema.",
                    'link' => route('admin.clientes.index'),
                    'link_texto' => 'Gerenciar Usuários',
                ];
            }
        } elseif ($user->isVendedor()) {
            $vendedor = $user->vendedor;

            // 1. Notificação de status da loja
            if ($vendedor && $vendedor->isPendente()) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-clock-history',
                    'titulo' => 'Loja em Análise Cadastral',
                    'mensagem' => 'Seus dados comerciais estão em análise pela administração. Você será notificado assim que aprovado.',
                ];
            } elseif ($vendedor && $vendedor->isRejeitado()) {
                $notificacoes[] = [
                    'tipo' => 'danger',
                    'icone' => 'bi-x-octagon-fill',
                    'titulo' => 'Loja Recusada pela Moderação',
                    'mensagem' => 'Sua solicitação de vendedor não foi aprovada. Revise suas informações comerciais.',
                    'link' => route('vendedor.perfil.editar'),
                    'link_texto' => 'Revisar Dados',
                ];
            } elseif ($vendedor && $vendedor->isAprovado()) {
                $notificacoes[] = [
                    'tipo' => 'success',
                    'icone' => 'bi-patch-check-fill',
                    'titulo' => 'Loja Ativa & Habilitada',
                    'mensagem' => 'Sua loja parceira está apta para publicar e comercializar livros no catálogo oficial.',
                    'link' => route('vendedor.livros.create'),
                    'link_texto' => 'Publicar Novo Livro',
                ];
            }

            // 2. Livros com estoque esgotado da própria loja
            if ($vendedor) {
                $estoqueZeradoLoja = $vendedor->livros()->where('quantidade', '<=', 0)->count();
                if ($estoqueZeradoLoja > 0) {
                    $notificacoes[] = [
                        'tipo' => 'danger',
                        'icone' => 'bi-box-seam-fill',
                        'titulo' => 'Estoque Esgotado na Loja',
                        'mensagem' => "Você possui {$estoqueZeradoLoja} livro(s) com quantidade zerada aguardando reposição.",
                        'link' => route('vendedor.livros.index', ['status_estoque' => 'sem_estoque']),
                        'link_texto' => 'Repor Estoque',
                    ];
                }

                // 3. Livros com estoque baixo (1 a 3 unidades)
                $estoqueBaixoLoja = $vendedor->livros()->where('quantidade', '>', 0)->where('quantidade', '<=', 3)->count();
                if ($estoqueBaixoLoja > 0) {
                    $notificacoes[] = [
                        'tipo' => 'warning',
                        'icone' => 'bi-exclamation-triangle-fill',
                        'titulo' => 'Estoque Baixo na sua Loja',
                        'mensagem' => "Você tem {$estoqueBaixoLoja} livro(s) com 3 ou menos unidades disponíveis.",
                        'link' => route('vendedor.livros.index'),
                        'link_texto' => 'Ajustar Quantidades',
                    ];
                }
            }
        } else {
            // Cliente
            // 1. Ausência de endereço principal para compras
            $temEnderecoPrincipal = $user->enderecos()->where('principal', true)->exists();
            if (!$temEnderecoPrincipal) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-geo-alt-fill',
                    'titulo' => 'Defina seu Endereço de Entrega',
                    'mensagem' => 'Você ainda não possui um endereço principal configurado. Cadastre para agilizar seus pedidos.',
                    'link' => route('painel', ['tab' => 'enderecos']),
                    'link_texto' => 'Cadastrar Endereço',
                ];
            }

            // 2. Dados cadastrais essenciais incompletos
            if (empty($user->cliente?->celular_contato) || empty($user->cliente?->cpf)) {
                $notificacoes[] = [
                    'tipo' => 'info',
                    'icone' => 'bi-person-badge-fill',
                    'titulo' => 'Complete seus Dados Pessoais',
                    'mensagem' => 'Preencha seu telefone e CPF no painel para facilitar contato e emissão de notas.',
                    'link' => route('painel', ['tab' => 'perfil']),
                    'link_texto' => 'Completar Perfil',
                ];
            }

            // 3. Convite para venda
            $solicitacaoVendedor = Vendedor::where('user_id', $user->id)->first();
            if ($solicitacaoVendedor && $solicitacaoVendedor->isPendente()) {
                $notificacoes[] = [
                    'tipo' => 'info',
                    'icone' => 'bi-hourglass-split',
                    'titulo' => 'Solicitação de Vendedor em Análise',
                    'mensagem' => "Sua solicitação para abrir a loja '{$solicitacaoVendedor->nome_fantasia}' está em avaliação.",
                ];
            } elseif (!$solicitacaoVendedor) {
                $notificacoes[] = [
                    'tipo' => 'primary',
                    'icone' => 'bi-shop',
                    'titulo' => 'Abra sua Loja Parceira',
                    'mensagem' => 'Deseja comercializar seus títulos em nosso catálogo? Cadastre sua loja parceira!',
                    'link' => route('vendedor.solicitar'),
                    'link_texto' => 'Quero Vender',
                ];
            }
        }

        return $notificacoes;
    }
}

