@extends('layouts.app')
@section('title', 'Reporte de Propinas')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-success me-2"></i>Reporte de Propinas</h2>
            <p class="text-muted mb-0">Propinas por mesero</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-auto">
            <label class="form-label small fw-bold">Desde</label>
            <input type="date" name="desde" class="form-control rounded-3" value="{{ $desde }}">
        </div>
        <div class="col-auto">
            <label class="form-label small fw-bold">Hasta</label>
            <input type="date" name="hasta" class="form-control rounded-3" value="{{ $hasta }}">
        </div>
        <div class="col-auto d-flex align-items-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-filter me-1"></i> Filtrar
            </button>
        </div>
    </form>

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <small class="text-muted d-block">Total Propinas</small>
                    <span class="fs-3 fw-bold text-success">RD$ {{ number_format($totalGlobal, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <small class="text-muted d-block">Órdenes con Propina</small>
                    <span class="fs-3 fw-bold text-primary">{{ $ordenesConPropina }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <small class="text-muted d-block">Promedio Global</small>
                    <span class="fs-3 fw-bold text-warning">RD$ {{ number_format($ordenesConPropina > 0 ? $totalGlobal / $ordenesConPropina : 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla por mesero --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
            <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Propinas por Mesero</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
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
@endsection
