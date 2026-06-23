@extends('layouts.app')

@section('title', $producto->nombre)

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-box-seam fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">{{ $producto->nombre }}</h2>
                    <p class="text-white text-opacity-75 mb-0">
                        <i class="bi bi-upc-scan me-1"></i>{{ $producto->codigo_barras ?? 'Sin código' }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar
                </a>
                <a href="{{ route('productos.index') }}" class="btn btn-white bg-white bg-opacity-25 text-white rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden glass-card">
                <div class="card-body p-4 text-center">
                    <img src="{{ $producto->imagen_url }}" class="rounded-3 shadow-sm img-fluid mb-3" style="max-height: 280px; object-fit: cover; background: #f1f5f9;" alt="{{ $producto->nombre }}">
                    <h4 class="fw-bold mb-1">{{ $producto->nombre }}</h4>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-upc-scan"></i> {{ $producto->codigo_barras ?? 'Sin código' }}
                    </p>
                    @if($producto->estado_stock === 'critical')
                        <span class="badge bg-danger rounded-pill px-3 py-2">Stock Crítico: {{ $producto->stock }}</span>
                    @elseif($producto->estado_stock === 'low')
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Stock Bajo: {{ $producto->stock }}</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2">Stock Normal: {{ $producto->stock }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 glass-card">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0" style="color: #0ea5e9;">
                            <i class="bi bi-info-circle me-2"></i>Información General
                        </h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Precio de Venta</small>
                                <span class="fs-4 fw-bold text-primary">RD$ {{ number_format($producto->precio, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Precio de Compra</small>
                                <span class="fs-5 fw-bold">RD$ {{ number_format($producto->precio_compra ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Ganancia por unidad</small>
                                <span class="fs-5 fw-bold {{ $producto->ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $producto->ganancia >= 0 ? '+' : '' }}RD$ {{ number_format($producto->ganancia, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Margen</small>
                                <span class="fs-5 fw-bold text-info">{{ number_format($producto->margen_porcentaje, 2) }}%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">ITBIS</small>
                                <span class="fw-bold">{{ number_format($producto->itbis_porcentaje ?? 18, 2) }}%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Categoría</small>
                                <span class="fw-bold">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Unidad de Medida</small>
                                <span class="fw-bold">{{ $producto->unidad_medida ?? 'Unidad' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted text-uppercase d-block small fw-semibold">Stock</small>
                                <span class="fw-bold">{{ $producto->stock }} unidades</span>
                            </div>
                        </div>
                        @if($producto->descripcion)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3">
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
                    <div class="card border-0 shadow-lg rounded-4 h-100 glass-card">
                        <div class="card-body p-4 text-center">
                            <div class="mb-2">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); width: 64px; height: 64px;">
                                    <i class="bi bi-cart-check fs-3 text-success"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Compras</h6>
                            <h2 class="fw-bold mb-0" style="color: #0ea5e9;">{{ $producto->detallesCompras->count() }}</h2>
                            <small class="text-muted">registros de compra</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100 glass-card">
                        <div class="card-body p-4 text-center">
                            <div class="mb-2">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3" style="background: rgba(99, 102, 241, 0.1); width: 64px; height: 64px;">
                                    <i class="bi bi-receipt fs-3" style="color: #6366f1;"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Ventas</h6>
                            <h2 class="fw-bold mb-0" style="color: #6366f1;">{{ $producto->ventaDetalles->count() }}</h2>
                            <small class="text-muted">veces vendido</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection