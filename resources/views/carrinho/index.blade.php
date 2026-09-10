<x-layouts.principal title="Meu Carrinho - Universo de Papel">
    <div class="container py-4">

        <!-- Cabeçalho da Página -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="shopping-cart" class="text-primary"></i>
                    <span>Meu Carrinho de Compras</span>
                </h1>
                <p class="text-muted small mb-0">Revise os livros adicionados antes de prosseguir para o pagamento.</p>
            </div>
            @if ($itens->isNotEmpty())
                <form action="{{ route('carrinho.limpar') }}" method="POST"
                    data-confirm="Tem certeza de que deseja esvaziar todos os itens do carrinho?">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        <span>Esvaziar Carrinho</span>
                    </button>
                </form>
            @endif
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($itens->isEmpty())
            <!-- Estado Vazio -->
            <div class="text-center py-5 bg-light rounded-4 border border-dashed my-4">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 70px; height: 70px;">
                    <i data-lucide="shopping-bag" style="width: 36px; height: 36px;"></i>
                </div>
                <h2 class="h5 fw-bold text-dark mb-2">Seu carrinho está vazio</h2>
                <p class="text-muted small mb-4" style="max-width: 420px; margin-inline: auto;">
                    Explore o nosso catálogo com milhares de títulos incríveis e encontre sua próxima grande leitura.
                </p>
                <a href="{{ url('/#catalogo') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                    <i data-lucide="book-open" class="me-1"></i> Explorar Catálogo
                </a>
            </div>
        @else
            <!-- Grid com Itens e Resumo -->
            <div class="row g-4">
                <!-- Coluna de Itens -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div
                            class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Itens Selecionados
                                ({{ $itens->sum('quantidade_carrinho') }})</span>
                            <span class="text-muted small">Preço unitário</span>
                        </div>
                        <div class="card-body p-0">
                            @foreach ($itens as $item)
                                <div
                                    class="p-3 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                    <!-- Capa e Dados do Livro -->
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="{{ route('livros.detalhes', $item) }}" class="flex-shrink-0">
                                            <img src="{{ $item->url_capa }}" alt="{{ $item->titulo }}"
                                                class="rounded-3 shadow-sm object-fit-cover"
                                                style="width: 65px; height: 90px;">
                                        </a>
                                        <div>
                                            <a href="{{ route('livros.detalhes', $item) }}"
                                                class="text-dark fw-bold text-decoration-none hover-primary text-truncate d-block"
                                                style="max-width: 320px;">
                                                {{ $item->titulo }}
                                            </a>
                                            <div class="text-muted small">
                                                <span>{{ $item->autor?->nome ?: 'Autor Desconhecido' }}</span>
                                                @if ($item->vendedor)
                                                    <span class="text-secondary">•</span>
                                                    <span class="text-success"><i
                                                            class="bi bi-shop me-1"></i>{{ $item->vendedor->nome_fantasia }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <span
                                                    class="badge {{ $item->quantidade > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill"
                                                    style="font-size: 11px;">
                                                    {{ $item->quantidade > 0 ? 'Em estoque' : 'Esgotado' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Controles de Quantidade e Preço -->
                                    <div
                                        class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3 ms-sm-auto">
                                        <!-- Formulário de Quantidade -->
                                        <form action="{{ route('carrinho.atualizar', $item) }}" method="POST"
                                            class="d-flex align-items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <label for="qtd_{{ $item->id }}"
                                                class="visually-hidden">Quantidade</label>
                                            <select name="quantidade" id="qtd_{{ $item->id }}"
                                                class="form-select form-select-sm rounded-3 py-1 pe-4"
                                                data-navigate-on-change>
                                                @for ($i = 1; $i <= min(10, max(1, $item->quantidade)); $i++)
                                                    <option value="{{ $i }}"
                                                        {{ (int) $item->quantidade_carrinho === $i ? 'selected' : '' }}>
                                                        {{ $i }} un
                                                    </option>
                                                @endfor
                                            </select>
                                        </form>

                                        <!-- Preço Unitário e Subtotal do Item -->
                                        <div class="text-end" style="min-width: 95px;">
                                            <div class="fw-bold text-dark">
                                                R$
                                                {{ number_format((float) $item->preco * (int) $item->quantidade_carrinho, 2, ',', '.') }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 11px;">
                                                {{ $item->preco_formatado }} cada
                                            </div>
                                        </div>

                                        <!-- Botão Remover -->
                                        <form action="{{ route('carrinho.remover', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete"
                                                title="Remover item do carrinho" aria-label="Remover item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url('/#catalogo') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Continuar Comprando
                        </a>
                    </div>
                </div>

                <!-- Coluna de Resumo do Pedido -->
                <div class="col-12 col-lg-4">
                    <!-- Card de Cupom -->
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                        <h2 class="h6 fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                            <i data-lucide="ticket" class="text-primary" style="width: 18px; height: 18px;"></i>
                            <span>Cupom de Desconto</span>
                        </h2>

                        @if ($cupom)
                            <div
                                class="p-2 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-success small">{{ $cupom->codigo }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $cupom->descricao_desconto }}
                                    </div>
                                </div>
                                <form action="{{ route('carrinho.cupom.remover') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"
                                        title="Remover cupom">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('carrinho.cupom.aplicar') }}" method="POST"
                                class="d-flex gap-2">
                                @csrf
                                <input type="text" name="codigo"
                                    class="form-control form-control-sm text-uppercase font-monospace"
                                    placeholder="Ex: LIVRO10" required>
                                <button type="submit" class="btn btn-dark btn-sm px-3 fw-semibold">Aplicar</button>
                            </form>
                        @endif
                    </div>

                    <!-- Card de Resumo Financeiro -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h2 class="h5 fw-bold text-dark mb-3">Resumo da Compra</h2>

                        <div class="d-flex justify-content-between text-secondary mb-2 small">
                            <span>Subtotal dos Livros</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>

                        @if ($desconto > 0)
                            <div class="d-flex justify-content-between text-success mb-2 small">
                                <span>Desconto Promocional</span>
                                <span>- R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between text-secondary mb-3 small">
                            <span>Frete Estimado</span>
                            <span>
                                @if ($valorFrete == 0.0)
                                    <strong class="text-success text-uppercase">Grátis</strong>
                                @else
                                    R$ {{ number_format($valorFrete, 2, ',', '.') }}
                                @endif
                            </span>
                        </div>

                        @if ($subtotal > 0 && $subtotal < 99.0)
                            <div class="p-2 bg-light rounded-3 text-muted mb-3" style="font-size: 11px;">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                Adicione mais <strong>R$ {{ number_format(99.0 - $subtotal, 2, ',', '.') }}</strong>
                                para garantir <strong>Frete Grátis</strong>!
                            </div>
                        @endif

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-6 fw-bold text-dark">Valor Total</span>
                            <span class="fs-4 fw-bold text-primary">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                            class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span>Fechar Pedido</span>
                            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                        </a>

                        <div class="mt-3 text-center text-muted" style="font-size: 11px;">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i>
                            Ambiente 100% criptografado e seguro
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-layouts.principal>
