<x-layouts.visitante>
    <div class="container-fluid px-lg-4 py-3">

        <!-- =======================================================
             1. CARROSSEL DE LIVROS EM DESTAQUE (100% RESPONSIVO & VANILLA JS)
             Sempre visível no topo da página principal para qualquer visitante
             ======================================================= -->
        <section class="mb-5">
            <x-carrosel :livros="$destaques" />
        </section>

        <!-- =======================================================
             2. CATÁLOGO COM FILTRO ESTILO AMAZON
             ======================================================= -->
        <section id="catalogo" class="mb-5">
            <div class="row g-4">

                <!-- SIDEBAR DE FILTROS (MOBILE-FIRST COM TOGGLE SANFONADO NO CELULAR) -->
                <aside class="col-12 col-lg-3">
                    @php
                        $filtrosAtivosCount = collect([
                            'busca',
                            'genero',
                            'categoria',
                            'editora',
                            'faixa_preco',
                            'preco_min',
                            'preco_max',
                            'em_estoque',
                        ])
                            ->filter(fn($f) => request()->filled($f))
                            ->count();
                    @endphp

                    <!-- Botão Expansível Exclusivo Mobile (< 992px) -->
                    <div class="d-lg-none mb-3">
                        <button
                            class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center py-2.5 px-3 rounded-3 shadow-xs"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filtrosMobileCollapse"
                            aria-expanded="{{ $filtrosAtivosCount > 0 ? 'true' : 'false' }}"
                            aria-controls="filtrosMobileCollapse">
                            <span class="fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-funnel-fill"></i> Filtrar Catálogo
                                @if ($filtrosAtivosCount > 0)
                                    <span class="badge bg-primary text-white rounded-pill px-2"
                                        style="font-size: 11px;">
                                        {{ $filtrosAtivosCount }} ativo(s)
                                    </span>
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>

                    <div class="collapse d-lg-block {{ $filtrosAtivosCount > 0 ? 'show' : '' }}"
                        id="filtrosMobileCollapse">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 85px; z-index: 10;">
                            <div class="card-body p-4">

                                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-funnel-fill text-primary"></i> Filtros
                                    </h5>
                                    @if (request()->hasAny([
                                            'busca',
                                            'genero',
                                            'categoria',
                                            'editora',
                                            'faixa_preco',
                                            'preco_min',
                                            'preco_max',
                                            'em_estoque',
                                            'ordem',
                                        ]))
                                        <a href="{{ url('/#catalogo') }}"
                                            class="small text-danger text-decoration-none fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i>Limpar
                                        </a>
                                    @endif
                                </div>

                                <form action="{{ url('/#catalogo') }}" method="GET" id="filtrosForm">

                                    <!-- FILTRO: BUSCA DENTRO DO CATÁLOGO COM BUSCA DINÂMICA -->
                                    <div class="mb-4">
                                        <label
                                            class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Palavra-chave</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="busca" class="form-control rounded-start-pill"
                                                placeholder="Título, autor, ISBN..." value="{{ request('busca') }}"
                                                data-dynamic-search autocomplete="off">
                                            <button class="btn btn-outline-primary rounded-end-pill" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- FILTRO: DEPARTAMENTOS / GÊNEROS (SELEÇÃO MANUAL) -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark small text-uppercase tracking-wider mb-2">
                                            Departamentos
                                        </h6>
                                        <div class="d-flex flex-column gap-2 small p-2 bg-light rounded-3"
                                            style="max-height: 220px; overflow-y: auto;">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="genero"
                                                    id="gen_todos" value=""
                                                    {{ !request('genero') && !request('categoria') ? 'checked' : '' }}>
                                                <label
                                                    class="form-check-label text-secondary w-100 d-flex justify-content-between align-items-center"
                                                    for="gen_todos">
                                                    <span>Todos os Gêneros</span>
                                                    <span
                                                        class="badge bg-white text-secondary border">{{ $totalLivros }}</span>
                                                </label>
                                            </div>
                                            @foreach ($generos as $gen)
                                                @php
                                                    $isSelected =
                                                        request('genero') == $gen->id ||
                                                        request('categoria') == $gen->nome ||
                                                        request('genero') == $gen->nome;
                                                @endphp
                                                <div class="form-check m-0">
                                                    <input class="form-check-input" type="radio" name="genero"
                                                        id="gen_{{ $gen->id }}" value="{{ $gen->id }}"
                                                        {{ $isSelected ? 'checked' : '' }}>
                                                    <label
                                                        class="form-check-label text-secondary w-100 d-flex justify-content-between align-items-center"
                                                        for="gen_{{ $gen->id }}">
                                                        <span class="text-truncate"
                                                            style="max-width: 140px;">{{ $gen->nome }}</span>
                                                        <span
                                                            class="badge bg-white text-secondary border">{{ $gen->livros_count }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <hr class="border-secondary border-opacity-25 my-3">

                                    <!-- FILTRO: PREÇO (PRESETS ESTILO AMAZON - DISPARO MANUAL) -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark small text-uppercase tracking-wider mb-2">Preço
                                        </h6>
                                        <div class="d-flex flex-column gap-2 small">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="faixa_preco"
                                                    id="preco_todos" value=""
                                                    {{ !request('faixa_preco') && !request('preco_min') && !request('preco_max') ? 'checked' : '' }}>
                                                <label class="form-check-label text-secondary"
                                                    for="preco_todos">Qualquer
                                                    preço</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="faixa_preco"
                                                    id="preco_ate_30" value="ate-30"
                                                    {{ request('faixa_preco') == 'ate-30' ? 'checked' : '' }}>
                                                <label class="form-check-label text-secondary" for="preco_ate_30">Até R$
                                                    30,00</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="faixa_preco"
                                                    id="preco_30_60" value="30-60"
                                                    {{ request('faixa_preco') == '30-60' ? 'checked' : '' }}>
                                                <label class="form-check-label text-secondary" for="preco_30_60">R$ 30 a
                                                    R$
                                                    60,00</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="faixa_preco"
                                                    id="preco_60_100" value="60-100"
                                                    {{ request('faixa_preco') == '60-100' ? 'checked' : '' }}>
                                                <label class="form-check-label text-secondary" for="preco_60_100">R$
                                                    60 a R$
                                                    100,00</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="faixa_preco"
                                                    id="preco_acima_100" value="acima-100"
                                                    {{ request('faixa_preco') == 'acima-100' ? 'checked' : '' }}>
                                                <label class="form-check-label text-secondary"
                                                    for="preco_acima_100">Acima
                                                    de R$ 100,00</label>
                                            </div>
                                        </div>

                                        <!-- FAIXA PERSONALIZADA -->
                                        <div class="mt-3 pt-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="input-group input-group-sm">
                                                    <span
                                                        class="input-group-text bg-light text-muted border-end-0">R$</span>
                                                    <input type="number" step="0.5" name="preco_min"
                                                        class="form-control" placeholder="Mín"
                                                        value="{{ request('preco_min') }}">
                                                </div>
                                                <span class="text-muted small">a</span>
                                                <div class="input-group input-group-sm">
                                                    <span
                                                        class="input-group-text bg-light text-muted border-end-0">R$</span>
                                                    <input type="number" step="0.5" name="preco_max"
                                                        class="form-control" placeholder="Máx"
                                                        value="{{ request('preco_max') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="border-secondary border-opacity-25 my-3">

                                    <!-- FILTRO: DISPONIBILIDADE / ESTOQUE (DISPARO MANUAL) -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark small text-uppercase tracking-wider mb-2">
                                            Disponibilidade</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="em_estoque"
                                                id="em_estoque" value="1"
                                                {{ request('em_estoque') ? 'checked' : '' }}>
                                            <label class="form-check-label text-secondary small" for="em_estoque">
                                                Somente livros em estoque ({{ $totalDisponiveis }})
                                            </label>
                                        </div>
                                    </div>

                                    <!-- FILTRO: EDITORAS (DISPARO MANUAL) -->
                                    @if ($editoras->isNotEmpty())
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark small text-uppercase tracking-wider mb-2">
                                                Editoras
                                            </h6>
                                            <select name="editora" class="form-select form-select-sm rounded-3">
                                                <option value="">Todas as Editoras</option>
                                                @foreach ($editoras as $ed)
                                                    <option value="{{ $ed->id }}"
                                                        {{ request('editora') == $ed->id ? 'selected' : '' }}>
                                                        {{ $ed->nome }} ({{ $ed->livros_count }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <!-- BOTÕES DE APLICAÇÃO MANUAL DOS FILTROS -->
                                    <div class="d-grid gap-2 pt-2 border-top">
                                        <button type="submit"
                                            class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-funnel-fill"></i>
                                            <span>Aplicar Filtros</span>
                                        </button>
                                        <a href="{{ url('/#catalogo') }}"
                                            class="btn btn-outline-secondary rounded-pill btn-sm py-1 d-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Limpar Filtros</span>
                                        </a>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </aside>

                <!-- GRID DE RESULTADOS & CABEÇALHO DO CATÁLOGO -->
                <main class="col-12 col-lg-9">

                    <!-- BARRA DE CABEÇALHO E ORDENAÇÃO -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                                <div>
                                    <h4 class="fw-bold text-dark mb-1">
                                        @if (request('busca'))
                                            Resultados para "<span class="text-primary">{{ request('busca') }}</span>"
                                        @elseif(request('genero') || request('categoria'))
                                            Catálogo de Livros
                                        @else
                                            Todos os Livros Disponíveis
                                        @endif
                                    </h4>
                                    <p class="text-muted small mb-0">
                                        Exibindo {{ $livros->firstItem() ?? 0 }}-{{ $livros->lastItem() ?? 0 }} de
                                        {{ $livros->total() }} títulos encontrados
                                    </p>
                                </div>

                                <!-- SELECT DE ORDENAÇÃO -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="ordemSelect"
                                        class="small text-secondary fw-semibold text-nowrap">Ordenar por:</label>
                                    <select id="ordemSelect"
                                        class="form-select form-select-sm rounded-pill border-secondary border-opacity-25"
                                        style="min-width: 170px;" data-navigate-on-change>
                                        <option value="{{ request()->fullUrlWithQuery(['ordem' => 'novidades']) }}"
                                            {{ request('ordem', 'novidades') == 'novidades' ? 'selected' : '' }}>
                                            Destaques / Mais Recentes
                                        </option>
                                        <option value="{{ request()->fullUrlWithQuery(['ordem' => 'preco_menor']) }}"
                                            {{ request('ordem') == 'preco_menor' ? 'selected' : '' }}>
                                            Preço: Menor para Maior
                                        </option>
                                        <option value="{{ request()->fullUrlWithQuery(['ordem' => 'preco_maior']) }}"
                                            {{ request('ordem') == 'preco_maior' ? 'selected' : '' }}>
                                            Preço: Maior para Menor
                                        </option>
                                        <option value="{{ request()->fullUrlWithQuery(['ordem' => 'titulo_az']) }}"
                                            {{ request('ordem') == 'titulo_az' ? 'selected' : '' }}>
                                            Nome: A a Z
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <!-- BADGES DE FILTROS ATIVOS -->
                            @if (request()->hasAny([
                                    'busca',
                                    'genero',
                                    'categoria',
                                    'editora',
                                    'faixa_preco',
                                    'preco_min',
                                    'preco_max',
                                    'em_estoque',
                                ]))
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                                    <span class="small text-secondary fw-semibold me-1">Filtros ativos:</span>

                                    @if (request('busca'))
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                            Busca: "{{ request('busca') }}"
                                            <a href="{{ request()->fullUrlWithQuery(['busca' => null]) }}"
                                                class="text-primary text-decoration-none ms-1">&times;</a>
                                        </span>
                                    @endif

                                    @if (request('genero') || request('categoria'))
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25 rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                            Gênero selecionado
                                            <a href="{{ request()->fullUrlWithQuery(['genero' => null, 'categoria' => null]) }}"
                                                class="text-info-emphasis text-decoration-none ms-1">&times;</a>
                                        </span>
                                    @endif

                                    @if (request('faixa_preco'))
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                            Preço: {{ request('faixa_preco') }}
                                            <a href="{{ request()->fullUrlWithQuery(['faixa_preco' => null]) }}"
                                                class="text-success text-decoration-none ms-1">&times;</a>
                                        </span>
                                    @endif

                                    @if (request('em_estoque'))
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                            Em estoque
                                            <a href="{{ request()->fullUrlWithQuery(['em_estoque' => null]) }}"
                                                class="text-success text-decoration-none ms-1">&times;</a>
                                        </span>
                                    @endif

                                    <a href="{{ url('/#catalogo') }}"
                                        class="small text-danger text-decoration-none fw-semibold ms-auto">
                                        Limpar todos os filtros
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- GRID DE PRODUTOS -->
                    @if ($livros->isEmpty())
                        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-book text-secondary" style="font-size: 38px;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Nenhum livro encontrado</h4>
                                <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
                                    Não encontramos nenhum livro com os filtros selecionados. Tente ajustar os termos de
                                    busca, gênero ou faixa de preço.
                                </p>
                                <a href="{{ url('/#catalogo') }}"
                                    class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                                    Ver Todos os Livros
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-2 g-sm-3 g-md-4 mb-4">
                            @foreach ($livros as $livro)
                                <div class="col">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden amazon-book-card transition-all">

                                        <!-- CAPA DO LIVRO -->
                                        <div
                                            class="position-relative bg-light text-center p-2 p-sm-3 overflow-hidden book-cover-wrapper">
                                            <a href="{{ route('livros.detalhes', $livro) }}"
                                                aria-label="Ver detalhes de {{ $livro->titulo }}">
                                                <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}"
                                                    class="img-fluid rounded-3 shadow-sm book-cover-img"
                                                    loading="lazy"
                                                    onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80'">
                                            </a>

                                            <!-- BOTÃO FAVORITAR ERGONÔMICO (A11Y) -->
                                            @php
                                                $isFavoritado = Auth::check() && $livro->isFavoritadoPor(Auth::user());
                                            @endphp
                                            <button type="button"
                                                class="btn-favorito-card position-absolute top-0 end-0 m-2 z-2"
                                                data-favorito-toggle
                                                data-favorito-url="{{ route('favoritos.toggle', $livro) }}"
                                                title="{{ $isFavoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                                                aria-label="{{ $isFavoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}">
                                                <i
                                                    class="bi {{ $isFavoritado ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' }} fs-6"></i>
                                            </button>

                                            <!-- BADGE DE GÊNERO -->
                                            @if ($livro->genero)
                                                <span
                                                    class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 backdrop-blur rounded-pill small px-2 py-1">
                                                    {{ $livro->genero->nome }}
                                                </span>
                                            @endif

                                            <!-- BADGE DE ESTOQUE -->
                                            @if (!$livro->isDisponivel())
                                                <span
                                                    class="position-absolute bottom-0 start-0 m-2 badge bg-danger rounded-pill small px-2 py-1">
                                                    Esgotado
                                                </span>
                                            @endif
                                        </div>

                                        <!-- CORPO DO CARD -->
                                        <div class="card-body p-3 d-flex flex-column">

                                            <!-- AUTOR -->
                                            <span
                                                class="small text-muted text-uppercase tracking-wider mb-1 text-truncate">
                                                {{ $livro->autor->nome ?? 'Autor Desconhecido' }}
                                            </span>

                                            <!-- TÍTULO -->
                                            <h6 class="fw-bold mb-2">
                                                <a href="{{ route('livros.detalhes', $livro) }}"
                                                    class="text-dark text-decoration-none book-title-clamp hover-primary">
                                                    {{ $livro->titulo }}
                                                </a>
                                            </h6>

                                            <!-- AVALIAÇÃO ESTILO AMAZON -->
                                            <div class="d-flex align-items-center gap-1 mb-2">
                                                <div class="text-warning small">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-half"></i>
                                                </div>
                                                <span class="text-muted small ms-1">(4.5)</span>
                                            </div>

                                            <!-- VENDEDOR & EDITORA -->
                                            <div class="small text-muted mb-3">
                                                @if ($livro->editora)
                                                    <span>{{ $livro->editora->nome }}</span> •
                                                @endif
                                                <span>Loja: <strong
                                                        class="text-dark">{{ $livro->vendedor->nome_fantasia ?? 'Livraria Oficial' }}</strong></span>
                                            </div>

                                            <!-- PREÇO & DISPONIBILIDADE -->
                                            <div class="mt-auto pt-2 border-top">
                                                <div class="d-flex align-items-baseline gap-1 mb-1">
                                                    <span
                                                        class="fs-4 fw-bold text-dark">{{ $livro->preco_formatado }}</span>
                                                    <span class="small text-muted">à vista</span>
                                                </div>
                                                <span class="small text-muted d-block mb-2">
                                                    ou até 3x sem juros
                                                </span>

                                                <!-- BOTÕES DE AÇÃO -->
                                                <div class="d-grid gap-2">
                                                    @if ($livro->isDisponivel())
                                                        <form action="{{ route('carrinho.adicionar') }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="livro_id"
                                                                value="{{ $livro->id }}">
                                                            <input type="hidden" name="quantidade" value="1">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm rounded-pill fw-semibold w-100 shadow-sm">
                                                                <i class="bi bi-cart-plus me-1"></i> Adicionar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button
                                                            class="btn btn-secondary btn-sm rounded-pill fw-semibold w-100 disabled"
                                                            disabled>
                                                            Esgotado
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('livros.detalhes', $livro) }}"
                                                        class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold">
                                                        <i class="bi bi-eye me-1"></i> Detalhes
                                                    </a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- PAGINAÇÃO -->
                        <div class="d-flex justify-content-center my-4">
                            {{ $livros->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                </main>

            </div>
        </section>

        <!-- =======================================================
             3. BANNER DE PARCERIA / SEJA UM VENDEDOR
             ======================================================= -->
        <section class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
            style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0 !important;">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success px-3 py-2 rounded-pill font-monospace"
                                style="font-size: 11px;">
                                <i class="bi bi-stars me-1"></i> Programa de Parceiros
                            </span>
                        </div>
                        <h2 class="fw-bold text-dark mb-2">Tem uma livraria, sebo ou quer vender seus livros?</h2>
                        <p class="text-secondary mb-3" style="font-size: 16px;">
                            Junte-se à <strong>Universo de Papel</strong>. Crie sua loja online, publique seus livros,
                            gerencie seu catálogo e comece a vender para leitores de todo o país. O cadastro é gratuito
                            e a aprovação é rápida!
                        </p>
                        <div class="row g-3 pt-2">
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Zero Mensalidade Fixa</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Painel de Gestão Completo</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Pagamento Seguro e Rápido</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end text-center">
                        <a href="{{ route('vendedor.solicitar') }}"
                            class="btn btn-success btn-lg rounded-pill px-4 py-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-shop fs-5"></i>
                            <span>Abrir Minha Loja Agora</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-layouts.visitante>
