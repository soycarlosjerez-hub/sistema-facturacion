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
                    <h2 class="fw-bold mb-1">Planes de Suscripci&oacute;n</h2>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de los planes mensuales vendibles</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.plans.create') }}" class="ui-btn ui-btn-solid">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Plan
                </a>
                <a href="{{ route('owner.dashboard') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>
        </div>
    </div>

    @php
        $stats = [
            'planes'   => $planes->count(),
            'activos'  => $planes->where('activo', true)->count(),
            'recomendados' => $planes->where('recomendado', true)->count(),
            'instancias' => $planes->sum('business_instances_count'),
        ];
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#8b5cf6">
                        <i class="bi bi-card-checklist fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $stats['planes'] }}</div>
                        <small class="text-muted">Planes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#22c55e"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#22c55e">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $stats['activos'] }}</div>
                        <small class="text-muted">Activos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#f59e0b">
                        <i class="bi bi-star fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $stats['recomendados'] }}</div>
                        <small class="text-muted">Recomendados</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.4s">
                <div class="ui-card-accent" style="background:#0ea5e9"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#0ea5e9">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $stats['instancias'] }}</div>
                        <small class="text-muted">Instancias</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($planes as $plan)
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.{{ min(5, $loop->iteration) }}s">
                @if($plan->recomendado)
                    <div class="ui-card-accent" style="background:#8b5cf6"></div>
                @else
                    <div class="ui-card-accent" style="background:#3b82f6"></div>
                @endif
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:52px;height:52px;background:{{ $plan->recomendado ? '#8b5cf6' : '#3b82f6' }};">
                            <i class="bi bi-{{ $plan->recomendado ? 'star-fill' : 'card-checklist' }} fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-0">
                                {{ $plan->nombre }}
                                @if($plan->recomendado)
                                    <span class="ui-badge ui-badge-primary rounded-pill ms-1" style="font-size:.55rem;">Recomendado</span>
                                @endif
                                @if(!$plan->activo)
                                    <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">Inactivo</span>
                                @endif
                            </h5>
                            <small class="text-muted">{{ $plan->slug }}</small>
                        </div>
                    </div>

                    @if($plan->descripcion)
                        <p class="text-muted small mb-3">{{ $plan->descripcion }}</p>
                    @endif

                    <div class="mb-3">
                        <span class="fs-3 fw-bold" style="color:var(--accent);">RD$ {{ number_format($plan->precio_mensual, 2) }}</span>
                        <span class="text-muted small">/mes</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-primary">{{ $plan->max_usuarios === null ? '&infin;' : $plan->max_usuarios }}</div>
                                <small class="text-muted">Usuarios</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-success">{{ $plan->max_sucursales === null ? '&infin;' : $plan->max_sucursales }}</div>
                                <small class="text-muted">Sucursales</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-info">{{ $plan->business_instances_count }}</div>
                                <small class="text-muted">Instancias</small>
                            </div>
                        </div>
                    </div>

                    @if(!empty($plan->features))
                    <div class="mb-3">
                        <small class="fw-bold text-muted d-block mb-2">Features ({{ count($plan->features) }})</small>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(array_slice($plan->features, 0, 4) as $feature)
                                <span class="ui-badge ui-badge-neutral rounded-pill px-2 py-1" style="font-size:.65rem;">
                                    {{ $feature }}
                                </span>
                            @endforeach
                            @if(count($plan->features) > 4)
                                <span class="ui-badge ui-badge-neutral rounded-pill px-2 py-1" style="font-size:.65rem;">
                                    +{{ count($plan->features) - 4 }} m&aacute;s
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('owner.plans.edit', $plan) }}" class="ui-btn ui-btn-ghost rounded-pill flex-grow-1">
                            <i class="bi bi-pencil me-2"></i>Editar
                        </a>
                        <form method="POST" action="{{ route('owner.plans.destroy', $plan) }}" onsubmit="return UI.confirm.delete('&iquest;Eliminar el plan "{{ $plan->nombre }}"?')" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">No hay planes creados. <a href="{{ route('owner.plans.create') }}">Crea el primero</a>.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
</div>
@endsection