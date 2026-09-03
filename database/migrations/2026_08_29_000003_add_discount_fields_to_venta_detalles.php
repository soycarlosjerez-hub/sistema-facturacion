<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('venta_detalles', 'descuento')) {
            Schema::table('venta_detalles', function ($table) {
                $table->decimal('descuento', 10, 2)->default(0)->after('subtotal');
            });
        }
        if (!Schema::hasColumn('venta_detalles', 'descuento_tipo')) {
            Schema::table('venta_detalles', function ($table) {
                $table->string('descuento_tipo', 20)->nullable()->default('monto')->after('descuento');
            });
        }
        if (!Schema::hasColumn('venta_detalles', 'itbis_porcentaje')) {
            Schema::table('venta_detalles', function ($table) {
                $table->decimal('itbis_porcentaje', 5, 2)->default(0)->after('descuento_tipo');
            });
        }
    }

    public function down(): void
    {
        foreach (['itbis_porcentaje', 'descuento_tipo', 'descuento'] as $col) {
            if (Schema::hasColumn('venta_detalles', $col)) {
                Schema::table('venta_detalles', function ($table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
