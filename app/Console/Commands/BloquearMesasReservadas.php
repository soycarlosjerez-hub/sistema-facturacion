<?php

namespace App\Console\Commands;

use App\Models\Reservacion;
use App\Models\Mesa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BloquearMesasReservadas extends Command
{
    protected $signature = 'restaurante:bloquear-reservas';
    protected $description = 'Marca mesas como reservadas cuando se acerca la hora de la reservaci├│n';

    public function handle()
    {
        $reservaciones = Reservacion::where('estado', 'pendiente')
            ->whereBetween('fecha_hora', [now(), now()->addHours(2)])
            ->get();

        $contador = 0;
        foreach ($reservaciones as $reservacion) {
            $mesa = $reservacion->mesa;
            if ($mesa && $mesa->estado === 'disponible') {
                $mesa->update(['estado' => 'reservada']);
                $reservacion->update(['estado' => 'confirmada']);
                $contador++;
            }
        }

        $this->info("{$contador} mesa(s) bloqueada(s) por reserva.");
        Log::info("BloquearMesasReservadas: {$contador} mesa(s) actualizada(s).");
    }
}
