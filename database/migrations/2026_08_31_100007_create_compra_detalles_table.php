<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicada con 2025_06_06_000001 (+ alters intermedios que agregan
        // itbis/tenant/equipo): si la tabla ya existe, no recrear.
        if (Schema::hasTable('compra_detalles')) {
            return;
        }

        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 12, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('itbis_porcentaje', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();

            $table->index(['tenant_id', 'compra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_detalles');
    }
};
