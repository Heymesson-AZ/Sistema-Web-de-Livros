<x-layouts.principal>
    <div class="container py-4 py-md-5">

        @if (session('status') === 'perfil-atualizado' || session('status') === 'profile-updated')
            <div
                class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">Dados do perfil atualizados com sucesso!</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @elseif (session('status'))
            <div
                class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <strong>Por favor, corrija os erros abaixo:</strong>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- CABEÇALHO DO PERFIL & PAINEL -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="p-4 p-md-5 text-white position-relative"
                style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4 position-relative"
                    style="z-index: 2;">
                    <!-- AVATAR -->
                    <div class="position-relative">
                        <img src="{{ $user->foto }}" alt="{{ $user->name }}"
                            class="rounded-circle shadow-lg border border-3 border-white object-fit-cover"
                            style="width: 96px; height: 96px;" id="painelHeaderAvatar">
                    </div>

                    <!-- DADOS BÁSICOS -->
                    <div class="text-center text-md-start flex-grow-1">
                        <div
                            class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                            <h2 class="h3 fw-bold mb-0 text-white">{{ $user->name }}</h2>
                            @if ($user->isAdmin())
                                <span class="badge bg-danger rounded-pill px-3 py-1 font-monospace"
                                    style="font-size: 11px;">
                                    <i class="bi bi-shield-lock-fill me-1"></i>
                                    {{ $user->admin?->cargo ?? 'Administrador' }}
                                </span>
                            @elseif ($user->isVendedor())
                                <span class="badge bg-success rounded-pill px-3 py-1 font-monospace"
                                    style="font-size: 11px;">
                                    <i class="bi bi-shop me-1"></i>
                                    {{ $user->vendedor?->nome_fantasia ?: 'Vendedor Parceiro' }}
                                </span>
                            @else
                                <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace"
                                    style="font-size: 11px;">
                                    <i class="bi bi-person-fill me-1"></i> Cliente Leitor
                                </span>
                            @endif

                            <span class="badge bg-secondary bg-opacity-50 rounded-pill px-2 py-1 text-capitalize"
                                style="font-size: 11px;">
                                Status: {{ $user->status }}
                            </span>
                        </div>

                        <p class="text-white-50 mb-0 small">
                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                            @if ($user->hasVerifiedEmail())
                                <span class="text-success ms-2"><i class="bi bi-patch-check-fill"></i> Verificado</span>
                            @else
                                <span class="text-warning ms-2"><i class="bi bi-exclamation-circle-fill"></i> Não
                                    verificado</span>
                            @endif
                        </p>
                    </div>

                    <!-- ATALHO DE LOGOUT -->
                    <div class="flex-shrink-0">
                        <form method="POST" action="{{ route('sair') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Sair da Conta
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ABAS DE NAVEGAÇÃO DO PAINEL CENTRALIZADO -->
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0 px-3 px-md-4" id="painelTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $tab === 'visao-geral' || !$tab ? 'active fw-bold text-primary' : 'text-secondary' }} py-3 px-3 border-bottom border-2"

                            id="visao-geral-tab" data-bs-toggle="tab" data-bs-target="#visao-geral" type="button"
                            role="tab">
                            <i class="bi bi-grid-1x2-fill me-1"></i> Visão Geral & Notificações
                            @if (!empty($notificacoes))
                                <span class="badge bg-danger rounded-pill ms-1">{{ count($notificacoes) }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $tab === 'perfil' ? 'active fw-bold text-primary' : 'text-secondary' }} py-3 px-3 border-bottom border-2"
                            id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button" role="tab">
                            <i class="bi bi-person-bounding-box me-1"></i> Meus Dados & Perfil
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $tab === 'enderecos' ? 'active fw-bold text-primary' : 'text-secondary' }} py-3 px-3 border-bottom border-2"
                            id="enderecos-tab" data-bs-toggle="tab" data-bs-target="#enderecos" type="button"
                            role="tab">
                            <i class="bi bi-geo-alt-fill me-1"></i> Meus Endereços
                            @if (isset($enderecos) && $enderecos->count() > 0)
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1">{{ $enderecos->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $tab === 'seguranca' ? 'active fw-bold text-primary' : 'text-secondary' }} py-3 px-3 border-bottom border-2"
                            id="seguranca-tab" data-bs-toggle="tab" data-bs-target="#seguranca" type="button"
                            role="tab">
                            <i class="bi bi-shield-lock me-1"></i> Segurança & Conta
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CONTEÚDO DAS ABAS -->
        <div class="tab-content" id="painelTabContent">

            <!-- ========================================================
                 ABA 1: VISÃO GERAL & NOTIFICAÇÕES RELEVANTES
                 ======================================================== -->
            <div class="tab-pane fade {{ $tab === 'visao-geral' || !$tab ? 'show active' : '' }}" id="visao-geral"
                role="tabpanel">

                <!-- 1. CARDS DE NOTIFICAÇÕES RELEVANTES -->
                @if (!empty($notificacoes))
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bell-fill text-warning"></i> Notificações do Sistema
                        </h5>
                        <div class="d-flex flex-column gap-3">
                            @foreach ($notificacoes as $notif)
                                <div
                                    class="alert alert-{{ $notif['tipo'] }} border-0 shadow-sm rounded-4 p-3 p-md-4 mb-0 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-circle bg-white p-2 text-{{ $notif['tipo'] }} shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center"
                                            style="width: 44px; height: 44px;">
                                            <i class="bi {{ $notif['icone'] }} fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $notif['titulo'] }}</h6>
                                            <p class="mb-0 small opacity-75">{{ $notif['mensagem'] }}</p>
                                        </div>
                                    </div>
                                    @if (!empty($notif['link']))
                                        <a href="{{ $notif['link'] }}"
                                            class="btn btn-{{ $notif['tipo'] }} btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap align-self-end align-self-md-center shadow-sm">
                                            {{ $notif['link_texto'] ?? 'Acessar' }} &rarr;
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. KPIS / ESTATÍSTICAS POR TIPO DE CONTA -->
                @if ($user->isAdmin())
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Total de Livros</span>
                                <h3 class="fw-bold text-primary mb-0 mt-1">{{ $kpis['total_livros'] ?? 0 }}</h3>
                                <a href="{{ route('admin.livros.index') }}"
                                    class="small text-decoration-none mt-1">Gerenciar catálogo &rarr;</a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Vendedores</span>
                                <h3 class="fw-bold text-success mb-0 mt-1">{{ $kpis['total_vendedores'] ?? 0 }}</h3>
                                <a href="{{ route('admin.vendedores.index') }}"
                                    class="small text-decoration-none mt-1">Ver lojas &rarr;</a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Clientes</span>
                                <h3 class="fw-bold text-info mb-0 mt-1">{{ $kpis['total_clientes'] ?? 0 }}</h3>
                                <a href="{{ route('admin.clientes.index') }}"
                                    class="small text-decoration-none mt-1">Ver clientes &rarr;</a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Solicitações Pendentes</span>
                                <h3 class="fw-bold text-warning mb-0 mt-1">{{ $kpis['vendedores_pendentes'] ?? 0 }}
                                </h3>
                                <a href="{{ route('admin.vendedores.index') }}"
                                    class="small text-decoration-none mt-1">Avaliar agora &rarr;</a>
                            </div>
                        </div>
                    </div>
                @elseif ($user->isVendedor())
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Meus Livros</span>
                                <h3 class="fw-bold text-primary mb-0 mt-1">{{ $kpis['total_livros'] ?? 0 }}</h3>
                                <a href="{{ route('vendedor.livros.index') }}"
                                    class="small text-decoration-none mt-1">Ver todos &rarr;</a>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Em Estoque</span>
                                <h3 class="fw-bold text-success mb-0 mt-1">{{ $kpis['em_estoque'] ?? 0 }}</h3>
                                <span class="small text-muted mt-1">Disponíveis</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Esgotados</span>
                                <h3 class="fw-bold text-danger mb-0 mt-1">{{ $kpis['esgotados'] ?? 0 }}</h3>
                                <span class="small text-muted mt-1">Repor estoque</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 3. INFORMAÇÕES DA CONTA & AÇÕES RÁPIDAS -->
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="bi bi-info-circle text-primary me-2"></i> Detalhes da Conta
                            </h5>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Nome Completo:</span>
                                    <strong class="text-dark">{{ $user->name }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">E-mail Cadastrado:</span>
                                    <strong class="text-dark">{{ $user->email }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Tipo de Perfil:</span>
                                    <strong class="text-capitalize">{{ $user->tipo }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Status da Conta:</span>
                                    <span class="badge bg-success text-capitalize">{{ $user->status }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Membro desde:</span>
                                    <strong>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Data não registrada' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="bi bi-lightning-charge text-warning me-2"></i> Ações Rápidas
                            </h5>
                            <div class="d-flex flex-column gap-2">
                                @if ($user->isAdmin())
                                    <a href="{{ route('admin.livros.create') }}"
                                        class="btn btn-primary d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-plus-circle me-2"></i> Publicar Novo Livro</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ route('admin.vendedores.index') }}"
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-shop me-2"></i> Gerenciar Vendedores</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ route('admin.administradores.index') }}"
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-shield-check me-2"></i> Gestão de Administradores</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                @elseif ($user->isVendedor())
                                    <a href="{{ route('vendedor.livros.create') }}"
                                        class="btn btn-success d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-plus-circle me-2"></i> Publicar Livro na Minha
                                            Loja</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ route('vendedor.livros.index') }}"
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-book me-2"></i> Meus Livros Publicados</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                @else
                                    <a href="{{ route('vendedor.solicitar') }}"
                                        class="btn btn-warning d-flex align-items-center justify-content-between rounded-3 py-2 px-3 fw-semibold text-dark">
                                        <span><i class="bi bi-shop me-2"></i> Quero Vender Meus Livros</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <a href="{{ url('/#catalogo') }}"
                                        class="btn btn-outline-primary d-flex align-items-center justify-content-between rounded-3 py-2 px-3">
                                        <span><i class="bi bi-book-half me-2"></i> Explorar Catálogo Completo</span>
                                        <i class="bi bi-chevron-right small"></i>
                                    </a>
                                @endif
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-between rounded-3 py-2 px-3 mt-1"
                                    onclick="document.getElementById('perfil-tab').click()">
                                    <span><i class="bi bi-pencil-square me-2"></i> Editar Dados do Perfil</span>
                                    <i class="bi bi-chevron-right small"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================
                 ABA 2: MEUS DADOS & PERFIL (FORMULÁRIO INTEGRADO)
                 ======================================================== -->
            <div class="tab-pane fade {{ $tab === 'perfil' ? 'show active' : '' }}" id="perfil" role="tabpanel">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">
                                <i class="bi bi-person-lines-fill text-primary me-2"></i> Atualizar Dados do Perfil
                            </h4>
                            <p class="text-muted small mb-0">Mantenha seus dados e fotos de identificação sempre
                                atualizados.</p>
                        </div>
                    </div>

                    @php
                        $updateRoute = match ($user->tipo) {
                            'admin' => route('admin.perfil.atualizar'),
                            'vendedor' => route('vendedor.perfil.atualizar'),
                            default => route('cliente.perfil.atualizar'),
                        };
                    @endphp

                    <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- 1. FOTO DE PERFIL COM PREVIEW -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Foto de
                                Perfil</label>
                            <div class="d-flex flex-column flex-sm-row align-items-start gap-3 p-3 bg-light rounded-4">
                                <img src="{{ $user->foto }}" id="avatarPreviewForm" alt="{{ $user->name }}"
                                    class="rounded-circle shadow-sm object-fit-cover flex-shrink-0"
                                    style="width: 72px; height: 72px; border: 2px solid #3b82f6;">

                                <div class="flex-grow-1">
                                    <input type="file" name="foto_perfil" id="foto_perfil"
                                        class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="window.previewImage(this, 'avatarPreviewForm')">
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                        Formatos aceitos: JPG, PNG, WEBP até 2MB.
                                    </small>

                                    @if ($user->foto_perfil)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remover_foto"
                                                value="1" id="removerFotoCheck">
                                            <label class="form-check-label small text-danger" for="removerFotoCheck">
                                                <i class="bi bi-trash me-1"></i> Remover foto atual (usar avatar
                                                inicial)
                                            </label>
                                        </div>
                                    @endif
                                    @error('foto_perfil')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 2. DADOS BÁSICOS (NOME, EMAIL, TELEFONE) -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label small fw-bold text-secondary">Nome Completo
                                    *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}"
                                    minlength="3" maxlength="100" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-bold text-secondary">E-mail *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($user->isCliente())
                                <div class="col-12 col-md-6">
                                    <label for="telefone" class="form-label small fw-bold text-secondary">Celular /
                                        WhatsApp *</label>
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                        id="telefone" name="telefone" data-mask="telefone" maxlength="15"
                                        value="{{ old('telefone', $user->cliente?->celular_contato) }}" required>
                                    @error('telefone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary">CPF Registrado</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->cliente?->cpf ?? 'Não informado' }}" readonly disabled>
                                    <small class="text-muted" style="font-size: 11px;">O CPF é fixo e vinculado à
                                        conta.</small>
                                </div>
                            @elseif ($user->isVendedor())
                                <div class="col-12 col-md-6">
                                    <label for="telefone_comercial"
                                        class="form-label small fw-bold text-secondary">Telefone / WhatsApp Comercial
                                        *</label>
                                    <input type="text"
                                        class="form-control @error('telefone_comercial') is-invalid @enderror"
                                        id="telefone_comercial" name="telefone_comercial" data-mask="telefone"
                                        maxlength="15"
                                        value="{{ old('telefone_comercial', $user->vendedor?->telefone_comercial) }}"
                                        required>
                                    @error('telefone_comercial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nome_fantasia" class="form-label small fw-bold text-secondary">Nome
                                        Fantasia da Loja *</label>
                                    <input type="text"
                                        class="form-control @error('nome_fantasia') is-invalid @enderror"
                                        id="nome_fantasia" name="nome_fantasia"
                                        value="{{ old('nome_fantasia', $user->vendedor?->nome_fantasia) }}" required>
                                    @error('nome_fantasia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary">CNPJ Registrado</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->vendedor?->cnpj ?? 'Não informado' }}" readonly disabled>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Razão Social</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->vendedor?->razao_social ?? 'Não informado' }}" readonly
                                        disabled>
                                </div>
                            @elseif ($user->isAdmin())
                                <div class="col-12 col-md-6">
                                    <label for="telefone_urgencia"
                                        class="form-label small fw-bold text-secondary">Telefone de Urgência *</label>
                                    <input type="text"
                                        class="form-control @error('telefone_urgencia') is-invalid @enderror"
                                        id="telefone_urgencia" name="telefone_urgencia" data-mask="telefone"
                                        maxlength="15"
                                        value="{{ old('telefone_urgencia', $user->admin?->telefone_urgencia) }}"
                                        required>
                                    @error('telefone_urgencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Cargo Administrativo</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->admin?->cargo ?? 'Administrador' }}" readonly disabled>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit"
                                class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- ========================================================
                 ABA: MEUS ENDEREÇOS
                 ======================================================== -->
            <div class="tab-pane fade {{ $tab === 'enderecos' ? 'show active' : '' }}" id="enderecos" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i> Meus Endereços de Entrega
                            </h5>
                            <p class="text-muted small mb-0">Gerencie seus endereços residenciais e comerciais com carregamento dinâmico via CEP.</p>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNovoEndereco">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Endereço
                        </button>
                    </div>

                    @if (session('status_endereco'))
                        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status_endereco') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($enderecos->isEmpty())
                        <div class="text-center py-5 bg-light rounded-4">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex p-3 text-muted mb-3">
                                <i class="bi bi-geo-alt display-6 text-primary"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Nenhum endereço cadastrado</h6>
                            <p class="text-muted small mb-3">Adicione seus endereços para agilizar o fechamento de pedidos.</p>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNovoEndereco">
                                Cadastrar Endereço
                            </button>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach ($enderecos as $end)
                                <div class="col-12 col-md-6">
                                    <div class="card h-100 border {{ $end->principal ? 'border-primary border-2 shadow-sm bg-primary bg-opacity-10' : 'border-light-subtle shadow-xs' }} rounded-4 p-4 position-relative">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $end->tipo === 'comercial' ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} rounded-pill text-capitalize px-3 py-1 fw-semibold">
                                                    <i class="bi {{ $end->tipo === 'comercial' ? 'bi-building' : 'bi-house-door' }} me-1"></i>
                                                    {{ $end->tipo ?? 'Residencial' }}
                                                </span>
                                                @if ($end->principal)
                                                    <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 11px;">
                                                        <i class="bi bi-star-fill me-1"></i> Principal
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border-0 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                    @if (!$end->principal)
                                                        <li>
                                                            <form action="{{ route('enderecos.definir-principal', $end) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item small py-2">
                                                                    <i class="bi bi-star me-2 text-warning"></i> Definir como Principal
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('enderecos.deletar', $end) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este endereço?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item small py-2 text-danger">
                                                                <i class="bi bi-trash me-2"></i> Excluir Endereço
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-1">
                                            {{ $end->rua }}, {{ $end->numero }}
                                            @if ($end->complemento)
                                                <span class="fw-normal text-muted">({{ $end->complemento }})</span>
                                            @endif
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            {{ $end->bairro }} &bull; {{ $end->cidade }} - {{ $end->estado }}
                                        </p>
                                        <p class="small text-secondary mb-0">
                                            <strong>CEP:</strong> {{ $end->cep }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========================================================
                 ABA 3: SEGURANÇA & CONTA
                 ======================================================== -->
            <div class="tab-pane fade {{ $tab === 'seguranca' ? 'show active' : '' }}" id="seguranca"
                role="tabpanel">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="bi bi-key-fill text-warning me-2"></i> Senha de Acesso
                    </h5>
                    <p class="text-muted small mb-4">Atualize sua senha periodicamente para manter sua conta protegida.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}" class="mb-2">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label for="current_password" class="form-label small fw-bold text-secondary">Senha
                                    Atual</label>
                                <input type="password"
                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                    id="current_password" name="current_password" autocomplete="current-password">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password" class="form-label small fw-bold text-secondary">Nova
                                    Senha</label>
                                <input type="password"
                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                    id="password" name="password" autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password_confirmation"
                                    class="form-label small fw-bold text-secondary">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="bi bi-shield-check me-1"></i> Atualizar Senha
                            </button>
                        </div>
                    </form>
                </div>

                <!-- EXCLUSÃO DE CONTA -->
                <div
                    class="card border-danger border-opacity-25 shadow-sm rounded-4 p-4 p-md-5 bg-danger bg-opacity-10">
                    <h5 class="fw-bold text-danger mb-2">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i> Zona de Risco: Excluir Conta
                    </h5>
                    <p class="text-secondary small mb-4">
                        Ao excluir sua conta, todos os seus dados e acessos serão removidos permanentemente. Esta ação
                        não poderá ser desfeita.
                    </p>

                    @php
                        $deleteRoute = match ($user->tipo) {
                            'admin' => route('admin.perfil.deletar'),
                            'vendedor' => route('vendedor.perfil.deletar'),
                            default => route('cliente.perfil.deletar'),
                        };
                    @endphp

                    <form method="POST" action="{{ $deleteRoute }}">
                        @csrf
                        @method('DELETE')

                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6">
                                <label for="delete_password" class="form-label small fw-bold text-danger">Confirme sua
                                    senha atual para prosseguir</label>
                                <input type="password"
                                    class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                    id="delete_password" name="password" placeholder="Digite sua senha" required>
                                @error('password', 'userDeletion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold"
                                    onclick="return confirm('Tem certeza absoluta que deseja excluir permanentemente sua conta?')">
                                    <i class="bi bi-trash-fill me-1"></i> Excluir Minha Conta Definitivamente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>

    <!-- MODAL NOVO ENDEREÇO COM INTEGRAÇÃO VIACEP -->
    <div class="modal fade" id="modalNovoEndereco" tabindex="-1" aria-labelledby="modalNovoEnderecoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-primary text-white p-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                        <h5 class="modal-title fw-bold mb-0" id="modalNovoEnderecoLabel">Adicionar Novo Endereço</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="{{ route('enderecos.salvar') }}" method="POST" data-viacep-container>
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Tipo de Endereço -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Tipo de Endereço *</label>
                                <select name="tipo" class="form-select rounded-3" required>
                                    <option value="residencial">Residencial (Casa / Apartamento)</option>
                                    <option value="comercial">Comercial (Trabalho / Empresa)</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>

                            <!-- CEP com busca automática ViaCEP -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">CEP *</label>
                                <div class="input-group">
                                    <input type="text" name="cep" class="form-control rounded-start-3"
                                        placeholder="00000-000" data-viacep="cep" data-mask="cep" maxlength="9" required>
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="bi bi-search"></i>
                                    </span>
                                </div>
                                <small class="d-block mt-1" data-viacep="mensagem" style="font-size: 11.5px;"></small>
                            </div>

                            <!-- Rua / Logradouro -->
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-bold text-secondary">Rua / Logradouro *</label>
                                <input type="text" name="rua" class="form-control rounded-3" placeholder="Av. Paulista, Rua das Flores..." data-viacep="rua" required>
                            </div>

                            <!-- Número -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Número *</label>
                                <input type="text" name="numero" class="form-control rounded-3" placeholder="123 ou S/N" data-viacep="numero" required>
                            </div>

                            <!-- Complemento -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Complemento (Opcional)</label>
                                <input type="text" name="complemento" class="form-control rounded-3" placeholder="Apto 42, Bloco B...">
                            </div>

                            <!-- Bairro -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Bairro *</label>
                                <input type="text" name="bairro" class="form-control rounded-3" placeholder="Centro, Jardins..." data-viacep="bairro" required>
                            </div>

                            <!-- Cidade -->
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Cidade *</label>
                                <input type="text" name="cidade" class="form-control rounded-3" placeholder="São Paulo" data-viacep="cidade" required>
                            </div>

                            <!-- UF / Estado -->
                            <div class="col-12 col-md-1">
                                <label class="form-label small fw-bold text-secondary">UF *</label>
                                <input type="text" name="estado" class="form-control rounded-3 text-uppercase text-center" placeholder="SP" maxlength="2" data-viacep="estado" required>
                            </div>

                            <!-- Tornar Principal -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="principal" value="1" id="switchPrincipal" {{ $enderecos->isEmpty() ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="switchPrincipal">
                                        Definir como endereço de entrega principal
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Salvar Endereço
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.principal>
