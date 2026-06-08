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
        Schema::create('ncf_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Crédito Fiscal, Consumo, etc.
            $table->string('prefijo', 3); // B01, B02
            $table->unsignedBigInteger('desde');
            $table->unsignedBigInteger('hasta');
            $table->unsignedBigInteger('actual');
            $table->date('fecha_vencimiento');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncf_sequences');
    }
};
