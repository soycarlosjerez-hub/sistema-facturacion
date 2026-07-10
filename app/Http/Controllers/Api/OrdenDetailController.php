<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdenDetalleResource;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Services\OrdenService;
use Illuminate\Http\Request;

class OrdenDetailController extends Controller
{
    public function __construct(
        protected OrdenService $ordenService
    ) {}

    public function store(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'notas'       => 'nullable|string|max:200',
            'curso'       => 'nullable|string|in:entrada,fuerte,postre,bebida',
        ]);

        $result = $this->ordenService->agregarItem(
            $orden,
            $validated['producto_id'],
            $validated['cantidad'],
            $validated['notas'] ?? null,
            $validated['curso'] ?? null
        );

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return new OrdenDetalleResource($result['detalle']);
    }

    public function update(Request $request, Orden $orden, OrdenDetalle $detalle)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $result = $this->ordenService->actualizarItem($orden, $detalle, $validated['cantidad']);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['orden' => $result['orden']]);
    }

    public function destroy(Orden $orden, OrdenDetalle $detalle)
    {
        $result = $this->ordenService->quitarItem($orden, $detalle);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['orden' => $result['orden']]);
    }
}
