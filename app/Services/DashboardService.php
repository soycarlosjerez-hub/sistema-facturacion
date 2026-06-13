<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Compra;
use App\Models\SesionCaja;
use App\Models\NcfSequence;
use App\Support\AlertasSistema;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getKpis(string $startDate = null, string $endDate = null): array
    {
        $cacheKey = sprintf('dashboard_kpis_%s_%s', $startDate ?? 'default', $endDate ?? 'default');

        return Cache::remember($cacheKey, 60, function () use ($startDate, $endDate) {
            if ($startDate || $endDate) {
                return $this->getFilteredKpis($startDate, $endDate);
            }

            return $this->getDefaultKpis();
        });
    }

    private function getFilteredKpis(?string $startDate, ?string $endDate): array
    {
        $ventasQuery = Venta::query();
        $pagosQuery = Pago::query();

        if ($startDate) {
            $ventasQuery->whereDate('created_at', '>=', $startDate);
            $pagosQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $ventasQuery->whereDate('created_at', '<=', $endDate);
            $pagosQuery->whereDate('created_at', '<=', $endDate);
        }

        $totalVentas = (float) $ventasQuery->sum('total');
        $totalTickets = (int) $ventasQuery->count();
        $ticketPromedio = $totalTickets > 0 ? round($totalVentas / $totalTickets, 2) : 0;

        return [
            'ventasHoy' => $totalVentas,
            'ventasAyer' => 0,
            'ticketsHoy' => $totalTickets,
            'ticketsAyer' => 0,
            'ticketPromedio' => $ticketPromedio,
            'ingresosMes' => $totalVentas,
            'ingresosMesAnt' => 0,
            'utilidadMes' => 0,
            'utilidadMesAnt' => 0,
            'margen' => 0,
            'totalCuentasPorCobrar' => (float) Cliente::sum('balance_pendiente'),
            'clientesConDeuda' => Cliente::where('balance_pendiente', '>', 0)->count(),
            'facturasPendientes' => Venta::whereIn('estado', ['pendiente', 'cuenta_abierta'])->count(),
        ];
    }

    private function getDefaultKpis(): array
    {
        $hoy = Carbon::today();
        $ayer = Carbon::yesterday();
        $mes = Carbon::now();
        $mesAnt = Carbon::now()->subMonth();

        $ventasHoy = (float) Venta::whereDate('created_at', $hoy)->sum('total');
        $ventasAyer = (float) Venta::whereDate('created_at', $ayer)->sum('total');
        $ticketsHoy = (int) Venta::whereDate('created_at', $hoy)->count();
        $ticketsAyer = (int) Venta::whereDate('created_at', $ayer)->count();
        $ticketPromedio = (float) (Venta::whereDate('created_at', $hoy)->avg('total') ?? 0);

        $ingresosMes = (float) Venta::whereMonth('created_at', $mes->month)
            ->whereYear('created_at', $mes->year)
            ->sum('total');
        $ingresosMesAnt = (float) Venta::whereMonth('created_at', $mesAnt->month)
            ->whereYear('created_at', $mesAnt->year)
            ->sum('total');

        $utilidadMes = $this->calcularUtilidad($mes->month, $mes->year);
        $utilidadMesAnt = $this->calcularUtilidad($mesAnt->month, $mesAnt->year);

        $totalCuentasPorCobrar = (float) Cliente::sum('balance_pendiente');
        $clientesConDeuda = Cliente::where('balance_pendiente', '>', 0)->count();
        $facturasPendientes = Venta::whereIn('estado', ['pendiente', 'cuenta_abierta'])->count();

        $margen = $ingresosMes > 0 ? round(($utilidadMes / $ingresosMes) * 100, 1) : 0;

        return compact(
            'ventasHoy', 'ventasAyer', 'ticketsHoy', 'ticketsAyer', 'ticketPromedio',
            'ingresosMes', 'ingresosMesAnt', 'utilidadMes', 'utilidadMesAnt', 'margen',
            'totalCuentasPorCobrar', 'clientesConDeuda', 'facturasPendientes'
        );
    }

    private function calcularUtilidad(int $month, int $year): float
    {
        return (float) (VentaDetalle::query()
            ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_detalles.producto_id')
            ->whereMonth('ventas.created_at', $month)
            ->whereYear('ventas.created_at', $year)
            ->selectRaw('SUM(venta_detalles.cantidad * (venta_detalles.precio_unitario - productos.precio_compra)) as total_utilidad')
            ->value('total_utilidad') ?? 0);
    }

    public function getCashRegisterStatus(): array
    {
        $sesionCajaActiva = SesionCaja::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->with('caja')
            ->latest()
            ->first();

        $hoy = Carbon::today();

        return [
            'abierta' => (bool) $sesionCajaActiva,
            'caja' => $sesionCajaActiva?->caja?->nombre,
            'abierta_en' => $sesionCajaActiva?->created_at,
            'monto_inicial' => $sesionCajaActiva?->monto_inicial ?? 0,
            'ventas_caja' => $sesionCajaActiva
                ? Venta::where('user_id', auth()->id())->whereDate('created_at', $hoy)->sum('total')
                : 0,
            'cobros_caja' => $sesionCajaActiva
                ? Pago::where('sesion_caja_id', $sesionCajaActiva->id)->whereDate('created_at', $hoy)->sum('monto')
                : 0,
        ];
    }

    public function getSecondaryStats(): array
    {
        $cacheKey = 'dashboard_secondary_stats';
        return Cache::remember($cacheKey, 120, function () {
            return [
                'totalProductos' => Producto::count(),
                'productosCriticos' => Producto::where('stock', '<=', 5)->count(),
                'productosBajos' => Producto::whereBetween('stock', [6, 15])->count(),
                'valorInventario' => Producto::selectRaw('SUM(stock * precio_compra) as val')->value('val') ?? 0,
                'totalClientes' => Cliente::count(),
                'cobrosHoy' => Pago::whereDate('created_at', Carbon::today())->sum('monto'),
                'cobrosMes' => Pago::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('monto'),
            ];
        });
    }

    public function getSalesChartData(int $days = 29): array
    {
        $ventasPorDia = Venta::query()
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as total, COUNT(*) as tickets')
            ->whereDate('created_at', '>=', Carbon::today()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        $labels = [];
        $data = [];
        $tickets = [];

        for ($i = $days; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i)->format('Y-m-d');
            $row = $ventasPorDia->firstWhere('fecha', $fecha);
            $labels[] = Carbon::parse($fecha)->format('d M');
            $data[] = (float) ($row->total ?? 0);
            $tickets[] = (int) ($row->tickets ?? 0);
        }

        return compact('labels', 'data', 'tickets');
    }

    public function getHourlySalesChart(): array
    {
        $hoy = Carbon::today();
        $ventasPorHora = Venta::whereDate('created_at', $hoy)
            ->selectRaw('HOUR(created_at) as hora, SUM(total) as total')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('total', 'hora');

        $labels = [];
        $data = [];
        for ($h = 8; $h <= 22; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $data[] = (float) ($ventasPorHora[$h] ?? 0);
        }

        return compact('labels', 'data');
    }

    public function getPaymentMethodChart(): array
    {
        $hoy = Carbon::today();
        
        $efectivo = Pago::whereDate('created_at', $hoy)->where('metodo_pago', 'efectivo')->sum('monto');
        $tarjeta = Pago::whereDate('created_at', $hoy)->where('metodo_pago', 'tarjeta')->sum('monto');
        $transferencia = Pago::whereDate('created_at', $hoy)->where('metodo_pago', 'transferencia')->sum('monto');
        $mixto = Pago::whereDate('created_at', $hoy)->where('metodo_pago', 'mixto')->sum('monto');

        $total = $efectivo + $tarjeta + $transferencia + $mixto;

        return [
            'labels' => ['Efectivo', 'Tarjeta', 'Transferencia', 'Mixto'],
            'data' => [$efectivo, $tarjeta, $transferencia, $mixto],
            'colors' => ['#22c55e', '#6366f1', '#f59e0b', '#38bdf8'],
            'total' => $total,
        ];
    }

    public function getTopProducts(int $limit = 5): Collection
    {
        $mes = Carbon::now();
        return DB::table('venta_detalles')
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
            ->limit($limit)
            ->get();
    }

    public function getTopDebtors(int $limit = 5): Collection
    {
        return Cliente::where('balance_pendiente', '>', 0)
            ->orderBy('balance_pendiente', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentActivity(): array
    {
        return [
            'ultimasVentas' => Venta::with(['cliente:id,nombre', 'usuario:id,name'])
                ->latest()
                ->limit(8)
                ->get(),
            'ultimasCompras' => Compra::with(['proveedor:id,nombre', 'user:id,name'])
                ->latest('fecha')
                ->limit(5)
                ->get(),
            'ultimosPagos' => Pago::with('venta.cliente:id,nombre')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    public function getAlerts(): array
    {
        $mes = Carbon::now();
        return [
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
            'productos_sin_rotacion' => Producto::whereDoesntHave('ventaDetalles', function ($q) use ($mes) {
                $q->whereMonth('venta_detalles.created_at', $mes->month)
                  ->whereYear('venta_detalles.created_at', $mes->year);
            })->where('stock', '>', 0)->count(),
        ];
    }

    public function getUserRanking(): Collection
    {
        $mes = Carbon::now();
        return DB::table('ventas')
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
    }
}
