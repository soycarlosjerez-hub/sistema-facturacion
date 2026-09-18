<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcommOrderController extends Controller
{
    /**
     * GET /api/ecomm/orders?customer_id=123
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:clientes,id',
        ]);

        $ventas = Venta::where('cliente_id', $validated['customer_id'])
            ->with('detalles.producto')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $ventas->through(function ($v) {
                return [
                    'id' => $v->id,
                    'numero' => $v->ncf ?? 'V-' . $v->id,
                    'estado' => $v->estado,
                    'total' => (float) $v->total,
                    'fecha' => $v->created_at?->toISOString() ?? now()->toISOString(),
                    'items' => $v->relationLoaded('detalles')
                        ? $v->detalles->map(fn ($d) => [
                            'producto' => $d->producto?->nombre,
                            'cantidad' => $d->cantidad,
                            'precio_unitario' => (float) $d->precio_unitario,
                            'subtotal' => (float) $d->subtotal,
                        ])->values()
                        : [],
                ];
            }),
            'meta' => [
                'total' => $ventas->total(),
                'per_page' => $ventas->perPage(),
                'current_page' => $ventas->currentPage(),
                'last_page' => $ventas->lastPage(),
            ],
        ]);
    }
}
