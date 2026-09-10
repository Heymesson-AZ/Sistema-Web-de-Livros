<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Cupom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CupomVendedorController extends Controller
{
    /**
     * Listar cupons exclusivos da loja e campanhas promocionais de incentivo.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $vendedor = $user->vendedor;

        if (!$vendedor || !$vendedor->isAprovado()) {
            return redirect()->route('vendedor.painel')->with('error', 'Sua loja precisa estar aprovada para gerenciar cupons.');
        }

        $cupons = $vendedor->cupons()
            ->withCount('pedidos')
            ->latest()
            ->paginate(10);

        // Campanhas promocionais da plataforma que exigem concordância/adesão de vendedores
        $campanhas = Cupom::where('requer_concordancia', true)
            ->where('tipo_origem', 'plataforma')
            ->with('vendedoresAderidos')
            ->latest()
            ->get();

        $campanhasAderidasIds = $vendedor->campanhasAderidas()->pluck('cupons.id')->toArray();

        return view('vendedor.cupons.index', compact('vendedor', 'cupons', 'campanhas', 'campanhasAderidasIds'));
    }

    /**
     * Formulário de cadastro de novo cupom exclusivo da loja.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || !$vendedor->isAprovado()) {
            return redirect()->route('vendedor.painel')->with('error', 'Sua loja precisa estar aprovada para criar cupons.');
        }

        return view('vendedor.cupons.cadastrar', compact('vendedor'));
    }

    /**
     * Salvar novo cupom da loja.
     */
    public function store(Request $request): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || !$vendedor->isAprovado()) {
            abort(403, 'Acesso restrito.');
        }

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:cupons,codigo'],
            'tipo_desconto' => ['required', 'in:percentual,valor_fixo'],
            'valor_desconto' => ['required', 'numeric', 'min:0.01'],
            'limite_uso' => ['nullable', 'integer', 'min:1'],
            'validade_cupom' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'codigo.required' => 'O código do cupom é obrigatório.',
            'codigo.unique' => 'Já existe um cupom cadastrado com este código.',
            'tipo_desconto.required' => 'Selecione o tipo de desconto.',
            'valor_desconto.required' => 'Informe o valor do desconto.',
            'validade_cupom.after_or_equal' => 'A validade não pode ser anterior a hoje.',
        ]);

        $dados['vendedor_id'] = $vendedor->id;
        $dados['tipo_origem'] = 'loja';
        $dados['requer_concordancia'] = false;

        Cupom::create($dados);

        return redirect()->route('vendedor.cupons.index')
            ->with('status', "Cupom '{$dados['codigo']}' criado com sucesso para sua loja!");
    }

    /**
     * Formulário de edição de cupom da loja.
     */
    public function edit(Request $request, Cupom $cupom): View|RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || (int) $cupom->vendedor_id !== (int) $vendedor->id) {
            abort(403, 'Acesso restrito. Este cupom não pertence à sua loja.');
        }

        return view('vendedor.cupons.editar', compact('vendedor', 'cupom'));
    }

    /**
     * Atualizar cupom da loja.
     */
    public function update(Request $request, Cupom $cupom): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || (int) $cupom->vendedor_id !== (int) $vendedor->id) {
            abort(403, 'Acesso restrito. Este cupom não pertence à sua loja.');
        }

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:cupons,codigo,' . $cupom->id],
            'tipo_desconto' => ['required', 'in:percentual,valor_fixo'],
            'valor_desconto' => ['required', 'numeric', 'min:0.01'],
            'limite_uso' => ['nullable', 'integer', 'min:1'],
            'validade_cupom' => ['nullable', 'date'],
        ]);

        $cupom->update($dados);

        return redirect()->route('vendedor.cupons.index')
            ->with('status', "Cupom '{$cupom->codigo}' atualizado com sucesso!");
    }

    /**
     * Excluir cupom da loja.
     */
    public function destroy(Request $request, Cupom $cupom): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || (int) $cupom->vendedor_id !== (int) $vendedor->id) {
            abort(403, 'Acesso restrito. Este cupom não pertence à sua loja.');
        }

        $codigo = $cupom->codigo;
        $cupom->delete();

        return redirect()->route('vendedor.cupons.index')
            ->with('status', "Cupom '{$codigo}' removido com sucesso.");
    }

    /**
     * Concordar e aderir a uma campanha promocional de incentivo da plataforma.
     */
    public function aderirCampanha(Request $request, Cupom $cupom): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor || !$vendedor->isAprovado()) {
            abort(403, 'Acesso restrito.');
        }

        if (!$cupom->requer_concordancia) {
            return back()->with('error', 'Este cupom não requer adesão manual.');
        }

        $vendedor->campanhasAderidas()->syncWithoutDetaching([
            $cupom->id => [
                'concordou' => true,
                'data_adesao' => now(),
            ],
        ]);

        return back()->with('status', "Sua loja concordou e agora participa da campanha promocional '{$cupom->codigo}'!");
    }

    /**
     * Retirar adesão da campanha promocional.
     */
    public function desistirCampanha(Request $request, Cupom $cupom): RedirectResponse
    {
        $vendedor = $request->user()->vendedor;
        if (!$vendedor) {
            abort(403, 'Acesso restrito.');
        }

        $vendedor->campanhasAderidas()->detach($cupom->id);

        return back()->with('status', "Sua loja foi retirada da campanha promocional '{$cupom->codigo}'.");
    }
}

