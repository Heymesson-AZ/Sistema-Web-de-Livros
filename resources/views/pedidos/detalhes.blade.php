<x-layouts.principal title="Detalhes do Pedido {{ $pedido->numero_pedido }} - Universo de Papel">
    <div class="container py-4">

        <!-- Topo de Navegação -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom">
            <div>
                <a href="{{ route('painel', ['tab' => 'pedidos']) }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para Meus Pedidos
                </a>
                <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>Pedido</span>
                    <span class="font-monospace text-primary">{{ $pedido->numero_pedido }}</span>
                </h1>
                <span class="text-muted small">Realizado em {{ $pedido->data_pedido?->translatedFormat('d \d\e F \d\e Y, H:i') }}</span>
            </div>
            <div>
                <span class="badge {{ $pedido->status_badge_class }} fs-6 rounded-pill px-3 py-2">
                    {{ $pedido->status_rotulo }}
                </span>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Coluna Principal: Itens e Logística -->
            <div class="col-12 col-lg-8">

                <!-- Card de Itens do Pedido -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="h6 fw-bold text-dark mb-0">Itens Comprados ({{ $pedido->itens->sum('quantidade_itens') }})</h2>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($pedido->itens as $item)
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->livro?->url_capa }}" alt="{{ $item->livro?->titulo }}"
                                        class="rounded-3 shadow-sm object-fit-cover" style="width: 55px; height: 75px;">
                                    <div>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">
                                            {{ $item->livro?->titulo ?: 'Título não disponível' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $item->livro?->autor?->nome ?: 'Autor não informado' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $item->quantidade_itens }}x {{ $item->valor_unitario_formatado }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-dark fs-6">{{ $item->subtotal_formatado }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Card de Avaliação (Para o Cliente) -->
                @if (Auth::user()->isCliente() || (Auth::user()->cliente && $pedido->cliente_id === Auth::user()->cliente->id))
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <span>Avaliação do Pedido</span>
                            </h2>
                            @if ($pedido->avaliacao)
                                <span class="badge bg-success-subtle text-success rounded-pill">Avaliado</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if ($pedido->avaliacao)
                                <div class="p-3 bg-light rounded-3">
                                    <div class="d-flex align-items-center gap-1 text-warning mb-2">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <i class="bi bi-star-fill {{ $s <= $pedido->avaliacao->avaliacao ? 'text-warning' : 'text-secondary opacity-25' }}"></i>
                                        @endfor
                                        <span class="text-dark fw-bold ms-2">{{ $pedido->avaliacao->avaliacao }} de 5 estrelas</span>
                                    </div>
                                    @if ($pedido->avaliacao->comentario)
                                        <p class="text-muted small mb-1">{{ $pedido->avaliacao->comentario }}</p>
                                    @endif
                                    <div class="text-muted" style="font-size: 11px;">
                                        Recomendou o vendedor: <strong>{{ $pedido->avaliacao->recomenda ? 'Sim' : 'Não' }}</strong>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('avaliacoes.salvar', $pedido) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Sua Nota (1 a 5 estrelas):</label>
                                        <select name="avaliacao" class="form-select form-select-sm" required>
                                            <option value="5">⭐⭐⭐⭐⭐ 5 Estrelas (Excelente)</option>
                                            <option value="4">⭐⭐⭐⭐ 4 Estrelas (Muito Bom)</option>
                                            <option value="3">⭐⭐⭐ 3 Estrelas (Regular)</option>
                                            <option value="2">⭐⭐ 2 Estrelas (Ruim)</option>
                                            <option value="1">⭐ 1 Estrela (Péssimo)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Comentário sobre os livros e entrega:</label>
                                        <textarea name="comentario" class="form-control form-control-sm" rows="3"
                                            placeholder="Conte o que achou da qualidade do livro, embalagem e agilidade..."></textarea>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="recomenda" value="1" id="chkRecomenda" checked>
                                        <label class="form-check-label small" for="chkRecomenda">
                                            Recomendo este vendedor para outros compradores
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">
                                        Enviar Avaliação
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Gestão de Status (Admin ou Vendedor do Pedido) -->
                @if (Auth::user()->isAdmin() || (Auth::user()->vendedor && $pedido->vendedor_id === Auth::user()->vendedor->id))
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-start border-4 border-primary">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i data-lucide="sliders" class="text-primary" style="width: 18px; height: 18px;"></i>
                                <span>Gerenciar Status do Pedido (Vendedor/Admin)</span>
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pedidos.atualizar-status', $pedido) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold">Status do Pedido:</label>
                                        <select name="status" class="form-select form-select-sm" required>
                                            <option value="pendente" {{ $pedido->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="processando" {{ $pedido->status === 'processando' ? 'selected' : '' }}>Em Processamento</option>
                                            <option value="enviado" {{ $pedido->status === 'enviado' ? 'selected' : '' }}>Enviado (Em trânsito)</option>
                                            <option value="entregue" {{ $pedido->status === 'entregue' ? 'selected' : '' }}>Entregue ao Cliente</option>
                                            <option value="cancelado" {{ $pedido->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold">Código de Rastreamento:</label>
                                        <input type="text" name="codigo_rastreamento" class="form-control form-control-sm text-uppercase font-monospace"
                                            value="{{ $pedido->entrega?->codigo_rastreamento }}" placeholder="Ex: BR123456789BR">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4">
                                            Atualizar Status Logístico
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Coluna Lateral: Endereço, Pagamento e Totais -->
            <div class="col-12 col-lg-4">

                <!-- Card de Entrega -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                    <h2 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Entrega</span>
                    </h2>
                    @if ($pedido->entrega)
                        <div class="small text-dark fw-semibold mb-1">
                            {{ $pedido->entrega->rua }}, {{ $pedido->entrega->numero }}
                        </div>
                        @if ($pedido->entrega->complemento)
                            <div class="text-muted small mb-1">{{ $pedido->entrega->complemento }}</div>
                        @endif
                        <div class="text-muted small mb-1">
                            {{ $pedido->entrega->bairro }} - {{ $pedido->entrega->cidade }}/{{ $pedido->entrega->estado }}
                        </div>
                        <div class="text-muted small font-monospace mb-3">CEP: {{ $pedido->entrega->cep }}</div>

                        @if ($pedido->entrega->codigo_rastreamento)
                            <div class="p-2 bg-light rounded-3 small">
                                <span class="text-muted d-block" style="font-size: 11px;">Rastreamento:</span>
                                <span class="fw-bold font-monospace text-primary">{{ $pedido->entrega->codigo_rastreamento }}</span>
                            </div>
                        @endif
                    @else
                        <p class="text-muted small mb-0">Informações de entrega não encontradas.</p>
                    @endif
                </div>

                <!-- Card de Pagamento -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                    <h2 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i data-lucide="credit-card" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Pagamento</span>
                    </h2>
                    @php
                        $pagamento = $pedido->pagamentos->first();
                    @endphp
                    @if ($pagamento)
                        <div class="d-flex justify-content-between align-items-center small mb-2">
                            <span class="text-muted">Método:</span>
                            <span class="fw-semibold text-dark">{{ $pagamento->metodo_formatado }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small mb-2">
                            <span class="text-muted">Status do Pagamento:</span>
                            <span class="badge {{ $pagamento->status_pagamento === 'aprovado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} rounded-pill">
                                {{ ucfirst($pagamento->status_pagamento) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Transação:</span>
                            <span class="font-monospace text-secondary" style="font-size: 11px;">{{ $pagamento->id_transacao }}</span>
                        </div>
                    @endif
                </div>

                <!-- Card de Resumo Financeiro -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h2 class="h6 fw-bold text-dark mb-3">Resumo Financeiro</h2>

                    @php
                        $subtotalItens = $pedido->itens->sum(fn ($i) => (float) $i->valor_unitario * (int) $i->quantidade_itens);
                        $frete = (float) ($pedido->entrega?->valor_frete ?? 0);
                    @endphp

                    <div class="d-flex justify-content-between text-secondary mb-2 small">
                        <span>Subtotal dos Itens</span>
                        <span>R$ {{ number_format($subtotalItens, 2, ',', '.') }}</span>
                    </div>

                    @if ($pedido->cupom)
                        <div class="d-flex justify-content-between text-success mb-2 small">
                            <span>Cupom ({{ $pedido->cupom->codigo }})</span>
                            <span>Aplicado</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between text-secondary mb-3 small">
                        <span>Frete</span>
                        <span>{{ $frete > 0 ? 'R$ ' . number_format($frete, 2, ',', '.') : 'Grátis' }}</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Total</span>
                        <span class="fs-4 fw-bold text-primary">{{ $pedido->total_formatado }}</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>

