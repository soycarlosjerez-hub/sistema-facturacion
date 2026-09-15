<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres_contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('vivienda_id')->constrained('alquileres_viviendas')->cascadeOnDelete();
            $table->foreignId('inquilino_id')->constrained('alquileres_inquilinos')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('monto_alquiler', 10, 2);
            $table->decimal('monto_deposito', 10, 2)->default(0);
            $table->unsignedTinyInteger('dia_pago')->default(1);
            $table->string('estado')->default('activo');
            $table->boolean('deposito_pagado')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres_contratos');
    }
};
