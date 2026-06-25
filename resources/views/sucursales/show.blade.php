@extends('layouts.app')

@section('title', $sucursal->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .sucursales-detail-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .sucursales-detail-card .form-label,
body.dark-mode .sucursales-detail-card .text-muted { color: #94a3b8; }
body.dark-mode .sucursales-detail-card .text-dark { color: #f1f5f9 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">
                        {{ $sucursal->nombre }}
                        @if($sucursal->es_matriz)
                            <span class="premium-badge active" style="font-size:.6rem;"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                        @endif
                        @if($sucursal->activa)
                            <span class="premium-badge active" style="font-size:.6rem;"><i class="bi bi-check-circle me-1"></i>Activa</span>
                        @else
                            <span class="premium-badge" style="font-size:.6rem;"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                        @endif
                    </h4>
                    <small class="text-white opacity-75">{{ $sucursal->codigo }} &middot; {{ $sucursal->direccion ?? 'Sin dirección' }}</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('sucursales.edit')
                <a href="{{ route('sucursales.edit', $sucursal) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-pencil me-2"></i>Editar
                </a>
                @endcan
                <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Overview Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.1s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-graph-up text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label">Ventas del mes</div>
                            <div class="stat-value">{{ $stats['ventas_mes'] }}</div>
                            <small class="text-muted">{{ $stats['ingresos_mes'] > 0 ? ($systemMoneda ?? '$') . ' ' . number_format($stats['ingresos_mes'], 0) : 'Sin ingresos' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.15s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-building text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label">Almacenes</div>
                            <div class="stat-value">{{ $sucursal->almacenes_count ?? $stats['almacenes'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-cash-stack text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label">Cajas activas</div>
                            <div class="stat-value">{{ $stats['cajas_activas'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.25s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-people text-info fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label">Empleados</div>
                            <div class="stat-value">{{ $stats['empleados'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail + Recent Activity --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="premium-card sucursales-detail-card h-100" style="animation-delay:.3s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-info-circle icon-purple"></i>
                    Información
                </div>
                <div class="card-body pt-0">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Código</div>
                        <div class="premium-detail-value">{{ $sucursal->codigo }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Nombre</div>
                        <div class="premium-detail-value">{{ $sucursal->nombre }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Dirección</div>
                        <div class="premium-detail-value">{{ $sucursal->direccion ?? '—' }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Teléfono</div>
                        <div class="premium-detail-value">{{ $sucursal->telefono ?? '—' }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Email</div>
                        <div class="premium-detail-value">{{ $sucursal->email ?? '—' }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">RNC</div>
                        <div class="premium-detail-value">{{ $sucursal->rnc ?? '—' }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Creado</div>
                        <div class="premium-detail-value">{{ $sucursal->created_at->format('d/m/Y h:i A') }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Actualizado</div>
                        <div class="premium-detail-value">{{ $sucursal->updated_at->format('d/m/Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="premium-card h-100" style="animation-delay:.35s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-clock-history icon-purple"></i>
                    Ventas recientes
                </div>
                <div class="card-body pt-0">
                    @forelse($activity['ultimas_ventas'] as $venta)
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                        <div>
                            <span class="fw-bold text-primary">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <small class="text-muted ms-2">{{ $venta->cliente?->nombre ?? 'Consumidor Final' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">{{ $systemMoneda ?? '$' }}{{ number_format($venta->total, 2) }}</span>
                            <small class="d-block text-muted">{{ $venta->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2 mb-0">Sin ventas registradas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="premium-card" style="animation-delay:.4s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-box-seam icon-purple"></i>
                    Almacenes
                </div>
                <div class="card-body pt-0">
                    @php $almacenes = $sucursal->almacenes()->orderBy('nombre')->get(); @endphp
                    @forelse($almacenes as $almacen)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span><i class="bi bi-building me-2 text-muted"></i>{{ $almacen->nombre }}</span>
                        <a href="{{ route('almacenes.show', $almacen) }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
                    </div>
                    @empty
                    <div class="text-muted small">Sin almacenes registrados.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="premium-card" style="animation-delay:.45s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-cash-coin icon-purple"></i>
                    Cajas
                </div>
                <div class="card-body pt-0">
                    @php $cajas = $sucursal->cajas()->orderBy('nombre')->get(); @endphp
                    @forelse($cajas as $caja)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span>{{ $caja->nombre }}</span>
                            @if($caja->activa)
                                <span class="premium-badge active" style="font-size:.6rem;">Activa</span>
                            @endif
                        </div>
                        <a href="{{ route('cajas.show', $caja) }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
                    </div>
                    @empty
                    <div class="text-muted small">Sin cajas registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
