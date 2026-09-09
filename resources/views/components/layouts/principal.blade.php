<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Universo de Papel') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Bootstrap & Ícones --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Scripts e Estilos via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <!-- BARRA SUPERIOR (TOPBAR) -->
    <nav class="topbar">
        <div class="topbar-left">
            <button type="button" class="menu-toggle-btn d-lg-none" id="menuToggle" aria-label="Abrir menu">
                <i data-lucide="menu"></i>
            </button>
            <a href="{{ url('/') }}" class="brand-link" title="Universo de Papel">
                <img src="{{ asset('images/logo-white.png') }}" alt="Universo de Papel" class="brand-logo-img">
            </a>
        </div>

        <div class="topbar-right">
            <form action="{{ url('/') }}" method="GET" class="search-box">
                <i data-lucide="search"></i>
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar livros..."
                    autocomplete="off" data-dynamic-search>
            </form>

            <!-- Botão de Notificações para Usuários Autenticados -->
            @auth
                @php
                    $notificacoesUsuario = \App\Http\Controllers\Painel\PainelController::obterNotificacoes(Auth::user());
                    $totalNotificacoes = count($notificacoesUsuario);
                @endphp
                <a href="{{ route('painel', ['tab' => 'visao-geral']) }}"
                    class="btn position-relative p-2 text-white border-0 bg-transparent rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 38px; height: 38px;"
                    title="{{ $totalNotificacoes > 0 ? $totalNotificacoes . ' notificações do sistema' : 'Notificações' }}">
                    <i class="bi bi-bell-fill fs-5"></i>
                    @if ($totalNotificacoes > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light"
                            style="font-size: 10px; padding: 3px 6px;">
                            {{ $totalNotificacoes }}
                        </span>
                    @endif
                </a>
            @endauth

            <!-- Menu Dropdown Centralizado do Usuário -->
            <x-navbar-user-dropdown />
        </div>
    </nav>

    <!-- FUNDO ESCURO MOBILE (BACKDROP) -->
    <div class="menu-backdrop" id="menuBackdrop"></div>

    <!-- MENU LATERAL -->
    <x-menu />

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        @isset($header)
            <div class="mb-4">
                {{ $header }}
            </div>
        @endisset

        {{ $slot }}
    </main>

    <!-- RODAPÉ -->
    <x-footer />

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>

</html>
