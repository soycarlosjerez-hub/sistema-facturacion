@extends('layouts.app')
@section('title', 'Solicitudes Pendientes')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Solicitudes Pendientes</h2>
                    <p class="mb-0 opacity-75">Registros nuevos que esperan tu validación.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.dashboard') }}" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-warning">Pendientes</small>
                    <h3 class="ui-stat-value mb-0 text-warning">{{ $pendingCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-success">Aprobadas</small>
                    <h3 class="ui-stat-value mb-0 text-success">{{ $approvedCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block text-secondary">Total Instancias</small>
                    <h3 class="ui-stat-value mb-0">{{ $approvedCount + $pendingCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Negocio</th>
                        <th>Tipo</th>
                        <th>Solicitante</th>
                        <th>Email</th>
                        <th>RNC</th>
                        <th>Registrado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingInstances as $instance)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('owner.solicitudes.show', $instance->id) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td>
                            @if($instance->businessType)
                                <span class="badge bg-{{ $instance->businessType->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType->nombre }}</span>
                            @else
                                <span class="badge bg-light text-muted rounded-pill">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:rgba(245,158,11,.15);color:#f59e0b;font-size:13px;font-weight:600;">
                                    {{ $instance->owner_nombre ? strtoupper(substr($instance->owner_nombre, 0, 1)) : '?' }}
                                </div>
                                <div>
                                    <div class="small fw-semibold">{{ $instance->owner_nombre ?? '—' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $instance->telefono ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $instance->owner_email ?? '—' }}</td>
                        <td>{{ $instance->rnc ?? '—' }}</td>
                        <td>{{ $instance->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('owner.solicitudes.show', $instance->id) }}" class="ui-btn ui-btn-sm" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('owner.solicitudes.aprobar', $instance->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-success" title="Aprobar" onclick="return confirm('¿Aprobar la solicitud de {{ $instance->nombre }}?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('owner.solicitudes.rechazar', $instance->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="ui-btn ui-btn-sm ui-btn-danger" title="Rechazar" onclick="return confirm('¿Rechazar la solicitud de {{ $instance->nombre }}?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">No hay solicitudes pendientes</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 pt-3">
            {{ $pendingInstances->links() }}
        </div>
    </div>

</div>
</div>
@endsection
