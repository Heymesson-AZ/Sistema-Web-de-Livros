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
        Schema::table('enderecos', function (Blueprint $table) {
            if (!Schema::hasColumn('enderecos', 'tipo')) {
                $table->string('tipo', 30)->default('casa')->after('complemento');
            }
            if (!Schema::hasColumn('enderecos', 'principal')) {
                $table->boolean('principal')->default(false)->after('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enderecos', function (Blueprint $table) {
            if (Schema::hasColumn('enderecos', 'principal')) {
                $table->dropColumn('principal');
            }
            if (Schema::hasColumn('enderecos', 'tipo')) {
                $table->dropColumn('tipo');
            }
        });
    }
};

