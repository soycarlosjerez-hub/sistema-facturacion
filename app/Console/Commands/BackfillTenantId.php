<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillTenantId extends Command
{
    protected $signature = 'tenant:backfill
                            {--dry-run : Solo mostrar cuántos registros se actualizarán}
                            {--instance= : ID de instancia forzado (default: inferir de user_id)}';
    protected $description = 'Asignar tenant_id a registros existentes que lo tengan NULL';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $instanceId = $this->option('instance');
        $totalUpdated = 0;

        // ============ Tablas con user_id directo ============
        $tablesWithUserId = [
            ['table' => 'ventas',             'fk' => 'user_id'],
            ['table' => 'pagos',              'fk' => 'user_id'],
            ['table' => 'productos',           'fk' => 'user_id'],
            ['table' => 'clientes',            'fk' => 'user_id'],
            ['table' => 'cajas',               'fk' => 'user_id'],
            ['table' => 'sesiones_caja',       'fk' => 'user_id'],
            ['table' => 'compras',             'fk' => 'user_id'],
            ['table' => 'proveedores',         'fk' => 'user_id'],
            ['table' => 'gastos',              'fk' => 'user_id'],
            ['table' => 'devoluciones',        'fk' => 'user_id'],
            ['table' => 'cotizaciones',        'fk' => 'user_id'],
            ['table' => 'conduces',            'fk' => 'user_id'],
            ['table' => 'reservaciones',       'fk' => 'user_id'],
            ['table' => 'waitlist_entries',    'fk' => 'user_id'],
            ['table' => 'ecf_documentos',      'fk' => 'usuario_id'],
            ['table' => 'ecf_log_envios',      'fk' => 'user_id'],
        ];

        foreach ($tablesWithUserId as $t) {
            $totalUpdated += $this->updateTable($t['table'], $t['fk'], $dryRun, $instanceId);
        }

        // ============ Tablas con venta_id (heredan de ventas) ============
        $tablesWithVentaId = [
            'venta_detalles',
            'split_bill_persons',
        ];

        foreach ($tablesWithVentaId as $table) {
            $totalUpdated += $this->updateFromParent($table, 'venta_id', 'ventas', $dryRun);
        }

        // ============ Tablas con detalle_id (heredan de compras/devoluciones) ============
        $tablesWithParent = [
            ['table' => 'detalle_compras',     'fk' => 'compra_id',     'parent' => 'compras'],
            ['table' => 'detalle_devoluciones', 'fk' => 'devolucion_id', 'parent' => 'devoluciones'],
            ['table' => 'cotizacion_items',    'fk' => 'cotizacion_id',  'parent' => 'cotizaciones'],
            ['table' => 'conduce_items',       'fk' => 'conduce_id',     'parent' => 'conduces'],
        ];

        foreach ($tablesWithParent as $t) {
            $totalUpdated += $this->updateFromParent($t['table'], $t['fk'], $t['parent'], $dryRun);
        }

        // ============ Tablas con nullable user_id o sin relación directa ============
        $extraTables = [
            ['table' => 'almacenes',              'fk' => 'user_id'],
            ['table' => 'almacen_movimientos',    'fk' => 'user_id'],
            ['table' => 'ncf_sequences',           'fk' => 'user_id'],
            ['table' => 'secuencias_ecf',          'fk' => 'user_id'],
            ['table' => 'mesas',                   'fk' => 'user_id'],
            ['table' => 'mesa_categorias',         'fk' => 'user_id'],
            ['table' => 'lista_precios',           'fk' => 'user_id'],
            ['table' => 'lista_precio_items',      'fk' => 'user_id'],
            ['table' => 'lavadores',               'fk' => 'user_id'],
            ['table' => 'lavadero_servicios',      'fk' => 'user_id'],
            ['table' => 'lavadero_citas',          'fk' => 'user_id'],
            ['table' => 'planta_gastos',           'fk' => 'user_id'],
        ];

        foreach ($extraTables as $t) {
            $totalUpdated += $this->updateTable($t['table'], $t['fk'], $dryRun, $instanceId);
        }

        if ($dryRun) {
            $this->info("\n[DRY RUN] {$totalUpdated} registro(s) serían actualizados.");
        } else {
            $this->info("\n{$totalUpdated} registro(s) actualizados.");
        }

        return 0;
    }

    private function updateTable(string $table, string $fk, bool $dryRun, ?string $instanceId): int
    {
        if (!DB::getSchemaBuilder()->hasTable($table)) return 0;
        if (!DB::getSchemaBuilder()->hasColumn($table, 'tenant_id')) return 0;

        $nullCount = DB::table($table)->whereNull('tenant_id')->count();
        if ($nullCount === 0) {
            $this->line("  <fg=green>✓</fg=green> {$table}: todos OK");
            return 0;
        }

        if ($dryRun) {
            $this->line("  <fg=cyan>~</fg=cyan> {$table}: {$nullCount} pendiente(s)");
            return $nullCount;
        }

        if ($instanceId) {
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $instanceId]);
        } elseif (DB::getSchemaBuilder()->hasColumn($table, $fk)) {
            $hasUserFk = DB::getSchemaBuilder()->hasColumn('users', 'id') &&
                         DB::getSchemaBuilder()->hasColumn($table, $fk);

            if ($fk === 'usuario_id' && $hasUserFk) {
                DB::statement(
                    "UPDATE {$table} t
                     INNER JOIN users u ON t.{$fk} = u.id
                     SET t.tenant_id = u.business_instance_id
                     WHERE t.tenant_id IS NULL"
                );
            } else {
                $columnCheck = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = '{$fk}'");
                if (!empty($columnCheck)) {
                    DB::statement(
                        "UPDATE {$table} t
                         INNER JOIN users u ON t.{$fk} = u.id
                         SET t.tenant_id = u.business_instance_id
                         WHERE t.tenant_id IS NULL AND u.business_instance_id IS NOT NULL"
                    );
                }
            }
        }

        $remaining = DB::table($table)->whereNull('tenant_id')->count();
        $updated = $nullCount - $remaining;
        if ($updated > 0) {
            $this->line("  <fg=green>✓</fg=green> {$table}: {$updated} actualizado(s)" . ($remaining > 0 ? ", {$remaining} aún sin tenant_id" : ""));
        } else {
            $this->line("  <fg=yellow>⚠</fg=yellow> {$table}: {$nullCount} sin actualizar (sin user_id vinculado)");
        }

        return $updated;
    }

    private function updateFromParent(string $table, string $fk, string $parentTable, bool $dryRun): int
    {
        if (!DB::getSchemaBuilder()->hasTable($table)) return 0;
        if (!DB::getSchemaBuilder()->hasColumn($table, 'tenant_id')) return 0;
        if (!DB::getSchemaBuilder()->hasTable($parentTable)) return 0;

        $nullCount = DB::table($table)->whereNull('tenant_id')->count();
        if ($nullCount === 0) {
            $this->line("  <fg=green>✓</fg=green> {$table}: todos OK");
            return 0;
        }

        if ($dryRun) {
            $this->line("  <fg=cyan>~</fg=cyan> {$table}: {$nullCount} pendiente(s)");
            return $nullCount;
        }

        DB::statement(
            "UPDATE {$table} t
             INNER JOIN {$parentTable} p ON t.{$fk} = p.id
             SET t.tenant_id = p.tenant_id
             WHERE t.tenant_id IS NULL AND p.tenant_id IS NOT NULL"
        );

        $remaining = DB::table($table)->whereNull('tenant_id')->count();
        $updated = $nullCount - $remaining;
        if ($updated > 0) {
            $this->line("  <fg=green>✓</fg=green> {$table}: {$updated} actualizado(s)" . ($remaining > 0 ? ", {$remaining} aún sin tenant_id" : ""));
        } else {
            $this->line("  <fg=yellow>⚠</fg=yellow> {$table}: {$nullCount} sin actualizar (padre sin tenant_id)");
        }

        return $updated;
    }
}
