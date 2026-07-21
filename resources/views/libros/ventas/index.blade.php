@extends('layouts.app')
@section('title', 'Libro de Ventas')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(16,185,129,.25) !important;
}
.filter-card > .card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; background:linear-gradient(90deg,#10b981,#059669); }
.filter-card .form-control:focus, .filter-card .form-select:focus { border-color:#059669!important;box-shadow:0 0 0 3px rgba(5,150,105,.15)!important; }
.premium-stat-card { background:rgba(255,255,255,.85);border-radius:1.2rem;box-shadow:0 4px 24px rgba(0,0,0,.04);position:relative;overflow:hidden; }
.premium-stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,0,0,.08); }
.premium-stat-card .stat-label { font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:700;margin-bottom:4px; }
.premium-stat-card .stat-value { font-size:1.5rem;font-weight:800; }
body.dark-mode .premium-stat-card { background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.06); }
body.dark-mode .premium-stat-card .stat-label { color:#94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color:#f1f5f9; }
#ventasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#ventasTable tbody td { padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
#ventasTable tbody tr:hover { background:rgba(16,185,129,.04); }
body.dark-mode #ventasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #ventasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #ventasTable tbody tr:hover { background:rgba(16,185,129,.08); }
.badge-anulada { background:#dc3545!important; }
.badge-completada { background:#198754!important; }
.badge-pendiente { background:#ffc107!important;color:#333!important; }
.badge-cuenta_abierta { background:#0dcaf0!important;color:#333!important; }
</style>
@endpush

@section('content')
<div class="premium-header text-white py-4 mb-4 rounded-4">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold"><i class="bi bi-journal-bookmark-fill me-2"></i>Libro de Ventas</h4>
                <small class="opacity-75">Registro fiscal conforme normativa DGII — Mes {{ \Carbon\Carbon::create($anio, $mes, 1)->format('F') }} {{ $anio }}</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('libros.ventas.csv', compact('mes','anio')) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                </a>
                <a href="{{ route('libros.ventas.pdf', compact('mes','anio')) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('libros.compras.index', compact('mes','anio')) }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i>Ir a Libro de Compras
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- Filtros -->
    <div class="card premium-card mb-4 filter-card">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('libros.ventas.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label form-label-sm mb-1">Mes</label>
                        <select name="mes" class="form-select form-select-sm">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ $mes==$m?'selected':'' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label form-label-sm mb-1">Año</label>
                        <input type="number" name="anio" class="form-control form-control-sm" value="{{ $anio }}" min="2020" max="2030">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label form-label-sm mb-1">Cliente</label>
                        <input type="text" name="cliente" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('cliente') }}">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label form-label-sm mb-1">Tipo NCF</label>
                        <select name="tipo_ncf" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="e31" {{ request('tipo_ncf')=='e31'?'selected':'' }}>E31 (B1)</option>
                            <option value="e32" {{ request('tipo_ncf')=='e32'?'selected':'' }}>E32 (B2)</option>
                            <option value="e33" {{ request('tipo_ncf')=='e33'?'selected':'' }}>E33 (Débito)</option>
                            <option value="e34" {{ request('tipo_ncf')=='e34'?'selected':'' }}>E34 (Crédito)</option>
                            <option value="e41" {{ request('tipo_ncf')=='e41'?'selected':'' }}>E41 (Compra)</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resúmenes -->
    @if($resumenGeneral)
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card">
                <div class="card-body">
                    <div class="stat-label">Total Facturas</div>
                    <div class="stat-value text-success">{{ number_format($resumenGeneral->total) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card">
                <div class="card-body">
                    <div class="stat-label">Gran Total Ventas</div>
                    <div class="stat-value">${{ number_format($resumenGeneral->gran_total, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card">
                <div class="card-body">
                    <div class="stat-label">Subtotal Gravado</div>
                    <div class="stat-value">${{ number_format($resumenGeneral->gran_subtotal, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card">
                <div class="card-body">
                    <div class="stat-label">ITBIS Cobrado</div>
                    <div class="stat-value text-warning">${{ number_format($resumenGeneral->gran_itbis, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Totales por tipo NCF -->
    @if(count($totales) > 0)
    <div class="card premium-card mb-4">
        <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <h6 class="fw-bold"><i class="bi bi-tags me-2"></i>Resumen por Tipo de NCF</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tipo NCF</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">ITBIS</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totales as $t)
                        <tr>
                            <td><code>{{ strtoupper($t->ncf_tipo) }}</code></td>
                            <td class="text-center">{{ number_format($t->cantidad) }}</td>
                            <td class="text-end">${{ number_format($t->subtotal, 2) }}</td>
                            <td class="text-end">${{ number_format($t->itbis_total, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($t->total_ventas, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de ventas -->
    <div class="card premium-card">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Detalle de Ventas</h6>
            <small class="text-muted">{{ $ventas->total() }} registros</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ventasTable">
                    <thead>
                        <tr>
                            <th>#</th>
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
                        @forelse($ventas as $i => $v)
                        <tr>
                            <td>{{ $ventas->firstItem() + $i }}</td>
                            <td>{{ $v->created_at->format('d/m/Y') }}</td>
                            <td><code>{{ $v->ncf ?? 'S/N' }}</code></td>
                            <td>{{ $v->cliente->nombre ?? 'Consumidor Final' }}</td>
                            <td>{{ $v->cliente->rnc_cedula ?? '00000000000' }}</td>
                            <td class="text-end">${{ number_format($v->subtotal, 2) }}</td>
                            <td class="text-end">${{ number_format($v->impuestos, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($v->total, 2) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $v->estado }}">{{ strtoupper(str_replace('_',' ',$v->estado)) }}</span>
                            </td>
                            <td>{{ $v->usuario->name ?? '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay ventas registradas en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2">
            {{ $ventas->links() }}
        </div>
    </div>
</div>
@endsection
