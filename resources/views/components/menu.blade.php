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
                    <a href="{{ url('/#catalogo') }}" class="menu-item-link">
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
                        <li><a href="{{ url('/?categoria=Ficção#catalogo') }}"><span>Ficção</span></a></li>
                        <li><a href="{{ url('/?categoria=Aventura#catalogo') }}"><span>Aventura</span></a></li>
                        <li><a href="{{ url('/?categoria=Terror#catalogo') }}"><span>Terror</span></a></li>
                        <li><a href="{{ url('/?categoria=Romance#catalogo') }}"><span>Romance</span></a></li>
                        <li><a href="{{ url('/?categoria=Fantasia#catalogo') }}"><span>Fantasia</span></a></li>
                        <li><a href="{{ url('/?categoria=Infantil#catalogo') }}"><span>Infantil</span></a></li>
                        <li><a href="{{ url('/?categoria=Biografia#catalogo') }}"><span>Biografia</span></a></li>
                        <li><a href="{{ url('/?categoria=Autoajuda#catalogo') }}"><span>Autoajuda</span></a></li>
                        <li><a href="{{ url('/?categoria=História#catalogo') }}"><span>História</span></a></li>
                        <li><a href="{{ url('/?categoria=Ciência#catalogo') }}"><span>Ciência</span></a></li>
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

                <!-- Itens para Usuários Logados -->
                @auth
                    @php
                        $notifsMenu = \App\Http\Controllers\Painel\PainelController::obterNotificacoes(Auth::user());
                        $qtdNotifsMenu = count($notifsMenu);
                    @endphp
                    <li
                        class="menu-item {{ request()->is('painel*') && request('tab') === 'visao-geral' ? 'active' : '' }}">
                        <a href="{{ route('painel', ['tab' => 'visao-geral']) }}" class="menu-item-link">
                            <div class="menu-item-header">
                                <i data-lucide="bell"></i>
                                <span>Notificações</span>
                                @if ($qtdNotifsMenu > 0)
                                    <span class="menu-badge badge-count bg-danger text-white">{{ $qtdNotifsMenu }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('painel*') && request('tab') === 'enderecos' ? 'active' : '' }}">
                        <a href="{{ route('painel', ['tab' => 'enderecos']) }}" class="menu-item-link">
                            <div class="menu-item-header">
                                <i data-lucide="map-pin"></i>
                                <span>Meus Endereços</span>
                            </div>
                        </a>
                    </li>
                @endauth

                <!-- Área do Vendedor / Seja um Vendedor -->
                @guest
                    <li class="menu-item {{ request()->routeIs('vendedor.solicitar') ? 'active' : '' }}">
                        <a href="{{ route('vendedor.solicitar') }}" class="menu-item-link">
                            <div class="menu-item-header">
                                <i data-lucide="store"></i>
                                <span>Seja um Vendedor</span>
                                <span class="menu-badge badge-sale">Vender</span>
                            </div>
                        </a>
                    </li>
                @else
                    @if (Auth::user()->isCliente())
                        <li class="menu-item {{ request()->routeIs('vendedor.solicitar') ? 'active' : '' }}">
                            <a href="{{ route('vendedor.solicitar') }}" class="menu-item-link">
                                <div class="menu-item-header">
                                    <i data-lucide="store"></i>
                                    <span>Quero Vender</span>
                                    <span class="menu-badge badge-sale">Parceria</span>
                                </div>
                            </a>
                        </li>
                    @endif
                @endguest

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
                    <a href="{{ route('painel') }}"
                        class="btn btn-outline-light w-100 mb-2 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-decoration-none font-monospace small">
                        <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                        <span>Meu Painel & Perfil</span>
                    </a>

                    <form method="POST" action="{{ route('sair') }}" class="w-100">
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
