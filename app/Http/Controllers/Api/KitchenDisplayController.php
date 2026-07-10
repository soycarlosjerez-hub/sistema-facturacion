<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenDetalle;
use App\Services\OrdenKitchenService;
use Illuminate\Http\Request;

class KitchenDisplayController extends Controller
{
    public function __construct(
        protected OrdenKitchenService $kitchenService
    ) {}

    public function index()
    {
        return response()->json($this->kitchenService->getOrders());
    }

    public function updateStatus(Request $request, OrdenDetalle $detalle)
    {
        $validated = $request->validate([
            'estado_cocina' => 'required|string|in:pendiente,en_preparacion,listo,entregado',
        ]);

        $result = $this->kitchenService->updateDetalleState($detalle, $validated['estado_cocina']);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json($result);
    }
}
