<?php

namespace App\Services;

use App\Enums\CocinaState;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use Illuminate\Support\Facades\DB;

class OrdenKitchenService
{
    public function getOrders(): array
    {
        $ordenes = Orden::deSucursal()
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso'])
            ->whereHas('detalles', fn($q) => $q->where('estado_cocina', '!=', 'entregado'))
            ->with([
                'detalles' => fn($q) => $q->where('estado_cocina', '!=', 'entregado')->with('producto:id,nombre')
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function ($o) {
                $cursos = [];
                foreach ($o->detalles as $detalle) {
                    $nombreCurso = $detalle->curso ?: 'General';
                    if (!isset($cursos[$nombreCurso])) {
                        $cursos[$nombreCurso] = [];
                    }
                    $cursos[$nombreCurso][] = [
                        'id' => $detalle->id,
                        'producto' => $detalle->producto ? ['id' => $detalle->producto->id, 'nombre' => $detalle->producto->nombre] : null,
                        'cantidad' => $detalle->cantidad,
                        'notas' => $detalle->notas,
                        'estado_cocina' => $detalle->estado_cocina,
                        'created_at' => $detalle->created_at?->toISOString(),
                    ];
                }

                return [
                    'id'             => $o->id,
                    'tipo_orden'     => $o->tipo_orden,
                    'estado'         => $o->estado,
                    'cliente_nombre' => $o->cliente?->nombre ?? '—',
                    'telefono'       => $o->telefono_contacto,
                    'direccion'      => $o->direccion_entrega,
                    'empresa'        => $o->entregaEmpresa?->nombre,
                    'hora_retiro'    => $o->hora_retiro?->format('h:i A'),
                    'time'           => $o->created_at->diffForHumans(),
                    'time_iso'       => $o->created_at->toIso8601String(),
                    'cursos'         => $cursos,
                ];
            });

        return ['ordenes' => $ordenes];
    }

    public function updateDetalleState(OrdenDetalle $detalle, string $nuevoEstado): array
    {
        $transiciones = [
            CocinaState::Pendiente->value => [CocinaState::EnPreparacion->value],
            CocinaState::EnPreparacion->value => [CocinaState::Listo->value],
            CocinaState::Listo->value => [CocinaState::Entregado->value],
        ];

        if (!isset($transiciones[$detalle->estado_cocina]) || !in_array($nuevoEstado, $transiciones[$detalle->estado_cocina])) {
            return ['error' => "No se puede cambiar de '{$detalle->estado_cocina}' a '$nuevoEstado'", 'code' => 422];
        }

        $detalle->update([
            'estado_cocina'    => $nuevoEstado,
            'cocina_updated_at' => now(),
        ]);

        return ['success' => true, 'detalle' => $detalle->fresh()->load('producto')];
    }
}
