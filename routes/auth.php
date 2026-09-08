<?php

use App\Http\Controllers\Autenticacao\AutenticacaoController;
use App\Http\Controllers\Autenticacao\AvisoVerificacaoEmailController;
use App\Http\Controllers\Autenticacao\CadastroUsuarioController;
use App\Http\Controllers\Autenticacao\ConfirmarSenhaController;
use App\Http\Controllers\Autenticacao\LinkRedefinicaoSenhaController;
use App\Http\Controllers\Autenticacao\NotificacaoVerificacaoEmailController;
use App\Http\Controllers\Autenticacao\RedefinicaoSenhaController;
use App\Http\Controllers\Autenticacao\SenhaController;
use App\Http\Controllers\Autenticacao\VerificarEmailController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// ROTAS DE AUTENTICAÇÃO PARA VISITANTES (GUEST)
// =============================================================================
Route::middleware('guest')->group(function () {
    // Cadastro de novos usuários
    Route::get('cadastrar', [CadastroUsuarioController::class, 'exibirFormulario'])
        ->name('cadastrar');
    Route::post('cadastrar', [CadastroUsuarioController::class, 'cadastrar'])
        ->name('cadastrar.salvar');

    // Login e autenticação
    Route::get('entrar', [AutenticacaoController::class, 'exibirLogin'])
        ->name('entrar');
    Route::post('entrar', [AutenticacaoController::class, 'autenticar'])
        ->name('entrar.autenticar');

    // Solicitação de link de redefinição de senha
    Route::get('esqueceu-senha', [LinkRedefinicaoSenhaController::class, 'exibirFormulario'])
        ->name('senha.solicitar');
    Route::post('esqueceu-senha', [LinkRedefinicaoSenhaController::class, 'enviarLink'])
        ->name('senha.email');

    // Redefinição de senha com token
    Route::get('redefinir-senha/{token}', [RedefinicaoSenhaController::class, 'exibirFormulario'])
        ->name('senha.redefinir');
    Route::post('redefinir-senha', [RedefinicaoSenhaController::class, 'redefinirSenha'])
        ->name('senha.atualizar');

    // -------------------------------------------------------------------------
    // Aliases retrocompatíveis (padrão Laravel / frameworks)
    // -------------------------------------------------------------------------
    Route::get('register', [CadastroUsuarioController::class, 'exibirFormulario'])->name('register');
    Route::post('register', [CadastroUsuarioController::class, 'cadastrar']);

    Route::get('login', [AutenticacaoController::class, 'exibirLogin'])->name('login');
    Route::post('login', [AutenticacaoController::class, 'autenticar']);

    Route::get('forgot-password', [LinkRedefinicaoSenhaController::class, 'exibirFormulario'])->name('password.request');
    Route::post('forgot-password', [LinkRedefinicaoSenhaController::class, 'enviarLink']);

    Route::get('reset-password/{token}', [RedefinicaoSenhaController::class, 'exibirFormulario'])->name('password.reset');
    Route::post('reset-password', [RedefinicaoSenhaController::class, 'redefinirSenha'])->name('password.store');
});

// =============================================================================
// ROTAS DE AUTENTICAÇÃO PARA USUÁRIOS AUTENTICADOS (AUTH)
// =============================================================================
Route::middleware('auth')->group(function () {
    // Verificação de e-mail
    Route::get('verificar-email', AvisoVerificacaoEmailController::class)
        ->name('verificacao.aviso');
    Route::get('verificar-email/{id}/{hash}', VerificarEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verificacao.verificar');
    Route::post('email/notificacao-verificacao', [NotificacaoVerificacaoEmailController::class, 'reenviar'])
        ->middleware('throttle:6,1')
        ->name('verificacao.enviar');

    // Confirmação de senha para ações sensíveis
    Route::get('confirmar-senha', [ConfirmarSenhaController::class, 'exibirConfirmacao'])
        ->name('senha.confirmar');
    Route::post('confirmar-senha', [ConfirmarSenhaController::class, 'confirmar'])
        ->name('senha.confirmar.salvar');

    // Atualização de senha
    Route::put('senha', [SenhaController::class, 'atualizar'])
        ->name('senha.alterar');

    // Encerramento de sessão (Logout)
    Route::post('sair', [AutenticacaoController::class, 'encerrarSessao'])
        ->name('sair');

    // -------------------------------------------------------------------------
    // Aliases retrocompatíveis (padrão Laravel / frameworks)
    // -------------------------------------------------------------------------
    Route::get('verify-email', AvisoVerificacaoEmailController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerificarEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [NotificacaoVerificacaoEmailController::class, 'reenviar'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmarSenhaController::class, 'exibirConfirmacao'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmarSenhaController::class, 'confirmar']);

    Route::put('password', [SenhaController::class, 'atualizar'])->name('password.update');

    Route::post('logout', [AutenticacaoController::class, 'encerrarSessao'])->name('logout');
});
