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
        Schema::create('carrinhos', function (Blueprint $table) {
            $table->id();
            // Referência ao cliente (quem é o dono deste carrinho)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            // Em vez disso, aumentamos a 'quantidade'
            $table->unique(['user_id', 'livro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrinho');
    }
};
