<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('programas_auditoria')) {
            return;
        }

        Schema::create('programas_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_interna_id')->constrained('auditorias_internas', 'fk_pa_ai_id')->cascadeOnDelete();
            $table->string('area_auditada', 100);
            $table->text('criterios_auditoria')->nullable();
            $table->unsignedBigInteger('auditor_id')->nullable();
            $table->date('fecha');
            $table->string('resultado', 50)->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('auditor_id', 'fk_pa_auditor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por', 'fk_pa_creado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_auditoria');
    }
};
