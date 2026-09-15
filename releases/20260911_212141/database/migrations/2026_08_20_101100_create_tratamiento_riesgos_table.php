<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamiento_riesgos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riesgo_id')->constrained('riesgos')->cascadeOnDelete();
            $table->string('tipo_accion', 50)->default('mitigar');
            $table->text('descripcion_accion');
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->string('status', 20)->default('pendiente');
            $table->text('resultados')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->timestamps();

            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamiento_riesgos');
    }
};
