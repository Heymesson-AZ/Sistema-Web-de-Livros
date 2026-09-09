<x-layouts.principal title="Finalizar Compra - Universo de Papel">
    <div class="container py-4">

        <!-- Título -->
        <div class="mb-4 pb-2 border-bottom">
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="shield-check" class="text-primary"></i>
                <span>Finalização de Pedido</span>
            </h1>
            <p class="text-muted small mb-0">Confirme o endereço de entrega e a forma de pagamento para concluir sua
                compra.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Corrija os pontos abaixo
                    para continuar:</div>
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <form action="{{ route('checkout.finalizar') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- Coluna Principal: Endereço e Pagamento -->
                <div class="col-12 col-lg-8">

                    <!-- Etapa 1: Endereço de Entrega -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div
                            class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-pill">1</span>
                                <span>Endereço de Entrega</span>
                            </span>
                            <a href="{{ route('painel', ['tab' => 'enderecos']) }}"
                                class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="bi bi-plus-circle me-1"></i> Novo Endereço
                            </a>
                        </div>
                        <div class="card-body p-4">
                            @if ($enderecos->isEmpty())
                                <div class="text-center py-4 bg-light rounded-3">
                                    <i class="bi bi-geo-alt-fill fs-2 text-muted mb-2 d-block"></i>
                                    <p class="text-muted small mb-3">Você ainda não possui nenhum endereço cadastrado.
                                    </p>
                                    <a href="{{ route('painel', ['tab' => 'enderecos']) }}"
                                        class="btn btn-primary btn-sm rounded-pill px-4">
                                        Cadastrar Meu Endereço
                                    </a>
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach ($enderecos as $index => $end)
                                        <div class="col-12 col-md-6">
                                            <div class="form-check p-0">
                                                <input class="btn-check" type="radio" name="endereco_id"
                                                    id="endereco_{{ $end->id }}" value="{{ $end->id }}"
                                                    {{ $end->principal || $loop->first ? 'checked' : '' }} required>
                                                <label
                                                    class="btn btn-outline-light text-dark text-start w-100 p-3 rounded-3 border d-flex flex-column justify-content-between h-100 shadow-sm"
                                                    for="endereco_{{ $end->id }}">
                                                    <div>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-1">
                                                            <span
                                                                class="badge bg-secondary-subtle text-secondary rounded-pill text-uppercase"
                                                                style="font-size: 10px;">
                                                                {{ $end->tipo }}
                                                            </span>
                                                            @if ($end->principal)
                                                                <span class="badge bg-primary text-white rounded-pill"
                                                                    style="font-size: 10px;">
                                                                    Principal
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="fw-bold text-dark small mb-1">{{ $end->rua }},
                                                            {{ $end->numero }}</div>
                                                        @if ($end->complemento)
                                                            <div class="text-muted small">{{ $end->complemento }}</div>
                                                        @endif
                                                        <div class="text-muted small">{{ $end->bairro }} -
                                                            {{ $end->cidade }}/{{ $end->estado }}</div>
                                                        <div class="text-muted small font-monospace">CEP:
                                                            {{ $end->cep }}</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Etapa 2: Forma de Pagamento -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-header bg-white py-3 border-bottom">
                            <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-pill">2</span>
                                <span>Forma de Pagamento</span>
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <!-- Abas/Opções de Pagamento -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="metodo_pagamento" id="pay_pix"
                                        value="pix" checked>
                                    <label
                                        class="btn btn-outline-light text-dark border p-3 w-100 rounded-3 text-center h-100 shadow-sm"
                                        for="pay_pix">
                                        <i class="bi bi-qr-code text-success fs-3 d-block mb-1"></i>
                                        <span class="fw-bold d-block small">PIX Instantâneo</span>
                                        <span class="text-muted" style="font-size: 11px;">Aprovação imediata</span>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="metodo_pagamento" id="pay_cartao"
                                        value="cartao_credito">
                                    <label
                                        class="btn btn-outline-light text-dark border p-3 w-100 rounded-3 text-center h-100 shadow-sm"
                                        for="pay_cartao">
                                        <i class="bi bi-credit-card-2-front text-primary fs-3 d-block mb-1"></i>
                                        <span class="fw-bold d-block small">Cartão de Crédito</span>
                                        <span class="text-muted" style="font-size: 11px;">Até 6x sem juros</span>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="metodo_pagamento" id="pay_boleto"
                                        value="boleto">
                                    <label
                                        class="btn btn-outline-light text-dark border p-3 w-100 rounded-3 text-center h-100 shadow-sm"
                                        for="pay_boleto">
                                        <i class="bi bi-upc-scan text-secondary fs-3 d-block mb-1"></i>
                                        <span class="fw-bold d-block small">Boleto Bancário</span>
                                        <span class="text-muted" style="font-size: 11px;">Vencimento em 3 dias</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Área do Cartão de Crédito (Preenchimento Condicional) -->
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-1">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <span>Dados do Cartão (Simulado & Criptografado)</span>
                                </h3>

                                @if ($cartoesSalvos->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Usar Cartão
                                            Salvo:</label>
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($cartoesSalvos as $cs)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="cartao_salvo_id" id="cartao_salvo_{{ $cs->id }}"
                                                        value="{{ $cs->id }}">
                                                    <label class="form-check-label small"
                                                        for="cartao_salvo_{{ $cs->id }}">
                                                        <strong>{{ $cs->descricao_formatada }}</strong>
                                                        @if ($cs->cartao_padrao)
                                                            <span
                                                                class="badge bg-primary-subtle text-primary rounded-pill ms-1">Padrão</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text small">Ou digite outro cartão abaixo:</div>
                                    </div>
                                    <hr class="my-3">
                                @endif

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold">Número do Cartão</label>
                                        <input type="text" name="numero_cartao" class="form-control"
                                            placeholder="0000 0000 0000 0000" data-mask="credit-card">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold">Nome Impresso no Cartão</label>
                                        <input type="text" name="nome_titular" class="form-control"
                                            placeholder="Ex: JOAO DA SILVA" style="text-transform: uppercase;">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small fw-semibold">Validade (MM/AA)</label>
                                        <input type="text" name="validade_cartao" class="form-control"
                                            placeholder="12/28" maxlength="5">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small fw-semibold">CVV</label>
                                        <input type="password" name="cvv" class="form-control" placeholder="123"
                                            maxlength="4">
                                    </div>
                                    <div class="col-12 col-md-4 d-flex align-items-end">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="salvar_cartao"
                                                value="1" id="chkSalvarCartao" checked>
                                            <label class="form-check-label small" for="chkSalvarCartao">
                                                Salvar cartão
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Coluna Lateral: Resumo do Pedido -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-sticky" style="top: 90px;">
                        <h2 class="h5 fw-bold text-dark mb-3">Resumo da Compra</h2>

                        <!-- Lista de Itens Resumida -->
                        <div class="mb-3 max-h-60 overflow-y-auto" style="max-height: 200px;">
                            @foreach ($itens as $item)
                                <div
                                    class="d-flex align-items-center justify-content-between py-2 border-bottom small">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <img src="{{ $item->url_capa }}" alt="{{ $item->titulo }}"
                                            class="rounded object-fit-cover" style="width: 30px; height: 42px;">
                                        <div class="text-truncate" style="max-width: 170px;">
                                            <span class="text-dark fw-semibold">{{ $item->titulo }}</span>
                                            <div class="text-muted" style="font-size: 11px;">Qtd:
                                                {{ $item->quantidade_carrinho }}</div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-dark">
                                        R$
                                        {{ number_format((float) $item->preco * (int) $item->quantidade_carrinho, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Valores -->
                        <div class="d-flex justify-content-between text-secondary mb-2 small">
                            <span>Subtotal</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>

                        @if ($desconto > 0)
                            <div class="d-flex justify-content-between text-success mb-2 small">
                                <span>Cupom Promocional</span>
                                <span>- R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between text-secondary mb-3 small">
                            <span>Frete</span>
                            <span>
                                @if ($valorFrete == 0.0)
                                    <strong class="text-success text-uppercase">Grátis</strong>
                                @else
                                    R$ {{ number_format($valorFrete, 2, ',', '.') }}
                                @endif
                            </span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-6 fw-bold text-dark">Total a Pagar</span>
                            <span class="fs-4 fw-bold text-primary">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        <button type="submit"
                            class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                            {{ $enderecos->isEmpty() ? 'disabled' : '' }}>
                            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                            <span>Confirmar e Finalizar Pedido</span>
                        </button>

                        @if ($enderecos->isEmpty())
                            <div class="text-danger small text-center mt-2">
                                Cadastre um endereço para prosseguir com o pedido.
                            </div>
                        @endif

                        <div class="mt-3 text-center text-muted" style="font-size: 11px;">
                            <i class="bi bi-lock-fill text-success me-1"></i>
                            Garantia de Compra Segura Universo de Papel
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</x-layouts.principal>
