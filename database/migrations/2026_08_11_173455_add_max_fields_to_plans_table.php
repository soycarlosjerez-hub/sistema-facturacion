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
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('max_almacenes')->nullable()->after('max_empresas');
            $table->integer('max_productos')->nullable()->after('max_almacenes');
            $table->integer('max_clientes')->nullable()->after('max_productos');
            $table->integer('max_proveedores')->nullable()->after('max_clientes');
            $table->integer('max_ventas_mensuales')->nullable()->after('max_proveedores');
            $table->integer('max_compras_mensuales')->nullable()->after('max_ventas_mensuales');
            $table->integer('max_gastos_mensuales')->nullable()->after('max_compras_mensuales');
            $table->integer('max_cajas')->nullable()->after('max_gastos_mensuales');
            $table->integer('max_cotizaciones_mensuales')->nullable()->after('max_cajas');
            $table->integer('max_conduces_mensuales')->nullable()->after('max_cotizaciones_mensuales');
            $table->integer('max_devoluciones_mensuales')->nullable()->after('max_conduces_mensuales');
            $table->integer('max_ordenes_mensuales')->nullable()->after('max_devoluciones_mensuales');
            $table->integer('max_mesas')->nullable()->after('max_ordenes_mensuales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'max_almacenes',
                'max_productos',
                'max_clientes',
                'max_proveedores',
                'max_ventas_mensuales',
                'max_compras_mensuales',
                'max_gastos_mensuales',
                'max_cajas',
                'max_cotizaciones_mensuales',
                'max_conduces_mensuales',
                'max_devoluciones_mensuales',
                'max_ordenes_mensuales',
                'max_mesas',
            ]);
        });
    }
};
