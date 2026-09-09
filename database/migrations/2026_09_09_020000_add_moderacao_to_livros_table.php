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
        Schema::table('livros', function (Blueprint $table) {
            $table->enum('status_moderacao', ['ativo', 'sob_analise', 'bloqueado_temporariamente', 'banido'])
                ->default('ativo')
                ->after('quantidade');
            $table->text('motivo_moderacao')->nullable()->after('status_moderacao');
            $table->timestamp('moderado_em')->nullable()->after('motivo_moderacao');
            $table->foreignId('moderado_por')->nullable()->after('moderado_em')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropForeign(['moderado_por']);
            $table->dropColumn(['status_moderacao', 'motivo_moderacao', 'moderado_em', 'moderado_por']);
        });
    }
};

