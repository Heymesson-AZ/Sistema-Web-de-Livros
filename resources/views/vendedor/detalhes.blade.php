<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendedores.index') }}" class="text-decoration-none text-muted">Vendedores</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $vendedor->nome_fantasia }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-shop text-primary me-2"></i>
                        Ficha do Vendedor
                    </h1>
                    <p class="text-muted small mb-0">Informações cadastrais e desempenho da livraria parceira.</p>
                </div>
                <a href="{{ route('admin.vendedores.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- CARTÃO DE RESUMO E AVATAR -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm mx-auto mb-3"
                         style="width: 80px; height: 80px; background: #e0f2fe; color: #0369a1; font-size: 2rem;">
                        {{ strtoupper(substr($vendedor->nome_fantasia ?? 'V', 0, 1)) }}
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $vendedor->nome_fantasia }}</h4>
                    <p class="text-muted small mb-3">{{ $vendedor->razao_social }}</p>

                    <div class="mb-4">
                        @if ($vendedor->status_aprovacao === 'aprovado')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i> Cadastro Aprovado
                            </span>
                        @elseif ($vendedor->status_aprovacao === 'pendente')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-hourglass-split me-1"></i> Aguardando Aprovação
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-x-circle-fill me-1"></i> Cadastro Rejeitado
                            </span>
                        @endif
                    </div>

                    <!-- ESTATÍSTICAS RÁPIDAS -->
                    <div class="row g-2 pt-3 border-top text-start">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Livros Ativos</span>
                                <span class="fs-5 fw-bold text-primary">{{ $vendedor->livros?->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Pedidos</span>
                                <span class="fs-5 fw-bold text-success">{{ $vendedor->pedidos?->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- AÇÕES -->
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.vendedores.edit', $vendedor) }}" class="btn btn-primary rounded-3">
                            <i class="bi bi-pencil-square me-1"></i> Editar Dados
                        </a>
                    </div>
                </div>
            </div>

            <!-- DADOS COMPLETOS -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary"></i>
                        Dados Corporativos da Loja
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Nome Fantasia</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->nome_fantasia }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Razão Social</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->razao_social }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">CNPJ</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->cnpj }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Inscrição Estadual</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->inscricao_estadual }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Telefone Comercial</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->telefone_comercial ?? 'Não informado' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Data de Registro</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->created_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-primary"></i>
                        Representante / Conta de Acesso
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Nome do Responsável</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->user?->name ?? 'Usuário não vinculado' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">E-mail</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->user?->email ?? '-' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Status do E-mail</span>
                            @if ($vendedor->user?->email_verified_at)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Verificado em {{ $vendedor->user->email_verified_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-exclamation-circle me-1"></i> Não verificado
                                </span>
                            @endif
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Última Atualização</span>
                            <span class="fw-semibold text-dark fs-6">{{ $vendedor->updated_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
