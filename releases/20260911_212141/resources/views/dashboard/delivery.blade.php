@extends('layouts.app')

@section('title', 'Dashboard Delivery')

@push('styles')
@include('partials.premium-ui')
<style>
.delivery-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
.chart-container {
    position: relative;
    height: 280px;
}
@media (max-width: 767.98px) {
    .delivery-stats .ui-stat { margin-bottom: .75rem; }
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
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Dashboard Delivery</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span>Resumen de operaciones de entrega</span>
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

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 delivery-stats">
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="delivery-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
                            <i class="bi bi-box-seam"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Entregas Hoy</div>
                    <div class="ui-stat-value">{{ $totalHoy }}</div>
                    <small class="text-muted">{{ $pendientes }} pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="delivery-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
                            <i class="bi bi-truck"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">En Camino</div>
                    <div class="ui-stat-value">{{ $enCamino }}</div>
                    <small class="text-muted">En tránsito</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="delivery-stat-icon" style="background:rgba(34,197,94,.1);color:#22c55e;">
                            <i class="bi bi-check-circle"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Entregadas</div>
                    <div class="ui-stat-value">{{ $entregadas }}</div>
                    <small class="text-muted">Exitosas hoy</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.25s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="delivery-stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444;">
                            <i class="bi bi-x-circle"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Fallidas</div>
                    <div class="ui-stat-value">{{ $fallidas }}</div>
                    <small class="text-muted">Requieren atención</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Additional Stats --}}
        <div class="col-lg-4">
            <div class="ui-card h-100" style="--delay:.3s">
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-people me-2"></i>Drivers Activos
                    </h6>
                    <div class="text-center py-3">
                        <div class="display-4 fw-bold text-primary">{{ $totalDriversActivos }}</div>
                        <small class="text-muted">Repartidores registrados</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card h-100" style="--delay:.35s">
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-cash-coin me-2"></i>Ganancias del Mes
                    </h6>
                    <div class="text-center py-3">
                        <div class="display-4 fw-bold" style="color:#16a34a;">RD${{ number_format($gananciasMes, 2) }}</div>
                        <small class="text-muted">Mes actual</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card h-100" style="--delay:.4s">
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-star me-2"></i>Top Driver
                    </h6>
                    @php $topDriver = $topDrivers->first(); @endphp
                    @if($topDriver && $topDriver->driver)
                        <div class="text-center py-3">
                            <div class="driver-avatar mx-auto mb-2" style="width:56px;height:56px;font-size:1.1rem;background:rgba(245,158,11,.1);color:#f59e0b;">
                                {{ strtoupper(substr($topDriver->driver->nombre, 0, 1) . substr($topDriver->driver->apellido, 0, 1)) }}
                            </div>
                            <h5 class="fw-bold mb-1">{{ $topDriver->driver->nombre }} {{ $topDriver->driver->apellido }}</h5>
                            <small class="text-muted">Repartidor del mes</small>
                            <div class="mt-2">
                                <span class="ui-badge ui-badge-warning">
                                    <i class="bi bi-trophy me-1"></i>{{ $topDriver->total }} entregas
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-trophy fs-2 d-block mb-2"></i>
                            Sin datos aún
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.45s">
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-bar-chart me-2"></i>Entregas Últimos 7 Días
                    </h6>
                    <div class="chart-container">
                        <canvas id="deliveriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.5s">
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-list-stars me-2"></i>Top 5 Drivers
                    </h6>
                    @if($topDrivers->count())
                        <ol class="list-group list-group-numbered">
                            @foreach($topDrivers as $idx => $driver)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill" style="background:rgba(14,165,233,.1);color:#0ea5e9;width:28px;text-align:center;">{{ $idx + 1 }}</span>
                                    <div>
                                        <strong>{{ $driver->driver->nombre ?? '' }} {{ $driver->driver->apellido ?? '' }}</strong>
                                        <br><small class="text-muted">Entregas del mes</small>
                                    </div>
                                </div>
                                <span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;">{{ $driver->total }}</span>
                            </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-trophy fs-2 d-block mb-2"></i>
                            Sin datos aún
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('deliveriesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($chartData, 'label')) !!},
            datasets: [{
                label: 'Entregas',
                data: {!! json_encode(array_column($chartData, 'count')) !!},
                backgroundColor: 'rgba(14,165,233,0.15)',
                borderColor: 'rgba(14,165,233,1)',
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
@endsection