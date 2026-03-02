<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Livraria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <x-menu />
    <main>
        {{ $slot }}
    </main>
</body>

</html>