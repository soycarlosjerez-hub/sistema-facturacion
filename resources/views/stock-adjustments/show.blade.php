@extends('layouts.app')
@section('title', 'Detalle de Ajuste de Stock')

@push('styles')
@include('partials.premium-ui')
<style>
.detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; }
.detail-value { font-size: 1rem; color: #0f172a; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-box-seam"></i></div>
                <div>
                    <h4 class="ui-header-title">Ajuste de Stock #{{ str_pad($stockAdjustment->id, 4, '0', STR_PAD_LEFT) }}</h4>
                    <div class="ui-header-meta">
                        {{ $stockAdjustment->tipo_label }} &middot; Creado el {{ $stockAdjustment->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-adjustments.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #10b981 !important;">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4">Detalles del Ajuste</h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-label mb-1">Producto</div>
                            <div class="detail-value fw-semibold">{{ $stockAdjustment->producto?->nombre ?? 'N/A' }}</div>
                            @if($stockAdjustment->producto?->codigo_barras)
                            <small class="text-muted font-monospace">{{ $stockAdjustment->producto->codigo_barras }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label mb-1">Tipo</div>
                            <div class="detail-value">
                                <span class="badge {{ $stockAdjustment->tipo == 'merma' ? 'bg-danger' : ($stockAdjustment->tipo == 'inventario' ? 'bg-info' : 'bg-primary') }}">
                                    {{ $stockAdjustment->tipo_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Sucursal</div>
                            <div class="detail-value">{{ $stockAdjustment->sucursal?->nombre ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Almacén</div>
                            <div class="detail-value">{{ $stockAdjustment->almacen?->nombre ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Usuario</div>
                            <div class="detail-value">{{ $stockAdjustment->user?->name ?? '-' }}</div>
                        </div>

                        <!-- Cantidades -->
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Cantidad Anterior</div>
                            <div class="detail-value fw-bold">{{ $stockAdjustment->cantidad_anterior }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Cantidad Nueva</div>
                            <div class="detail-value fw-bold">{{ $stockAdjustment->cantidad_nueva }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Diferencia</div>
                            <div class="detail-value fw-bold {{ $stockAdjustment->diferencia >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $stockAdjustment->diferencia >= 0 ? '+' : '' }}{{ $stockAdjustment->diferencia }}
                            </div>
                        </div>
                    </div>

                    @if($stockAdjustment->motivo)
                    <hr class="my-4">
                    <div class="detail-label mb-1">Motivo</div>
                    <div class="detail-value">{{ $stockAdjustment->motivo }}</div>
                    @endif

                    @if($stockAdjustment->notas)
                    <hr class="my-4">
                    <div class="detail-label mb-1">Notas</div>
                    <div class="detail-value">{{ $stockAdjustment->notas }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
