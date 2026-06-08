@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">Gestión de Cuentas</h3>
            <p class="text-muted mb-0">Auditando deudas, fiados y cuentas abiertas</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="bg-white d-inline-block px-4 py-2 rounded-pill shadow-sm border">
                <span class="text-muted small fw-bold text-uppercase me-2">Total por Cobrar:</span>
                <span class="fs-5 fw-bold text-danger">RD${{ number_format($clientes->sum('balance_pendiente'), 2) }}</span>
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
