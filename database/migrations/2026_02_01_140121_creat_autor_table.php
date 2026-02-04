<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A função que será executada quando a migration for aplicada.
     */
    public function up(): void
    {   
        // Criando a tabela 'autor' com os campos especificados
        // blueprint é usado para definir a estrutura da tabela

        Schema::create('autor', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('nome'); // Nome do autor
            $table->string('nacionalidade')->nullable(); // 'nullable' permite que o campo fique vazio
            $table->date('data_nascimento'); // Campo específico para datas
            $table->string('email')->unique(); // Campo de email único
            $table->string('biografia', 1000)->nullable(); // Campo de biografia com limite de 1000 caracteres
            $table->string('foto_perfil')->nullable(); // Campo para URL ou caminho da foto de perfil
            $table->timestamps(); // Campos 'created_at' e 'updated_at'
        });
    }

    /**
     * A função down será executada quando a migration for revertida.
     */

    public function down(): void
    {
        // Removendo a tabela 'autor' caso a migration seja revertida
        Schema::dropIfExists('autor');
    }
};
