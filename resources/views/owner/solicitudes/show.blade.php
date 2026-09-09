@extends('layouts.app')
@section('title', 'Detalle Solicitud')

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
                    <h2 class="fw-bold mb-1">Detalle de Solicitud</h2>
                    <p class="mb-0 opacity-75">Revisión completa de la solicitud registrada.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.solicitudes.index') }}" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-arrow-left me-2"></i>Volver a Solicitudes
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-warning"></i>Datos del Negocio</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Nombre del Negocio</label>
                            <div class="fw-bold">{{ $instance->nombre }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Tipo de Negocio</label>
                            <div>
                                @if($instance->businessType)
                                    <span class="badge bg-{{ $instance->businessType->color ?? 'secondary' }} bg-opacity-10 text-{{ $instance->businessType->color ?? 'secondary' }} rounded-pill px-3 py-2">{{ $instance->businessType->nombre }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">RNC</label>
                            <div class="fw-bold">{{ $instance->rnc }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Teléfono</label>
                            <div class="fw-bold">{{ $instance->telefono }}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small fw-semibold d-block">Dirección</label>
                            <div class="fw-bold">{{ $instance->direccion }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person me-2 text-primary"></i>Datos del Solicitante</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Nombre</label>
                            <div class="fw-bold">{{ $instance->owner_nombre ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Email</label>
                            <div class="fw-bold">{{ $instance->owner_email ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Registrado</label>
                            <div class="fw-bold">{{ $instance->created_at->format('d/m/Y H:i') }} hrs</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold d-block">Última Actualización</label>
                            <div class="fw-bold">{{ $instance->updated_at->format('d/m/Y H:i') }} hrs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-gear me-2 text-warning"></i>Acciones</h5>

                    <div class="mb-3">
                        <a href="{{ route('owner.instances.show', $instance->id) }}" class="ui-btn ui-btn-ghost w-100 mb-2">
                            <i class="bi bi-eye me-2"></i>Ver Instancia Completa
                        </a>
                        <a href="{{ route('owner.instances.config', $instance->id) }}" class="ui-btn ui-btn-ghost w-100 mb-2">
                            <i class="bi bi-sliders me-2"></i>Configuración
                        </a>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3 text-success">Aprobar Solicitud</h6>
                    <p class="text-muted small mb-3">Se activará el período de prueba (15 días) y se notificará al usuario.</p>
                    <form action="{{ route('owner.solicitudes.aprobar', $instance->id) }}" method="POST" onsubmit="return confirm('¿Aprobar la solicitud de {{ $instance->nombre }}?')">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn-success w-100 mb-3">
                            <i class="bi bi-check-lg me-2"></i>Aprobar
                        </button>
                    </form>

                    <hr>

                    <h6 class="fw-bold mb-3 text-danger">Rechazar Solicitud</h6>
                    <form action="{{ route('owner.solicitudes.rechazar', $instance->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold d-block">Motivo del Rechazo</label>
                            <textarea name="motivo" class="ui-input bg-white form-control" rows="3" required placeholder="Indica el motivo..."></textarea>
                        </div>
                        <button type="submit" class="ui-btn ui-btn-danger w-100" onclick="return confirm('¿Rechazar esta solicitud? Se enviará una notificación al solicitante.')">
                            <i class="bi bi-x-lg me-2"></i>Rechazar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
