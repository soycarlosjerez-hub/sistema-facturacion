@extends('layouts.app')
@section('title', 'Libro de Compras')

@push('styles')
@include('partials.premium-ui')
<style>
.filter-card .ui-select:focus { border-color:#f59e0b!important;box-shadow:0 0 0 3px rgba(245,158,11,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#f59e0b,#d97706)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#d97706,#b45309)!important;box-shadow:0 6px 20px rgba(245,158,11,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
#comprasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#comprasTable tbody td { padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
#comprasTable tbody tr { transition:background .15s; }
#comprasTable tbody tr:hover { background:rgba(245,158,11,.04); }
body.dark-mode #comprasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #comprasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #comprasTable tbody tr:hover { background:rgba(245,158,11,.08); }
body.dark-mode #comprasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Libro de Compras</h2>
                    <div class="ui-header-meta">Registro fiscal de compras — Mes {{ \Carbon\Carbon::create($anio, $mes, 1)->translatedFormat('F') }} {{ $anio }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('libros.compras.csv', compact('mes','anio')) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</a>
                <a href="{{ route('libros.compras.pdf', compact('mes','anio')) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
                <a href="{{ route('libros.ventas.index', compact('mes','anio')) }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-arrow-left me-1"></i>Ir a Libro de Ventas</a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" action="{{ route('libros.compras.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Mes</label>
            </div>
            <div class="col-auto">
                <select name="mes" class="ui-select">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $mes==$m?'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Año</label>
            </div>
            <div class="col-auto">
                <input type="number" name="anio" class="ui-select" value="{{ $anio }}" min="2020" max="2030" style="width:90px;">
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Proveedor</label>
            </div>
            <div class="col-auto">
                <input type="text" name="proveedor" class="ui-select" placeholder="Buscar..." value="{{ request('proveedor') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-search me-1"></i>Filtrar</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Resúmenes -->
    @if($resumenGeneral)
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3">
                <div class="text-warning mb-1"><i class="bi bi-cart-check fs-4"></i></div>
                <div class="ui-stat-label">Total Compras</div>
                <div class="ui-stat-value">{{ number_format($resumenGeneral->total) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3">
                <div class="text-warning mb-1"><i class="bi bi-cash-stack fs-4"></i></div>
                <div class="ui-stat-label">Gran Total Compras</div>
                <div class="ui-stat-value">RD$ {{ number_format($resumenGeneral->gran_total, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3">
                <div class="text-primary mb-1"><i class="bi bi-calculator fs-4"></i></div>
                <div class="ui-stat-label">Subtotal Gravado</div>
                <div class="ui-stat-value">RD$ {{ number_format($resumenGeneral->gran_subtotal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3">
                <div class="text-info mb-1"><i class="bi bi-percent fs-4"></i></div>
                <div class="ui-stat-label">ITBIS Creditable</div>
                <div class="ui-stat-value">RD$ {{ number_format($resumenGeneral->gran_itbis, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3" style="border:1px solid rgba(220,53,69,.2);">
                <div class="text-danger mb-1"><i class="bi bi-arrow-return-left fs-4"></i></div>
                <div class="ui-stat-label">ITBIS Retenido</div>
                <div class="ui-stat-value">RD$ {{ number_format($retencionesResumen['itbis'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3" style="border:1px solid rgba(220,53,69,.2);">
                <div class="text-danger mb-1"><i class="bi bi-percent fs-4"></i></div>
                <div class="ui-stat-label">ISR Retenido</div>
                <div class="ui-stat-value">RD$ {{ number_format($retencionesResumen['isr'], 2) }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Totales por proveedor -->
    @if(count($totalesProveedor) > 0)
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Resumen por Proveedor</h5>
        </div>
        <div class="table-responsive px-3 py-3">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Proveedor</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">ITBIS Ret.</th>
                        <th class="text-end">ISR Ret.</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totalesProveedor as $tp)
                    <tr>
                        <td class="ps-4 fw-semibold small">{{ $tp->proveedor->nombre ?? 'N/A' }}</td>
                        <td class="text-end">RD$ {{ number_format($tp->subtotal, 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($tp->itbis, 2) }}</td>
                        <td class="text-end text-danger">RD$ {{ number_format($tp->itbis_retenido, 2) }}</td>
                        <td class="text-end text-danger">RD$ {{ number_format($tp->isr_retenido, 2) }}</td>
                        <td class="text-end pe-4 fw-bold">RD$ {{ number_format($tp->total_compras, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tabla de compras -->
    <div class="ui-card overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Detalle de Compras</h5>
            <small class="text-muted">{{ $compras->total() }} registro(s)</small>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="comprasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>RNC</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Ret. ITBIS</th>
                        <th class="text-end">Ret. ISR</th>
                        <th class="text-end">Total</th>
                        <th class="text-end pe-4">Total Neto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $i => $c)
                    <tr>
                        <td class="ps-4 font-monospace small">{{ $compras->firstItem() + $i }}</td>
                        <td><small>{{ $c->fecha->format('d/m/Y') }}</small></td>
                        <td><span class="fw-semibold small">{{ $c->proveedor->nombre ?? 'N/A' }}</span></td>
                        <td><span class="font-monospace small">{{ $c->proveedor->rnc ?? '' }}</span></td>
                        <td class="text-end">RD$ {{ number_format($c->subtotal, 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($c->itbis_total, 2) }}</td>
                        <td class="text-end text-danger">RD$ {{ number_format($c->retencion_itbis, 2) }}</td>
                        <td class="text-end text-danger">RD$ {{ number_format($c->retencion_isr, 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($c->total, 2) }}</td>
                        <td class="text-end pe-4">RD$ {{ number_format($c->total_neto ?? $c->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2 mb-0">No hay compras registradas en este período</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2 px-3">
            {{ $compras->links() }}
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#comprasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [0, 1, 2, 3, 9] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
