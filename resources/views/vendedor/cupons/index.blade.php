<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Painel do Vendedor</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Cupons da Loja & Campanhas
                </li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div
                class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Cupons da Loja & Incentivos Promocionais</h3>
                    <p class="text-muted small mb-0">
                        Crie códigos de desconto exclusivos para sua loja
                        <strong>{{ $vendedor->nome_fantasia }}</strong> e participe das campanhas de incentivo da
                        plataforma.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendedor.painel') }}"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Painel da Loja
                    </a>
                    <a href="{{ route('vendedor.cupons.create') }}"
                        class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg"></i> Novo Cupom da Loja
                    </a>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="small fw-semibold">{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div class="small fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- SEÇÃO 1: CUPONS DA MINHA LOJA -->
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-ticket-perforated-fill text-primary me-2"></i> Meus Cupons de Desconto
                </h5>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 font-monospace">
                    {{ $cupons->total() }} cupom(ns) cadastrado(s)
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Código</th>
                                <th class="py-3">Desconto</th>
                                <th class="py-3">Usos / Limite</th>
                                <th class="py-3">Validade</th>
                                <th class="py-3">Status</th>
                                <th class="pe-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cupons as $cupom)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge bg-dark bg-opacity-10 text-dark border font-monospace px-2 py-1 fs-6">
                                                {{ $cupom->codigo }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success">
                                            {{ $cupom->descricao_desconto }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">
                                            <strong>{{ $cupom->usos_atuais }}</strong> /
                                            {{ $cupom->limite_uso ? $cupom->limite_uso : 'Ilimitado' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($cupom->validade_cupom)
                                            <span
                                                class="small {{ $cupom->validade_cupom->isPast() ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                <i
                                                    class="bi bi-calendar-event me-1"></i>{{ $cupom->validade_cupom->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="small text-muted">Sem expiração</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($cupom->isValido())
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"
                                                style="font-size: 11px;">
                                                <i class="bi bi-check-circle me-1"></i> Ativo
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1"
                                                style="font-size: 11px;">
                                                <i class="bi bi-x-circle me-1"></i> Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="actions-group justify-content-end">
                                            {{-- BOTÃO PADRONIZADO EDITAR (ICON-ONLY) --}}
                                            <a href="{{ route('vendedor.cupons.edit', $cupom) }}"
                                                class="btn-action btn-action-edit" title="Editar cupom"
                                                aria-label="Editar cupom">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('vendedor.cupons.destroy', $cupom) }}"
                                                method="POST" class="d-inline"
                                                data-confirm-message="Deseja realmente remover o cupom {{ $cupom->codigo }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-delete"
                                                    title="Excluir cupom" aria-label="Excluir cupom">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-ticket-perforated fs-1 text-muted d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Nenhum cupom cadastrado para sua loja</h6>
                                        <p class="text-muted small mb-3">Crie cupons de desconto exclusivos para
                                            impulsionar as vendas dos seus livros.</p>
                                        <a href="{{ route('vendedor.cupons.create') }}"
                                            class="btn btn-primary btn-sm rounded-pill px-4">
                                            <i class="bi bi-plus-lg me-1"></i> Criar Primeiro Cupom
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($cupons->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $cupons->links() }}
                </div>
            @endif
        </div>

        <!-- SEÇÃO 2: CAMPANHAS PROMOCIONAIS DE INCENTIVO DA PLATAFORMA -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i class="bi bi-stars text-warning me-2"></i> Campanhas de Incentivo da Plataforma
                        </h5>
                        <p class="text-muted small mb-0">
                            A plataforma lança campanhas promocionais periódicas. Concorde com a participação para que
                            seus livros recebam destaque e desconto.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Código da Campanha</th>
                                <th class="py-3">Benefício Promocional</th>
                                <th class="py-3">Validade</th>
                                <th class="py-3">Sua Participação</th>
                                <th class="pe-4 py-3 text-end">Adesão / Concordância</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($campanhas as $campanha)
                                @php
                                    $jaAderiu = in_array($campanha->id, $campanhasAderidasIds, true);
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <span
                                            class="badge bg-warning bg-opacity-25 text-dark border border-warning font-monospace px-2 py-1 fs-6">
                                            {{ $campanha->codigo }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-primary">
                                            {{ $campanha->descricao_desconto }}
                                        </span>
                                        <div class="text-muted" style="font-size: 11px;">Aplica-se aos títulos das
                                            lojas participantes</div>
                                    </td>
                                    <td>
                                        @if ($campanha->validade_cupom)
                                            <span class="small text-muted">
                                                Até {{ $campanha->validade_cupom->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="small text-muted">Contínua</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($jaAderiu)
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> Concordou & Participando
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-muted border border-secondary-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-dash-circle me-1"></i> Não participante
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if ($jaAderiu)
                                            <form action="{{ route('vendedor.cupons.desistir', $campanha) }}"
                                                method="POST" class="d-inline"
                                                data-confirm-message="Deseja suspender a participação da sua loja nesta campanha?">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                                    <i class="bi bi-x-circle me-1"></i> Sair da Campanha
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('vendedor.cupons.aderir', $campanha) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-hand-thumbs-up-fill me-1"></i> Concordar & Aderir
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        <i class="bi bi-info-circle me-1"></i> Nenhuma campanha promocional de
                                        incentivo ativa pela administração no momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
