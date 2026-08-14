<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    private function sucursalId(): ?int
    {
        return session('sucursal_id');
    }

    public function resumen(): array
    {
        $sucursalId = $this->sucursalId();

        $ventasHoy = Venta::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', today())->sum('total');

        $ventasMes = Venta::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

        $comprasMes = Compra::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->sum('total');

        $productosBajoStock = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')->count();

        $sesionesAbiertas = SesionCaja::where('estado', 'abierta')->count();

        $stats = Venta::when($sucursalId, fn($q) => $q->where('ventas.sucursal_id', $sucursalId))
            ->whereMonth('ventas.created_at', now()->month)->whereYear('ventas.created_at', now()->year)
            ->join('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->selectRaw('
                COALESCE(SUM(ventas.total), 0) as total_ventas,
                COALESCE(SUM(venta_detalles.cantidad * productos.precio_compra), 0) as costo_mes
            ')
            ->first();

        $utilidadMes = ((float)$stats->total_ventas) - ((float)$stats->costo_mes);

        $sucursales = Sucursal::orderBy('nombre')->get();
        $sucursalActiva = $sucursalId ? Sucursal::find($sucursalId) : null;

        return compact(
            'ventasHoy', 'ventasMes', 'comprasMes',
            'productosBajoStock', 'sesionesAbiertas', 'utilidadMes',
            'sucursales', 'sucursalActiva'
        );
    }

    public function ventas(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();
        $query = Venta::query()
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc');

        $ventas = $query->with('cliente:id,nombre,rnc_cedula', 'usuario:id,name', 'caja:id,nombre', 'pagos')->get();

        $ventasPorCajero = Venta::selectRaw('
                COALESCE(user_id, 0) as user_id,
                COUNT(*) as cantidad,
                SUM(total) as total,
                SUM(subtotal) as subtotal,
                SUM(impuestos) as itbis
            ')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $pagosEfectivo = Venta::selectRaw('COALESCE(SUM(pagos.monto), 0) as total_efectivo')
            ->join('pagos', 'pagos.venta_id', '=', 'ventas.id')
            ->when($sucursalId, fn($q) => $q->where('ventas.sucursal_id', $sucursalId))
            ->where('pagos.metodo_pago', 'efectivo')
            ->whereDate('ventas.created_at', '>=', $desde)
            ->whereDate('ventas.created_at', '<=', $hasta)
            ->first();

        $totalCajas = Venta::selectRaw('COUNT(DISTINCT caja_id) as count')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->whereNotNull('caja_id')
            ->value('count') ?? 0;

        $cajerosMap = $ventasPorCajero->map(function ($item) {
            $usuario = $item->user_id ? (\App\Models\User::find($item->user_id)) : null;
            return [
                'cajero_id'       => $item->user_id,
                'cajero_nombre'   => $usuario?->name ?? 'Sin Cajero',
                'cantidad'        => $item->cantidad,
                'total'           => $item->total,
                'subtotal'        => $item->subtotal,
                'itbis'           => $item->itbis,
                'efectivo'        => 0,
                'cajas_usadas'    => [],
                'cajas_count'     => 0,
            ];
        })->sortByDesc('total')->values();

        $totalEfectivo = (float) $pagosEfectivo->total_efectivo;

        return [
            'ventas'        => $ventas,
            'desde'         => $desde,
            'hasta'         => $hasta,
            'totalGeneral'  => $ventas->sum('total'),
            'totalItbis'    => $ventas->sum('impuestos'),
            'totalEfectivo' => $totalEfectivo,
            'cantidad'      => $ventas->count(),
            'ventasPorCajero' => $cajerosMap,
            'totalCajas'    => $totalCajas,
        ];
    }

    public function compras(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();

        $totals = Compra::selectRaw('
                COUNT(*) as cantidad,
                SUM(total) as total_general,
                SUM(itbis_total) as total_itbis,
                SUM(retencion_isr) as total_isr,
                SUM(retencion_itbis) as total_itbis_ret
            ')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->first();

        $compras = Compra::with('proveedor:id,nombre,rnc', 'user:id,name')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha', 'desc')
            ->get();

        return [
            'compras'          => $compras,
            'desde'            => $desde,
            'hasta'            => $hasta,
            'totalGeneral'     => (float) $totals->total_general,
            'totalItbis'       => (float) $totals->total_itbis,
            'totalRetenciones' => (float) ($totals->total_isr + $totals->total_itbis_ret),
            'cantidad'         => (int) $totals->cantidad,
        ];
    }

    public function gastos(string $desde, string $hasta, ?string $categoria = null): array
    {
        $sucursalId = $this->sucursalId();

        $gastos = Gasto::with('user:id,name')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha_gasto', '>=', $desde)
            ->whereDate('fecha_gasto', '<=', $hasta)
            ->when($categoria, fn($q) => $q->ofCategoria($categoria))
            ->orderBy('fecha_gasto', 'desc')
            ->get();

        $totalPorCategoria = Gasto::selectRaw('categoria, SUM(monto) as total, COUNT(*) as count')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha_gasto', '>=', $desde)
            ->whereDate('fecha_gasto', '<=', $hasta)
            ->when($categoria, fn($q) => $q->ofCategoria($categoria))
            ->groupBy('categoria')
            ->get()
            ->keyBy('categoria');

        return [
            'gastos'            => $gastos,
            'desde'             => $desde,
            'hasta'             => $hasta,
            'categoria'         => $categoria,
            'totalGeneral'      => $gastos->sum('monto'),
            'cantidad'          => $gastos->count(),
            'totalPorCategoria' => $totalPorCategoria,
            'categorias'        => Gasto::categorias(),
        ];
    }

    public function stock(string $filtro = 'todos', ?string $buscar = null): array
    {
        $query = Producto::query()
            ->withCount(['movimientosAlmacen as stock_movimientos_count' => function ($q) {
                $q->select(DB::raw('SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END)'));
            }]);

        if ($filtro === 'bajo_stock') {
            $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo');
        } elseif ($filtro === 'sin_stock') {
            $query->where('stock', '<=', 0);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo_barras', 'like', "%{$buscar}%")
                  ->orWhere('referencia', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('stock', 'asc')->get();
        $totalProductos = $productos->count();
        $totalValorInventario = $productos->sum(fn($p) => $p->stock * ($p->precio_compra ?? 0));
        $bajoStock = $productos->filter(fn($p) => $p->stock > 0 && $p->stock <= ($p->stock_minimo ?? 0))->count();
        $sinStock = $productos->filter(fn($p) => $p->stock <= 0)->count();
        $almacenes = Almacen::orderBy('nombre')->get();

        return compact(
            'productos', 'totalProductos', 'totalValorInventario', 'bajoStock', 'sinStock',
            'filtro', 'buscar', 'almacenes'
        );
    }

    public function caja(string $desde, string $hasta, ?int $cajaId = null): array
    {
        $query = SesionCaja::with('caja:id,nombre', 'user:id,name')
            ->whereDate('fecha_apertura', '>=', $desde)
            ->whereDate('fecha_apertura', '<=', $hasta)
            ->orderBy('fecha_apertura', 'desc');

        if ($this->sucursalId()) {
            $query->whereHas('caja', fn($q) => $q->where('sucursal_id', $this->sucursalId()));
        }

        if ($cajaId) {
            $query->where('caja_id', $cajaId);
        }

        $sesiones = $query->get();
        $totalVentas = $sesiones->sum(fn($s) => $s->ventas_efectivo + $s->ventas_tarjeta + $s->ventas_transferencia);
        $totalDescuadre = $sesiones->sum('descuadre');
        $cantidad = $sesiones->count();
        $abiertas = $sesiones->where('estado', 'abierta')->count();
        $cerradas = $sesiones->where('estado', 'cerrada')->count();
        $cajas = Caja::orderBy('nombre')->get();

        return compact(
            'sesiones', 'desde', 'hasta', 'totalVentas', 'totalDescuadre', 'cantidad',
            'abiertas', 'cerradas', 'cajas'
        );
    }

    public function utilidades(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();

        $totals = Venta::selectRaw('
                COALESCE(SUM(total), 0) as total_ventas,
                COALESCE(SUM(impuestos), 0) as total_itbis
            ')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->first();

        $costoDetails = VentaDetalle::selectRaw('
                vd.venta_id,
                v.created_at as fecha,
                COALESCE(vc.nombre, "Consumidor Final") as cliente,
                COALESCE(vp.nombre, vd.nombre) as producto,
                SUM(vd.cantidad) as cantidad,
                AVG(vd.precio_unitario) as precio,
                COALESCE(SUM(vd.cantidad * vp.precio_compra), 0) as costo,
                COALESCE(SUM(vd.subtotal), 0) as subtotal
            ')
            ->join('ventas as v', 'v.id', '=', 'vd.venta_id')
            ->leftJoin('productos as vp', 'vp.id', '=', 'vd.producto_id')
            ->leftJoin('clientes as vc', 'vc.id', '=', 'v.cliente_id')
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereDate('v.created_at', '>=', $desde)
            ->whereDate('v.created_at', '<=', $hasta)
            ->groupBy('vd.venta_id', 'v.created_at', 'vc.nombre', 'vp.nombre', 'vd.nombre', 'vd.cantidad', 'vd.precio_unitario', 'vd.subtotal')
            ->get();

        $ventas = Venta::with('cliente:id,nombre', 'detalles')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->get();

        $detalles = collect();
        $totalCosto = 0;
        $totalProductosVendidos = 0;

        foreach ($ventas as $v) {
            foreach ($v->detalles as $d) {
                $costoUnitario = $d->producto?->precio_compra ?? 0;
                $costoLinea = $costoUnitario * $d->cantidad;
                $totalCosto += $costoLinea;
                $totalProductosVendidos += $d->cantidad;
                $detalles->push([
                    'venta_id'  => $v->id,
                    'fecha'     => $v->created_at->format('d/m/Y'),
                    'cliente'   => $v->cliente?->nombre ?? 'Consumidor Final',
                    'producto'  => $d->producto?->nombre ?? $d->nombre ?? 'Producto',
                    'cantidad'  => $d->cantidad,
                    'precio'    => $d->precio_unitario ?? 0,
                    'costo'     => $costoUnitario,
                    'subtotal'  => $d->subtotal ?? 0,
                    'ganancia'  => ($d->subtotal ?? 0) - $costoLinea,
                ]);
            }
        }

        $totalVentas = (float) $totals->total_ventas;
        $utilidadBruta = $totalVentas - $totalCosto;
        $margen = $totalVentas > 0 ? ($utilidadBruta / $totalVentas) * 100 : 0;

        return compact(
            'detalles', 'desde', 'hasta', 'totalVentas', 'totalCosto',
            'totalItbis', 'utilidadBruta', 'margen', 'totalProductosVendidos'
        );
    }

    public function retenciones(int $mes, int $anio, string $tipo): array
    {
        $compras = collect();
        $ventas = collect();

        if ($tipo === 'compras' || $tipo === 'ambos') {
            $compras = Compra::with('proveedor')
                ->when($this->sucursalId(), fn($q) => $q->where('sucursal_id', $this->sucursalId()))
                ->whereMonth('fecha', $mes)->whereYear('fecha', $anio)
                ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
                ->get();
        }

        if ($tipo === 'ventas' || $tipo === 'ambos') {
            $ventas = Venta::with('cliente')
                ->when($this->sucursalId(), fn($q) => $q->where('sucursal_id', $this->sucursalId()))
                ->whereMonth('created_at', $mes)->whereYear('created_at', $anio)
                ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
                ->get();
        }

        $totalRetIsr = $compras->sum('retencion_isr') + $ventas->sum('retencion_isr');
        $totalRetItbis = $compras->sum('retencion_itbis') + $ventas->sum('retencion_itbis');
        $totalGeneral = $totalRetIsr + $totalRetItbis;

        return compact('compras', 'ventas', 'mes', 'anio', 'tipo', 'totalRetIsr', 'totalRetItbis', 'totalGeneral');
    }

    public function restaurante(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();

        $ventasPorMesero = Venta::select('user_id', DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas'))
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->groupBy('user_id')->with('usuario:id,name')->get();

        $ventasPorMesa = Venta::select('mesa_id', DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas'))
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->groupBy('mesa_id')->with('mesa:id,numero,nombre')->get();

        $ventasPorHora = Venta::select(
            DB::raw('CASE WHEN HOUR(created_at) < 17 THEN "Almuerzo (6AM-4PM)" ELSE "Cena (5PM-11PM)" END as turno'),
            DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas')
        )->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->groupBy('turno')->get();

        $productosTop = VentaDetalle::select('producto_id', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('SUM(subtotal) as total_ventas'))
            ->whereHas('venta', fn($q) => $q->whereNotNull('mesa_id')
                ->when($sucursalId, fn($sq) => $sq->where('sucursal_id', $sucursalId))
                ->whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta))
            ->groupBy('producto_id')->orderByDesc('total_cantidad')->limit(20)
            ->with('producto:id,nombre')->get();

        $sucursales = Sucursal::orderBy('nombre')->get();

        return compact('desde', 'hasta', 'sucursales', 'sucursalId',
            'ventasPorMesero', 'ventasPorMesa', 'ventasPorHora', 'productosTop');
    }

    public function propinas(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();
        $propinas = Venta::select(
                'user_id',
                DB::raw('SUM(propina) as total_propinas'),
                DB::raw('COUNT(*) as total_ordenes'),
                DB::raw('AVG(propina) as promedio_propina')
            )
            ->where('propina', '>', 0)
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->groupBy('user_id')
            ->with('usuario:id,name')
            ->get();

        $totalGlobal = $propinas->sum('total_propinas');
        $ordenesConPropina = $propinas->sum('total_ordenes');

        return compact('propinas', 'desde', 'hasta', 'totalGlobal', 'ordenesConPropina');
    }

    public function comisionesDelivery(string $desde, string $hasta): array
    {
        $sucursalId = $this->sucursalId();
        $companies = DeliveryCompany::orderBy('nombre')->get();

        $ventas = Venta::with('deliveryCompany', 'mesa')
            ->whereNotNull('delivery_company_id')
            ->whereNotNull('delivery_fee')
            ->where('delivery_fee', '>', 0)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc')
            ->get();

        $porCompany = $companies->map(fn($c) => [
            'id'        => $c->id,
            'nombre'    => $c->nombre,
            'total_fee' => $ventas->where('delivery_company_id', $c->id)->sum('delivery_fee'),
            'ventas'    => $ventas->where('delivery_company_id', $c->id)->count(),
        ]);

        return [
            'companies' => $porCompany,
            'detalles'  => $ventas,
            'desde'     => $desde,
            'hasta'     => $hasta,
            'totalFees' => $ventas->sum('delivery_fee'),
        ];
    }

    public function exportCsv(array $data, array $headers, \Closure $rows): \Illuminate\Http\Response
    {
        $callback = function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, $headers);
            $rows($output);
            fclose($output);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename={$data['filename']}",
        ]);
    }
}
