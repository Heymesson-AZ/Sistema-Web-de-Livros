<div class="menu-wrapper">
    <div id="menu">
        <div class="menu-inner">

            <ul class="menu-list">

                <li class="menu-item">
                    <div class="menu-item-header">
                        <i data-lucide="house"></i>
                        <span>Início</span>
                    </div>
                </li>

                <li class="menu-item">
                    <div class="menu-item-header">
                        <i data-lucide="book-open"></i>
                        <span>Catálogo</span>
                    </div>
                </li>

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

                <li class="menu-item">
                    <div class="menu-item-header">
                        <i data-lucide="badge-percent"></i>
                        <span>Promoções</span>
                    </div>
                </li>

                <li class="menu-item">
                    <div class="menu-item-header">
                        <i data-lucide="shopping-cart"></i>
                        <span>Carrinho</span>
                    </div>
                </li>

                <li class="menu-item has-submenu">
                    <div class="menu-item-header">
                        <i data-lucide="user"></i>
                        <span>Conta</span>
                        <i class="submenu-arrow" data-lucide="chevron-down"></i>
                    </div>

                    <ul class="submenu">
                        <li>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <div class="menu-item-header">
                                    <i data-lucide="log-in"></i>
                                    <span>Login</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <div class="menu-item-header">
                                    <i data-lucide="user-plus"></i>
                                    <span>Criar Conta</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-item">
                    <div class="menu-item-header">
                        <i data-lucide="mail"></i>
                        <span>Contato</span>
                    </div>
                </li>

            </ul>

            <!-- RODAPÉ -->
            <div class="menu-footer">
                <div class="social-icons">
                    {{-- Ícones de redes sociais --}}
                    <a href="#" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    {{-- X, antigo twitter --}}
                    <a href="#" aria-label="X">
                        <i class="bi bi-x" style="font-size: 2rem;"></i>
                    </a>
                    {{-- theads --}}
                    <a href="#" aria-label="Threads">
                        <i class="bi bi-threads"></i>
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">
                        <i class="log-out" data-lucide="log-out"></i>
                        <span>Sair</span>
                    </button>
                </form>

            </div>
            {{-- fim do rodapé --}}
        </div>
        {{-- fim da div menu inner --}}
    </div>
    {{-- fim da div menu com id= menu --}}
</div>
{{-- fim do componente menu --}}
