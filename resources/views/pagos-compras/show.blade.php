@extends('layouts.app')
@section('title', 'Detalle de Pago')

@push('styles')
@include('partials.premium-ui')
<style>
.detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; }
.detail-value { font-size: 1rem; color: #0f172a; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#059669;--accent-rgb:5,150,105;--accent-hover:#047857;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-receipt-cutoff"></i></div>
                <div>
                    <h4 class="ui-header-title">Detalle de Pago</h4>
                    <div class="ui-header-meta">Pago #{{ str_pad($pagoCompra->id, 4, '0', STR_PAD_LEFT) }} &middot; {{ $pagoCompra->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('pagos-compras.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
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
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3">Información del Pago</h6>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Monto</div>
                        <div class="fs-2 fw-bold text-success">RD$ {{ number_format($pagoCompra->monto, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Método de Pago</div>
                        <div>
                            <span class="badge {{ $pagoCompra->metodo_pago == 'efectivo' ? 'bg-success' : ($pagoCompra->metodo_pago == 'tarjeta' ? 'bg-primary' : 'bg-info') }}">
                                {{ ucfirst($pagoCompra->metodo_pago) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Fecha de Pago</div>
                        <div>{{ $pagoCompra->fecha_pago?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Creado por</div>
                        <div>{{ $pagoCompra->user?->name ?? 'N/A' }}</div>
                    </div>
                    @if($pagoCompra->nota)
                    <div class="mb-3">
                        <div class="detail-label mb-1">Nota</div>
                        <div>{{ $pagoCompra->nota }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3">Información de la Compra</h6>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-label mb-1">Nº Compra</div>
                            <div class="fw-semibold">{{ $pagoCompra->compra?->folio ?? '#' . $pagoCompra->compra_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label mb-1">Proveedor</div>
                            <div class="fw-semibold">{{ $pagoCompra->compra->proveedor?->nombre ?? 'N/A' }}</div>
                            @if($pagoCompra->compra->proveedor?->rnc)
                            <small class="text-muted font-monospace">RNC: {{ $pagoCompra->compra->proveedor->rnc }}</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Total Compra</div>
                            <div class="fw-bold">RD$ {{ number_format($totalCompra, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Total Pagado</div>
                            <div class="fw-bold text-success">RD$ {{ number_format($totalPagado, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label mb-1">Saldo Pendiente</div>
                            <div class="fw-bold text-danger">RD$ {{ number_format($totalCompra - $totalPagado, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($totalPagado < $totalCompra)
            <div class="ui-card mt-4" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3">Registrar Pago Adicional</h6>
                    <form action="{{ route('pagos-compras.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="compra_id" value="{{ $pagoCompra->compra_id }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="ui-label">Monto <span class="text-danger">*</span></label>
                                <input type="number" name="monto" class="ui-input" step="0.01" min="0.01" max="{{ $totalCompra - $totalPagado }}" required>
                                <small class="text-muted">Máximo: RD$ {{ number_format($totalCompra - $totalPagado, 2) }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label">Método de Pago <span class="text-danger">*</span></label>
                                <select name="metodo_pago" class="ui-select" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label">Nota</label>
                                <input type="text" name="nota" class="ui-input" maxlength="500">
                            </div>
                        </div>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill mt-3">
                            <i class="bi bi-check-lg me-1"></i>Registrar Pago
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
