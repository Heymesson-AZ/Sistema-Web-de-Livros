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
        Schema::table('cupons', function (Blueprint $table) {
            $table->foreignId('vendedor_id')->nullable()->after('id')->constrained('vendedores')->nullOnDelete();
            $table->string('tipo_origem')->default('plataforma')->after('vendedor_id'); // 'plataforma' ou 'loja'
            $table->boolean('requer_concordancia')->default(false)->after('validade_cupom'); // campanhas promocionais de incentivo do admin
        });

        Schema::create('cupom_vendedor_adesao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupom_id')->constrained('cupons')->cascadeOnDelete();
            $table->foreignId('vendedor_id')->constrained('vendedores')->cascadeOnDelete();
            $table->boolean('concordou')->default(true);
            $table->timestamp('data_adesao')->nullable();
            $table->unique(['cupom_id', 'vendedor_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupom_vendedor_adesao');

        Schema::table('cupons', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn(['vendedor_id', 'tipo_origem', 'requer_concordancia']);
        });
    }
};

