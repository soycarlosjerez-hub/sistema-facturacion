@extends('layouts.app')

@section('title', 'Instalaciones')

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-tools me-2"></i>Instalaciones</h2>
            <p class="text-muted mb-0">Gestión de instalaciones de equipos de climatización</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Instalaciones</h5>
                <a href="{{ route('climatizacion.instalaciones.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Instalación
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar número, cliente o dirección..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach (\App\Models\Instalacion::ESTADOS as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipo_inmueble" class="form-select">
                        <option value="">Todos los inmuebles</option>
                        @foreach (\App\Models\Instalacion::TIPOS_INMUEBLE as $key => $label)
                            <option value="{{ $key }}" {{ request('tipo_inmueble') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-search me-1"></i>Filtrar</button>
                    <a href="{{ route('climatizacion.instalaciones.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Limpiar</a>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Dirección</th>
                            <th>Tipo Inmueble</th>
                            <th>Instalador</th>
                            <th>Programada Para</th>
                            <th>Completada En</th>
                            <th>Estado</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($instalaciones as $inst)
                        <tr>
                            <td class="fw-medium">{{ $inst->numero }}</td>
                            <td>{{ $inst->cliente?->nombre ?? '-' }}</td>
                            <td class="text-truncate" style="max-width:180px;">{{ $inst->direccion_instalacion ?? '-' }}</td>
                            <td>{{ \App\Models\Instalacion::TIPOS_INMUEBLE[$inst->tipo_inmueble] ?? $inst->tipo_inmueble }}</td>
                            <td>{{ $inst->instalador?->name ?? '-' }}</td>
                            <td>{{ $inst->programada_para ? $inst->programada_para->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $inst->completada_en ? $inst->completada_en->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @php
                                    $badgeColor = match ($inst->estado) {
                                        'pendiente' => 'secondary',
                                        'programada' => 'info',
                                        'en_progreso' => 'warning',
                                        'completada' => 'success',
                                        'cancelada' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">{{ \App\Models\Instalacion::ESTADOS[$inst->estado] ?? $inst->estado }}</span>
                            </td>
                            <td class="text-end fw-medium">{{ number_format($inst->total ?? 0, 2) }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('climatizacion.instalaciones.show', $inst) }}" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                                    @if (!in_array($inst->estado, ['completada', 'cancelada']))
                                        <a href="{{ route('climatizacion.instalaciones.edit', $inst) }}" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                    @endif
                                    @if (!in_array($inst->estado, ['completada', 'cancelada']))
                                        @php
                                            $nextState = match ($inst->estado) {
                                                'pendiente' => 'programada',
                                                'programada' => 'en_progreso',
                                                default => null,
                                            };
                                        @endphp
                                        @if ($nextState)
                                            <form action="{{ route('climatizacion.instalaciones.advance', $inst) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="next_state" value="{{ $nextState }}">
                                                <button type="submit" class="btn btn-outline-primary" title="Avanzar estado"><i class="bi bi-forward"></i></button>
                                            </form>
                                        @endif
                                    @endif
                                    @if (!in_array($inst->estado, ['completada', 'cancelada']))
                                        <form action="{{ route('climatizacion.instalaciones.destroy', $inst) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta instalación?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No hay instalaciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $instalaciones->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
