@extends('layouts.app')

@section('title', $titulo)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text text-danger me-2"></i>
                {{ $titulo }}
            </h2>
            <p class="text-muted mb-0">Período: {{ ucfirst($periodo->translatedFormat('F Y')) }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.fiscales', ['tipo' => $tipo === '607' ? '606' : '607', 'mes' => $mes, 'anio' => $anio]) }}" 
               class="btn btn-outline-info rounded-pill">
                <i class="bi bi-arrow-left-right me-1"></i> Cambiar a {{ $tipo === '607' ? '606 (Compras)' : '607 (Ventas)' }}
            </a>
            <a href="{{ route('reportes.fiscales.export', request()->all()) }}" class="btn btn-success rounded-pill">
                <i class="bi bi-download me-1"></i> CSV DGII
            </a>
            <a href="{{ route('reportes.fiscales.txt', request()->all()) }}" class="btn btn-warning rounded-pill">
                <i class="bi bi-filetype-txt me-1"></i> TXT DGII
            </a>
            <a href="{{ route('reportes.fiscales.pdf', request()->all()) }}" class="btn btn-danger rounded-pill">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-grid me-1"></i> Reportes
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('reportes.fiscales') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0">Mes</label>
                </div>
                <div class="col-auto">
                    <select name="mes" class="form-select border-0 bg-white">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="anio" class="form-select border-0 bg-white">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Registros</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ number_format($cantidad) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Monto Facturado</small>
                    <h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($total_monto, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">ITBIS</small>
                    <h4 class="fw-bold mb-0 mt-1 text-warning">RD$ {{ number_format($total_itbis, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Total General</small>
                    <h4 class="fw-bold mb-0 mt-1 text-success">RD$ {{ number_format($total_general, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-table me-2"></i>Detalle de {{ $tipo === '607' ? 'Ventas' : 'Compras' }}
            </h5>
            <small class="text-muted">{{ $cantidad }} registro(s)</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
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
                <tfoot class="table-light fw-bold">
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

    <div class="card border-0 shadow-sm rounded-4 mt-4">
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
@endsection
