<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secuencias_ecf', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('tipo_ecf', 3)->comment('E31, E32, E33, E34, E41, E43, E44, E45, E46, E47');
            $table->unsignedBigInteger('desde')->default(1);
            $table->unsignedBigInteger('hasta');
            $table->unsignedBigInteger('actual')->default(0);
            $table->date('fecha_vencimiento');
            $table->boolean('activo')->default(true);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            $table->unique('tipo_ecf');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secuencias_ecf');
    }
};
