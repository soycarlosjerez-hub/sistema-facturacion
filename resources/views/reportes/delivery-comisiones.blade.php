@extends('layouts.app')
@section('title', 'Comisiones Delivery')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-truck text-primary me-2"></i>Comisiones Delivery</h2>
            <p class="text-muted mb-0">Reporte de comisiones por empresas de delivery</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-auto">
            <label class="form-label small fw-bold">Desde</label>
            <input type="date" name="desde" class="form-control rounded-3" value="{{ $desde }}">
        </div>
        <div class="col-auto">
            <label class="form-label small fw-bold">Hasta</label>
            <input type="date" name="hasta" class="form-control rounded-3" value="{{ $hasta }}">
        </div>
        <div class="col-auto d-flex align-items-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-filter me-1"></i> Filtrar
            </button>
        </div>
    </form>

    {{-- Resumen por compañía --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
            <h6 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Resumen por Compañía</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Compañía</th>
                            <th class="text-center">Ventas</th>
                            <th class="text-end">Total Comisiones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $c)
                        <tr>
                            <td class="fw-medium">{{ $c['nombre'] }}</td>
                            <td class="text-center">{{ $c['ventas'] }}</td>
                            <td class="text-end fw-bold">RD$ {{ number_format($c['total_fee'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="fw-bold">Total</th>
                            <th class="text-center">{{ collect($companies)->sum('ventas') }}</th>
                            <th class="text-end fw-bold">RD$ {{ number_format($totalFees, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Detalle de ventas con delivery --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-success"></i>Detalle de Ventas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Mesa</th>
                            <th>Compañía</th>
                            <th class="text-end">Total Venta</th>
                            <th class="text-end">Comisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalles as $i => $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $v->mesa?->nombre ?? '—' }}</td>
                            <td>{{ $v->deliveryCompany?->nombre ?? '—' }}</td>
                            <td class="text-end">RD$ {{ number_format($v->total, 2) }}</td>
                            <td class="text-end fw-bold">RD$ {{ number_format($v->delivery_fee, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
