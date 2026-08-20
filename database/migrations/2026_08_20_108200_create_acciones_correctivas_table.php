<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acciones_correctivas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('no_conformidad_id')->constrained('no_conformidades')->cascadeOnDelete();
            $table->string('titulo_accion', 255);
            $table->text('descripcion_accion');
            $table->date('fecha_asignacion');
            $table->date('fecha_limite')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->date('fecha_ejecucion')->nullable();
            $table->text('evidencia_ejecucion')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_correctivas');
    }
};
