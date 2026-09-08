<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                            class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clientes.index') }}"
                            class="text-decoration-none text-muted">Clientes</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                        {{ $cliente->user?->name }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Ficha do Cliente
                    </h1>
                    <p class="text-muted small mb-0">Informações cadastrais e histórico de compras do cliente.</p>
                </div>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- CARTÃO DE RESUMO E AVATAR -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <img src="{{ $cliente->user?->foto }}" alt="{{ $cliente->user?->name }}"
                        class="rounded-circle shadow-sm object-fit-cover mx-auto mb-3 border border-2 border-white"
                        style="width: 80px; height: 80px;">

                    <h4 class="fw-bold text-dark mb-1">{{ $cliente->user?->name ?? 'Cliente' }}</h4>
                    <p class="text-muted small mb-3">{{ $cliente->user?->email ?? '-' }}</p>

                    <div class="mb-4">
                        @if ($cliente->user?->status === 'ativo')
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-person-check-fill me-1"></i> Conta de Cliente Ativa
                            </span>
                        @elseif($cliente->user?->status === 'inativo')
                            <span
                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-person-dash-fill me-1"></i> Conta Inativa
                            </span>
                        @else
                            <span
                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-slash-circle-fill me-1"></i> Conta Banida / Bloqueada
                            </span>
                        @endif
                    </div>

                    <!-- ESTATÍSTICAS RÁPIDAS -->
                    <div class="row g-2 pt-3 border-top text-start">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Pedidos</span>
                                <span class="fs-5 fw-bold text-success">{{ $cliente->pedidos?->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light text-center">
                                <span class="text-muted small d-block">Avaliações</span>
                                <span class="fs-5 fw-bold text-warning">{{ $cliente->avaliacoes?->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- AÇÕES -->
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-primary rounded-3">
                            <i class="bi bi-pencil-square me-1"></i> Editar Dados
                        </a>
                    </div>
                </div>
            </div>

            <!-- DADOS COMPLETOS -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard text-primary"></i>
                        Dados Pessoais
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Nome Completo</span>
                            <span class="fw-semibold text-dark fs-6">{{ $cliente->user?->name }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">CPF</span>
                            <span class="fw-semibold font-monospace text-dark fs-6">{{ $cliente->cpf }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Celular de Contato</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $cliente->celular_contato ?? 'Não informado' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Data de Nascimento</span>
                            <span class="fw-semibold text-dark fs-6">
                                @if ($cliente->data_nascimento)
                                    {{ \Carbon\Carbon::parse($cliente->data_nascimento)->format('d/m/Y') }}
                                    ({{ \Carbon\Carbon::parse($cliente->data_nascimento)->age }} anos)
                                @else
                                    Não informada
                                @endif
                            </span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Data de Cadastro</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $cliente->created_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Última Atualização</span>
                            <span
                                class="fw-semibold text-dark fs-6">{{ $cliente->updated_at?->format('d/m/Y \à\s H:i') ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock text-primary"></i>
                        Informações da Conta de Acesso
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">E-mail Cadastrado</span>
                            <span class="fw-semibold text-dark fs-6">{{ $cliente->user?->email ?? '-' }}</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Confirmação de E-mail</span>
                            @if ($cliente->user?->email_verified_at)
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Verificado em
                                    {{ $cliente->user->email_verified_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                                    <i class="bi bi-exclamation-circle me-1"></i> E-mail não verificado
                                </span>
                            @endif
                        </div>

                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Status do Acesso</span>
                            <span
                                class="badge {{ $cliente->user?->status === 'ativo' ? 'bg-success' : ($cliente->user?->status === 'inativo' ? 'bg-secondary' : 'bg-danger') }} rounded-pill text-capitalize px-3 py-1">
                                {{ $cliente->user?->status ?? 'ativo' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
