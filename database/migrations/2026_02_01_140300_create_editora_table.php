<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * A função (Up) que será executada quando a migration for aplicada.
     */
    public function up(): void
    {
        Schema::create('editoras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });
    }

    /**
     * A função down será executada quando a migration for revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('editoras');
    }
};
