@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/scss/dashboard.scss'])
    <style>
        .premium-header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border-radius: 1rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            position: relative;
            overflow: hidden;
        }
        .premium-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }
        .filter-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            color: white;
        }
        .filter-glass input[type="date"] {
            background: transparent;
            color: white;
            border: none;
        }
        .filter-glass input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        .quick-action-premium {
            transition: all 0.3s ease;
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            text-decoration: none;
            border: 1px solid transparent;
        }
        .quick-action-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{--============ HERO + ACCIONES RÁPIDAS ============--}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="premium-header h-100 p-4 p-md-5">
                <div class="position-relative" style="z-index:2;">
                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
                        <div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 mb-2 shadow-sm">
                                <i class="bi bi-broadcast me-1"></i> En vivo
                            </span>
                            <h2 class="fw-bold mb-1 text-white">¡Hola, {{ Auth::user()->name }}!</h2>
                            <p class="text-white-50 mb-0 fs-6">
                                <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                                <span class="mx-2">·</span>
                                <i class="bi bi-clock me-1"></i><span id="live-clock" class="fw-medium">{{ now()->format('h:i A') }}</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($cajaActual['abierta'])
                                <div class="text-end me-3 bg-white bg-opacity-10 rounded-4 p-3 shadow-sm" style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                    <div class="d-flex align-items-center gap-2 mb-1 justify-content-end">
                                        <span class="caja-pulse" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block; box-shadow: 0 0 10px #22c55e;"></span>
                                        <small class="text-white-50 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.65rem;">Caja abierta</small>
                                    </div>
                                    <h5 class="fw-bold text-white mb-0">{{ $cajaActual['caja'] ?? 'Caja principal' }}</h5>
                                    <small class="text-white-50">desde {{ $cajaActual['abierta_en']?->format('h:i A') }}</small>
                                </div>
                            @else
                                <a href="{{ route('cajas.index') }}" class="btn btn-warning rounded-pill px-4 py-2 shadow fw-bold">
                                    <i class="bi bi-cash-coin me-1"></i> Abrir caja
                                </a>
                            @endif
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-center mb-4">
                        <div class="col-auto">
                            <div class="filter-glass px-3 py-1 d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-event text-white-50"></i>
                                <input type="date" name="desde" value="{{ request('desde') }}" class="form-control-sm border-0 bg-transparent text-white focus-ring focus-ring-light" style="outline: none;">
                            </div>
                        </div>
                        <div class="col-auto text-white-50 fw-bold">-</div>
                        <div class="col-auto">
                            <div class="filter-glass px-3 py-1 d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-event text-white-50"></i>
                                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control-sm border-0 bg-transparent text-white focus-ring focus-ring-light" style="outline: none;">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-light rounded-pill px-3 py-1 fw-bold shadow-sm">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                        </div>
                        @if(request('desde') || request('hasta'))
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light rounded-pill px-3 py-1 border-2">
                                <i class="bi bi-x-lg me-1"></i>Limpiar
                            </a>
                        </div>
                        @endif
                    </form>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25" style="backdrop-filter: blur(5px);">
                                <small class="text-uppercase fw-bold text-white-50 mb-1 d-block" style="font-size:.7rem;letter-spacing:1px;">Ventas {{ request('desde') ? 'filtradas' : 'hoy' }}</small>
                                <h3 class="fw-bold text-white mb-0">{{ $moneda }} {{ number_format($kpis['ventasHoy'], 2) }}</h3>
                                <small class="text-white-50"><i class="bi bi-receipt me-1"></i>{{ $kpis['ticketsHoy'] }} ticket(s)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25" style="backdrop-filter: blur(5px);">
                                <small class="text-uppercase fw-bold text-white-50 mb-1 d-block" style="font-size:.7rem;letter-spacing:1px;">Cobros del día</small>
                                <h3 class="fw-bold text-white mb-0">{{ $moneda }} {{ number_format($secondaryStats['cobrosHoy'], 2) }}</h3>
                                <small class="text-white-50"><i class="bi bi-cash me-1"></i>Pagos registrados</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25" style="backdrop-filter: blur(5px);">
                                <small class="text-uppercase fw-bold text-white-50 mb-1 d-block" style="font-size:.7rem;letter-spacing:1px;">Ticket promedio</small>
                                <h3 class="fw-bold text-white mb-0">{{ $moneda }} {{ number_format($kpis['ticketPromedio'], 2) }}</h3>
                                <small class="text-white-50"><i class="bi bi-graph-up me-1"></i>por venta</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.75rem;letter-spacing:1px;">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Acciones Rápidas
                    </h6>
                    <div class="row g-3 flex-grow-1">
                        <div class="col-6">
                            <a href="{{ route('ventas.create') }}" class="quick-action-premium h-100" style="background:linear-gradient(135deg, rgba(2,132,199,0.1), rgba(56,189,248,0.1)); color:#0284c7; border: 1px solid rgba(56,189,248,0.3);">
                                <i class="bi bi-cart-plus fs-2 mb-2"></i>
                                <span class="fw-bold small text-center">Nueva Venta</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('compras.create') }}" class="quick-action-premium h-100" style="background:linear-gradient(135deg, rgba(22,163,74,0.1), rgba(34,197,94,0.1)); color:#16a34a; border: 1px solid rgba(34,197,94,0.3);">
                                <i class="bi bi-bag-plus fs-2 mb-2"></i>
                                <span class="fw-bold small text-center">Nueva Compra</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('productos.create') }}" class="quick-action-premium h-100" style="background:linear-gradient(135deg, rgba(217,119,6,0.1), rgba(245,158,11,0.1)); color:#d97706; border: 1px solid rgba(245,158,11,0.3);">
                                <i class="bi bi-box-seam fs-2 mb-2"></i>
                                <span class="fw-bold small text-center">Nuevo Producto</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('clientes.cuentas') }}" class="quick-action-premium h-100" style="background:linear-gradient(135deg, rgba(220,38,38,0.1), rgba(239,68,68,0.1)); color:#dc2626; border: 1px solid rgba(239,68,68,0.3);">
                                <i class="bi bi-cash-coin fs-2 mb-2"></i>
                                <span class="fw-bold small text-center">Cobrar Deudas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--============ KPIs ============--}}
    @include('dashboard._kpis')

    {{--============ CHARTS ============--}}
    @include('dashboard._content')

    {{--============ ACTIVIDAD + ALERTAS ============--}}
    @include('dashboard._activity')
</div>

@include('dashboard._scripts')
@endsection
