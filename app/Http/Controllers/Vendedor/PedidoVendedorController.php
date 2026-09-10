<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PedidoVendedorController extends Controller
{
    /**
     * Listar todos os pedidos recebidos pela loja do vendedor.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || !$vendedor->isAprovado()) {
            return redirect()->route('vendedor.painel')->with('error', 'Sua loja precisa estar aprovada para gerenciar pedidos.');
        }

        $query = Pedido::where('vendedor_id', $vendedor->id)
            ->with(['cliente.user', 'itens.livro', 'entrega', 'pagamentos'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = trim($request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('numero_pedido', 'like', "%{$busca}%")
                  ->orWhereHas('cliente.user', function ($u) use ($busca) {
                      $u->where('name', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%");
                  });
            });
        }

        $pedidos = $query->paginate(12)->withQueryString();

        // KPIs dos pedidos da loja
        $totalPedidos = Pedido::where('vendedor_id', $vendedor->id)->count();
        $totalPendentes = Pedido::where('vendedor_id', $vendedor->id)->whereIn('status', ['pendente', 'processando'])->count();
        $totalEnviados = Pedido::where('vendedor_id', $vendedor->id)->where('status', 'enviado')->count();
        $totalEntregues = Pedido::where('vendedor_id', $vendedor->id)->where('status', 'entregue')->count();

        return view('vendedor.pedidos.index', compact(
            'vendedor',
            'pedidos',
            'totalPedidos',
            'totalPendentes',
            'totalEnviados',
            'totalEntregues'
        ));
    }

    /**
     * Exibir detalhes completos de um pedido da loja.
     */
    public function show(Request $request, Pedido $pedido): View|RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || (int) $pedido->vendedor_id !== (int) $vendedor->id) {
            abort(403, 'Acesso restrito. Este pedido não pertence à sua loja.');
        }

        $pedido->load(['cliente.user', 'itens.livro', 'entrega', 'pagamentos', 'cupom']);

        return view('vendedor.pedidos.detalhes', compact('vendedor', 'pedido'));
    }

    /**
     * Atualizar o status logístico do pedido pelo vendedor.
     */
    public function atualizarStatus(Request $request, Pedido $pedido): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || (int) $pedido->vendedor_id !== (int) $vendedor->id) {
            abort(403, 'Acesso restrito. Este pedido não pertence à sua loja.');
        }

        $dados = $request->validate([
            'status' => ['required', 'in:processando,enviado,entregue,cancelado'],
            'codigo_rastreio' => ['nullable', 'string', 'max:50'],
        ]);

        $pedido->status = $dados['status'];
        $pedido->save();

        if ($pedido->entrega) {
            if ($dados['status'] === 'enviado') {
                $pedido->entrega->status = 'em_transito';
            } elseif ($dados['status'] === 'entregue') {
                $pedido->entrega->status = 'entregue';
            } elseif ($dados['status'] === 'cancelado') {
                $pedido->entrega->status = 'cancelado';
            }

            if (!empty($dados['codigo_rastreio'])) {
                $pedido->entrega->codigo_rastreio = $dados['codigo_rastreio'];
            }
            $pedido->entrega->save();
        }

        return back()->with('status', "Status do pedido {$pedido->numero_pedido} atualizado para '{$pedido->status_rotulo}'.");
    }
}

