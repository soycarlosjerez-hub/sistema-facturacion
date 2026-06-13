<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;

class KdsController extends Controller
{
    public function index()
    {
        return view('restaurante.kds');
    }

    public function orders()
    {
        $ordenes = Venta::deSucursal()->whereIn('estado', ['abierta', 'completada'])
            ->whereHas('detalles', fn($q) => $q->where('estado_cocina', '!=', 'servido'))
            ->with([
                'mesa:id,numero,nombre',
                'detalles' => fn($q) => $q->where('estado_cocina', '!=', 'servido')->with('producto:id,nombre')
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function ($v) {
                $cursos = $v->detalles->groupBy('curso');
                return [
                    'id'      => $v->id,
                    'mesa'    => $v->mesa?->nombre ?? 'Mesa ' . ($v->mesa?->numero ?? '—'),
                    'mesa_id' => $v->mesa_id,
                    'estado'  => $v->estado,
                    'time'    => $v->created_at->diffForHumans(),
                    'cursos'  => $cursos->toArray(),
                ];
            });

        return response()->json(['ordenes' => $ordenes]);
    }

    public function updateEstado(Request $request, VentaDetalle $detalle)
    {
        $request->validate(['estado' => 'required|in:pendiente,preparando,listo,servido']);
        $detalle->update([
            'estado_cocina'     => $request->estado,
            'cocina_updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function audio()
    {
        $nuevos = VentaDetalle::where('estado_cocina', 'pendiente')
            ->where('cocina_updated_at', '>=', now()->subMinutes(5))
            ->whereDoesntHave('venta', fn($q) => $q->whereIn('estado', ['anulada'])->deSucursal())
            ->count();
        return response()->json(['nuevos' => $nuevos]);
    }
}
