<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KdsController extends Controller
{
    public function index()
    {
        return view('restaurante.kds');
    }

    public function orders()
    {
        // Órdenes de mesa (terminal de mesas) — solo las enviadas a cocina
        $sucursalId = session('sucursal_id') ?? Auth::user()?->sucursal_id;
        $mesas = Venta::whereIn('estado', ['abierta', 'completada'])
            ->when($sucursalId, fn($q) => $q->where(function ($w) use ($sucursalId) {
                $w->where('sucursal_id', $sucursalId)->orWhereNull('sucursal_id');
            }))
            ->whereHas('detalles', fn($q) => $q
                ->whereNotIn('estado_cocina', ['servido', 'no_enviado'])
                ->whereHas('producto', fn($pq) => $pq->where('incluir_kds', true)))
            ->with([
                'mesa:id,numero,nombre',
                'detalles' => fn($q) => $q
                    ->whereNotIn('estado_cocina', ['servido', 'no_enviado'])
                    ->whereHas('producto', fn($pq) => $pq->where('incluir_kds', true))
                    ->with('producto:id,nombre')
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function ($v) {
                $cursos = $v->detalles->groupBy('curso');
                return [
                    'origen'  => 'mesa',
                    'id'      => $v->id,
                    'mesa'    => $v->mesa?->nombre ?? 'Mesa ' . ($v->mesa?->numero ?? '—'),
                    'mesa_id' => $v->mesa_id,
                    'estado'  => $v->estado,
                    'time'    => $v->created_at->diffForHumans(),
                    'cursos'  => $cursos->toArray(),
                ];
            });

        // Órdenes API (modelo Orden) — llegan directo al KDS
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
                        'id'             => $detalle->id,
                        'producto'       => $detalle->producto ? ['id' => $detalle->producto->id, 'nombre' => $detalle->producto->nombre] : null,
                        'cantidad'       => $detalle->cantidad,
                        'notas'          => $detalle->notas,
                        'estado_cocina'  => $detalle->estado_cocina,
                        'created_at'     => $detalle->created_at?->toISOString(),
                    ];
                }

                return [
                    'origen'         => 'orden',
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

        $todas = $mesas->concat($ordenes)->values();

        return response()->json(['ordenes' => $todas]);
    }

    public function updateEstado(Request $request, string $origen, $detalle)
    {
        if ($origen === 'orden') {
            $request->validate(['estado' => 'required|in:pendiente,en_preparacion,listo,entregado']);
            $detalle = OrdenDetalle::findOrFail($detalle);
        } else {
            $request->validate(['estado' => 'required|in:pendiente,preparando,listo,servido']);
            $detalle = VentaDetalle::findOrFail($detalle);
        }

        $detalle->update([
            'estado_cocina'     => $request->estado,
            'cocina_updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function limpiar()
    {
        $afectadosMesa = VentaDetalle::whereIn('estado_cocina', ['pendiente', 'preparando', 'listo'])
            ->whereHas('venta', fn($q) => $q->whereIn('estado', ['abierta', 'completada']))
            ->update([
                'estado_cocina'     => 'servido',
                'cocina_updated_at' => now(),
            ]);

        $afectadosOrden = OrdenDetalle::whereIn('estado_cocina', ['pendiente', 'en_preparacion', 'listo'])
            ->whereHas('orden', fn($q) => $q->deSucursal()->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso']))
            ->update([
                'estado_cocina'     => 'entregado',
                'cocina_updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'limpiados' => $afectadosMesa + $afectadosOrden]);
    }

    public function audio()
    {
        $nuevosMesa = VentaDetalle::where('estado_cocina', 'pendiente')
            ->where('cocina_updated_at', '>=', now()->subMinutes(5))
            ->whereDoesntHave('venta', fn($q) => $q->whereIn('estado', ['anulada']))
            ->whereHas('producto', fn($q) => $q->where('incluir_kds', true))
            ->count();

        $nuevosOrden = OrdenDetalle::where('estado_cocina', 'pendiente')
            ->where('cocina_updated_at', '>=', now()->subMinutes(5))
            ->whereHas('orden', fn($q) => $q->deSucursal()->whereIn('estado', ['pendiente', 'confirmada', 'en_proceso']))
            ->count();

        return response()->json(['nuevos' => $nuevosMesa + $nuevosOrden]);
    }
}
