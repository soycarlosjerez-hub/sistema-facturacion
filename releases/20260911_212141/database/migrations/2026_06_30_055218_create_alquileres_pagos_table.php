<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('contrato_id')->constrained('alquileres_contratos')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->unsignedTinyInteger('mes_cobrado');
            $table->unsignedSmallInteger('ano_cobrado');
            $table->string('metodo_pago')->default('efectivo');
            $table->string('recibo_numero')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres_pagos');
    }
};
