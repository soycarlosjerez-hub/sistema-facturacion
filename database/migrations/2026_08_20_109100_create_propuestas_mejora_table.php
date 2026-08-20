<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('propuestas_mejora')) {
            return;
        }

        Schema::create('propuestas_mejora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mejora_continua_id');
            $table->foreign('mejora_continua_id', 'fk_pm_mc_id')->references('id')->on('mejoras_continuas')->onDelete('cascade');
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

            $table->foreign('proponen_por', 'fk_pm_proponen_por_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por', 'fk_pm_aprobado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propuestas_mejora');
    }
};
