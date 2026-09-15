@extends('layouts.app')
@section('title', 'Reporte de Retenciones')

@push('styles')
@include('partials.premium-ui')
<style>
.filter-card .ui-select:focus { border-color:#8b5cf6!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
#retencionesComprasTable thead th, #retencionesVentasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#retencionesComprasTable tbody td, #retencionesVentasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#retencionesComprasTable tbody tr, #retencionesVentasTable tbody tr { transition:background .15s; }
#retencionesComprasTable tbody tr:hover { background:rgba(139,92,246,.04); }
#retencionesVentasTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #retencionesComprasTable thead th, body.dark-mode #retencionesVentasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #retencionesComprasTable tbody td, body.dark-mode #retencionesVentasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #retencionesComprasTable tbody tr:hover, body.dark-mode #retencionesVentasTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #retencionesComprasTable tfoot td, body.dark-mode #retencionesVentasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
.nav-tabs-custom .nav-link { color:#64748b;font-weight:600;border:none;padding:12px 24px;border-bottom:3px solid transparent;transition:all .2s; }
.nav-tabs-custom .nav-link.active { color:#8b5cf6;border-bottom-color:#8b5cf6;background:transparent; }
.nav-tabs-custom .nav-link:hover:not(.active) { color:#8b5cf6;background:rgba(139,92,246,.05); }
body.dark-mode .nav-tabs-custom .nav-link { color:#94a3b8; }
body.dark-mode .nav-tabs-custom .nav-link.active { color:#a78bfa; }
body.dark-mode .nav-tabs-custom .nav-link:hover:not(.active) { color:#a78bfa;background:rgba(139,92,246,.1); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-percent"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Reporte de Retenciones</h2>
                    <div class="ui-header-meta">Período: {{ ucfirst(Carbon\Carbon::create()->month($mes)->translatedFormat('F')) }} {{ $anio }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.retenciones.csv', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Mes</label>
            </div>
            <div class="col-auto">
                <select name="mes" class="ui-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Año</label>
            </div>
            <div class="col-auto">
                <select name="anio" class="ui-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Tipo</label>
            </div>
            <div class="col-auto">
                <select name="tipo" class="ui-select">
                    <option value="compras" {{ $tipo === 'compras' ? 'selected' : '' }}>Compras</option>
                    <option value="ventas" {{ $tipo === 'ventas' ? 'selected' : '' }}>Ventas</option>
                    <option value="ambos" {{ $tipo === 'ambos' ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="text-primary mb-1"><i class="bi bi-cart-check fs-4"></i></div>
                <div class="ui-stat-label">Retención ISR (Compras)</div>
                <div class="ui-stat-value">RD$ {{ number_format($totalRetIsr, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="text-warning mb-1"><i class="bi bi-percent fs-4"></i></div>
                <div class="ui-stat-label">Retención ITBIS</div>
                <div class="ui-stat-value">RD$ {{ number_format($totalRetItbis, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="text-danger mb-1"><i class="bi bi-wallet2 fs-4"></i></div>
                <div class="ui-stat-label">Total Retenido</div>
                <div class="ui-stat-value">RD$ {{ number_format($totalGeneral, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    @if($tipo === 'compras' || $tipo === 'ambos')
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success me-2"></i>Retenciones en Compras</h5>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="retencionesComprasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Proveedor</th>
                        <th>RNC</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Ret ISR</th>
                        <th class="text-end">Ret ITBIS</th>
                        <th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $c)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($tipo === 'ventas' || $tipo === 'ambos')
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-primary me-2"></i>Retenciones en Ventas</h5>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="retencionesVentasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Cliente</th>
                        <th>RNC</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Ret ISR</th>
                        <th class="text-end">Ret ITBIS</th>
                        <th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $v)
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
                    @endforeach
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
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'Sin retenciones en compras' },
        columnDefs: [{ orderable: false, targets: [4,5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#retencionesVentasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'Sin retenciones en ventas' },
        columnDefs: [{ orderable: false, targets: [4,5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
