<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participante_capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->cascadeOnDelete();
            $table->unsignedBigInteger('usuario_id');
            $table->integer('puntuacion')->nullable()->comment('0-100');
            $table->string('estado', 20)->default('inscritos');
            $table->date('fecha_evaluacion')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('archivo_certificado')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participante_capacitaciones');
    }
};
