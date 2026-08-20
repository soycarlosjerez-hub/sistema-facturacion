<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propuestas_mejora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mejora_continua_id')->constrained('mejoras_continuas')->cascadeOnDelete();
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->text('accion_realizada');
            $table->text('resultado')->nullable();
            $table->text('evidencia')->nullable();
            $table->decimal('ahorro_estimado', 12, 2)->nullable();
            $table->date('fecha_propuesta');
            $table->date('fecha_implementacion')->nullable();
            $table->string('estado', 20)->default('propuesta');
            $table->unsignedBigInteger('proponen_por')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('proponen_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propuestas_mejora');
    }
};
