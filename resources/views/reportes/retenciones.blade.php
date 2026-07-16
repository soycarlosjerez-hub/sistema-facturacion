@extends('layouts.app')
@section('title', 'Reporte de Retenciones')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(59,130,246,.25) !important;
}
.premium-header .btn-outline-secondary {
    color: #fff !important;
    border-color: rgba(255,255,255,.6) !important;
    background: rgba(255,255,255,.1) !important;
}
.premium-header .btn-outline-secondary:hover {
    background: rgba(255,255,255,.25) !important;
    border-color: #fff !important;
}
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.filter-card > .card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color:#4f46e5!important;box-shadow:0 0 0 3px rgba(79,70,229,.15)!important; }
.filter-card .btn-primary { background:linear-gradient(135deg,#4f46e5,#3b82f6)!important;border:none!important; }
.filter-card .btn-primary:hover { background:linear-gradient(135deg,#4338ca,#2563eb)!important;box-shadow:0 6px 20px rgba(79,70,229,.4)!important; }
@media(max-width:575.98px){.filter-card .form-control,.filter-card .form-select{min-width:100%;}}
.premium-stat-card { background:rgba(255,255,255,.85);border-radius:1.2rem;box-shadow:0 4px 24px rgba(0,0,0,.04);transition:transform .2s,box-shadow .2s;position:relative;overflow:hidden; }
.premium-stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,0,0,.08); }
.premium-stat-card .stat-label { font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:700;margin-bottom:4px; }
.premium-stat-card .stat-value { font-size:1.5rem;font-weight:800; }
body.dark-mode .premium-stat-card { background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.06); }
body.dark-mode .premium-stat-card .stat-label { color:#94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color:#f1f5f9; }
#retencionesComprasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#retencionesComprasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#retencionesComprasTable tbody tr { transition:background .15s; }
#retencionesComprasTable tbody tr:hover { background:rgba(59,130,246,.04); }
#retencionesVentasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#retencionesVentasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#retencionesVentasTable tbody tr { transition:background .15s; }
#retencionesVentasTable tbody tr:hover { background:rgba(59,130,246,.04); }
body.dark-mode #retencionesComprasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #retencionesComprasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #retencionesComprasTable tbody tr:hover { background:rgba(59,130,246,.08); }
body.dark-mode #retencionesVentasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #retencionesVentasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #retencionesVentasTable tbody tr:hover { background:rgba(59,130,246,.08); }
body.dark-mode #retencionesComprasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
body.dark-mode #retencionesVentasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Reporte de Retenciones</h2>
                <p class="mb-0" style="color:rgba(255,255,255,.8);">Período: {{ ucfirst(Carbon\Carbon::create()->month($mes)->translatedFormat('F')) }} {{ $anio }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.retenciones.csv', request()->all()) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card filter-card mb-4">
        <div class="card-accent blue"></div>
        <div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Mes</label></div>
            <div class="col-auto">
                <select name="mes" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="anio" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tipo" class="form-select">
                    <option value="compras" {{ $tipo === 'compras' ? 'selected' : '' }}>Compras</option>
                    <option value="ventas" {{ $tipo === 'ventas' ? 'selected' : '' }}>Ventas</option>
                    <option value="ambos" {{ $tipo === 'ambos' ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div>
</div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="premium-stat-card text-center p-3"><div class="stat-label">Retención ISR</div><div class="stat-value text-primary">RD$ {{ number_format($totalRetIsr, 2) }}</div></div></div>
        <div class="col-md-4"><div class="premium-stat-card text-center p-3"><div class="stat-label">Retención ITBIS</div><div class="stat-value text-warning">RD$ {{ number_format($totalRetItbis, 2) }}</div></div></div>
        <div class="col-md-4"><div class="premium-stat-card text-center p-3"><div class="stat-label">Total Retenido</div><div class="stat-value text-danger">RD$ {{ number_format($totalGeneral, 2) }}</div></div></div>
    </div>

    @if($tipo === 'compras' || $tipo === 'ambos')
    <div class="premium-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success me-2"></i>Retenciones en Compras</h5></div>
        <div class="table-responsive px-3 py-3">
            <table id="retencionesComprasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
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
    <div class="premium-card overflow-hidden">
        <div class="card-header bg-white border-0 py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-primary me-2"></i>Retenciones en Ventas</h5></div>
        <div class="table-responsive px-3 py-3">
            <table id="retencionesVentasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
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
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#retencionesComprasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [4,5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#retencionesVentasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [4,5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
