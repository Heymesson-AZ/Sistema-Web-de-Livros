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
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $exception, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Sua sessão expirou por inatividade. Por favor, recarregue a página.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Sua sessão expirou devido à inatividade. O formulário foi recarregado, por favor tente enviar novamente.');
        });
    })->create();
