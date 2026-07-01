@extends('layouts.app')
@section('title', 'Documentación API')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-code-slash me-2 text-primary"></i>Documentación de la API</h2>
            <p class="text-muted mb-0">Listado de todos los endpoints disponibles del API REST.</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:100px;">M&eacute;todo</th>
                            <th>Endpoint</th>
                            <th class="pe-4">Nombre de ruta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routes as $route)
                        <tr>
                            <td class="ps-4">
                                @php
                                    $methodColors = [
                                        'GET' => 'success',
                                        'POST' => 'primary',
                                        'PUT' => 'warning',
                                        'PATCH' => 'info',
                                        'DELETE' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $methodColors[$route['method']] ?? 'secondary' }} rounded-pill text-uppercase" style="font-size:.65rem;min-width:60px;">
                                    {{ $route['method'] }}
                                </span>
                            </td>
                            <td>
                                <code class="fw-bold">{{ $route['uri'] }}</code>
                            </td>
                            <td class="pe-4">
                                <small class="text-muted">{{ $route['name'] ?? '—' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay rutas de API registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection