@extends('layouts.app')
@section('title', 'Reporte de Retenciones')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(239,68,68,0.4);
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
            <h2 class="fw-bold mb-1"><i class="bi bi-percent" style="color:#7c3aed;me-2"></i>Reporte de Retenciones</h2>
            <p class="text-muted mb-0">Período: {{ ucfirst(Carbon\Carbon::create()->month($mes)->translatedFormat('F')) }} {{ $anio }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.retenciones.csv', request()->all()) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
        </div>
    </div>

    <div class="filter-card p-3 mb-4">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Mes</label></div>
            <div class="col-auto">
                <select name="mes" class="form-select border-0 bg-white">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="anio" class="form-select border-0 bg-white">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tipo" class="form-select border-0 bg-white">
                    <option value="compras" {{ $tipo === 'compras' ? 'selected' : '' }}>Compras</option>
                    <option value="ventas" {{ $tipo === 'ventas' ? 'selected' : '' }}>Ventas</option>
                    <option value="ambos" {{ $tipo === 'ambos' ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Retención ISR</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalRetIsr, 2) }}</h4></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Retención ITBIS</small><h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($totalRetItbis, 2) }}</h4></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total Retenido</small><h4 class="fw-bold mb-0 mt-1 text-danger">RD$ {{ number_format($totalGeneral, 2) }}</h4></div></div></div>
    </div>

    @if($tipo === 'compras' || $tipo === 'ambos')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success me-2"></i>Retenciones en Compras</h5></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Proveedor</th><th>RNC</th><th>Documento</th><th>Fecha</th><th class="text-end">Total</th><th class="text-end">Ret ISR</th><th class="text-end">Ret ITBIS</th><th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $c)
                        <tr>
                            <td class="ps-4"><span class="fw-semibold small">{{ $c->proveedor?->nombre ?? 'N/A' }}</span></td>
                            <td><span class="font-monospace small">{{ $c->proveedor?->rnc ?? '' }}</span></td>
                            <td><span class="font-monospace small">{{ $c->folio ?? '#' . $c->id }}</span></td>
                            <td><small>{{ $c->fecha?->format('d/m/Y') ?? '' }}</small></td>
                            <td class="text-end">RD$ {{ number_format($c->total, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($c->retencion_isr ?? 0, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($c->retencion_itbis ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format(($c->retencion_isr ?? 0) + ($c->retencion_itbis ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-3 text-muted"><small>Sin retenciones en compras</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($tipo === 'ventas' || $tipo === 'ambos')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-primary me-2"></i>Retenciones en Ventas</h5></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Cliente</th><th>RNC</th><th>Documento</th><th>Fecha</th><th class="text-end">Total</th><th class="text-end">Ret ISR</th><th class="text-end">Ret ITBIS</th><th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                        <tr>
                            <td class="ps-4"><span class="fw-semibold small">{{ $v->cliente?->nombre ?? 'N/A' }}</span></td>
                            <td><span class="font-monospace small">{{ $v->cliente?->rnc_cedula ?? '' }}</span></td>
                            <td><span class="font-monospace small">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                            <td><small>{{ $v->created_at->format('d/m/Y') }}</small></td>
                            <td class="text-end">RD$ {{ number_format($v->total, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($v->retencion_isr ?? 0, 2) }}</td>
                            <td class="text-end text-danger">RD$ {{ number_format($v->retencion_itbis ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format(($v->retencion_isr ?? 0) + ($v->retencion_itbis ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-3 text-muted"><small>Sin retenciones en ventas</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
