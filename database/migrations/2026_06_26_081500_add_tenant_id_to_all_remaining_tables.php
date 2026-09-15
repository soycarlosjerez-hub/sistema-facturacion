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
        $backfill = function (string $table) use ($defaultTenant) {
            if (! $defaultTenant) {
                return;
            }
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
        };

        $add = function (string $table) use ($defaultTenant, $backfill) {
            if (Schema::hasColumn($table, 'tenant_id')) {
                return;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            $backfill($table);
            if ($defaultTenant && DB::getDriverName() !== 'sqlite') {
                SafeAlterTable::alter("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
            }
        };

        foreach (['mesa_categorias', 'lavadores', 'lavadero_servicios', 'secuencias_ecf', 'ncf_sequences', 'proveedores'] as $t) {
            if (! Schema::hasTable($t)) {
                continue;
            }
            $add($t);
        }

        // Tables with direct FK to a parent that has tenant_id
        $direct = [
            'mesas' => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'split_bill_persons' => ['fk' => 'venta_id', 'parent' => 'ventas'],
            'ecf_log_envios' => ['fk' => 'ecf_documento_id', 'parent' => 'ecf_documentos'],
            'lavadero_citas' => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'reservaciones' => ['fk' => 'cliente_id', 'parent' => 'clientes'],
            'compra_detalles' => ['fk' => 'compra_id', 'parent' => 'compras'],
            'devoluciones' => ['fk' => 'venta_id', 'parent' => 'ventas'],
            'gastos' => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'cajas' => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
        ];

        foreach ($direct as $table => $cfg) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (Schema::hasColumn($table, 'tenant_id')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn($table, $cfg['fk']) && Schema::hasColumn($cfg['parent'], 'tenant_id')) {
                DB::statement("UPDATE {$table} c INNER JOIN {$cfg['parent']} p ON p.id = c.{$cfg['fk']} SET c.tenant_id = p.tenant_id");
            }
            $backfill($table);
            if ($defaultTenant && DB::getDriverName() !== 'sqlite') {
                SafeAlterTable::alter("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
            }
        }

        // Two-hop chain tables
        if (Schema::hasTable('detalles_devolucion') && ! Schema::hasColumn('detalles_devolucion', 'tenant_id')) {
            Schema::table('detalles_devolucion', function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('detalles_devolucion', 'tenant_id') && Schema::hasColumn('devoluciones', 'tenant_id')) {
                DB::statement('UPDATE detalles_devolucion dd INNER JOIN devoluciones d ON d.id = dd.devolucion_id INNER JOIN ventas v ON v.id = d.venta_id SET dd.tenant_id = v.tenant_id');
            }
            $backfill('detalles_devolucion');
            if ($defaultTenant && DB::getDriverName() !== 'sqlite' && Schema::hasColumn('detalles_devolucion', 'tenant_id')) {
                SafeAlterTable::alter('ALTER TABLE detalles_devolucion MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL');
            }
        }

        if (Schema::hasTable('sesion_cajas') && ! Schema::hasColumn('sesion_cajas', 'tenant_id')) {
            Schema::table('sesion_cajas', function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('sesion_cajas', 'tenant_id') && Schema::hasColumn('cajas', 'tenant_id')) {
                DB::statement('UPDATE sesion_cajas sc INNER JOIN cajas c ON c.id = sc.caja_id SET sc.tenant_id = c.tenant_id');
            }
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('sesion_cajas', 'tenant_id') && Schema::hasColumn('cajas', 'sucursal_id') && Schema::hasColumn('sucursales', 'tenant_id')) {
                DB::statement('UPDATE sesion_cajas sc INNER JOIN cajas c ON c.id = sc.caja_id INNER JOIN sucursales s ON s.id = c.sucursal_id SET sc.tenant_id = s.tenant_id');
            }
            $backfill('sesion_cajas');
            if ($defaultTenant && DB::getDriverName() !== 'sqlite') {
                SafeAlterTable::alter('ALTER TABLE sesion_cajas MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL');
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'mesas', 'split_bill_persons', 'mesa_categorias', 'ecf_log_envios',
            'lavadores', 'lavadero_servicios', 'lavadero_citas', 'reservaciones',
            'secuencias_ecf', 'ncf_sequences', 'compra_detalles', 'detalles_devolucion',
            'devoluciones', 'gastos', 'proveedores', 'cajas', 'sesion_cajas',
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
