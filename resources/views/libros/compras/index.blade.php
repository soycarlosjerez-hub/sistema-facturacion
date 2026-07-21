@extends('layouts.app')
@section('title', 'Libro de Compras')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(245,158,11,.25) !important;
}
.filter-card > .card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; background:linear-gradient(90deg,#f59e0b,#d97706); }
.filter-card .form-control:focus, .filter-card .form-select:focus { border-color:#d97706!important;box-shadow:0 0 0 3px rgba(217,119,6,.15)!important; }
.premium-stat-card { background:rgba(255,255,255,.85);border-radius:1.2rem;box-shadow:0 4px 24px rgba(0,0,0,.04);position:relative;overflow:hidden; }
.premium-stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,0,0,.08); }
.premium-stat-card .stat-label { font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:700;margin-bottom:4px; }
.premium-stat-card .stat-value { font-size:1.5rem;font-weight:800; }
body.dark-mode .premium-stat-card { background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.06); }
body.dark-mode .premium-stat-card .stat-label { color:#94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color:#f1f5f9; }
#comprasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#comprasTable tbody td { padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
#comprasTable tbody tr:hover { background:rgba(245,158,11,.04); }
body.dark-mode #comprasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #comprasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #comprasTable tbody tr:hover { background:rgba(245,158,11,.08); }
</style>
@endpush

@section('content')
<div class="premium-header text-white py-4 mb-4 rounded-4">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold"><i class="bi bi-cart-check-fill me-2"></i>Libro de Compras</h4>
                <small class="opacity-75">Registro fiscal de compras — Mes {{ \Carbon\Carbon::create($anio, $mes, 1)->format('F') }} {{ $anio }}</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('libros.compras.csv', compact('mes','anio')) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                </a>
                <a href="{{ route('libros.compras.pdf', compact('mes','anio')) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('libros.ventas.index', compact('mes','anio')) }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i>Ir a Libro de Ventas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- Filtros -->
    <div class="card premium-card mb-4 filter-card">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('libros.compras.index') }}">
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
                        <label class="form-label form-label-sm mb-1">Proveedor</label>
                        <input type="text" name="proveedor" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('proveedor') }}">
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
                    <div class="stat-label">Total Compras</div>
                    <div class="stat-value text-warning">{{ number_format($resumenGeneral->total) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card">
                <div class="card-body">
                    <div class="stat-label">Gran Total Compras</div>
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
                    <div class="stat-label">ITBIS Creditable</div>
                    <div class="stat-value text-info">${{ number_format($resumenGeneral->gran_itbis, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card border-danger">
                <div class="card-body">
                    <div class="stat-label">ITBIS Retenido</div>
                    <div class="stat-value text-danger">${{ number_format($retencionesResumen['itbis'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card border-danger">
                <div class="card-body">
                    <div class="stat-label">ISR Retenido</div>
                    <div class="stat-value text-danger">${{ number_format($retencionesResumen['isr'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Totales por proveedor -->
    @if(count($totalesProveedor) > 0)
    <div class="card premium-card mb-4">
        <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <h6 class="fw-bold"><i class="bi bi-building me-2"></i>Resumen por Proveedor</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">ITBIS</th>
                            <th class="text-end">ITBIS Ret.</th>
                            <th class="text-end">ISR Ret.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totalesProveedor as $tp)
                        <tr>
                            <td>{{ $tp->proveedor->nombre ?? 'N/A' }}</td>
                            <td class="text-end">${{ number_format($tp->subtotal, 2) }}</td>
                            <td class="text-end">${{ number_format($tp->itbis, 2) }}</td>
                            <td class="text-end text-danger">${{ number_format($tp->itbis_retenido, 2) }}</td>
                            <td class="text-end text-danger">${{ number_format($tp->isr_retenido, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($tp->total_compras, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de compras -->
    <div class="card premium-card">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Detalle de Compras</h6>
            <small class="text-muted">{{ $compras->total() }} registros</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="comprasTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>RNC</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">ITBIS</th>
                            <th class="text-end">Ret. ITBIS</th>
                            <th class="text-end">Ret. ISR</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Total Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $i => $c)
                        <tr>
                            <td>{{ $compras->firstItem() + $i }}</td>
                            <td>{{ $c->fecha->format('d/m/Y') }}</td>
                            <td>{{ $c->proveedor->nombre ?? 'N/A' }}</td>
                            <td>{{ $c->proveedor->rnc ?? '' }}</td>
                            <td class="text-end">${{ number_format($c->subtotal, 2) }}</td>
                            <td class="text-end">${{ number_format($c->itbis_total, 2) }}</td>
                            <td class="text-end text-danger">${{ number_format($c->retencion_itbis, 2) }}</td>
                            <td class="text-end text-danger">${{ number_format($c->retencion_isr, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($c->total, 2) }}</td>
                            <td class="text-end">${{ number_format($c->total_neto ?? $c->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay compras registradas en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2">
            {{ $compras->links() }}
        </div>
    </div>
</div>
@endsection
