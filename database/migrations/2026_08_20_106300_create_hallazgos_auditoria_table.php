<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hallazgos_auditoria')) {
            return;
        }

        Schema::create('hallazgos_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_interna_id');
            $table->foreign('auditoria_interna_id', 'fk_ha_ai_id')->references('id')->on('auditorias_internas')->onDelete('cascade');
            $table->string('descripcion');
            $table->text('evidencia_objetiva')->nullable();
            $table->string('tipo', 50)->default('no_conformidad');
            $table->string('gravedad', 20)->default('menor');
            $table->text('referencia_normativa')->nullable();
            $table->string('estado', 20)->default('abierto');
            $table->text('accion_correctiva')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('asignado_a', 'fk_ha_asignado_a_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por', 'fk_ha_creado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hallazgos_auditoria');
    }
};
