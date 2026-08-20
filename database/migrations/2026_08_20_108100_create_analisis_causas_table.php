<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_causas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('no_conformidad_id')->constrained('no_conformidades')->cascadeOnDelete();
            $table->text('causa_raiz');
            $table->text('metodo_analisis')->nullable()->comment('5 why, Ishikawa, etc.');
            $table->string('resultado', 255)->nullable();
            $table->text('evidencia_analisis')->nullable();
            $table->unsignedBigInteger('analista_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('analista_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_causas');
    }
};
