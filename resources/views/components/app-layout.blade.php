{{-- resources/views/components/app-layout.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Meu App Laravel' }}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-light">
    
    <nav>
        <a href="/">Home</a> | <a href="/perfil">Perfil</a>
    </nav>

    <main class="container">
        {{-- Aqui é onde o conteúdo da sua página será "injetado" --}}
        {{ $slot }}
    </main>

    <footer>
        <p>&copy; 2026 - Meu Sistema Inc.</p>
    </footer>

</body>
</html>