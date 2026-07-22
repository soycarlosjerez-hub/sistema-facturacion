@extends('layouts.app')
@section('title', $categoria->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $categoria->nombre }}</h4>
                    <div class="ui-header-meta">{{ $categoria->productos->count() }} producto(s)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('categorias.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <div class="ui-detail-row">
                <span class="ui-detail-label">Descripción</span>
                <span class="ui-detail-value">{{ $categoria->descripcion ?? 'Sin descripción' }}</span>
            </div>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Estado</span>
                <span class="ui-detail-value">
                    @if($categoria->activa)
                        <span class="ui-badge-success"><i class="bi bi-check-circle-fill"></i> Activa</span>
                    @else
                        <span class="ui-badge-danger"><i class="bi bi-x-circle-fill"></i> Inactiva</span>
                    @endif
                </span>
            </div>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Productos</span>
                <span class="ui-detail-value fw-bold fs-4">{{ $categoria->productos->count() }}</span>
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-title"><i class="bi bi-box-seam me-2"></i> Productos en esta categoría</div>
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
