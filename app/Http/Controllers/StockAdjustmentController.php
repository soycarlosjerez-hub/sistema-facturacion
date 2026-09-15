<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['producto', 'sucursal', 'almacen', 'user'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('producto', fn($q) => $q->where('nombre', 'like', "%{$request->search}%"))
                  ->orWhere('motivo', 'like', "%{$request->search}%")
                  ->orWhere('notas', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $adjustments = $query->paginate(20);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $almacenes = Almacen::orderBy('nombre')->get();
        return view('stock-adjustments.create', compact('productos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'almacen_id' => 'nullable|exists:almacenes,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_anterior' => 'required|integer|min:0',
            'cantidad_nueva' => 'required|integer|min:0',
            'tipo' => 'required|in:ajuste,merma,inventario,dacion,recepcion',
            'motivo' => 'nullable|string|max:500',
            'notas' => 'nullable|string|max:2000',
        ]);

        $validated['diferencia'] = $validated['cantidad_nueva'] - $validated['cantidad_anterior'];
        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            $adjustment = StockAdjustment::create($validated);
            $producto = Producto::findOrFail($validated['producto_id']);

            if ($validated['almacen_id']) {
                $almacenProducto = \App\Models\AlmacenProducto::firstOrCreate(
                    ['almacen_id' => $validated['almacen_id'], 'producto_id' => $validated['producto_id']],
                    ['stock_actual' => 0, 'stock_minimo' => $producto->stock_minimo]
                );

                $diferencia = $validated['cantidad_nueva'] - $almacenProducto->stock_actual;
                $almacenProducto->stock_actual = $validated['cantidad_nueva'];
                $almacenProducto->save();

                \App\Models\AlmacenMovimiento::create([
                    'almacen_id' => $validated['almacen_id'],
                    'producto_id' => $validated['producto_id'],
                    'tipo' => $diferencia > 0 ? 'entrada' : 'salida',
                    'cantidad' => abs($diferencia),
                    'nota' => 'Ajuste de inventario: ' . $adjustment->motivo,
                    'user_id' => auth()->id(),
                ]);
            }

            $nuevoStockGlobal = max(0, $producto->stock + $validated['diferencia']);
            Producto::where('id', $validated['producto_id'])->update(['stock' => $nuevoStockGlobal]);
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Ajuste de stock registrado correctamente.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['producto', 'sucursal', 'almacen', 'user']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }
}
