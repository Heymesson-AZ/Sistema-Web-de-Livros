<x-layouts.visitante>
    <div class="container py-4 py-md-5">

        <!-- HERO BANNER -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 text-white"
            style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-lg-7">
                        <span class="badge bg-primary bg-opacity-25 text-primary-emphasis border border-primary border-opacity-25 px-3 py-2 rounded-pill mb-3">
                            <i class="bi bi-book-half me-1"></i> O seu universo literário online
                        </span>
                        <h1 class="display-5 fw-bold text-white mb-3">Milhares de livros novos, usados e raros esperando por você.</h1>
                        <p class="lead text-light text-opacity-75 mb-4" style="font-size: 1.1rem;">
                            Conectamos leitores apaixonados a sebos, livrarias e vendedores de todo o Brasil com compra segura e entrega rápida.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm fw-semibold">
                                <i class="bi bi-compass me-1"></i> Explorar Catálogo
                            </a>
                            <a href="{{ route('vendedor.solicitar') }}" class="btn btn-warning btn-lg rounded-pill px-4 shadow-sm fw-semibold text-dark">
                                <i class="bi bi-shop me-1"></i> Quero Vender Meus Livros
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 text-center d-none d-lg-block">
                        <div class="p-4 bg-white bg-opacity-10 rounded-4 backdrop-blur shadow-sm">
                            <i class="bi bi-journal-bookmark-fill text-warning" style="font-size: 80px;"></i>
                            <h4 class="fw-bold mt-2 mb-1">Universo de Papel</h4>
                            <p class="text-light text-opacity-75 small mb-0">Marketplace colaborativo de livrarias e sebos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANNER DE PARCERIA / SEJA UM VENDEDOR -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5"
            style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0 !important;">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success px-3 py-2 rounded-pill font-monospace" style="font-size: 11px;">
                                <i class="bi bi-stars me-1"></i> Programa de Parceiros
                            </span>
                        </div>
                        <h2 class="fw-bold text-dark mb-2">Tem uma livraria, sebo ou quer vender seus livros?</h2>
                        <p class="text-secondary mb-3" style="font-size: 16px;">
                            Junte-se à <strong>Universo de Papel</strong>. Crie sua loja online, publique seus livros, gerencie seus pedidos e comece a vender para leitores de todo o país. O cadastro é gratuito e a aprovação é rápida!
                        </p>
                        <div class="row g-3 pt-2">
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Zero Mensalidade Fixa</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Painel de Gestão Completo</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Pagamento Seguro e Rápido</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end text-center">
                        <a href="{{ route('vendedor.solicitar') }}" class="btn btn-success btn-lg rounded-pill px-4 py-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-shop fs-5"></i>
                            <span>Abrir Minha Loja Agora</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.visitante>
