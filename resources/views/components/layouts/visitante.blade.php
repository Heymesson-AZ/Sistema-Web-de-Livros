<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Livraria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <nav class="bg-gray-800 text-white p-1" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 class="text-2xl font-bold">Minha Livraria</h1>
        </div>

        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Buscar livros...">
        </div>
        
    </nav>
    <x-menu />
    <main>
        {{ $slot }}
    </main>
</body>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Ativa todos os ícones do Lucide na página
    lucide.createIcons();
</script>

</html>