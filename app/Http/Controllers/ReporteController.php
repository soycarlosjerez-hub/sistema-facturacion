<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\AlmacenMovimiento;
use App\Models\Sucursal;
use App\Models\Almacen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    private function sucursalFiltro()
    {
        return session('sucursal_id');
    }

    public function index()
    {
        $sucursalId = $this->sucursalFiltro();

        $ventasHoy = Venta::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', today())->sum('total');

        $ventasMes = Venta::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

        $comprasMes = Compra::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->sum('total');

        $productosBajoStock = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')->count();

        $sesionesAbiertas = SesionCaja::where('estado', 'abierta')->count();

        $ventasMesIds = Venta::when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->pluck('id');
        $totalVentasValor = Venta::whereIn('id', $ventasMesIds)->sum('total');
        $costoMes = VentaDetalle::whereIn('venta_id', $ventasMesIds)
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->selectRaw('COALESCE(SUM(venta_detalles.cantidad * productos.precio_compra), 0) as total_costo')
            ->value('total_costo') ?? 0;
        $utilidadMes = $totalVentasValor - $costoMes;

        $sucursales = Sucursal::orderBy('nombre')->get();
        $sucursalActiva = $sucursalId ? Sucursal::find($sucursalId) : null;

        return view('reportes.index', compact(
            'ventasHoy', 'ventasMes', 'comprasMes',
            'productosBajoStock', 'sesionesAbiertas', 'utilidadMes',
            'sucursales', 'sucursalActiva'
        ));
    }

    /* =======================
     |  VENTAS
     =======================*/

    public function ventas(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $query = Venta::with('cliente:id,nombre,rnc_cedula', 'usuario:id,name', 'caja:id,nombre')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc');

        $ventas = $query->get();
        $totalGeneral = $ventas->sum('total');
        $totalItbis = $ventas->sum('impuestos');
        $totalEfectivo = $ventas->sum('total');
        $cantidad = $ventas->count();

        return view('reportes.ventas', compact(
            'ventas', 'desde', 'hasta', 'totalGeneral', 'totalItbis', 'totalEfectivo', 'cantidad'
        ));
    }

    public function ventasCsv(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $ventas = Venta::with('cliente:id,nombre,rnc_cedula', 'usuario:id,name')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "ventas_{$desde}_{$hasta}.csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($ventas) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['#', 'Cliente', 'RNC', 'Vendedor', 'NCF', 'Fecha', 'Subtotal', 'ITBIS', 'Total', 'Efectivo', 'Método']);
            foreach ($ventas as $i => $v) {
                fputcsv($output, [
                    $i + 1, $v->cliente?->nombre ?? 'Consumidor Final', $v->cliente?->rnc_cedula ?? '',
                    $v->user?->name ?? '', $v->ncf ?? $v->encf ?? 'S/N',
                    $v->created_at->format('d/m/Y H:i'),
                    number_format($v->subtotal ?? 0, 2, '.', ''),
                    number_format($v->impuestos ?? 0, 2, '.', ''),
                    number_format($v->total, 2, '.', ''),
                    number_format($v->total ?? 0, 2, '.', ''),
                    $v->metodo_pago ?? 'Efectivo',
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ventasPdf(Request $request)
    {
        $data = $this->ventas($request);
        $html = view('reportes.ventas-pdf', $data->getData())->render();
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream("ventas_{$data['desde']}_{$data['hasta']}.pdf");
    }

    /* =======================
     |  COMPRAS
     =======================*/

    public function compras(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $query = Compra::with('proveedor:id,nombre,rnc', 'user:id,name')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha', 'desc');

        $compras = $query->get();
        $totalGeneral = $compras->sum('total');
        $totalItbis = $compras->sum('itbis_total');
        $totalRetenciones = $compras->sum('retencion_isr') + $compras->sum('retencion_itbis');
        $cantidad = $compras->count();

        return view('reportes.compras', compact(
            'compras', 'desde', 'hasta', 'totalGeneral', 'totalItbis', 'totalRetenciones', 'cantidad'
        ));
    }

    public function comprasCsv(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $compras = Compra::with('proveedor:id,nombre,rnc')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('fecha', '>=', $desde)->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha', 'desc')->get();

        $filename = "compras_{$desde}_{$hasta}.csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($compras) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['#', 'Proveedor', 'RNC', 'Usuario', 'Folio', 'Fecha', 'Subtotal', 'ITBIS', 'Ret ISR', 'Ret ITBIS', 'Total']);
            foreach ($compras as $i => $c) {
                fputcsv($output, [
                    $i + 1, $c->proveedor?->nombre ?? 'N/A', $c->proveedor?->rnc ?? '',
                    $c->user?->name ?? '', $c->folio ?? '', $c->fecha?->format('d/m/Y') ?? '',
                    number_format($c->subtotal ?? 0, 2, '.', ''),
                    number_format($c->itbis_total ?? 0, 2, '.', ''),
                    number_format($c->retencion_isr ?? 0, 2, '.', ''),
                    number_format($c->retencion_itbis ?? 0, 2, '.', ''),
                    number_format($c->total, 2, '.', ''),
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function comprasPdf(Request $request)
    {
        $data = $this->compras($request);
        $html = view('reportes.compras-pdf', $data->getData())->render();
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream("compras_{$data['desde']}_{$data['hasta']}.pdf");
    }

    /* =======================
     |  STOCK / INVENTARIO
     =======================*/

    public function stock(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $filtro = $request->input('filtro', 'todos');
        $buscar = $request->input('buscar');

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

        return view('reportes.stock', compact(
            'productos', 'totalProductos', 'totalValorInventario', 'bajoStock', 'sinStock',
            'filtro', 'buscar', 'almacenes'
        ));
    }

    public function stockCsv(Request $request)
    {
        $data = $this->stock($request);
        $productos = $data['productos'];
        $filename = "inventario_" . now()->format('Ymd') . ".csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($productos) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['Código', 'Nombre', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Costo', 'Precio Venta', 'Valor Inventario', 'Estado']);
            foreach ($productos as $p) {
                $estado = $p->stock <= 0 ? 'Sin Stock' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'Stock Bajo' : 'Disponible');
                fputcsv($output, [
                    $p->codigo_barras ?? $p->referencia ?? '', $p->nombre,
                    $p->categoria?->nombre ?? '', $p->stock, $p->stock_minimo ?? 0,
                    number_format($p->precio_compra ?? 0, 2, '.', ''),
                    number_format($p->precio ?? 0, 2, '.', ''),
                    number_format(($p->stock * ($p->precio_compra ?? 0)), 2, '.', ''),
                    $estado,
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockPdf(Request $request)
    {
        $data = $this->stock($request);
        $html = view('reportes.stock-pdf', $data->getData())->render();
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream("inventario_" . now()->format('Ymd') . ".pdf");
    }

    /* =======================
     |  CAJA
     =======================*/

    public function caja(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $query = SesionCaja::with('caja:id,nombre', 'user:id,name')
            ->whereDate('fecha_apertura', '>=', $desde)
            ->whereDate('fecha_apertura', '<=', $hasta)
            ->orderBy('fecha_apertura', 'desc');

        if ($sucursalId) {
            $query->whereHas('caja', fn($q) => $q->where('sucursal_id', $sucursalId));
        }

        if ($cajaId = $request->input('caja_id')) {
            $query->where('caja_id', $cajaId);
        }

        $sesiones = $query->get();
        $totalVentas = $sesiones->sum(fn($s) => $s->ventas_efectivo + $s->ventas_tarjeta + $s->ventas_transferencia);
        $totalDescuadre = $sesiones->sum('descuadre');
        $cantidad = $sesiones->count();
        $abiertas = $sesiones->where('estado', 'abierta')->count();
        $cerradas = $sesiones->where('estado', 'cerrada')->count();

        $cajas = Caja::orderBy('nombre')->get();

        return view('reportes.caja', compact(
            'sesiones', 'desde', 'hasta', 'totalVentas', 'totalDescuadre', 'cantidad',
            'abiertas', 'cerradas', 'cajas'
        ));
    }

    public function cajaCsv(Request $request)
    {
        $data = $this->caja($request);
        $sesiones = $data['sesiones'];
        $filename = "caja_{$data['desde']}_{$data['hasta']}.csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($sesiones) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['Caja', 'Cajero', 'Apertura', 'Cierre', 'Inicial', 'Efectivo', 'Tarjeta', 'Transferencia', 'Declarado', 'Descuadre', 'Estado']);
            foreach ($sesiones as $s) {
                fputcsv($output, [
                    $s->caja?->nombre ?? '', $s->user?->name ?? '',
                    $s->fecha_apertura?->format('d/m/Y H:i') ?? '',
                    $s->fecha_cierre?->format('d/m/Y H:i') ?? '',
                    number_format($s->monto_inicial ?? 0, 2, '.', ''),
                    number_format($s->ventas_efectivo ?? 0, 2, '.', ''),
                    number_format($s->ventas_tarjeta ?? 0, 2, '.', ''),
                    number_format($s->ventas_transferencia ?? 0, 2, '.', ''),
                    number_format($s->monto_declarado ?? 0, 2, '.', ''),
                    number_format($s->descuadre ?? 0, 2, '.', ''),
                    $s->estado ?? '',
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =======================
     |  UTILIDADES
     =======================*/

    public function utilidades(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));

        $ventas = Venta::with('detalles.producto', 'cliente:id,nombre')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->get();

        $totalVentas = $ventas->sum('total');
        $totalCosto = 0;
        $totalItbis = $ventas->sum('impuestos');
        $totalProductosVendidos = 0;
        $detalles = collect();

        foreach ($ventas as $v) {
            foreach ($v->detalles as $d) {
                $costoUnitario = $d->producto?->precio_compra ?? 0;
                $costoLinea = $costoUnitario * $d->cantidad;
                $totalCosto += $costoLinea;
                $totalProductosVendidos += $d->cantidad;
                $detalles->push([
                    'venta_id' => $v->id,
                    'fecha' => $v->created_at->format('d/m/Y'),
                    'cliente' => $v->cliente?->nombre ?? 'Consumidor Final',
                    'producto' => $d->producto?->nombre ?? $d->nombre ?? 'Producto',
                    'cantidad' => $d->cantidad,
                    'precio' => $d->precio_unitario ?? 0,
                    'costo' => $costoUnitario,
                    'subtotal' => $d->subtotal ?? 0,
                    'ganancia' => ($d->subtotal ?? 0) - $costoLinea,
                ]);
            }
        }

        $utilidadBruta = $totalVentas - $totalCosto;
        $margen = $totalVentas > 0 ? ($utilidadBruta / $totalVentas) * 100 : 0;

        return view('reportes.utilidades', compact(
            'detalles', 'desde', 'hasta', 'totalVentas', 'totalCosto', 'totalItbis',
            'utilidadBruta', 'margen', 'totalProductosVendidos'
        ));
    }

    public function utilidadesCsv(Request $request)
    {
        $data = $this->utilidades($request);
        $detalles = $data['detalles'];
        $filename = "utilidades_{$data['desde']}_{$data['hasta']}.csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($detalles) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['Venta', 'Fecha', 'Cliente', 'Producto', 'Cantidad', 'Precio', 'Costo', 'Subtotal', 'Ganancia']);
            foreach ($detalles as $d) {
                fputcsv($output, [
                    $d['venta_id'], $d['fecha'], $d['cliente'], $d['producto'],
                    $d['cantidad'],
                    number_format($d['precio'], 2, '.', ''),
                    number_format($d['costo'], 2, '.', ''),
                    number_format($d['subtotal'], 2, '.', ''),
                    number_format($d['ganancia'], 2, '.', ''),
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =======================
     |  RETENCIONES
     =======================*/

    public function retenciones(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $tipo = $request->input('tipo', 'compras');

        $compras = collect();
        $ventas = collect();

        if ($tipo === 'compras' || $tipo === 'ambos') {
            $compras = Compra::with('proveedor')
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->whereMonth('fecha', $mes)->whereYear('fecha', $anio)
                ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
                ->get();
        }

        if ($tipo === 'ventas' || $tipo === 'ambos') {
            $ventas = Venta::with('cliente')
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->whereMonth('created_at', $mes)->whereYear('created_at', $anio)
                ->where(fn($q) => $q->where('retencion_isr', '>', 0)->orWhere('retencion_itbis', '>', 0))
                ->get();
        }

        $totalRetIsr = $compras->sum('retencion_isr') + $ventas->sum('retencion_isr');
        $totalRetItbis = $compras->sum('retencion_itbis') + $ventas->sum('retencion_itbis');
        $totalGeneral = $totalRetIsr + $totalRetItbis;

        return view('reportes.retenciones', compact(
            'compras', 'ventas', 'mes', 'anio', 'tipo',
            'totalRetIsr', 'totalRetItbis', 'totalGeneral'
        ));
    }

    public function retencionesCsv(Request $request)
    {
        $data = $this->retenciones($request);
        $compras = $data['compras'];
        $ventas = $data['ventas'];
        $mes = $data['mes'];
        $anio = $data['anio'];
        $tipo = $data['tipo'];

        $filename = "retenciones_{$tipo}_{$anio}_{$mes}.csv";
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($compras, $ventas, $tipo) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($tipo === 'compras' || $tipo === 'ambos') {
                fputcsv($output, ['Tipo', 'Proveedor', 'RNC', 'Documento', 'Fecha', 'Total', 'Ret ISR', 'Ret ITBIS', 'Total Retenido']);
                foreach ($compras as $c) {
                    fputcsv($output, [
                        'Compra', $c->proveedor?->nombre ?? 'N/A', $c->proveedor?->rnc ?? '',
                        $c->folio ?? '#' . $c->id, $c->fecha?->format('d/m/Y') ?? '',
                        number_format($c->total, 2, '.', ''),
                        number_format($c->retencion_isr ?? 0, 2, '.', ''),
                        number_format($c->retencion_itbis ?? 0, 2, '.', ''),
                        number_format(($c->retencion_isr ?? 0) + ($c->retencion_itbis ?? 0), 2, '.', ''),
                    ]);
                }
            }

            if ($tipo === 'ventas' || $tipo === 'ambos') {
                if ($tipo === 'ambos') fputcsv($output, []); // blank separator
                fputcsv($output, ['Tipo', 'Cliente', 'RNC', 'Documento', 'Fecha', 'Total', 'Ret ISR', 'Ret ITBIS', 'Total Retenido']);
                foreach ($ventas as $v) {
                    fputcsv($output, [
                        'Venta', $v->cliente?->nombre ?? 'N/A', $v->cliente?->rnc_cedula ?? '',
                        '#' . str_pad($v->id, 5, '0', STR_PAD_LEFT), $v->created_at->format('d/m/Y'),
                        number_format($v->total, 2, '.', ''),
                        number_format($v->retencion_isr ?? 0, 2, '.', ''),
                        number_format($v->retencion_itbis ?? 0, 2, '.', ''),
                        number_format(($v->retencion_isr ?? 0) + ($v->retencion_itbis ?? 0), 2, '.', ''),
                    ]);
                }
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =======================
     |  RESTAURANTE
     =======================*/

    public function restaurante(Request $request)
    {
        $sucursalId = $this->sucursalFiltro();
        $desde = $request->input('desde', today()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', today()->format('Y-m-d'));
        $sucursales = Sucursal::orderBy('nombre')->get();

        // Ventas por mesero
        $ventasPorMesero = Venta::select('user_id', DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas'))
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->groupBy('user_id')
            ->with('usuario:id,name')
            ->get();

        // Ocupación de mesas
        $ventasPorMesa = Venta::select('mesa_id', DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas'))
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->groupBy('mesa_id')
            ->with('mesa:id,numero,nombre')
            ->get();

        // Ventas por hora (lunch vs dinner)
        $ventasPorHora = Venta::select(DB::raw('CASE WHEN HOUR(created_at) < 17 THEN "Almuerzo (6AM-4PM)" ELSE "Cena (5PM-11PM)" END as turno'), DB::raw('COUNT(*) as total_ordenes'), DB::raw('SUM(total) as total_ventas'))
            ->whereNotNull('mesa_id')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->groupBy('turno')
            ->get();

        // Productos más vendidos en restaurante
        $productosTop = VentaDetalle::select('producto_id', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('SUM(subtotal) as total_ventas'))
            ->whereHas('venta', function ($q) use ($sucursalId, $desde, $hasta) {
                $q->whereNotNull('mesa_id')
                  ->when($sucursalId, fn($sq) => $sq->where('sucursal_id', $sucursalId))
                  ->whereDate('created_at', '>=', $desde)
                  ->whereDate('created_at', '<=', $hasta);
            })
            ->groupBy('producto_id')
            ->orderByDesc('total_cantidad')
            ->limit(20)
            ->with('producto:id,nombre')
            ->get();

        return view('reportes.restaurante', compact(
            'desde', 'hasta', 'sucursales', 'sucursalId',
            'ventasPorMesero', 'ventasPorMesa', 'ventasPorHora', 'productosTop'
        ));
    }

    /* =======================
     |  EXPORT PDF HELPERS
     =======================*/

    private function exportPdf($view, $data, $filename)
    {
        $html = view($view, $data)->render();
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream($filename);
    }
}
