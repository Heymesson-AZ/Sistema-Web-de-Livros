<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Minha Loja</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.livros.index') }}"
                        class="text-decoration-none text-muted">Meus Livros</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                    {{ Str::limit($livro->titulo, 30) }}</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-info-circle text-success me-2"></i>
                    Detalhes do Livro
                </h1>
                <p class="text-muted small mb-0">
                    Estatísticas de vendas, informações de estoque e detalhes da obra na sua loja.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('livros.detalhes', $livro) }}" target="_blank"
                    class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver no Catálogo Público
                </a>
                <a href="{{ route('vendedor.livros.edit', $livro) }}" class="btn btn-success rounded-3">
                    <i class="bi bi-pencil me-1"></i> Editar Livro
                </a>
                <a href="{{ route('vendedor.livros.index') }}" class="btn btn-light border rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="row g-4">

            <!-- COLUNA ESQUERDA: CAPA & PREÇO -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="bg-light p-4 text-center d-flex align-items-center justify-content-center"
                        style="min-height: 320px;">
                        <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}" class="rounded-3 shadow"
                            style="max-height: 280px; max-width: 100%; object-fit: contain;"
                            onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80'">
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="display-6 fw-bold text-dark mb-1">{{ $livro->preco_formatado }}</div>
                        <span class="text-muted small">Preço de Venda Praticado</span>

                        <div class="mt-3 pt-3 border-top">
                            @if ($livro->isDisponivel())
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ $livro->quantidade }} un.
                                    disponíveis
                                </span>
                            @else
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Estoque Esgotado
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- CARDS DE DESEMPENHO -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white">
                            <span class="text-muted small text-uppercase fw-semibold">Vendas</span>
                            <h4 class="fw-bold text-dark mt-1 mb-0">{{ $totalVendido }} un.</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white">
                            <span class="text-muted small text-uppercase fw-semibold">Faturamento</span>
                            <h5 class="fw-bold text-success mt-1 mb-0">R$
                                {{ number_format((float) $receitaGerada, 2, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUNA DIREITA: INFORMAÇÕES DA OBRA -->
            <div class="col-12 col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Informações da Obra</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-muted fw-semibold d-block">Título</label>
                            <span class="fs-5 fw-bold text-dark">{{ $livro->titulo }}</span>
                        </div>

                        <div class="col-6 col-sm-4">
                            <label class="small text-muted fw-semibold d-block">ISBN</label>
                            <span class="font-monospace fw-bold text-dark">{{ $livro->isbn }}</span>
                        </div>

                        <div class="col-6 col-sm-4">
                            <label class="small text-muted fw-semibold d-block">Data de Publicação</label>
                            <span
                                class="text-dark">{{ $livro->data_publicacao ? $livro->data_publicacao->format('d/m/Y') : '-' }}</span>
                        </div>

                        <div class="col-6 col-sm-4">
                            <label class="small text-muted fw-semibold d-block">Publicado na Loja em</label>
                            <span
                                class="text-dark">{{ $livro->created_at ? $livro->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Classificação & Editora</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4">
                            <div class="p-3 bg-light rounded-3">
                                <span class="small text-muted fw-semibold d-block mb-1">Autor</span>
                                <strong class="text-dark">{{ $livro->autor->nome ?? 'Não informado' }}</strong>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="p-3 bg-light rounded-3">
                                <span class="small text-muted fw-semibold d-block mb-1">Gênero Literário</span>
                                <span
                                    class="badge bg-primary rounded-pill px-3 py-2">{{ $livro->genero->nome ?? 'Não informado' }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="p-3 bg-light rounded-3">
                                <span class="small text-muted fw-semibold d-block mb-1">Editora</span>
                                <strong class="text-dark">{{ $livro->editora->nome ?? 'Não informada' }}</strong>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Sinopse da Obra</h5>
                    <div class="text-secondary lh-base" style="white-space: pre-line;">
                        {{ $livro->sinopse ?: 'Nenhuma sinopse cadastrada para esta obra.' }}
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-layouts.principal>
