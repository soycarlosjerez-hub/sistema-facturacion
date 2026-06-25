@extends('layouts.app')
@section('title', 'Instancias de Negocio')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem;">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Instancias de Negocio</h2>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de todas las instancias multi-tenant.</p>
                </div>
            </div>
            <a href="{{ route('owner.instances.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                <i class="bi bi-plus-lg me-2"></i>Nueva Instancia
            </a>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('owner.instances.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-white" placeholder="Buscar por nombre, slug, RNC..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="business_type" class="form-select border-0 bg-white">
                        <option value="">Todos los tipos</option>
                        @foreach($businessTypes as $type)
                            <option value="{{ $type->id }}" {{ request('business_type') == $type->id ? 'selected' : '' }}>{{ $type->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="status" class="form-select border-0 bg-white">
                        <option value="">Todos los estados</option>
                        <option value="al-dia" {{ request('status') === 'al-dia' ? 'selected' : '' }}>Al d&iacute;a</option>
                        <option value="atrasado" {{ request('status') === 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                        <option value="bloqueado" {{ request('status') === 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                        <option value="vencido" {{ request('status') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                        <option value="inactivo" {{ request('status') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('owner.instances.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Tipo</th>
                        <th class="text-center">Estado Pago</th>
                        <th class="text-center">Bloqueo</th>
                        <th>Costo Mensual</th>
                        <th>Vencimiento</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instances as $instance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td><span class="badge bg-{{ $instance->businessType?->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td class="text-center">
                            @if(!$instance->activo)
                                <span class="badge bg-secondary rounded-pill px-2">Inactiva</span>
                            @elseif($instance->bloqueado)
                                <span class="badge bg-danger rounded-pill px-2">Bloqueada</span>
                            @elseif($instance->estaAlDia())
                                <span class="badge bg-success rounded-pill px-2"><i class="bi bi-check-circle me-1"></i>Al d&iacute;a</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-2"><i class="bi bi-exclamation-triangle me-1"></i>{{ $instance->mesesAtrasados() }} mes(es)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($instance->bloqueado)
                                <span class="badge bg-danger rounded-pill px-2"><i class="bi bi-lock-fill me-1"></i>Bloqueado</span>
                            @else
                                <span class="badge bg-success rounded-pill px-2"><i class="bi bi-unlock me-1"></i>Normal</span>
                            @endif
                        </td>
                        <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</td>
                        <td>
                            @if($instance->fecha_vencimiento)
                                {{ $instance->fecha_vencimiento->format('d/m/Y') }}
                                @if($instance->activo && $instance->fecha_vencimiento < now())
                                    <span class="text-danger fw-bold small">(vencida)</span>
                                @elseif($instance->activo && $instance->fecha_vencimiento->diffInDays(now()) <= 30)
                                    <span class="text-warning fw-bold small">({{ $instance->fecha_vencimiento->diffForHumans() }})</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver detalles"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('owner.instances.edit', $instance) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('owner.instances.config', $instance) }}" class="btn btn-sm btn-outline-warning rounded-pill me-1" title="Configuraci&oacute;n"><i class="bi bi-gear"></i></a>
                            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-sm btn-outline-success rounded-pill me-1" title="Registrar Pago"><i class="bi bi-cash-coin"></i></a>
                            @if($instance->activo)
                            <form action="{{ route('owner.instances.destroy', $instance) }}" method="POST" class="d-inline" onsubmit="return confirm('Desactivar la instancia {{ $instance->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete" title="Desactivar"><i class="bi bi-power"></i></button>
                            </form>
                            @endif
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
        @if($instances->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            {{ $instances->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
