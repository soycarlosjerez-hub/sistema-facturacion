<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Centralized table cleanup list for tenant data purge.
 *
 * Keeping this in a dedicated class avoids hardcoding table names
 * scattered across controllers and makes it easier to add/remove
 * tenant-scoped tables as the application grows.
 */
class TenantCleanupService
{
    /**
     * All tenant-scoped tables that should be purged when cleaning an instance.
     *
     * These are tables that use a `tenant_id` column to scope data per business instance.
     * The array is organized by functional groups with comments for maintainability.
     */
    public static function getAll(): array
    {
        return [
            // ── Sales & Payments ──
            'split_bill_persons',
            'venta_detalles',
            'pagos',
            'ventas',

            // ── ECF / NCF ──
            'ecf_log_envios',
            'ecf_documentos',
            'secuencias_ecf',
            'ncf_sequences',

            // ── Conduces (delivery notes) ──
            'conduce_items',
            'conduces',

            // ── Returns ──
            'detalles_devolucion',
            'devoluciones',

            // ── Purchases ──
            'compra_detalles',
            'compras',

            // ── Expenses ──
            'gastos',

            // ── Quotes / Estimations ──
            'cotizacion_items',
            'cotizaciones',

            // ── Warehouse ──
            'almacen_movimientos',
            'almacenes',

            // ── Restaurant ──
            'reservaciones',
            'waitlist_entries',
            'mesas',
            'mesa_ubicaciones',
            'mesa_categorias',
            'categories',

            // ── Car Wash ──
            'lavadero_citas',
            'lavadero_servicios',
            'lavadores',

            // ── Real Estate / Rental ──
            'alquiler_contratos',
            'alquiler_inquilinos',
            'alquiler_viviendas',
            'alquiler_pagos',

            // ── Tattoo ──
            'tattoo_appointments',
            'tattoo_artists',
            'tattoo_designs',

            // ── Vehicles ──
            'vehiculos',

            // ── Cash Registers ──
            'sesion_cajas',
            'cajas',

            // ── Price Lists ──
            'lista_precio_items',
            'lista_precios',

            // ── Master Operational Data ──
            'proveedores',
            'clientes',
            'productos',
            'categorias',
            'sucursales',

            // ── Operational Settings ──
            'system_settings',

            // ── Instance Error Logs ──
            'instance_error_logs',
        ];
    }

    /**
     * Delete all records with the given tenant_id from the specified tables.
     *
     * @param  int  $tenantId
     * @param  array|null  $tables  Override the default list (for testing or special cases)
     * @return int Total rows deleted
     */
    public static function clearTenantData(int $tenantId, ?array $tables = null): int
    {
        $tables = $tables ?? self::getAll();
        $totalDeleted = 0;

        DB::transaction(function () use ($tenantId, $tables, &$totalDeleted) {
            foreach ($tables as $table) {
                if (! DB::getSchemaBuilder()->hasTable($table)) {
                    continue;
                }

                // Ensure the table has a tenant_id column before attempting to filter
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                if (! in_array('tenant_id', $columns)) {
                    continue;
                }

                $deleted = DB::table($table)->where('tenant_id', $tenantId)->delete();
                $totalDeleted += $deleted;
            }
        });

        return $totalDeleted;
    }
}
