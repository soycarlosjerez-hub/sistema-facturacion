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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // usuario que realiza la venta
            $table->foreignId('cliente_id')->nullable()->constrained()->nullOnDelete();  // cliente, puede ser null (venta sin cliente registrado)
            $table->foreignId('tipo_venta_id')->constrained('tipos_ventas')->onDelete('restrict'); // Tipo de venta
            $table->dateTime('fecha')->default(now());
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->integer('ventas_count')->default(0);
            $table->string('estado')->default('pendiente'); // pendiente, anulada, completada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
