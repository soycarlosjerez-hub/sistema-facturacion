<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoVentaResource;
use App\Models\TipoVenta;
use Illuminate\Http\Request;

class TipoVentaController extends Controller
{
    public function index()
    {
        return TipoVentaResource::collection(TipoVenta::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $tipo = TipoVenta::create($validated);

        return new TipoVentaResource($tipo);
    }

    public function show(TipoVenta $tipoVenta)
    {
        return new TipoVentaResource($tipoVenta);
    }

    public function update(Request $request, TipoVenta $tipoVenta)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $tipoVenta->update($validated);

        return new TipoVentaResource($tipoVenta);
    }

    public function destroy(TipoVenta $tipoVenta)
    {
        $tipoVenta->delete();
        return response()->json(['message' => 'Tipo de venta eliminado.']);
    }
}
