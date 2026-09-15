<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visita_programada', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_mantenimiento_id')->nullable();
            $table->unsignedBigInteger('mantenimiento_id')->nullable();
            $table->date('fecha_programada');
            $table->date('fecha_ejecutada')->nullable();
            $table->string('estado', 30)->default('programada');
            $table->text('observaciones')->nullable();
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();

            $table->index(['tenant_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visita_programada');
    }
};
