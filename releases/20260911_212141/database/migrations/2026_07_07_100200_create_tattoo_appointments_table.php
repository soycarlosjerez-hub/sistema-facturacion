<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tattoo_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('artista_id')->nullable()->constrained('tattoo_artists')->nullOnDelete();
            $table->foreignId('diseno_id')->nullable()->constrained('tattoo_designs')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_hora_inicio');
            $table->dateTime('fecha_hora_fin')->nullable();
            $table->unsignedSmallInteger('duracion_min')->default(60);
            $table->string('estado', 30)->default('pendiente');
            $table->decimal('deposito_monto', 10, 2)->default(0);
            $table->decimal('deposito_pct', 5, 2)->default(30.00);
            $table->boolean('deposito_pagado')->default(false);
            $table->string('metodo_deposito', 20)->nullable();
            $table->decimal('total_servicio', 10, 2)->default(0);
            $table->decimal('descuento_aplicado', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2)->default(0);
            $table->text('notas_cliente')->nullable();
            $table->text('notas_internas')->nullable();
            $table->string('lugar_tatuaje', 60)->nullable();
            $table->string('tamanio_approx', 50)->nullable();
            $table->boolean('revision_previa')->default(false);
            $table->boolean('revision_completada')->default(false);
            $table->dateTime('revision_fecha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tattoo_appointments');
    }
};
