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
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            // id do usuário que favoritou o produto
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // id do livro que foi favoritado
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            //  garanti que um usuário só possa favoritar um livro uma vez
            $table->unique(['user_id', 'livro_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
