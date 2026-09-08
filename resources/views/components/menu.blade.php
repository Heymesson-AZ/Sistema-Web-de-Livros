<div class="menu-wrapper" id="menuWrapper">
    <div id="menu">
        <div class="menu-inner">

            <!-- Cabeçalho visível no Drawer Mobile -->
            <div class="menu-mobile-header d-lg-none">
                <a href="{{ url('/') }}" class="brand-link text-decoration-none" title="Universo de Papel">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Universo de Papel" class="brand-logo-img">
                </a>
                <button type="button" class="menu-close-btn" id="menuCloseBtn" aria-label="Fechar menu">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <!-- Lista Principal de Navegação -->
            <ul class="menu-list">

                <!-- Início -->
                <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}" class="menu-item-link">
                        <div class="menu-item-header">
                            <i data-lucide="house"></i>
                            <span>Início</span>
                        </div>
                    </a>
                </li>

                <!-- Catálogo -->
                <li class="menu-item">
                    <a href="#" class="menu-item-link">
                        <div class="menu-item-header">
                            <i data-lucide="book-open"></i>
                            <span>Catálogo</span>
                        </div>
                    </a>
                </li>

                <!-- Categorias com Submenu -->
                <li class="menu-item has-submenu">
                    <div class="menu-item-header">
                        <i data-lucide="tag"></i>
                        <span>Categorias</span>
                        <i class="submenu-arrow" data-lucide="chevron-down"></i>
                    </div>

                    <ul class="submenu">
                        <li><span>Ficção</span></li>
                        <li><span>Aventura</span></li>
                        <li><span>Terror</span></li>
                        <li><span>Romance</span></li>
                        <li><span>Fantasia</span></li>
                        <li><span>Infantil</span></li>
                        <li><span>Biografia</span></li>
                        <li><span>Autoajuda</span></li>
                        <li><span>História</span></li>
                        <li><span>Ciência</span></li>
                        <li><span>Religião</span></li>
                        <li><span>Mangá</span></li>
                    </ul>
                </li>

                <!-- Promoções com Badge -->
                <li class="menu-item">
                    <a href="#" class="menu-item-link">
                        <div class="menu-item-header">
                            <i data-lucide="badge-percent"></i>
                            <span>Promoções</span>
                            <span class="menu-badge badge-sale">Ofertas</span>
                        </div>
                    </a>
                </li>

                <!-- Carrinho -->
                <li class="menu-item">
                    <a href="#" class="menu-item-link">
                        <div class="menu-item-header">
                            <i data-lucide="shopping-cart"></i>
                            <span>Carrinho</span>
                            <span class="menu-badge badge-count">0</span>
                        </div>
                    </a>
                </li>

                <!-- Conta do Usuário -->
                <li
                    class="menu-item has-submenu {{ request()->routeIs('dashboard', 'cliente.perfil.*', 'vendedor.perfil.*', 'admin.perfil.*') ? 'active' : '' }}">
                    <div class="menu-item-header">
                        <i data-lucide="user"></i>
                        <span>{{ Auth::check() ? Auth::user()->name : 'Minha Conta' }}</span>
                        <i class="submenu-arrow" data-lucide="chevron-down"></i>
                    </div>

                    <ul class="submenu">
                        @guest
                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <div class="menu-item-header">
                                        <i data-lucide="log-in"></i>
                                        <span>Entrar</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    <div class="menu-item-header">
                                        <i data-lucide="user-plus"></i>
                                        <span>Cadastrar</span>
                                    </div>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}">
                                    <div class="menu-item-header">
                                        <i data-lucide="layout-dashboard"></i>
                                        <span>Painel</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                @php
                                    $perfilRoute = match (Auth::user()->tipo) {
                                        'cliente' => route('cliente.perfil.editar'),
                                        'vendedor' => route('vendedor.perfil.editar'),
                                        'admin' => route('admin.perfil.editar'),
                                        default => route('dashboard'),
                                    };
                                @endphp
                                <a href="{{ $perfilRoute }}">
                                    <div class="menu-item-header">
                                        <i data-lucide="user-check"></i>
                                        <span>Meu Perfil</span>
                                    </div>
                                </a>
                            </li>
                        @endguest
                    </ul>
                </li>

                @auth
                    @if (Auth::user()->isAdmin())
                        <!-- Administração (Apenas Admin) -->
                        <li
                            class="menu-item has-submenu {{ request()->routeIs('admin.administradores.*') ? 'active' : '' }}">
                            <div class="menu-item-header">
                                <i data-lucide="shield-check"></i>
                                <span>Administração</span>
                                <i class="submenu-arrow" data-lucide="chevron-down"></i>
                            </div>

                            <ul class="submenu">
                                <li>
                                    <a href="{{ route('admin.administradores.index') }}">
                                        <div class="menu-item-header">
                                            <i data-lucide="users"></i>
                                            <span>Administradores</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.administradores.create') }}">
                                        <div class="menu-item-header">
                                            <i data-lucide="user-plus"></i>
                                            <span>Novo Admin</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endauth

                <!-- Contato -->
                <li class="menu-item">
                    <a href="#" class="menu-item-link">
                        <div class="menu-item-header">
                            <i data-lucide="mail"></i>
                            <span>Contato</span>
                        </div>
                    </a>
                </li>

            </ul>

            <!-- RODAPÉ DO MENU -->
            <div class="menu-footer">
                <div class="social-icons">
                    <a href="#" aria-label="Instagram" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" aria-label="Facebook" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" aria-label="X" title="X (Twitter)">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="#" aria-label="Threads" title="Threads">
                        <i class="bi bi-threads"></i>
                    </a>
                </div>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button class="logout-btn" type="submit">
                            <i data-lucide="log-out"></i>
                            <span>Sair da Conta</span>
                        </button>
                    </form>
                @else
                    <button class="login-quick-btn" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i data-lucide="log-in"></i>
                        <span>Acessar Conta</span>
                    </button>
                @endauth
            </div>

        </div>
    </div>
</div>
