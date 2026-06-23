@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4);
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
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .btn-icon-hover:hover { transform: scale(1.15); }
    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 1.2rem;
        transition: transform 0.2s;
    }
    tr:hover .avatar-circle { transform: scale(1.1); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Premium Header -->
    <div class="premium-header">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-coin fs-3 text-white"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0 text-white">Cuentas por Cobrar</h2>
                        <p class="text-white text-opacity-75 mb-0">Auditando deudas, fiados y cuentas abiertas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="bg-white bg-opacity-20 d-inline-block px-4 py-2 rounded-pill border border-white border-opacity-25">
                    <span class="small fw-bold text-uppercase me-2 opacity-75">Total por Cobrar:</span>
                    <span class="fs-5 fw-bold">RD${{ number_format($clientes->sum('balance_pendiente'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-10">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-0 bg-white" 
                               placeholder="Buscar cliente con deuda..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary rounded-3 w-100">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Clientes con Deuda -->
    @foreach($clientes as $c)
    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
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
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr style="font-size: 0.7rem; text-transform: uppercase;">
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
                                <span class="badge bg-warning text-dark rounded-pill px-2" style="font-size: 0.65rem;">FIAO</span>
                            @else
                                <span class="badge bg-primary text-white rounded-pill px-2" style="font-size: 0.65rem;">CTA. ABIERTA</span>
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
    @endforeach

    <!-- Paginación -->
    <div class="mt-4">
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
@endsection
