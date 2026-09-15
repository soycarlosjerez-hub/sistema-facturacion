<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('lugar')->nullable();
            $table->string('modalidad', 50)->default('presencial');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('instructor_nombre', 100)->nullable();
            $table->unsignedBigInteger('duracion_horas')->default(2);
            $table->text('temas')->nullable();
            $table->string('estado', 20)->default('programada');
            $table->string('archivo_evidencia')->nullable();
            $table->string('archivo_certificado')->nullable();
            $table->unsignedBigInteger('evaluacion_calificacion')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitaciones');
    }
};
