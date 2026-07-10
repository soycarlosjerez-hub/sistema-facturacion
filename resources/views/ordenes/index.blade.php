@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Órdenes</h1>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary">Nueva Orden</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Pendientes</h5>
                    <h3>{{ $totales['pendientes'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>En Proceso</h5>
                    <h3>{{ $totales['en_proceso'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Total Hoy</h5>
                    <h3>RD$ {{ number_format($totales['hoy'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                        <tr>
                            <td>{{ $orden->id }}</td>
                            <td>
                                <span class="badge bg-{{ $orden->tipo_orden === 'delivery' ? 'info' : ($orden->tipo_orden === 'pickup' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($orden->tipo_orden) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $orden->estado === 'pendiente' ? 'danger' : ($orden->estado === 'completada' ? 'success' : 'primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td>{{ $orden->cliente?->nombre ?? '—' }}</td>
                            <td>RD$ {{ number_format($orden->subtotal + $orden->impuestos, 2) }}</td>
                            <td>{{ $orden->created_at->format('d/m/Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-info">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $ordenes->links() }}
        </div>
    </div>
</div>
@endsection
