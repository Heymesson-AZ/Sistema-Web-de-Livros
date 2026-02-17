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
        Schema::create('pedidos', function (Blueprint $table) {
            // Campos nessesarios para a tabela 'pedidos'
            $table->id();
            $table->timestamps();   
            // numero do pedido
            $table->string('numero_pedido')->unique();
            // status do pedido
            $table->enum('status', ['pendente', 'processando', 'enviado', 'entregue', 'cancelado', 'devolvido'])->default('pendente');
            // total do pedido
            $table->decimal('total', 10, 2);
            // data do pedido
            $table->dateTime('data_pedido');
            // referencia ao usuario que fez o pedido
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // referencia ao cupom utilizado no pedido, se houver
            $table->foreignId('cupom_id')->nullable()->constrained('cupons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
