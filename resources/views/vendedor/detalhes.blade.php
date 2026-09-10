<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                            class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendedores.index') }}"
                            class="text-decoration-none text-muted">Vendedores</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                        {{ $vendedor->nome_fantasia }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-shop text-primary me-2"></i>
                        Ficha do Vendedor
                    </h1>
                    <p class="text-muted small mb-0">Informações cadastrais e desempenho da livraria parceira.</p>
                </div>
                <a href="{{ route('admin.vendedores.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- CARTÃO DE RESUMO E AVATAR -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <img src="{{ $vendedor->user?->foto }}" alt="{{ $vendedor->nome_fantasia }}"
                        class="rounded-circle shadow-sm object-fit-cover mx-auto mb-3 border border-2 border-white"
                        style="width: 80px; height: 80px;">

                    <h4 class="fw-bold text-dark mb-1">{{ $vendedor->nome_fantasia }}</h4>
                    <p class="text-muted small mb-3">{{ $vendedor->razao_social }}</p>

                    <div class="mb-4 d-flex flex-column gap-1 align-items-center">
                        @if ($vendedor->status_aprovacao === 'aprovado')
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i> Loja Aprovada
                            </span>
                        @elseif ($vendedor->status_aprovacao === 'pendente')
                            <span
                                class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-hourglass-split me-1"></i> Aguardando Aprovação
                            </span>
                        @else
                            <span
                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-x-circle-fill me-1"></i> Loja Rejeitada
                            </span>
                        @endif

                        @if ($vendedor->user?->status === 'ativo')
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                                Conta Ativa
                            </span>
                        @elseif($vendedor->user?->status === 'inativo')
                            <span
                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 small">
                                Conta Inativa
                            </span>
                        @else
                            <span
                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                                Conta Banida
                            </span>
                        @endif
                    </div>

                    <!-- ESTATÍSTICAS RÁPIDAS -->
                    <div class="row g-2 pt-3 border-top text-start">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Livros Ativos</span>
                                <span class="fs-5 fw-bold text-primary">{{ $vendedor->livros?->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Pedidos</span>
                                <span class="fs-5 fw-bold text-success">{{ $vendedor->pedidos?->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- AÇÕES -->
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.vendedores.edit', $vendedor) }}" class="btn btn-primary rounded-3">
                            <i class="bi bi-pencil-square me-1"></i> Editar Dados
                        </a>
                    </div>
                </div>
            </div>

            <!-- DADOS COMPLETOS -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary"></i>
                        Dados Corporativos da Loja
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Nome Fantasia</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->nome_fantasia }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Razão Social</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->razao_social }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">CNPJ</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->cnpj }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Inscrição Estadual</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->inscricao_estadual }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Telefone Comercial</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $vendedor->telefone_comercial ?? 'Não informado' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Data de Registro</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $vendedor->created_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-primary"></i>
                        Representante / Conta de Acesso
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Nome do Responsável</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $vendedor->user?->name ?? 'Usuário não vinculado' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">E-mail</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->user?->email ?? '-' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Status do E-mail</span>
                            @if ($vendedor->user?->email_verified_at)
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Verificado em
                                    {{ $vendedor->user->email_verified_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-exclamation-circle me-1"></i> Não verificado
                                </span>
                            @endif
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Status do Acesso</span>
                            <span
                                class="badge {{ $vendedor->user?->status === 'ativo' ? 'bg-success' : ($vendedor->user?->status === 'inativo' ? 'bg-secondary' : 'bg-danger') }} rounded-pill text-capitalize px-3 py-1">
                                {{ $vendedor->user?->status ?? 'ativo' }}
                            </span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Última Atualização</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $vendedor->updated_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: GESTÃO E MODERAÇÃO DOS LIVROS DA LOJA -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
            <div
                class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-book text-primary"></i>
                        Catálogo de Livros da Loja
                    </h5>
                    <p class="text-muted small mb-0">
                        Inspecione os títulos cadastrados por esta livraria parceira e aplique ou remova restrições em
                        caso de violação de diretrizes.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.livros.index', ['vendedor_id' => $vendedor->id]) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Ver no Painel de Moderação</span>
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width: 70px;">Capa</th>
                            <th class="py-3">Título & ISBN</th>
                            <th class="py-3">Autor / Gênero</th>
                            <th class="py-3">Preço</th>
                            <th class="py-3 text-center">Estoque</th>
                            <th class="py-3">Moderação / Restrições</th>
                            <th class="pe-4 py-3 text-end" style="min-width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($vendedor->livros as $livro)
                            <tr>
                                <td class="ps-4 py-3">
                                    <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}"
                                        class="rounded shadow-xs object-fit-cover" style="width: 44px; height: 58px;">
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">{{ $livro->titulo }}</div>
                                    <div class="text-muted font-monospace small">ISBN: {{ $livro->isbn }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark small fw-semibold">
                                        {{ $livro->autor->nome ?? 'Não informado' }}</div>
                                    <span
                                        class="badge bg-light text-secondary border mt-1">{{ $livro->genero->nome ?? 'Geral' }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="fw-bold text-success">{{ $livro->preco_formatado }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    @if ($livro->quantidade > 0)
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                            {{ $livro->quantidade }} un.
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">
                                            Esgotado
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span
                                        class="badge {{ $livro->status_moderacao_badge_class }} rounded-pill px-2.5 py-1 fw-semibold">
                                        {{ $livro->status_moderacao_rotulo }}
                                    </span>
                                    @if ($livro->motivo_moderacao && !$livro->isAtivo())
                                        <small class="d-block text-danger mt-1 text-truncate"
                                            style="max-width: 200px;" title="{{ $livro->motivo_moderacao }}">
                                            <i
                                                class="bi bi-exclamation-circle me-1"></i>{{ $livro->motivo_moderacao }}
                                        </small>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <div class="actions-group justify-content-end">
                                        <!-- Botão para aplicar / remover restrições -->
                                        <button type="button" class="btn-action btn-action-moderate"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRestricaoLivro{{ $livro->id }}"
                                            title="Aplicar ou Remover Restrição" aria-label="Restrição">
                                            <i class="bi bi-shield-exclamation"></i>
                                        </button>

                                        <!-- Ver detalhes do livro -->
                                        <a href="{{ route('admin.livros.show', $livro) }}"
                                            class="btn-action btn-action-view" title="Visualizar Livro"
                                            aria-label="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>

                                    <!-- MODAL DE MODERAÇÃO DE RESTRIÇÃO DO LIVRO -->
                                    <div class="modal fade" id="modalRestricaoLivro{{ $livro->id }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <form action="{{ route('admin.livros.status', $livro) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5
                                                            class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                            <i class="bi bi-shield-lock-fill text-primary"></i>
                                                            Moderar Restrição: {{ $livro->titulo }}
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <p class="text-muted small mb-3">
                                                            Defina se este exemplar da loja
                                                            <strong>{{ $vendedor->nome_fantasia }}</strong> possui
                                                            alguma violação de regras ou se está liberado para venda.
                                                        </p>

                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Status
                                                                de Moderação</label>
                                                            <select name="status_moderacao"
                                                                class="form-select rounded-3" required>
                                                                <option value="ativo"
                                                                    {{ $livro->status_moderacao === 'ativo' ? 'selected' : '' }}>
                                                                    Ativo (Liberado para exibição e venda - Sem
                                                                    restrição)
                                                                </option>
                                                                <option value="sob_analise"
                                                                    {{ $livro->status_moderacao === 'sob_analise' ? 'selected' : '' }}>
                                                                    Sob Análise (Oculto preventivamente para checagem)
                                                                </option>
                                                                <option value="bloqueado_temporariamente"
                                                                    {{ $livro->status_moderacao === 'bloqueado_temporariamente' ? 'selected' : '' }}>
                                                                    Bloqueado Temporariamente (Suspensão por violação em
                                                                    apuração)
                                                                </option>
                                                                <option value="banido"
                                                                    {{ $livro->status_moderacao === 'banido' ? 'selected' : '' }}>
                                                                    Banido (Infração grave ou violação de direitos
                                                                    autorais)
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label
                                                                class="form-label small fw-bold text-secondary">Justificativa
                                                                da Restrição / Violação</label>
                                                            <textarea name="motivo_moderacao" rows="3" class="form-control rounded-3"
                                                                placeholder="Descreva a violação constatada ou motivo da restrição...">{{ $livro->motivo_moderacao }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-light rounded-3 px-3"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary rounded-3 px-3">
                                                            Salvar Regra de Moderação
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-book fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        <h6 class="fw-bold mb-1">Nenhum livro cadastrado por esta loja</h6>
                                        <p class="small mb-0">Esta livraria ainda não publicou títulos em seu catálogo.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.principal>
