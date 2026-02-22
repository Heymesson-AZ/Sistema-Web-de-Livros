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
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');

            // --- DADOS DO ENDEREÇO (Snapshot para Segurança) ---
            $table->string('rua');
            $table->string('bairro');
            $table->string('cidade');
            $table->char('estado', 2);
            $table->string('cep');
            $table->string('pais');
            $table->string('complemento')->nullable();

            // --- DADOS LOGÍSTICOS  ---
            $table->enum('status', ['pendente', 'em_transito', 'entregue', 'cancelada'])->default('pendente');
            $table->string('codigo_rastreamento')->nullable();
            $table->string('url_nota_fiscal')->nullable();
            $table->date('data_previsao_entrega')->nullable();
            $table->date('data_entrega')->nullable();
            $table->string('nome_recebedor')->nullable();
            $table->string('cpf_recebedor')->nullable();
            $table->decimal('valor_frete', 8, 2)->nullable();
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
