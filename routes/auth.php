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

// Rotas de autenticação para visitantes (guest)
Route::middleware('guest')->group(function () {
    // Registro de novos usuários
    Route::get('register', [CadastroUsuarioController::class, 'exibirFormulario'])
        ->name('register');
    Route::post('register', [CadastroUsuarioController::class, 'cadastrar']);

    // Login e autenticação
    Route::get('login', [AutenticacaoController::class, 'exibirLogin'])
        ->name('login');
    Route::post('login', [AutenticacaoController::class, 'autenticar']);

    // Solicitação de link de redefinição de senha
    Route::get('forgot-password', [LinkRedefinicaoSenhaController::class, 'exibirFormulario'])
        ->name('password.request');
    Route::post('forgot-password', [LinkRedefinicaoSenhaController::class, 'enviarLink'])
        ->name('password.email');

    // Redefinição de senha com token
    Route::get('reset-password/{token}', [RedefinicaoSenhaController::class, 'exibirFormulario'])
        ->name('password.reset');
    Route::post('reset-password', [RedefinicaoSenhaController::class, 'redefinirSenha'])
        ->name('password.store');
});

// Rotas de autenticação para usuários autenticados (auth)
Route::middleware('auth')->group(function () {
    // Verificação de e-mail
    Route::get('verify-email', AvisoVerificacaoEmailController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerificarEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [NotificacaoVerificacaoEmailController::class, 'reenviar'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirmação de senha para ações sensíveis
    Route::get('confirm-password', [ConfirmarSenhaController::class, 'exibirConfirmacao'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmarSenhaController::class, 'confirmar']);

    // Atualização de senha
    Route::put('password', [SenhaController::class, 'atualizar'])->name('password.update');

    // Encerramento de sessão (Logout)
    Route::post('logout', [AutenticacaoController::class, 'encerrarSessao'])
        ->name('logout');
});
