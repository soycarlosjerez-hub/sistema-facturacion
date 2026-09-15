@extends('layouts.app')

@section('title', 'Dashboard Delivery')

@push('styles')
@include('partials.premium-ui')
<style>
/* Dashboard Delivery Styles */
.stat-main .ui-stat-value {
    font-size: 2.25rem;
    font-weight: 800;
}
.stat-secondary .ui-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}
.chart-bar-container {
    display: flex;
    align-items: flex-end;
    gap: .5rem;
    height: 180px;
    padding: 0 .5rem;
}
.chart-bar-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    justify-content: flex-end;
}
.chart-bar {
    width: 100%;
    max-width: 48px;
    border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, var(--accent, #0ea5e9), rgba(14,165,233,.4));
    transition: all .3s ease;
    min-height: 4px;
    position: relative;
}
.chart-bar:hover {
    filter: brightness(1.15);
    transform: scaleY(1.02);
    transform-origin: bottom;
}
.chart-bar-label {
    font-size: .7rem;
    color: #64748b;
    margin-top: .5rem;
    font-weight: 600;
    text-align: center;
}
.chart-bar-value {
    font-size: .72rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: .35rem;
}
.ranking-number {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 800;
    flex-shrink: 0;
}
.ranking-number.gold { background: rgba(245,158,11,.15); color: #d97706; }
.rankingNumber.silver { background: rgba(148,163,184,.15); color: #64748b; }
.rankingNumber.bronze { background: rgba(180,83,9,.12); color: #b45309; }
.rankingNumber.normal { background: rgba(148,163,184,.08); color: #94a3b8; }
.delivery-summary-card {
    background: linear-gradient(135deg, rgba(14,165,233,.06), rgba(14,165,233,.02));
    border: 1px solid rgba(14,165,233,.12);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
}
@media (max-width: 767.98px) {
    .stat-main .ui-stat-value { font-size: 1.75rem; }
    .chart-bar-container { height: 140px; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Dashboard Delivery</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>{{ now()->format('d/m/Y') }} — Resumen operativo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Main Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-main" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-truck me-1"></i>Entregas Hoy</div>
                    <div class="ui-stat-value">{{ $stats['hoy'] }}</div>
                    <div class="ui-stat-sub">
                        <i class="bi bi-arrow-up me-1"></i>vs ayer: {{ $stats['variacion_hoy'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-main" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-hourglass-split me-1"></i>Pendientes</div>
                    <div class="ui-stat-value" style="color:#d97706;">{{ $stats['pendientes'] }}</div>
                    <div class="ui-stat-sub">Sin asignar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-main" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-truck-flatbed me-1"></i>En Camino</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;">{{ $stats['en_camino'] }}</div>
                    <div class="ui-stat-sub">En tránsito</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-main" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-check-circle me-1"></i>Entregadas</div>
                    <div class="ui-stat-value" style="color:#16a34a;">{{ $stats['entregadas'] }}</div>
                    <div class="ui-stat-sub">Hoy completadas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-secondary" style="--delay:.3s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-people me-1"></i>Drivers Activos</div>
                    <div class="ui-stat-value">{{ $stats['drivers_activos'] }}</div>
                    <div class="ui-stat-sub">de {{ $stats['total_drivers'] }} registrados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-secondary" style="--delay:.35s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-cash-stack me-1"></i>Ganancias del Mes</div>
                    <div class="ui-stat-value" style="color:#16a34a;">${{ number_format($stats['ganancias_mes'], 2) }}</div>
                    <div class="ui-stat-sub">Comisiones delivery</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-secondary" style="--delay:.4s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-x-octagon me-1"></i>Fallidas Hoy</div>
                    <div class="ui-stat-value" style="color:#dc2626;">{{ $stats['fallidas'] }}</div>
                    <div class="ui-stat-sub">
                        {{ $stats['tasa_exito'] ?? 0 }}% tasa éxito
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat stat-secondary" style="--delay:.45s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-clock-history me-1"></i>Tiempo Promedio</div>
                    <div class="ui-stat-value" style="font-size:1.3rem;">{{ $stats['tiempo_promedio'] ?? '—' }}</div>
                    <div class="ui-stat-sub">Minutos por entrega</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Tables Row --}}
    <div class="row g-4 mb-4">
        {{-- Weekly Chart --}}
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.5s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-4" style="color:#0ea5e9;">
                        <i class="bi bi-bar-chart me-2"></i>Últimos 7 Días
                    </h6>
                    <div class="chart-bar-container">
                        @foreach($weekly_data as $day)
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar-value">{{ $day['count'] }}</div>
                            <div class="chart-bar" style="height: {{ $day['percentage'] }}%;" title="{{ $day['label'] }}: {{ $day['count'] }} entregas"></div>
                            <div class="chart-bar-label">{{ $day['short_label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Drivers --}}
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.55s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-4" style="color:#0ea5e9;">
                        <i class="bi bi-trophy me-2"></i>Top 5 Drivers
                    </h6>
                    <div class="table-responsive">
                        <table class="ui-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Driver</th>
                                    <th class="text-center">Entregas</th>
                                    <th class="text-end">Ganancia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_drivers as $index => $driver)
                                <tr>
                                    <td>
                                        <span class="ranking-number {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'normal')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="driver-avatar" style="width:30px;height:30px;font-size:.65rem;">
                                                {{ strtoupper(substr($driver->nombre, 0, 1) . substr($driver->apellido, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold small">{{ $driver->nombre }} {{ $driver->apellido }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="ui-badge ui-badge-info">{{ $driver->entregas }}</span>
                                    </td>
                                    <td class="text-end fw-bold small">${{ number_format($driver->ganancia, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted small">
                                        Sin datos disponibles
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Deliveries --}}
    <div class="ui-card" style="--delay:.6s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-clock-history me-2"></i>Últimas Entregas
                </h6>
                <a href="{{ route('delivery-tracking.index') }}" class="ui-btn ui-btn-link ui-btn-sm">
                    Ver todas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Orden</th>
                            <th>Cliente</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Hora</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_deliveries as $delivery)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <a href="{{ route('delivery-tracking.show', $delivery) }}" class="text-decoration-none" style="color:#0ea5e9;">
                                    #{{ $delivery->orden_id }}
                                </a>
                            </td>
                            <td>{{ $delivery->orden?->cliente?->nombre ?? 'N/A' }}</td>
                            <td>
                                @if($delivery->driver)
                                    <small>{{ $delivery->driver->nombre }} {{ $delivery->driver->apellido }}</small>
                                @else
                                    <span class="text-muted small">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $st = [
                                        'creado' => ['badge' => 'neutral', 'icon' => 'bi-clock', 'label' => 'Creado'],
                                        'en_camino' => ['badge' => 'info', 'icon' => 'bi-truck', 'label' => 'En Camino'],
                                        'entregado' => ['badge' => 'success', 'icon' => 'bi-check-circle', 'label' => 'Entregado'],
                                        'fallido' => ['badge' => 'danger', 'icon' => 'bi-x-circle', 'label' => 'Fallido'],
                                        'cancelado' => ['badge' => 'warning', 'icon' => 'bi-slash-circle', 'label' => 'Cancelado'],
                                    ];
                                    $sc = $st[$delivery->status] ?? $st['creado'];
                                @endphp
                                <span class="ui-badge ui-badge-{{ $sc['badge'] }}">
                                    <i class="bi {{ $sc['icon'] }} me-1"></i>{{ $sc['label'] }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $delivery->created_at->format('H:i') }}</small></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('delivery-tracking.show', $delivery) }}" class="ui-action ui-action-view" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Sin entregas recientes
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
