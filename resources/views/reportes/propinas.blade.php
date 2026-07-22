@extends('layouts.app')
@section('title', 'Reporte de Propinas')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-input:focus,
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-input,.filter-card .ui-select{min-width:100%;}}
#propinasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#propinasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#propinasTable tbody tr { transition:background .15s; }
#propinasTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #propinasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #propinasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #propinasTable tbody tr:hover { background:rgba(139,92,246,.08); }
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
                    <h2 class="ui-header-title">Reporte de Propinas</h2>
                    <div class="ui-header-meta">Propinas por mesero</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-0">Desde</label>
                    <input type="date" name="desde" class="ui-input" value="{{ $desde }}">
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-0">Hasta</label>
                    <input type="date" name="hasta" class="ui-input" value="{{ $hasta }}">
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Total Propinas</div>
                <div class="ui-stat-value text-success">RD$ {{ number_format($totalGlobal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Órdenes con Propina</div>
                <div class="ui-stat-value text-primary">{{ $ordenesConPropina }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Promedio Global</div>
                <div class="ui-stat-value text-warning">RD$ {{ number_format($ordenesConPropina > 0 ? $totalGlobal / $ordenesConPropina : 0, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Tabla por mesero --}}
    <div class="ui-card">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
            <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Propinas por Mesero</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive px-3 py-3">
                <table id="propinasTable" class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mesero</th>
                            <th class="text-center">Órdenes</th>
                            <th class="text-end">Total Propinas</th>
                            <th class="text-end">Promedio x Orden</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($propinas as $p)
                        <tr>
                            <td class="fw-medium">{{ $p->usuario?->name ?? '—' }}</td>
                            <td class="text-center">{{ $p->total_ordenes }}</td>
                            <td class="text-end fw-bold text-success">RD$ {{ number_format($p->total_propinas, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($p->promedio_propina, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#propinasTable').DataTable({
        responsive: true, pageLength: 10, lengthMenu: [[5,10,25,-1],[5,10,25,'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [2,3] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
