<?php
// Este arquivo define as rotas de autenticação para a aplicação, incluindo registro, login, recuperação de senha, 
//verificação de email e logout. As rotas são organizadas em grupos de middleware 
//para garantir que apenas usuários convidados (guest) possam acessar as rotas de registro e login, 
//enquanto apenas usuários autenticados (auth) possam acessar as rotas de verificação de email, confirmação de senha e logout.

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação para usuários convidados (guest) e autenticados (auth)
Route::middleware('guest')->group(function () {
    // Rotas para registro, login e recuperação de senha
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
        // Rota para criar um novo usuário
    Route::post('register', [RegisteredUserController::class, 'store']);
        // Rota para exibir o formulário de login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
        // Rota para processar o login do usuário
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
        // Rotas para recuperação de senha
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
        // Rota para enviar o link de redefinição de senha para o email do usuário
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
        // Rota para exibir o formulário de redefinição de senha usando o token enviado por email
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
        // Rota para processar a redefinição de senha do usuário
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Rotas para verificação de email, confirmação de senha e logout
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
        // Rota para verificar o email do usuário usando o link enviado por email
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
        // Rota para reenviar o link de verificação de email para o usuário
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
        // Rota para exibir o formulário de confirmação de senha
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
        // Rota para processar a confirmação de senha do usuário
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
        // Rota para exibir o formulário de atualização de senha
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
        // Rota para fazer logout do usuário
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
