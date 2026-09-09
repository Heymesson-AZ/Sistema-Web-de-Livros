<?php

use App\Http\Controllers\Administrador\AdministradorController;
use App\Http\Controllers\Administrador\LivroAdminController;
use App\Http\Controllers\Cliente\ClienteController;
use App\Http\Controllers\Livro\LivroPublicoController;
use App\Http\Controllers\Painel\PainelController;
use App\Http\Controllers\Vendedor\LivroVendedorController;
use App\Http\Controllers\Vendedor\VendedorController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (Acesso livre a todos os visitantes)
Route::get('/', [LivroPublicoController::class, 'index'])->name('inicio');
Route::get('/busca-rapida', [LivroPublicoController::class, 'buscaRapida'])->name('livros.busca-rapida');
Route::get('/livros/{livro}', [LivroPublicoController::class, 'show'])->name('livros.show');
Route::get('/livro/{livro}', [LivroPublicoController::class, 'show'])->name('livros.detalhes');

// Painel principal e Perfil centralizado, acessível para usuários autenticados e verificados
Route::get('/dashboard', [PainelController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/painel', [PainelController::class, 'index'])->middleware(['auth', 'verified'])->name('painel');
Route::get('/perfil', function () {
    return redirect()->route('painel', ['tab' => 'perfil']);
})->middleware(['auth', 'verified'])->name('perfil');


// Rotas para o perfil de Cliente
Route::middleware(['auth', 'checkTipo:cliente'])->group(function () {
    Route::prefix('cliente')->group(function () {
        Route::get('/perfil-cliente', [ClienteController::class, 'editarPerfil'])->name('cliente.perfil.editar');
        Route::patch('/perfil-cliente', [ClienteController::class, 'atualizarPerfil'])->name('cliente.perfil.atualizar');
        Route::delete('/perfil-cliente', [ClienteController::class, 'deletarConta'])->name('cliente.perfil.deletar');
    });
});

// Solicitação de Cadastro de Vendedor (Pública para visitantes e clientes)
Route::get('/vendedor/cadastrar', [VendedorController::class, 'solicitarCadastro'])->name('vendedor.solicitar');
Route::post('/vendedor/cadastrar', [VendedorController::class, 'enviarSolicitacao'])->name('vendedor.solicitar.salvar');

// Rotas para o perfil e painel de Vendedor
Route::middleware(['auth', 'checkTipo:vendedor'])->group(function () {
    Route::prefix('vendedor')->group(function () {
        Route::get('/painel', [VendedorController::class, 'painel'])->name('vendedor.painel');
        Route::get('/perfil-vendedor', [VendedorController::class, 'editarPerfil'])->name('vendedor.perfil.editar');
        Route::patch('/perfil-vendedor', [VendedorController::class, 'atualizarPerfil'])->name('vendedor.perfil.atualizar');
        Route::delete('/perfil-vendedor', [VendedorController::class, 'deletarConta'])->name('vendedor.perfil.deletar');

        // CRUD de Livros do Vendedor
        Route::resource('livros', LivroVendedorController::class)
            ->names('vendedor.livros');
    });
});

// Rotas para o perfil de Admin e Gestão de Administradores, Vendedores, Clientes e Livros
Route::middleware(['auth', 'checkTipo:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/perfil-admin', [AdministradorController::class, 'editarPerfil'])->name('admin.perfil.editar');
        Route::patch('/perfil-admin', [AdministradorController::class, 'atualizarPerfil'])->name('admin.perfil.atualizar');
        Route::delete('/perfil-admin', [AdministradorController::class, 'deletarConta'])->name('admin.perfil.deletar');

        // CRUD Global de Livros
        Route::resource('livros', LivroAdminController::class)
            ->names('admin.livros');

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
