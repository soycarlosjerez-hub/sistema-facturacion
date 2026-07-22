@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#dc2626;--accent-rgb:220,38,38;--accent-hover:#b91c1c;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cuentas por Cobrar</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Auditando deudas, fiados y cuentas abiertas
                    </div>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 d-inline-block px-4 py-2 rounded-pill" style="backdrop-filter:blur(8px);border:1.5px solid rgba(255,255,255,.25);">
                <span class="small fw-bold text-uppercase me-2 opacity-75">Total por Cobrar:</span>
                <span class="fs-5 fw-bold">RD${{ number_format($clientes->sum('balance_pendiente'), 2) }}</span>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-10">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="ui-input" placeholder="Buscar cliente con deuda..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($clientes as $c)
    <div class="ui-card mb-3" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
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
                <table class="ui-table table-sm">
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
                                    <span class="ui-badge ui-badge-warning">FIAO</span>
                                @else
                                    <span class="ui-badge ui-badge-info">CTA. ABIERTA</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold small">RD${{ number_format($v->total, 2) }}</td>
                            <td class="text-end text-success small">RD${{ number_format($pagado, 2) }}</td>
                            <td class="text-end text-danger fw-bold small">RD${{ number_format($pendiente, 2) }}</td>
                            <td class="text-center pe-4">
                                <a href="{{ route('pagos.realizar', $v->id) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
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
