<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Painel do Vendedor</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.pedidos.index') }}"
                        class="text-decoration-none text-muted">Pedidos da Loja</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                    {{ $pedido->numero_pedido }}</li>
            </ol>
        </nav>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="small fw-semibold">{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- CABEÇALHO DO PEDIDO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div
                class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <h3 class="fw-bold text-dark font-monospace mb-0">{{ $pedido->numero_pedido }}</h3>
                        <span class="badge {{ $pedido->status_badge_class }} rounded-pill px-3 py-1 font-monospace"
                            style="font-size: 12px;">
                            {{ $pedido->status_rotulo }}
                        </span>
                    </div>
                    <p class="text-muted small mb-0">
                        Realizado em
                        {{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m/Y \à\s H:i') : $pedido->created_at->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('vendedor.pedidos.index') }}"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Voltar aos Pedidos
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- COLUNA PRINCIPAL: ITENS DO PEDIDO -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-book text-primary me-2"></i> Itens Comprados
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4 py-3">Livro</th>
                                        <th class="py-3 text-center">Qtd</th>
                                        <th class="py-3 text-end">Preço Unitário</th>
                                        <th class="pe-4 py-3 text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pedido->itens as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $item->livro?->url_capa }}"
                                                        alt="{{ $item->livro?->titulo }}" class="rounded-2 shadow-sm"
                                                        style="width: 48px; height: 68px; object-fit: cover;"
                                                        onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=120&q=80'">
                                                    <div>
                                                        <h6 class="fw-semibold text-dark mb-1">
                                                            {{ $item->livro?->titulo ?? 'Livro não encontrado' }}</h6>
                                                        <div class="small text-muted">ISBN:
                                                            {{ $item->livro?->isbn ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center font-monospace">
                                                {{ $item->quantidade_itens }}
                                            </td>
                                            <td class="text-end font-monospace text-muted">
                                                {{ $item->valor_unitario_formatado }}
                                            </td>
                                            <td class="pe-4 text-end fw-bold font-monospace text-dark">
                                                {{ $item->subtotal_formatado }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4">
                        <div class="row justify-content-end">
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Subtotal dos Itens:</span>
                                    <span class="font-monospace">R$
                                        {{ number_format((float) $pedido->itens->sum(fn($i) => (float) $i->valor_unitario * (int) $i->quantidade_itens), 2, ',', '.') }}</span>
                                </div>
                                @if ($pedido->cupom)
                                    <div class="d-flex justify-content-between mb-2 small text-success">
                                        <span>Cupom ({{ $pedido->cupom->codigo }}):</span>
                                        <span class="font-monospace">- {{ $pedido->cupom->descricao_desconto }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Frete de Envio:</span>
                                    <span class="font-monospace">R$
                                        {{ number_format((float) ($pedido->entrega?->valor_frete ?? 0), 2, ',', '.') }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                                    <span>Total do Pedido:</span>
                                    <span class="font-monospace text-success">{{ $pedido->total_formatado }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENDEREÇO DE ENTREGA -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-geo-alt text-primary me-2"></i> Endereço para Entrega
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($pedido->entrega)
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <div class="text-muted small">Logradouro e Número:</div>
                                    <div class="fw-semibold text-dark">{{ $pedido->entrega->rua }},
                                        {{ $pedido->entrega->numero }}
                                        {{ $pedido->entrega->complemento ? "({$pedido->entrega->complemento})" : '' }}
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Bairro:</div>
                                    <div class="fw-semibold text-dark">{{ $pedido->entrega->bairro }}</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="text-muted small">Cidade / Estado:</div>
                                    <div class="fw-semibold text-dark">{{ $pedido->entrega->cidade }} -
                                        {{ $pedido->entrega->estado }}</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="text-muted small">CEP:</div>
                                    <div class="fw-semibold text-dark font-monospace">{{ $pedido->entrega->cep }}</div>
                                </div>
                                @if ($pedido->entrega->codigo_rastreio)
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="text-muted small mb-1">Código de Rastreamento:</div>
                                            <div class="fw-bold font-monospace text-primary fs-6">
                                                {{ $pedido->entrega->codigo_rastreio }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-muted small mb-0">Informações de entrega não registradas para este pedido.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COLUNA LATERAL: ATUALIZAR STATUS & DADOS DO CLIENTE -->
            <div class="col-12 col-lg-4">

                <!-- CONTROLE LOGÍSTICO DO VENDEDOR -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-arrow-repeat text-primary me-2"></i> Gestão do Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('vendedor.pedidos.status', $pedido) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="statusSelect" class="form-label small fw-semibold">Status do
                                    Pedido:</label>
                                <select name="status" id="statusSelect" class="form-select form-select-sm">
                                    <option value="processando"
                                        {{ $pedido->status === 'processando' ? 'selected' : '' }}>Em Processamento /
                                        Separando</option>
                                    <option value="enviado" {{ $pedido->status === 'enviado' ? 'selected' : '' }}>
                                        Enviado / Em Trânsito</option>
                                    <option value="entregue" {{ $pedido->status === 'entregue' ? 'selected' : '' }}>
                                        Entregue ao Destinatário</option>
                                    <option value="cancelado" {{ $pedido->status === 'cancelado' ? 'selected' : '' }}>
                                        Cancelado</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="codigoRastreio" class="form-label small fw-semibold">Código de Rastreio
                                    (Opcional):</label>
                                <input type="text" name="codigo_rastreio" id="codigoRastreio"
                                    class="form-control form-control-sm font-monospace text-uppercase"
                                    placeholder="Ex: BR123456789XP"
                                    value="{{ old('codigo_rastreio', $pedido->entrega?->codigo_rastreio) }}">
                            </div>

                            <button type="submit"
                                class="btn btn-primary btn-sm rounded-pill w-100 fw-semibold shadow-sm">
                                Atualizar Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- DADOS DO CLIENTE -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person text-primary me-2"></i> Dados do Cliente
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $pedido->cliente?->user?->foto }}"
                                alt="{{ $pedido->cliente?->user?->name }}" class="rounded-circle shadow-sm"
                                style="width: 48px; height: 48px; aspect-ratio: 1/1; object-fit: cover;"
                                onerror="this.onerror=null;this.src='{{ $pedido->cliente?->user?->foto_padrao }}';">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">
                                    {{ $pedido->cliente?->user?->name ?? 'Cliente Desconhecido' }}</h6>
                                <div class="small text-muted">{{ $pedido->cliente?->user?->email }}</div>
                            </div>
                        </div>

                        <div class="text-muted small mb-1">Telefone de Contato:</div>
                        <div class="fw-semibold text-dark mb-3">
                            {{ $pedido->cliente?->celular_contato ?? 'Não informado' }}
                        </div>

                        <div class="text-muted small mb-1">Total de Pedidos com sua Loja:</div>
                        <div class="fw-semibold text-dark">
                            {{ $pedido->cliente?->pedidos()->where('vendedor_id', $vendedor->id)->count() }} pedido(s)
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>
