<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Livro\LivroGestaoController;
use App\Models\Livro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controlador de Gestão e Moderação de Livros para Administradores
 *
 * O Administrador gerencia e fiscaliza a integridade da plataforma:
 * Modera o catálogo geral, inspeciona dados, aplica regras de conformidade
 * (ativo, sob análise, bloqueado temporariamente, banido) e remove conteúdos criminosos ou irregulares.
 * A publicação de novos livros é atribuição exclusiva dos vendedores lojistas parceiros.
 */
class LivroAdminController extends LivroGestaoController
{
    /**
     * Define o contexto administrativo
     */
    protected bool $isAdmin = true;

    /**
     * Regra de Negócio: Administradores não publicam livros diretamente.
     * Redireciona para a moderação de livros com mensagem informativa.
     */
    public function create(): View|RedirectResponse
    {
        return redirect()->route('admin.livros.index')
            ->with('info', 'A publicação de livros é atribuição exclusiva dos lojistas e vendedores parceiros. O Administrador é responsável pela moderação, análise e fiscalização do catálogo.');
    }

    /**
     * Regra de Negócio: Bloqueia tentativa direta de submissão de cadastro de livro por administradores.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.livros.index')
            ->with('info', 'A publicação de livros é atribuição exclusiva dos lojistas e vendedores parceiros. O Administrador é responsável pela moderação, análise e fiscalização do catálogo.');
    }

    /**
     * Alterar o status de moderação de um livro com registro de motivo, data e moderador responsável.
     * Regras: ativo, sob_analise, bloqueado_temporariamente, banido.
     */
    public function alterarStatusModeracao(Request $request, Livro $livro): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->podeModerarLivros()) {
            abort(403, 'Acesso restrito. Seu cargo não possui permissão para executar a moderação de livros.');
        }

        $request->validate([
            'status_moderacao' => [
                'required',
                'string',
                Rule::in([
                    Livro::STATUS_MODERACAO_ATIVO,
                    Livro::STATUS_MODERACAO_SOB_ANALISE,
                    Livro::STATUS_MODERACAO_BLOQUEADO,
                    Livro::STATUS_MODERACAO_BANIDO,
                ]),
            ],
            'motivo_moderacao' => ['nullable', 'string', 'max:1000'],
        ], [
            'status_moderacao.required' => 'O status de moderação é obrigatório.',
            'status_moderacao.in' => 'Status de moderação inválido.',
            'motivo_moderacao.max' => 'A justificativa não pode ultrapassar 1000 caracteres.',
        ]);

        $statusAnterior = $livro->status_moderacao;
        $novoStatus = $request->input('status_moderacao');
        $motivo = $request->filled('motivo_moderacao') ? trim($request->input('motivo_moderacao')) : null;

        $livro->update([
            'status_moderacao' => $novoStatus,
            'motivo_moderacao' => $motivo,
            'moderado_em' => now(),
            'moderado_por' => $user->id,
        ]);

        $mensagens = [
            Livro::STATUS_MODERACAO_ATIVO => "O livro '{$livro->titulo}' foi aprovado e está ativo no catálogo público.",
            Livro::STATUS_MODERACAO_SOB_ANALISE => "O livro '{$livro->titulo}' foi colocado sob análise preventiva e está temporariamente oculto do catálogo público.",
            Livro::STATUS_MODERACAO_BLOQUEADO => "O livro '{$livro->titulo}' foi bloqueado temporariamente por suspeita de irregularidade cadastral.",
            Livro::STATUS_MODERACAO_BANIDO => "O livro '{$livro->titulo}' foi banido em definitivo por infração grave ou violação legal.",
        ];

        return back()->with('status', $mensagens[$novoStatus] ?? 'Status de moderação atualizado com sucesso.');
    }
}
