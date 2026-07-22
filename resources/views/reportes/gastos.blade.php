@extends('layouts.app')
@section('title', 'Resumen de Gastos')

@push('styles')
@include('partials.premium-ui')
<style>
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-input:focus,
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-input,.filter-card .ui-select{min-width:100%;}}
#gastosTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#gastosTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#gastosTable tbody tr { transition:background .15s; }
#gastosTable tbody tr:hover { background:rgba(139,92,246,.04); }
#gastosTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #gastosTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #gastosTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #gastosTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #gastosTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Resumen de Gastos</h2>
                    <div class="ui-header-meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} gasto(s)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.gastos.csv', ['desde' => $desde, 'hasta' => $hasta, 'categoria' => $categoria]) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.gastos.pdf', ['desde' => $desde, 'hasta' => $hasta, 'categoria' => $categoria]) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4"><div class="ui-card-accent"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="ui-input" value="{{ $desde }}"></div>
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="ui-input" value="{{ $hasta }}"></div>
            <div class="col-auto">
                <select name="categoria" class="ui-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $key => $label)
                        <option value="{{ $key }}" {{ $categoria === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div></div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Cantidad</div><div class="ui-stat-value">{{ $cantidad }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Total General</div><div class="ui-stat-value text-warning">RD$ {{ number_format($totalGeneral, 2) }}</div></div></div>
        <div class="col-md-6"><div class="ui-card h-100"><div class="card-body p-3"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Por Categoría</small>
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

    <div class="ui-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="gastosTable" class="table align-middle mb-0">
                <thead>
                    <tr class="text-muted">
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
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="7" class="ps-4 py-3 text-end text-uppercase small">Total</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($totalGeneral, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#gastosTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
