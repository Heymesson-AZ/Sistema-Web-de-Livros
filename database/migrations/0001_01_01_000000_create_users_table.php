<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criando as tabelas de usuários, tokens de redefinição de senha e sessões.
     */
    public function up(): void
    {   
        // Tabela para armazenar os usuários
        Schema::create('users', function (Blueprint $table) {
            // Campos básicos de um usuário
            // O campo 'id' é a chave primária auto-incrementada
            $table->id();
            // O campo 'name' armazena o nome do usuário
            $table->string('name');
            // O campo 'email' armazena o email do usuário e deve ser único
            $table->string('email')->unique();
            // O campo 'email_verified_at' armazena a data e hora da verificação do email, pode ser nulo
            $table->timestamp('email_verified_at')->nullable();
            // O campo 'password' armazena a senha do usuário
            $table->string('password');
            // O campo 'tipo' armazena o tipo de usuário, que pode ser 'cliente', 'vendedor' ou 'admin', com valor padrão 'cliente'
            $table->enum('tipo', ['cliente', 'vendedor', 'admin'])->default('cliente');
            // O campo 'status' armazena o status do usuário, que pode ser 'ativo', 'inativo' ou 'banido', com valor padrão 'ativo'
            $table->enum('status', ['ativo', 'inativo', 'banido'])->default('ativo');
            // O campo 'remember_token' é usado para lembrar a sessão do usuário
            $table->rememberToken();
            // Os campos 'created_at' e 'updated_at' são timestamps que registram quando o usuário foi criado e atualizado
            $table->timestamps();
            // O campo 'deleted_at' é usado para soft deletes, permitindo que os usuários sejam "excluídos" sem serem removidos do banco de dados
            $table->softDeletes();
        });
        
        // Tabela para armazenar tokens de redefinição de senha
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // O campo 'email' é a chave primária e armazena o email do usuário para quem o token foi gerado
            $table->string('email')->primary();
            // O campo 'token' armazena o token de redefinição de senha
            $table->string('token');
            // O campo 'created_at' armazena a data e hora em que o token foi criado, pode ser nulo
            $table->timestamp('created_at')->nullable();
        });
        // Tabela para armazenar sessões de usuários
        Schema::create('sessions', function (Blueprint $table) {
            //  O campo 'id' é a chave primária e armazena o identificador único da sessão
            $table->string('id')->primary();
            // O campo 'user_id' é uma chave estrangeira que referencia o usuário associado à sessão, pode ser nulo e é indexado para melhorar a performance das consultas
            $table->foreignId('user_id')->nullable()->index();
            // O campo 'ip_address' armazena o endereço IP do usuário durante a sessão, com um comprimento máximo de 45 caracteres para acomodar endereços IPv6, pode ser nulo
            $table->string('ip_address', 45)->nullable();
            // O campo 'user_agent' armazena informações sobre o navegador e sistema operacional do usuário, pode ser nulo
            $table->text('user_agent')->nullable();
            // O campo 'payload' armazena os dados da sessão em formato de texto longo
            $table->longText('payload');
            // O campo 'last_activity' armazena a data e hora da última atividade do usuário na sessão, é indexado para melhorar a performance das consultas
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Revertendo as migrações, removendo as tabelas criadas.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
