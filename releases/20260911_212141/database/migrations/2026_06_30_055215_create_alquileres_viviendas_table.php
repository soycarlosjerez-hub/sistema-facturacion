<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres_viviendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tipo')->default('apartamento');
            $table->unsignedTinyInteger('habitaciones')->default(0);
            $table->unsignedTinyInteger('banos')->default(0);
            $table->decimal('area_m2', 8, 2)->nullable();
            $table->decimal('monto_alquiler', 10, 2)->default(0);
            $table->decimal('monto_deposito', 10, 2)->default(0);
            $table->string('estado')->default('disponible');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres_viviendas');
    }
};
