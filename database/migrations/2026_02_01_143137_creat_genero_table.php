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
        // Criando a tabela 'genero' com os campos especificados
        Schema::create('genero', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('nome'); // Nome do gênero
            $table->timestamps(); // Campos 'created_at' e 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Removendo a tabela 'genero' caso a migration seja revertida
        Schema::dropIfExists('genero');
    }
};
