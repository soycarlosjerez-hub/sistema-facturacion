@extends('layouts.app')

@section('title', 'Servicio ' . ($servicio->numero_proyecto ?? 'Domótica'))

@push('styles')
@include('partials.premium-ui')
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #06b6d4;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
body.dark-mode .info-item { background: rgba(30,41,59,.8); }
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
</style>
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
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $servicio->numero_proyecto }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        {{ $servicio->cliente->nombre ?? 'Sin cliente' }}
                        <span class="divider">·</span>
                        <i class="bi bi-tag me-1"></i>
                        {{ $servicio->tipo_servicio_label ?? ucfirst($servicio->tipo_servicio) }}
                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-{{ match($servicio->estado) {
                            'pendiente' => 'warning',
                            'programado' => 'info',
                            'en_curso' => 'primary',
                            'completado' => 'success',
                            'cancelado' => 'danger',
                            default => 'secondary'
                        }}">{{ $servicio->estado_label }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('domotica.edit')
                @if(!in_array($servicio->estado, ['completado', 'cancelado']))
                <a href="{{ route('domotica.edit', $servicio) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endif
                @endcan
                <a href="{{ route('domotica.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Subtotal</div>
                    <div class="ui-stat-value">RD$ {{ number_format($servicio->subtotal ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">ITBIS</div>
                    <div class="ui-stat-value">RD$ {{ number_format($servicio->itbis ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Descuento</div>
                    <div class="ui-stat-value text-danger">-RD$ {{ number_format($servicio->descuento ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total</div>
                    <div class="ui-stat-value text-success">RD$ {{ number_format($servicio->total ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-info-circle"></i> Información del Servicio</div>
        <div class="ui-card-body">
            <div class="row g-3">
                <div class="col-md-3"><div class="info-item"><div class="label">Título</div><div class="value">{{ $servicio->titulo }}</div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Tipo de Servicio</div><div class="value">{{ $servicio->tipo_servicio_label ?? ucfirst($servicio->tipo_servicio) }}</div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Técnico</div><div class="value">{{ $servicio->tecnico->nombre ?? 'Sin asignar' }}</div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Presupuesto</div><div class="value">RD$ {{ number_format($servicio->presupuesto ?? 0, 2) }}</div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Dirección de Instalación</div><div class="value">{{ $servicio->direccion_instalacion ?? '-' }}</div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Fecha Programada</div><div class="value">{{ $servicio->fecha_programada ? $servicio->fecha_programada->format('d/m/Y') : '-' }}</div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Fecha Completada</div><div class="value">{{ $servicio->fecha_completada ? $servicio->fecha_completada->format('d/m/Y') : '-' }}</div></div></div>
                <div class="col-12"><div class="info-item"><div class="label">Descripción</div><div class="value">{{ $servicio->descripcion ?? '-' }}</div></div></div>
                @if($servicio->notas)
                <div class="col-12"><div class="info-item"><div class="label">Notas</div><div class="value">{{ $servicio->notas }}</div></div></div>
                @endif
            </div>
        </div>
    </div>

    @can('domotica.edit')
    @if(!in_array($servicio->estado, ['completado', 'cancelado']))
    <div class="ui-card mb-4" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-sliders"></i> Gestión del Servicio</div>
        <div class="ui-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label">Cambiar Estado</label>
                    <form method="POST" action="{{ route('domotica.cambiar-estado', $servicio) }}" class="d-flex gap-2">
                        @csrf
                        <select name="estado" class="ui-select">
                            <option value="pendiente" {{ $servicio->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="programado" {{ $servicio->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                            <option value="en_curso" {{ $servicio->estado == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="completado" {{ $servicio->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ $servicio->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        <button type="submit" class="ui-btn ui-btn-primary btn-sm">Actualizar</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route('domotica.completar', $servicio) }}">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn-success btn-sm w-100">
                            <i class="bi bi-check2-circle me-1"></i> Marcar como Completado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-plus-square"></i> Agregar Equipo/Producto</div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('domotica.agregar-equipo', $servicio) }}" class="row g-2">
                @csrf
                <div class="col-lg-3">
                    <select name="producto_id" class="ui-select" required>
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos ?? [] as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="number" name="cantidad" class="ui-input" placeholder="Cantidad" min="1" value="1" required>
                </div>
                <div class="col-lg-2">
                    <input type="number" name="precio_unitario" class="ui-input" placeholder="Precio unitario" step="0.01" min="0" required>
                </div>
                <div class="col-lg-3">
                    <input type="text" name="ubicacion_instalacion" class="ui-input" placeholder="Ubicación de instalación">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-primary w-100"><i class="bi bi-plus-lg"></i> Agregar</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endcan

    <div class="ui-card" style="--delay:.4s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-box-seam"></i> Equipos Instalados</div>
        <div class="ui-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicio->instalaciones as $inst)
                        <tr>
                            <td>{{ $inst->producto->nombre ?? 'Producto eliminado' }}</td>
                            <td>{{ $inst->cantidad }}</td>
                            <td>RD$ {{ number_format($inst->precio_unitario ?? 0, 2) }}</td>
                            <td>RD$ {{ number_format(($inst->cantidad ?? 0) * ($inst->precio_unitario ?? 0), 2) }}</td>
                            <td>{{ $inst->ubicacion_instalacion ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $inst->estado_label ?? ucfirst($inst->estado ?? 'pendiente') }}</span>
                            </td>
                            <td class="text-end">
                                @can('domotica.edit')
                                <form action="{{ route('domotica.eliminar-equipo', [$servicio, $inst]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Retirar este equipo del servicio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Retirar"><i class="bi bi-x-lg"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay equipos registrados en este servicio.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection