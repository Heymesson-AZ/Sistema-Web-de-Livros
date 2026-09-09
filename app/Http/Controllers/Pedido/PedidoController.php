<?php

namespace App\Http\Controllers\Pedido;

use App\Http\Controllers\Carrinho\CarrinhoController;
use App\Http\Controllers\Controller;
use App\Models\CartaoSalvo;
use App\Models\Cliente;
use App\Models\Cupom;
use App\Models\Endereco;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\PedidoEntrega;
use App\Models\PedidoItem;
use App\Models\Vendedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    /**
     * Tela de Checkout (Revisão, Endereço de Entrega e Pagamento).
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $itens = CarrinhoController::obterItensCarrinho();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Seu carrinho está vazio. Adicione livros antes de finalizar a compra.');
        }

        $subtotal = $itens->sum(fn ($item) => (float) $item->preco * (int) $item->quantidade_carrinho);

        // Cupom de Desconto
        $cupom = null;
        $desconto = 0.0;
        $codigoCupom = session('cupom_codigo');
        if ($codigoCupom) {
            $cupom = Cupom::where('codigo', strtoupper($codigoCupom))->first();
            if ($cupom && $cupom->isValido()) {
                $desconto = $cupom->calcularDesconto($subtotal);
            }
        }

        $valorFrete = ($subtotal > 0 && $subtotal >= 99.00) ? 0.0 : 15.00;
        $total = max(0.0, ($subtotal - $desconto) + $valorFrete);

        $enderecos = $user->enderecos()->orderByDesc('principal')->get();
        $cartoesSalvos = $user->cartoesSalvos()->orderByDesc('cartao_padrao')->get();

        return view('checkout.index', compact('itens', 'subtotal', 'cupom', 'desconto', 'valorFrete', 'total', 'enderecos', 'cartoesSalvos'));
    }

    /**
     * Finalizar compra e criar Pedido, Entrega, Itens e Pagamento.
     */
    public function finalizar(Request $request): RedirectResponse
    {
        $user = $request->user();
        $itens = CarrinhoController::obterItensCarrinho();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $dados = $request->validate([
            'endereco_id' => ['required', 'exists:enderecos,id'],
            'metodo_pagamento' => ['required', 'in:cartao_credito,pix,boleto,cartao_debito'],
            'cartao_salvo_id' => ['nullable', 'exists:cartoes_salvos,id'],
            'numero_cartao' => ['nullable', 'string'],
            'nome_titular' => ['nullable', 'string', 'max:100'],
            'validade_cartao' => ['nullable', 'string', 'max:7'],
            'cvv' => ['nullable', 'string', 'max:4'],
            'salvar_cartao' => ['nullable', 'boolean'],
        ], [
            'endereco_id.required' => 'Selecione um endereço para a entrega do pedido.',
            'metodo_pagamento.required' => 'Escolha uma forma de pagamento.',
        ]);

        $endereco = $user->enderecos()->findOrFail($dados['endereco_id']);

        // Garante que o usuário possua perfil de cliente
        $cliente = $user->cliente;
        if (!$cliente) {
            $cliente = Cliente::create([
                'user_id' => $user->id,
                'celular_contato' => '11999999999',
            ]);
        }

        $pedido = DB::transaction(function () use ($user, $cliente, $endereco, $dados, $itens) {
            // 1. Validação de estoque e cálculo de subtotal
            $subtotal = 0.0;
            foreach ($itens as $item) {
                if ($item->quantidade < $item->quantidade_carrinho) {
                    throw new \Exception("O livro '{$item->titulo}' possui apenas {$item->quantidade} unidade(s) em estoque.");
                }
                $subtotal += (float) $item->preco * (int) $item->quantidade_carrinho;
            }

            // 2. Cupom de Desconto
            $cupom = null;
            $desconto = 0.0;
            $codigoCupom = session('cupom_codigo');
            if ($codigoCupom) {
                $cupom = Cupom::where('codigo', strtoupper($codigoCupom))->first();
                if ($cupom && $cupom->isValido()) {
                    $desconto = $cupom->calcularDesconto($subtotal);
                }
            }

            $valorFrete = ($subtotal > 0 && $subtotal >= 99.00) ? 0.0 : 15.00;
            $total = max(0.0, ($subtotal - $desconto) + $valorFrete);

            // 3. Vendedor do pedido (vinculado ao primeiro item ou fallback)
            $primeiroLivro = $itens->first();
            $vendedorId = $primeiroLivro->vendedor_id ?? Vendedor::value('id');

            // 4. Criação do Pedido
            $numeroPedido = 'PED-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $statusInicial = ($dados['metodo_pagamento'] === 'cartao_credito') ? 'processando' : 'pendente';

            $pedidoCriado = Pedido::create([
                'numero_pedido' => $numeroPedido,
                'status' => $statusInicial,
                'total' => $total,
                'data_pedido' => now(),
                'vendedor_id' => $vendedorId,
                'cliente_id' => $cliente->id,
                'cupom_id' => $cupom?->id,
            ]);

            // 5. Snapshot de Entrega
            PedidoEntrega::create([
                'pedido_id' => $pedidoCriado->id,
                'rua' => $endereco->rua,
                'numero' => $endereco->numero,
                'bairro' => $endereco->bairro,
                'cidade' => $endereco->cidade,
                'estado' => $endereco->estado,
                'cep' => $endereco->cep,
                'pais' => $endereco->pais ?? 'Brasil',
                'complemento' => $endereco->complemento,
                'status' => 'pendente',
                'valor_frete' => $valorFrete,
                'metodo_envio' => 'correios',
                'data_previsao_entrega' => now()->addDays(5),
            ]);

            // 6. Itens do Pedido & Baixa de Estoque
            foreach ($itens as $item) {
                PedidoItem::create([
                    'pedido_id' => $pedidoCriado->id,
                    'livro_id' => $item->id,
                    'quantidade_itens' => $item->quantidade_carrinho,
                    'valor_unitario' => $item->preco,
                ]);

                // Decremento imediato do estoque
                $item->decrement('quantidade', $item->quantidade_carrinho);
            }

            // 7. Registro do Pagamento
            $statusPagamento = ($dados['metodo_pagamento'] === 'cartao_credito') ? 'aprovado' : 'pendente';
            Pagamento::create([
                'pedido_id' => $pedidoCriado->id,
                'metodo_pagamento' => $dados['metodo_pagamento'],
                'status_pagamento' => $statusPagamento,
                'id_transacao' => 'TX-' . strtoupper(uniqid()),
                'valor_pago' => $total,
                'data_confirmacao_pagamento' => ($statusPagamento === 'aprovado' ? now() : null),
            ]);

            // 8. Salvar cartão se solicitado
            if (!empty($dados['salvar_cartao']) && !empty($dados['numero_cartao'])) {
                $numLimpo = preg_replace('/\D/', '', $dados['numero_cartao']);
                $ultimosDigitos = substr($numLimpo, -4) ?: '4242';
                $bandeira = str_starts_with($numLimpo, '4') ? 'visa' : (str_starts_with($numLimpo, '5') ? 'mastercard' : 'elo');

                CartaoSalvo::create([
                    'user_id' => $user->id,
                    'token_cartao' => 'tok_' . bin2hex(random_bytes(10)),
                    'ultimos_digitos' => $ultimosDigitos,
                    'bandeira_cartao' => $bandeira,
                    'cartao_padrao' => $user->cartoesSalvos()->count() === 0,
                ]);
            }

            // 9. Esvaziar carrinho e limpar cupom da sessão
            if ($user->carrinho) {
                $user->carrinho->livros()->detach();
            }
            session()->forget('carrinho');
            session()->forget('cupom_codigo');

            return $pedidoCriado;
        });

        return redirect()->route('pedidos.sucesso', $pedido)
            ->with('status', 'Pedido realizado com sucesso!');
    }

    /**
     * Tela de confirmação e sucesso do pedido.
     */
    public function sucesso(Pedido $pedido): View
    {
        $this->autorizarAcessoPedido($pedido);

        $pedido->load(['itens.livro.autor', 'entrega', 'pagamentos', 'cupom']);

        return view('pedidos.sucesso', compact('pedido'));
    }

    /**
     * Visualização detalhada de um pedido.
     */
    public function detalhes(Pedido $pedido): View
    {
        $this->autorizarAcessoPedido($pedido);

        $pedido->load(['itens.livro.autor', 'entrega', 'pagamentos', 'cupom', 'avaliacao']);

        return view('pedidos.detalhes', compact('pedido'));
    }

    /**
     * Atualização do status do pedido e código de rastreamento (Admin ou Vendedor).
     */
    public function atualizarStatus(Request $request, Pedido $pedido): RedirectResponse
    {
        $user = $request->user();

        // Apenas Admin ou o Vendedor responsável pelo pedido podem alterar o status
        if (!$user->isAdmin() && $user->vendedor?->id !== $pedido->vendedor_id) {
            abort(403, 'Você não tem permissão para alterar o status deste pedido.');
        }

        $dados = $request->validate([
            'status' => ['required', 'in:pendente,processando,enviado,entregue,cancelado,devolvido'],
            'codigo_rastreamento' => ['nullable', 'string', 'max:50'],
        ]);

        $pedido->update(['status' => $dados['status']]);

        if ($pedido->entrega) {
            $statusEntrega = match ($dados['status']) {
                'enviado' => 'em_transito',
                'entregue' => 'entregue',
                'cancelado' => 'cancelada',
                default => 'pendente',
            };

            $atualizacaoEntrega = ['status' => $statusEntrega];
            if (!empty($dados['codigo_rastreamento'])) {
                $atualizacaoEntrega['codigo_rastreamento'] = strtoupper(trim($dados['codigo_rastreamento']));
            }
            if ($dados['status'] === 'entregue') {
                $atualizacaoEntrega['data_entrega'] = now();
            }

            $pedido->entrega->update($atualizacaoEntrega);
        }

        return back()->with('status', "Status do pedido {$pedido->numero_pedido} atualizado com sucesso!");
    }

    /**
     * Verifica se o usuário atual pode visualizar o pedido.
     */
    private function autorizarAcessoPedido(Pedido $pedido): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->cliente && $pedido->cliente_id === $user->cliente->id) {
            return;
        }

        if ($user->vendedor && $pedido->vendedor_id === $user->vendedor->id) {
            return;
        }

        abort(403, 'Acesso não autorizado a este pedido.');
    }
}

