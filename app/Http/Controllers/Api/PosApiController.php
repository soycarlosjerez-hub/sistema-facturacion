<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    protected PosService $service;

    public function __construct(PosService $service)
    {
        $this->service = $service;
    }

    /**
     * POST /api/pos/checkout
     * Procesa venta mixta (servicio + productos + paquetes)
     */
    public function checkout(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente_id'   => 'nullable|exists:clientes,id',
                'vehiculo_id'  => 'nullable|exists:vehiculos,id',
                'metodo_pago'  => 'required|string|in:efectivo,tarjeta,transferencia,fiado',
                'tipo_venta_id'=> 'nullable|exists:tipos_ventas,id',
                'servicios'    => 'nullable|array',
                'servicios.*'  => 'integer|exists:lavadero_servicios,id',
                'productos'    => 'nullable|array',
                'productos.*.id'   => 'integer|exists:productos,id',
                'productos.*.cantidad' => 'integer|min:1',
                'paquetes'     => 'nullable|array',
                'paquetes.*.id'    => 'integer|exists:lavadero_paquetes,id',
                'paquetes.*.cantidad' => 'integer|min:1',
            ]);

            // Build items with prices
            if (!empty($data['servicios'])) {
                foreach ($data['servicios'] as $i => $id) {
                    $servicio = \App\Models\LavaderoServicio::find($id);
                    $data['servicios'][$i] = [
                        'id'   => $id,
                        'precio' => (float) $servicio->precio,
                    ];
                }
            }

            if (!empty($data['productos'])) {
                foreach ($data['productos'] as $i => &$prod) {
                    $producto = \App\Models\Producto::find($prod['id']);
                    $prod['precio'] = (float) ($producto->precio ?? 0);
                    $prod['cantidad'] = $prod['cantidad'] ?? 1;
                }
            }

            if (!empty($data['paquetes'])) {
                foreach ($data['paquetes'] as $i => &$paq) {
                    $paquete = \App\Models\LavaderoPaquete::find($paq['id']);
                    $paq['precio'] = (float) ($paquete->precio ?? 0);
                    $paq['cantidad'] = $paq['cantidad'] ?? 1;
                }
            }

            $result = $this->service->checkout($data);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /api/pos/quick-sale?search=xxx&linea=alimentos
     * Búsqueda rápida de productos
     */
    public function quickSale(Request $request)
    {
        $query = $request->input('search', '');
        $linea = $request->input('linea');

        if (strlen($query) < 2) {
            return response()->json(['productos' => []]);
        }

        $productos = $this->service->quickSearch($query, $linea);

        return response()->json([
            'productos' => $productos->map(function ($p) {
                return [
                    'id'           => (int) $p->id,
                    'nombre'       => $p->nombre,
                    'codigo_barras'=> $p->codigo_barras,
                    'precio'       => (float) $p->precio,
                    'stock'        => (int) $p->stock,
                    'categoria_id' => (int) ($p->categoria_id ?? 0),
                ];
            }),
        ]);
    }

    /**
     * POST /api/pos/hold
     * Guarda venta en espera
     */
    public function hold(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente_id'  => 'nullable|exists:clientes,id',
                'vehiculo_id' => 'nullable|exists:vehiculos,id',
                'servicios'   => 'nullable|array',
                'productos'   => 'nullable|array',
                'paquetes'    => 'nullable|array',
                'metodo_pago' => 'nullable|string|in:efectivo,tarjeta,transferencia',
                'total'       => 'nullable|numeric|min:0',
            ]);

            $result = $this->service->holdSale($data);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/pos/restore/{hold_id}
     * Restaura venta en espera
     */
    public function restore(Request $request, string $holdId)
    {
        try {
            $data = $this->service->restoreSale($holdId);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/pos/calculate-total
     * Calcula el total de los items
     */
    public function calculateTotal(Request $request)
    {
        $items = $request->input('items', []);

        return response()->json($this->service->calculateTotal($items));
    }
}
