<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Minha Loja de Vendedor</li>
            </ol>
        </nav>

        @if (session('status') === 'solicitacao-enviada')
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Solicitação enviada com sucesso!</h5>
                    <p class="mb-0 small text-secondary">
                        Sua solicitação para atuar como vendedor na Universo de Papel foi registrada. Acompanhe abaixo o status do processo de aprovação.
                    </p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- CABEÇALHO DA LOJA -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                            style="width: 64px; height: 64px; background: linear-gradient(135deg, #0d9488 0%, #059669 100%); font-size: 24px;">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1">{{ $vendedor->nome_fantasia }}</h3>
                            <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                                <span><i class="bi bi-building me-1"></i>{{ $vendedor->razao_social }}</span>
                                <span>•</span>
                                <span><i class="bi bi-card-text me-1"></i>CNPJ: {{ $vendedor->cnpj }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- BADGE DE STATUS -->
                    <div>
                        @if ($vendedor->isPendente())
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 fs-6 shadow-sm">
                                <i class="bi bi-hourglass-split"></i> Em Análise
                            </span>
                        @elseif ($vendedor->isAprovado())
                            <span class="badge bg-success px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 fs-6 shadow-sm">
                                <i class="bi bi-patch-check-fill"></i> Loja Aprovada
                            </span>
                        @elseif ($vendedor->isRejeitado())
                            <span class="badge bg-danger px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 fs-6 shadow-sm">
                                <i class="bi bi-x-circle-fill"></i> Solicitação Recusada
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCO CONDICIONAL CONFORME O STATUS -->

        <!-- 1. CASO PENDENTE -->
        @if ($vendedor->isPendente())
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-warning border-5">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 50px; height: 50px; font-size: 24px;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Sua solicitação de vendedor está em análise</h4>
                            <p class="text-muted mb-0">
                                Nossa equipe administrativa está validando seus dados cadastrais e fiscais. O prazo médio de análise é de <strong>24 a 48 horas úteis</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- LINHA DO TEMPO VISUAL DO PROCESSO -->
                    <div class="row g-3 py-3 px-2 bg-light rounded-4 mb-4">
                        <div class="col-12 col-md-4 text-center text-md-start">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">1. Cadastro Enviado</div>
                                    <div class="text-muted" style="font-size: 12px;">Dados recebidos no sistema</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 text-center text-md-start">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-warning small">2. Análise da Moderação</div>
                                    <div class="text-muted" style="font-size: 12px;">Em andamento pela equipe</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 text-center text-md-start">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-secondary bg-opacity-25 text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-secondary small">3. Liberação de Vendas</div>
                                    <div class="text-muted" style="font-size: 12px;">Ativação do catálogo</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INFORMATIVO DE BLOQUEIO TEMPORÁRIO -->
                    <div class="alert alert-warning bg-warning bg-opacity-10 border-0 rounded-3 d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-shield-exclamation text-warning fs-4"></i>
                        <div class="small">
                            <strong>Funcionalidades pausadas temporariamente:</strong> A publicação de livros e processamento de vendas ficam bloqueados até que um administrador aprove seu cadastro. Você pode navegar pelo site e ajustar suas informações normalmente.
                        </div>
                    </div>

                    <!-- BOTÕES DE AÇÃO -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vendedor.perfil.editar') }}" class="btn btn-outline-primary rounded-3 px-4">
                            <i class="bi bi-pencil me-1"></i> Revisar / Editar Dados da Loja
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3 px-4 text-muted">
                            Voltar ao Painel Geral
                        </a>
                    </div>
                </div>
            </div>

        <!-- 2. CASO REJEITADO -->
        @elseif ($vendedor->isRejeitado())
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-danger border-5">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 50px; height: 50px; font-size: 24px;">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Sua solicitação de vendedor não foi aprovada</h4>
                            <p class="text-muted mb-0">
                                A equipe de moderação não pôde aprovar sua solicitação com as informações fornecidas. Isso pode acontecer por CNPJ irregular, dados comerciais incorretos ou não conformidade com as diretrizes da plataforma.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vendedor.perfil.editar') }}" class="btn btn-primary rounded-3 px-4">
                            <i class="bi bi-pencil me-1"></i> Corrigir Dados e Reenviar
                        </a>
                        <a href="mailto:universopapel.notification@gmail.com" class="btn btn-outline-secondary rounded-3 px-4">
                            <i class="bi bi-envelope me-1"></i> Falar com o Suporte
                        </a>
                    </div>
                </div>
            </div>

        <!-- 3. CASO APROVADO -->
        @else
            <!-- CARDS DE INDICADORES (KPIS) -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="rounded-4 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px; font-size: 22px;">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Livros no Catálogo</div>
                                <h3 class="fw-bold text-dark mb-0">{{ $livrosCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="rounded-4 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px; font-size: 22px;">
                                <i class="bi bi-bag-check"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Pedidos Recebidos</div>
                                <h3 class="fw-bold text-dark mb-0">{{ $pedidosCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="rounded-4 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center"
                                style="width: 54px; height: 54px; font-size: 22px;">
                                <i class="bi bi-star"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Avaliações da Loja</div>
                                <h3 class="fw-bold text-dark mb-0">{{ $avaliacoesCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FERRAMENTAS DA LOJA ATIVA -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-grid text-primary me-2"></i> Gestão da Loja
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vendedor.perfil.editar') }}" class="btn btn-outline-primary rounded-3 px-4 py-2">
                            <i class="bi bi-gear me-1"></i> Configurações da Loja
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3 px-4 py-2 text-muted">
                            <i class="bi bi-house me-1"></i> Painel do Usuário
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- DADOS DA LOJA REGISTRADOS -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle text-primary me-2"></i> Ficha Cadastral da Loja
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">Nome Fantasia</div>
                        <div class="fw-semibold text-dark">{{ $vendedor->nome_fantasia }}</div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">Razão Social</div>
                        <div class="fw-semibold text-dark">{{ $vendedor->razao_social }}</div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">CNPJ</div>
                        <div class="fw-semibold text-dark">{{ $vendedor->cnpj }}</div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">Inscrição Estadual</div>
                        <div class="fw-semibold text-dark">{{ $vendedor->inscricao_estadual ?? 'Não informada' }}</div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">Telefone / WhatsApp Comercial</div>
                        <div class="fw-semibold text-dark">{{ $vendedor->telefone_comercial ?? 'Não informado' }}</div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="text-muted small">Status Atual</div>
                        <div>
                            @if ($vendedor->isPendente())
                                <span class="badge bg-warning text-dark">Pendente de Análise</span>
                            @elseif ($vendedor->isAprovado())
                                <span class="badge bg-success">Aprovado e Ativo</span>
                            @else
                                <span class="badge bg-danger">Rejeitado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
