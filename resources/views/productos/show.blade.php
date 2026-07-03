@extends('layouts.app')

@section('title', $producto->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
/* Productos show-specific styles */
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
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">{{ $producto->nombre }}</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-upc-scan me-1"></i>{{ $producto->codigo_barras ?? 'Sin código' }}
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('productos.edit')
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                @endcan
                <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="premium-card" style="animation-delay:.1s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-4 text-center">
                    <img src="{{ $producto->imagen_url }}" class="rounded-3 shadow-sm img-fluid mb-3" style="max-height:280px;object-fit:cover;background:#f1f5f9;" alt="{{ $producto->nombre }}">
                    <h4 class="fw-bold mb-1">{{ $producto->nombre }}</h4>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-upc-scan"></i> {{ $producto->codigo_barras ?? 'Sin código' }}
                    </p>
                    <div class="mb-3">
                        <span class="badge bg-{{ $producto->color_badge_activo }} rounded-pill px-3 py-2">
                            <i class="bi bi-{{ $producto->activo ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                            {{ $producto->activo_label }}
                        </span>
                    </div>
                    @if($producto->estado_stock === 'critical')
                        <span class="badge bg-danger rounded-pill px-3 py-2">Stock Crítico: {{ $producto->stock }}</span>
                    @elseif($producto->estado_stock === 'low')
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Stock Bajo: {{ $producto->stock }}</span>
                    @else
                        <span class="badge rounded-pill px-3 py-2" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">Stock Normal: {{ $producto->stock }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="premium-card mb-4" style="animation-delay:.15s;">
                <div class="card-accent blue"></div>
                <div class="premium-card-title">
                    <i class="bi bi-info-circle icon-blue"></i>
                    Información General
                </div>
                <div class="premium-card-subtitle">Datos completos del producto</div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Precio de Venta</small>
                                <span class="fs-4 fw-bold" style="color:#4f46e5;">RD$ {{ number_format($producto->precio, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Precio de Compra</small>
                                <span class="fs-5 fw-bold">RD$ {{ number_format($producto->precio_compra ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Ganancia por unidad</small>
                                <span class="fs-5 fw-bold {{ $producto->ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $producto->ganancia >= 0 ? '+' : '' }}RD$ {{ number_format($producto->ganancia, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Margen</small>
                                <span class="fs-5 fw-bold" style="color:#06b6d4;">{{ number_format($producto->margen_porcentaje, 2) }}%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">ITBIS</small>
                                <span class="fw-bold">{{ number_format($producto->itbis_porcentaje ?? 18, 2) }}%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Categoría</small>
                                <span class="fw-bold">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Unidad de Medida</small>
                                <span class="fw-bold">{{ $producto->unidad_medida ?? 'Unidad' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Stock</small>
                                <span class="fw-bold">{{ $producto->stock }} unidades</span>
                            </div>
                        </div>
                        @if($producto->descripcion)
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                    <small class="text-muted text-uppercase d-block small fw-semibold">Descripción</small>
                                    <p class="mb-0">{{ $producto->descripcion }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="premium-card h-100" style="animation-delay:.2s;">
                        <div class="card-accent green"></div>
                        <div class="card-body p-4 text-center">
                            <div class="premium-user-avatar avatar-green mx-auto mb-3">
                                <i class="bi bi-cart-check fs-3" style="color:#10b981;"></i>
                            </div>
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Compras</h6>
                            <h2 class="fw-bold mb-0" style="color:#4f46e5;">{{ $producto->detallesCompras->count() }}</h2>
                            <small class="text-muted">registros de compra</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="premium-card h-100" style="animation-delay:.25s;">
                        <div class="card-accent purple"></div>
                        <div class="card-body p-4 text-center">
                            <div class="premium-user-avatar avatar-blue mx-auto mb-3">
                                <i class="bi bi-receipt fs-3" style="color:#8b5cf6;"></i>
                            </div>
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Ventas</h6>
                            <h2 class="fw-bold mb-0" style="color:#6366f1;">{{ $producto->ventaDetalles->count() }}</h2>
                            <small class="text-muted">veces vendido</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
