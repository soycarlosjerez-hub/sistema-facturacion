<?php

namespace App\Http\Controllers;

use App\Services\PosService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    protected PosService $service;

    public function __construct(PosService $service)
    {
        $this->service = $service;
    }

    /**
     * Vista principal del POS unificado (lavadero + tienda)
     */
    public function index()
    {
        $servicios = \App\Models\LavaderoServicio::activos()->orderBy('orden')->get();
        $paquetes = \App\Models\LavaderoPaquete::activos()->orderBy('orden')->get();
        $productos = \App\Models\Producto::activos()->orderBy('nombre')->get();
        $categorias = \App\Models\Categoria::activas()->orderBy('nombre')->get();
        $lavadores = \App\Models\Lavador::activos()->orderBy('nombre')->get();
        $clientes = \App\Models\Cliente::orderBy('nombre')->limit(50)->get();

        // Cálculo de ITBIS desde la instancia
        $itbisPorcentaje = \App\Models\SystemSetting::itbisDefault();

        return view('pos.index', compact(
            'servicios', 'paquetes', 'productos', 'categorias',
            'lavadores', 'clientes', 'itbisPorcentaje'
        ));
    }

    /**
     * Procesar la venta mixta (servicio + productos + paquete)
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
                'productos'    => 'nullable|array',
                'paquetes'     => 'nullable|array',
            ]);

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
     * Búsqueda rápida de productos para el POS
     */
    public function quickSale(Request $request)
    {
        $query = $request->input('search', '');
        $linea = $request->input('linea');

        $productos = $this->service->quickSearch($query, $linea);

        return response()->json([
            'productos' => $productos->map(function ($p) {
                return [
                    'id'           => (int) $p->id,
                    'nombre'       => $p->nombre,
                    'codigo_barras'=> $p->codigo_barras,
                    'precio'       => (float) $p->precio,
                    'stock'        => (int) $p->stock,
                    'imagen'       => $p->imagen,
                    'categoria_id' => (int) $p->categoria_id,
                ];
            }),
        ]);
    }

    /**
     * Guardar venta en espera (hold)
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
     * Restaurar venta en espera
     */
    public function restore(Request $request)
    {
        try {
            $holdId = $request->input('hold_id');

            if (!$holdId) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Se requiere hold_id',
                ], 422);
            }

            $data = $this->service->restoreSale($holdId);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Calcular total de items actuales
     */
    public function calcularTotal(Request $request)
    {
        $items = $request->input('items', []);

        $result = $this->service->calculateTotal($items);

        return response()->json($result);
    }

    /**
     * Listar ventas en espera del usuario actual
     */
    public function holdsList()
    {
        $userId = auth()->id();
        $prefix = 'hold_' . $userId . '_';
        $holds = [];

        foreach (session()->all() as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $holdId = str_replace($prefix, '', $key);
                $holds[] = [
                    'hold_id'   => $holdId,
                    'data'      => $value,
                ];
            }
        }

        return response()->json(['holds' => array_values($holds)]);
    }
}
