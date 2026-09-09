<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universo de Papel</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Links Bootstrap para ícones --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
@php
    $modalAutomatico = '';
    if (old('formulario') === 'registro' || $errors->hasAny(['name', 'cpf', 'data_nascimento', 'telefone', 'password_confirmation'])) {
        $modalAutomatico = 'registerModal';
    } elseif (old('formulario') === 'recuperar_senha' || session('form_sucesso') === 'recuperar_senha') {
        $modalAutomatico = 'forgotPasswordModal';
    } elseif ($errors->any() || session('status')) {
        $modalAutomatico = 'loginModal';
    }
@endphp

<body @if($modalAutomatico) data-auth-modal="{{ $modalAutomatico }}" @endif>

    <!-- NAVBAR -->
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

            @auth
                <!-- Dropdown de Notificações para Usuários Autenticados -->
                <x-dropdown-notificacoes />

                <!-- Menu Dropdown Centralizado do Usuário -->
                <x-navbar-user-dropdown />
            @else
                <!-- Botão Venda Conosco (Desktop) -->
                <a href="{{ route('vendedor.solicitar') }}"
                    class="btn btn-warning btn-sm d-none d-md-flex align-items-center gap-1 rounded-pill px-3 ms-2 fw-semibold text-dark text-decoration-none shadow-sm">
                    <i data-lucide="store" style="width: 15px; height: 15px;"></i>
                    <span>Venda Conosco</span>
                </a>

                <!-- Botão Entrar Rápido (Desktop) -->
                <button type="button"
                    class="btn btn-outline-light btn-sm d-none d-md-flex align-items-center gap-2 rounded-pill px-3 ms-2"
                    data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                    <span>Entrar</span>
                </button>
            @endauth
        </div>
    </nav>

    <!-- BACKDROP MOBILE -->
    <div class="menu-backdrop" id="menuBackdrop"></div>

    <!-- MENU -->
    <x-menu />

    <x-modal-login />
    <x-modal-registro />
    <x-modal-esqueceu-senha />

    <!-- CONTEÚDO -->
    <main class="main-content">
        {{ $slot }}
    </main>

    <!-- RODAPÉ -->
    <x-footer />

    <!-- SCRIPTS EXTERNOS -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
