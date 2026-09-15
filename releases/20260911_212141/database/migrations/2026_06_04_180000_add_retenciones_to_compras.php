<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->boolean('aplica_retencion_isr')->default(false)->after('observaciones');
            $table->boolean('aplica_retencion_itbis')->default(false)->after('aplica_retencion_isr');
            $table->decimal('retencion_isr', 12, 2)->default(0)->after('aplica_retencion_itbis');
            $table->decimal('retencion_itbis', 12, 2)->default(0)->after('retencion_isr');
            $table->decimal('total_neto', 12, 2)->default(0)->after('retencion_itbis');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('retencion_isr', 12, 2)->default(0)->after('descuento');
            $table->decimal('retencion_itbis', 12, 2)->default(0)->after('retencion_isr');
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('rnc', 20)->nullable()->after('direccion');
            $table->string('tipo_persona', 20)->default('juridica')->after('rnc');
            $table->boolean('sujeto_retencion_isr')->default(true)->after('tipo_persona');
            $table->boolean('sujeto_retencion_itbis')->default(true)->after('sujeto_retencion_isr');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $columns = ['aplica_retencion_isr', 'aplica_retencion_itbis', 'retencion_isr', 'retencion_itbis', 'total_neto'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('compras', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        Schema::table('ventas', function (Blueprint $table) {
            $columns = ['retencion_isr', 'retencion_itbis'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ventas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        Schema::table('proveedores', function (Blueprint $table) {
            $columns = ['rnc', 'tipo_persona', 'sujeto_retencion_isr', 'sujeto_retencion_itbis'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('proveedores', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
