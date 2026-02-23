<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

// Middleware para verificar o tipo de usuário antes de permitir o acesso a determinadas rotas ou recursos
class CheckTipoUsuario
{
    /**
     *  O Handle é um middleware que verifica o tipo de usuário antes de permitir o acesso a determinadas 
     * rotas ou recursos. Ele pode ser usado para garantir que apenas usuários com um tipo específico 
     * (por exemplo, admin, editor, etc.) possam acessar certas partes do aplicativo.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $tipo_usuario): Response
    {
        // Verifica se o usuário está autenticado e se o tipo de usuário corresponde ao esperado
        if (!Auth::check() || Auth::user()->tipo !== $tipo_usuario) {
            abort(403, 'Ação não autorizada.');
        }
        return $next($request);
    }
}
