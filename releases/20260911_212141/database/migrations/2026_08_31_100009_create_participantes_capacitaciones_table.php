<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capacitacion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('puntuacion', 5, 2)->nullable();
            $table->string('estado', 30)->default('inscrito');
            $table->date('fecha_evaluacion')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('archivo_certificado', 500)->nullable();
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();

            $table->index(['tenant_id', 'capacitacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes_capacitaciones');
    }
};
