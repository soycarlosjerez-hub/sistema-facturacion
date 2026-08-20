<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competencias_empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('puesto', 100);
            $table->string('competencia', 100);
            $table->integer('nivel')->default(1)->comment('1-5');
            $table->date('fecha_evaluacion')->nullable();
            $table->string('evidencia_tipo', 50)->nullable();
            $table->string('archivo_evidencia')->nullable();
            $table->unsignedBigInteger('evaluado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('evaluado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competencias_empleados');
    }
};
