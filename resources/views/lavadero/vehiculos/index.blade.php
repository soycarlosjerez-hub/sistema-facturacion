@extends('layouts.app')
@section('title', 'Vehículos')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-car-front text-primary me-2"></i>Vehículos</h2>
            <p class="text-muted mb-0">Registro de vehículos y su historial de servicios</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control rounded-3" placeholder="Buscar placa, marca, cliente..." value="{{ request('q') }}">
            <button class="btn btn-primary rounded-pill px-3">Buscar</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
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
                            <a href="{{ route('lavadero.vehiculos.show', $v) }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
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
