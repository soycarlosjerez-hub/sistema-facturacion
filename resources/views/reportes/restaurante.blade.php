@extends('layouts.app')
@section('title', 'Reportes Restaurante')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(59,130,246,.25) !important;
}
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Reportes Restaurante</h2>
                <p class="text-white-50 mb-0">Análisis de ventas del terminal de mesas</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card card-accent blue p-3 mb-4">
        <form method="GET" class="row g-2">
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
    </div>

    <div class="row g-4">
        {{-- Ventas por mesero --}}
        <div class="col-md-6">
            <div class="premium-card card-accent blue h-100">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Ventas por Mesero</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light small">
                                <tr><th>Mesero</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorMesero as $v)
                                <tr>
                                    <td>{{ $v->usuario?->name ?? '—' }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventas por mesa --}}
        <div class="col-md-6">
            <div class="premium-card card-accent blue h-100">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-grid-3x3 me-2 text-success"></i>Ventas por Mesa</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light small">
                                <tr><th>Mesa</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorMesa as $v)
                                <tr>
                                    <td>{{ $v->mesa?->nombre ?? 'Mesa ' . ($v->mesa?->numero ?? $v->mesa_id) }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventas por turno --}}
        <div class="col-md-6">
            <div class="premium-card card-accent blue h-100">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-sun me-2 text-warning"></i>Ventas por Turno</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light small">
                                <tr><th>Turno</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorHora as $v)
                                <tr>
                                    <td>{{ $v->turno }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Productos más vendidos --}}
        <div class="col-md-6">
            <div class="premium-card card-accent blue h-100">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-danger"></i>Productos más Vendidos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light small">
                                <tr><th>#</th><th>Producto</th><th class="text-center">Cantidad</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($productosTop as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $p->producto->nombre ?? '—' }}</td>
                                    <td class="text-center">{{ $p->total_cantidad }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($p->total_ventas, 2) }}</td>
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
    </div>
</div>
@endsection
