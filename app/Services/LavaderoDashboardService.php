<?php

namespace App\Services;

use App\Models\Lavadero;
use App\Models\LavaderoServicio;
use App\Models\Lavador;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LavaderoDashboardService
{
    public function getDashboardData(?int $sucursalId = null): array
    {
        $tenantId = Auth::user()->business_instance_id;

        $today = today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        // === KPIs de hoy ===
        $ventasHoy = Venta::where('tenant_id', $tenantId)
            ->where('estado', '!=', 'anulada')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha', [$todayStart, $todayEnd])
            ->sum('total');

        $ventasCountHoy = Venta::where('tenant_id', $tenantId)
            ->where('estado', '!=', 'anulada')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha', [$todayStart, $todayEnd])
            ->count();

        // Vehículos en el lavadero (en proceso)
        $vehiculosProceso = Lavadero::where('estado', 'en_proceso')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->count();

        // Baches ocupados (esperando)
        $bachesOcupados = Lavadero::where('estado', 'esperando')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->count();

        // === Servicio más popular de hoy ===
        $servicioMasPopular = $this->getServicioPopularHoy($tenantId, $sucursalId);

        // === Lavador más productivo del día ===
        $lavadorMasProductivo = $this->getLavadorProductivoDelDia($tenantId, $sucursalId);

        // === Ventas por tipo (servicios, alimentos, accesorios) ===
        $ventasMixtas = $this->getVentasMixtas($tenantId, $sucursalId, $todayStart, $todayEnd);
        $serviciosCount = $this->getServiciosCountHoy($tenantId, $sucursalId, $todayStart, $todayEnd);

        // === Top servicios del mes ===
        $topServicios = $this->getTopServiciosMes($tenantId, $sucursalId);

        // === Ingresos por semana (últimos 7 días) ===
        $ingresosSemana = $this->getIngresosSemana($tenantId, $sucursalId);

        // === Estado de baches ===
        $bachesEstado = $this->getBachesEstado($tenantId, $sucursalId);

        return [
            'kpi' => [
                'ventas_hoy'            => round($ventasHoy, 2),
                'ventas_count_hoy'      => $ventasCountHoy,
                'vehiculos_proceso'     => $vehiculosProceso,
                'baches_ocupados'       => $bachesOcupados,
            ],
            'servicio_mas_popular' => $servicioMasPopular,
            'lavador_mas_productivo' => $lavadorMasProductivo,
            'ventas_mixtas'        => $ventasMixtas,
            'servicios_count_hoy'  => $serviciosCount,
            'top_servicios'        => $topServicios,
            'ingresos_semana'      => $ingresosSemana,
            'baches_estado'        => $bachesEstado,
        ];
    }

    protected function getServicioPopularHoy(int $tenantId, ?int $sucursalId): ?array
    {
        $today = today()->copy()->startOfDay();
        $tomorrow = today()->copy()->addDay()->startOfDay();

        $servicio = VentaDetalle::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$today, $tomorrow])
            ->whereNotNull('notas')
            ->where('notas', '!=', '')
            ->selectRaw('notas as nombre, COUNT(*) as total')
            ->when($sucursalId, function ($q) use ($tenantId, $sucursalId) {
                $q->whereHas('venta', fn($vq) => $vq->where('sucursal_id', $sucursalId));
            })
            ->groupBy('notas')
            ->orderByDesc('total')
            ->limit(1)
            ->first();

        if (!$servicio) {
            // Fallback: service más popular de los últimos 7 días
            $servicio = LavaderoServicio::where('activo', true)
                ->selectRaw('lavadero_servicios.nombre, COUNT(lavador_venta.lavador_id) as total')
                ->leftJoin('ventas', 'lavadero_servicios.tenant_id', '=', 'ventas.tenant_id')
                ->leftJoin('lavador_venta', 'ventas.id', '=', 'lavador_venta.venta_id')
                ->when($sucursalId, fn($q) => $q->where('ventas.sucursal_id', $sucursalId))
                ->whereBetween('ventas.fecha', [now()->subDays(7), now()])
                ->groupBy('lavadero_servicios.nombre')
                ->orderByDesc('total')
                ->first();
        }

        return $servicio ? (array) $servicio : null;
    }

    protected function getLavadorProductivoDelDia(int $tenantId, ?int $sucursalId): ?array
    {
        $today = today();

        $lavador = \DB::table('lavador_venta')
            ->join('lavadores', 'lavador_venta.lavador_id', '=', 'lavadores.id')
            ->join('ventas', 'lavador_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.estado', '!=', 'anulada')
            ->where('lavadores.activo', true)
            ->where('lavadores.tenant_id', $tenantId)
            ->whereDate('ventas.fecha', $today)
            ->when($sucursalId, fn($q) => $q->where('ventas.sucursal_id', $sucursalId))
            ->selectRaw('lavadores.id, lavadores.nombre, SUM(lavador_venta.comision) as total_comision, COUNT(*) as ventas')
            ->groupBy('lavadores.id', 'lavadores.nombre')
            ->orderByDesc('total_comision')
            ->first();

        return $lavador ? (array) $lavador : null;
    }

    protected function getVentasMixtas(int $tenantId, ?int $sucursalId, \DateTime $start, \DateTime $end): array
    {
        // Count ventas que incluyeron productos de tienda + servicios
        $ventasMixtasCount = VentaDetalle::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('producto_id')
            ->when($sucursalId, fn($q) => $q->whereHas('venta', fn($vq) => $vq->where('sucursal_id', $sucursalId)))
            ->distinct()
            ->count('venta_id');

        $ventasProductosTotal = VentaDetalle::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('producto_id')
            ->when($sucursalId, fn($q) => $q->whereHas('venta', fn($vq) => $vq->where('sucursal_id', $sucursalId)))
            ->sum('subtotal');

        return [
            'count'       => $ventasMixtasCount,
            'subtotal'    => round($ventasProductosTotal, 2),
        ];
    }

    protected function getServiciosCountHoy(int $tenantId, ?int $sucursalId, \DateTime $start, \DateTime $end): int
    {
        return VentaDetalle::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('notas')
            ->where('notas', '!=', '')
            ->when($sucursalId, fn($q) => $q->whereHas('venta', fn($vq) => $vq->where('sucursal_id', $sucursalId)))
            ->count();
    }

    protected function getTopServiciosMes(int $tenantId, ?int $sucursalId): array
    {
        $startOfMonth = today()->copy()->startOfMonth();
        $endOfMonth = today()->copy()->endOfDay();

        return VentaDetalle::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('notas')
            ->where('notas', '!=', '')
            ->when($sucursalId, fn($q) => $q->whereHas('venta', fn($vq) => $vq->where('sucursal_id', $sucursalId)))
            ->selectRaw('notas as nombre, COUNT(*) as total, SUM(subtotal) as ingresos')
            ->groupBy('notas')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();
    }

    protected function getIngresosSemana(int $tenantId, ?int $sucursalId): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->copy()->subDays($i);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();

            $total = Venta::where('tenant_id', $tenantId)
                ->where('estado', '!=', 'anulada')
                ->where('fecha', '>=', $start)
                ->where('fecha', '<=', $end)
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->sum('total');

            $count = Venta::where('tenant_id', $tenantId)
                ->where('estado', '!=', 'anulada')
                ->where('fecha', '>=', $start)
                ->where('fecha', '<=', $end)
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->count();

            $days[] = [
                'dia'       => $date->format('D'),
                'fecha'     => $date->format('Y-m-d'),
                'total'     => round($total, 2),
                'ventas'    => $count,
            ];
        }

        return $days;
    }

    protected function getBachesEstado(int $tenantId, ?int $sucursalId): array
    {
        // Lavaderos por estado
        $porEstado = Lavadero::where('tenant_id', $tenantId)
            ->selectRaw('estado, COUNT(*) as total')
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        $estadosDefinidos = ['esperando' => 0, 'en_proceso' => 0, 'completado' => 0, 'entregado' => 0];
        foreach ($porEstado as $estado => $total) {
            if (isset($estadosDefinidos[$estado])) {
                $estadosDefinidos[$estado] = $total;
            }
        }

        return $estadosDefinidos;
    }
}
