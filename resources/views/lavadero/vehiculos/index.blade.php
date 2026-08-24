@extends('layouts.app')
@section('title', 'Vehículos')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Vehículos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-card-list me-1"></i>
                        <span>Registro de vehículos y su historial de servicios</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="q" class="ui-input" placeholder="Buscar placa, marca, cliente..." value="{{ request('q') }}">
                    <button class="ui-btn ui-btn-solid"><i class="bi bi-search me-1"></i> Buscar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table mb-0">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Marca / Modelo</th>
                            <th>Año</th>
                            <th>Color</th>
                            <th>Cliente</th>
                            <th>Visitas</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $v)
                        <tr>
                            <td class="fw-bold">{{ $v->placa ?? '—' }}</td>
                            <td>{{ $v->marca }} {{ $v->modelo }}</td>
                            <td>{{ $v->anio ?? '—' }}</td>
                            <td>{{ $v->color ?? '—' }}</td>
                            <td>{{ $v->cliente?->nombre ?? '—' }}</td>
                            <td>{{ $v->ventas_count ?? 0 }}</td>
                            <td class="text-end">
                                <a href="{{ route('lavadero.vehiculos.show', $v) }}" class="ui-action ui-action-view" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="ui-empty-state">
                                    <i class="bi bi-car-front"></i>
                                    <p>Sin vehículos registrados</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $vehiculos->links() }}</div>
</div>
@endsection
