<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsignacionResource;
use App\Models\Consignacion;
use Illuminate\Http\Request;

class ConsignacionesController extends Controller
{
    public function index(Request $request)
    {
        $query = Consignacion::with(['obra'])
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->galeria, fn ($q) => $q->where('galeria_nombre', 'like', "%{$request->galeria}%"))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('galeria_nombre', 'like', "%{$request->search}%");
            }))->orWhereHas('obra', function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%");
            })
            ->when($request->expired_only, fn ($q) => $q->where('estado', 'activa')->where('fecha_fin', '<', now()))
            ->when($request->active_only, fn ($q) => $q->where('estado', 'activa'));

        $query->orderBy('fecha_inicio', 'desc');

        return ConsignacionResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'galeria_nombre' => 'required|string|max:255',
            'obra_id' => 'required|exists:obras,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'comision_percentage' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
        ]);

        $consignacion = Consignacion::create($validated);

        return new ConsignacionResource($consignacion->load('obra'));
    }

    public function show(Consignacion $consignacion)
    {
        return new ConsignacionResource($consignacion->load('obra'));
    }

    public function update(Request $request, Consignacion $consignacion)
    {
        $validated = $request->validate([
            'galeria_nombre' => 'sometimes|string|max:255',
            'obra_id' => 'sometimes|exists:obras,id',
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date',
            'comision_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'estado' => 'sometimes|in:activa,vendida,devuelta',
            'fecha_venta' => 'sometimes|nullable|date',
            'precio_venta' => 'sometimes|nullable|numeric|min:0',
            'comision_monto' => 'sometimes|nullable|numeric|min:0',
            'pago_recibido' => 'sometimes|nullable|boolean',
            'pago_fecha' => 'sometimes|nullable|date',
            'notas' => 'sometimes|nullable|string',
        ]);

        if (isset($validated['estado']) && $validated['estado'] === 'vendida') {
            $precioVenta = $validated['precio_venta'] ?? $consignacion->precio_venta;
            $comisionPct = $validated['comision_percentage'] ?? $consignacion->comision_percentage;
            if ($precioVenta) {
                $validated['comision_monto'] = ($precioVenta * $comisionPct) / 100;
            }
        }

        $consignacion->update($validated);

        return new ConsignacionResource($consignacion->load('obra'));
    }

    public function destroy(Consignacion $consignacion)
    {
        $consignacion->delete();
        return response()->json(['message' => 'Consignacion eliminada correctamente.']);
    }
}
