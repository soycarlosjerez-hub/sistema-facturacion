<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias_internas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado', 20)->default('planificada');
            $table->unsignedBigInteger('coordinador_id')->nullable();
            $table->unsignedBigInteger('auditor_lider_id')->nullable();
            $table->text('alcance')->nullable();
            $table->text('criterios')->nullable();
            $table->text('metodologia')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->foreign('coordinador_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('auditor_lider_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias_internas');
    }
};
