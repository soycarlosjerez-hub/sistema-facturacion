@extends('layouts.app')
@section('title', $instance->nombre)
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-building text-primary me-2"></i>{{ $instance->nombre }}
                @if(!$instance->activo)
                    <span class="badge bg-secondary rounded-pill px-3 ms-2" style="font-size:.6rem;">Inactiva</span>
                @elseif($instance->bloqueado)
                    <span class="badge bg-danger rounded-pill px-3 ms-2" style="font-size:.6rem;"><i class="bi bi-lock-fill me-1"></i>Bloqueada</span>
                @elseif($instance->estaAlDia())
                    <span class="badge bg-success rounded-pill px-3 ms-2" style="font-size:.6rem;"><i class="bi bi-check-circle me-1"></i>Al d&iacute;a</span>
                @else
                    <span class="badge bg-warning text-dark rounded-pill px-3 ms-2" style="font-size:.6rem;"><i class="bi bi-exclamation-triangle me-1"></i>{{ $instance->mesesAtrasados() }} mes(es) atrasado</span>
                @endif
            </h2>
            <p class="text-muted mb-0">{{ $instance->businessType?->nombre ?? 'Sin tipo' }} &middot; {{ $instance->slug }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owner.instances.edit', $instance) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="{{ route('owner.instances.config', $instance) }}" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-gear me-2"></i>Configuraci&oacute;n
            </a>
            <a href="{{ route('owner.instances.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-people text-primary fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Usuarios</small>
                            <h3 class="fw-bold mb-0">{{ $instance->users->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-{{ $instance->businessType?->color ?? 'secondary' }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-tag fs-5 text-{{ $instance->businessType?->color ?? 'secondary' }}"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Tipo</small>
                            <h3 class="fw-bold mb-0" style="font-size:.9rem;">{{ $instance->businessType?->nombre ?? '—' }}</h3>
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
                            <i class="bi bi-currency-dollar text-success fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Costo Mensual</small>
                            <h3 class="fw-bold mb-0" style="font-size:.9rem;">{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</h3>
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
                            <i class="bi bi-calendar text-warning fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;">Vencimiento</small>
                            <h3 class="fw-bold mb-0" style="font-size:.85rem;">{{ $instance->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informaci&oacute;n General</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small fw-bold">Nombre</dt>
                        <dd class="col-sm-8">{{ $instance->nombre }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Slug</dt>
                        <dd class="col-sm-8"><code>{{ $instance->slug }}</code></dd>
                        <dt class="col-sm-4 text-muted small fw-bold">RNC</dt>
                        <dd class="col-sm-8">{{ $instance->rnc ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Email</dt>
                        <dd class="col-sm-8">{{ $instance->email ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Tel&eacute;fono</dt>
                        <dd class="col-sm-8">{{ $instance->telefono ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Direcci&oacute;n</dt>
                        <dd class="col-sm-8">{{ $instance->direccion ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Tipo</dt>
                        <dd class="col-sm-8"><span class="badge bg-{{ $instance->businessType?->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Due&ntilde;o</dt>
                        <dd class="col-sm-8">{{ $instance->owner?->name ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted small fw-bold">Creado</dt>
                        <dd class="col-sm-8">{{ $instance->created_at->format('d/m/Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock text-danger me-2"></i>Bloqueo de Instancia</h5>
                    @if($instance->bloqueado)
                        <span class="badge bg-danger rounded-pill">Bloqueada</span>
                    @else
                        <span class="badge bg-success rounded-pill">Normal</span>
                    @endif
                </div>
                <div class="card-body p-4 pt-0">
                    @if($instance->bloqueado)
                        <div class="alert alert-danger rounded-4 border-0">
                            <i class="bi bi-lock-fill me-2"></i>
                            <strong>Motivo:</strong> {{ $instance->motivo_bloqueo ?? 'Sin especificar' }}
                            <br><small>Bloqueado el {{ $instance->bloqueado_en?->format('d/m/Y h:i A') ?? '—' }}</small>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('owner.instances.toggle-block', $instance) }}" onsubmit="return confirm('{{ $instance->bloqueado ? 'Desbloquear' : 'Bloquear' }} esta instancia?')">
                        @csrf
                        <input type="hidden" name="bloqueado" value="{{ $instance->bloqueado ? '0' : '1' }}">
                        @if(!$instance->bloqueado)
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Motivo del Bloqueo <span class="text-danger">*</span></label>
                            <textarea name="motivo_bloqueo" class="form-control rounded-4" rows="2" placeholder="Ej: Mora en pago de mensualidad..." required></textarea>
                        </div>
                        @endif
                        <button type="submit" class="btn btn-{{ $instance->bloqueado ? 'success' : 'danger' }} rounded-pill w-100 fw-bold">
                            <i class="bi bi-{{ $instance->bloqueado ? 'unlock' : 'lock-fill' }} me-2"></i>
                            {{ $instance->bloqueado ? 'Desbloquear Instancia' : 'Bloquear Instancia' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin text-success me-2"></i>Historial de Pagos</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.instances.pagos', $instance) }}" class="btn btn-sm btn-outline-info rounded-pill">Ver historial completo</a>
                <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-sm btn-success rounded-pill fw-bold">
                    <i class="bi bi-plus-lg me-1"></i>Registrar Pago
                </a>
            </div>
        </div>
        <div class="card-body p-4 pt-0">
            @php
                $ultimoPago = $instance->ultimoPago()->first();
            @endphp
            @if($ultimoPago)
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="p-3 bg-success bg-opacity-10 rounded-4 text-center">
                        <small class="text-success d-block fw-bold text-uppercase" style="font-size:.6rem;">Último Pago</small>
                        <span class="fw-bold">{{ $ultimoPago->mes_pagado->isoFormat('MMM YYYY') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-info bg-opacity-10 rounded-4 text-center">
                        <small class="text-info d-block fw-bold text-uppercase" style="font-size:.6rem;">Monto</small>
                        <span class="fw-bold">{{ $systemMoneda ?? 'RD$' }} {{ number_format($ultimoPago->monto, 2) }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-{{ $instance->estaAlDia() ? 'success' : 'warning' }} bg-opacity-10 rounded-4 text-center">
                        <small class="text-{{ $instance->estaAlDia() ? 'success' : 'warning' }} d-block fw-bold text-uppercase" style="font-size:.6rem;">Estado</small>
                        <span class="fw-bold">{{ $instance->estaAlDia() ? 'Al d&iacute;a' : $instance->mesesAtrasados() . ' mes(es) atrasado' }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if($pagosRecientes->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mes Pagado</th>
                            <th>Monto</th>
                            <th>M&eacute;todo</th>
                            <th>Fecha de Pago</th>
                            <th>Registrado por</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagosRecientes as $pago)
                        <tr>
                            <td class="fw-bold">{{ $pago->mes_pagado->isoFormat('MMMM YYYY') }}</td>
                            <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->metodo_pago ?? '—' }}</td>
                            <td>{{ $pago->fecha_pago->format('d/m/Y h:i A') }}</td>
                            <td>{{ $pago->registradoPor?->name ?? '—' }}</td>
                            <td><small class="text-muted">{{ $pago->notas ?? '—' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-0">No hay pagos registrados para esta instancia.</p>
                <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-success rounded-pill mt-2 btn-sm fw-bold">
                    <i class="bi bi-plus-lg me-1"></i>Registrar Primer Pago
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Usuarios Asignados</h5>
            <div class="d-flex gap-2 align-items-center">
                <small class="text-muted">{{ $instance->users->count() }} usuario(s)</small>
                <a href="{{ route('owner.instances.users.create', $instance) }}" class="btn btn-sm btn-success rounded-pill fw-bold">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body p-4 pt-0">
            @forelse($instance->users as $user)
            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <span class="fw-bold text-primary" style="font-size:.85rem;">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <span class="fw-bold">{{ $user->name }}</span>
                        <small class="text-muted d-block">{{ $user->email }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @foreach($user->roles as $role)
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill me-1">{{ $role->name }}</span>
                    @endforeach
                    <a href="{{ route('owner.instances.users.edit', [$instance, $user]) }}" class="btn btn-sm btn-outline-warning rounded-pill ms-2" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('owner.instances.users.destroy', [$instance, $user]) }}" onsubmit="return confirm('&iquest;Eliminar a {{ $user->name }} de {{ $instance->nombre }}? Esta acci&oacute;n no se puede deshacer.')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-0">No hay usuarios asignados a esta instancia.</p>
                <a href="{{ route('owner.instances.users.create', $instance) }}" class="btn btn-success rounded-pill mt-2 btn-sm fw-bold">
                    <i class="bi bi-plus-lg me-1"></i>Crear Primer Usuario
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
