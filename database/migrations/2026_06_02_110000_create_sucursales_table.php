<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 255);
            $table->string('direccion', 500)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('rnc', 20)->nullable();
            $table->boolean('activa')->default(true);
            $table->boolean('es_matriz')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
