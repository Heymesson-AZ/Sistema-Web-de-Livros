<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Painel do Vendedor</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Clientes da Loja</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div
                class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Clientes da sua Loja</h3>
                    <p class="text-muted small mb-0">
                        Consulte os leitores que adquiriram livros da sua loja
                        <strong>{{ $vendedor->nome_fantasia }}</strong> e o histórico de compras.
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

        <!-- FILTROS DE BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ route('vendedor.clientes.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="busca" class="form-control form-control-sm"
                                placeholder="Buscar cliente por nome ou e-mail..." value="{{ request('busca') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-search-submit">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        @if (request()->filled('busca'))
                            <a href="{{ route('vendedor.clientes.index') }}" class="btn-search-clear">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- LISTAGEM DE CLIENTES -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-primary me-2"></i> Leitores Compradores
                </h5>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 font-monospace">
                    {{ $clientes->total() }} cliente(s)
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Cliente</th>
                                <th class="py-3">Contato</th>
                                <th class="py-3">Localidade</th>
                                <th class="py-3 text-center">Pedidos com sua Loja</th>
                                <th class="pe-4 py-3 text-end">Total Comprado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cli)
                                @php
                                    $ultimoPedido = $cli->pedidos->first();
                                    $localidade = $ultimoPedido?->entrega
                                        ? "{$ultimoPedido->entrega->cidade} - {$ultimoPedido->entrega->estado}"
                                        : 'Não informada';
                                    $totalGasto = $cli->pedidos->sum('total');
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $cli->user?->foto }}" alt="{{ $cli->user?->name }}"
                                                class="rounded-circle shadow-sm"
                                                style="width: 40px; height: 40px; aspect-ratio: 1/1; object-fit: cover;"
                                                onerror="this.onerror=null;this.src='{{ $cli->user?->foto_padrao }}';">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    {{ $cli->user?->name ?? 'Cliente Anônimo' }}</div>
                                                <div class="small text-muted">{{ $cli->user?->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark">
                                            <i class="bi bi-telephone me-1 text-muted"></i>
                                            {{ $cli->celular_contato ?? 'Não informado' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $localidade }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border font-monospace">
                                            {{ $cli->pedidos_count }} pedido(s)
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="fw-bold font-monospace text-success">
                                            R$ {{ number_format((float) $totalGasto, 2, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Nenhum cliente registrado</h6>
                                        <p class="text-muted small mb-0">Quando leitores comprarem livros da sua loja,
                                            eles aparecerão nesta lista.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($clientes->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
