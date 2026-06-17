@extends('layouts.app')
@section('title', 'Panel de Control - Dueño del Sistema')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i>Panel de Control</h2>
            <p class="text-muted mb-0">Resumen general de todas las instancias de negocio.</p>
        </div>
        <a href="{{ route('owner.instances.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Nueva Instancia
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Total</small>
                    <h3 class="fw-bold mb-0 display-6">{{ $totalInstancias }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <small class="text-success d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Al D&iacute;a</small>
                    <h3 class="fw-bold mb-0 text-success display-6">{{ $instancias->filter(fn($i) => $i->activo && !$i->bloqueado && $i->estaAlDia())->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-warning bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <small class="text-warning d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Atrasadas</small>
                    <h3 class="fw-bold mb-0 text-warning display-6">{{ $instanciasConAtraso->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <small class="text-danger d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Bloqueadas</small>
                    <h3 class="fw-bold mb-0 text-danger display-6">{{ $bloqueadas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-info bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <small class="text-info d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Por Vencer</small>
                    <h3 class="fw-bold mb-0 text-info display-6">{{ $porVencer }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-secondary bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <small class="text-secondary d-block fw-bold text-uppercase" style="font-size:.6rem;letter-spacing:.5px;">Vencidas</small>
                    <h3 class="fw-bold mb-0 text-secondary display-6">{{ $vencidas }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart text-primary me-2"></i>Distribuci&oacute;n por Tipo</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @forelse($instanciasPorTipo as $tipo => $count)
                    @php $pct = $totalInstancias > 0 ? round($count / $totalInstancias * 100) : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-medium small">{{ $tipo }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
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
            <div class="card border-0 shadow-sm rounded-4 h-100">
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
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $instance->fecha_vencimiento->diffForHumans() }}</span>
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
            <div class="card border-0 shadow-sm rounded-4 h-100">
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
                        <span class="badge bg-primary rounded-pill">{{ $totalTipos }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold small">Total usuarios</span>
                        <span class="badge bg-info rounded-pill">{{ $totalUsuarios }}</span>
                    </div>
                    <hr>
                    <a href="{{ route('owner.business-types.index') }}" class="btn btn-outline-primary rounded-pill w-100 btn-sm">
                        <i class="bi bi-gear me-2"></i>Gestionar Tipos
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($instanciasConAtraso->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-warning border-4">
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-clock-history me-2"></i>Instancias con Atraso en Pagos</h5>
            <span class="badge bg-warning rounded-pill">{{ $instanciasConAtraso->count() }}</span>
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
                        <th>Último Pago</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instanciasConAtraso as $instance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td><span class="badge bg-{{ $instance->businessType?->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</td>
                        <td><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ $instance->mesesAtrasados() }} mes(es)</span></td>
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
                            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-sm btn-success rounded-pill me-1" title="Registrar Pago">
                                <i class="bi bi-cash-coin"></i> Pago
                            </a>
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-sm btn-outline-info rounded-pill" title="Detalles">
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

    <div class="card border-0 shadow-sm rounded-4">
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
                        <td><span class="badge bg-{{ $instance->businessType?->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td class="text-center">
                            @if(!$instance->activo)
                                <span class="badge bg-secondary rounded-pill">Inactiva</span>
                            @elseif($instance->bloqueado)
                                <span class="badge bg-danger rounded-pill">Bloqueada</span>
                            @elseif($instance->estaAlDia())
                                <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Al d&iacute;a</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>{{ $instance->mesesAtrasados() }} mes(es)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($instance->bloqueado)
                                <span class="badge bg-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>Bloqueado</span>
                            @else
                                <span class="badge bg-success rounded-pill"><i class="bi bi-unlock me-1"></i>Normal</span>
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
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('owner.instances.edit', $instance) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('owner.instances.config', $instance) }}" class="btn btn-sm btn-outline-warning rounded-pill me-1" title="Config"><i class="bi bi-gear"></i></a>
                            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-sm btn-outline-success rounded-pill" title="Pago"><i class="bi bi-cash-coin"></i></a>
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
@endsection
