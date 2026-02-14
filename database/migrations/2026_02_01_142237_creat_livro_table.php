<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * A função (Up) que será executada quando a migration for aplicada.
     */
    public function up(): void
    {
        // Estrutura da Tabela 'livro' a ser criada
        Schema::create('livro', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('titulo'); // Título do livro
            $table->date('data_publicacao'); // Data de publicação do livro
            $table->string('isbn')->unique(); // ISBN único para cada livro
            $table->text('sinopse')->nullable(); // Sinopse do livro, campo de texto que pode ser nulo
            $table->string('capa')->nullable(); // URL ou caminho da capa do livro
            $table->decimal('preco', 8, 2); // Preço do livro, com 8 dígitos no total e 2 casas decimais
            $table->integer('quantidade')->default(0); // Quantidade em estoque, valor padrão 0
             // Definindo a chave estrangeira para 'autor_id'
            $table->foreignId('autor_id')->constrained('autor')->onDelete('cascade');
            $table->foreignId('genero_id')->constrained('genero'); // Chave estrangeira referenciando o gênero do livro
            $table->foreignId('editora_id')->constrained('editora'); // Chave estrangeira referenciando a editora do livro
            $table->timestamps(); // Campos 'created_at' e 'updated_at'
        });
    }

  /**
     * A função down será executada quando a migration for revertida.
     */
    public function down(): void
    {
        // Removendo a tabela 'livro' caso a migration seja revertida
        Schema::dropIfExists('livro');
    }
};
