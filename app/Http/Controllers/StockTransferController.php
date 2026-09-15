<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\Sucursal;
use App\Models\Producto;
use App\Models\AlmacenProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['sucursal_origen', 'sucursal_destino', 'user'])
            ->latest();

        if ($request->filled('search')) {
            $query->where('codigo', 'like', "%{$request->search}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('sucursal_id')) {
            $query->where(function($q) use ($request) {
                $q->where('sucursal_origen_id', $request->sucursal_id)
                  ->orWhere('sucursal_destino_id', $request->sucursal_id);
            });
        }

        $transfers = $query->paginate(20);
        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();
        return view('stock-transfers.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sucursal_origen_id' => 'required|exists:sucursales,id',
            'sucursal_destino_id' => 'required|exists:sucursales,id|different:sucursal_origen_id',
            'notas' => 'nullable|string|max:500',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $transfer = StockTransfer::create([
                'sucursal_origen_id' => $validated['sucursal_origen_id'],
                'sucursal_destino_id' => $validated['sucursal_destino_id'],
                'notas' => $validated['notas'] ?? null,
                'user_id' => auth()->id(),
                'estado' => 'borrador',
            ]);

            foreach ($validated['productos'] as $producto) {
                StockTransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'producto_id' => (int) $producto['id'],
                    'cantidad' => (int) $producto['cantidad'],
                    'recibido' => 0,
                ]);
            }
        });

        return redirect()->route('stock-transfers.index')
            ->with('success', 'Transferencia creada correctamente.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['detalle.producto', 'sucursal_origen', 'sucursal_destino', 'user']);
        return view('stock-transfers.show', compact('stockTransfer'));
    }

    public function enviar(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->estado === 'borrador' || $stockTransfer->estado === 'aprobada') {
            $stockTransfer->update(['estado' => 'enviada']);
        }
        return redirect()->back()->with('success', 'Transferencia marcada como enviada.');
    }

    public function recibir(Request $request, StockTransfer $stockTransfer)
    {
        $validated = $request->validate([
            'detalles' => 'required|array',
            'detalles.*.id' => 'required|exists:stock_transfer_details,id',
            'detalles.*.recibido' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $stockTransfer) {
            foreach ($validated['detalles'] as $detalle) {
                $detail = StockTransferDetail::findOrFail($detalle['id']);
                if ($detail->transfer_id === $stockTransfer->id) {
                    $detail->recibido = (int) $detalle['recibido'];
                    $detail->save();

                    // Update destination warehouse stock
                    // This requires knowing which warehouse to update
                    // For now, just update the global product stock
                    if ($detail->recibido >= $detail->cantidad) {
                        Producto::where('id', $detail->producto_id)
                            ->increment('stock', $detail->cantidad);
                    }
                }
            }

            // Check if all items received
            $allReceived = $stockTransfer->detalle->every(function ($d) {
                return $d->recibido >= $d->cantidad;
            });

            if ($allReceived) {
                $stockTransfer->update(['estado' => 'recibida']);
            }
        });

        return redirect()->back()->with('success', 'Recibido correctamente.');
    }
}
