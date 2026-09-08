<?php

use App\Http\Controllers\Admin\AdministradorController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\VendedorController;
use App\Http\Controllers\Perfil\PerfilController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (Acesso livre a todos os visitantes)
Route::get('/', function () {
    return view('paginas.inicio');
})->name('inicio');

// Painel principal (Dashboard), acessível para usuários autenticados e verificados
Route::get('/dashboard', function () {
    return view('paginas.painel');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/painel', function () {
    return view('paginas.painel');
})->middleware(['auth', 'verified'])->name('painel');


// Rotas para o perfil de Cliente
Route::middleware(['auth', 'checkTipo:cliente'])->group(function () {
    Route::prefix('cliente')->group(function () {
        Route::get('/perfil-cliente', [PerfilController::class, 'editar'])->name('cliente.perfil.editar');
        Route::patch('/perfil-cliente', [PerfilController::class, 'atualizar'])->name('cliente.perfil.atualizar');
        Route::delete('/perfil-cliente', [PerfilController::class, 'deletar'])->name('cliente.perfil.deletar');
    });
});

// Rotas para o perfil de Vendedor
Route::middleware(['auth', 'checkTipo:vendedor'])->group(function () {
    Route::prefix('vendedor')->group(function () {
        Route::get('/perfil-vendedor', [PerfilController::class, 'editar'])->name('vendedor.perfil.editar');
        Route::patch('/perfil-vendedor', [PerfilController::class, 'atualizar'])->name('vendedor.perfil.atualizar');
        Route::delete('/perfil-vendedor', [PerfilController::class, 'deletar'])->name('vendedor.perfil.deletar');
    });
});

// Rotas para o perfil de Admin e Gestão de Administradores
Route::middleware(['auth', 'checkTipo:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/perfil-admin', [PerfilController::class, 'editar'])->name('admin.perfil.editar');
        Route::patch('/perfil-admin', [PerfilController::class, 'atualizar'])->name('admin.perfil.atualizar');
        Route::delete('/perfil-admin', [PerfilController::class, 'deletar'])->name('admin.perfil.deletar');

        // Rotas do CRUD de Administradores
        Route::resource('administradores', AdministradorController::class)
            ->parameters(['administradores' => 'administrador'])
            ->names('admin.administradores');

        // Rotas do CRUD de Vendedores
        Route::patch('vendedores/{vendedor}/status', [VendedorController::class, 'alterarStatus'])
            ->name('admin.vendedores.status');
        Route::resource('vendedores', VendedorController::class)
            ->parameters(['vendedores' => 'vendedor'])
            ->names('admin.vendedores');

        // Rotas do CRUD de Clientes
        Route::resource('clientes', ClienteController::class)
            ->parameters(['clientes' => 'cliente'])
            ->names('admin.clientes');
    });
});

require __DIR__ . '/auth.php';
