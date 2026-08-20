<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riesgos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('area', 100);
            $table->text('descripcion');
            $table->string('causa')->nullable();
            $table->text('consecuencia')->nullable();
            $table->integer('probabilidad')->default(3)->comment('1-5');
            $table->integer('impacto')->default(3)->comment('1-5');
            $table->integer('nivel')->default(9);
            $table->string('clasificacion', 20)->default('medio');
            $table->text('controles_existentes')->nullable();
            $table->text('plan_accion')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->string('estado', 20)->default('identificado');
            $table->text('plan_mitigacion')->nullable();
            $table->integer('probabilidad_residual')->nullable();
            $table->integer('impacto_residual')->nullable();
            $table->integer('nivel_residual')->default(0);
            $table->text('observaciones')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            // auditoria_id and mejora_continua_id FKs added in 2026_08_20_109200_fix_cross_references_sgc.php
            // since those tables (auditorias_internas, mejoras_continuas) are created in later migration files
            $table->unsignedBigInteger('auditoria_id')->nullable();
            $table->unsignedBigInteger('mejora_continua_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estado', 'clasificacion']);
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riesgos');
    }
};
