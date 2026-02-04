<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>@yield('titulo')</title>
</head>

<body class="bg-gray-100 justify-content-center items-center min-h-screen">

    <header>
        <h1>Bem-vindo ao Sistema Web de Livros</h1>
    </header>

    <main>
       {{-- --}} @section('conteudo')
        <p>Este é o conteúdo padrão do sistema de livros.</p>
        @show
        <hr>
        @yield('lista_livros')
        <hr>
        {{-- Aqui acontece a mágica --}}
        @include('components.sidebar', [
        'title' => 'Destaques da Semana',
        'user_name' => 'Heymesson Azevedo'
        ])
        <hr>
        {{-- Em qualquer arquivo .blade.php --}}
        <x-alerta tipo="success" titulo="Sucesso!">
            Sua alteração foi salva com **sucesso**!
        </x-alerta>

        <x-alerta tipo="danger" titulo="Erro!">
            Não foi possível conectar ao banco de dados.
        </x-alerta>

        <hr>
        {{-- Usando o layout --}}
        <x-app-layout title="Painel de Controle">
            {{-- Tudo o que escrever aqui dentro vai automaticamente para o $slot --}}
            
            <p>Bem-vindo ao seu painel administrativo.</p>

            <div class="stats">
                <p>Vendas hoje: 15</p>
            </div>
            
        </x-app-layout>

    </main>

    <footer>
        <p>@yield('rodape')</p>
    </footer>

</body>

</html>