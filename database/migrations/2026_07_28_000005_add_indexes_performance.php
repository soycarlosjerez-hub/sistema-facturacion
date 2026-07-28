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
        // ordenes_reparacion: composite index on tenant_id + estado, and created_at
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            $table->index(['tenant_id', 'estado'], 'ordenes_reparacion_tenant_estado_idx');
            $table->index('created_at', 'ordenes_reparacion_created_at_idx');
        });

        // equipos: composite index on tenant_id + estado, and marca
        Schema::table('equipos', function (Blueprint $table) {
            $table->index(['tenant_id', 'estado'], 'equipos_tenant_estado_idx');
            $table->index('marca', 'equipos_marca_idx');
        });

        // tickets_garantia: composite index on business_instance_id + estado
        Schema::table('tickets_garantia', function (Blueprint $table) {
            $table->index(['business_instance_id', 'estado'], 'tickets_garantia_bi_estado_idx');
        });

        // detalle_pieza_reparacion: index on orden_reparacion_id
        Schema::table('detalle_pieza_reparacion', function (Blueprint $table) {
            $table->index('orden_reparacion_id', 'detalle_pieza_reparacion_orden_idx');
        });

        // almacen_movimientos: composite index on producto_id + created_at
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->index(['producto_id', 'created_at'], 'almacen_movimientos_producto_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            $table->dropIndex('ordenes_reparacion_tenant_estado_idx');
            $table->dropIndex('ordenes_reparacion_created_at_idx');
        });

        Schema::table('equipos', function (Blueprint $table) {
            $table->dropIndex('equipos_tenant_estado_idx');
            $table->dropIndex('equipos_marca_idx');
        });

        Schema::table('tickets_garantia', function (Blueprint $table) {
            $table->dropIndex('tickets_garantia_bi_estado_idx');
        });

        Schema::table('detalle_pieza_reparacion', function (Blueprint $table) {
            $table->dropIndex('detalle_pieza_reparacion_orden_idx');
        });

        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->dropIndex('almacen_movimientos_producto_created_idx');
        });
    }
};
