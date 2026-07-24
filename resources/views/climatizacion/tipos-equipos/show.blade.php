@extends('layouts.app')

@section('title', $tipo->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-detail-value code {
    background: rgba(6,182,212,.1);
    color: #22d3ee;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">
                        {{ $tipo->nombre }}
                    </h4>
                    <div class="ui-header-meta">
                        <span>Detalles del tipo de equipo</span>
                        <span class="divider">&middot;</span>
                        @if($tipo->activo)
                            <span class="ui-badge-success" style="font-size:.7rem;"><i class="bi bi-check-circle me-1"></i>Activo</span>
                        @else
                            <span class="ui-badge-neutral" style="font-size:.7rem;"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex gap-2">
                    <a href="{{ route('climatizacion.tipos-equipos.edit', $tipo) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </a>
                    <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2" style="color:#06b6d4;"></i>Información General</h5>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Nombre</span>
                        <span class="ui-detail-value">{{ $tipo->nombre }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Slug</span>
                        <span class="ui-detail-value"><code class="px-2 py-1 rounded-2" style="background:#f1f5f9;font-size:.85rem;">{{ $tipo->slug }}</code></span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Categoría</span>
                        <span class="ui-detail-value">{{ ucfirst($tipo->categoria) }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Icono</span>
                        <span class="ui-detail-value">
                            @if($tipo->icono)
                                <i class="bi {{ $tipo->icono }} me-2" style="color:#06b6d4;"></i><code class="px-2 py-1 rounded-2" style="background:#f1f5f9;font-size:.85rem;">{{ $tipo->icono }}</code>
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Orden</span>
                        <span class="ui-detail-value">{{ $tipo->orden }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            @if($tipo->activo)
                                <span class="ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                            @else
                                <span class="ui-badge-neutral"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                            @endif
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado</span>
                        <span class="ui-detail-value">{{ $tipo->created_at ? $tipo->created_at->format('d/m/Y h:i A') : '-' }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Actualizado</span>
                        <span class="ui-detail-value">{{ $tipo->updated_at ? $tipo->updated_at->format('d/m/Y h:i A') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center py-5">
                    @if($tipo->icono)
                        <i class="bi {{ $tipo->icono }}" style="font-size:4rem;color:#06b6d4;opacity:.6;"></i>
                    @else
                        <i class="bi bi-cpu" style="font-size:4rem;color:#06b6d4;opacity:.4;"></i>
                    @endif
                    <h5 class="fw-bold mt-3 mb-1">{{ $tipo->nombre }}</h5>
                    <p class="text-muted small mb-0">{{ ucfirst($tipo->categoria) }}</p>
                    <span class="ui-badge-primary mt-2 d-inline-block">
                        <i class="bi bi-hash me-1"></i>Orden {{ $tipo->orden }}
                    </span>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('climatizacion.tipos-equipos.edit', $tipo) }}" class="ui-btn ui-btn-solid flex-fill rounded-pill">
                    <i class="bi bi-pencil me-2"></i>Editar
                </a>
                <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="ui-btn ui-btn-ghost flex-fill rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
