@extends('layouts.app')
@section('title', 'Lavadores')
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
                    <h2 class="fw-bold mb-0 text-white">Lavadores</h2>
                    <p class="text-white text-opacity-75 mb-0">Gestión de empleados del lavadero</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#lavadorModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Lavador
            </button>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent green"></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th class="text-center">% Comisión</th>
                        <th>Teléfono</th>
                        <th class="text-center">Activo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lavadores as $l)
                    <tr>
                        <td>{{ $l->id }}</td>
                        <td class="fw-medium">{{ $l->nombre }}</td>
                        <td>
                            <span class="badge {{ $l->tipo === 'fijo' ? 'bg-primary' : 'bg-warning text-dark' }} rounded-pill">
                                {{ $l->tipo === 'fijo' ? 'Fijo' : 'Temporal' }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">{{ number_format($l->porcentaje, 1) }}%</td>
                        <td>{{ $l->telefono ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $l->activo ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                {{ $l->activo ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="premium-btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $l->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('lavadero.lavadores.destroy', $l) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este lavador?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Sin lavadores registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="lavadorModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.lavadores.store') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Lavador</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre</label>
                    <input type="text" name="nombre" class="form-control rounded-3" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tipo</label>
                        <select name="tipo" class="form-select rounded-3" onchange="actualizarPorcentajeDefecto(this)">
                            <option value="temporal">Temporal</option>
                            <option value="fijo">Fijo</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">% Comisión</label>
                        <div class="input-group">
                            <input type="number" name="porcentaje" class="form-control rounded-start-3" step="0.01" min="0" max="100" value="{{ $defaultTemporal }}">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Identificación</label>
                    <input type="text" name="identificacion" class="form-control rounded-3">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="2"></textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="lavadorActivo" checked>
                    <label class="form-check-label" for="lavadorActivo">Activo</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales Editar --}}
@foreach($lavadores as $l)
<div class="modal fade" id="editModal{{ $l->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.lavadores.update', $l) }}" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Lavador</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre</label>
                    <input type="text" name="nombre" class="form-control rounded-3" value="{{ $l->nombre }}" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Tipo</label>
                        <select name="tipo" class="form-select rounded-3">
                            <option value="temporal" {{ $l->tipo === 'temporal' ? 'selected' : '' }}>Temporal</option>
                            <option value="fijo" {{ $l->tipo === 'fijo' ? 'selected' : '' }}>Fijo</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">% Comisión</label>
                        <div class="input-group">
                            <input type="number" name="porcentaje" class="form-control rounded-start-3" step="0.01" min="0" max="100" value="{{ $l->porcentaje }}">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3" value="{{ $l->telefono }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ $l->email }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Identificación</label>
                    <input type="text" name="identificacion" class="form-control rounded-3" value="{{ $l->identificacion }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="2">{{ $l->notas }}</textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="editActivo{{ $l->id }}" {{ $l->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="editActivo{{ $l->id }}">Activo</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
function actualizarPorcentajeDefecto(select) {
    const pctInput = select.closest('.modal-body').querySelector('input[name="porcentaje"]');
    if (select.value === 'fijo') {
        pctInput.value = '{{ $defaultFijo }}';
    } else {
        pctInput.value = '{{ $defaultTemporal }}';
    }
}
</script>
@endsection
