<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // Configura o middleware globalmente, se necessário
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkTipo' => \App\Http\Middleware\CheckTipoUsuario::class,
        ]);
    })


    // Configurações adicionais, como providers, aliases, etc., podem ser adicionadas aqui
    ->withExceptions(function (Exceptions $exceptions): void {
        //

    })->create();
