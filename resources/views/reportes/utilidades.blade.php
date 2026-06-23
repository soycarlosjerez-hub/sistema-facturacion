@extends('layouts.app')
@section('title', 'Utilidades / Rentabilidad')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(34,197,94,0.4);
    position: relative; overflow: hidden;
}
.premium-header::after {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.filter-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-graph-up text-danger me-2"></i>Utilidades / Rentabilidad</h2>
            <p class="text-muted mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $detalles->count() }} línea(s)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.utilidades.csv', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
        </div>
    </div>

    <div class="filter-card p-3 mb-4">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="form-control border-0 bg-white" value="{{ $desde }}"></div>
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ $hasta }}"></div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas</small><h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($totalVentas, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Costo</small><h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($totalCosto, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Utilidad Bruta</small><h4 class="fw-bold mb-0 mt-1 {{ $utilidadBruta >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($utilidadBruta, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Margen</small><h4 class="fw-bold mb-0 mt-1 text-info">{{ number_format($margen, 1) }}%</h4></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Venta</th><th>Fecha</th><th>Cliente</th><th>Producto</th>
                        <th class="text-end">Cant.</th><th class="text-end">Precio</th><th class="text-end">Costo</th><th class="text-end">Subtotal</th><th class="text-end pe-4">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detalles as $d)
                        <tr>
                            <td class="ps-4 font-monospace small">#{{ str_pad($d['venta_id'], 5, '0', STR_PAD_LEFT) }}</td>
                            <td><small>{{ $d['fecha'] }}</small></td>
                            <td><span class="small">{{ $d['cliente'] }}</span></td>
                            <td><span class="fw-semibold small">{{ $d['producto'] }}</span></td>
                            <td class="text-end">{{ $d['cantidad'] }}</td>
                            <td class="text-end">RD$ {{ number_format($d['precio'], 2) }}</td>
                            <td class="text-end text-warning">RD$ {{ number_format($d['costo'], 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($d['subtotal'], 2) }}</td>
                            <td class="text-end pe-4 fw-bold {{ $d['ganancia'] >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($d['ganancia'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay datos en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4 py-3 text-end text-uppercase small">Totales ({{ $totalProductosVendidos }} unidades)</td>
                        <td class="text-end py-3">{{ $totalProductosVendidos }}</td>
                        <td class="text-end py-3"></td>
                        <td class="text-end py-3 text-warning">RD$ {{ number_format($totalCosto, 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($totalVentas, 2) }}</td>
                        <td class="text-end pe-4 py-3 {{ $utilidadBruta >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($utilidadBruta, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
