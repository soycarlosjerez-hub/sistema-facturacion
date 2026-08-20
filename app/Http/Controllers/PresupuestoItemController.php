<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\PresupuestoItem;
use App\Http\Requests\StorePresupuestoItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresupuestoItemController extends Controller
{
    /**
     * Add an item to a presupuesto.
     * Route: POST /presupuestos/{presupuesto}/items
     */
    public function store(StorePresupuestoItemRequest $request, Presupuesto $presupuesto)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $producto = null;

            if (!empty($data['producto_id'])) {
                $producto = \App\Models\Producto::find($data['producto_id']);
            }

            $data['tenant_id'] = auth()->user()->business_instance_id;
            $data['presupuesto_id'] = $presupuesto->id;

            $item = new PresupuestoItem($data);

            if ($producto && empty($data['descripcion'])) {
                $item->descripcion = $producto->nombre;
            }

            $item->calcular();
            $presupuesto->items()->save($item);

            // Recalculate totals
            $presupuesto->calcularTotales();

            DB::commit();

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'descripcion' => $item->descripcion,
                    'cantidad' => (float) $item->cantidad,
                    'precio_unitario' => (float) $item->precio_unitario,
                    'descuento' => (float) $item->descuento,
                    'subtotal' => (float) $item->subtotal,
                    'linea_total' => $item->linea_total,
                ],
                'subtotal' => number_format($presupuesto->subtotal, 2),
                'itbis' => number_format($presupuesto->itbis, 2),
                'total' => number_format($presupuesto->total, 2),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove an item from a presupuesto.
     * Route: DELETE /presupuestos/{presupuesto}/items/{item}
     */
    public function destroy(Presupuesto $presupuesto, PresupuestoItem $item)
    {
        // Verify ownership
        if ($item->presupuesto_id !== $presupuesto->id) {
            return response()->json(['success' => false, 'message' => 'Item no pertenece a este presupuesto.'], 403);
        }

        try {
            DB::beginTransaction();

            $item->delete();

            // Recalculate totals
            $presupuesto->calcularTotales();

            DB::commit();

            return response()->json([
                'success' => true,
                'subtotal' => number_format($presupuesto->subtotal, 2),
                'itbis' => number_format($presupuesto->itbis, 2),
                'total' => number_format($presupuesto->total, 2),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
