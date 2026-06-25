@extends('layouts.app')
@section('title', 'Utilidades / Rentabilidad')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(59,130,246,.25) !important;
}
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Utilidades / Rentabilidad</h2>
                <p class="text-white-50 mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $detalles->count() }} línea(s)</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.utilidades.csv', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card card-accent blue p-3 mb-4">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="form-control border-0 bg-white" value="{{ $desde }}"></div>
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ $hasta }}"></div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Ventas</small><h4 class="fw-bold mb-0 mt-1">RD$ {{ number_format($totalVentas, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Costo</small><h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($totalCosto, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Utilidad Bruta</small><h4 class="fw-bold mb-0 mt-1 {{ $utilidadBruta >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($utilidadBruta, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Margen</small><h4 class="fw-bold mb-0 mt-1 text-info">{{ number_format($margen, 1) }}%</h4></div></div></div>
    </div>

    <div class="premium-card overflow-hidden">
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
