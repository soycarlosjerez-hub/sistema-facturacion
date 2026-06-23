<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\TipoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class CotizacionService
{
    public function list(array $filters = []): array
    {
        $query = Cotizacion::with(['cliente', 'user', 'sucursal'])->orderBy('id', 'desc');

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($buscar = $filters['buscar'] ?? null) {
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', fn($cq) => $cq->where('nombre', 'like', "%{$buscar}%")->orWhere('documento', 'like', "%{$buscar}%"));
            });
        }

        if ($estado = $filters['estado'] ?? null) {
            $query->where('estado', $estado);
        }
        if ($desde = $filters['fecha_desde'] ?? null) {
            $query->where('fecha', '>=', $desde);
        }
        if ($hasta = $filters['fecha_hasta'] ?? null) {
            $query->where('fecha', '<=', $hasta);
        }
        if (!empty($filters['vencidas'])) {
            $query->vencidas();
        }

        $cotizaciones = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => Cotizacion::count(),
            'pendientes'  => Cotizacion::whereIn('estado', ['borrador', 'enviada'])->count(),
            'aprobadas'   => Cotizacion::where('estado', 'aprobada')->count(),
            'vencidas'    => Cotizacion::vencidas()->count(),
            'convertidas' => Cotizacion::where('estado', 'convertida')->count(),
            'monto_total' => Cotizacion::whereIn('estado', ['borrador', 'enviada', 'aprobada'])->sum('total'),
        ];

        return compact('cotizaciones', 'stats');
    }

    public function getCreateData(): array
    {
        $clientes = \App\Models\Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen']);
        $numero = Cotizacion::generarNumero();

        return compact('clientes', 'productos', 'numero');
    }

    public function create(array $data): Cotizacion
    {
        try {
            DB::beginTransaction();

            $cotizacion = Cotizacion::create([
                'numero'       => Cotizacion::generarNumero(),
                'cliente_id'   => $data['cliente_id'],
                'user_id'      => Auth::id(),
                'sucursal_id'  => session('sucursal_id'),
                'tenant_id'    => Auth::user()->business_instance_id ?? null,
                'fecha'        => $data['fecha'],
                'fecha_validez' => $data['fecha_validez'] ?? null,
                'estado'       => $data['estado'] ?? 'borrador',
                'descuento'    => $data['descuento'] ?? 0,
                'notas'        => $data['notas'] ?? null,
                'condiciones'  => $data['condiciones'] ?? null,
            ]);

            $this->syncItems($cotizacion, $data['items']);

            DB::commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando cotización: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getEditData(Cotizacion $cotizacion): array
    {
        if (in_array($cotizacion->estado, ['convertida', 'anulada'])) {
            abort(400, 'No se puede editar una cotización ' . $cotizacion->estado);
        }

        $clientes = \App\Models\Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen']);
        $cotizacion->load('items');

        return compact('cotizacion', 'clientes', 'productos');
    }

    public function update(Cotizacion $cotizacion, array $data): Cotizacion
    {
        if (in_array($cotizacion->estado, ['convertida', 'anulada'])) {
            abort(400, 'No se puede editar una cotización ' . $cotizacion->estado);
        }

        try {
            DB::beginTransaction();

            $cotizacion->update([
                'cliente_id'   => $data['cliente_id'],
                'fecha'        => $data['fecha'],
                'fecha_validez' => $data['fecha_validez'] ?? null,
                'descuento'    => $data['descuento'] ?? 0,
                'notas'        => $data['notas'] ?? null,
                'condiciones'  => $data['condiciones'] ?? null,
            ]);

            $cotizacion->items()->delete();
            $this->syncItems($cotizacion, $data['items']);

            DB::commit();
            return $cotizacion;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando cotización: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(Cotizacion $cotizacion): void
    {
        if ($cotizacion->estado === 'convertida') {
            abort(400, 'No se puede eliminar una cotización convertida en venta');
        }
        $cotizacion->delete();
    }

    public function cambiarEstado(Cotizacion $cotizacion, string $nuevoEstado, bool $enviarEmail = false, ?string $mensajeEmail = null): array
    {
        $estadoAnterior = $cotizacion->estado;
        $cotizacion->update(['estado' => $nuevoEstado]);

        $resultado = [
            'success' => true,
            'message' => 'Estado actualizado a: ' . $cotizacion->estado_label,
        ];

        $debeEnviar = $enviarEmail || ($estadoAnterior !== 'enviada' && $nuevoEstado === 'enviada');

        if ($debeEnviar) {
            $emailResult = app(CotizacionEmailService::class)->enviar($cotizacion, $mensajeEmail ?? '');
            if ($emailResult['success']) {
                $resultado['message'] .= ' | Email enviado a ' . $emailResult['destinatario'];
            } else {
                $resultado['message'] .= ' | ⚠️ No se pudo enviar email: ' . $emailResult['error'];
            }
        }

        return $resultado;
    }

    public function convertirAVenta(Cotizacion $cotizacion): Venta
    {
        if (!$cotizacion->puede_convertirse) {
            throw new \Exception('Esta cotización no puede ser convertida a venta');
        }

        try {
            DB::beginTransaction();

            $tipoVenta = TipoVenta::where('nombre', 'Contado')->first() ?? TipoVenta::first();
            if (!$tipoVenta) {
                throw new \Exception('No hay tipos de venta configurados');
            }

            $almacenId = Almacen::first()?->id ?? 1;
            $sesion = SesionCaja::where('user_id', Auth::id())->where('estado', 'abierta')->latest('id')->first();

            $venta = Venta::create([
                'cliente_id'      => $cotizacion->cliente_id,
                'user_id'         => Auth::id() ?? $cotizacion->user_id,
                'tipo_venta_id'   => $tipoVenta->id,
                'caja_id'         => $sesion?->caja_id,
                'fecha'           => now(),
                'subtotal'        => (float) $cotizacion->subtotal,
                'descuento'       => (float) ($cotizacion->descuento ?? 0),
                'impuestos'       => (float) $cotizacion->itbis,
                'total'           => (float) $cotizacion->total,
                'tipo_comprobante' => 'ecf',
                'estado'          => 'completada',
                'tenant_id'       => Auth::user()->business_instance_id ?? null,
            ]);

            foreach ($cotizacion->items as $item) {
                $subtotalItem = ($item->cantidad * $item->precio_unitario) - $item->descuento;
                $venta->detalles()->create([
                    'producto_id'    => $item->producto_id,
                    'almacen_id'     => $almacenId,
                    'cantidad'       => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal'       => $subtotalItem,
                    'tenant_id'      => Auth::user()->business_instance_id ?? null,
                ]);
            }

            $cotizacion->update([
                'estado'        => 'convertida',
                'venta_id'      => $venta->id,
                'convertida_en' => now(),
            ]);

            DB::commit();
            return $venta;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error convirtiendo cotización: ' . $e->getMessage());
            throw $e;
        }
    }

    public function buscarProductos(string $q)
    {
        return Producto::where(function ($query) use ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                  ->orWhere('codigo_barras', 'like', "%{$q}%");
        })->limit(20)->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen'])
        ->map(fn($p) => [
            'id'               => $p->id,
            'codigo'           => $p->codigo_barras,
            'nombre'           => $p->nombre,
            'precio'           => (float) $p->precio,
            'itbis_porcentaje' => (float) $p->itbis_porcentaje,
            'unidad'           => $p->unidad_medida ?? 'Unidad',
            'stock'            => (float) $p->stock,
            'imagen'           => $p->imagen_url,
        ]);
    }

    protected function syncItems(Cotizacion $cotizacion, array $items): void
    {
        foreach ($items as $i => $itemData) {
            $producto = !empty($itemData['producto_id']) ? Producto::find($itemData['producto_id']) : null;

            $item = new CotizacionItem([
                'producto_id'      => $itemData['producto_id'] ?? null,
                'codigo'           => $producto?->codigo_barras,
                'nombre'           => $itemData['nombre'] ?? $producto?->nombre ?? 'Item',
                'descripcion'      => $itemData['descripcion'] ?? null,
                'unidad'           => $itemData['unidad'] ?? $producto?->unidad_medida ?? 'Unidad',
                'cantidad'         => $itemData['cantidad'],
                'precio_unitario'  => $itemData['precio_unitario'],
                'descuento'        => $itemData['descuento'] ?? 0,
                'itbis_porcentaje' => $itemData['itbis_porcentaje'] ?? ($producto?->itbis_porcentaje ?? 18),
                'orden'            => $i,
                'tenant_id'        => Auth::user()->business_instance_id ?? null,
            ]);

            $item->calcular();
            $cotizacion->items()->save($item);
        }

        $cotizacion->calcularTotales();
    }
}
