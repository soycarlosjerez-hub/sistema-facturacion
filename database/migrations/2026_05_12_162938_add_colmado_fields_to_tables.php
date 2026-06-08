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
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'codigo_barras')) {
                $table->string('codigo_barras')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('productos', 'precio_compra')) {
                $table->decimal('precio_compra', 10, 2)->default(0)->after('precio');
            }
            if (!Schema::hasColumn('productos', 'unidad_medida')) {
                $table->string('unidad_medida')->default('Unidad')->after('precio_compra');
            }
            if (!Schema::hasColumn('productos', 'itbis_porcentaje')) {
                $table->decimal('itbis_porcentaje', 5, 2)->default(18)->after('unidad_medida');
            }
        });

        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'ncf')) {
                $table->string('ncf', 11)->nullable()->after('id');
            }
            if (!Schema::hasColumn('ventas', 'ncf_tipo')) {
                $table->string('ncf_tipo')->nullable()->after('ncf');
            }
            if (!Schema::hasColumn('ventas', 'ncf_vencimiento')) {
                $table->date('ncf_vencimiento')->nullable()->after('ncf_tipo');
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'limite_credito')) {
                $table->decimal('limite_credito', 12, 2)->default(0)->after('rnc_cedula');
            }
            if (!Schema::hasColumn('clientes', 'balance_pendiente')) {
                $table->decimal('balance_pendiente', 12, 2)->default(0)->after('limite_credito');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['codigo_barras', 'precio_compra', 'unidad_medida', 'itbis_porcentaje']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['ncf', 'ncf_tipo', 'ncf_vencimiento']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['limite_credito', 'balance_pendiente']);
        });
    }
};
