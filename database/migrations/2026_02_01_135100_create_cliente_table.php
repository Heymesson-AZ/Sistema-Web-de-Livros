<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
        {
            Schema::create('cliente', function (Blueprint $table) {
                $table->id();
                // chave estrangeira do usuario (1 para 1)
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                // cpf do cliente
                $table->string('cpf')->unique();
                // telefone do cliente
                $table->string('celular_contato')->nullable();
                // data de nascimento do cliente
                $table->date('data_nascimento');
                // o timestamp de criação e atualização do cliente
                $table->timestamps();
                // soft deletes para o cliente
                $table->softDeletes();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
