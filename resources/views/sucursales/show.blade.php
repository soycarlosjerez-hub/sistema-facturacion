@extends('layouts.app')

@section('title', $sucursal->nombre)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-building text-primary me-2"></i>{{ $sucursal->nombre }}
                @if($sucursal->es_matriz)
                    <span class="badge bg-primary rounded-pill px-3 ms-2" style="font-size:.6rem;"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                @endif
                @if($sucursal->activa)
                    <span class="badge bg-success rounded-pill px-3 ms-1" style="font-size:.6rem;"><i class="bi bi-check-circle me-1"></i>Activa</span>
                @else
                    <span class="badge bg-danger rounded-pill px-3 ms-1" style="font-size:.6rem;"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                @endif
            </h2>
            <p class="text-muted mb-0">{{ $sucursal->codigo }} &middot; {{ $sucursal->direccion ?? 'Sin dirección' }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('sucursales.edit')
            <a href="{{ route('sucursales.edit', $sucursal) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    {{-- Overview Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-graph-up text-primary fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Ventas del mes</small>
                            <h3 class="fw-bold mb-0">{{ $stats['ventas_mes'] }}</h3>
                            <small class="text-muted">{{ $stats['ingresos_mes'] > 0 ? ($systemMoneda ?? '$') . ' ' . number_format($stats['ingresos_mes'], 0) : 'Sin ingresos' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-building text-success fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Almacenes</small>
                            <h3 class="fw-bold mb-0">{{ $sucursal->almacenes_count ?? $stats['almacenes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-cash-stack text-warning fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Cajas activas</small>
                            <h3 class="fw-bold mb-0">{{ $stats['cajas_activas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-people text-info fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Empleados</small>
                            <h3 class="fw-bold mb-0">{{ $stats['empleados'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail + Recent Activity --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Información</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small fw-bold">Código</dt>
                        <dd class="col-sm-8">{{ $sucursal->codigo }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Nombre</dt>
                        <dd class="col-sm-8">{{ $sucursal->nombre }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Dirección</dt>
                        <dd class="col-sm-8">{{ $sucursal->direccion ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Teléfono</dt>
                        <dd class="col-sm-8">{{ $sucursal->telefono ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Email</dt>
                        <dd class="col-sm-8">{{ $sucursal->email ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">RNC</dt>
                        <dd class="col-sm-8">{{ $sucursal->rnc ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Creado</dt>
                        <dd class="col-sm-8">{{ $sucursal->created_at->format('d/m/Y h:i A') }}</dd>

                        <dt class="col-sm-4 text-muted small fw-bold">Actualizado</dt>
                        <dd class="col-sm-8">{{ $sucursal->updated_at->format('d/m/Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-info me-2"></i>Ventas recientes</h5>
                    <a href="{{ route('ventas.index', ['sucursal' => $sucursal->id]) }}" class="text-decoration-none small fw-bold">Ver todas</a>
                </div>
                <div class="card-body p-4 pt-0">
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
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-success me-2"></i>Almacenes</h5>
                </div>
                <div class="card-body p-4 pt-0">
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
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin text-warning me-2"></i>Cajas</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @php $cajas = $sucursal->cajas()->orderBy('nombre')->get(); @endphp
                    @forelse($cajas as $caja)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span>{{ $caja->nombre }}</span>
                            @if($caja->activa)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-2" style="font-size:.6rem;">Activa</span>
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
