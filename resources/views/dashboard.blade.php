@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $pctCambio = function($actual, $anterior) {
        if ($anterior == 0) return $actual > 0 ? 100 : 0;
        return round((($actual - $anterior) / $anterior) * 100, 1);
    };
    $esPositivo = function($valor) { return $valor >= 0; };
@endphp

@section('content')
<div class="container-fluid px-0">
    <style>
        .kpi-card { transition: all .3s cubic-bezier(.4,0,.2,1); border:1px solid rgba(15,23,42,.06); position:relative; overflow:hidden; }
        .kpi-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--accent); transition: width .3s; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -8px rgba(15,23,42,.15) !important; }
        .kpi-card:hover::before { width:8px; }
        .kpi-card.accent-primary { --accent: #38bdf8; }
        .kpi-card.accent-success { --accent: #22c55e; }
        .kpi-card.accent-warning { --accent: #f59e0b; }
        .kpi-card.accent-danger  { --accent: #ef4444; }
        .kpi-card.accent-info    { --accent: #6366f1; }
        .kpi-card.accent-dark    { --accent: #0f172a; }

        .icon-bubble { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
        .bg-soft-primary { background:rgba(56,189,248,.12); color:#0284c7; }
        .bg-soft-success { background:rgba(34,197,94,.12); color:#16a34a; }
        .bg-soft-warning { background:rgba(245,158,11,.12); color:#d97706; }
        .bg-soft-danger  { background:rgba(239,68,68,.12);  color:#dc2626; }
        .bg-soft-info    { background:rgba(99,102,241,.12); color:#4f46e5; }
        .bg-soft-dark    { background:rgba(15,23,42,.1);    color:#0f172a; }

        .trend-pill { font-size:.7rem; padding:.2rem .55rem; border-radius:999px; font-weight:700; }
        .trend-up   { background:rgba(34,197,94,.12); color:#16a34a; }
        .trend-down { background:rgba(239,68,68,.12);  color:#dc2626; }
        .trend-flat { background:rgba(100,116,139,.12); color:#64748b; }

        .hero-stats-card { background: linear-gradient(135deg,#0f172a 0%,#1e293b 100%); color:#f8fafc; position:relative; overflow:hidden; }
        .hero-stats-card::after { content:''; position:absolute; right:-30px; top:-30px; width:200px; height:200px; background: radial-gradient(circle, rgba(56,189,248,.18) 0%, transparent 70%); }
        .hero-stats-card::before { content:''; position:absolute; left:-30px; bottom:-30px; width:180px; height:180px; background: radial-gradient(circle, rgba(34,197,94,.12) 0%, transparent 70%); }

        .chart-card { background: #fff; }
        body.dark-mode .chart-card { background:#0f172a; border:1px solid #1e293b; }

        .stat-mini { padding:14px 16px; border-radius:12px; background:rgba(248,250,252,.7); border:1px solid #e2e8f0; }
        body.dark-mode .stat-mini { background:rgba(15,23,42,.4); border-color:#1e293b; }

        .top-product { transition: all .2s; padding:12px; border-radius:10px; }
        .top-product:hover { background: rgba(56,189,248,.06); transform: translateX(4px); }

        .debtor-row { transition: all .2s; padding:12px 16px; border-radius:10px; }
        .debtor-row:hover { background: rgba(239,68,68,.05); }

        .activity-dot { width:10px; height:10px; border-radius:50%; display:inline-block; box-shadow: 0 0 0 4px rgba(56,189,248,.18); }

        .quick-action { display:flex; flex-direction:column; align-items:center; gap:.4rem; padding:1rem; border-radius:14px; background:rgba(255,255,255,.06); color:#f1f5f9; text-decoration:none; transition:all .2s; border:1px solid rgba(255,255,255,.08); }
        .quick-action:hover { background: rgba(56,189,248,.15); color:#fff; transform: translateY(-2px); border-color:rgba(56,189,248,.3); }
        .quick-action i { font-size:1.5rem; }

        .alert-pill { padding:.6rem .9rem; border-radius:12px; font-size:.85rem; display:flex; align-items:center; gap:.5rem; }
        .alert-pill i { font-size:1.1rem; }

        .caja-pulse { width:10px; height:10px; border-radius:50%; background:#22c55e; animation: pulse 2s infinite; display:inline-block; }
        @keyframes pulse { 0% { box-shadow:0 0 0 0 rgba(34,197,94,.6); } 70% { box-shadow:0 0 0 12px rgba(34,197,94,0); } 100% { box-shadow:0 0 0 0 rgba(34,197,94,0); } }

        .rank-1 { background: linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; }
        .rank-2 { background: linear-gradient(135deg,#cbd5e1,#94a3b8); color:#fff; }
        .rank-3 { background: linear-gradient(135deg,#d97706,#92400e); color:#fff; }
        .rank-n { background:#e2e8f0; color:#64748b; }
        body.dark-mode .rank-n { background:#1e293b; color:#94a3b8; }
    </style>

    {{-- ============ HERO + ACCIONES RÁPIDAS ============ --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card hero-stats-card border-0 shadow-lg rounded-4 h-100">
                <div class="card-body p-4 p-md-5 position-relative" style="z-index:2;">
                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 mb-2">
                                <i class="bi bi-broadcast me-1"></i> En vivo
                            </span>
                            <h2 class="fw-bold mb-1 text-white">¡Hola, {{ Auth::user()->name }}!</h2>
                            <p class="text-white-50 mb-0">
                                <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                                <span class="mx-2">·</span>
                                <i class="bi bi-clock me-1"></i><span id="live-clock">{{ now()->format('h:i A') }}</span>
                            </p>
                        </div>
                        @if($cajaActual['abierta'])
                            <div class="text-end">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="caja-pulse"></span>
                                    <small class="text-white-50">Caja abierta</small>
                                </div>
                                <h5 class="fw-bold text-white mb-0">{{ $cajaActual['caja'] ?? 'Caja principal' }}</h5>
                                <small class="text-white-50">desde {{ $cajaActual['abierta_en']?->format('h:i A') }}</small>
                            </div>
                        @else
                            <a href="{{ route('cajas.index') }}" class="btn btn-warning rounded-pill px-3 fw-bold">
                                <i class="bi bi-cash-coin me-1"></i> Abrir caja
                            </a>
                        @endif
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Ventas hoy</small>
                            <h3 class="fw-bold text-white mb-0">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ventasHoy, 2) }}</h3>
                            <small class="text-white-50">{{ $ticketsHoy }} ticket(s)</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Cobros del día</small>
                            <h3 class="fw-bold text-success mb-0">{{ $systemMoneda ?? 'RD$' }} {{ number_format($cobrosHoy, 2) }}</h3>
                            <small class="text-white-50">Pagos registrados</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Ticket promedio</small>
                            <h3 class="fw-bold text-info mb-0">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ticketPromedio, 2) }}</h3>
                            <small class="text-white-50">por venta</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size:.75rem;letter-spacing:1px;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Acciones rápidas
                    </h6>
                    <div class="row g-2 flex-grow-1">
                        <div class="col-6">
                            <a href="{{ route('ventas.create') }}" class="quick-action h-100 justify-content-center" style="background:rgba(56,189,248,.12);color:#0284c7;border-color:rgba(56,189,248,.2);">
                                <i class="bi bi-cart-plus"></i><span class="fw-bold small">Nueva Venta</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('compras.create') }}" class="quick-action h-100 justify-content-center" style="background:rgba(34,197,94,.12);color:#16a34a;border-color:rgba(34,197,94,.2);">
                                <i class="bi bi-bag-plus"></i><span class="fw-bold small">Nueva Compra</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('productos.create') }}" class="quick-action h-100 justify-content-center" style="background:rgba(245,158,11,.12);color:#d97706;border-color:rgba(245,158,11,.2);">
                                <i class="bi bi-box-seam"></i><span class="fw-bold small">Nuevo Producto</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('clientes.cuentas') }}" class="quick-action h-100 justify-content-center" style="background:rgba(239,68,68,.12);color:#dc2626;border-color:rgba(239,68,68,.2);">
                                <i class="bi bi-cash-coin"></i><span class="fw-bold small">Cobrar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ KPIs PRINCIPALES ============ --}}
    <div class="row g-3 mb-4">
        @php
            $cambioVentasHoy = $pctCambio($ventasHoy, $ventasAyer);
            $cambioIngresos  = $pctCambio($ingresosMes, $ingresosMesAnt);
            $cambioUtilidad  = $pctCambio($utilidadMes, $utilidadMesAnt);
            $margen = $ingresosMes > 0 ? round(($utilidadMes / $ingresosMes) * 100, 1) : 0;
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card accent-primary border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-bubble bg-soft-primary"><i class="bi bi-graph-up-arrow"></i></div>
                        <span class="trend-pill {{ $esPositivo($cambioVentasHoy) ? 'trend-up' : 'trend-down' }}">
                            <i class="bi bi-arrow-{{ $esPositivo($cambioVentasHoy) ? 'up' : 'down' }}-short"></i>{{ abs($cambioVentasHoy) }}%
                        </span>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ventas de hoy</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ventasHoy, 2) }}</h3>
                    <small class="text-muted">vs {{ $systemMoneda ?? 'RD$' }} {{ number_format($ventasAyer, 0) }} ayer</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card accent-info border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-bubble bg-soft-info"><i class="bi bi-cash-stack"></i></div>
                        <span class="trend-pill {{ $esPositivo($cambioIngresos) ? 'trend-up' : 'trend-down' }}">
                            <i class="bi bi-arrow-{{ $esPositivo($cambioIngresos) ? 'up' : 'down' }}-short"></i>{{ abs($cambioIngresos) }}%
                        </span>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ingresos del mes</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ingresosMes, 2) }}</h3>
                    <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card accent-success border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-bubble bg-soft-success"><i class="bi bi-piggy-bank"></i></div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size:.7rem;">
                            Margen {{ $margen }}%
                        </span>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Utilidad del mes</small>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ $systemMoneda ?? 'RD$' }} {{ number_format($utilidadMes, 2) }}</h3>
                    <small class="text-muted">Ganancia bruta</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card accent-danger border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-bubble bg-soft-danger"><i class="bi bi-wallet2"></i></div>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size:.7rem;">
                            {{ $facturasPendientes }} fact.
                        </span>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Cuentas por cobrar</small>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ $systemMoneda ?? 'RD$' }} {{ number_format($totalCuentasPorCobrar, 2) }}</h3>
                    <small class="text-muted">{{ $clientesConDeuda }} cliente(s) con deuda</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ KPIs SECUNDARIOS ============ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card kpi-card accent-warning border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-soft-warning flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">STOCK CRÍTICO</small>
                            <h4 class="fw-bold text-warning mb-0">{{ $productosCriticos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card kpi-card accent-info border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-soft-info flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                            <i class="bi bi-box"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">TOTAL PRODUCTOS</small>
                            <h4 class="fw-bold mb-0">{{ $totalProductos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card kpi-card accent-primary border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-soft-primary flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">CLIENTES</small>
                            <h4 class="fw-bold mb-0">{{ \App\Models\Cliente::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card kpi-card accent-dark border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-soft-dark flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                            <i class="bi bi-safe2"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">INVENTARIO</small>
                            <h4 class="fw-bold mb-0">{{ $systemMoneda ?? 'RD$' }} {{ number_format($valorInventario, 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ GRÁFICOS PRINCIPALES ============ --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-graph-up text-primary me-2"></i>Tendencia de ventas</h5>
                        <small class="text-muted">Últimos 30 días · Total: {{ $systemMoneda ?? 'RD$' }} {{ number_format(array_sum($chartData), 0) }}</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light active" data-range="30">30d</button>
                        <button class="btn btn-light" data-range="7">7d</button>
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <canvas id="ventasChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart text-success me-2"></i>Ventas por hora</h5>
                    <small class="text-muted">Hoy · Pico: {{ $systemMoneda ?? 'RD$' }} {{ number_format(max($horasData), 0) }}</small>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="horasChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TOP PRODUCTOS + DEUDORES + RANKING ============ --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-5">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Top productos</h5>
                    <span class="badge bg-light text-muted">{{ now()->translatedFormat('F') }}</span>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($topProductos as $i => $prod)
                        @php
                            $maxVendidos = $topProductos->max('cantidad_vendida') ?: 1;
                            $porcentaje = ($prod->cantidad_vendida / $maxVendidos) * 100;
                            $rankClass = 'rank-' . ($i + 1);
                            if ($i > 2) $rankClass = 'rank-n';
                        @endphp
                        <div class="top-product d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-circle {{ $rankClass }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-truncate" title="{{ $prod->nombre }}">{{ $prod->nombre }}</h6>
                                    <span class="text-muted small ms-2">{{ $prod->cantidad_vendida }} u.</span>
                                </div>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-primary bg-gradient" style="width:{{ $porcentaje }}%"></div>
                                </div>
                                <small class="text-muted">{{ $systemMoneda ?? 'RD$' }} {{ number_format($prod->ingreso_total, 0) }} · Util: {{ $systemMoneda ?? 'RD$' }} {{ number_format($prod->utilidad, 0) }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-emoji-frown fs-1"></i>
                            <p class="mt-2 mb-0">Aún no hay ventas este mes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-people text-danger me-2"></i>Mayores deudores</h5>
                    <a href="{{ route('clientes.cuentas') }}" class="text-decoration-none small fw-bold">Ver todos</a>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($topDeudores as $deudor)
                        <div class="debtor-row d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:42px;height:42px;">
                                    {{ strtoupper(substr($deudor->nombre, 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold mb-0 text-truncate" style="max-width:160px;">{{ $deudor->nombre }}</h6>
                                    <small class="text-muted">{{ $deudor->rnc_cedula ?? 'Sin RNC' }}</small>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold text-danger">{{ $systemMoneda ?? 'RD$' }} {{ number_format($deudor->balance_pendiente, 0) }}</div>
                                <a href="{{ route('clientes.cuentas') }}" class="text-decoration-none small fw-bold">Cobrar</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="mt-2 mb-0">¡Sin deudas pendientes!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge text-info me-2"></i>Ranking cajeros</h5>
                    <small class="text-muted">Ventas del mes</small>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($rankingUsuarios as $i => $user)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n')) }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;">
                                {{ $i + 1 }}
                            </div>
                            <div class="overflow-hidden flex-grow-1">
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width:120px;">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->tickets }} tickets</small>
                            </div>
                            <div class="text-end">
                                <small class="fw-bold">{{ $systemMoneda ?? 'RD$' }} {{ number_format($user->total_vendido, 0) }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person-x fs-1"></i>
                            <p class="mt-2 mb-0">Sin actividad</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============ ACTIVIDAD RECIENTE + ALERTAS ============ --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card chart-card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Ventas recientes</h5>
                    <a href="{{ route('ventas.index') }}" class="text-decoration-none small fw-bold">Ver historial completo</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:1px;">
                                <th class="ps-4 py-3">Ticket</th>
                                <th>Cliente</th>
                                <th>Cajero</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $venta)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('ventas.show', $venta) }}" class="text-decoration-none">
                                            <span class="fw-bold text-primary">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</span>
                                    </td>
                                    <td><span class="small text-muted">{{ $venta->usuario->name ?? '—' }}</span></td>
                                    <td class="text-end fw-bold">{{ $systemMoneda ?? 'RD$' }} {{ number_format($venta->total, 2) }}</td>
                                    <td>
                                        @php
                                            $estadoBadge = match($venta->estado) {
                                                'completada', 'pagada' => 'success',
                                                'pendiente' => 'warning',
                                                'cuenta_abierta' => 'info',
                                                'anulada' => 'secondary',
                                                default => 'secondary'
                                            };
                                            $estadoLabel = match($venta->estado) {
                                                'completada', 'pagada' => 'Pagada',
                                                'pendiente' => 'Pendiente',
                                                'cuenta_abierta' => 'Cta. Abierta',
                                                'anulada' => 'Anulada',
                                                default => ucfirst($venta->estado)
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $estadoBadge }} bg-opacity-10 text-{{ $estadoBadge }} rounded-pill px-2 py-1">{{ $estadoLabel }}</span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $venta->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2 mb-0">Sin ventas registradas</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bell text-warning me-2"></i>Alertas y acción</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($alertas['stock_critico']->count() > 0)
                        <div class="alert-pill bg-warning bg-opacity-10 text-warning mb-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="flex-grow-1">
                                <strong>{{ $alertas['stock_critico']->count() }} producto(s) con stock crítico</strong>
                                <div class="small text-muted">{{ $alertas['stock_critico']->pluck('nombre')->take(2)->implode(', ') }}@if($alertas['stock_critico']->count() > 2) y más...@endif</div>
                            </div>
                            <a href="{{ route('productos.index', ['stock_status' => 'critical']) }}" class="btn btn-sm btn-warning rounded-pill px-2 py-0">Ver</a>
                        </div>
                    @endif

                    @if($facturasPendientes > 0)
                        <div class="alert-pill bg-danger bg-opacity-10 text-danger mb-2">
                            <i class="bi bi-receipt-cutoff"></i>
                            <div class="flex-grow-1">
                                <strong>{{ $facturasPendientes }} factura(s) pendiente(s)</strong>
                                <div class="small text-muted">Cuentas abiertas o sin pagar</div>
                            </div>
                            <a href="{{ route('clientes.cuentas') }}" class="btn btn-sm btn-danger rounded-pill px-2 py-0">Cobrar</a>
                        </div>
                    @endif

                    @if($alertas['ncf_por_vencer']->count() > 0)
                        <div class="alert-pill bg-info bg-opacity-10 text-info mb-2">
                            <i class="bi bi-receipt"></i>
                            <div class="flex-grow-1">
                                <strong>NCF por vencer</strong>
                                <div class="small text-muted">{{ $alertas['ncf_por_vencer']->pluck('nombre')->take(2)->implode(', ') }}</div>
                            </div>
                            <a href="{{ route('ncf.index') }}" class="btn btn-sm btn-info rounded-pill px-2 py-0">Revisar</a>
                        </div>
                    @endif

                    @if($productosSinRotacion > 0)
                        <div class="alert-pill bg-secondary bg-opacity-10 text-secondary mb-2">
                            <i class="bi bi-archive"></i>
                            <div class="flex-grow-1">
                                <strong>{{ $productosSinRotacion }} producto(s) sin ventas este mes</strong>
                                <div class="small text-muted">Considera promocionarlos</div>
                            </div>
                        </div>
                    @endif

                    @if($alertas['stock_critico']->count() === 0 && $facturasPendientes === 0 && $alertas['ncf_por_vencer']->count() === 0 && $productosSinRotacion === 0 && count($alertas['sistema']) === 0)
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-emoji-smile text-success fs-1"></i>
                            <p class="mt-2 mb-0">¡Todo en orden!</p>
                            <small>Sin alertas pendientes</small>
                        </div>
                    @endif

                    @if(count($alertas['sistema']) > 0)
                    <div class="mt-3">
                        <small class="text-muted text-uppercase fw-bold"><i class="bi bi-gear me-1"></i>Sistema</small>
                        @foreach($alertas['sistema'] as $alerta)
                        <div class="alert alert-{{ $alerta['color'] }} border-0 rounded-3 py-2 px-3 mb-1 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi {{ $alerta['icono'] }} me-2"></i>
                                <span>{{ $alerta['mensaje'] }}</span>
                            </div>
                            <a href="{{ $alerta['link'] }}" class="btn btn-sm btn-outline-{{ $alerta['color'] }} rounded-pill ms-2 flex-shrink-0">{{ $alerta['link_text'] }}</a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.body.classList.contains('dark-mode');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // Live clock
    function updateClock() {
        const el = document.getElementById('live-clock');
        if (el) {
            const d = new Date();
            el.textContent = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }
    }
    setInterval(updateClock, 30000);

    // Ventas 30 días
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    const grad = ventasCtx.createLinearGradient(0, 0, 0, 320);
    grad.addColorStop(0, isDark ? 'rgba(56,189,248,0.5)' : 'rgba(13,110,253,0.35)');
    grad.addColorStop(1, 'rgba(56,189,248,0)');

    new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Ventas',
                data: @json($chartData),
                borderColor: isDark ? '#38bdf8' : '#0d6efd',
                backgroundColor: grad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: isDark ? '#38bdf8' : '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f8fafc' : '#1e293b',
                    bodyColor: isDark ? '#cbd5e1' : '#64748b',
                    borderColor: isDark ? '#334155' : '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: (ctx) => '{{ $systemMoneda ?? "RD$" }} ' + ctx.parsed.y.toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                y: { grid: { color: gridColor, borderDash: [5, 5] }, ticks: { color: textColor, font: { size: 10 }, callback: v => v.toLocaleString() }, beginAtZero: true }
            }
        }
    });

    // Ventas por hora
    const horasCtx = document.getElementById('horasChart').getContext('2d');
    new Chart(horasCtx, {
        type: 'bar',
        data: {
            labels: @json($horasLabels),
            datasets: [{
                data: @json($horasData),
                backgroundColor: isDark ? 'rgba(56,189,248,0.6)' : 'rgba(13,110,253,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9 } } },
                y: { grid: { color: gridColor, borderDash: [5, 5] }, ticks: { color: textColor, font: { size: 9 }, callback: v => v.toLocaleString() }, beginAtZero: true }
            }
        }
    });
});
</script>
@endsection
