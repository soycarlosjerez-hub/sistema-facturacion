@extends('layouts.app')

@section('title', 'Detalle de Factura #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT))

@push('styles')
@include('partials.premium-ui')
<style>
.invoice-card {
    background: white;
    color: #1e293b;
}
.pagos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.pagos-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.pagos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
body.dark-mode .invoice-card {
    background: rgba(15,23,42,.9);
    color: #f1f5f9;
}
body.dark-mode .pagos-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .pagos-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
@media print {
    .breadcrumb, .btn, .nav-section-title, .nav, header, .premium-header { display: none !important; }
    .invoice-card { box-shadow: none !important; border: 1px solid #eee !important; }
    .container-fluid { padding: 0 !important; }
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
                    <h4 class="fw-bold mb-1 text-white">Comprobante de Venta</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-receipt me-1"></i>
                        Factura #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                        <span class="mx-2">·</span>
                        {{ $venta->created_at->format('d/m/Y h:i A') }}
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('ventas.pdf', $venta->id) }}" class="btn btn-light rounded-pill px-3 shadow-sm" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-file-pdf me-1"></i>PDF
                </a>
                <button onclick="window.print()" class="btn btn-light rounded-pill px-3 shadow-sm" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden invoice-card">
                <div class="p-4 text-center border-bottom border-dashed">
                    <h4 class="fw-bold mb-0">COLMADO PREMIUM</h4>
                    <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 2px;">Venta Realizada</small>
                </div>
                
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Folio</small>
                            <span class="fw-bold text-primary">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha</small>
                            <span class="fw-bold small">{{ $venta->created_at->format('d/m/Y h:i A') }}</span>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3 bg-light">
                        <div class="mb-2">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Cliente</small>
                            <span class="fw-bold">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Atendido por</small>
                            <span class="small">{{ $venta->usuario->name ?? 'Sistema' }}</span>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        @if($venta->estado == 'completada')
                            <span class="premium-badge" style="background:rgba(16,185,129,.1);color:#059669;"><i class="bi bi-check-circle-fill me-1"></i>PAGADA COMPLETAMENTE</span>
                        @elseif($venta->estado == 'cuenta_abierta')
                            <span class="premium-badge" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-door-open-fill me-1"></i>CUENTA ABIERTA</span>
                        @else
                            <span class="premium-badge" style="background:rgba(245,158,11,.1);color:#d97706;"><i class="bi bi-exclamation-circle-fill me-1"></i>FIAO PENDIENTE</span>
                        @endif
                    </div>

                    <div class="border-top border-bottom border-dashed py-3 mb-3">
                        @foreach($venta->detalles as $d)
                        <div class="d-flex justify-content-between mb-2">
                            <div class="small pe-3" style="flex: 1;">
                                <div class="fw-bold">{{ $d->producto->nombre }}</div>
                                <small class="text-muted">{{ $d->cantidad }} x RD${{ number_format($d->precio_unitario, 2) }}</small>
                            </div>
                            <div class="fw-bold text-end small">RD${{ number_format($d->subtotal, 2) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Subtotal</span>
                            <span class="small">RD${{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">ITBIS (Impuestos)</span>
                            <span class="small">RD${{ number_format($venta->impuestos, 2) }}</span>
                        </div>
                        @if($venta->descuento > 0)
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span class="small">Descuento</span>
                            <span class="small">-RD${{ number_format($venta->descuento, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                            <span class="fw-bold h5">TOTAL</span>
                            <span class="fw-bold h5 text-primary">RD${{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>

                    @if($venta->ncf)
                    <div class="alert alert-secondary border-0 rounded-3 text-center py-2 mb-2">
                        <small class="text-uppercase fw-bold opacity-50 d-block" style="font-size: 0.6rem;">NCF</small>
                        <span class="fw-bold" style="letter-spacing: 1px;">{{ $venta->ncf }}</span>
                    </div>
                    @endif

                    @if($venta->ecf)
                    @php
                        $ecfEstado = $venta->ecf->estado_info;
                    @endphp
                    <div class="alert alert-{{ $ecfEstado['color'] }} border-0 rounded-3 py-2 mb-0">
                        <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.6rem;">
                            <i class="bi bi-shield-check me-1"></i>e-CF - {{ $ecfEstado['label'] }}
                        </small>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="fw-bold" style="letter-spacing: 1px;">{{ $venta->ecf->encf }}</span>
                            <a href="{{ route('ecf.show', $venta->ecf) }}" class="btn btn-sm btn-light rounded-pill ms-2">
                                Ver <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="p-3 text-center opacity-50 bg-light">
                    <small>Gracias por su compra</small>
                </div>
            </div>

            @if($venta->estado != 'completada')
            <a href="{{ route('pagos.realizar', $venta->id) }}" class="btn btn-success rounded-pill px-4 shadow w-100 mt-3">
                <i class="bi bi-cash-coin me-2"></i>Registrar Pago
            </a>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="premium-card mb-4" style="animation-delay:.1s;">
                <div class="card-accent blue"></div>
                <div class="premium-card-title">
                    <i class="bi bi-cash-coin icon-blue"></i>
                    Seguimiento de Pagos
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pagos-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha de Pago</th>
                                    <th>Monto Pagado</th>
                                    <th>Método / Nota</th>
                                    <th class="text-end pe-4">Referencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($venta->pagos as $p)
                                <tr>
                                    <td class="ps-4 small">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y h:i A') }}</td>
                                    <td><span class="fw-bold text-success">RD${{ number_format($p->monto, 2) }}</span></td>
                                    <td class="small text-muted">{{ $p->nota ?? 'Pago registrado' }}</td>
                                    <td class="text-end pe-4"><small class="premium-badge">ID-{{ $p->id }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        No se han registrado pagos para esta venta todavía.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($venta->pagos->count())
                            <tfoot class="fw-bold" style="background:rgba(241,245,249,.5);">
                                <tr>
                                    <td class="ps-4">Resumen de Cobros</td>
                                    <td class="text-success">RD${{ number_format($venta->pagos->sum('monto'), 2) }}</td>
                                    <td class="text-danger">Pendiente: RD${{ number_format($venta->total - $venta->pagos->sum('monto'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card" style="animation-delay:.15s;">
                <div class="card-accent blue"></div>
                <div class="premium-card-title">
                    <i class="bi bi-archive icon-blue"></i>
                    Movimientos de Inventario (Kardex)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm pagos-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th>Almacén</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalles as $det)
                                @php 
                                    $mov = \App\Models\AlmacenMovimiento::where('producto_id', $det->producto_id)
                                            ->where('nota', 'like', '%' . $venta->id . '%')
                                            ->first();
                                @endphp
                                <tr class="small">
                                    <td class="ps-4">{{ $det->producto->nombre }}</td>
                                    <td>{{ $det->almacen->nombre ?? 'N/A' }}</td>
                                    <td><span class="premium-badge" style="background:rgba(239,68,68,.1);color:#dc2626;">Salida</span></td>
                                    <td class="text-center fw-bold">{{ $det->cantidad }}</td>
                                    <td class="text-muted">{{ $venta->usuario->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<div class="modal fade" id="modalAnularVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Anular Venta #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div class="small">
                            Esta acción es <strong>irreversible</strong>. Se revertirá el stock al inventario
                            @if($venta->cliente_id && ($venta->estado == 'pendiente' || $venta->estado == 'cuenta_abierta'))
                                y se ajustará el balance pendiente del cliente
                            @endif.
                        </div>
                    </div>
                    <div class="row g-2 small mb-3">
                        <div class="col-6"><strong>Total:</strong> RD$ {{ number_format($venta->total, 2) }}</div>
                        <div class="col-6"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'N/A' }}</div>
                        <div class="col-6"><strong>Pagado:</strong> RD$ {{ number_format($venta->pagos->sum('monto'), 2) }}</div>
                        <div class="col-6"><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</div>
                    </div>
                    <label class="form-label fw-bold small text-uppercase">Motivo de anulación <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control border-0 bg-light" rows="3" required minlength="5" placeholder="Ej: Error en productos, cliente devolvió, etc."></textarea>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="confirmar" value="1" id="confirmAnular" required>
                        <label class="form-check-label small fw-bold" for="confirmAnular">Confirmo que deseo anular esta venta</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-x-circle me-1"></i>Anular Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
