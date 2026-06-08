<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sesion_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 12, 2)->default(0);
            $table->decimal('ventas_efectivo', 12, 2)->default(0);
            $table->decimal('ventas_tarjeta', 12, 2)->default(0);
            $table->decimal('ventas_transferencia', 12, 2)->default(0);
            $table->decimal('monto_declarado', 12, 2)->nullable();
            $table->decimal('descuadre', 12, 2)->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesion_cajas');
    }
};
