<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('placa', 20)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->smallInteger('anio')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('vin', 50)->nullable();
            $table->string('tipo', 50)->default('automovil');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('placa');
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
