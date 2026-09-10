<x-layouts.principal>
    <div class="container py-4">

        <!-- CABEÇALHO DA PÁGINA -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-shop text-primary me-2"></i>
                    Gestão de Vendedores
                </h1>
                <p class="text-muted small mb-0">
                    Gerencie os parceiros e lojistas da livraria Universo de Papel.
                </p>
            </div>
            <a href="{{ route('admin.vendedores.create') }}"
                class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Novo Vendedor</span>
            </a>
        </div>

        <!-- MENSAGENS DE STATUS E ERRO -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- CARDS DE MÉTRICAS (KPIS) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Vendedores</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalVendedores }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                            <i class="bi bi-shop fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Aptos para Vender</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $totalAptos ?? $totalAprovados }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #f0fdf4; color: #16a34a;">
                            <i class="bi bi-patch-check-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Pendentes de Análise</span>
                            <h3 class="fw-bold mb-0 text-warning mt-1">{{ $totalPendentes }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #fefce8; color: #ca8a04;">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Rejeitados / Banidos</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ $totalRejeitados + ($totalBanidos ?? 0) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #fef2f2; color: #dc2626;">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS E BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <form method="GET" action="{{ route('admin.vendedores.index') }}" class="row g-2 align-items-center">
                <!-- Busca textual -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="busca" class="form-control border-start-0 ps-0"
                            placeholder="Buscar responsável, e-mail, CNPJ ou loja..." value="{{ request('busca') }}"
                            data-dynamic-search autocomplete="off">
                    </div>
                </div>

                <!-- Filtro de Aprovação Cadastral -->
                <div class="col-6 col-md-2">
                    <select name="status_aprovacao" class="form-select">
                        <option value="">Aprovação (Todas)</option>
                        <option value="aprovado"
                            {{ ($statusAprovacaoSelecionado ?? request('status_aprovacao')) === 'aprovado' ? 'selected' : '' }}>
                            Aprovados</option>
                        <option value="pendente"
                            {{ ($statusAprovacaoSelecionado ?? request('status_aprovacao')) === 'pendente' ? 'selected' : '' }}>
                            Pendentes</option>
                        <option value="rejeitado"
                            {{ ($statusAprovacaoSelecionado ?? request('status_aprovacao')) === 'rejeitado' ? 'selected' : '' }}>
                            Rejeitados</option>
                    </select>
                </div>

                <!-- Filtro de Status da Conta -->
                <div class="col-6 col-md-2">
                    <select name="status_conta" class="form-select">
                        <option value="">Conta (Todas)</option>
                        <option value="ativo"
                            {{ ($statusContaSelecionado ?? request('status_conta')) === 'ativo' ? 'selected' : '' }}>
                            Ativo</option>
                        <option value="inativo"
                            {{ ($statusContaSelecionado ?? request('status_conta')) === 'inativo' ? 'selected' : '' }}>
                            Inativo</option>
                        <option value="banido"
                            {{ ($statusContaSelecionado ?? request('status_conta')) === 'banido' ? 'selected' : '' }}>
                            Banido</option>
                    </select>
                </div>

                <!-- Filtro de Aptidão Operacional -->
                <div class="col-6 col-md-2">
                    <select name="situacao" class="form-select">
                        <option value="">Aptidão (Todas)</option>
                        <option value="apto"
                            {{ ($situacaoSelecionada ?? request('situacao')) === 'apto' ? 'selected' : '' }}>Aptos para
                            Vender</option>
                        <option value="inapto"
                            {{ ($situacaoSelecionada ?? request('situacao')) === 'inapto' ? 'selected' : '' }}>Inaptos
                            / Bloqueados</option>
                    </select>
                </div>

                <!-- Botões de Filtrar e Limpar -->
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn-search-submit flex-grow-1">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                    @if (request()->hasAny(['busca', 'situacao', 'status', 'status_aprovacao', 'status_conta', 'aprovacao', 'conta']))
                        <a href="{{ route('admin.vendedores.index') }}" class="btn-search-clear" title="Limpar Filtros"
                            aria-label="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABELA DE VENDEDORES -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="min-width: 220px;">Loja / Empresa</th>
                            <th class="py-3" style="min-width: 200px;">Responsável & Contato</th>
                            <th class="py-3 text-center" style="width: 110px;">Conta</th>
                            <th class="py-3 text-center" style="width: 130px;">Aprovação</th>
                            <th class="py-3" style="min-width: 170px;">Aptidão de Venda</th>
                            <th class="py-3 text-center" style="width: 140px;">Catálogo</th>
                            <th class="pe-4 py-3 text-end" style="min-width: 180px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($vendedores as $vendedor)
                            <tr>
                                <!-- 1. Loja / Empresa -->
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($vendedor->user?->foto_perfil)
                                            <img src="{{ asset('storage/' . $vendedor->user->foto_perfil) }}"
                                                alt="{{ $vendedor->nome_fantasia }}"
                                                class="rounded-3 shadow-xs object-fit-cover flex-shrink-0"
                                                style="width: 44px; height: 44px;">
                                        @else
                                            <div class="rounded-3 shadow-xs d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 44px; height: 44px; background-color: #eff6ff; color: #2563eb;">
                                                <i class="bi bi-shop fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $vendedor->nome_fantasia }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 180px;"
                                                title="{{ $vendedor->razao_social }}">
                                                {{ $vendedor->razao_social }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Responsável & Contato -->
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">
                                        {{ $vendedor->user?->name ?? 'Usuário Removido' }}</div>
                                    <div class="text-muted small mb-1">{{ $vendedor->user?->email ?? '-' }}</div>
                                    <div class="d-flex flex-wrap gap-2 text-secondary" style="font-size: 11.5px;">
                                        <span title="CNPJ da Loja"><i
                                                class="bi bi-card-text me-1"></i>{{ $vendedor->cnpj }}</span>
                                        @if ($vendedor->telefone_comercial)
                                            <span title="Telefone Comercial"><i
                                                    class="bi bi-telephone me-1"></i>{{ $vendedor->telefone_comercial }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. Conta de Acesso -->
                                <td class="py-3 text-center">
                                    @if ($vendedor->user?->status === 'ativo')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold"
                                            style="font-size: 11px;">
                                            <i class="bi bi-person-check-fill me-1"></i> Ativo
                                        </span>
                                    @elseif($vendedor->user?->status === 'inativo')
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold"
                                            style="font-size: 11px;">
                                            <i class="bi bi-pause-circle-fill me-1"></i> Inativo
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-semibold"
                                            style="font-size: 11px;">
                                            <i class="bi bi-slash-circle-fill me-1"></i> Banido
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. Aprovação Cadastral -->
                                <td class="py-3 text-center">
                                    <span
                                        class="badge {{ $vendedor->aprovacao_badge_class }} rounded-pill px-2.5 py-1 fw-semibold"
                                        style="font-size: 11px;">
                                        <i class="bi {{ $vendedor->aprovacao_icone }} me-1"></i>
                                        {{ $vendedor->aprovacao_rotulo }}
                                    </span>
                                </td>

                                <!-- 5. Aptidão Operacional -->
                                <td class="py-3">
                                    @if ($vendedor->isAptoParaVender())
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Apto para Vender
                                        </span>
                                    @else
                                        <span
                                            class="badge {{ $vendedor->situacao_badge_class }} rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1"
                                            title="{{ $vendedor->motivo_inapto }}">
                                            <i class="bi {{ $vendedor->situacao_icone }}"></i>
                                            {{ $vendedor->situacao_rotulo }}
                                        </span>
                                        @if ($vendedor->motivo_inapto)
                                            <small class="d-block text-muted text-truncate mt-0.5"
                                                style="font-size: 11px; max-width: 170px;"
                                                title="{{ $vendedor->motivo_inapto }}">
                                                {{ $vendedor->motivo_inapto }}
                                            </small>
                                        @endif
                                    @endif
                                </td>

                                <!-- 6. Catálogo & Pedidos -->
                                <td class="py-3 text-center">
                                    <a href="{{ route('admin.livros.index', ['vendedor_id' => $vendedor->id]) }}"
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-2 py-1 me-1"
                                        title="Ver e moderar títulos desta loja">
                                        <i class="bi bi-book me-1"></i>{{ $vendedor->livros_count ?? 0 }}
                                    </a>
                                    <span class="badge bg-light text-secondary border px-2 py-1"
                                        title="Pedidos recebidos">
                                        <i
                                            class="bi bi-bag-check me-1 text-success"></i>{{ $vendedor->pedidos_count ?? 0 }}
                                    </span>
                                </td>

                                <!-- 7. Ações Padronizadas -->
                                <td class="pe-4 py-3 text-end">
                                    <div class="actions-group justify-content-end">
                                        <!-- Aprovação rápida se não aprovado -->
                                        @if ($vendedor->status_aprovacao !== 'aprovado')
                                            <form method="POST"
                                                action="{{ route('admin.vendedores.status', $vendedor) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="aprovado">
                                                <button type="submit" class="btn-action btn-action-approve"
                                                    title="Aprovar Cadastro da Loja" aria-label="Aprovar">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Rejeição rápida se não rejeitado -->
                                        @if ($vendedor->status_aprovacao !== 'rejeitado')
                                            <form method="POST"
                                                action="{{ route('admin.vendedores.status', $vendedor) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejeitado">
                                                <button type="submit" class="btn-action btn-action-reject"
                                                    title="Rejeitar Cadastro da Loja" aria-label="Rejeitar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Acesso Direto aos Livros da Loja -->
                                        <a href="{{ route('admin.livros.index', ['vendedor_id' => $vendedor->id]) }}"
                                            class="btn-action btn-action-view" title="Moderar Livros da Loja"
                                            aria-label="Livros">
                                            <i class="bi bi-book"></i>
                                        </a>

                                        <!-- Detalhes do Vendedor -->
                                        <a href="{{ route('admin.vendedores.show', $vendedor) }}"
                                            class="btn-action btn-action-view" title="Visualizar Detalhes Cadastrais"
                                            aria-label="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Editar Vendedor -->
                                        <a href="{{ route('admin.vendedores.edit', $vendedor) }}"
                                            class="btn-action btn-action-edit" title="Editar Vendedor"
                                            aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- Excluir Vendedor -->
                                        <button type="button" class="btn-action btn-action-delete"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $vendedor->id }}"
                                            title="Excluir Vendedor" aria-label="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
                                    <div class="modal fade" id="deleteModal{{ $vendedor->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5
                                                        class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                        Confirmar Exclusão
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <p class="text-secondary mb-2">
                                                        Tem certeza que deseja excluir o vendedor
                                                        <strong>{{ $vendedor->nome_fantasia }}</strong> (CNPJ:
                                                        {{ $vendedor->cnpj }})?
                                                    </p>
                                                    <p class="text-danger small mb-0 fw-semibold">
                                                        Atenção: A conta de acesso associada a este vendedor também será
                                                        excluída permanentemente.
                                                    </p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-3 px-3"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST"
                                                        action="{{ route('admin.vendedores.destroy', $vendedor) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-3 px-3">
                                                            Sim, Excluir Vendedor
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        <h6 class="fw-bold mb-1">Nenhum vendedor encontrado</h6>
                                        <p class="small mb-3">Tente ajustar seus termos de busca ou cadastrar um novo
                                            vendedor.</p>
                                        @if (request()->hasAny(['busca', 'situacao', 'status', 'status_aprovacao', 'status_conta', 'aprovacao', 'conta']))
                                            <a href="{{ route('admin.vendedores.index') }}"
                                                class="btn btn-sm btn-outline-primary rounded-3">
                                                Limpar Filtros
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINAÇÃO -->
            @if ($vendedores->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $vendedores->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
