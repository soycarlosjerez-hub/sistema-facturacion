@extends('layouts.app')

@section('title', $titulo)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
#fiscalesTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#fiscalesTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#fiscalesTable tbody tr { transition:background .15s; }
#fiscalesTable tbody tr:hover { background:rgba(139,92,246,.04); }
#fiscalesTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #fiscalesTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #fiscalesTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #fiscalesTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #fiscalesTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">{{ $titulo }}</h2>
                    <div class="ui-header-meta">Período: {{ ucfirst($periodo->translatedFormat('F Y')) }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.fiscales', ['tipo' => $tipo === '607' ? '606' : '607', 'mes' => $mes, 'anio' => $anio]) }}" 
                   class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-arrow-left-right me-1"></i> Cambiar a {{ $tipo === '607' ? '606 (Compras)' : '607 (Ventas)' }}
                </a>
                <a href="{{ route('reportes.fiscales.export', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-download me-1"></i> CSV DGII
                </a>
                <a href="{{ route('reportes.fiscales.txt', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-filetype-txt me-1"></i> TXT DGII
                </a>
                <a href="{{ route('reportes.fiscales.pdf', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-file-pdf me-1"></i> PDF
                </a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-grid me-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" action="{{ route('reportes.fiscales') }}" class="row g-2 align-items-end">
            <input type="hidden" name="tipo" value="{{ $tipo }}">
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Mes</label>
            </div>
            <div class="col-auto">
                <select name="mes" class="ui-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="anio" class="ui-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Registros</div>
                <div class="ui-stat-value">{{ number_format($cantidad) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Monto Facturado</div>
                <div class="ui-stat-value text-primary">RD$ {{ number_format($total_monto, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">ITBIS</div>
                <div class="ui-stat-value text-warning">RD$ {{ number_format($total_itbis, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Total General</div>
                <div class="ui-stat-value text-success">RD$ {{ number_format($total_general, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-table me-2"></i>Detalle de {{ $tipo === '607' ? 'Ventas' : 'Compras' }}
            </h5>
            <small class="text-muted">{{ $cantidad }} registro(s)</small>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="fiscalesTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">RNC/Cédula</th>
                        <th>{{ $tipo === '607' ? 'Cliente' : 'Proveedor' }}</th>
                        <th>NCF / Comprobante</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th class="text-end">Monto Facturado</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $r)
                        <tr>
                            <td class="ps-4 font-monospace small">{{ $r['rnc'] }}</td>
                            <td><span class="fw-semibold small">{{ $r['cliente'] ?? $r['proveedor'] }}</span></td>
                            <td><span class="font-monospace small">{{ $r['ncf'] }}</span></td>
                            <td><span class="badge bg-light text-dark rounded-pill">{{ $r['tipo_ncf'] }}</span></td>
                            <td><small>{{ $r['fecha'] }}</small></td>
                            <td class="text-end">RD$ {{ number_format($r['monto_facturado'], 2) }}</td>
                            <td class="text-end text-warning fw-semibold">RD$ {{ number_format($r['itbis'], 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($r['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">No hay registros para este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="5" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ {{ number_format($total_monto, 2) }}</td>
                        <td class="text-end py-3 text-warning">RD$ {{ number_format($total_itbis, 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($total_general, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="ui-card mt-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-bubble bg-soft-danger flex-shrink-0" style="width:52px;height:52px;font-size:1.3rem;">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Formato {{ $tipo }} - DGII</h6>
                    <p class="text-muted small mb-0">
                        Este reporte corresponde al formato {{ $tipo === '607' ? '607 (Ventas)' : '606 (Compras)' }} 
                        requerido por la DGII para la declaración mensual de ITBIS.
                        Los datos pueden exportarse en formato CSV para subir al portal de la DGII.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#fiscalesTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
