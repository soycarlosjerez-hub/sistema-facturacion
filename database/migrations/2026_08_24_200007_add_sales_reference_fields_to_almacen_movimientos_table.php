<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar campos de venta y línea de negocio a movimientos de almacén.
     * Permite rastrear qué movimiento de inventario proviene de una venta (alimentos, bebidas, accesorios).
     */
    public function up(): void
    {
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen_movimientos', 'venta_id')) {
                $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete()->after('detalle_compra_id');
            }

            if (!Schema::hasColumn('almacen_movimientos', 'linea_negocio')) {
                $table->enum('linea_negocio', ['alimentos', 'bebidas', 'accesorios', 'servicio'])
                    ->nullable()->after('venta_id');
            }
        });

        // Índices separados (Laravel no permite agregar índice compuesto directamente con hasIndex)
        if (!Schema::hasIndex('almacen_movimientos', ['venta_id'])) {
            Schema::table('almacen_movimientos', function (Blueprint $table) {
                $table->index('venta_id');
            });
        }

        if (!Schema::hasIndex('almacen_movimientos', ['linea_negocio'])) {
            Schema::table('almacen_movimientos', function (Blueprint $table) {
                $table->index('linea_negocio');
            });
        }
    }

    public function down(): void
    {
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('almacen_movimientos', 'linea_negocio')) {
                $table->dropColumn('linea_negocio');
            }
            if (Schema::hasColumn('almacen_movimientos', 'venta_id')) {
                $table->dropForeign(['venta_id']);
                $table->dropColumn('venta_id');
            }
        });
    }
};
