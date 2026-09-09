<footer class="site-footer mt-auto bg-dark text-white border-top border-secondary border-opacity-25 py-3 py-md-4">
    <div class="container">
        <div class="row align-items-center gy-3">
            <!-- Marca & Slogan Conciso -->
            <div class="col-12 col-md-4 text-center text-md-start">
                <div class="d-inline-flex align-items-center gap-2 mb-1">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Universo de Papel"
                        style="height: 28px; width: auto;" onerror="this.style.display='none'">
                    <span class="fs-5 fw-bold text-white tracking-wide">Universo de Papel</span>
                </div>
                <p class="text-light text-opacity-50 small mb-0 lh-sm">
                    Plataforma completa de livros, leitores e livreiros parceiros.
                </p>
            </div>

            <!-- Links Rápidos e Fluidos -->
            <div class="col-12 col-md-4 text-center">
                <ul class="list-inline mb-0 small">
                    <li class="list-inline-item mx-2">
                        <a href="{{ url('/') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Início</a>
                    </li>
                    <li class="list-inline-item mx-2">
                        <a href="{{ url('/#catalogo') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Catálogo</a>
                    </li>
                    <li class="list-inline-item mx-2">
                        <a href="{{ route('carrinho.index') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Carrinho</a>
                    </li>
                    <li class="list-inline-item mx-2">
                        <a href="{{ route('vendedor.solicitar') }}"
                            class="text-light text-opacity-75 text-decoration-none hover-white">Venda Conosco</a>
                    </li>
                </ul>
            </div>

            <!-- Créditos & Desenvolvedor em Linha Única (Sem Redundância) -->
            <div class="col-12 col-md-4 text-center text-md-end">
                <div
                    class="small text-light text-opacity-75 d-flex flex-wrap justify-content-center justify-content-md-end align-items-center gap-2">
                    <span>&copy; {{ date('Y') }} <strong>Universo de Papel</strong></span>
                    <span class="text-secondary">•</span>
                    <span>Dev: <strong class="text-white">Heymesson Azevedo</strong></span>
                    <span class="text-secondary">•</span>
                    <a href="https://github.com/Heymesson-AZ" target="_blank" rel="noopener"
                        class="text-light text-opacity-75 text-decoration-none hover-white d-inline-flex align-items-center gap-1"
                        title="GitHub Heymesson Azevedo">
                        <i class="bi bi-github"></i>
                    </a>
                </div>
                <div class="text-light text-opacity-50 small mt-1" style="font-size: 11px;">
                    {{ now()->translatedFormat('d \d\e F \d\e Y') }}
                </div>
            </div>
        </div>
    </div>
</footer>
