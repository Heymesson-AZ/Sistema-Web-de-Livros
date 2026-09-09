<footer class="site-footer mt-auto bg-dark text-white border-top border-secondary border-opacity-25 pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Coluna 1: Sobre a Plataforma -->
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Universo de Papel"
                        style="height: 38px; width: auto;" onerror="this.style.display='none'">
                    <span class="fs-4 fw-bold text-white">Universo de Papel</span>
                </div>
                <p class="text-light text-opacity-75 small mb-3 lh-base">
                    Sua plataforma completa para descobrir, comprar e vender livros.
                    Conectando leitores, livreiros e sebos por todo o Brasil com segurança e paixão pela leitura.
                </p>
                <div class="d-flex align-items-center gap-2 text-warning small">
                    <i class="bi bi-shield-check fs-5"></i>
                    <span>Compra 100% segura & envio para todo o país</span>
                </div>
            </div>

            <!-- Coluna 2: Navegação Rápida -->
            <div class="col-6 col-md-2 offset-md-1">
                <h6 class="text-white fw-bold text-uppercase small mb-3">Navegação</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li>
                        <a href="{{ url('/') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">
                            <i class="bi bi-chevron-right me-1 text-secondary" style="font-size: 11px;"></i>Início
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#catalogo') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">
                            <i class="bi bi-chevron-right me-1 text-secondary" style="font-size: 11px;"></i>Catálogo de
                            Livros
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vendedor.solicitar') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">
                            <i class="bi bi-chevron-right me-1 text-secondary" style="font-size: 11px;"></i>Venda
                            Conosco
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Coluna 3: Gêneros em Destaque -->
            <div class="col-6 col-md-2">
                <h6 class="text-white fw-bold text-uppercase small mb-3">Categorias</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li><a href="{{ url('/?categoria=Ficção') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Ficção</a></li>
                    <li><a href="{{ url('/?categoria=Romance') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Romance</a></li>
                    <li><a href="{{ url('/?categoria=Fantasia') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Fantasia</a></li>
                    <li><a href="{{ url('/?categoria=Tecnologia') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Tecnologia</a></li>
                    <li><a href="{{ url('/?categoria=História') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">História</a></li>
                </ul>
            </div>

            <!-- Coluna 4: Desenvolvedor -->
            <div class="col-12 col-md-3">
                <h6 class="text-white fw-bold text-uppercase small mb-3">Desenvolvimento</h6>
                <div class="p-3 rounded-3 bg-secondary bg-opacity-10 border border-secondary border-opacity-25">
                    <p class="small text-light text-opacity-75 mb-2">
                        Plataforma desenvolvida com excelência técnica por:
                    </p>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                            style="width: 34px; height: 34px; font-size: 13px;">
                            HA
                        </div>
                        <div>
                            <span class="fw-bold text-white d-block" style="font-size: 14px;">Heymesson Azevedo</span>
                            <span class="text-light text-opacity-50 small">Desenvolvedor</span>
                        </div>
                    </div>
                    <div
                        class="d-flex align-items-center gap-3 mt-2 pt-2 border-top border-secondary border-opacity-25">
                        <a href="https://github.com/Heymesson-AZ" target="_blank" rel="noopener"
                            class="text-light text-opacity-75 text-decoration-none hover-white d-flex align-items-center gap-1 small">
                            <i class="bi bi-github"></i> GitHub
                        </a>
                        <span class="text-secondary">•</span>
                        <span class="text-light text-opacity-50 small">{{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary border-opacity-25 my-4">

        <!-- Linha Inferior: Direitos Autorais & Data Atual -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-light text-opacity-75 small text-center text-md-start">
            <div>
                <span>&copy; {{ date('Y') }} <strong>Universo de Papel</strong>. Todos os direitos
                    reservados.</span>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 gap-md-3">
                <span>Desenvolvido por: <strong class="text-white">Heymesson Azevedo</strong></span>
                <span class="d-none d-md-inline">•</span>
                <span>{{ now()->translatedFormat('d \d\e F \d\e Y') }}</span>
            </div>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: #0f172a !important;
        margin-left: var(--menu-width-closed, 74px);
    }

    @media (max-width: 991.98px) {
        .site-footer {
            margin-left: 0 !important;
        }
    }

    .site-footer .hover-white:hover {
        color: #fff !important;
        text-decoration: underline !important;
    }
</style>
