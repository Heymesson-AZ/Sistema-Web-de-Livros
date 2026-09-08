<x-layouts.principal>
    <div class="container py-3 py-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-3">
                    <i data-lucide="check-circle" class="text-success me-2" style="width: 28px; height: 28px;"></i>
                    <h2 class="h4 mb-0 text-success fw-bold">Login realizado com sucesso!</h2>
                </div>

                <p class="text-muted">Seja bem-vindo ao nosso sistema, <strong>{{ Auth::user()->name }}</strong>!</p>

                <hr class="my-3">

                <!-- Dados do Usuário logado -->
                <h5 class="fw-bold mb-3">Informações da Conta</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><strong>Nome:</strong> {{ Auth::user()->name }}</li>
                    <li class="mb-2"><strong>E-mail:</strong> {{ Auth::user()->email }}</li>
                    <li class="mb-2"><strong>Tipo de Conta:</strong> <span
                            class="badge bg-primary text-capitalize">{{ Auth::user()->tipo }}</span></li>
                    <li class="mb-2"><strong>Status:</strong> <span
                            class="badge bg-success text-capitalize">{{ Auth::user()->status }}</span></li>

                    @if (Auth::user()->tipo === 'cliente')
                        <li class="mb-2"><strong>Celular de Contato:</strong>
                            {{ Auth::user()->cliente?->celular_contato ?? 'Não informado' }}</li>
                    @elseif(Auth::user()->tipo === 'vendedor')
                        <li class="mb-2"><strong>Telefone Comercial:</strong>
                            {{ Auth::user()->vendedor?->telefone_comercial ?? 'Não informado' }}</li>
                    @elseif(Auth::user()->tipo === 'admin')
                        <li class="mb-2"><strong>Telefone de Urgência:</strong>
                            {{ Auth::user()->admin?->telefone_urgencia ?? 'Não informado' }}</li>
                        <li class="mb-2"><strong>Cargo:</strong> {{ Auth::user()->admin?->cargo ?? 'Administrador' }}
                        </li>
                        <li class="mb-2"><strong>Departamento:</strong>
                            {{ Auth::user()->admin?->departamento ?? 'Geral' }}</li>
                    @endif
                </ul>

                @if (Auth::user()->tipo === 'cliente')
                    <div class="card border-0 bg-light rounded-4 mt-4 p-4">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-shop text-primary me-2"></i> Quer vender seus livros na Universo de Papel?
                                </h6>
                                <p class="text-muted small mb-0">
                                    Abra sua livraria ou sebo parceiro e comece a vender para leitores de todo o país sem taxas de adesão.
                                </p>
                            </div>
                            <a href="{{ route('vendedor.solicitar') }}" class="btn btn-warning rounded-pill px-4 fw-semibold text-dark text-nowrap shadow-sm">
                                <i class="bi bi-arrow-right-circle me-1"></i> Quero Ser Vendedor
                            </a>
                        </div>
                    </div>
                @elseif (Auth::user()->tipo === 'vendedor')
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Status da sua Loja de Vendedor</h6>
                            @if (Auth::user()->vendedor?->isPendente())
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-hourglass-split me-1"></i> Pendente de Aprovação
                                </span>
                            @elseif (Auth::user()->vendedor?->isAprovado())
                                <span class="badge bg-success px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Loja Ativa e Aprovada
                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Solicitação Recusada
                                </span>
                            @endif
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="row g-2 small">
                                <div class="col-12 col-md-4"><strong>Nome Fantasia:</strong> {{ Auth::user()->vendedor?->nome_fantasia }}</div>
                                <div class="col-12 col-md-4"><strong>Razão Social:</strong> {{ Auth::user()->vendedor?->razao_social }}</div>
                                <div class="col-12 col-md-4"><strong>CNPJ:</strong> {{ Auth::user()->vendedor?->cnpj }}</div>
                            </div>
                        </div>

                        <a href="{{ route('vendedor.painel') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2">
                            <i data-lucide="store"></i>
                            <span>Acessar Painel da Minha Loja</span>
                        </a>
                    </div>
                @elseif (Auth::user()->tipo === 'admin')
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-3">Painel de Gestão Administrativa</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.administradores.index') }}"
                                class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2">
                                <i data-lucide="shield-check"></i>
                                <span>Administradores</span>
                            </a>
                            <a href="{{ route('admin.vendedores.index') }}"
                                class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2">
                                <i data-lucide="store"></i>
                                <span>Vendedores</span>
                            </a>
                            <a href="{{ route('admin.clientes.index') }}"
                                class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2">
                                <i data-lucide="users"></i>
                                <span>Clientes</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.principal>
