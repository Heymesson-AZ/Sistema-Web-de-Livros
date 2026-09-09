<?php

use App\Http\Controllers\Administrador\AdministradorController;
use App\Http\Controllers\Administrador\CupomController;
use App\Http\Controllers\Administrador\LivroAdminController;
use App\Http\Controllers\Carrinho\CarrinhoController;
use App\Http\Controllers\Cliente\AvaliacaoController;
use App\Http\Controllers\Cliente\CartaoSalvoController;
use App\Http\Controllers\Cliente\ClienteController;
use App\Http\Controllers\Cliente\EnderecoController;
use App\Http\Controllers\Cliente\FavoritoController;
use App\Http\Controllers\Livro\LivroPublicoController;
use App\Http\Controllers\Painel\PainelController;
use App\Http\Controllers\Pedido\PedidoController;
use App\Http\Controllers\Vendedor\LivroVendedorController;
use App\Http\Controllers\Vendedor\VendedorController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (Acesso livre a todos os visitantes)
Route::get('/', [LivroPublicoController::class, 'index'])->name('inicio');
Route::get('/busca-rapida', [LivroPublicoController::class, 'buscaRapida'])->name('livros.busca-rapida');
Route::get('/livros/{livro}', [LivroPublicoController::class, 'show'])->name('livros.show');
Route::get('/livro/{livro}', [LivroPublicoController::class, 'show'])->name('livros.detalhes');

// Rotas do Carrinho de Compras (públicas com suporte a visitante e usuário logado)
Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::patch('/carrinho/atualizar/{livro}', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');
Route::delete('/carrinho/remover/{livro}', [CarrinhoController::class, 'remover'])->name('carrinho.remover');
Route::post('/carrinho/limpar', [CarrinhoController::class, 'limpar'])->name('carrinho.limpar');
Route::post('/carrinho/cupom', [CarrinhoController::class, 'aplicarCupom'])->name('carrinho.cupom.aplicar');
Route::delete('/carrinho/cupom', [CarrinhoController::class, 'removerCupom'])->name('carrinho.cupom.remover');

// Painel principal e Perfil centralizado, acessível para usuários autenticados e verificados
Route::get('/dashboard', [PainelController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/painel', [PainelController::class, 'index'])->middleware(['auth', 'verified'])->name('painel');
Route::get('/perfil', function () {
    return redirect()->route('painel', ['tab' => 'perfil']);
})->middleware(['auth', 'verified'])->name('perfil');

// Rotas de Usuário Autenticado (Endereços, Checkout, Pedidos, Cartões, Favoritos e Avaliações)
Route::middleware(['auth'])->group(function () {
    // Gestão de Endereços do Usuário (Multi-endereço)
    Route::get('/enderecos', [EnderecoController::class, 'listar'])->name('enderecos.listar');
    Route::post('/enderecos', [EnderecoController::class, 'salvar'])->name('enderecos.salvar');
    Route::put('/enderecos/{endereco}', [EnderecoController::class, 'atualizar'])->name('enderecos.atualizar');
    Route::patch('/enderecos/{endereco}/principal', [EnderecoController::class, 'definirPrincipal'])->name('enderecos.principal');
    Route::match(['post', 'patch'], '/enderecos/{endereco}/definir-principal', [EnderecoController::class, 'definirPrincipal'])->name('enderecos.definir-principal');
    Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'deletar'])->name('enderecos.deletar');

    // Fluxo de Checkout e Gestão de Pedidos
    Route::get('/checkout', [PedidoController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/finalizar', [PedidoController::class, 'finalizar'])->name('checkout.finalizar');
    Route::post('/pedidos/finalizar', [PedidoController::class, 'finalizar'])->name('pedidos.finalizar');
    Route::get('/pedidos/{pedido}/sucesso', [PedidoController::class, 'sucesso'])->name('pedidos.sucesso');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'detalhes'])->name('pedidos.show');
    Route::patch('/pedidos/{pedido}/status', [PedidoController::class, 'atualizarStatus'])->name('pedidos.atualizar-status');
    Route::post('/pedidos/{pedido}/avaliar', [AvaliacaoController::class, 'salvar'])->name('avaliacoes.salvar');
    Route::delete('/avaliacoes/{avaliacao}', [AvaliacaoController::class, 'deletar'])->name('avaliacoes.deletar');

    // Cartões Salvos para Compras Rápidas
    Route::post('/cartoes-salvos', [CartaoSalvoController::class, 'salvar'])->name('cartoes.salvar');
    Route::match(['post', 'patch'], '/cartoes-salvos/{cartao}/padrao', [CartaoSalvoController::class, 'definirPadrao'])->name('cartoes.definir-padrao');
    Route::match(['post', 'patch'], '/cartoes-salvos/{cartao}/definir-padrao', [CartaoSalvoController::class, 'definirPadrao'])->name('cartoes.padrao');
    Route::delete('/cartoes-salvos/{cartao}', [CartaoSalvoController::class, 'deletar'])->name('cartoes.deletar');

    // Favoritos / Lista de Desejos
    Route::post('/favoritos/toggle/{livro}', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::delete('/favoritos/{livro}', [FavoritoController::class, 'remover'])->name('favoritos.remover');
});


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

        // Gestão e Moderação de Livros
        Route::patch('livros/{livro}/status', [LivroAdminController::class, 'alterarStatusModeracao'])
            ->name('admin.livros.status');
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

        // Rotas do CRUD de Cupons de Desconto
        Route::resource('cupons', CupomController::class)
            ->names('admin.cupons');
    });
});

require __DIR__ . '/auth.php';
