<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            if (!Schema::hasColumn('compras', 'almacen_id')) {
                $table->foreignId('almacen_id')->nullable()->after('proveedor_id')->constrained('almacenes')->nullOnDelete();
            }
        });

        Schema::table('almacen_movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen_movimientos', 'detalle_compra_id')) {
                $table->foreignId('detalle_compra_id')->nullable()->after('producto_id')->constrained('compra_detalles')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->dropForeign(['detalle_compra_id']);
            $table->dropColumn('detalle_compra_id');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['almacen_id']);
            $table->dropColumn('almacen_id');
        });
    }
};
