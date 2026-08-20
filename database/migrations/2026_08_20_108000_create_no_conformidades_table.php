<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_conformidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('impacto')->nullable();
            $table->string('tipo', 50)->default('mayor');
            $table->string('procedencia', 50)->default('interna');
            $table->date('fecha_deteccion');
            $table->string('estado', 20)->default('abierto');
            $table->text('contencion_temporal')->nullable();
            $table->date('fecha_contencion')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->unsignedBigInteger('detector_id')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->foreignId('auditoria_id')->nullable()->constrained('auditorias_internas')->onDelete('set null');
            $table->foreignId('mejora_continua_id')->nullable()->constrained('mejoras_continuas')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asignado_a')->references('id')->on('users')->onDelete('set null');
            $table->foreign('detector_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_conformidades');
    }
};
