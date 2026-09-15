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
        if (Schema::hasTable('detalle_pieza_reparacion')) {
            return;
        }
        Schema::create('detalle_pieza_reparacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('costo_unitario', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pieza_reparacion');
    }
};
