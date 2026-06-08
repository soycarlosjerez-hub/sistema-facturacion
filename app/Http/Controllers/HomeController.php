<?php

namespace App\Http\Controllers;

use App\Models\AlmacenMovimiento;
use App\Models\Cliente;
use App\Models\Sucursal;
use App\Models\Compra;
use App\Models\NcfSequence;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SesionCaja;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HomeExport;
use App\Support\AlertasSistema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $hoy    = Carbon::today();
        $ayer   = Carbon::yesterday();
        $mes    = Carbon::now();
        $mesAnt = Carbon::now()->subMonth();

        // ============ KPIs PRINCIPALES ============
        $ventasHoy      = Venta::whereDate('created_at', $hoy)->sum('total');
        $ventasAyer     = Venta::whereDate('created_at', $ayer)->sum('total');
        $ticketsHoy     = Venta::whereDate('created_at', $hoy)->count();
        $ticketsAyer    = Venta::whereDate('created_at', $ayer)->count();
        $ticketPromedio = Venta::whereDate('created_at', $hoy)->avg('total') ?? 0;

        $ingresosMes    = Venta::whereMonth('created_at', $mes->month)->whereYear('created_at', $mes->year)->sum('total');
        $ingresosMesAnt = Venta::whereMonth('created_at', $mesAnt->month)->whereYear('created_at', $mesAnt->year)->sum('total');

        $utilidadMes = VentaDetalle::query()
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->whereMonth('ventas.created_at', $mes->month)
            ->whereYear('ventas.created_at', $mes->year)
            ->selectRaw('SUM(venta_detalles.cantidad * (venta_detalles.precio_unitario - productos.precio_compra)) as total_utilidad')
            ->value('total_utilidad') ?? 0;

        $utilidadMesAnt = VentaDetalle::query()
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->whereMonth('ventas.created_at', $mesAnt->month)
            ->whereYear('ventas.created_at', $mesAnt->year)
            ->selectRaw('SUM(venta_detalles.cantidad * (venta_detalles.precio_unitario - productos.precio_compra)) as total_utilidad')
            ->value('total_utilidad') ?? 0;

        // Cuentas por cobrar
        $totalCuentasPorCobrar = Cliente::sum('balance_pendiente');
        $clientesConDeuda      = Cliente::where('balance_pendiente', '>', 0)->count();
        $facturasPendientes    = Venta::whereIn('estado', ['pendiente', 'cuenta_abierta'])->count();

        // Cobros del día
        $cobrosHoy = Pago::whereDate('created_at', $hoy)->sum('monto');
        $cobrosMes = Pago::whereMonth('created_at', $mes->month)->whereYear('created_at', $mes->year)->sum('monto');

        // Inventario
        $totalProductos     = Producto::count();
        $productosCriticos  = Producto::where('stock', '<=', 5)->count();
        $productosBajos     = Producto::whereBetween('stock', [6, 15])->count();
        $valorInventario    = Producto::selectRaw('SUM(stock * precio_compra) as val')->value('val') ?? 0;
        $productosSinRotacion = Producto::whereDoesntHave('ventaDetalles', function ($q) use ($mes) {
            $q->whereMonth('venta_detalles.created_at', $mes->month)
              ->whereYear('venta_detalles.created_at', $mes->year);
        })->where('stock', '>', 0)->count();

        // ============ CAJA ACTIVA ============
        $sesionCajaActiva = SesionCaja::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->with('caja')
            ->latest()
            ->first();

        $cajaActual = [
            'abierta'        => (bool) $sesionCajaActiva,
            'caja'           => $sesionCajaActiva?->caja?->nombre,
            'abierta_en'     => $sesionCajaActiva?->created_at,
            'monto_inicial'  => $sesionCajaActiva?->monto_inicial ?? 0,
            'ventas_caja'    => $sesionCajaActiva ? Venta::where('user_id', auth()->id())->whereDate('created_at', $hoy)->sum('total') : 0,
            'cobros_caja'    => $sesionCajaActiva ? Pago::where('sesion_caja_id', $sesionCajaActiva->id)->whereDate('created_at', $hoy)->sum('monto') : 0,
        ];

        // ============ GRÁFICOS ============
        // Ventas últimos 30 días
        $ventasPorDia = Venta::query()
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as total, COUNT(*) as tickets')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(29))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Rellenar días sin ventas
        $chartLabels = [];
        $chartData   = [];
        $chartTickets = [];
        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i)->format('Y-m-d');
            $row   = $ventasPorDia->firstWhere('fecha', $fecha);
            $chartLabels[]   = Carbon::parse($fecha)->format('d M');
            $chartData[]     = (float) ($row->total ?? 0);
            $chartTickets[]  = (int) ($row->tickets ?? 0);
        }

        // Ventas por hora hoy
        $ventasPorHora = Venta::whereDate('created_at', $hoy)
            ->selectRaw('HOUR(created_at) as hora, SUM(total) as total')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('total', 'hora');

        $horasLabels = [];
        $horasData   = [];
        for ($h = 8; $h <= 22; $h++) {
            $horasLabels[] = sprintf('%02d:00', $h);
            $horasData[]   = (float) ($ventasPorHora[$h] ?? 0);
        }

        // Ventas por método de pago hoy
        $ventasPorMetodo = Venta::whereDate('created_at', $hoy)
            ->selectRaw('estado, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('estado')
            ->get();

        // Top productos del mes
        $topProductos = DB::table('venta_detalles')
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->whereMonth('ventas.created_at', $mes->month)
            ->whereYear('ventas.created_at', $mes->year)
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.imagen',
                'productos.precio',
                'productos.stock',
                DB::raw('SUM(venta_detalles.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_detalles.cantidad * venta_detalles.precio_unitario) as ingreso_total'),
                DB::raw('SUM(venta_detalles.cantidad * (venta_detalles.precio_unitario - productos.precio_compra)) as utilidad')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.imagen', 'productos.precio', 'productos.stock')
            ->orderByDesc('cantidad_vendida')
            ->limit(5)
            ->get();

        // Top deudores
        $topDeudores = Cliente::where('balance_pendiente', '>', 0)
            ->orderBy('balance_pendiente', 'desc')
            ->limit(5)
            ->get();

        // ============ ACTIVIDAD RECIENTE ============
        $ultimasVentas   = Venta::with(['cliente:id,nombre', 'usuario:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        $ultimasCompras  = Compra::with(['proveedor:id,nombre', 'user:id,name'])
            ->latest('fecha')
            ->limit(5)
            ->get();

        $ultimosPagos    = Pago::with('venta.cliente:id,nombre')
            ->latest()
            ->limit(5)
            ->get();

        // ============ ALERTAS ============
        $alertas = [
            'stock_critico' => Producto::stockCritico()
                ->orderBy('stock', 'asc')
                ->limit(5)
                ->get(),
            'ncf_por_vencer' => NcfSequence::where('activo', true)
                ->whereNotNull('fecha_vencimiento')
                ->whereDate('fecha_vencimiento', '<=', Carbon::today()->addDays(30))
                ->whereDate('fecha_vencimiento', '>=', Carbon::today())
                ->get(),
            'clientes_morosos' => Cliente::where('balance_pendiente', '>', 0)
                ->orderBy('balance_pendiente', 'desc')
                ->limit(3)
                ->get(),
            'sistema' => AlertasSistema::todas(),
        ];

        // ============ RENDIMIENTO DE USUARIOS (CAJEROS) ============
        $rankingUsuarios = DB::table('ventas')
            ->join('users', 'users.id', '=', 'ventas.user_id')
            ->whereMonth('ventas.created_at', $mes->month)
            ->whereYear('ventas.created_at', $mes->year)
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(ventas.id) as tickets'),
                DB::raw('SUM(ventas.total) as total_vendido')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'ventasHoy', 'ventasAyer', 'ticketsHoy', 'ticketsAyer', 'ticketPromedio',
            'ingresosMes', 'ingresosMesAnt',
            'utilidadMes', 'utilidadMesAnt',
            'totalCuentasPorCobrar', 'clientesConDeuda', 'facturasPendientes',
            'cobrosHoy', 'cobrosMes',
            'totalProductos', 'productosCriticos', 'productosBajos', 'valorInventario', 'productosSinRotacion',
            'cajaActual',
            'chartLabels', 'chartData', 'chartTickets',
            'horasLabels', 'horasData',
            'ventasPorMetodo',
            'topProductos', 'topDeudores',
            'ultimasVentas', 'ultimasCompras', 'ultimosPagos',
            'alertas', 'rankingUsuarios'
        ));
    }

    public function pdf(Request $request)
    {
        $data = $this->buildReportData();
        $pdf = Pdf::loadView('dashboard.pdf', $data);
        return $pdf->stream('dashboard-' . date('Y-m-d') . '.pdf');
    }

    public function export(Request $request)
    {
        return Excel::download(new HomeExport, 'dashboard-' . date('Y-m-d') . '.xlsx');
    }

    private function buildReportData(): array
    {
        return [];
    }

    public function toggleDarkMode(Request $request)
    {
        $darkMode = session('dark_mode', false);
        session(['dark_mode' => !$darkMode]);
        return back();
    }

    public function setSucursalActiva(Request $request)
    {
        $request->validate(['sucursal_id' => 'nullable|exists:sucursales,id']);
        session(['sucursal_id' => $request->sucursal_id]);
        return back()->with('success', 'Sucursal activa cambiada.');
    }
}
