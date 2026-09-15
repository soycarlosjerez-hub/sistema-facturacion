<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_satisfaccion_id')->constrained('encuestas_satisfaccion')->onDelete('cascade');
            $table->foreignId('pregunta_encuesta_id')->constrained('preguntas_encuestas')->onDelete('cascade');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('valor');
            $table->text('comentario')->nullable();
            $table->unsignedBigInteger('respondido_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_encuestas');
    }
};
