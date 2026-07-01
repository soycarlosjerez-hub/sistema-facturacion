<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fetch a valid tenant_id from users (prefer one with most users)
        $defaultTenant = DB::table('users')
            ->select('business_instance_id')
            ->whereNotNull('business_instance_id')
            ->groupBy('business_instance_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->value('business_instance_id');

        if (!$defaultTenant) return;

        $addColumn = function (string $table) use ($defaultTenant) {
            if (Schema::hasColumn($table, 'tenant_id')) return;
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        };

        // Parent tables (no FK to backfill from)
        $parents = ['sucursales', 'almacenes', 'conduces', 'cotizaciones', 'lista_precios'];
        foreach ($parents as $table) {
            $addColumn($table);
        }

        // Child tables — backfill from parent's tenant_id
        $children = [
            'venta_detalles'      => ['fk' => 'venta_id',    'parent' => 'ventas'],
            'compra_detalles'     => ['fk' => 'compra_id',   'parent' => 'compras'],
            'conduce_items'       => ['fk' => 'conduce_id',  'parent' => 'conduces'],
            'cotizacion_items'    => ['fk' => 'cotizacion_id','parent' => 'cotizaciones'],
            'lista_precio_items'  => ['fk' => 'lista_precio_id','parent' => 'lista_precios'],
            'pagos'               => ['fk' => 'venta_id',    'parent' => 'ventas'],
            'ecf_documentos'      => ['fk' => 'venta_id',    'parent' => 'ventas'],
            'almacen_movimientos'  => ['fk' => 'almacen_id',  'parent' => 'almacenes'],
            'waitlist_entries'    => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
        ];

        foreach ($children as $table => $cfg) {
            if (Schema::hasColumn($table, 'tenant_id')) continue;

            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });

            DB::statement("
                UPDATE {$table} c
                INNER JOIN {$cfg['parent']} p ON p.id = c.{$cfg['fk']}
                SET c.tenant_id = p.tenant_id
            ");

            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        }
    }

    public function down(): void
    {
        $tables = [
            'sucursales', 'almacenes', 'conduces', 'cotizaciones', 'lista_precios',
            'venta_detalles', 'compra_detalles', 'conduce_items', 'cotizacion_items',
            'lista_precio_items', 'pagos', 'ecf_documentos', 'almacen_movimientos', 'waitlist_entries',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('tenant_id');
                });
            }
        }
    }
};
