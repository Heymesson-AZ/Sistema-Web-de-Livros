@auth
    <div class="dropdown user-dropdown-container">
        <button class="user-pill-btn dropdown-toggle border-0" type="button" id="userMenuDropdown" data-bs-toggle="dropdown"
            aria-expanded="false">
            <img src="{{ Auth::user()->foto }}" alt="{{ Auth::user()->name }}" class="user-avatar-img">
            <span class="user-name-label d-none d-md-inline">{{ Auth::user()->name }}</span>
            <i data-lucide="chevron-down" class="dropdown-chevron-icon d-none d-md-inline"
                style="width: 14px; height: 14px;"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu shadow-lg border-0"
            aria-labelledby="userMenuDropdown">
            <!-- Cabeçalho com dados e avatar do usuário -->
            <li class="user-dropdown-header p-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ Auth::user()->foto }}" alt="{{ Auth::user()->name }}" class="user-dropdown-avatar">
                    <div class="user-dropdown-details overflow-hidden">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"
                            title="{{ Auth::user()->name }}">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-muted small text-truncate" style="max-width: 180px;"
                            title="{{ Auth::user()->email }}">
                            {{ Auth::user()->email }}
                        </div>
                        <div class="mt-1 d-flex flex-wrap gap-1">
                            @if (Auth::user()->isAdmin())
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0"
                                    style="font-size: 11px;">
                                    <i class="bi bi-shield-lock-fill me-1"></i>
                                    {{ Auth::user()->admin?->cargo ?? 'Administrador' }}
                                </span>
                            @elseif (Auth::user()->isVendedor())
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0"
                                    style="font-size: 11px;">
                                    <i class="bi bi-shop me-1"></i>
                                    {{ Auth::user()->vendedor?->nome_fantasia ?: 'Vendedor' }}
                                </span>
                                @if (Auth::user()->vendedor?->status_aprovacao === 'pendente')
                                    <span
                                        class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0"
                                        style="font-size: 10px;">
                                        Pendente
                                    </span>
                                @endif
                            @else
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0"
                                    style="font-size: 11px;">
                                    <i class="bi bi-person-fill me-1"></i> Cliente
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </li>

            <!-- Links Principais -->
            <li class="pt-2">
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('dashboard') }}">
                    <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                    <span>Painel Principal</span>
                </a>
            </li>

            @php
                $perfilRoute = match (Auth::user()->tipo) {
                    'cliente' => route('cliente.perfil.editar'),
                    'vendedor' => route('vendedor.perfil.editar'),
                    'admin' => route('admin.perfil.editar'),
                    default => route('dashboard'),
                };
            @endphp
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ $perfilRoute }}">
                    <i data-lucide="user-cog" style="width: 16px; height: 16px;"></i>
                    <span>Meu Perfil</span>
                </a>
            </li>

            <!-- Ações por Tipo de Usuário -->
            @if (Auth::user()->isAdmin())
                <li>
                    <hr class="dropdown-divider my-2">
                </li>
                <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1"
                    style="font-size: 10px; letter-spacing: 0.5px;">
                    Administração
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.livros.index') }}">
                        <i data-lucide="book-marked" style="width: 16px; height: 16px;"></i>
                        <span>Catálogo de Livros</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('admin.administradores.index') }}">
                        <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                        <span>Administradores</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('admin.vendedores.index') }}">
                        <i data-lucide="store" style="width: 16px; height: 16px;"></i>
                        <span>Vendedores</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('admin.clientes.index') }}">
                        <i data-lucide="users" style="width: 16px; height: 16px;"></i>
                        <span>Clientes</span>
                    </a>
                </li>
            @elseif (Auth::user()->isVendedor())
                <li>
                    <hr class="dropdown-divider my-2">
                </li>
                <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1"
                    style="font-size: 10px; letter-spacing: 0.5px;">
                    Minha Loja
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('vendedor.painel') }}">
                        <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                        <span>Painel da Loja</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('vendedor.livros.index') }}">
                        <i data-lucide="book-marked" style="width: 16px; height: 16px;"></i>
                        <span>Meus Livros</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('vendedor.livros.create') }}">
                        <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                        <span>Publicar Livro</span>
                    </a>
                </li>
            @elseif (Auth::user()->isCliente())
                <li>
                    <hr class="dropdown-divider my-2">
                </li>
                <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1"
                    style="font-size: 10px; letter-spacing: 0.5px;">
                    Parceria
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-warning-emphasis"
                        href="{{ route('vendedor.solicitar') }}">
                        <i data-lucide="store" style="width: 16px; height: 16px;"></i>
                        <span>Quero Vender</span>
                    </a>
                </li>
            @endif

            <li>
                <hr class="dropdown-divider my-2">
            </li>

            <!-- Botão Sair da Conta -->
            <li class="pb-1">
                <form method="POST" action="{{ route('sair') }}">
                    @csrf
                    <button type="submit"
                        class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger w-100 border-0 bg-transparent">
                        <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                        <span>Sair da Conta</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
@endauth
