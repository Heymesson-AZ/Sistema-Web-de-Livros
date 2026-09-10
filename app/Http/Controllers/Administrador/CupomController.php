<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Cupom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CupomController extends Controller
{
    /**
     * Listar todos os cupons de desconto cadastrados.
     */
    public function index(Request $request): View
    {
        $query = Cupom::withCount('pedidos')->latest();

        if ($busca = $request->input('busca')) {
            $query->where('codigo', 'like', "%{$busca}%");
        }

        $cupons = $query->paginate(15)->withQueryString();

        return view('administrador.cupons.index', compact('cupons'));
    }

    /**
     * Formulário de cadastro de novo cupom.
     */
    public function create(): View
    {
        if (!auth()->user()->podeGerenciarCupons()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para gerenciar cupons de desconto.');
        }

        return view('administrador.cupons.cadastrar');
    }

    /**
     * Salvar um novo cupom no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->podeGerenciarCupons()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para gerenciar cupons de desconto.');
        }

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:cupons,codigo'],
            'tipo_desconto' => ['required', 'in:percentual,valor_fixo'],
            'valor_desconto' => ['required', 'numeric', 'min:0.01'],
            'limite_uso' => ['nullable', 'integer', 'min:1'],
            'validade_cupom' => ['nullable', 'date', 'after_or_equal:today'],
            'requer_concordancia' => ['nullable', 'boolean'],
        ], [
            'codigo.required' => 'O código do cupom é obrigatório.',
            'codigo.unique' => 'Já existe um cupom cadastrado com este código.',
            'tipo_desconto.required' => 'Selecione o tipo de desconto.',
            'valor_desconto.required' => 'Informe o valor do desconto.',
            'validade_cupom.after_or_equal' => 'A validade não pode ser anterior a hoje.',
        ]);

        $dados['tipo_origem'] = 'plataforma';
        $dados['requer_concordancia'] = $request->boolean('requer_concordancia');

        Cupom::create($dados);

        return redirect()->route('admin.cupons.index')
            ->with('status', "Cupom '{$dados['codigo']}' criado com sucesso!");
    }

    /**
     * Formulário de edição de cupom existente.
     */
    public function edit(Cupom $cupon): View
    {
        if (!auth()->user()->podeGerenciarCupons()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para gerenciar cupons de desconto.');
        }

        return view('administrador.cupons.editar', ['cupom' => $cupon]);
    }

    /**
     * Atualizar cupom existente.
     */
    public function update(Request $request, Cupom $cupon): RedirectResponse
    {
        if (!auth()->user()->podeGerenciarCupons()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para gerenciar cupons de desconto.');
        }

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:cupons,codigo,' . $cupon->id],
            'tipo_desconto' => ['required', 'in:percentual,valor_fixo'],
            'valor_desconto' => ['required', 'numeric', 'min:0.01'],
            'limite_uso' => ['nullable', 'integer', 'min:1'],
            'validade_cupom' => ['nullable', 'date'],
            'requer_concordancia' => ['nullable', 'boolean'],
        ]);

        $dados['requer_concordancia'] = $request->boolean('requer_concordancia');

        $cupon->update($dados);

        return redirect()->route('admin.cupons.index')
            ->with('status', "Cupom '{$cupon->codigo}' atualizado com sucesso!");
    }

    /**
     * Excluir cupom do sistema.
     */
    public function destroy(Cupom $cupon): RedirectResponse
    {
        if (!auth()->user()->podeGerenciarCupons()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para gerenciar cupons de desconto.');
        }

        $codigo = $cupon->codigo;
        $cupon->delete();

        return redirect()->route('admin.cupons.index')
            ->with('status', "Cupom '{$codigo}' removido com sucesso.");
    }
}

