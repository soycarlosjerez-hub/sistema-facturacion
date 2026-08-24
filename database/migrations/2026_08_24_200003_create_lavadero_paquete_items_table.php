<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Items que componen cada paquete de lavadero.
     * Cada item puede ser un servicio de lavadero, un producto (alimento/bebida/accesorio), o ambos.
     */
    public function up(): void
    {
        Schema::create('lavadero_paquete_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paquete_id')->constrained('lavadero_paquetes')->cascadeOnDelete();
            $table->enum('tipo', ['servicio', 'producto']);
            $table->foreignId('servicio_id')->nullable()->constrained('lavadero_servicios')->nullOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_individual', 12, 2)->nullable();
            $table->boolean('incluir_automatico')->default(true);
            $table->smallInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['paquete_id', 'tipo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadero_paquete_items');
    }
};
