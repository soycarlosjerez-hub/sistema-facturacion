<?php

namespace App\Http\Controllers;

use App\Models\OrdenDetalle;
use App\Services\OrdenKitchenService;
use Illuminate\Http\Request;

class PosKdsController extends Controller
{
    public function __construct(
        protected OrdenKitchenService $kitchenService
    ) {}

    public function index()
    {
        return view('ordenes.kds');
    }

    public function orders()
    {
        return response()->json($this->kitchenService->getOrders());
    }

    public function updateEstado(Request $request, OrdenDetalle $detalle)
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
