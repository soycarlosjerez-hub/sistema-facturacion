@extends('layouts.app')
@section('title', 'Tipos de Negocio')

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
                    <h2 class="fw-bold mb-1">Tipos de Negocio</h2>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de tipos de negocio y sus m&oacute;dulos disponibles.</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.business-types.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo
                </a>
                <a href="{{ route('owner.dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($businessTypes as $type)
        <div class="col-md-6 col-lg-4">
            <div class="premium-card h-100">
                <div class="card-accent purple"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:52px;height:52px;background-color:var(--bs-{{ $type->color ?? 'secondary' }});">
                            <i class="{{ $type->icon ?? 'bi-grid' }} fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{ $type->nombre }}
                                @if(!$type->activo)
                                    <span class="badge bg-secondary rounded-pill ms-1" style="font-size:.55rem;">Inactivo</span>
                                @endif
                            </h5>
                            <small class="text-muted">Slug: {{ $type->slug }}</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">{{ $type->descripcion ?? 'Sin descripción' }}</p>
                    <div class="mb-3">
                        <small class="fw-bold text-muted d-block mb-2">M&oacute;dulos visibles ({{ $type->modules->where('visible', true)->count() }}/{{ $type->modules->count() }})</small>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($type->modules as $module)
                                <span class="badge rounded-pill px-2 py-1 {{ $module->visible ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}" style="font-size:.65rem;">
                                    {{ $module->visible ? '' : '' }}{{ $module->modulo_key }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('owner.business-types.edit', $type) }}" class="btn btn-outline-primary rounded-pill flex-grow-1">
                            <i class="bi bi-pencil me-2"></i>Editar M&oacute;dulos
                        </a>
                        <form method="POST" action="{{ route('owner.business-types.destroy', $type) }}" onsubmit="return confirm('&iquest;Eliminar el tipo de negocio &quot;{{ $type->nombre }}&quot;? Esta acci&oacute;n no se puede deshacer.')" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="premium-btn-delete" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="premium-card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">No hay tipos de negocio registrados.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
</div>
@endsection
