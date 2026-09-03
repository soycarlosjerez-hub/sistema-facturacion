@extends('layouts.app')
@section('title', 'Ganancias Repartidores')
@section('topbar_class', 'px-4')

@push('styles')
<style>
.ui-stat { background: var(--glass-card); border: 1px solid rgba(255,255,255,.1); border-radius: var(--radius-xl); padding: 1.25rem; }
.ui-stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
.ui-stat-value { font-size: 1.5rem; font-weight: 800; }
.earning-row { background: var(--glass-card); border: 1px solid rgba(255,255,255,.08); border-radius: var(--radius-lg); padding: .75rem 1rem; margin-bottom: .5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1"><i class="bi bi-cash-stack me-2"></i>Ganancias Repartidores</h4>
            <p class="text-muted mb-0">Control de ganancias por entrega y período</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Ganancias</div>
                <div class="ui-stat-value text-success">RD$ <span id="totalGanancias">0.00</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Propinas</div>
                <div class="ui-stat-value text-info">RD$ <span id="totalPropinas">0.00</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Total Entregas</div>
                <div class="ui-stat-value" id="totalEntregas">0</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat text-center">
                <div class="ui-stat-label">Períodos</div>
                <div class="ui-stat-value">{{ $earnings->total() }}</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('driver-earnings.index') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <label class="form-label form-label-sm">Driver</label>
                <select name="driver_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->nombre }} {{ $driver->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label form-label-sm">Desde</label>
                <input type="date" name="periodo_inicio" class="form-control form-control-sm" value="{{ request('periodo_inicio') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label form-label-sm">Hasta</label>
                <input type="date" name="periodo_fin" class="form-control form-control-sm" value="{{ request('periodo_fin') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label form-label-sm">&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
        </div>
    </form>

    <div class="card border-0" style="background: var(--glass-card);">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Driver</th>
                        <th>Entregas</th>
                        <th>Ganancias</th>
                        <th>Propinas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($earnings as $earning)
                    <tr>
                        <td>{{ $earning->periodo_inicio->format('d/m/Y') }} - {{ $earning->periodo_fin->format('d/m/Y') }}</td>
                        <td>{{ $earning->driver->nombre }} {{ $earning->driver->apellido }}</td>
                        <td>{{ $earning->total_entregas }}</td>
                        <td class="text-success fw-bold">RD$ {{ number_format($earning->total_ganancias, 2) }}</td>
                        <td class="text-info">RD$ {{ $earning->propinas }}</td>
                        <td>
                            <a href="{{ route('driver-earnings.show', $earning) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay registros de ganancias</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $earnings->links() }}</div>
</div>
@endsection
