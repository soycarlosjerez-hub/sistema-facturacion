#!/usr/bin/env php
<?php

namespace App\Console\Commands\Skills;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantIdValidator extends Command
{
    protected $signature = 'skill:tenant-id-validator {--fix : Populate missing tenant_id values}';
    protected $description = 'Validates and populates tenant_id across all modules using TenantScope';

    public function handle(): int
    {
        $this->info("=== Tenant ID Validation Tool ===\n");
        
        $tablesWithTenantScope = $this->getTablesWithTenantScope();
        $stats = $this->analyzeTenantIdPopulation($tablesWithTenantScope);
        
        $this->displayValidationReport($tablesWithTenantScope, $stats);
        
        if ($this->option('fix')) {
            $this->populateMissingTenantId($tablesWithTenantScope);
        }
        
        $this->displayFinalStats($stats);
        
        return Command::SUCCESS;
    }

    private function getTablesWithTenantScope(): array
    {
        // List of models that use TenantScope trait (exact table names from database)
        $models = [
            'productos',
            'categorias', 
            'clientes',
            'proveedores',
            'ventas',
            'compras',
            'venta_detalles',
            'compra_detalles',
            'cajas',
            'sesion_cajas',
            'pagos',
            'pagos_instancia',
            'almacenes',
            'almacen_movimientos',
            'ncf_sequences',
            'secuencias_ecf',
            'mesas',
            'mesa_ubicaciones',
            'mesa_categorias',
            'waitlist_entries',
            'split_bill_persons',
            'conduces',
            'conduce_items',
            'cotizaciones',
            'cotizacion_items',
            'gastos',
            'ecf_documentos',
            'ecf_log_envios',
            'lavadero_citas',
            'lavadero_servicios',
            'lavadores',
            'lavador_venta',
            'reservaciones',
            'modulos',
            'business_instances',
            'business_instance_modules',
            'instance_roles',
            'instance_role_modules',
            'instance_error_logs',
            'business_types',
            'business_type_modules',
            'wizard_steps',
        ];
        
        return $models;
    }

    private function analyzeTenantIdPopulation(array $tables): array
    {
        $stats = [
            'total_tables' => 0,
            'tables_with_data' => 0,
            'tables_missing_data' => 0,
            'total_records_with_id' => 0,
            'total_records_missing_id' => 0,
            'tables_details' => [],
        ];
        
        foreach ($tables as $table) {
            $tableStats = $this->getTableStats($table);
            $stats['tables_details'][$table] = $tableStats;
            
            $stats['total_tables']++;
            
            if ($tableStats['has_tenant_id_column']) {
                $stats['tables_with_data']++;
                $stats['total_records_with_id'] += $tableStats['total_records'];
            } else {
                $stats['tables_missing_data']++;
            }
            
            $stats['total_records_missing_id'] += $tableStats['records_without_id'];
        }
        
        return $stats;
    }

    private function getTableStats(string $table): array
    {
        try {
            // Use SHOW COLUMNS instead of information_schema for better compatibility
            $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
            
            $hasTenantIdColumn = collect($columns)->contains(fn($col) => strtolower($col->Field) === 'tenant_id');
            
            $result = ['has_tenant_id_column' => $hasTenantIdColumn];
            
            if ($hasTenantIdColumn) {
                $countResult = DB::select("SELECT COUNT(*) as total, SUM(CASE WHEN tenant_id IS NULL OR tenant_id = 0 THEN 1 ELSE 0 END) as without_id FROM `{$table}`");
                $total = $countResult[0]->total;
                $withoutId = $countResult[0]->without_id;
                
                $result['has_data'] = true;
                $result['total_records'] = (int) $total;
                $result['records_without_id'] = (int) $withoutId;
                $result['percentage_without_id'] = $total > 0 ? round(($withoutId / $total) * 100, 2) : 0;
            } else {
                $result['has_data'] = false;
                $result['total_records'] = 0;
                $result['records_without_id'] = 0;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            // Table doesn't exist or other error
            return [
                'has_tenant_id_column' => false,
                'has_data' => false,
                'total_records' => 0,
                'records_without_id' => 0,
            ];
        }
    }

    private function displayValidationReport(array $tables, array $stats): void
    {
        $this->info("\n=== TABLES WITH TENANT SCOPE ===\n");
        foreach ($tables as $table) {
            $detail = $stats['tables_details'][$table];
            $status = $detail['has_data'] ? "✅ OK" : "❌ MISSING COLUMN";
            $this->line("{$table}: {$status} " . ($detail['has_data'] ? "({$detail['percentage_without_id']}% sin tenant_id)" : ""));
        }
        
        $this->info("\n=== SUMMARY ===");
        $this->line("Total tables: {$stats['total_tables']}");
        $this->line("Tables with tenant_id: {$stats['tables_with_data']}");
        $this->line("Tables without tenant_id: {$stats['tables_missing_data']}");
        $this->line("Total records with tenant_id: {$stats['total_records_with_id']}");
        $this->line("Total records without tenant_id: {$stats['total_records_missing_id']}");
    }

    private function populateMissingTenantId(array $tables): void
    {
        $this->info("\n=== POPULATING MISSING TENANT_ID ===");
        $totalFixed = 0;
        
        foreach ($tables as $table) {
            $detail = $this->getTableStats($table);
            
            if (!$detail['has_tenant_id_column']) {
                $this->warn("Saltando {$table}: columna tenant_id no existe");
                continue;
            }
            
            $count = DB::table($table)
                ->whereNull('tenant_id')
                ->orWhere('tenant_id', '0')
                ->update(['tenant_id' => 1]);
            
            if ($count > 0) {
                $this->info("  ✓ {$table}: asignados {$count} tenant_id = 1");
                $totalFixed += $count;
            }
        }
        
        $this->info("\n✓ Total registros actualizados: {$totalFixed}");
    }

    private function displayFinalStats(array $stats): void
    {
        $this->info("\n=== FINAL STATISTICS ===");
        $this->line("Total tables processed: {$stats['total_tables']}");
        $this->line("Total records in database: " . ($stats['total_records_with_id'] + $stats['total_records_missing_id']));
        $this->line("Records with valid tenant_id: {$stats['total_records_with_id']}");
        $this->line("Records missing tenant_id: {$stats['total_records_missing_id']}");
        
        if ($stats['total_records_missing_id'] > 0) {
            $this->warn("\n⚠️ Some records still don't have a tenant_id. Run the command with --fix flag to populate them.");
        } else {
            $this->info("\n✅ All records have tenant_id assigned.");
        }
    }
}
