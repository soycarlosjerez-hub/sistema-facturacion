@extends('layouts.app')

@section('title', 'Ganancias de Repartidores')

@push('styles')
@include('partials.premium-ui')
<style>
/* Earnings Module Styles */
.earning-period {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .85rem;
    border-radius: 9999px;
    font-size: .8rem;
    font-weight: 600;
    background: rgba(14,165,233,.08);
    color: #0ea5e9;
}
.total-highlight {
    background: linear-gradient(135deg, rgba(14,165,233,.08), rgba(14,165,233,.03));
    border: 1px solid rgba(14,165,233,.15);
    border-radius: var(--radius-xl);
    padding: 1.25rem 1.5rem;
}
.grand-total {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0ea5e9;
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
                    <h4 class="ui-header-title">Ganancias de Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        <span>Control de pagos y comisiones</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button type="button" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#calculateModal">
                    <i class="bi bi-calculator me-1"></i> Calcular Ganancias
                </button>
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
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <label class="ui-label">Desde</label>
                    <input type="date" name="periodo_inicio" class="ui-input" value="{{ request('periodo_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Hasta</label>
                    <input type="date" name="periodo_fin" class="ui-input" value="{{ request('periodo_fin') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Stats --}}
    @if(isset($summary))
    <div class="row g-3 mb-4 earnings-stats">
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Períodos</div>
                    <div class="ui-stat-value">{{ $summary['periodos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Entregas</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;">{{ $summary['total_entregas'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total a Pagar</div>
                    <div class="ui-stat-value" style="color:#16a34a;">${{ number_format($summary['total_ganancias'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Período</th>
                        <th>Repartidor</th>
                        <th class="text-center">Entregas</th>
                        <th class="text-end">Ganancia Total</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($earnings as $earning)
                    <tr>
                        <td class="ps-4">
                            <span class="earning-period">
                                <i class="bi bi-calendar-range me-1"></i>
                                {{ $earning->periodo_inicio->format('d/m/Y') }} — {{ $earning->periodo_fin->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="driver-avatar" style="width:32px;height:32px;font-size:.65rem;">
                                    {{ strtoupper(substr($earning->driver->nombre, 0, 1) . substr($earning->driver->apellido, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $earning->driver->nombre }} {{ $earning->driver->apellido }}</div>
                                    <small class="text-muted">{{ $earning->driver->cedula }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-info">{{ $earning->total_entregas }}</span>
                        </td>
                        <td class="text-end fw-bold" style="color:#16a34a;">
                            ${{ number_format($earning->total_ganancias, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('driver-earnings.show', $earning) }}" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-cash fs-1 d-block mb-2"></i>
                            No hay registros de ganancias
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($earnings->hasPages())
        <div class="card-footer bg-transparent border-0 p-3">
            {{ $earnings->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Calculate Modal --}}
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-info text-white rounded-top-4">
                <h6 class="modal-title fw-bold"><i class="bi bi-calculator me-2"></i>Calcular Ganancias</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('driver-earnings.calcular') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="ui-label">Repartidor <span class="text-danger">*</span></label>
                        <select name="driver_id" class="ui-select form-select" required>
                            <option value="">Seleccionar repartidor...</option>
                            @foreach($drivers ?? [] as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->nombre }} {{ $driver->apellido }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="ui-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" name="periodo_inicio" class="ui-input" required>
                        </div>
                        <div class="col-6">
                            <label class="ui-label">Fecha Fin <span class="text-danger">*</span></label>
                            <input type="date" name="periodo_fin" class="ui-input" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-calculator me-1"></i>Calcular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
