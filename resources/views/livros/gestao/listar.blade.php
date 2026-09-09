<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                @if ($ehAdmin)
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Catálogo Geral</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                            class="text-decoration-none text-muted">Minha Loja</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Meus Livros</li>
                @endif
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi {{ $ehAdmin ? 'bi-journals text-primary' : 'bi-shop text-success' }} me-2"></i>
                    {{ $ehAdmin ? 'Catálogo de Livros' : 'Catálogo da Minha Loja' }}
                </h1>
                <p class="text-muted small mb-0">
                    @if ($ehAdmin)
                        Supervisão, moderação e auditoria técnica de todas as obras cadastradas no sistema.
                    @else
                        Gerencie os títulos publicados pela sua livraria <strong>{{ $vendedor->nome_fantasia }}</strong>.
                    @endif
                </p>
            </div>

            @if (!$ehAdmin)
                <div>
                    <a href="{{ route('vendedor.livros.create') }}" class="btn btn-success rounded-3 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Publicar Novo Livro
                    </a>
                </div>
            @endif
        </div>

        <!-- MENSAGEM DE STATUS / ALERTA -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- INDICADORES (KPIS) -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Total de Livros</span>
                            <span class="h4 fw-bold text-dark mb-0">{{ $totalLivros }}</span>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <i class="bi bi-book fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Disponíveis</span>
                            <span class="h4 fw-bold text-success mb-0">{{ $totalComEstoque }}</span>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Sem Estoque</span>
                            <span class="h4 fw-bold text-danger mb-0">{{ $totalSemEstoque }}</span>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3">
                            <i class="bi bi-x-octagon fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Valor em Estoque</span>
                            <span class="h4 fw-bold text-primary mb-0">R$ {{ number_format($valorTotalEstoque, 2, ',', '.') }}</span>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS E BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route($rotaPrefix . '.index') }}" method="GET">
                    <div class="row g-2">
                        <!-- Termo de busca -->
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="busca" class="form-control border-start-0"
                                    placeholder="Buscar por título, ISBN ou autor..."
                                    value="{{ request('busca') }}" data-dynamic-search autocomplete="off">
                            </div>
                        </div>

                        <!-- Gênero -->
                        <div class="col-6 col-md-2">
                            <select name="genero_id" class="form-select">
                                <option value="">Todos os Gêneros</option>
                                @foreach ($generos as $gen)
                                    <option value="{{ $gen->id }}" {{ request('genero_id') == $gen->id ? 'selected' : '' }}>
                                        {{ $gen->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Editora -->
                        <div class="col-6 col-md-2">
                            <select name="editora_id" class="form-select">
                                <option value="">Todas Editoras</option>
                                @foreach ($editoras as $edit)
                                    <option value="{{ $edit->id }}" {{ request('editora_id') == $edit->id ? 'selected' : '' }}>
                                        {{ $edit->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vendedor (Apenas para Admin) -->
                        @if ($ehAdmin)
                            <div class="col-6 col-md-2">
                                <select name="vendedor_id" class="form-select">
                                    <option value="">Todos Vendedores</option>
                                    @foreach ($vendedores as $v)
                                        <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                                            {{ $v->nome_fantasia }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Status Estoque -->
                        <div class="col-6 col-md-{{ $ehAdmin ? '1' : '2' }}">
                            <select name="status_estoque" class="form-select">
                                <option value="">Estoque</option>
                                <option value="com_estoque" {{ request('status_estoque') === 'com_estoque' ? 'selected' : '' }}>Disponível</option>
                                <option value="sem_estoque" {{ request('status_estoque') === 'sem_estoque' ? 'selected' : '' }}>Zerado</option>
                            </select>
                        </div>

                        <!-- Botão Filtrar -->
                        <div class="col-12 col-md-{{ $ehAdmin ? '1' : '2' }} d-grid">
                            <button type="submit" class="btn btn-primary rounded-3">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABELA DE LIVROS -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th scope="col" style="width: 70px;">Capa</th>
                            <th scope="col">Obra / ISBN</th>
                            <th scope="col">Autor & Gênero</th>
                            @if ($ehAdmin)
                                <th scope="col">Loja Vendedora</th>
                            @endif
                            <th scope="col">Preço</th>
                            <th scope="col">Estoque</th>
                            <th scope="col" class="text-end" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livros as $livro)
                            <tr>
                                <td>
                                    <img src="{{ $livro->capa ? asset('storage/' . $livro->capa) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80' }}"
                                        alt="{{ $livro->titulo }}" class="rounded shadow-sm"
                                        style="width: 46px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <a href="{{ route($rotaPrefix . '.show', $livro) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $livro->titulo }}
                                    </a>
                                    <small class="text-muted d-block font-monospace">ISBN: {{ $livro->isbn }}</small>
                                </td>
                                <td>
                                    <span class="d-block text-dark small fw-semibold">{{ $livro->autor->nome ?? 'N/A' }}</span>
                                    <span class="badge bg-light text-secondary border">{{ $livro->genero->nome ?? 'N/A' }}</span>
                                </td>
                                @if ($ehAdmin)
                                    <td>
                                        <span class="small fw-semibold text-dark">{{ $livro->vendedor->nome_fantasia ?? 'N/A' }}</span>
                                    </td>
                                @endif
                                <td>
                                    <strong class="text-success">R$ {{ number_format($livro->preco, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if ($livro->quantidade > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                            {{ $livro->quantidade }} un.
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                            Esgotado
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route($rotaPrefix . '.show', $livro) }}" class="btn btn-outline-secondary" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route($rotaPrefix . '.edit', $livro) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route($rotaPrefix . '.destroy', $livro) }}" method="POST" class="d-inline"
                                            data-confirm="Tem certeza de que deseja remover '{{ $livro->titulo }}' do catálogo?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $ehAdmin ? 7 : 6 }}" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0 fw-semibold">Nenhum livro encontrado.</p>
                                    <small>Tente ajustar seus termos de busca ou filtros.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($livros->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $livros->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>

