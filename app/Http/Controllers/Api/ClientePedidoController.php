<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientePedidoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cliente = $request->user();

        $ordenes = Orden::with(['detalles.producto', 'sucursal', 'entregaEmpresa'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'pedidos' => $ordenes->through(fn($o) => $this->resource($o)),
            'pagination' => [
                'total'         => $ordenes->total(),
                'per_page'      => $ordenes->perPage(),
                'current_page'  => $ordenes->currentPage(),
                'last_page'     => $ordenes->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $cliente = $request->user();

        $orden = Orden::with(['detalles.producto', 'sucursal', 'pagos', 'entregaEmpresa'])
            ->where('cliente_id', $cliente->id)
            ->findOrFail($id);

        return response()->json(['pedido' => $this->resource($orden)]);
    }

    private function resource(Orden $orden): array
    {
        return [
            'id'                => $orden->id,
            'tipo_orden'        => $orden->tipo_orden,
            'estado'            => $orden->estado,
            'subtotal'          => $orden->subtotal,
            'impuestos'         => $orden->impuestos,
            'descuento'         => $orden->descuento,
            'delivery_fee'      => $orden->delivery_fee,
            'total'             => $orden->total,
            'direccion_entrega' => $orden->direccion_entrega,
            'telefono_contacto' => $orden->telefono_contacto,
            'notas'             => $orden->notas,
            'sucursal'          => $orden->sucursal ? [
                'id'   => $orden->sucursal->id,
                'nombre' => $orden->sucursal->nombre,
            ] : null,
            'empresa_entrega' => $orden->entregaEmpresa ? [
                'id'   => $orden->entregaEmpresa->id,
                'nombre' => $orden->entregaEmpresa->nombre,
            ] : null,
            'detalles' => $orden->relationLoaded('detalles')
                ? $orden->detalles->map(fn($d) => [
                    'id'             => $d->id,
                    'producto'       => $d->producto ? $d->producto->nombre : null,
                    'cantidad'       => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'subtotal'       => $d->subtotal,
                ])
                : [],
            'created_at' => $orden->created_at,
            'updated_at' => $orden->updated_at,
        ];
    }
}
