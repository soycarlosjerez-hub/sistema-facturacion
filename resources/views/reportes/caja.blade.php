@extends('layouts.app')
@section('title', 'Reporte de Caja')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-stack text-info me-2"></i>Reporte de Caja / Turnos</h2>
            <p class="text-muted mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} sesión(es)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.caja.csv', request()->all()) }}" class="btn btn-success rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto"><label class="form-label small fw-semibold mb-0">Desde</label></div>
                <div class="col-auto"><input type="date" name="desde" class="form-control border-0 bg-white" value="{{ $desde }}"></div>
                <div class="col-auto"><label class="form-label small fw-semibold mb-0">Hasta</label></div>
                <div class="col-auto"><input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ $hasta }}"></div>
                <div class="col-auto">
                    <select name="caja_id" class="form-select border-0 bg-white">
                        <option value="">Todas las cajas</option>
                        @foreach($cajas as $c)
                            <option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Sesiones</small><h4 class="fw-bold mb-0 mt-1">{{ $cantidad }} <small class="text-muted" style="font-size:.6rem;">({{ $abiertas }} abiertas / {{ $cerradas }} cerradas)</small></h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total Ventas</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalVentas, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Descuadre Total</small><h4 class="fw-bold mb-0 mt-1 {{ $totalDescuadre >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($totalDescuadre, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Promedio x Sesión</small><h4 class="fw-bold mb-0 mt-1 text-info">RD$ {{ $cantidad > 0 ? number_format($totalVentas / $cantidad, 2) : '0.00' }}</h4></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Caja</th><th>Cajero</th><th>Apertura</th><th>Cierre</th>
                        <th class="text-end">Inicial</th><th class="text-end">Efectivo</th><th class="text-end">Tarjeta</th><th class="text-end">Transf.</th><th class="text-end">Declarado</th><th class="text-end pe-4">Descuadre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $s)
                        <tr>
                            <td class="ps-4"><span class="fw-semibold small">{{ $s->caja?->nombre ?? '' }}</span></td>
                            <td><small>{{ $s->user?->name ?? '' }}</small></td>
                            <td><small>{{ $s->fecha_apertura?->format('d/m/Y H:i') ?? '-' }}</small></td>
                            <td><small>{{ $s->fecha_cierre?->format('d/m/Y H:i') ?? 'Abierta' }}</small></td>
                            <td class="text-end">RD$ {{ number_format($s->monto_inicial ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_efectivo ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_tarjeta ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_transferencia ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->monto_declarado ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold {{ ($s->descuadre ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($s->descuadre ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay sesiones en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('monto_inicial'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_efectivo'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_tarjeta'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_transferencia'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('monto_declarado'), 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($sesiones->sum('descuadre'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection