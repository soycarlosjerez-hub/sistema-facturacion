<?php

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
        if (!$defaultTenant) return;

        $add = function (string $table) use ($defaultTenant) {
            if (Schema::hasColumn($table, 'tenant_id')) return;
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        };

        // Standalone tables — no FK chain, assign default tenant
        foreach (['mesa_categorias', 'lavadores', 'lavadero_servicios', 'secuencias_ecf', 'ncf_sequences', 'proveedores'] as $t) {
            $add($t);
        }

        // Tables with direct FK to a parent that has tenant_id
        $direct = [
            'mesas'             => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'split_bill_people' => ['fk' => 'venta_id',    'parent' => 'ventas'],
            'ecf_log_envios'    => ['fk' => 'ecf_documento_id', 'parent' => 'ecf_documentos'],
            'lavadero_citas'    => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'reservaciones'     => ['fk' => 'cliente_id',  'parent' => 'clientes'],
            'compra_detalles'   => ['fk' => 'compra_id',   'parent' => 'compras'],
            'devoluciones'      => ['fk' => 'venta_id',    'parent' => 'ventas'],
            'gastos'            => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
            'cajas'             => ['fk' => 'sucursal_id', 'parent' => 'sucursales'],
        ];

        foreach ($direct as $table => $cfg) {
            if (Schema::hasColumn($table, 'tenant_id')) continue;
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            DB::statement("UPDATE {$table} c INNER JOIN {$cfg['parent']} p ON p.id = c.{$cfg['fk']} SET c.tenant_id = p.tenant_id");
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE {$table} MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        }

        // Two-hop chain tables
        // detalles_devolucion -> devoluciones -> ventas
        if (!Schema::hasColumn('detalles_devolucion', 'tenant_id')) {
            Schema::table('detalles_devolucion', function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            DB::statement("UPDATE detalles_devolucion dd INNER JOIN devoluciones d ON d.id = dd.devolucion_id INNER JOIN ventas v ON v.id = d.venta_id SET dd.tenant_id = v.tenant_id");
            DB::table('detalles_devolucion')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE detalles_devolucion MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        }

        // sesion_cajas -> cajas -> sucursales
        if (!Schema::hasColumn('sesion_cajas', 'tenant_id')) {
            Schema::table('sesion_cajas', function (Blueprint $t) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            });
            DB::statement("UPDATE sesion_cajas sc INNER JOIN cajas c ON c.id = sc.caja_id INNER JOIN sucursales s ON s.id = c.sucursal_id SET sc.tenant_id = s.tenant_id");
            DB::table('sesion_cajas')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);
            DB::statement("ALTER TABLE sesion_cajas MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL");
        }
    }

    public function down(): void
    {
        $tables = [
            'mesas', 'split_bill_people', 'mesa_categorias', 'ecf_log_envios',
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
