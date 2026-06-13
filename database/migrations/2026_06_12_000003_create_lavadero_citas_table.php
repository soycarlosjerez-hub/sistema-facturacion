<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lavadero_citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->dateTime('fecha_hora');
            $table->string('servicio', 200)->nullable();
            $table->string('estado', 50)->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('fecha_hora');
            $table->index('estado');
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadero_citas');
    }
};
