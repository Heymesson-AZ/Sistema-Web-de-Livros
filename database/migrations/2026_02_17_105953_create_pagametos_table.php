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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            // id do pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            // metodo de pagamento
            $table->enum('metodo_pagamento', ['cartao_credito', 'boleto', 'pix','cartao_debito']);
            //status do pagamento
            $table->enum('status_pagamento', ['pendente', 'aprovado', 'rejeitado']);
            // id_transacao do gateway de pagamento
            $table->string('id_transacao')->nullable();
            //valor liquido pago
            $table->decimal('valor_pago', 10, 2);
            // data de confirmaçao do pagamento
            $table->timestamp('data_confirmacao_pagamento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagametos');
    }
};
