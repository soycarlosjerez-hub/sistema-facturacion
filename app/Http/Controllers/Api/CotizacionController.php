<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CotizacionResource;
use App\Models\Cotizacion;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::with(['cliente', 'sucursal', 'user', 'detalles.producto'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('folio', 'like', '%' . $request->search . '%')
                    ->orWhere('referencia', 'like', '%' . $request->search . '%');
            }));

        return CotizacionResource::collection($query->orderBy('fecha', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50',
            'cliente_id' => 'required|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'user_id' => 'required|exists:users,id',
            'fecha' => 'nullable|date',
            'vigencia' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $cotizacion = Cotizacion::create($validated);

        foreach ($validated['detalles'] as $detalle) {
            $cotizacion->detalles()->create(array_merge($detalle, ['cotizacion_id' => $cotizacion->id]));
        }

        return new CotizacionResource($cotizacion->load(['cliente', 'sucursal', 'user', 'detalles.producto']));
    }

    public function show(Cotizacion $cotizacion)
    {
        return new CotizacionResource($cotizacion->load(['cliente', 'sucursal', 'user', 'detalles.producto']));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'folio' => 'sometimes|string|max:50',
            'estado' => 'sometimes|string|max:20',
            'notas' => 'nullable|string',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $cotizacion->update($validated);

        return new CotizacionResource($cotizacion->load(['cliente', 'sucursal', 'user', 'detalles.producto']));
    }

    public function destroy(Cotizacion $cotizacion)
    {
        $cotizacion->delete();
        return response()->json(['message' => 'Cotización eliminada.']);
    }
}
