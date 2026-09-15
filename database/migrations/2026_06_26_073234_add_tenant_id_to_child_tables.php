<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultTenant = DB::table('users')
            ->select('business_instance_id')
            ->whereNotNull('business_instance_id')
            ->groupBy('business_instance_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->value('business_instance_id');

        // Sin usuarios aún (BD fresca/tests): igual agregar las columnas
        // (nullable); el backfill se omite hasta que haya datos.
        $addColumn = function (string $table) use ($defaultTenant) {
            if (Schema::hasColumn($table, 'tenant_id')) {
                return;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            if (! $defaultTenant) {
                return;
            }
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            if (DB::getDriverName() !== 'sqlite') {
                SafeAlterTable::alter("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
            }
        };

        $parents = ['sucursales', 'almacenes', 'conduces', 'cotizaciones', 'lista_precios'];
        foreach ($parents as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $addColumn($table);
        }

        $children = [
            'venta_detalles' => ['fk' => 'venta_id', 'parent' => 'ventas'],
            'compra_detalles' => ['fk' => 'compra_id', 'parent' => 'compras'],
            'conduce_items' => ['fk' => 'conduce_id', 'parent' => 'conduces'],
            'cotizacion_items' => ['fk' => 'cotizacion_id', 'parent' => 'cotizaciones'],
            'lista_precio_items' => ['fk' => 'lista_precio_id', 'parent' => 'lista_precios'],
            'almacen_movimientos' => ['fk' => 'almacen_id', 'parent' => 'almacenes'],
            'plantilla_impresiones' => ['fk' => 'tenant_id', 'parent' => 'plantilla_impresiones'],
            'ordenes_detalles' => ['fk' => 'orden_id', 'parent' => 'ordenes'],
            'pagos_instancia' => ['fk' => 'instance_id', 'parent' => 'business_instances'],
            'pagos' => ['fk' => 'venta_id', 'parent' => 'ventas'],
            'venta_dominios' => ['fk' => 'venta_id', 'parent' => 'ventas'],
            'diagnosticos' => ['fk' => 'orden_id', 'parent' => 'ordenes'],
            'ordenes_piezas' => ['fk' => 'orden_id', 'parent' => 'ordenes'],
            'garantias' => ['fk' => 'producto_id', 'parent' => 'productos'],
            'plantilla_gastos' => ['fk' => 'tenant_id', 'parent' => 'plantilla_gastos'],
            'gastos' => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
        ];

        foreach ($children as $table => $cfg) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (Schema::hasColumn($table, 'tenant_id')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            if (DB::getDriverName() !== 'sqlite' && $cfg['fk'] !== 'tenant_id' && Schema::hasColumn($table, $cfg['fk']) && Schema::hasColumn($cfg['parent'], 'tenant_id')) {
                DB::statement("UPDATE {$table} c INNER JOIN {$cfg['parent']} p ON p.id = c.{$cfg['fk']} SET c.tenant_id = p.tenant_id");
            }
            if ($defaultTenant) {
                DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            }
            if ($defaultTenant && DB::getDriverName() !== 'sqlite' && Schema::hasColumn($table, 'tenant_id')) {
                SafeAlterTable::alter("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'sucursales', 'almacenes', 'conduces', 'cotizaciones', 'lista_precios',
            'venta_detalles', 'compra_detalles', 'conduce_items', 'cotizacion_items',
            'lista_precio_items', 'almacen_movimientos', 'plantilla_impresiones',
            'ordenes_detalles', 'pagos_instancia', 'pagos', 'venta_dominios',
            'diagnosticos', 'ordenes_piezas', 'garantias', 'plantilla_gastos', 'gastos',
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
