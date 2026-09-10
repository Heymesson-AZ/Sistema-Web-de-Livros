<x-layouts.principal>
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning p-4 mb-3"
                            style="width: 88px; height: 88px;">
                            <i class="bi bi-clock-history fs-1 text-warning"></i>
                        </div>
                        <h1 class="h3 fw-bold text-dark mb-2">Página Expirada</h1>
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill small mb-3">Erro 419
                            - Sessão Inativa</span>
                        <p class="text-muted small mb-4">
                            Por razões de segurança e proteção dos seus dados, sua sessão expirou após um período de
                            inatividade ou alteração no navegador. Basta recarregar a página para gerar uma nova chave
                            segura.
                        </p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <button type="button" onclick="window.location.reload();"
                            class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                            <span>Atualizar Página</span>
                        </button>
                        <a href="{{ url('/') }}"
                            class="btn btn-outline-secondary rounded-3 px-4 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-house-door fs-5"></i>
                            <span>Voltar ao Início</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.principal>
