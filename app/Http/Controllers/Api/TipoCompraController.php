<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoCompraResource;
use App\Models\TipoCompra;
use Illuminate\Http\Request;

class TipoCompraController extends Controller
{
    public function index()
    {
        return TipoCompraResource::collection(TipoCompra::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipo = TipoCompra::create($validated);

        return new TipoCompraResource($tipo);
    }

    public function show(TipoCompra $tipoCompra)
    {
        return new TipoCompraResource($tipoCompra);
    }

    public function update(Request $request, TipoCompra $tipoCompra)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
        ]);

        $tipoCompra->update($validated);

        return new TipoCompraResource($tipoCompra);
    }

    public function destroy(TipoCompra $tipoCompra)
    {
        $tipoCompra->delete();
        return response()->json(['message' => 'Tipo de compra eliminado.']);
    }
}
