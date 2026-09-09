@php
    $usuario = Auth::user();
    $notificacoes = $usuario ? \App\Http\Controllers\Painel\PainelController::obterNotificacoes($usuario) : [];
    $totalNotificacoes = count($notificacoes);
@endphp

<div class="dropdown me-1 me-md-2" id="dropdownNotificacoesWrapper">
    <button type="button"
        class="btn position-relative p-2 text-white border-0 bg-transparent rounded-circle d-flex align-items-center justify-content-center shadow-none hover-bg-white-10"
        id="dropdownNotificacoesBtn" data-bs-toggle="dropdown" aria-expanded="false"
        title="{{ $totalNotificacoes > 0 ? $totalNotificacoes . ' notificação(ões) relevante(s)' : 'Sem notificações' }}"
        style="width: 40px; height: 40px; transition: background-color 0.2s ease;">
        <i class="bi bi-bell-fill fs-5"></i>
        @if ($totalNotificacoes > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light"
                style="font-size: 10.5px; padding: 3px 6px;">
                {{ $totalNotificacoes }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2"
        aria-labelledby="dropdownNotificacoesBtn"
        style="width: 320px; max-width: 90vw; z-index: 1060; overflow: hidden;">

        <!-- CABEÇALHO DO DROPDOWN -->
        <div class="d-flex align-items-center justify-content-between px-3 py-3 bg-light border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bell text-primary fw-bold"></i>
                <span class="fw-bold text-dark small mb-0">Notificações</span>
            </div>
            @if ($totalNotificacoes > 0)
                <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 11px;">
                    {{ $totalNotificacoes }} pendente(s)
                </span>
            @else
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" style="font-size: 11px;">
                    Tudo em dia
                </span>
            @endif
        </div>

        <!-- LISTA DE NOTIFICAÇÕES -->
        <div class="list-group list-group-flush" style="max-height: 360px; overflow-y: auto;">
            @forelse($notificacoes as $notif)
                @php
                    $corBg = match($notif['tipo'] ?? 'info') {
                        'danger' => 'bg-danger-subtle text-danger',
                        'warning' => 'bg-warning-subtle text-warning-emphasis',
                        'success' => 'bg-success-subtle text-success',
                        default => 'bg-primary-subtle text-primary',
                    };
                @endphp
                <div class="list-group-item list-group-item-action px-3 py-2 border-bottom border-light">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 {{ $corBg }}"
                            style="width: 32px; height: 32px;">
                            <i class="bi {{ $notif['icone'] ?? 'bi-info-circle-fill' }} fs-6"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 12.5px; max-width: 190px;">
                                    {{ $notif['titulo'] }}
                                </h6>
                            </div>
                            <p class="text-secondary small mb-2 lh-sm" style="font-size: 11.5px;">
                                {{ $notif['mensagem'] }}
                            </p>
                            @if (!empty($notif['link']))
                                <a href="{{ $notif['link'] }}" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold"
                                    style="font-size: 11px; border-radius: 6px;">
                                    {{ $notif['link_texto'] ?? 'Visualizar' }} <i class="bi bi-chevron-right" style="font-size: 9px;"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 px-3 text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-2">
                        <i class="bi bi-check2-all fs-4 text-success"></i>
                    </div>
                    <p class="small mb-0 fw-semibold text-secondary">Nenhuma notificação nova no momento.</p>
                    <span class="text-muted" style="font-size: 11px;">Tudo limpo na sua conta.</span>
                </div>
            @endforelse
        </div>

        <!-- RODAPÉ DO DROPDOWN -->
        <div class="p-2 bg-light text-center border-top">
            <a href="{{ route('painel', ['tab' => 'visao-geral']) }}"
                class="small text-decoration-none fw-bold text-primary d-inline-flex align-items-center gap-1">
                <span>Ver resumo no Painel</span>
                <i class="bi bi-arrow-right small"></i>
            </a>
        </div>
    </div>
</div>

