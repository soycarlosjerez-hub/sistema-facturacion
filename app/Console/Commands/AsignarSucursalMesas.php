<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AsignarSucursalMesas extends Command
{
    protected $signature = 'restaurante:asignar-sucursal-mesas
                            {--sucursal-id= : ID de la sucursal a asignar (default: primera sucursal disponible)}
                            {--tenant-id= : Filtrar mesas de una instancia específica}';

    protected $description = 'Asigna sucursal_id a las mesas que no tienen una sucursal asignada';

    public function handle()
    {
        $query = DB::table('mesas')->whereNull('sucursal_id');

        if ($tenantId = $this->option('tenant-id')) {
            $query->where('tenant_id', $tenantId);
        }

        $sinSucursal = $query->get();

        if ($sinSucursal->isEmpty()) {
            $this->info('No hay mesas sin sucursal asignada.');
            return 0;
        }

        $this->warn("{$sinSucursal->count()} mesa(s) sin sucursal.");

        $sucursalId = $this->option('sucursal-id');

        if (!$sucursalId) {
            $sucursalQuery = DB::table('sucursales');
            if ($tenantId) {
                $sucursalQuery->where('tenant_id', $tenantId);
            }
            $sucursal = $sucursalQuery->first();
            if (!$sucursal) {
                $this->error('No se encontraron sucursales disponibles.');
                $this->comment('Use --sucursal-id=<id> para especificar una.');
                return 1;
            }
            $sucursalId = $sucursal->id;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($sinSucursal as $mesa) {
            $conflict = DB::table('mesas')
                ->where('sucursal_id', $sucursalId)
                ->where('numero', $mesa->numero)
                ->where('id', '!=', $mesa->id)
                ->exists();

            if ($conflict) {
                $this->warn("Mesa ID {$mesa->id} (numero: {$mesa->numero}) omitida — ya existe otra mesa con ese número en la sucursal.");
                $skipped++;
                continue;
            }

            DB::table('mesas')->where('id', $mesa->id)->update(['sucursal_id' => $sucursalId]);
            $updated++;
        }

        $this->info("{$updated} mesa(s) actualizada(s), {$skipped} omitida(s) con sucursal_id = {$sucursalId}.");
    }
}
