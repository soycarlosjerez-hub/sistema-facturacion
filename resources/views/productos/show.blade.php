@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>{{ $producto->nombre }}</h2>
            <p class="text-muted mb-0">Detalle completo del producto</p>
        </div>
        <div>
            <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Editar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Información General</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Precio de Venta</small>
                            <span class="fs-4 fw-bold text-primary">RD$ {{ number_format($producto->precio, 2) }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Precio de Compra</small>
                            <span class="fs-5 fw-bold">RD$ {{ number_format($producto->precio_compra ?? 0, 2) }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Ganancia por unidad</small>
                            <span class="fs-5 fw-bold {{ $producto->ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $producto->ganancia >= 0 ? '+' : '' }}RD$ {{ number_format($producto->ganancia, 2) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Margen</small>
                            <span class="fs-5 fw-bold text-info">{{ number_format($producto->margen_porcentaje, 2) }}%</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted text-uppercase d-block">ITBIS</small>
                            <span class="fw-bold">{{ number_format($producto->itbis_porcentaje ?? 18, 2) }}%</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted text-uppercase d-block">Categoría</small>
                            <span class="fw-bold">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted text-uppercase d-block">Unidad de Medida</small>
                            <span class="fw-bold">{{ $producto->unidad_medida ?? 'Unidad' }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted text-uppercase d-block">Stock</small>
                            <span class="fw-bold">{{ $producto->stock }} unidades</span>
                        </div>
                        @if($producto->descripcion)
                            <div class="col-12">
                                <small class="text-muted text-uppercase d-block">Descripción</small>
                                <p class="mb-0">{{ $producto->descripcion }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-cart-check text-success me-1"></i>Compras</h6>
                            <h2 class="fw-bold mb-0">{{ $producto->detallesCompras->count() }}</h2>
                            <small class="text-muted">registros de compra</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-receipt text-primary me-1"></i>Ventas</h6>
                            <h2 class="fw-bold mb-0">{{ $producto->ventaDetalles->count() }}</h2>
                            <small class="text-muted">veces vendido</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
