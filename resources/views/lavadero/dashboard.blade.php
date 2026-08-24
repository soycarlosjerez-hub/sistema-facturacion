@extends('layouts.app')

@section('title', 'Dashboard Lavadero')

@push('styles')
@include('partials.premium-ui')
<style>
/* ===== KPI Cards ===== */
.kpi-card {
    transition: all .3s cubic-bezier(.4,0,.2,1);
    border: 1px solid rgba(15,23,42,.06);
    position: relative;
    overflow: hidden;
}
.kpi-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--accent);
    transition: width .3s;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -8px rgba(15,23,42,.15) !important;
}
.kpi-card:hover::before { width: 8px; }

.kpi-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}
.bg-soft-primary { background: rgba(56,189,248,.12); color: #0284c7; }
.bg-soft-success { background: rgba(34,197,94,.12); color: #16a34a; }
.bg-soft-warning { background: rgba(245,158,11,.12); color: #d97706; }
.bg-soft-danger  { background: rgba(239,68,68,.12); color: #dc2626; }
.bg-soft-info    { background: rgba(99,102,241,.12); color: #4f46e5; }
.bg-soft-dark    { background: rgba(15,23,42,.1); color: #0f172a; }

.trend-pill {
    font-size: .7rem;
    padding: .2rem .55rem;
    border-radius: 999px;
    font-weight: 700;
}
.trend-up { background: rgba(34,197,94,.12); color: #16a34a; }
.trend-down { background: rgba(239,68,68,.12); color: #dc2626; }

/* ===== Chart Card ===== */
.chart-card { background: #fff; border-radius: 1rem; }
body.dark-mode .chart-card { background: #0f172a; border: 1px solid #1e293b; }

/* ===== Baches ocupados ===== */
.bache-card {
    transition: all .2s;
    border-radius: .75rem;
    padding: .85rem 1rem;
    border: 1.5px solid;
    cursor: pointer;
}
.bache-card.occupied {
    background: rgba(239,68,68,.06);
    border-color: rgba(239,68,68,.25);
}
.bache-card.available {
    background: rgba(34,197,94,.06);
    border-color: rgba(34,197,94,.25);
}
.bache-card:hover { transform: translateY(-2px); }

/* ===== Top Products ===== */
.top-product {
    transition: all .2s;
    padding: 12px;
    border-radius: 10px;
    &:hover { background: rgba(6,182,212,.06); transform: translateX(4px); }
}

/* ===== Dark Mode ===== */
body.dark-mode .kpi-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .chart-card { background: #0f172a; border-color: #1e293b; }
body.dark-mode .bache-card.occupied { background: rgba(239,68,68,.12); border-color: rgba(239,68,68,.25); }
body.dark-mode .bache-card.available { background: rgba(34,197,94,.1); border-color: rgba(34,197,94,.2); }
body.dark-mode .top-product:hover { background: rgba(6,182,212,.1); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:0s; background: linear-gradient(135deg, #164e63, #0e7490, #06b6d4, #164e63);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Dashboard de Lavadero</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span>Resumen del día — {{ \Carbon\Carbon::today()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <form method="GET" action="{{ route('lavadero.dashboard') }}" class="d-flex gap-2 align-items-center">
                    <input type="date" name="fecha" class="form-control form-control-sm rounded-pill date-filter" value="{{ $fecha ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                    <button class="ui-btn ui-btn-primary ui-btn-sm ui-btn-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </form>
                <a href="{{ route('lavadero.index') }}" class="ui-btn ui-btn-primary ui-btn-sm ui-btn-pill">
                    <i class="bi bi-box-arrow-up-right me-1"></i> POS
                </a>
            </div>
        </div>
    </div>

    {{-- KPIs Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card p-3" style="--delay:.05s">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-soft-primary rounded-3">
                        <i class="bi bi-car-front"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Vehículos Hoy</div>
                        <div class="fs-3 fw-bold" style="color: var(--accent);">{{ $vehiculosHoy ?? 0 }}</div>
                    </div>
                </div>
                @if($vehiculosHoyPrev !== null)
                <div class="mt-2">
                    <span class="trend-pill {{ $vehiculosHoyPrev >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="bi bi-caret-{{ $vehiculosHoyPrev >= 0 ? 'up' : 'down' }}-fill me-1"></i>
                        {{ abs($vehiculosHoyPrev) }}% vs ayer
                    </span>
                </div>
                @endif
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card p-3" style="--delay:.1s">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-soft-success rounded-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Ingresos Hoy</div>
                        <div class="fs-3 fw-bold" style="color: var(--accent);">RD$ {{ number_format($ingresosHoy ?? 0, 2) }}</div>
                    </div>
                </div>
                @if($ingresosHoyPrev !== null)
                <div class="mt-2">
                    <span class="trend-pill {{ $ingresosHoyPrev >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="bi bi-caret-{{ $ingresosHoyPrev >= 0 ? 'up' : 'down' }}-fill me-1"></i>
                        {{ abs($ingresosHoyPrev) }}% vs ayer
                    </span>
                </div>
                @endif
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card p-3" style="--delay:.15s">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-soft-warning rounded-3">
                        <i class="bi bi-cup-straw"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Vta. Alimentos</div>
                        <div class="fs-3 fw-bold" style="color: var(--accent);">RD$ {{ number_format($ventasAlimentos ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="trend-pill trend-up">
                        <i class="bi bi-graph-up me-1"></i>
                        {{ $ventasAlimentosCount ?? 0 }} ventas
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card p-3" style="--delay:.2s">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-soft-info rounded-3">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Vta. Accesorios</div>
                        <div class="fs-3 fw-bold" style="color: var(--accent);">RD$ {{ number_format($ventasAccesorios ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="trend-pill trend-up">
                        <i class="bi bi-graph-up me-1"></i>
                        {{ $ventasAccesoriosCount ?? 0 }} ventas
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Gráfico de ventas por línea --}}
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-bar-chart-line"></i>Ventas por Línea de Negocio
                </div>
                <div class="ui-card-subtitle">Comparativa de ingresos por servicio, alimentos y accesorios</div>
                <div class="ui-card-body">
                    <canvas id="chartLineas" height="280"></canvas>
                </div>
            </div>

            {{-- Últimos Servicios --}}
            <div class="ui-card mt-3" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-clock-history"></i>Últimos Servicios
                </div>
                <div class="ui-card-body p-0">
                    <div class="table-responsive">
                        <table class="table ui-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Servicios</th>
                                    <th class="text-end">Total</th>
                                    <th>Método</th>
                                    <th class="text-center">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($ultimosServicios ?? []) as $s)
                                <tr>
                                    <td class="fw-bold text-muted">#{{ $s->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width:32px;height:32px;font-size:.7rem;font-weight:700;
                                                        background:rgba(6,182,212,.1);color:#0891b2;">
                                                {{ strtoupper(substr($s->cliente ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="fw-medium small">{{ $s->cliente ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="small">{{ $s->vehiculo ?? '—' }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:rgba(6,182,212,.1);color:#0891b2;">
                                            {{ $s->servicios ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($s->total ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ match($s->metodo) {
                                            'efectivo' => 'bg-success bg-opacity-10 text-success',
                                            'tarjeta' => 'bg-primary bg-opacity-10 text-primary',
                                            'transferencia' => 'bg-info bg-opacity-10 text-info',
                                            default => 'bg-secondary bg-opacity-10 text-secondary'
                                        } }}">
                                            <i class="bi bi-{{ match($s->metodo) {
                                                'efectivo' => 'cash',
                                                'tarjeta' => 'credit-card',
                                                'transferencia' => 'bank',
                                                default => 'question-circle'
                                            }} me-1"></i>
                                            {{ ucfirst($s->metodo ?? '?') }}
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted">{{ $s->created_at?->format('h:i A') ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox d-block mb-2 fs-3"></i>
                                        Sin servicios registrados
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha --}}
        <div class="col-lg-4">
            {{-- Estado de Baches --}}
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-grid-3x3-gap"></i>Estado de Baches
                </div>
                <div class="ui-card-subtitle">Talleres de lavado activos</div>
                <div class="ui-card-body">
                    <div class="row g-2" id="baches-grid">
                        @forelse(($baches ?? []) as $b)
                        <div class="col-4">
                            <div class="bache-card text-center {{ $b->ocupado ? 'occupied' : 'available' }}" style="--delay:{{ sprintf('%.1f', 0.1 * $loop->index) }}s">
                                <div class="fw-bold mb-1" style="font-size:.85rem;">#{{ $b->numero }}</div>
                                <small class="{{ $b->ocupado ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
                                    <i class="bi bi-{{ $b->ocupado ? 'pause-circle-fill' : 'play-circle-fill' }} me-1"></i>
                                    {{ $b->ocupado ? 'Ocupado' : 'Libre' }}
                                </small>
                                @if($b->ocupado)
                                <div class="text-muted mt-1" style="font-size:.7rem;">
                                    {{ $b->cliente_actual ?? '—' }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-3 text-muted small">
                            <i class="bi bi-droplet-slash d-block mb-2"></i>
                            Sin baches configurados
                        </div>
                        @endforelse
                    </div>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="d-inline-block" style="width:10px;height:10px;border-radius:50%;background:#22c55e;"></span>
                            <small class="text-muted">Libre ({{ ($bachesOcupados ?? 0) - ($bachesDisponibles ?? 0) }})</small>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="d-inline-block" style="width:10px;height:10px;border-radius:50%;background:#ef4444;"></span>
                            <small class="text-muted">Ocupado ({{ $bachesOcupados ?? 0 }})</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Productos Más Vendidos --}}
            <div class="ui-card mt-3" style="--delay:.4s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-trophy"></i>Top Productos
                </div>
                <div class="ui-card-subtitle">Productos más vendidos hoy</div>
                <div class="ui-card-body p-0">
                    @forelse(($topProductos ?? []) as $i => $p)
                    <div class="top-product d-flex align-items-center justify-content-between px-3" style="--delay:{{ sprintf('%.1f', 0.1 * $loop->index) }}s">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold {{ $loop->index < 3 ? 'text-warning' : 'text-muted' }}"
                                  style="font-size:.75rem;min-width:24px;text-align:center;">
                                {{ $loop->index + 1 }}
                            </span>
                            <div>
                                <div class="fw-medium small">{{ $p->nombre ?? 'Producto' }}</div>
                                <div class="text-muted" style="font-size:.7rem;">{{ $p->cantidad ?? 0 }} uds</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small" style="color:var(--accent);">RD$ {{ number_format($p->total ?? 0, 2) }}</div>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <div class="border-bottom mx-3"></div>
                    @endif
                    @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-box-seam d-block mb-2 fs-3"></i>
                        Sin ventas de productos aún
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Alertas de Inventario --}}
            @if($alertasInventario && count($alertasInventario) > 0)
            <div class="ui-card mt-3" style="--delay:.45s;border:1.5px solid rgba(239,68,68,.2);">
                <div class="ui-card-accent" style="background:linear-gradient(90deg,#ef4444,#f87171);"></div>
                <div class="ui-card-title" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill"></i>Alertas de Inventario
                </div>
                <div class="ui-card-subtitle">Productos con stock crítico</div>
                <div class="ui-card-body p-0">
                    @foreach($alertasInventario as $a)
                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                        <div>
                            <div class="fw-medium small">{{ $a->nombre ?? 'Producto' }}</div>
                            <div class="text-muted" style="font-size:.7rem;">Stock: {{ $a->stock ?? 0 }}</div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill small">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i>Crítico
                        </span>
                    </div>
                    @if(!$loop->last)
                    <div class="border-bottom mx-3"></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart: Ventas por línea
    var ctx = document.getElementById('chartLineas');
    if (!ctx) return;

    var labels = @json($graficoLabels ?? ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']);
    var dataServicios = @json($graficoServicios ?? [0,0,0,0,0,0,0]);
    var dataAlimentos = @json($graficoAlimentos ?? [0,0,0,0,0,0,0]);
    var dataAccesorios = @json($graficoAccesorios ?? [0,0,0,0,0,0,0]);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Servicios',
                    data: dataServicios,
                    backgroundColor: 'rgba(6,182,212,.7)',
                    borderColor: 'rgba(6,182,212,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                },
                {
                    label: 'Alimentos',
                    data: dataAlimentos,
                    backgroundColor: 'rgba(245,158,11,.7)',
                    borderColor: 'rgba(245,158,11,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                },
                {
                    label: 'Accesorios',
                    data: dataAccesorios,
                    backgroundColor: 'rgba(139,92,246,.7)',
                    borderColor: 'rgba(139,92,246,1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 20, font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': RD$ ' + ctx.parsed.y.toLocaleString('es-DO', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: {
                        callback: function(v) { return 'RD$ ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v); },
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
