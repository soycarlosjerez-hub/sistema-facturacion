@extends('layouts.app')
@section('title', 'Resumen de Compras')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(245,158,11,0.4);
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
            <h2 class="fw-bold mb-1"><i class="bi bi-cart-check text-success me-2"></i>Resumen de Compras</h2>
            <p class="text-muted mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} compra(s)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.compras.csv', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
            <a href="{{ route('reportes.compras.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-danger rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
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
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Cantidad</small><h4 class="fw-bold mb-0 mt-1">{{ $cantidad }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total General</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalGeneral, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">ITBIS</small><h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($totalItbis, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Retenciones</small><h4 class="fw-bold mb-0 mt-1 text-danger">RD$ {{ number_format($totalRetenciones, 2) }}</h4></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">#</th><th>Proveedor</th><th>Usuario</th><th>Folio</th><th>Fecha</th>
                        <th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Ret ISR</th><th class="text-end">Ret ITBIS</th><th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $c)
                        <tr>
                            <td class="ps-4">{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="fw-semibold small">{{ $c->proveedor?->nombre ?? 'N/A' }}</span></td>
                            <td><small>{{ $c->user?->name ?? '' }}</small></td>
                            <td><span class="font-monospace small">{{ $c->folio ?? 'S/F' }}</span></td>
                            <td><small>{{ $c->fecha?->format('d/m/Y') ?? '' }}</small></td>
                            <td class="text-end">RD$ {{ number_format($c->subtotal ?? 0, 2) }}</td>
                            <td class="text-end text-warning fw-semibold">RD$ {{ number_format($c->itbis_total ?? 0, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($c->retencion_isr ?? 0, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($c->retencion_itbis ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($c->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay compras en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ {{ number_format($compras->sum('subtotal'), 2) }}</td>
                        <td class="text-end py-3 text-warning">RD$ {{ number_format($compras->sum('itbis_total'), 2) }}</td>
                        <td class="text-end py-3 text-danger">RD$ {{ number_format($compras->sum('retencion_isr'), 2) }}</td>
                        <td class="text-end py-3 text-danger">RD$ {{ number_format($compras->sum('retencion_itbis'), 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($compras->sum('total'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
