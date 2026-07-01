<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentaResource;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos', 'tipoVenta', 'mesa'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->fecha_desde, fn ($q) => $q->whereDate('fecha', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn ($q) => $q->whereDate('fecha', '<=', $request->fecha_hasta))
            ->when($request->search_ncf, fn ($q) => $q->where('ncf', 'like', '%' . $request->search_ncf . '%'))
            ->when($request->min_total, fn ($q) => $q->where('total', '>=', $request->min_total))
            ->when($request->max_total, fn ($q) => $q->where('total', '<=', $request->max_total));

        return VentaResource::collection($query->orderBy('fecha', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ncf' => 'required|string|max:50',
            'ncf_tipo' => 'required|string|max:20',
            'tipo_comprobante' => 'nullable|string|max:20',
            'encf' => 'nullable|string|max:50',
            'user_id' => 'required|exists:users,id',
            'caja_id' => 'required|exists:cajas,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_venta_id' => 'nullable|exists:tipos_ventas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'cargo_servicio' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
            'tipo_orden' => 'nullable|string|max:20',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.impuesto' => 'nullable|numeric|min:0',
        ]);

        $venta = Venta::create($validated);

        foreach ($validated['detalles'] as $detalle) {
            $venta->detalles()->create(array_merge($detalle, ['venta_id' => $venta->id]));
        }

        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos']));
    }

    public function show(Venta $venta)
    {
        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos', 'tipoVenta', 'mesa']));
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'ncf' => 'sometimes|string|max:50',
            'ncf_tipo' => 'sometimes|string|max:20',
            'estado' => 'sometimes|string|max:20',
            'descuento' => 'sometimes|numeric|min:0',
            'notas' => 'nullable|string',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $venta->update($validated);

        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos']));
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return response()->json(['message' => 'Venta eliminada.']);
    }

    public function resumen(Request $request)
    {
        $query = Venta::where('estado', '!=', 'cancelada');

        if ($request->sucursal_id) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->fecha_desde) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        return response()->json([
            'total_ventas' => $query->count(),
            'total_ingresos' => $query->sum('total'),
            'total_subtotal' => $query->sum('subtotal'),
            'total_impuestos' => $query->sum('impuestos'),
            'total_descuentos' => $query->sum('descuento'),
            'promedio_ticket' => $query->avg('total'),
        ]);
    }
}
