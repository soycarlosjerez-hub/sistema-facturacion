@extends('layouts.app')
@section('title', $categoria->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
</style>
@endpush

@section('content')
<div class="premium-page">
    <div class="container-fluid px-4">
        <div class="premium-header mb-4">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $categoria->nombre }}</h2>
                        <p class="mb-0 opacity-75">{{ $categoria->productos->count() }} producto(s)</p>
                    </div>
                </div>
                <a href="{{ route('categorias.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>

        <div class="premium-card mb-4">
            <div class="card-accent purple"></div>
            <div class="card-body p-4">
                <div class="premium-detail-row">
                    <span class="premium-detail-label">Descripción</span>
                    <span class="premium-detail-value">{{ $categoria->descripcion ?? 'Sin descripción' }}</span>
                </div>
                <div class="premium-detail-row">
                    <span class="premium-detail-label">Estado</span>
                    <span class="premium-detail-value">
                        @if($categoria->activa)
                            <span class="premium-badge active"><i class="bi bi-check-circle-fill"></i> Activa</span>
                        @else
                            <span class="premium-badge"><i class="bi bi-x-circle-fill"></i> Inactiva</span>
                        @endif
                    </span>
                </div>
                <div class="premium-detail-row">
                    <span class="premium-detail-label">Productos</span>
                    <span class="premium-detail-value fw-bold fs-4">{{ $categoria->productos->count() }}</span>
                </div>
            </div>
        </div>

        <div class="premium-card">
            <div class="premium-card-title"><i class="bi bi-box-seam icon-purple"></i> Productos en esta categoría</div>
            <div class="card-body">
                @if($categoria->productos->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-3">Producto</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoria->productos as $p)
                                    <tr>
                                        <td class="ps-3">{{ $p->nombre }}</td>
                                        <td class="text-end">RD$ {{ number_format($p->precio, 2) }}</td>
                                        <td class="text-end">{{ $p->stock }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No hay productos en esta categoría.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
