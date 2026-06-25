@extends('layouts.app')

@section('title', 'Impresoras')

@push('styles')
@include('partials.premium-ui')
<style>
    .filter-card { background: rgba(255,255,255,.7); backdrop-filter: blur(20px); border-radius: 1.2rem; border: 1px solid rgba(255,255,255,.8); padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
    body.dark-mode .filter-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Impresoras</h3>
                    <p class="mb-0 opacity-75">Configuración de impresoras térmicas y documentos</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('impresoras.historial') }}" class="btn btn-light rounded-pill">
                    <i class="bi bi-clock-history me-1"></i> Historial
                </a>
                <a href="{{ route('impresoras.plantillas') }}" class="btn btn-light rounded-pill">
                    <i class="bi bi-file-earmark-text me-1"></i> Plantillas
                </a>
                <a href="{{ route('impresoras.create') }}" class="btn btn-light rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Impresora
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="premium-stat-card p-3">
                <div class="card-accent blue"></div>
                <small class="stat-label">Total</small>
                <h3 class="fw-bold mb-0 stat-value">{{ $stats['total'] }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="premium-stat-card p-3">
                <div class="card-accent blue"></div>
                <small class="stat-label">Activas</small>
                <h3 class="fw-bold text-success mb-0 stat-value">{{ $stats['activas'] }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="premium-stat-card p-3">
                <div class="card-accent blue"></div>
                <small class="stat-label">Red</small>
                <h3 class="fw-bold text-info mb-0 stat-value">{{ $stats['red'] }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="premium-stat-card p-3">
                <div class="card-accent blue"></div>
                <small class="stat-label">Auto-Ventas</small>
                <h3 class="fw-bold text-warning mb-0 stat-value">{{ $stats['auto_ventas'] }}</h3>
            </div>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Impresora</th>
                        <th>Tipo</th>
                        <th>Conexión</th>
                        <th>Papel</th>
                        <th class="text-center">Auto</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($impresoras as $imp)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-semibold">{{ $imp->nombre }}</span>
                            @if($imp->descripcion)
                                <br><small class="text-muted">{{ $imp->descripcion }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill">{{ $imp->tipo_conexion }}</span></td>
                        <td><small class="font-monospace">{{ $imp->conexion_resumen }}</small></td>
                        <td>{{ $imp->tamano_label }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @foreach(['ventas','cotizaciones','conduces'] as $mod)
                                <form action="{{ route('impresoras.toggle-auto', [$imp, $mod]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm rounded-circle border-0 {{ $imp->{'auto_imprimir_'.$mod} ? 'text-success' : 'text-muted' }}"
                                        title="{{ $imp->{'auto_imprimir_'.$mod} ? 'Auto-'.$mod.' activado' : 'Auto-'.$mod.' desactivado' }}"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-{{ $mod === 'ventas' ? 'cart' : ($mod === 'cotizaciones' ? 'file-text' : 'truck') }}{{ $imp->{'auto_imprimir_'.$mod} ? '-fill' : '' }}"></i>
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-{{ $imp->activo ? 'success' : 'secondary' }}-subtle text-{{ $imp->activo ? 'success' : 'secondary' }}">
                                {{ $imp->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('impresoras.probar', $imp) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success rounded-pill px-2" title="Probar impresión">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </form>
                            <a href="{{ route('impresoras.edit', $imp) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('impresoras.destroy', $imp) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Eliminar {{ $imp->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-printer fs-1 d-block mb-2"></i>
                        No hay impresoras configuradas
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection