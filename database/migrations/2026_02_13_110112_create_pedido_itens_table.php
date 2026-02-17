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
        Schema::create('pedidos_itens', function (Blueprint $table) {
            $table->id();
             // quantidade de itens no pedido
            $table->integer('quantidade_itens');
            // valor unitarios do itens do pedido
            $table->decimal('valor_unitario', 10, 2);
            // id do livro
            $table->foreignId('livro_id')->constrained('livros')->onDelete('restrict');
            // id do pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('restrict');
            //timestamps para controle de criação e atualização dos registros
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_itens');
    }
};
