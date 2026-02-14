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
        Schema::create('avalicaoes', function (Blueprint $table) {
            $table->id();
            // chave estrangeira do vendedor
            $table->foreignId('vendedor_id')->constrained('vendedor')->onDelete('cascade');
            // chave estrageira do cliente
            $table->foreignId('cliente_id')->constrained('cliente')->onDelete('cascade');
            // chave estrangeira do pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            // campo de avaliação, pode ser um número de 1 a 5
            $table->integer('avaliacao')->unsigned()->default(5);
            // campo de comentário, pode ser nulo
            $table->text('comentario')->nullable();
            //recomenda ou não o vendedor, pode ser nulo
            $table->boolean('recomenda')->nullable();
            // timestamps para registrar quando a avaliação foi criada e atualizada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avalicaoes');
    }
};
