<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                @if ($ehAdmin)
                    <li class="breadcrumb-item"><a href="{{ route('admin.livros.index') }}"
                            class="text-decoration-none text-muted">Catálogo</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                            class="text-decoration-none text-muted">Minha Loja</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendedor.livros.index') }}"
                            class="text-decoration-none text-muted">Meus Livros</a></li>
                @endif
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Detalhes</li>
            </ol>
        </nav>

        <!-- CABEÇALHO COM AÇÕES -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-book-half text-primary me-2"></i>
                    {{ $livro->titulo }}
                </h1>
                <p class="text-muted small mb-0">
                    Código ISBN: <code class="text-primary">{{ $livro->isbn }}</code> |
                    Loja: <strong>{{ $livro->vendedor->nome_fantasia ?? 'Sem Vendedor' }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route($rotaPrefix . '.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <a href="{{ route($rotaPrefix . '.edit', $livro) }}" class="btn btn-primary rounded-3">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form action="{{ route($rotaPrefix . '.destroy', $livro) }}" method="POST"
                    data-confirm="Tem certeza de que deseja remover este livro do catálogo?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3">
                        <i class="bi bi-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- CAPA E STATUS RÁPIDOS -->
            <div class="col-12 col-md-4 col-lg-3 text-center">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="bg-light p-2 rounded-3 border d-inline-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 180px; height: 250px;">
                        <img src="{{ $livro->capa ? asset('storage/' . $livro->capa) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80' }}"
                            alt="{{ $livro->titulo }}" class="rounded shadow-sm"
                            style="max-width: 160px; max-height: 235px; object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <div class="fs-4 fw-bold text-success mb-1">
                            R$ {{ number_format($livro->preco, 2, ',', '.') }}
                        </div>
                        @if ($livro->quantidade > 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                <i class="bi bi-check-circle me-1"></i> {{ $livro->quantidade }} em estoque
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                <i class="bi bi-x-circle me-1"></i> Estoque Esgotado
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- FICHA TÉCNICA E DESEMPENHO -->
            <div class="col-12 col-md-8 col-lg-9">
                <!-- CARDS DE MÉTRICAS -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="text-muted small d-block mb-1">Unidades Vendidas</span>
                            <span class="fs-4 fw-bold text-dark">{{ $totalVendido }} un.</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="text-muted small d-block mb-1">Receita Gerada</span>
                            <span class="fs-4 fw-bold text-primary">R$ {{ number_format($receitaGerada, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="text-muted small d-block mb-1">Valor em Estoque</span>
                            <span class="fs-4 fw-bold text-success">R$ {{ number_format($livro->preco * $livro->quantidade, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- DADOS TÉCNICOS -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Ficha Técnica</h5>
                    <div class="row g-3 small">
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Autor:</span>
                            <strong class="text-dark">{{ $livro->autor->nome ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Gênero:</span>
                            <strong class="text-dark">{{ $livro->genero->nome ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Editora:</span>
                            <strong class="text-dark">{{ $livro->editora->nome ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Data de Publicação:</span>
                            <strong class="text-dark">{{ $livro->data_publicacao ? $livro->data_publicacao->format('d/m/Y') : 'N/A' }}</strong>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Loja Vendedora:</span>
                            <strong class="text-dark">{{ $livro->vendedor->nome_fantasia ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="text-muted d-block">Cadastrado em:</span>
                            <strong class="text-dark">{{ $livro->created_at ? $livro->created_at->format('d/m/Y H:i') : 'N/A' }}</strong>
                        </div>
                    </div>

                    @if ($livro->sinopse)
                        <h6 class="fw-bold text-dark mt-4 mb-2">Sinopse</h6>
                        <p class="text-secondary small lh-base mb-0" style="white-space: pre-line;">
                            {{ $livro->sinopse }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>

