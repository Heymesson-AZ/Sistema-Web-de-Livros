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
        Schema::create('pedido_entregas', function (Blueprint $table) {
            $table->id();
            // foreign key para pedidos
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            //status da entrega
            $table->enum('status', ['pendente', 'em_transito', 'entregue', 'cancelada'])->default('pendente');
            // codigo de rastreamento
            $table->string('codigo_rastreamento')->nullable();
            // url da nota fiscal
            $table->string('url_nota_fiscal')->nullable();
            // data de previsao de entrega
            $table->date('data_previsao_entrega')->nullable();
            // data de entrega (quando for entregue)
            $table->date('data_entrega')->nullable();
            // nome do recebedor
            $table->string('nome_recebedor')->nullable();
            // cpf do recebedor
            $table->string('cpf_recebedor')->nullable();
            // valor do frete
            $table->decimal('valor_frete', 8, 2)->nullable();
            // metodo de envio
            $table->enum('metodo_envio', ['correios', 'transportadora'])->default('correios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_entregas');
    }
};
