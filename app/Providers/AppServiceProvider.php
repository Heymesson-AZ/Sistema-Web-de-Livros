<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Mensagem interativa e acolhedora de Confirmação de Cadastro
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $firstName = explode(' ', trim($notifiable->name ?? 'Leitor(a)'))[0];

            return (new MailMessage)
                ->subject("✨ Bem-vindo(a) à Universo de Papel! Confirme seu cadastro")
                ->greeting("Olá, {$firstName}! 📖")
                ->line("Ficamos muito felizes em ter você aqui no **Universo de Papel**! Sua jornada por histórias incríveis, ofertas exclusivas e os melhores livros está prestes a começar.")
                ->line("Para ativar sua conta com total segurança e liberar todas as funcionalidades da loja, basta confirmar seu e-mail:")
                ->action("🚀 Confirmar Cadastro e Explorar Livros", $url)
                ->line("⏰ **Atenção:** Este link de ativação é válido por **60 minutos**.")
                ->line("💡 Se você não realizou esse cadastro em nossa livraria, não se preocupe! Basta desconsiderar esta mensagem que nenhum dado será ativado.")
                ->salutation("Boas leituras e seja muito bem-vindo(a)! 📚✨\n\n**Equipe Universo de Papel**");
        });

        // 2. Mensagem interativa e segura de Recuperação de Senha
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $firstName = explode(' ', trim($notifiable->name ?? 'Leitor(a)'))[0];
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject("🔐 Recuperação de Acesso - Universo de Papel")
                ->greeting("Olá, {$firstName}! 📚")
                ->line("Recebemos uma solicitação para redefinir a senha da sua conta no **Universo de Papel**.")
                ->line("Não se preocupe, vamos te ajudar a recuperar o seu acesso rapidinho para você não perder nenhuma leitura!")
                ->action("🔑 Criar Minha Nova Senha", $url)
                ->line("⏰ **Importante:** Este link de segurança expira em **60 minutos**.")
                ->line("🛡️ **Dica de Segurança:** Se não foi você quem solicitou a redefinição de senha, nenhuma ação é necessária. Sua senha atual continua segura e sua conta permanece totalmente protegida.")
                ->salutation("Conte sempre conosco! 📖✨\n\n**Equipe Universo de Papel**");
        });
    }
}
