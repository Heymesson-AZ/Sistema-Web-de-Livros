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
        Schema::create('cartoes_salvos', function (Blueprint $table) {
            $table->id();
            // referencia ao usuario dono do cartao
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // token cogigo devolvido pelo Gateway (stripe, pagseguro, etc)
            $table->string('token_cartao');
            // ultimos quatro digitos
            $table->string('ultimos_digitos', 4);
            // bandeira do cartao (visa, mastercard, etc)
            $table->string('bandeira_cartao');
            // campo booleano para indicar se o cartao é o padrao do usuario
            $table->boolean('cartao_padrao')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartoes_salvos');
    }
};
