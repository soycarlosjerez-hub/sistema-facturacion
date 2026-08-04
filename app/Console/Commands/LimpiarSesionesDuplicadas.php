<?php

namespace App\Console\Commands;

use App\Models\SesionCaja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarSesionesDuplicadas extends Command
{
    protected $signature = 'cajas:limpiar-sesiones-duplicadas {--dry-run : Solo mostrar qué se cerraría, sin aplicar cambios}';
    protected $description = 'Cerrar sesiones de caja duplicadas (más de una abierta por caja), conservando la más reciente';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Agrupar sesiones abiertas por caja
        $duplicados = SesionCaja::where('estado', 'abierta')
            ->select('caja_id')
            ->groupBy('caja_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('caja_id');

        if ($duplicados->isEmpty()) {
            $this->info('No hay sesiones duplicadas abiertas. Todo correcto.');
            return Command::SUCCESS;
        }

        $totalCerradas = 0;

        foreach ($duplicados as $cajaId) {
            $sesiones = SesionCaja::with('caja', 'user')
                ->where('caja_id', $cajaId)
                ->where('estado', 'abierta')
                ->orderByDesc('fecha_apertura')
                ->get();

            $conservar = $sesiones->shift(); // la más reciente

            $this->line('');
            $this->info("Caja #{$cajaId} (" . ($conservar->caja->nombre ?? 'sin nombre') . "): {$sesiones->count()} sesión(es) duplicada(s).");
            $this->line("  - Conservada: sesión #{$conservar->id} abierta el " . $conservar->fecha_apertura . " por " . ($conservar->user->name ?? 'usuario ' . $conservar->user_id));

            foreach ($sesiones as $sesion) {
                $ventasCount = DB::table('ventas')->where('sesion_caja_id', $sesion->id)->count();
                $pagosCount = DB::table('pagos')->where('sesion_caja_id', $sesion->id)->count();
                $tieneOperaciones = ($ventasCount + $pagosCount) > 0;

                $this->line(
                    "  - Cerrada: sesión #{$sesion->id} abierta el {$sesion->fecha_apertura} por " .
                    ($sesion->user->name ?? 'usuario ' . $sesion->user_id) .
                    " (ventas: {$ventasCount}, pagos: {$pagosCount})" .
                    ($tieneOperaciones ? ' [TIENE OPERACIONES ASOCIADAS]' : '')
                );

                if ($dryRun) {
                    continue;
                }

                $sesion->update([
                    'estado'       => 'cerrada',
                    'fecha_cierre' => now(),
                    'notas'        => 'Cerrada automáticamente por duplicado (mantenimiento). Conservada sesión #' . $conservar->id,
                ]);
                $totalCerradas++;
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se aplicó ningún cambio. Ejecuta sin --dry-run para cerrar las sesiones duplicadas.');
            return Command::SUCCESS;
        }

        $this->info("Se cerraron {$totalCerradas} sesión(es) duplicada(s).");
        return Command::SUCCESS;
    }
}
