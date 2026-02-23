<?php

use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Route;

// Rotas publica(Todo mundo pode acessar)
Route::get('/', function () {
    return view('welcome');
});

// rota para o dashboard, acessível apenas para usuários autenticados e verificados. 
// Ele retorna a view 'dashboard' quando acessado.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Rotas para o perfil de Cliente

// quando passamos mais de um middleware, o Laravel irá aplicar todos eles na ordem em que foram definidos.
// Neste caso, as rotas dentro deste grupo só serão acessíveis para usuários que estão autenticados (auth) e que têm o tipo 'cliente'
// estou usando o middleware 'checkTipo' para verificar se o usuário autenticado tem o tipo 'cliente'. 
Route::middleware(['auth', 'checkTipo:cliente'])->group(function () {
    Route::prefix('cliente')->group(function () {
        //rota para o perfil do cliente, onde ele pode ver, editar suas informações, atualizar e deletar sua conta
        Route::get('/perfil-cliente', [PerfilController::class, 'edit'])->name('cliente.perfil.editar'); // Rota para exibir o formulário de edição do perfil do cliente
        Route::patch('/perfil-cliente', [PerfilController::class, 'update'])->name('cliente.perfil.atualizar'); // Rota para processar a atualização do perfil do cliente
        Route::delete('/perfil-cliente', [PerfilController::class, 'destroy'])->name('cliente.perfil.deletar'); // Rota para processar a exclusão da conta do cliente   
    });
});

// Rotas para o perfil de Vendedor
Route::middleware(['auth', 'checkTipo:vendedor'])->group(function () {
    Route::prefix('vendedor')->group(function () {
        Route::get('/perfil-vendedor', [PerfilController::class, 'edit'])->name('vendedor.perfil.editar'); // Rota para exibir o formulário de edição do perfil do vendedor
        Route::patch('/perfil-vendedor', [PerfilController::class, 'update'])->name('vendedor.perfil.atualizar'); // Rota para processar a atualização do perfil do vendedor
        Route::delete('/perfil-vendedor', [PerfilController::class, 'destroy'])->name('vendedor.perfil.deletar'); // Rota para processar a exclusão da conta do vendedor
    });
});

require __DIR__ . '/auth.php';
