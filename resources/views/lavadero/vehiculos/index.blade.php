@extends('layouts.app')
@section('title', 'Vehículos')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-droplet"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Vehículos</h2>
                    <p class="text-white text-opacity-75 mb-0">Registro de vehículos y su historial de servicios</p>
                </div>
            </div>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control rounded-3" placeholder="Buscar placa, marca, cliente..." value="{{ request('q') }}">
                <button class="btn btn-light rounded-pill px-3 fw-bold">Buscar</button>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent green"></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light small">
                    <tr><th>Placa</th><th>Marca / Modelo</th><th>Año</th><th>Color</th><th>Cliente</th><th>Visitas</th><th></th></tr>
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
                            <a href="{{ route('lavadero.vehiculos.show', $v) }}" class="premium-btn-edit">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Sin vehículos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $vehiculos->links() }}</div>
</div>
@endsection
