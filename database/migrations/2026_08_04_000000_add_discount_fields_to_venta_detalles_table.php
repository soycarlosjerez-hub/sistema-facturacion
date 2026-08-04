<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_detalles', 'descuento')) {
                $table->decimal('descuento', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('venta_detalles', 'descuento_tipo')) {
                $table->string('descuento_tipo', 20)->nullable()->default('monto')->after('descuento');
            }
            if (!Schema::hasColumn('venta_detalles', 'itbis_porcentaje')) {
                $table->decimal('itbis_porcentaje', 5, 2)->default(0)->after('descuento_tipo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'itbis_porcentaje')) {
                $table->dropColumn('itbis_porcentaje');
            }
            if (Schema::hasColumn('venta_detalles', 'descuento_tipo')) {
                $table->dropColumn('descuento_tipo');
            }
            if (Schema::hasColumn('venta_detalles', 'descuento')) {
                $table->dropColumn('descuento');
            }
        });
    }
};
