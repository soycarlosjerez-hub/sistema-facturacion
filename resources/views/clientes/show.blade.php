@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">
                <i class="bi bi-person"></i> {{ $cliente->nombre }}
            </h3>
            <small class="text-muted">Detalle del cliente</small>
        </div>

        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Datos del cliente -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body row g-3">

            <div class="col-md-4">
                <strong>Nombre:</strong><br>
                {{ $cliente->nombre }}
            </div>

            <div class="col-md-4">
                <strong>Email:</strong><br>
                {{ $cliente->email ?? '—' }}
            </div>

            <div class="col-md-4">
                <strong>Teléfono:</strong><br>
                {{ $cliente->telefono ?? '—' }}
            </div>

            <div class="col-md-4">
                <strong>RNC / Cédula:</strong><br>
                {{ $cliente->rnc_cedula ?? '—' }}
            </div>

            <div class="col-md-8">
                <strong>Dirección:</strong><br>
                {{ $cliente->direccion ?? '—' }}
            </div>

        </div>
    </div>

    <!-- Ventas del cliente -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 fw-semibold">
            <i class="bi bi-receipt"></i> Ventas realizadas
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cliente->ventas as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                        <td>RD$ {{ number_format($venta->total, 2) }}</td>
                        <td>
                            <span class="badge bg-success">
                                Completada
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ventas.show', $venta) }}"
                               class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Este cliente no tiene ventas registradas
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
