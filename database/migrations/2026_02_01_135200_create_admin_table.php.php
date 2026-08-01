<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('telefone_urgencia')->nullable();

            // Valores padrão usando as constantes do Model
            $table->string('cargo')->default(\App\Models\Admin::CARGO_SUPER_ADMIN);
            $table->string('departamento')->default(\App\Models\Admin::DEPARTAMENTO_TECNOLOGIA);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
