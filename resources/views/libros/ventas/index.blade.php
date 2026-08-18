@extends('layouts.app')
@section('title', 'Libro de Ventas')

@push('styles')
@include('partials.premium-ui')
<style>
.badge-anulada { background:#dc3545!important; }
.badge-completada { background:#198754!important; }
.badge-pendiente { background:#ffc107!important;color:#333!important; }
.badge-cuenta_abierta { background:#0dcaf0!important;color:#333!important; }
.filter-card .ui-select:focus { border-color:#10b981!important;box-shadow:0 0 0 3px rgba(16,185,129,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#10b981,#059669)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#059669,#047857)!important;box-shadow:0 6px 20px rgba(16,185,129,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
#ventasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#ventasTable tbody td { padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
#ventasTable tbody tr { transition:background .15s; }
#ventasTable tbody tr:hover { background:rgba(16,185,129,.04); }
body.dark-mode #ventasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #ventasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #ventasTable tbody tr:hover { background:rgba(16,185,129,.08); }
body.dark-mode #ventasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Libro de Ventas</h2>
                    <div class="ui-header-meta">Registro fiscal conforme normativa DGII — Mes {{ \Carbon\Carbon::create($anio, $mes, 1)->translatedFormat('F') }} {{ $anio }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('libros.ventas.csv', compact('mes','anio')) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</a>
                <a href="{{ route('libros.ventas.pdf', compact('mes','anio')) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
                <a href="{{ route('libros.compras.index', compact('mes','anio')) }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-arrow-left me-1"></i>Ir a Libro de Compras</a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" action="{{ route('libros.ventas.index') }}" class="row g-2 align-items-end">
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
                <label class="ui-label small fw-semibold mb-0">Cliente</label>
            </div>
            <div class="col-auto">
                <input type="text" name="cliente" class="ui-select" placeholder="Buscar..." value="{{ request('cliente') }}">
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Tipo NCF</label>
            </div>
            <div class="col-auto">
                <select name="tipo_ncf" class="ui-select">
                    <option value="">Todos</option>
                    <option value="e31" {{ request('tipo_ncf')=='e31'?'selected':'' }}>E31 (B1)</option>
                    <option value="e32" {{ request('tipo_ncf')=='e32'?'selected':'' }}>E32 (B2)</option>
                    <option value="e33" {{ request('tipo_ncf')=='e33'?'selected':'' }}>E33 (Débito)</option>
                    <option value="e34" {{ request('tipo_ncf')=='e34'?'selected':'' }}>E34 (Crédito)</option>
                    <option value="e41" {{ request('tipo_ncf')=='e41'?'selected':'' }}>E41 (Compra)</option>
                </select>
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
                <div class="text-success mb-1"><i class="bi bi-receipt fs-4"></i></div>
                <div class="ui-stat-label">Total Facturas</div>
                <div class="ui-stat-value">{{ number_format($resumenGeneral->total) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="ui-stat text-center p-3">
                <div class="text-success mb-1"><i class="bi bi-cash-stack fs-4"></i></div>
                <div class="ui-stat-label">Gran Total Ventas</div>
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
                <div class="text-warning mb-1"><i class="bi bi-percent fs-4"></i></div>
                <div class="ui-stat-label">ITBIS Cobrado</div>
                <div class="ui-stat-value">RD$ {{ number_format($resumenGeneral->gran_itbis, 2) }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Totales por tipo NCF -->
    @if(count($totales) > 0)
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-tags me-2"></i>Resumen por Tipo de NCF</h5>
        </div>
        <div class="table-responsive px-3 py-3">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Tipo NCF</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totales as $t)
                    <tr>
                        <td class="ps-4"><code>{{ strtoupper($t->ncf_tipo) }}</code></td>
                        <td class="text-center">{{ number_format($t->cantidad) }}</td>
                        <td class="text-end">RD$ {{ number_format($t->subtotal, 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($t->itbis_total, 2) }}</td>
                        <td class="text-end pe-4 fw-bold">RD$ {{ number_format($t->total_ventas, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tabla de ventas -->
    <div class="ui-card overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Detalle de Ventas</h5>
            <small class="text-muted">{{ $ventas->total() }} registro(s)</small>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="ventasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Fecha</th>
                        <th>NCF</th>
                        <th>Cliente</th>
                        <th>RNC/Cédula</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Estado</th>
                        <th>Vendedor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $i => $v)
                    <tr>
                        <td class="ps-4 font-monospace small">{{ $ventas->firstItem() + $i }}</td>
                        <td><small>{{ $v->created_at->format('d/m/Y') }}</small></td>
                        <td><span class="font-monospace small">{{ $v->ncf ?? 'S/N' }}</span></td>
                        <td><span class="fw-semibold small">{{ $v->cliente->nombre ?? 'Consumidor Final' }}</span></td>
                        <td><span class="font-monospace small">{{ $v->cliente->rnc_cedula ?? '00000000000' }}</span></td>
                        <td class="text-end">RD$ {{ number_format($v->subtotal, 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($v->impuestos, 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($v->total, 2) }}</td>
                        <td class="text-center"><span class="badge badge-{{ $v->estado }}">{{ strtoupper(str_replace('_',' ',$v->estado)) }}</span></td>
                        <td><small>{{ $v->usuario->name ?? '' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2 px-3">
            {{ $ventas->links() }}
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#ventasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            emptyTable: 'No hay ventas registradas en este período'
        },
        columnDefs: [{ orderable: false, targets: [5,6,7,8,9] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
