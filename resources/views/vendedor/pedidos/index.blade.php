<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Painel do Vendedor</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Pedidos da Loja</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div
                class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Pedidos Recebidos</h3>
                    <p class="text-muted small mb-0">
                        Gerencie as vendas, acompanhe envios e atualize o status de entrega da loja
                        <strong>{{ $vendedor->nome_fantasia }}</strong>.
                    </p>
                </div>
                <div>
                    <a href="{{ route('vendedor.painel') }}"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Painel da Loja
                    </a>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="small fw-semibold">{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- KPIS DE PEDIDOS -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi bi-bag"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ $totalPedidos }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Pendentes</div>
                            <h4 class="fw-bold mb-0 text-warning">{{ $totalPendentes }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Em Envio</div>
                            <h4 class="fw-bold mb-0 text-info">{{ $totalEnviados }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Entregues</div>
                            <h4 class="fw-bold mb-0 text-success">{{ $totalEntregues }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS DE BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ route('vendedor.pedidos.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="busca" class="form-control form-control-sm"
                                placeholder="Buscar por número do pedido ou nome do cliente..."
                                value="{{ request('busca') }}">
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Todos os status</option>
                            <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente
                            </option>
                            <option value="processando" {{ request('status') === 'processando' ? 'selected' : '' }}>Em
                                Processamento</option>
                            <option value="enviado" {{ request('status') === 'enviado' ? 'selected' : '' }}>Enviado
                            </option>
                            <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue
                            </option>
                            <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>
                                Cancelado</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-search-submit">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        @if (request()->hasAny(['busca', 'status']))
                            <a href="{{ route('vendedor.pedidos.index') }}" class="btn-search-clear">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- TABELA DE PEDIDOS -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-list-check text-primary me-2"></i> Listagem de Pedidos
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Pedido</th>
                                <th class="py-3">Data</th>
                                <th class="py-3">Cliente</th>
                                <th class="py-3">Itens</th>
                                <th class="py-3">Total</th>
                                <th class="py-3">Status</th>
                                <th class="pe-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pedidos as $ped)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold font-monospace text-dark">{{ $ped->numero_pedido }}</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">
                                            {{ $ped->data_pedido ? $ped->data_pedido->format('d/m/Y H:i') : $ped->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ $ped->cliente?->user?->name ?? 'Cliente' }}</div>
                                        <div class="small text-muted">{{ $ped->cliente?->user?->email }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $ped->itens->sum('quantidade_itens') }} livro(s)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ $ped->total_formatado }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $ped->status_badge_class }} rounded-pill px-3 py-1 font-monospace"
                                            style="font-size: 11px;">
                                            {{ $ped->status_rotulo }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('vendedor.pedidos.show', $ped) }}"
                                            class="btn-action btn-action-view" title="Ver detalhes do pedido"
                                            aria-label="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-bag-x fs-1 text-muted d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Nenhum pedido encontrado</h6>
                                        <p class="text-muted small mb-0">Sua loja ainda não recebeu pedidos com os
                                            critérios selecionados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pedidos->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
