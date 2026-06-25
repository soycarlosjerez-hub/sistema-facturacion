@extends('layouts.app')

@section('title', 'Compra ' . $compra->folio)

@push('styles')
@include('partials.premium-ui')
<style>
.detalles-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.detalles-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.detalles-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
body.dark-mode .detalles-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .detalles-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Compra {{ $compra->folio }}</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-cart me-1"></i>
                        Detalle completo de la compra y productos recibidos
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('compras.edit', $compra) }}" class="btn btn-light rounded-pill px-4 fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                @if($compra->puede_generar_ecf)
                <form action="{{ route('compras.generar-ecf', $compra) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-warning rounded-pill px-4 fw-bold" onclick="return confirm('¿Generar e-CF E41 para esta compra?')">
                        <i class="bi bi-shield-check me-1"></i>Generar e-CF E41
                    </button>
                </form>
                @endif
                <a href="{{ route('compras.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.05s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-3">
                    <div class="stat-label mb-1"><i class="bi bi-truck me-1"></i>Proveedor</div>
                    <div class="stat-value" style="color:#3b82f6;font-size:1rem;">{{ $compra->proveedor->nombre ?? 'N/A' }}</div>
                    <small class="text-muted">RNC: {{ $compra->proveedor->rnc_cedula ?? '—' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.1s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-3">
                    <div class="stat-label mb-1"><i class="bi bi-calendar3 me-1"></i>Fecha</div>
                    <div class="stat-value" style="color:#3b82f6;font-size:1rem;">{{ $compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $compra->created_at->format('h:i A') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.15s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-3">
                    <div class="stat-label mb-1"><i class="bi bi-tag me-1"></i>Tipo</div>
                    <div class="stat-value" style="color:#3b82f6;font-size:1rem;">{{ $compra->tipoCompra->nombre ?? 'N/A' }}</div>
                    <small class="text-muted">Registrado por {{ $compra->user->name ?? '—' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card" style="animation-delay:.2s;">
                <div class="card-accent blue"></div>
                <div class="card-body p-3 text-center">
                    <div class="stat-label mb-1"><i class="bi bi-cash-stack me-1"></i>Total</div>
                    <div class="stat-value" style="color:#3b82f6;">RD$ {{ number_format($compra->total, 2) }}</div>
                    <small class="text-muted">Incluye ITBIS</small>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.25s;">
        <div class="card-accent blue"></div>
        <div class="premium-card-title">
            <i class="bi bi-list-check icon-blue"></i>
            Productos recibidos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table detalles-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">ITBIS %</th>
                            <th class="text-end">Base</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compra->detalles as $detalle)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $detalle->producto->nombre ?? '—' }}</div>
                                @if($detalle->producto)
                                    <small class="text-muted">
                                        Stock actual: <strong>{{ $detalle->producto->stock }}</strong>
                                    </small>
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ $detalle->cantidad }}</td>
                            <td class="text-end">RD$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-end text-muted">{{ number_format($detalle->itbis_porcentaje ?? 18, 2) }}%</td>
                            <td class="text-end text-muted">RD$ {{ number_format($detalle->base, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-success">RD$ {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay productos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background:rgba(241,245,249,.5);">
                        <tr>
                            <td colspan="5" class="text-end fw-bold ps-4">Subtotal:</td>
                            <td class="text-end pe-4">RD$ {{ number_format($compra->subtotal ?? $compra->detalles->sum('base'), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold ps-4">ITBIS:</td>
                            <td class="text-end pe-4">RD$ {{ number_format($compra->itbis_total ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-5 ps-4">TOTAL:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-primary">RD$ {{ number_format($compra->total, 2) }}</td>
                        </tr>
                        @if($compra->aplica_retencion_isr || $compra->aplica_retencion_itbis)
                        <tr class="text-danger">
                            <td colspan="5" class="text-end fw-bold ps-4">Retención ISR:</td>
                            <td class="text-end pe-4">- RD$ {{ number_format($compra->retencion_isr, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="5" class="text-end fw-bold ps-4">Retención ITBIS:</td>
                            <td class="text-end pe-4">- RD$ {{ number_format($compra->retencion_itbis, 2) }}</td>
                        </tr>
                        <tr class="text-success">
                            <td colspan="5" class="text-end fw-bold fs-5 ps-4">Total a Pagar:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-success">RD$ {{ number_format($compra->total_neto, 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($compra->observaciones)
    <div class="premium-card" style="animation-delay:.3s;">
        <div class="card-accent blue"></div>
        <div class="premium-card-title">
            <i class="bi bi-chat-left-text icon-blue"></i>
            Observaciones
        </div>
        <div class="card-body">
            <p class="mb-0 text-muted">{{ $compra->observaciones }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
