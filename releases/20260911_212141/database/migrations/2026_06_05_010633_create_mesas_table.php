<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->string('numero', 20);
            $table->string('nombre', 100)->nullable();
            $table->integer('capacidad')->default(4);
            $table->string('ubicacion', 100)->nullable();
            $table->string('estado', 20)->default('disponible');
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->unique(['sucursal_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
