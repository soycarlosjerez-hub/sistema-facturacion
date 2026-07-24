@extends('layouts.app')

@section('title', $instalacion->numero)

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-tools me-2"></i>{{ $instalacion->numero }}</h2>
            <p class="text-muted mb-0">Detalles de la instalación</p>
        </div>
    </div>

    <div class="row g-3">
        <!-- Información General -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información General</h5>
                    @php
                        $badgeColor = match ($instalacion->estado) {
                            'pendiente' => 'secondary',
                            'programada' => 'info',
                            'en_progreso' => 'warning',
                            'completada' => 'success',
                            'cancelada' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeColor }} fs-6">{{ \App\Models\Instalacion::ESTADOS[$instalacion->estado] ?? $instalacion->estado }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Número</label>
                            <p class="fw-medium">{{ $instalacion->numero }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cliente</label>
                            <p class="fw-medium">{{ $instalacion->cliente?->nombre ?? 'Sin cliente' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo de Inmueble</label>
                            <p>{{ \App\Models\Instalacion::TIPOS_INMUEBLE[$instalacion->tipo_inmueble] ?? $instalacion->tipo_inmueble }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Instalador</label>
                            <p>{{ $instalacion->instalador?->name ?? 'No asignado' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Dirección de Instalación</label>
                            <p>{{ $instalacion->direccion_instalacion ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Programada Para</label>
                            <p>{{ $instalacion->programada_para ? $instalacion->programada_para->format('d/m/Y H:i') : 'Por definir' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Completada En</label>
                            <p>{{ $instalacion->completada_en ? $instalacion->completada_en->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Nota Interna</label>
                            <p class="text-pre-wrap">{{ $instalacion->nota_interna ?? 'Sin notas' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Creado por</label>
                            <p>{{ $instalacion->creadoPor?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Creado</label>
                            <p>{{ $instalacion->created_at?->format('d/m/Y h:i A') ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Actualizado</label>
                            <p>{{ $instalacion->updated_at?->format('d/m/Y h:i A') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen / Total -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Total de Productos</label>
                        <p class="h4 mb-0">{{ $instalacion->productos->sum('pivot.cantidad') }} unidades</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Total de la Instalación</label>
                        <p class="h3 mb-0 fw-bold text-success">${{ number_format($instalacion->total ?? 0, 2) }}</p>
                    </div>
                    <hr>
                    <label class="form-label text-muted small">Transiciones de Estado</label>
                    <ul class="list-unstyled mb-0">
                        @foreach (\App\Models\Instalacion::ESTADOS as $key => $label)
                            @php
                                $isCurrent = $instalacion->estado === $key;
                                $isPast = in_array($key, [
                                    'pendiente', 'programada', 'en_progreso', 'completada'
                                ]) && array_search($key, array_keys(\App\Models\Instalacion::ESTADOS)) <= array_search($instalacion->estado, array_keys(\App\Models\Instalacion::ESTADOS));
                            @endphp
                            <li class="d-flex align-items-center mb-1">
                                @if ($isCurrent)
                                    <i class="bi bi-arrow-right-circle-fill text-{{ $badgeColor }} me-2"></i>
                                    <span class="fw-bold">{{ $label }}</span>
                                @elseif ($isPast && $instalacion->estado !== 'cancelada')
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <span class="text-muted">{{ $label }}</span>
                                @else
                                    <i class="bi bi-circle text-secondary me-2"></i>
                                    <span class="text-muted">{{ $label }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-white border-0 pt-3">
            <h5 class="mb-0">Productos de la Instalación</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($instalacion->productos as $producto)
                        <tr>
                            <td class="fw-medium">{{ $producto->nombre }}</td>
                            <td><code>{{ $producto->codigo ?? '-' }}</code></td>
                            <td class="text-center">{{ $producto->pivot->cantidad }}</td>
                            <td class="text-end">${{ number_format($producto->pivot->precio_unitario, 2) }}</td>
                            <td class="text-end fw-medium">${{ number_format($producto->pivot->cantidad * $producto->pivot->precio_unitario, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No hay productos asociados a esta instalación</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th class="text-end">${{ number_format($instalacion->total ?? 0, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="mt-3">
        @if (!in_array($instalacion->estado, ['completada', 'cancelada']))
            <a href="{{ route('climatizacion.instalaciones.edit', $instalacion) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>

            @php
                $nextState = match ($instalacion->estado) {
                    'pendiente' => 'programada',
                    'programada' => 'en_progreso',
                    'en_progreso' => 'completada',
                    default => null,
                };
            @endphp
            @if ($nextState)
                <form action="{{ route('climatizacion.instalaciones.advance', $instalacion) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="next_state" value="{{ $nextState }}">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Avanzar estado a {{ \App\Models\Instalacion::ESTADOS[$nextState] }}?');">
                        <i class="bi bi-forward me-1"></i>Avanzar a {{ \App\Models\Instalacion::ESTADOS[$nextState] }}
                    </button>
                </form>
            @endif
        @endif
        <a href="{{ route('climatizacion.instalaciones.index') }}" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left me-1"></i>Volver</a>
    </div>
</div>

<style>
.text-pre-wrap {
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
@endsection
