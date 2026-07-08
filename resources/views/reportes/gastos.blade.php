@extends('layouts.app')
@section('title', 'Resumen de Gastos')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #f97316 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(245,158,11,.25) !important;
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
                <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-white me-2"></i>Resumen de Gastos</h2>
                <p class="text-white-50 mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} gasto(s)</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.gastos.csv', ['desde' => $desde, 'hasta' => $hasta, 'categoria' => $categoria]) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.gastos.pdf', ['desde' => $desde, 'hasta' => $hasta, 'categoria' => $categoria]) }}" class="btn btn-danger rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-cash-coin"></i>
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
            <div class="col-auto">
                <select name="categoria" class="form-select border-0 bg-white">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $key => $label)
                        <option value="{{ $key }}" {{ $categoria === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Cantidad</small><h4 class="fw-bold mb-0 mt-1">{{ $cantidad }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total General</small><h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($totalGeneral, 2) }}</h4></div></div></div>
        <div class="col-md-6"><div class="premium-card card-accent blue h-100"><div class="card-body p-3"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Por Categoría</small>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @forelse($totalPorCategoria as $cat => $info)
                    <span class="badge bg-light text-dark border fw-normal px-3 py-2">
                        {{ $categorias[$cat] ?? $cat }}: <strong class="text-warning">RD$ {{ number_format($info['total'], 2) }}</strong>
                        <small class="text-muted">({{ $info['count'] }})</small>
                    </span>
                @empty
                    <span class="text-muted small">—</span>
                @endforelse
            </div>
        </div></div></div>
    </div>

    <div class="premium-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">#</th><th>Descripción</th><th>Categoría</th><th>Método de Pago</th><th>Comprobante</th><th>Usuario</th><th>Fecha</th><th class="text-end pe-4">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $g)
                        <tr>
                            <td class="ps-4">{{ str_pad($g->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="fw-semibold small">{{ $g->descripcion }}</span></td>
                            <td><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $categorias[$g->categoria] ?? $g->categoria ?? '—' }}</span></td>
                            <td><small>{{ $g->metodo_pago ?? '—' }}</small></td>
                            <td><span class="font-monospace small">{{ $g->comprobante ?? '—' }}</span></td>
                            <td><small>{{ $g->user?->name ?? '—' }}</small></td>
                            <td><small>{{ $g->fecha_gasto?->format('d/m/Y') ?? '' }}</small></td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($g->monto, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay gastos en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="7" class="ps-4 py-3 text-end text-uppercase small">Total</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($totalGeneral, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
