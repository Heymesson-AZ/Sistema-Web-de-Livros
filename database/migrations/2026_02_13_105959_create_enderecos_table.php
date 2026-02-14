<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Criacao da tabela enderecos.
     */
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            //campos nessessarios para tabela
            $table->string('rua');
            $table->string('bairro');
            $table->string('cidade');
            $table->char('estado', 2); 
            $table->string('cep');
            $table->string('pais');
            $table->string('complemento')->nullable();
            // id do usario (foreign key)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Remocao da tabela enderecos.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');

    }
};
