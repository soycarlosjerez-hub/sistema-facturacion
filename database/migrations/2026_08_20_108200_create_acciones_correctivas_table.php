<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('acciones_correctivas')) {
            return;
        }

        Schema::create('acciones_correctivas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('no_conformidad_id');
            $table->foreign('no_conformidad_id', 'fk_acr_nc_id')->references('id')->on('no_conformidades')->onDelete('cascade');
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
            $table->foreignId('documento_sgc_id')->nullable();
            $table->foreign('documento_sgc_id', 'fk_acr_dsgc_id')->references('id')->on('documentos_sgc')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('responsable_id', 'fk_acr_responsable_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por', 'fk_acr_aprobado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_correctivas');
    }
};
