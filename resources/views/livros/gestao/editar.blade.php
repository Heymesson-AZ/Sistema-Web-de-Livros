<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                @if ($ehAdmin)
                    <li class="breadcrumb-item"><a href="{{ route('admin.livros.index') }}"
                            class="text-decoration-none text-muted">Livros</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                            class="text-decoration-none text-muted">Minha Loja</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendedor.livros.index') }}"
                            class="text-decoration-none text-muted">Meus Livros</a></li>
                @endif
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar Livro</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Editar Obra: <span class="text-secondary">{{ $livro->titulo }}</span>
                </h1>
                <p class="text-muted small mb-0">
                    Modifique os dados técnicos, precificação ou quantidade em estoque da obra.
                </p>
            </div>
            <a href="{{ route($rotaPrefix . '.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <form method="POST" action="{{ route($rotaPrefix . '.update', $livro) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @include('livros.gestao._formulario')

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route($rotaPrefix . '.index') }}" class="btn btn-light rounded-3 px-4">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                                    <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>

