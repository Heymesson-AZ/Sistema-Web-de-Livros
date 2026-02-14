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
            Schema::create('vendedor', function (Blueprint $table) {
                $table->id();
                // chave estrageira do usuario
                $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
                // cnpj do vendedor
                $table->string('cnpj')->unique();
                // telefone do vendedor
                $table->string('telefone');
                // razao social do vendedor
                $table->string('razao_social');
                // nome fantasia do vendedor
                $table->string('nome_fantasia');
                // iscricao estadual do vendedor
                $table->string('inscricao_estadual');
                //status de aprovação do vendedor
                $table->enum('status_aprovacao', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
                // o timestamp de criação e atualização do vendedor
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendedor');
    }
};
