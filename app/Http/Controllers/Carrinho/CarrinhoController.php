<?php

namespace App\Http\Controllers\Carrinho;

use App\Http\Controllers\Controller;
use App\Models\Carrinho;
use App\Models\Cupom;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarrinhoController extends Controller
{
    /**
     * Exibir a página do carrinho com os itens, subtotais e resumo.
     */
    public function index(Request $request): View
    {
        $itens = $this->obterItensCarrinho();
        $subtotal = $itens->sum(fn ($item) => (float) $item->preco * (int) $item->quantidade_carrinho);

        // Cupom de Desconto
        $cupom = null;
        $desconto = 0.0;
        $codigoCupom = session('cupom_codigo');
        if ($codigoCupom) {
            $cupom = Cupom::where('codigo', strtoupper($codigoCupom))->first();
            if ($cupom && $cupom->isValido()) {
                $resultadoCupom = $cupom->calcularDescontoParaItens($itens);
                if ($resultadoCupom['aplicavel']) {
                    $desconto = $resultadoCupom['desconto'];
                } else {
                    session()->forget('cupom_codigo');
                    $cupom = null;
                }
            } else {
                session()->forget('cupom_codigo');
                $cupom = null;
            }
        }

        // Frete simulado: Grátis acima de R$ 99,00; caso contrário R$ 15,00
        $valorFrete = ($subtotal > 0 && $subtotal >= 99.00) ? 0.0 : ($subtotal > 0 ? 15.00 : 0.0);
        $total = max(0.0, ($subtotal - $desconto) + $valorFrete);

        return view('carrinho.index', compact('itens', 'subtotal', 'cupom', 'desconto', 'valorFrete', 'total'));
    }

    /**
     * Adicionar exemplar de livro ao carrinho.
     */
    public function adicionar(Request $request): JsonResponse|RedirectResponse
    {
        $dados = $request->validate([
            'livro_id' => ['required', 'exists:livros,id'],
            'quantidade' => ['nullable', 'integer', 'min:1'],
        ]);

        $livro = Livro::findOrFail($dados['livro_id']);
        $quantidadeAdicionar = (int) ($dados['quantidade'] ?? 1);

        if (!$livro->isAtivo()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este exemplar não está disponível para aquisição no momento.',
                ], 422);
            }
            return back()->with('erro', 'Este exemplar não está disponível para aquisição no momento.');
        }

        if ($livro->quantidade <= 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Este título está esgotado no momento.',
                ], 422);
            }
            return back()->with('error', 'Este título está esgotado no momento.');
        }

        if (Auth::check()) {
            $carrinho = Carrinho::firstOrCreate(['user_id' => Auth::id()]);
            $itemExistente = $carrinho->livros()->where('livro_id', $livro->id)->first();
            $qtdAtual = $itemExistente ? (int) $itemExistente->pivot->quantidade : 0;
            $novaQuantidade = min($qtdAtual + $quantidadeAdicionar, $livro->quantidade);

            $carrinho->livros()->syncWithoutDetaching([
                $livro->id => ['quantidade' => $novaQuantidade],
            ]);
            $totalItens = (int) $carrinho->livros()->sum('carrinho_livro.quantidade');
        } else {
            $sessao = session()->get('carrinho', []);
            $qtdAtual = $sessao[$livro->id] ?? 0;
            $novaQuantidade = min($qtdAtual + $quantidadeAdicionar, $livro->quantidade);
            $sessao[$livro->id] = $novaQuantidade;
            session()->put('carrinho', $sessao);
            $totalItens = array_sum($sessao);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => "'{$livro->titulo}' foi adicionado ao seu carrinho!",
                'total_itens' => $totalItens,
            ]);
        }

        if ($request->boolean('comprar_agora')) {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('carrinho.index')
            ->with('status', "'{$livro->titulo}' foi adicionado ao seu carrinho!");
    }

    /**
     * Atualizar a quantidade de um livro específico no carrinho.
     */
    public function atualizar(Request $request, Livro $livro): JsonResponse|RedirectResponse
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $quantidade = min((int) $dados['quantidade'], $livro->quantidade);

        if (Auth::check()) {
            $carrinho = Carrinho::firstOrCreate(['user_id' => Auth::id()]);
            $carrinho->livros()->updateExistingPivot($livro->id, ['quantidade' => $quantidade]);
        } else {
            $sessao = session()->get('carrinho', []);
            $sessao[$livro->id] = $quantidade;
            session()->put('carrinho', $sessao);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Quantidade atualizada com sucesso.',
            ]);
        }

        return redirect()->route('carrinho.index')->with('status', 'Quantidade do carrinho atualizada com sucesso.');
    }

    /**
     * Remover um exemplar do carrinho.
     */
    public function remover(Request $request, Livro $livro): RedirectResponse
    {
        if (Auth::check()) {
            $carrinho = Auth::user()->carrinho;
            if ($carrinho) {
                $carrinho->livros()->detach($livro->id);
            }
        } else {
            $sessao = session()->get('carrinho', []);
            unset($sessao[$livro->id]);
            session()->put('carrinho', $sessao);
        }

        return redirect()->route('carrinho.index')->with('status', 'Item removido do seu carrinho.');
    }

    /**
     * Limpar todos os itens do carrinho.
     */
    public function limpar(): RedirectResponse
    {
        if (Auth::check()) {
            $carrinho = Auth::user()->carrinho;
            if ($carrinho) {
                $carrinho->livros()->detach();
            }
        } else {
            session()->forget('carrinho');
        }

        session()->forget('cupom_codigo');

        return redirect()->route('carrinho.index')
            ->with('status', 'Seu carrinho foi esvaziado.');
    }

    /**
     * Aplicar cupom de desconto promocional.
     */
    public function aplicarCupom(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => ['required', 'string', 'max:50'],
        ], [
            'codigo.required' => 'Informe o código do cupom.',
        ]);

        $codigo = strtoupper(trim($request->codigo));
        $cupom = Cupom::where('codigo', $codigo)->first();

        if (!$cupom || !$cupom->isValido()) {
            return redirect()->route('carrinho.index')->with('error', 'Cupom inválido, expirado ou com limite de usos atingido.');
        }

        $itens = $this->obterItensCarrinho();
        $resultadoCupom = $cupom->calcularDescontoParaItens($itens);

        if (!$resultadoCupom['aplicavel']) {
            return redirect()->route('carrinho.index')->with('error', $resultadoCupom['motivo']);
        }

        session(['cupom_codigo' => $cupom->codigo]);

        return redirect()->route('carrinho.index')->with('status', "Cupom '{$cupom->codigo}' aplicado com sucesso! ({$cupom->descricao_desconto})");
    }

    /**
     * Remover cupom de desconto aplicado.
     */
    public function removerCupom(): RedirectResponse
    {
        session()->forget('cupom_codigo');

        return redirect()->route('carrinho.index')->with('status', 'Cupom de desconto removido.');
    }

    /**
     * Retorna a coleção de itens do carrinho unificada para usuário ou visitante.
     */
    public static function obterItensCarrinho(): \Illuminate\Support\Collection
    {
        if (Auth::check()) {
            $carrinho = Carrinho::firstOrCreate(['user_id' => Auth::id()]);
            return $carrinho->livros()
                ->with(['autor', 'vendedor', 'editora'])
                ->get()
                ->map(function ($livro) {
                    $livro->quantidade_carrinho = (int) $livro->pivot->quantidade;
                    return $livro;
                });
        }

        $sessao = session()->get('carrinho', []);
        if (empty($sessao)) {
            return collect();
        }

        $ids = array_keys($sessao);
        return Livro::with(['autor', 'vendedor', 'editora'])
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($livro) use ($sessao) {
                $livro->quantidade_carrinho = (int) ($sessao[$livro->id] ?? 1);
                return $livro;
            });
    }

    /**
     * Quantidade total de itens no carrinho (para badges no menu e navbar).
     */
    public static function obterContagemItens(): int
    {
        if (Auth::check()) {
            $carrinho = Auth::user()->carrinho;
            return $carrinho ? (int) $carrinho->livros()->sum('carrinho_livro.quantidade') : 0;
        }

        $sessao = session()->get('carrinho', []);
        return (int) array_sum($sessao);
    }
}
