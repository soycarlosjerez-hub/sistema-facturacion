@extends('layouts.app')
@section('title', 'Planes')

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
                    <i class="bi bi-card-checklist"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Planes de Suscripci&oacute;n</h3>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de los planes mensuales vendibles</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.plans.create') }}" class="ui-btn ui-btn-solid" style="background:#8b5cf6;border-color:#8b5cf6;color:#fff;">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Plan
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($planes as $plan)
        <div class="col-md-6 col-xl-3">
            <div class="ui-card h-100" style="--delay:.1s">
                @if($plan->recomendado)
                    <div class="ui-card-accent" style="background:#8b5cf6"></div>
                    <span class="badge position-absolute top-0 start-50 translate-middle bg-primary rounded-pill px-3">Recomendado</span>
                @else
                    <div class="ui-card-accent" style="background:#3b82f6"></div>
                @endif
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $plan->nombre }}</h5>
                            <small class="text-muted">{{ $plan->slug }}</small>
                        </div>
                        <span class="ui-badge ui-badge-{{ $plan->activo ? 'success' : 'neutral' }} rounded-pill text-uppercase" style="font-size:.6rem;">
                            {{ $plan->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="fs-4 fw-bold" style="color:var(--accent);">RD$ {{ number_format($plan->precio_mensual, 2) }}</span>
                        <span class="text-muted small">/mes</span>
                    </div>
                    <dl class="small mb-3">
                        <dt class="text-muted">Usuarios</dt>
                        <dd class="mb-1">{{ $plan->max_usuarios === null ? 'Ilimitados' : $plan->max_usuarios }}</dd>
                        <dt class="text-muted">Sucursales</dt>
                        <dd class="mb-1">{{ $plan->max_sucursales === null ? 'Ilimitadas' : $plan->max_sucursales }}</dd>
                        <dt class="text-muted">Instancias activas</dt>
                        <dd class="mb-0">{{ $plan->business_instances_count }}</dd>
                    </dl>
                    <div class="d-flex gap-2">
                        <a href="{{ route('owner.plans.edit', $plan) }}" class="ui-btn ui-btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <form method="POST" action="{{ route('owner.plans.destroy', $plan) }}" onsubmit="return UI.confirm.delete('¿Eliminar el plan {{ $plan->nombre }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="ui-btn ui-btn-danger btn-sm">
                                <i class="bi bi-trash me-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ui-card text-center p-5">
                <p class="text-muted mb-0">No hay planes creados. <a href="{{ route('owner.plans.create') }}">Crea el primero</a>.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
</div>
@endsection
