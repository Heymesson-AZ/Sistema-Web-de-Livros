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
        $tab = $request->query('tab', 'visao-geral');

        return view('paginas.painel', compact('user', 'notificacoes', 'kpis', 'tab', 'enderecos'));
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
            // 1. Vendedores pendentes de aprovação
            $vendedoresPendentes = Vendedor::where('status_aprovacao', Vendedor::STATUS_PENDENTE)->count();
            if ($vendedoresPendentes > 0) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-hourglass-split',
                    'titulo' => 'Solicitações de Vendedores Pendentes',
                    'mensagem' => "Existem {$vendedoresPendentes} loja(s) de vendedor aguardando avaliação da moderação.",
                    'link' => route('admin.vendedores.index'),
                    'link_texto' => 'Avaliar solicitações',
                ];
            }

            // 2. Livros com estoque crítico
            $livrosEstoqueBaixo = Livro::where('quantidade', '<=', 3)->count();
            if ($livrosEstoqueBaixo > 0) {
                $notificacoes[] = [
                    'tipo' => 'danger',
                    'icone' => 'bi-exclamation-triangle-fill',
                    'titulo' => 'Alerta de Estoque Crítico',
                    'mensagem' => "Existem {$livrosEstoqueBaixo} exemplar(es) com estoque zerado ou em nível crítico (<= 3 unidades).",
                    'link' => route('admin.livros.index'),
                    'link_texto' => 'Ver catálogo de livros',
                ];
            }
        } elseif ($user->isVendedor()) {
            $vendedor = $user->vendedor;

            // Notificação de status da loja
            if ($vendedor && $vendedor->isPendente()) {
                $notificacoes[] = [
                    'tipo' => 'warning',
                    'icone' => 'bi-clock-history',
                    'titulo' => 'Loja em Análise Cadastral',
                    'mensagem' => 'Seus dados comerciais foram recebidos e estão sob avaliação da equipe. Em breve você receberá a confirmação por e-mail.',
                ];
            } elseif ($vendedor && $vendedor->isAprovado()) {
                $notificacoes[] = [
                    'tipo' => 'success',
                    'icone' => 'bi-patch-check-fill',
                    'titulo' => 'Loja Ativa e Habilitada',
                    'mensagem' => 'Sua livraria parceira está aprovada e apta para publicar e comercializar livros.',
                    'link' => route('vendedor.livros.create'),
                    'link_texto' => 'Publicar novo livro',
                ];
            }

            // Livros com estoque crítico da loja
            if ($vendedor) {
                $estoqueCriticoLoja = $vendedor->livros()->where('quantidade', '<=', 3)->count();
                if ($estoqueCriticoLoja > 0) {
                    $notificacoes[] = [
                        'tipo' => 'danger',
                        'icone' => 'bi-box-seam-fill',
                        'titulo' => 'Estoque Baixo na sua Loja',
                        'mensagem' => "Você possui {$estoqueCriticoLoja} livro(s) com quantidade menor ou igual a 3 unidades.",
                        'link' => route('vendedor.livros.index'),
                        'link_texto' => 'Gerenciar estoque',
                    ];
                }
            }
        } else {
            // Cliente
            $solicitacaoVendedor = Vendedor::where('user_id', $user->id)->first();
            if ($solicitacaoVendedor) {
                if ($solicitacaoVendedor->isPendente()) {
                    $notificacoes[] = [
                        'tipo' => 'info',
                        'icone' => 'bi-hourglass-split',
                        'titulo' => 'Solicitação de Vendedor em Análise',
                        'mensagem' => "Sua solicitação de abertura da loja '{$solicitacaoVendedor->nome_fantasia}' está sendo avaliada.",
                    ];
                }
            } else {
                $notificacoes[] = [
                    'tipo' => 'primary',
                    'icone' => 'bi-shop',
                    'titulo' => 'Abra sua Loja Parceira',
                    'mensagem' => 'Cadastre sua livraria ou sebo e alcance leitores em todo o Brasil sem mensalidades.',
                    'link' => route('vendedor.solicitar'),
                    'link_texto' => 'Quero ser vendedor',
                ];
            }

            // Notificação de perfil incompleto
            if (!$user->foto_perfil || empty($user->cliente?->celular_contato)) {
                $notificacoes[] = [
                    'tipo' => 'info',
                    'icone' => 'bi-person-circle',
                    'titulo' => 'Complete seu Perfil',
                    'mensagem' => 'Adicione uma foto de perfil e seu celular de contato para uma experiência personalizada.',
                    'link' => '#aba-perfil',
                    'link_texto' => 'Completar dados',
                ];
            }
        }

        return $notificacoes;
    }
}

