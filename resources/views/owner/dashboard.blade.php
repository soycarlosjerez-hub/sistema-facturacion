@extends('layouts.app')
@section('title', 'Panel de Control - Dueño del Sistema')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Panel de Control</h2>
                    <p class="mb-0 opacity-75">Resumen general de todas las instancias de negocio.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.create') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Nueva Instancia
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Total</small>
                    <h3 class="ui-stat-value mb-0">{{ $totalInstancias }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-success">Al D&iacute;a</small>
                    <h3 class="ui-stat-value mb-0 text-success">{{ $instancias->filter(fn($i) => $i->activo && !$i->bloqueado && $i->estaAlDia())->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-warning">Atrasadas</small>
                    <h3 class="ui-stat-value mb-0 text-warning">{{ $instanciasConAtraso->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.25s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-danger">Bloqueadas</small>
                    <h3 class="ui-stat-value mb-0 text-danger">{{ $bloqueadas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.3s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-info">Por Vencer</small>
                    <h3 class="ui-stat-value mb-0 text-info">{{ $porVencer }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="ui-stat h-100" style="--delay:.35s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-secondary">Vencidas</small>
                    <h3 class="ui-stat-value mb-0 text-secondary">{{ $vencidas }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart text-primary me-2"></i>Distribuci&oacute;n por Tipo</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($instanciasPorTipo as $tipo => $count)
                    @php $pct = $totalInstancias > 0 ? round($count / $totalInstancias * 100) : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium small">{{ $tipo }}</span>
                            <span class="ui-badge ui-badge-primary rounded-pill">{{ $count }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar" role="progressbar" style="width:{{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2 mb-0">Sin instancias</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Pr&oacute;ximos Vencimientos</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($proximosVencimientos as $instance)
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                        <div>
                            <a href="{{ route('owner.instances.show', $instance) }}" class="fw-bold text-decoration-none small">{{ $instance->nombre }}</a>
                            <small class="text-muted d-block">{{ $instance->businessType?->nombre }}</small>
                        </div>
                        <span class="ui-badge ui-badge-warning rounded-pill">{{ $instance->fecha_vencimiento->diffForHumans() }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-check fs-1"></i>
                        <p class="mt-2 mb-0">Ninguna instancia por vencer</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin text-success me-2"></i>Resumen Financiero</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;">Ingresos Esperados / Mes</small>
                        <h4 class="fw-bold mb-0">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ingresosEsperados, 2) }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;">Cobrado Este Mes</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ingresosRealesMes, 2) }}</h4>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">Total tipos</span>
                        <span class="ui-badge ui-badge-primary rounded-pill">{{ $totalTipos }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold small">Total usuarios</span>
                        <span class="ui-badge ui-badge-info rounded-pill">{{ $totalUsuarios }}</span>
                    </div>
                    <hr>
                    <a href="{{ route('owner.business-types.index') }}" class="ui-btn ui-btn-ghost rounded-pill w-100 btn-sm">
                        <i class="bi bi-gear me-2"></i>Gestionar Tipos
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($instanciasConAtraso->isNotEmpty())
    <div class="ui-card mb-4" style="--delay:.25s;border-left: 4px solid #f59e0b !important;">
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-clock-history me-2"></i>Instancias con Atraso en Pagos</h5>
            <span class="ui-badge ui-badge-warning rounded-pill">{{ $instanciasConAtraso->count() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Instancia</th>
                        <th>Tipo</th>
                        <th>Costo Mensual</th>
                        <th>Meses Atrasados</th>
                        <th>Deuda Estimada</th>
                        <th>&Uacute;ltimo Pago</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instanciasConAtraso as $instance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td><span class="ui-badge ui-badge-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</td>
                        <td><span class="ui-badge ui-badge-danger rounded-pill">{{ $instance->mesesAtrasados() }} mes(es)</span></td>
                        <td class="fw-bold text-danger">{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->deudaEstimada(), 2) }}</td>
                        <td>
                            @php $ultimo = $instance->ultimoPago()->first(); @endphp
                            @if($ultimo)
                                {{ $ultimo->mes_pagado->isoFormat('MMM YYYY') }}
                            @else
                                <span class="text-muted">Sin pagos</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="ui-btn ui-btn-solid btn-sm rounded-pill me-1" title="Registrar Pago" style="background:#10b981;border-color:#10b981;color:#fff;">
                                <i class="bi bi-cash-coin me-1"></i> Pago
                            </a>
                            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill" title="Detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-building text-primary me-2"></i>Todas las Instancias</h5>
            <a href="{{ route('owner.instances.index') }}" class="text-decoration-none small fw-bold">Ver todas</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Tipo</th>
                        <th class="text-center">Estado Pago</th>
                        <th class="text-center">Bloqueo</th>
                        <th>Vencimiento</th>
                        <th>Costo Mensual</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instancias->take(8) as $instance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td><span class="ui-badge ui-badge-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td class="text-center">
                            @if(!$instance->activo)
                                <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                            @elseif($instance->bloqueado)
                                <span class="ui-badge ui-badge-danger rounded-pill">Bloqueada</span>
                            @elseif($instance->estaAlDia())
                                <span class="ui-badge ui-badge-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Al d&iacute;a</span>
                            @else
                                <span class="ui-badge ui-badge-warning rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>{{ $instance->mesesAtrasados() }} mes(es)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($instance->bloqueado)
                                <span class="ui-badge ui-badge-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>Bloqueado</span>
                            @else
                                <span class="ui-badge ui-badge-success rounded-pill"><i class="bi bi-unlock me-1"></i>Normal</span>
                            @endif
                        </td>
                        <td>
                            @if($instance->fecha_vencimiento)
                                {{ $instance->fecha_vencimiento->format('d/m/Y') }}
                                @if($instance->activo && $instance->fecha_vencimiento < now())
                                    <span class="text-danger fw-bold small">(vencida)</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill me-1" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('owner.instances.edit', $instance) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('owner.instances.config', $instance) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill me-1" title="Config"><i class="bi bi-gear"></i></a>
                            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill" title="Pago"><i class="bi bi-cash-coin"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay instancias registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
