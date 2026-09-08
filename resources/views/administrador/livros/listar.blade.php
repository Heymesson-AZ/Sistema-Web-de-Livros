<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Gestão de Livros</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-book-half text-primary me-2"></i>
                    Catálogo de Livros
                </h1>
                <p class="text-muted small mb-0">
                    Gerenciamento global de todas as obras, estoques e vendedores cadastrados na Universo de Papel.
                </p>
            </div>
            <a href="{{ route('admin.livros.create') }}"
                class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Novo Livro</span>
            </a>
        </div>

        <!-- MENSAGENS DE FEEDBACK -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- KPIS / INDICADORES -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total de Livros</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalLivros }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                            <i class="bi bi-journal-album fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Com Estoque</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $totalComEstoque }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #ecfdf5; color: #059669;">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Esgotados</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ $totalSemEstoque }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #fef2f2; color: #dc2626;">
                            <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Valor em Estoque</span>
                            <h4 class="fw-bold mb-0 text-primary mt-1">R$
                                {{ number_format((float) $valorTotalEstoque, 2, ',', '.') }}</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: #f0fdf4; color: #16a34a;">
                            <i class="bi bi-cash-coin fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS DE PESQUISA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.livros.index') }}" method="GET" class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-secondary">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" name="busca" class="form-control border-start-0"
                                placeholder="Título, autor, ISBN..." value="{{ request('busca') }}">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Gênero</label>
                        <select name="genero_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($generos as $g)
                                <option value="{{ $g->id }}"
                                    {{ request('genero_id') == $g->id ? 'selected' : '' }}>{{ $g->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Editora</label>
                        <select name="editora_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($editoras as $e)
                                <option value="{{ $e->id }}"
                                    {{ request('editora_id') == $e->id ? 'selected' : '' }}>{{ $e->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Vendedor</label>
                        <select name="vendedor_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($vendedores as $v)
                                <option value="{{ $v->id }}"
                                    {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->nome_fantasia ?? $v->razao_social }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Estoque</label>
                        <select name="status_estoque" class="form-select">
                            <option value="">Todos</option>
                            <option value="com_estoque"
                                {{ request('status_estoque') == 'com_estoque' ? 'selected' : '' }}>Em Estoque</option>
                            <option value="sem_estoque"
                                {{ request('status_estoque') == 'sem_estoque' ? 'selected' : '' }}>Esgotados</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-1 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                            <i class="bi bi-funnel-fill"></i>
                        </button>
                        @if (request()->hasAny(['busca', 'genero_id', 'editora_id', 'vendedor_id', 'status_estoque', 'ordem']))
                            <a href="{{ route('admin.livros.index') }}" class="btn btn-outline-secondary"
                                title="Limpar Filtros">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- TABELA DE LIVROS -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">Capa</th>
                            <th>Título / ISBN</th>
                            <th>Autor</th>
                            <th>Gênero & Editora</th>
                            <th>Vendedor</th>
                            <th>Preço</th>
                            <th class="text-center">Estoque</th>
                            <th class="text-end pe-4" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livros as $livro)
                            <tr>
                                <td class="ps-4">
                                    <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}"
                                        class="rounded-2 shadow-sm"
                                        style="width: 44px; height: 60px; object-fit: cover;"
                                        onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80'">
                                </td>
                                <td>
                                    <a href="{{ route('admin.livros.show', $livro) }}"
                                        class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                        {{ $livro->titulo }}
                                    </a>
                                    <span class="small text-muted font-monospace">ISBN: {{ $livro->isbn }}</span>
                                </td>
                                <td>
                                    <span
                                        class="text-secondary small fw-semibold">{{ $livro->autor->nome ?? '-' }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-light text-secondary border rounded-pill">{{ $livro->genero->nome ?? '-' }}</span>
                                    <div class="small text-muted mt-1">{{ $livro->editora->nome ?? '-' }}</div>
                                </td>
                                <td>
                                    <span
                                        class="small fw-semibold text-dark">{{ $livro->vendedor->nome_fantasia ?? ($livro->vendedor->razao_social ?? '-') }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $livro->preco_formatado }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($livro->isDisponivel())
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                            {{ $livro->quantidade }} un.
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                            Esgotado
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.livros.show', $livro) }}"
                                            class="btn btn-outline-secondary" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.livros.edit', $livro) }}"
                                            class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Excluir"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $livro->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- MODAL DE EXCLUSÃO -->
                                    <div class="modal fade" id="deleteModal{{ $livro->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $livro->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger"
                                                        id="deleteModalLabel{{ $livro->id }}">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar
                                                        Exclusão
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start py-3">
                                                    <p class="mb-2">Tem certeza que deseja remover o livro
                                                        <strong>"{{ $livro->titulo }}"</strong> do catálogo?</p>
                                                    <p class="small text-muted mb-0">Esta ação realiza uma exclusão
                                                        segura (Soft Delete), preservando o histórico de pedidos e
                                                        relatórios anteriores.</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-3"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('admin.livros.destroy', $livro) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger rounded-pill px-4">Excluir
                                                            Livro</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Nenhum livro encontrado com os critérios pesquisados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($livros->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                    {{ $livros->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
