<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->decimal('precio_mensual', 10, 2);
            $table->decimal('precio_implementacion', 10, 2)->nullable();
            $table->decimal('precio_lanzamiento', 10, 2)->nullable();
            $table->integer('max_usuarios')->nullable();
            $table->integer('max_sucursales')->nullable();
            $table->integer('max_empresas')->nullable();
            $table->json('features')->nullable();
            $table->json('modulos')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('recomendado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
