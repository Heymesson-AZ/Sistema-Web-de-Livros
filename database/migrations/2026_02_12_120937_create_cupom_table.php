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
        Schema::create('cupons', function (Blueprint $table) {
            $table->id();
            //codigo do cupom
            $table->string('codigo')->unique();
            //tipo de desconto: percentual ou valor fixo
            $table->enum('tipo_desconto', ['percentual', 'valor_fixo']);
            // valor do desconto
            $table->decimal('valor_desconto', 8, 2);
            //limite de uso do cupom
            $table->integer('limite_uso')->nullable();
            // data de validade do cupom
            $table->date('validade_cupom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupons');
    }
};
