<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Venta;
use App\Models\Proveedor;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LibroRetencionesService
{
    private function sucursalId(): ?int
    {
        return session('sucursal_id');
    }

    /**
     * Obtiene todos los datos necesarios para la vista consolidada de libros de retenciones.
     */
    public function index(Request $request): array
    {
        $mes = (int) $request->input('mes', now()->month);
        $anio = (int) $request->input('anio', now()->year);
        $tipo = $request->input('tipo', 'ambos');
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $sucursalId = $this->sucursalId();

        // ── Compras con retenciones ──────────────────────────────────────
        $comprasQuery = Compra::with(['proveedor:id,nombre,rnc', 'sucursal:id,nombre'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0));

        if ($sucursalId) {
            $comprasQuery->where('sucursal_id', $sucursalId);
        }

        $compras = $comprasQuery->orderBy('fecha', 'desc')->paginate(25)->withQueryString();

        // ── Ventas con retenciones ───────────────────────────────────────
        $ventasQuery = Venta::with(['cliente:id,nombre,rnc_cedula', 'sucursal:id,nombre'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0));

        if ($sucursalId) {
            $ventasQuery->where('sucursal_id', $sucursalId);
        }

        $ventas = $ventasQuery->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // ── Resumen agregado ─────────────────────────────────────────────
        $comprasTotales = Compra::whereBetween('fecha', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->selectRaw('
                COALESCE(SUM(retencion_isr), 0) as total_isr,
                COALESCE(SUM(retencion_itbis), 0) as total_itbis,
                COUNT(*) as cantidad
            ')
            ->first();

        $ventasTotales = Venta::whereBetween('created_at', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->selectRaw('
                COALESCE(SUM(retencion_isr), 0) as total_isr,
                COALESCE(SUM(retencion_itbis), 0) as total_itbis,
                COUNT(*) as cantidad
            ')
            ->first();

        $resumen = [
            'total_isr_compras'     => (float) ($comprasTotales->total_isr ?? 0),
            'total_itbis_compras'   => (float) ($comprasTotales->total_itbis ?? 0),
            'cantidad_compras'      => (int) ($comprasTotales->cantidad ?? 0),
            'total_isr_ventas'      => (float) ($ventasTotales->total_isr ?? 0),
            'total_itbis_ventas'    => (float) ($ventasTotales->total_itbis ?? 0),
            'cantidad_ventas'       => (int) ($ventasTotales->cantidad ?? 0),
            'gran_total'            => (float) (($comprasTotales->total_isr ?? 0) + ($comprasTotales->total_itbis ?? 0) + ($ventasTotales->total_isr ?? 0) + ($ventasTotales->total_itbis ?? 0)),
        ];

        // ── Agrupación por Proveedor (compras) ───────────────────────────
        $porProveedor = Compra::selectRaw('
                proveedores.id as proveedor_id,
                proveedores.nombre,
                proveedores.rnc,
                COUNT(*) as cantidad_compras,
                COALESCE(SUM(retencion_isr), 0) as total_isr,
                COALESCE(SUM(retencion_itbis), 0) as total_itbis,
                COALESCE(SUM(retencion_isr + retencion_itbis), 0) as total_retenido
            ')
            ->join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
            ->whereBetween('compras.fecha', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('compras.sucursal_id', $sucursalId))
            ->groupBy('proveedores.id', 'proveedores.nombre', 'proveedores.rnc')
            ->orderByDesc('total_retenido')
            ->get();

        // ── Agrupación por Cliente (ventas) ──────────────────────────────
        $porCliente = Venta::selectRaw('
                clientes.id as cliente_id,
                clientes.nombre,
                clientes.rnc_cedula,
                COUNT(*) as cantidad_ventas,
                COALESCE(SUM(retencion_isr), 0) as total_isr,
                COALESCE(SUM(retencion_itbis), 0) as total_itbis,
                COALESCE(SUM(retencion_isr + retencion_itbis), 0) as total_retenido
            ')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->whereBetween('ventas.created_at', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('ventas.sucursal_id', $sucursalId))
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.rnc_cedula')
            ->orderByDesc('total_retenido')
            ->get();

        return compact(
            'compras', 'ventas', 'resumen', 'porProveedor', 'porCliente',
            'mes', 'anio', 'tipo', 'desde', 'hasta'
        );
    }

    /**
     * Datos crudos para exportación Excel (sin paginar).
     */
    public function exportData(Request $request): array
    {
        $mes = (int) $request->input('mes', now()->month);
        $anio = (int) $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $sucursalId = $this->sucursalId();

        $compras = Compra::with(['proveedor:id,nombre,rnc', 'sucursal:id,nombre'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('fecha', 'desc')
            ->get();

        $ventas = Venta::with(['cliente:id,nombre,rnc_cedula', 'sucursal:id,nombre'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('created_at', 'desc')
            ->get();

        return compact('compras', 'ventas', 'mes', 'anio');
    }
}
