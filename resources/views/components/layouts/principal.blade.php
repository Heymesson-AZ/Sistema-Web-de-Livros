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
            <div class="search-box">
                <i data-lucide="search"></i>
                <input type="text" placeholder="Buscar livros...">
            </div>

            <!-- Perfil / Usuário (Desktop) -->
            <a href="{{ route('dashboard') }}"
                class="user-pill-link d-none d-md-flex align-items-center gap-2 text-decoration-none ms-2">
                <div class="user-avatar-circle">
                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                </div>
                <span class="user-name-label">{{ Auth::user()->name }}</span>
            </a>
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
