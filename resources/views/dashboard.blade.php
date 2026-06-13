@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    @vite(['resources/scss/dashboard.scss'])
@endpush

@section('content')
<div class="container-fluid px-0">

    {{--============ HERO + ACCIONES RÁPIDAS ============--}}
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
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($cajaActual['abierta'])
                                <div class="text-end me-3">
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
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="row g-2 mt-3">
                        <div class="col-auto">
                            <input type="date" name="desde" value="{{ request('desde') }}" class="form-control form-control-sm date-filter border-0">
                        </div>
                        <div class="col-auto">
                            <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control form-control-sm date-filter border-0">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                        </div>
                        @if(request('desde') || request('hasta'))
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="bi bi-x-lg me-1"></i>Limpiar
                            </a>
                        </div>
                        @endif
                    </form>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Ventas {{ request('desde') ? 'filtradas' : 'hoy' }}</small>
                            <h3 class="fw-bold text-white mb-0">{{ $moneda }} {{ number_format($kpis['ventasHoy'], 2) }}</h3>
                            <small class="text-white-50">{{ $kpis['ticketsHoy'] }} ticket(s)</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Cobros del día</small>
                            <h3 class="fw-bold text-success mb-0">{{ $moneda }} {{ number_format($secondaryStats['cobrosHoy'], 2) }}</h3>
                            <small class="text-white-50">Pagos registrados</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:.7rem;letter-spacing:1px;">Ticket promedio</small>
                            <h3 class="fw-bold text-info mb-0">{{ $moneda }} {{ number_format($kpis['ticketPromedio'], 2) }}</h3>
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

    {{--============ KPIs ============--}}
    @include('dashboard._kpis')

    {{--============ CHARTS ============--}}
    @include('dashboard._content')

    {{--============ ACTIVIDAD + ALERTAS ============--}}
    @include('dashboard._activity')
</div>

@include('dashboard._scripts')
@endsection
