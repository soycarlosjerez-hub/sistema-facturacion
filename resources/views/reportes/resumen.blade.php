@extends('layouts.app')

@section('title', 'Resumen Fiscal Anual')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-bar-chart-line text-danger me-2"></i>
                Resumen Fiscal Anual
            </h2>
            <p class="text-muted mb-0">Comparativo mensual ITBIS - Ventas vs Compras</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            <form method="GET" action="{{ route('reportes.resumen') }}" class="d-flex gap-2 align-items-center">
                <select name="anio" class="form-select border-0 bg-white" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="{{ route('reportes.fiscales') }}" class="btn btn-outline-danger rounded-pill">
                <i class="bi bi-file-earmark-text me-1"></i> Ir a 607/606
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i>Resumen {{ $anio }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Mes</th>
                        <th class="text-center" colspan="3">Ventas (607)</th>
                        <th class="text-center" colspan="3">Compras (606)</th>
                        <th class="text-center pe-4 py-3">ITBIS a Pagar</th>
                    </tr>
                    <tr class="text-muted" style="font-size:.65rem;text-transform:uppercase;">
                        <th class="ps-4"></th>
                        <th class="text-end">Cant.</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Cant.</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalVentasCant = $totalVentasMonto = $totalVentasItbis = 0;
                        $totalComprasCant = $totalComprasMonto = $totalComprasItbis = 0;
                    @endphp
                    @foreach($meses as $m)
                        @php
                            $itbisPagar = $m['ventas_itbis'] - $m['compras_itbis'];
                            $totalVentasCant += $m['ventas_cant'];
                            $totalVentasMonto += $m['ventas_total'];
                            $totalVentasItbis += $m['ventas_itbis'];
                            $totalComprasCant += $m['compras_cant'];
                            $totalComprasMonto += $m['compras_total'];
                            $totalComprasItbis += $m['compras_itbis'];
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $m['label'] }}</td>
                            <td class="text-end">{{ number_format($m['ventas_cant']) }}</td>
                            <td class="text-end">RD$ {{ number_format($m['ventas_total'], 2) }}</td>
                            <td class="text-end text-warning fw-semibold">RD$ {{ number_format($m['ventas_itbis'], 2) }}</td>
                            <td class="text-end">{{ number_format($m['compras_cant']) }}</td>
                            <td class="text-end">RD$ {{ number_format($m['compras_total'], 2) }}</td>
                            <td class="text-end text-info fw-semibold">RD$ {{ number_format($m['compras_itbis'], 2) }}</td>
                            <td class="text-end pe-4 fw-bold {{ $itbisPagar >= 0 ? 'text-danger' : 'text-success' }}">
                                RD$ {{ number_format($itbisPagar, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="ps-4 text-uppercase small">Total</td>
                        <td class="text-end">{{ number_format($totalVentasCant) }}</td>
                        <td class="text-end">RD$ {{ number_format($totalVentasMonto, 2) }}</td>
                        <td class="text-end text-warning">RD$ {{ number_format($totalVentasItbis, 2) }}</td>
                        <td class="text-end">{{ number_format($totalComprasCant) }}</td>
                        <td class="text-end">RD$ {{ number_format($totalComprasMonto, 2) }}</td>
                        <td class="text-end text-info">RD$ {{ number_format($totalComprasItbis, 2) }}</td>
                        <td class="text-end pe-4 {{ ($totalVentasItbis - $totalComprasItbis) >= 0 ? 'text-danger' : 'text-success' }}">
                            RD$ {{ number_format($totalVentasItbis - $totalComprasItbis, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(239,68,68,.06);">
                        <div class="icon-bubble bg-soft-danger flex-shrink-0" style="width:48px;height:48px;font-size:1.2rem;">
                            <i class="bi bi-arrow-up-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">ITBIS Trasladado (Ventas)</small>
                            <h4 class="fw-bold text-danger mb-0">RD$ {{ number_format($totalVentasItbis, 2) }}</h4>
                            <small class="text-muted">ITBIS cobrado a clientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(34,197,94,.06);">
                        <div class="icon-bubble bg-soft-success flex-shrink-0" style="width:48px;height:48px;font-size:1.2rem;">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">ITBIS Acreditable (Compras)</small>
                            <h4 class="fw-bold text-success mb-0">RD$ {{ number_format($totalComprasItbis, 2) }}</h4>
                            <small class="text-muted">ITBIS pagado a proveedores</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mt-2" style="background:rgba(245,158,11,.08);">
                <div class="icon-bubble bg-soft-warning flex-shrink-0" style="width:52px;height:52px;font-size:1.3rem;">
                    <i class="bi bi-calculator"></i>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">ITBIS a Pagar / Compensar</small>
                    <h4 class="fw-bold mb-0 {{ ($totalVentasItbis - $totalComprasItbis) >= 0 ? 'text-danger' : 'text-success' }}">
                        RD$ {{ number_format($totalVentasItbis - $totalComprasItbis, 2) }}
                    </h4>
                    <small class="text-muted">
                        @if(($totalVentasItbis - $totalComprasItbis) >= 0)
                            ITBIS a pagar a DGII
                        @else
                            ITBIS a favor (crédito para próximos meses)
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
