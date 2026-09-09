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
                            <span class="text-muted small fw-semibold text-uppercase">Aprovados</span>
                            <span class="text-muted small fw-semibold text-uppercase">Aprovados & Ativos</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $totalAprovados }}</h3>
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
                            <span class="text-muted small fw-semibold text-uppercase">Pendentes</span>
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
                            <span class="text-muted small fw-semibold text-uppercase">Rejeitados</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ $totalRejeitados }}</h3>
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
                <div class="col-12 col-md-5">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="busca" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por nome, e-mail, CNPJ ou razão social..."
                            value="{{ request('busca') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Aprovação (Todos)</option>
                        <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovados
                        </option>
                        <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendentes
                        </option>
                        <option value="rejeitado" {{ request('status') === 'rejeitado' ? 'selected' : '' }}>Rejeitados
                        </option>
                <div class="col-12 col-md-4">
                    <select name="situacao" class="form-select">
                        <option value="">Situação da Loja (Todas)</option>
                        <option value="aprovado" {{ ($situacaoSelecionada ?? request('status')) === 'aprovado' ? 'selected' : '' }}>Aprovados & Ativos</option>
                        <option value="pendente" {{ ($situacaoSelecionada ?? request('status')) === 'pendente' ? 'selected' : '' }}>Pendentes de Análise</option>
                        <option value="inativo" {{ ($situacaoSelecionada ?? request('status')) === 'inativo' ? 'selected' : '' }}>Inativos / Pausados</option>
                        <option value="rejeitado" {{ ($situacaoSelecionada ?? request('status')) === 'rejeitado' ? 'selected' : '' }}>Rejeitados</option>
                        <option value="banido" {{ ($situacaoSelecionada ?? request('status')) === 'banido' ? 'selected' : '' }}>Banidos</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status_conta" class="form-select">
                        <option value="">Conta (Todas)</option>
                        <option value="ativo" {{ request('status_conta') === 'ativo' ? 'selected' : '' }}>Ativo
                        </option>
                        <option value="inativo" {{ request('status_conta') === 'inativo' ? 'selected' : '' }}>Inativo
                        </option>
                        <option value="banido" {{ request('status_conta') === 'banido' ? 'selected' : '' }}>Banido
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                    @if (request()->hasAny(['busca', 'status', 'status_conta']))
                    @if (request()->hasAny(['busca', 'situacao', 'status', 'status_conta']))
                        <a href="{{ route('admin.vendedores.index') }}" class="btn btn-outline-secondary"
                            title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABELA DE VENDEDORES -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3">Loja / Empresa</th>
                            <th class="py-3">Responsável</th>
                            <th class="py-3">CNPJ / Contato</th>
                            <th class="py-3">Aprovação</th>
                            <th class="py-3">Conta</th>
                            <th class="py-3">Situação da Loja</th>
                            <th class="py-3 text-center">Catálogo</th>
                            <th class="pe-4 py-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($vendedores as $vendedor)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $vendedor->user?->foto }}" alt="{{ $vendedor->nome_fantasia }}"
                                            class="rounded-3 shadow-sm object-fit-cover"
                                            style="width: 44px; height: 44px;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $vendedor->nome_fantasia }}</div>
                                            <div class="text-muted small">{{ $vendedor->razao_social }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <div class="fw-semibold text-dark">
                                        {{ $vendedor->user?->name ?? 'Usuário Removido' }}</div>
                                    <div class="text-muted small">{{ $vendedor->user?->email ?? '-' }}</div>
                                </td>

                                <td class="py-3">
                                    <div class="fw-medium text-secondary">{{ $vendedor->cnpj }}</div>
                                    <div class="text-muted small">
                                        <i
                                            class="bi bi-telephone me-1"></i>{{ $vendedor->telefone_comercial ?? 'Não informado' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    @if ($vendedor->status_aprovacao === 'aprovado')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aprovado
                                        </span>
                                    @elseif ($vendedor->status_aprovacao === 'pendente')
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i> Pendente
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-x-circle-fill me-1"></i> Rejeitado
                                        </span>
                                    @endif
                                    <span class="badge {{ $vendedor->situacao_badge_class }} rounded-pill px-3 py-1 fw-semibold">
                                        <i class="bi {{ $vendedor->situacao_icone }} me-1"></i> {{ $vendedor->situacao_rotulo }}
                                    </span>
                                </td>

                                <td class="py-3">
                                    @if ($vendedor->user?->status === 'ativo')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Ativo</span>
                                    @elseif($vendedor->user?->status === 'inativo')
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Inativo</span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Banido</span>
                                    @endif
                                </td>

                                <td class="py-3 text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 me-1"
                                        title="Livros cadastrados">
                                        <i class="bi bi-book me-1 text-primary"></i>
                                        {{ $vendedor->livros_count ?? 0 }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1"
                                        title="Pedidos vinculados">
                                        <i class="bi bi-bag-check me-1 text-success"></i>
                                        {{ $vendedor->pedidos_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    <div class="btn-group gap-1">
                                        <!-- Ações rápidas de aprovação/rejeição -->
                                        @if ($vendedor->status_aprovacao !== 'aprovado')
                                            <form method="POST"
                                                action="{{ route('admin.vendedores.status', $vendedor) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="aprovado">
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-success rounded-3"
                                                    title="Aprovar Vendedor">
                                                    <i class="bi bi-check-lg"></i>
                                                    class="btn btn-sm btn-outline-success rounded-3 d-inline-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    title="Aprovar Vendedor" aria-label="Aprovar">
                                                    <i class="bi bi-check-lg fs-6"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($vendedor->status_aprovacao !== 'rejeitado')
                                            <form method="POST"
                                                action="{{ route('admin.vendedores.status', $vendedor) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejeitado">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"
                                                    title="Rejeitar Vendedor">
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-3 d-inline-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    title="Rejeitar Vendedor" aria-label="Rejeitar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.vendedores.show', $vendedor) }}"
                                            class="btn btn-sm btn-outline-secondary rounded-3"
                                            title="Visualizar Detalhes">
                                            class="btn btn-sm btn-outline-secondary rounded-3 d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;"
                                            title="Visualizar Detalhes" aria-label="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- BOTÃO DE EDIÇÃO PADRONIZADO (ICON-ONLY EM DESTAQUE) -->
                                        <a href="{{ route('admin.vendedores.edit', $vendedor) }}"
                                            class="btn btn-sm btn-primary rounded-3 text-white fw-semibold shadow-sm px-2.5 py-1 d-inline-flex align-items-center gap-1"
                                            title="Editar Vendedor">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                            class="btn btn-sm btn-primary rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;"
                                            title="Editar Vendedor" aria-label="Editar">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-3 d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $vendedor->id }}"
                                            title="Excluir Vendedor">
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
                                        @if (request()->hasAny(['busca', 'status', 'status_conta']))
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
