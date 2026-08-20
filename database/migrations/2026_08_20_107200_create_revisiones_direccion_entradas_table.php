<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisiones_direccion_entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_direccion_id')->constrained('revisiones_direccion')->cascadeOnDelete();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->text('documento_referencia')->nullable();
            $table->integer('orden')->default(0);
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_direccion_entradas');
    }
};
