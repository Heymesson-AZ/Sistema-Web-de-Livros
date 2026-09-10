<x-layouts.visitante>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"
                        class="text-decoration-none text-muted">Início</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/#catalogo') }}"
                        class="text-decoration-none text-muted">Livros</a></li>
                @if ($livro->genero)
                    <li class="breadcrumb-item">
                        <a href="{{ url('/?genero=' . $livro->genero->id) }}" class="text-decoration-none text-muted">
                            {{ $livro->genero->nome }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                    {{ Str::limit($livro->titulo, 35) }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5 mb-5">

            <!-- 1. CAPA DO LIVRO (ESQUERDA) -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top position-relative"
                    style="top: 85px;">
                    @php
                        $isFavoritado = Auth::check() && $livro->isFavoritadoPor(Auth::user());
                    @endphp
                    <button type="button"
                        class="btn-favorito-card position-absolute top-0 end-0 m-3 z-2"
                        data-favorito-toggle
                        data-favorito-url="{{ route('favoritos.toggle', $livro) }}"
                        title="{{ $isFavoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                        aria-label="{{ $isFavoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}">
                        <i
                            class="bi {{ $isFavoritado ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' }} fs-5"></i>
                    </button>
                    <div class="bg-light p-4 text-center d-flex align-items-center justify-content-center"
                        style="min-height: 420px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                        <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}"
                            class="img-fluid rounded-3 shadow-lg"
                            style="max-height: 380px; max-width: 100%; object-fit: contain;"
                            onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80'">
                    </div>
                    <div class="card-footer bg-white border-top border-light p-3 text-center">
                        <div class="d-flex justify-content-center align-items-center gap-3 text-muted small">
                            <span><i class="bi bi-shield-check text-success me-1"></i>Compra Garantida</span>
                            <span>•</span>
                            <span><i class="bi bi-truck text-primary me-1"></i>Envio Rápido</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. DETALHES PRINCIPAIS (CENTRO) -->
            <div class="col-12 col-md-7 col-lg-5">
                <div class="mb-2 d-flex align-items-center gap-2">
                    @if ($livro->genero)
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                            {{ $livro->genero->nome }}
                        </span>
                    @endif
                    @if ($livro->isDisponivel())
                        <span
                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                            Em Estoque
                        </span>
                    @else
                        <span
                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                            Esgotado
                        </span>
                    @endif
                </div>

                <h1 class="h2 fw-bold text-dark mb-2">{{ $livro->titulo }}</h1>

                <p class="text-muted mb-3">
                    Por <strong class="text-dark">{{ $livro->autor->nome ?? 'Autor Desconhecido' }}</strong>
                </p>

                <!-- AVALIAÇÕES ESTILO AMAZON -->
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="text-warning">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span class="fw-bold text-dark small">4.8</span>
                    <span class="text-muted small">•</span>
                    <span class="text-primary small">(124 avaliações de leitores)</span>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- SINOPSE -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">Sinopse</h5>
                    <div class="text-secondary lh-lg" style="white-space: pre-line;">
                        {{ $livro->sinopse ?: 'Nenhuma sinopse informada para esta obra.' }}
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- DETALHES TÉCNICOS DO LIVRO -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">Detalhes da Edição</h5>
                    <div class="row g-2 small">
                        <div class="col-6 col-sm-4 text-muted">Editora:</div>
                        <div class="col-6 col-sm-8 text-dark fw-semibold">
                            {{ $livro->editora->nome ?? 'Não informada' }}</div>

                        <div class="col-6 col-sm-4 text-muted">Ano de Publicação:</div>
                        <div class="col-6 col-sm-8 text-dark fw-semibold">
                            {{ $livro->data_publicacao ? $livro->data_publicacao->format('Y') : 'Não informado' }}
                        </div>

                        <div class="col-6 col-sm-4 text-muted">ISBN:</div>
                        <div class="col-6 col-sm-8 text-dark fw-semibold font-monospace">
                            {{ $livro->isbn ?: 'Não informado' }}</div>

                        <div class="col-6 col-sm-4 text-muted">Gênero Literário:</div>
                        <div class="col-6 col-sm-8 text-dark fw-semibold">{{ $livro->genero->nome ?? 'Não informado' }}
                        </div>

                        <div class="col-6 col-sm-4 text-muted">Idioma:</div>
                        <div class="col-6 col-sm-8 text-dark fw-semibold">Português (Brasil)</div>
                    </div>
                </div>

            </div>

            <!-- 3. BOX DE COMPRA / VENDEDOR ESTILO AMAZON (DIREITA) -->
            <div class="col-12 col-lg-3">
                <div class="card border border-secondary border-opacity-25 shadow-sm rounded-4 p-4 sticky-top"
                    style="top: 85px;">

                    <!-- PREÇO -->
                    <div class="mb-3">
                        <span class="text-muted small d-block">Preço à vista:</span>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="display-6 fw-bold text-dark">{{ $livro->preco_formatado }}</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill small mt-1">
                            Em até 3x de R$ {{ number_format((float) ($livro->preco / 3), 2, ',', '.') }} sem juros
                        </span>
                    </div>

                    <hr class="border-secondary border-opacity-25 my-3">

                    <!-- STATUS DE ESTOQUE -->
                    <div class="mb-3">
                        @if ($livro->isDisponivel())
                            <div class="d-flex align-items-center gap-2 text-success fw-bold">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Em estoque</span>
                            </div>
                            <small class="text-muted d-block mt-1">Restam {{ $livro->quantidade }} unidades
                                disponíveis</small>
                        @else
                            <div class="d-flex align-items-center gap-2 text-danger fw-bold">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Indisponível</span>
                            </div>
                            <small class="text-muted d-block mt-1">Avise-me quando chegar</small>
                        @endif
                    </div>

                    <!-- VENDEDOR -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="small text-muted mb-1">Vendido por:</div>
                        <strong class="text-dark d-block">
                            {{ $livro->vendedor->nome_fantasia ?? ($livro->vendedor->razao_social ?? 'Livraria Oficial') }}
                        </strong>
                        <div class="small text-muted mt-1">
                            Enviado e garantido por: <strong>Universo de Papel</strong>
                        </div>
                    </div>

                    <!-- BOTÕES DE COMPRA -->
                    @if ($livro->isDisponivel())
                        <form action="{{ route('carrinho.adicionar') }}" method="POST" class="d-grid gap-2 mb-3">
                            @csrf
                            <input type="hidden" name="livro_id" value="{{ $livro->id }}">
                            <input type="hidden" name="quantidade" value="1">
                            <button type="submit" name="comprar_agora" value="1"
                                class="btn btn-warning btn-lg rounded-pill fw-bold text-dark shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-bag-check-fill"></i>
                                <span>Comprar Agora</span>
                            </button>
                            <button type="submit"
                                class="btn btn-outline-primary rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-cart-plus"></i>
                                <span>Adicionar ao Carrinho</span>
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary rounded-pill w-100 disabled" disabled>
                            Produto Esgotado
                        </button>
                    @endif

                    <div class="text-center mt-3">
                        <a href="{{ url('/#catalogo') }}" class="small text-decoration-none text-muted">
                            <i class="bi bi-arrow-left me-1"></i>Continuar comprando
                        </a>
                    </div>

                </div>
            </div>

        </div>

        <!-- LIVROS RELACIONADOS -->
        @if ($relacionados->isNotEmpty())
            <section class="mt-5 pt-4 border-top">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold text-dark m-0">Títulos Semelhantes Recomendados</h4>
                    <a href="{{ url('/?genero=' . $livro->genero_id) }}"
                        class="small text-primary text-decoration-none fw-semibold">
                        Ver mais deste gênero <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                    @foreach ($relacionados as $rel)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center p-2">
                                <a href="{{ route('livros.detalhes', $rel) }}" class="text-decoration-none">
                                    <div class="bg-light p-2 rounded-2 mb-2 d-flex align-items-center justify-content-center"
                                        style="height: 160px;">
                                        <img src="{{ $rel->url_capa }}" alt="{{ $rel->titulo }}"
                                            class="img-fluid rounded shadow-sm"
                                            style="max-height: 140px; object-fit: contain;">
                                    </div>
                                    <h6 class="fw-bold text-dark small mb-1 text-truncate"
                                        title="{{ $rel->titulo }}">{{ $rel->titulo }}</h6>
                                    <div class="text-primary fw-bold small">{{ $rel->preco_formatado }}</div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</x-layouts.visitante>
