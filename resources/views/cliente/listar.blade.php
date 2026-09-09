<x-layouts.principal>
    <div class="container py-4">

        <!-- CABEÇALHO DA PÁGINA -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-people text-primary me-2"></i>
                    Gestão de Clientes
                </h1>
                <p class="text-muted small mb-0">
                    Gerencie os clientes e compradores cadastrados na livraria Universo de Papel.
                </p>
            </div>
            <a href="{{ route('admin.clientes.create') }}"
                class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i>
                <span>Novo Cliente</span>
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
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total de Clientes</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalClientes }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Clientes Com Pedidos</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $totalComPedidos }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #f0fdf4; color: #16a34a;">
                            <i class="bi bi-bag-check-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Cadastros Este Mês</span>
                            <h3 class="fw-bold mb-0 text-primary mt-1">{{ $totalNovosMes }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #fdf4ff; color: #9333ea;">
                            <i class="bi bi-calendar-check-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS E BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <form method="GET" action="{{ route('admin.clientes.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="busca" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por nome, e-mail, CPF ou celular..." value="{{ request('busca') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                        <option value="banido" {{ request('status') === 'banido' ? 'selected' : '' }}>Banido</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                    @if (request()->hasAny(['busca', 'status']))
                        <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary"
                            title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABELA DE CLIENTES -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Cliente</th>
                            <th class="py-3">CPF</th>
                            <th class="py-3">Contato</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Data Nasc.</th>
                            <th class="py-3 text-center">Atividades</th>
                            <th class="pe-4 py-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($clientes as $cliente)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $cliente->user?->foto }}" alt="{{ $cliente->user?->name }}"
                                            class="rounded-circle shadow-sm object-fit-cover"
                                            style="width: 44px; height: 44px;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $cliente->user?->name ?? 'Sem Nome' }}
                                            </div>
                                            <div class="text-muted small">{{ $cliente->user?->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="font-monospace text-secondary fw-medium">{{ $cliente->cpf }}</span>
                                </td>

                                <td class="py-3">
                                    <div class="text-secondary small">
                                        <i
                                            class="bi bi-phone me-1"></i>{{ $cliente->celular_contato ?? 'Não informado' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    @if ($cliente->user?->status === 'ativo')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Ativo</span>
                                    @elseif($cliente->user?->status === 'inativo')
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Inativo</span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill"
                                            style="font-size: 11px;">Banido</span>
                                    @endif
                                </td>

                                <td class="py-3 text-muted small">
                                    {{ $cliente->data_nascimento ? \Carbon\Carbon::parse($cliente->data_nascimento)->format('d/m/Y') : '-' }}
                                </td>

                                <td class="py-3 text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 me-1"
                                        title="Total de Pedidos">
                                        <i class="bi bi-bag-check me-1 text-success"></i>
                                        {{ $cliente->pedidos_count ?? 0 }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1" title="Avaliações Feitas">
                                        <i class="bi bi-star me-1 text-warning"></i>
                                        {{ $cliente->avaliacoes_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    <div class="btn-group gap-1">
                                        <a href="{{ route('admin.clientes.show', $cliente) }}"
                                            class="btn btn-sm btn-outline-secondary rounded-3"
                                            title="Visualizar Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.clientes.edit', $cliente) }}"
                                            class="btn btn-sm btn-primary rounded-3 text-white fw-semibold shadow-sm px-2.5 py-1 d-inline-flex align-items-center gap-1"
                                            title="Editar Cliente">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteClienteModal{{ $cliente->id }}"
                                            title="Excluir Cliente">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
                                    <div class="modal fade" id="deleteClienteModal{{ $cliente->id }}"
                                        tabindex="-1" aria-hidden="true">
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
                                                        Tem certeza que deseja excluir o cliente
                                                        <strong>{{ $cliente->user?->name }}</strong> (CPF:
                                                        {{ $cliente->cpf }})?
                                                    </p>
                                                    <p class="text-danger small mb-0 fw-semibold">
                                                        Atenção: A conta de acesso do cliente também será excluída
                                                        permanentemente.
                                                    </p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-3 px-3"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST"
                                                        action="{{ route('admin.clientes.destroy', $cliente) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-3 px-3">
                                                            Sim, Excluir Cliente
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
                                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        <h6 class="fw-bold mb-1">Nenhum cliente encontrado</h6>
                                        <p class="small mb-3">Tente ajustar seus termos de busca ou cadastrar um novo
                                            cliente.</p>
                                        @if (request()->hasAny(['busca', 'status']))
                                            <a href="{{ route('admin.clientes.index') }}"
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
            @if ($clientes->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
