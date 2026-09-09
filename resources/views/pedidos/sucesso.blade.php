<x-layouts.principal title="Pedido Realizado com Sucesso - Universo de Papel">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center bg-white">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 75px; height: 75px;">
                        <i data-lucide="check-check" style="width: 40px; height: 40px;"></i>
                    </div>

                    <h1 class="h3 fw-bold text-dark mb-2">Pedido Realizado com Sucesso!</h1>
                    <p class="text-muted small mb-4">
                        Obrigado pela sua compra! O seu pedido já foi registrado em nosso sistema e está sendo preparado.
                    </p>

                    <!-- Detalhes do Pedido -->
                    <div class="p-3 bg-light rounded-3 text-start mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Número do Pedido:</span>
                            <span class="fw-bold font-monospace text-primary fs-6">{{ $pedido->numero_pedido }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Status Atual:</span>
                            <span class="badge {{ $pedido->status_badge_class }} rounded-pill px-3 py-1">
                                {{ $pedido->status_rotulo }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Forma de Pagamento:</span>
                            <span class="fw-semibold text-dark small">{{ $pedido->pagamentos->first()?->metodo_formatado ?? 'Não informado' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Total Pago:</span>
                            <span class="fw-bold text-dark fs-5">{{ $pedido->total_formatado }}</span>
                        </div>
                        @if ($pedido->entrega)
                            <div class="border-top pt-2 mt-2">
                                <span class="text-muted small d-block mb-1">Endereço de Envio:</span>
                                <span class="small text-dark fw-semibold">
                                    {{ $pedido->entrega->rua }}, {{ $pedido->entrega->numero }} - {{ $pedido->entrega->bairro }}, {{ $pedido->entrega->cidade }}/{{ $pedido->entrega->estado }} (CEP: {{ $pedido->entrega->cep }})
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Bloco Especial de Pagamento (PIX / Boleto) -->
                    @php
                        $pagamento = $pedido->pagamentos->first();
                    @endphp
                    @if ($pagamento && $pagamento->metodo_pagamento === 'pix')
                        <div class="p-4 border border-success border-opacity-50 rounded-4 bg-success bg-opacity-10 mb-4 text-center">
                            <h2 class="h6 fw-bold text-success mb-2 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-qr-code"></i>
                                <span>Pagamento via PIX Instantâneo</span>
                            </h2>
                            <p class="text-muted small mb-3">Escaneie o QR Code ou copie a chave para efetuar o pagamento em seu banco:</p>

                            <!-- QR Code Ilustrativo / Simulado -->
                            <div class="bg-white p-3 d-inline-block rounded-3 shadow-sm mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=00020126360014BR.GOV.BCB.PIX0114+5511999999999520400005303986540{{ $pedido->total }}5802BR5917UNIVERSO+DE+PAPEL6009SAO+PAULO"
                                    alt="QR Code PIX" style="width: 150px; height: 150px;" class="img-fluid">
                            </div>

                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm font-monospace text-muted"
                                    value="00020126360014BR.GOV.BCB.PIX0114UNIVERSOPAPEL{{ $pedido->numero_pedido }}" readonly id="inputPix">
                                <button class="btn btn-success btn-sm px-3" type="button"
                                    data-clipboard-target="#inputPix">
                                    <i class="bi bi-clipboard me-1"></i> Copiar
                                </button>
                            </div>
                            <span class="text-muted" style="font-size: 11px;">O pedido será aprovado instantaneamente após a confirmação bancária.</span>
                        </div>
                    @elseif ($pagamento && $pagamento->metodo_pagamento === 'boleto')
                        <div class="p-4 border border-secondary border-opacity-50 rounded-4 bg-light mb-4 text-center">
                            <h2 class="h6 fw-bold text-dark mb-2 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-upc-scan"></i>
                                <span>Boleto Bancário Gerado</span>
                            </h2>
                            <p class="text-muted small mb-3">Utilize a linha digitável abaixo para realizar o pagamento:</p>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm font-monospace text-muted"
                                    value="34191.79001 01043.510047 91020.150008 5 99990000{{ str_replace('.', '', number_format($pedido->total, 2, '', '')) }}"
                                    readonly id="inputBoleto">
                                <button class="btn btn-secondary btn-sm px-3" type="button"
                                    data-clipboard-target="#inputBoleto">
                                    <i class="bi bi-clipboard me-1"></i> Copiar
                                </button>
                            </div>
                            <span class="text-muted" style="font-size: 11px;">Vencimento em 3 dias úteis. Compensação em até 48 horas.</span>
                        </div>
                    @endif

                    <!-- Ações Finais -->
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                            <i data-lucide="eye" class="me-1"></i> Detalhes do Pedido
                        </a>
                        <a href="{{ route('painel', ['tab' => 'pedidos']) }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i data-lucide="package" class="me-1"></i> Ver Meus Pedidos
                        </a>
                        <a href="{{ url('/#catalogo') }}" class="btn btn-link text-decoration-none">
                            Continuar Comprando
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.principal>
