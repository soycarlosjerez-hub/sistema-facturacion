@extends('layouts.app')

@section('title', 'Ganancias Repartidores')

@push('styles')
@include('partials.premium-ui')
<style>
.earning-summary-card {
    background: linear-gradient(135deg, rgba(14,165,233,.08), rgba(34,197,94,.08));
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    border: 1px solid rgba(14,165,233,.15);
}
.earning-amount {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #0ea5e9, #22c55e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
@media (max-width: 767.98px) {
    .earnings-stats .ui-stat { margin-bottom: .75rem; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Ganancias Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wallet2 me-1"></i>
                        <span>Control de comisiones y propinas</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('driver-earnings.exportar', request()->only(['driver_id','desde','hasta'])) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-download me-1"></i> Exportar CSV
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('driver-earnings.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="ui-label">Repartidor</label>
                    <select name="driver_id" class="ui-select form-select">
                        <option value="">Todos los repartidores</option>
                        @foreach($drivers ?? [] as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->nombre }} {{ $driver->apellido }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label">Desde</label>
                    <input type="date" name="desde" class="ui-textarea form-control" value="{{ request('desde', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="ui-label">Hasta</label>
                    <input type="date" name="hasta" class="ui-textarea form-control" value="{{ request('hasta', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="ui-btn ui-btn-outline w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#calcularModal">
                        <i class="bi bi-calculator me-1"></i>Calcular Período
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    @if(isset($summary))
    <div class="row g-3 mb-4 earnings-stats">
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-body">
                    <div class="ui-stat-label">Total Ganancias</div>
                    <div class="earning-amount">${{ number_format($summary['total_ganancias'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-body">
                    <div class="ui-stat-label">Total Propinas</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">${{ number_format($summary['total_propinas'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.25s">
                <div class="ui-card-body">
                    <div class="ui-stat-label">Total Ingresos</div>
                    <div class="ui-stat-value" style="color:#22c55e;">${{ number_format($summary['total_ingresos'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.3s">
                <div class="ui-card-body">
                    <div class="ui-stat-label">Total Entregas</div>
                    <div class="ui-stat-value">{{ $summary['total_entregas'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Details Table --}}
    <div class="ui-card" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                <i class="bi bi-list-check me-2"></i>Detalle de Entregas
            </h6>
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Orden</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Dirección</th>
                            <th>Monto Base</th>
                            <th>Propina</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($summary['detalles'] ?? []) as $detalle)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <a href="{{ route('ordenes.show', $detalle['orden_id']) }}" class="text-decoration-none" style="color:#0ea5e9;">
                                    #{{ $detalle['orden_id'] }}
                                </a>
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($detalle['fecha'])->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $detalle['cliente'] }}</td>
                            <td><small class="text-muted">{{ Str::limit($detalle['direccion'], 30) }}</small></td>
                            <td>${{ number_format($detalle['monto_ganancia'], 2) }}</td>
                            <td>${{ number_format($detalle['propina'], 2) }}</td>
                            <td class="fw-bold" style="color:#22c55e;">${{ number_format($detalle['total'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                No hay entregas para el período seleccionado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Calcular Periodo Modal --}}
<div class="modal fade" id="calcularModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-primary text-white rounded-top-4">
                <h6 class="modal-title fw-bold"><i class="bi bi-calculator me-2"></i>Calcular Ganancias</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('driver-earnings.calcular') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="ui-label">Repartidor</label>
                        <select name="driver_id" class="ui-select form-select" required>
                            <option value="">Seleccionar repartidor...</option>
                            @foreach($drivers ?? [] as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->nombre }} {{ $driver->apellido }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="ui-label">Desde</label>
                            <input type="date" name="desde" class="ui-textarea form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="ui-label">Hasta</label>
                            <input type="date" name="hasta" class="ui-textarea form-control" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-check-lg me-1"></i>Calcular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
