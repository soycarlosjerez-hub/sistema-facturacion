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
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Impresoras</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $impresoras->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('impresoras.historial') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-clock-history me-1"></i> Historial
                </a>
                <a href="{{ route('impresoras.plantillas') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-file-earmark-text me-1"></i> Plantillas
                </a>
                <a href="{{ route('impresoras.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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
            <div class="ui-stat p-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <small class="ui-stat-label">Total</small>
                    <h3 class="fw-bold mb-0 ui-stat-value">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat p-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <small class="ui-stat-label">Activas</small>
                    <h3 class="fw-bold text-success mb-0 ui-stat-value">{{ $stats['activas'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat p-3" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <small class="ui-stat-label">Red</small>
                    <h3 class="fw-bold text-info mb-0 ui-stat-value">{{ $stats['red'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat p-3" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <small class="ui-stat-label">Auto-Ventas</small>
                    <h3 class="fw-bold text-warning mb-0 ui-stat-value">{{ $stats['auto_ventas'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
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
                        <td><span class="ui-badge ui-badge-neutral rounded-pill">{{ $imp->tipo_conexion }}</span></td>
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
                            @if($imp->activo)
                                <span class="ui-badge ui-badge-success rounded-pill">Activa</span>
                            @else
                                <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('impresoras.probar', $imp) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="ui-action ui-action-edit" title="Probar impresión">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </form>
                            <a href="{{ route('impresoras.edit', $imp) }}" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('impresoras.destroy', $imp) }}" method="POST" class="d-inline"
                                onsubmit="return UI.confirm.delete('Eliminar {{ $imp->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="ui-action ui-action-delete" title="Eliminar">
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
        @if(method_exists($impresoras, 'hasPages') && $impresoras->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $impresoras->links() }}
        </div>
        @endif
    </div>
</div>
@endsection