<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Universo de Papel </title>
    {{-- Links Bootstrap para ícones --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<>

    <!-- NAVBAR -->
    <nav class="topbar">
        <div class="topbar-left">
            <div class="brand">
                <div class="brand-text">
                    <span class="brand-title">Universo de Papel</span>
                </div>
            </div>
        </div>

        <div class="topbar-right">
            <div class="search-box">
                <i data-lucide="search"></i>
                <input type="text" placeholder="Buscar livros...">
            </div>
        </div>
    </nav>

    <!-- MENU -->
    <x-menu />

    <x-modal-login />
    <x-modal-registro />
    <x-modal-esqueceu-senha />
    <!-- CONTEÚDO -->
    <main class="main-content">
        {{ $slot }}
    </main>

    <!-- LUCIDE -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Atualize o script de erro para abrir o modal correto se houver falha no registro
        @if ($errors->has('name') || $errors->has('cpf') || $errors->has('telefone'))
            var regModal = new bootstrap.Modal(document.getElementById('registerModal'));
            regModal.show();
        @elseif ($errors->any())
            var logModal = new bootstrap.Modal(document.getElementById('loginModal'));
            logModal.show();
        @endif
    </script>
    </body>

</html>
