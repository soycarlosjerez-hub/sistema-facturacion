<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preguntas_encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_satisfaccion_id')->constrained('encuestas_satisfaccion')->cascadeOnDelete();
            $table->text('texto');
            $table->string('tipo', 50)->default('escala_5');
            $table->integer('orden')->default(0);
            $table->boolean('obligatoria')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preguntas_encuestas');
    }
};
