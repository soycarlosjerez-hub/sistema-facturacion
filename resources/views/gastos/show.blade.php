@extends('layouts.app')

@section('title', 'Detalle del Gasto')

@push('styles')
@include('partials.premium-ui')
<style>
/* Gastos show-specific styles */
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-eye"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Detalle del Gasto</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-receipt me-1"></i>
                        {{ $gasto->descripcion }}
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('gastos.edit')
                <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(245,158,11,.2);border:1.5px solid rgba(245,158,11,.35);color:#fff;">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('gastos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="premium-card" style="animation-delay:.1s;">
                <div class="card-accent green"></div>
                <div class="premium-card-title">
                    <i class="bi bi-info-circle icon-green"></i>
                    Información del Gasto
                </div>
                <div class="premium-card-subtitle">Datos completos del registro</div>
                <div class="card-body">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Descripción</div>
                        <div class="premium-detail-value fw-semibold">{{ $gasto->descripcion }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Monto</div>
                        <div class="premium-detail-value fw-bold" style="color:#059669;font-size:1.2rem;">RD$ {{ number_format($gasto->monto, 2) }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Categoría</div>
                        <div class="premium-detail-value">
                            @if($gasto->categoria)
                                <span class="badge rounded-pill" style="background:rgba(16,185,129,.1);color:#059669;font-weight:600;">{{ \App\Models\Gasto::categorias()[$gasto->categoria] ?? $gasto->categoria }}</span>
                            @else
                                <span class="text-muted">Sin categoría</span>
                            @endif
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Método de Pago</div>
                        <div class="premium-detail-value">{{ $gasto->metodo_pago ? ucfirst($gasto->metodo_pago) : '—' }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">N° Comprobante</div>
                        <div class="premium-detail-value">
                            @if($gasto->comprobante)
                                <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">{{ $gasto->comprobante }}</span>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Fecha del Gasto</div>
                        <div class="premium-detail-value">{{ $gasto->fecha_gasto->format('d/m/Y') }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Notas</div>
                        <div class="premium-detail-value">{{ $gasto->notas ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="premium-card" style="animation-delay:.15s;">
                <div class="card-accent amber"></div>
                <div class="premium-card-title">
                    <i class="bi bi-person-badge icon-amber"></i>
                    Registrado por
                </div>
                <div class="premium-card-subtitle">Información del usuario que creó el gasto</div>
                <div class="card-body text-center">
                    <div class="premium-user-avatar avatar-amber mx-auto mb-3">
                        <i class="bi bi-person-circle fs-2" style="color:#d97706;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ $gasto->user?->name ?? '—' }}</h6>
                    <small class="text-muted">{{ $gasto->created_at->format('d/m/Y h:i A') }}</small>
                    @if($gasto->caja)
                        <hr class="my-3">
                        <div class="text-start">
                            <small class="text-muted d-block">Caja: <span class="fw-semibold" style="color:#059669;">{{ $gasto->caja->nombre }}</span></small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
