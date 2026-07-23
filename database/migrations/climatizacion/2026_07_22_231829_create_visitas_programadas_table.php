<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas_programadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_mantenimiento_id')->nullable()->constrained('contratos_mantenimiento')->nullOnDelete();
            $table->foreignId('mantenimiento_id')->nullable()->constrained('mantenimientos')->nullOnDelete();
            $table->date('fecha_programada');
            $table->dateTime('fecha_ejecutada')->nullable();
            $table->enum('estado', ['programada', 'completada', 'cancelada'])->default('programada');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('fecha_programada');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas_programadas');
    }
};
