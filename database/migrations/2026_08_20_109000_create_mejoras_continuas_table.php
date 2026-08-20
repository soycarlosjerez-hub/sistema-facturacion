<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mejoras_continuas')) {
            return;
        }

        Schema::create('mejoras_continuas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->text('objetivo');
            $table->text('solucion_propuesta')->nullable();
            $table->text('beneficios_esperados')->nullable();
            $table->string('prioridad', 20)->default('media');
            $table->string('estado', 20)->default('propuesta');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_completada')->nullable();
            $table->text('resultados')->nullable();
            $table->text('evidencias')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->unsignedBigInteger('proponente_id')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->string('vinculada_a', 50)->nullable()->comment('riesgo, nc, mejora, auditoria');
            $table->unsignedBigInteger('vinculado_id')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc', 'fk_mcc_dsgc_id')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('responsable_id', 'fk_mcc_responsable_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('proponente_id', 'fk_mcc_proponente_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por', 'fk_mcc_aprobado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejoras_continuas');
    }
};
