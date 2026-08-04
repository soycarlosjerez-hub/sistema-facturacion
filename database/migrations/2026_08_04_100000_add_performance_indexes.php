<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->index('created_at', 'idx_ventas_created_at');
            $table->index('sucursal_id', 'idx_ventas_sucursal_id');
            $table->index(['sucursal_id', 'created_at'], 'idx_ventas_sucursal_created');
            $table->index('cliente_id', 'idx_ventas_cliente_id');
            $table->index('estado', 'idx_ventas_estado');
            $table->index('user_id', 'idx_ventas_user_id');
            $table->index('caja_id', 'idx_ventas_caja_id');
            $table->index('ncf_tipo', 'idx_ventas_ncf_tipo');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->index('fecha', 'idx_compras_fecha');
            $table->index('sucursal_id', 'idx_compras_sucursal_id');
            $table->index(['sucursal_id', 'fecha'], 'idx_compras_sucursal_fecha');
            $table->index('proveedor_id', 'idx_compras_proveedor_id');
            $table->index(['retencion_isr', 'retencion_itbis'], 'idx_compras_retenciones');
            $table->index('tipo_compra_id', 'idx_compras_tipo');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->index('venta_id', 'idx_pagos_venta_id');
            $table->index('metodo_pago', 'idx_pagos_metodo_pago');
            $table->index(['venta_id', 'metodo_pago'], 'idx_pagos_venta_metodo');
        });

        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->index('venta_id', 'idx_venta_detalles_venta_id');
            $table->index('producto_id', 'idx_venta_detalles_producto_id');
            $table->index(['venta_id', 'producto_id'], 'idx_venta_detalles_composite');
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->index('compra_id', 'idx_compra_detalles_compra_id');
            $table->index('producto_id', 'idx_compra_detalles_producto_id');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->index('stock', 'idx_productos_stock');
            $table->index(['stock', 'stock_minimo'], 'idx_productos_stock_minimo');
            $table->index('categoria_id', 'idx_productos_categoria_id');
            $table->index('activo', 'idx_productos_activo');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('rnc_cedula', 'idx_clientes_rnc_cedula');
            $table->index('tipo_cliente', 'idx_clientes_tipo_cliente');
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->index('rnc', 'idx_proveedores_rnc');
            $table->index('tenant_id', 'idx_proveedores_tenant_id');
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->index('fecha', 'idx_gastos_fecha');
            $table->index('categoria_id', 'idx_gastos_categoria_id');
            $table->index('sucursal_id', 'idx_gastos_sucursal_id');
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->index('sucursal_id', 'idx_cajas_sucursal_id');
            $table->index('estado', 'idx_cajas_estado');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_created_at');
            $table->dropIndex('idx_ventas_sucursal_id');
            $table->dropIndex('idx_ventas_sucursal_created');
            $table->dropIndex('idx_ventas_cliente_id');
            $table->dropIndex('idx_ventas_estado');
            $table->dropIndex('idx_ventas_user_id');
            $table->dropIndex('idx_ventas_caja_id');
            $table->dropIndex('idx_ventas_ncf_tipo');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex('idx_compras_fecha');
            $table->dropIndex('idx_compras_sucursal_id');
            $table->dropIndex('idx_compras_sucursal_fecha');
            $table->dropIndex('idx_compras_proveedor_id');
            $table->dropIndex('idx_compras_retenciones');
            $table->dropIndex('idx_compras_tipo');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex('idx_pagos_venta_id');
            $table->dropIndex('idx_pagos_metodo_pago');
            $table->dropIndex('idx_pagos_venta_metodo');
        });

        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->dropIndex('idx_venta_detalles_venta_id');
            $table->dropIndex('idx_venta_detalles_producto_id');
            $table->dropIndex('idx_venta_detalles_composite');
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->dropIndex('idx_compra_detalles_compra_id');
            $table->dropIndex('idx_compra_detalles_producto_id');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_stock');
            $table->dropIndex('idx_productos_stock_minimo');
            $table->dropIndex('idx_productos_categoria_id');
            $table->dropIndex('idx_productos_activo');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_rnc_cedula');
            $table->dropIndex('idx_clientes_tipo_cliente');
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropIndex('idx_proveedores_rnc');
            $table->dropIndex('idx_proveedores_tenant_id');
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->dropIndex('idx_gastos_fecha');
            $table->dropIndex('idx_gastos_categoria_id');
            $table->dropIndex('idx_gastos_sucursal_id');
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropIndex('idx_cajas_sucursal_id');
            $table->dropIndex('idx_cajas_estado');
        });
    }
};
