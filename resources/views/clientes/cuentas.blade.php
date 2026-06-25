@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@push('styles')
@include('partials.premium-ui')
<style>
.cuentas-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(239,68,68,.04);
    margin: 0;
}
.cuentas-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.cuentas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.cuentas-table tbody tr:last-child td { border-bottom: none; }
.cuentas-table tbody tr { transition: background .15s; }
.cuentas-table tbody tr:hover { background: rgba(239,68,68,.03); }
body.dark-mode .cuentas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .cuentas-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#dc2626,#ef4444,#f97316,#dc2626);box-shadow:0 8px 32px rgba(220,38,38,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Cuentas por Cobrar</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Auditando deudas, fiados y cuentas abiertas
                    </small>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 d-inline-block px-4 py-2 rounded-pill" style="backdrop-filter:blur(8px);border:1.5px solid rgba(255,255,255,.25);">
                <span class="small fw-bold text-uppercase me-2 opacity-75">Total por Cobrar:</span>
                <span class="fs-5 fw-bold">RD${{ number_format($clientes->sum('balance_pendiente'), 2) }}</span>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent red"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar cliente con deuda..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($clientes as $c)
    <div class="premium-card mb-3" style="animation-delay:.15s;">
        <div class="card-accent red"></div>
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $c->nombre }}</h6>
                        <small class="text-muted">{{ $c->telefono ?? 'Sin teléfono' }}</small>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block small fw-bold text-uppercase">Balance Pendiente</small>
                    <span class="fs-5 fw-bold text-danger">RD${{ number_format($c->balance_pendiente, 2) }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm cuentas-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Venta #</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Monto Total</th>
                            <th class="text-end">Pagado</th>
                            <th class="text-end">Pendiente</th>
                            <th class="text-center pe-4" width="150">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($c->ventas as $v)
                        @php $pagado = $v->montoPagado(); $pendiente = $v->total - $pagado; @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="small">{{ $v->created_at->format('d/m/Y h:i A') }}</td>
                            <td>
                                @if($v->estado == 'pendiente')
                                    <span class="premium-badge" style="background:rgba(245,158,11,.1);color:#d97706;">FIAO</span>
                                @else
                                    <span class="premium-badge" style="background:rgba(59,130,246,.1);color:#3b82f6;">CTA. ABIERTA</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold small">RD${{ number_format($v->total, 2) }}</td>
                            <td class="text-end text-success small">RD${{ number_format($pagado, 2) }}</td>
                            <td class="text-end text-danger fw-bold small">RD${{ number_format($pendiente, 2) }}</td>
                            <td class="text-center pe-4">
                                <a href="{{ route('pagos.realizar', $v->id) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                    <i class="bi bi-cash me-1"></i> Cobrar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-center mt-3">
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
@endsection
