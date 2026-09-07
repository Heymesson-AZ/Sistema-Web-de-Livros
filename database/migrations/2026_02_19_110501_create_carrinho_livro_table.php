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
        Schema::create('carrinho_livro', function (Blueprint $table) {
            $table->id();

            // Chave para o Carrinho
            $table->foreignId('carrinho_id')->constrained('carrinhos')->onDelete('cascade');

            // Chave para o Livro
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');

            // Quantidade de exemplares no carrinho
            $table->unsignedInteger('quantidade')->default(1);

            // Garante que cada livro só apareça uma vez no mesmo carrinho
            $table->unique(['carrinho_id', 'livro_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrinho_livro');
    }
};
