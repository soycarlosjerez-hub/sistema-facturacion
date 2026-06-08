<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->text('motivo');
            $table->enum('tipo', ['parcial', 'total'])->default('parcial');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['borrador', 'completada', 'anulada'])->default('borrador');
            $table->foreignId('nota_credito_id')->nullable()->constrained('ecf_documentos')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detalles_devolucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')->constrained('devoluciones')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('itbis_porcentaje', 5, 2)->default(18);
            $table->decimal('subtotal', 12, 2);
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_devolucion');
        Schema::dropIfExists('devoluciones');
    }
};
