@extends('layouts.app')
@section('title', 'Gestión de Mesas')
@push('styles')
@include('partials.premium-ui')
<style>
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
.btn-icon-hover {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}
.btn-icon-hover:hover { transform: scale(1.15); }
</style>
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-cup-straw"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Gestión de Mesas</h2>
                    <p class="text-white text-opacity-75 mb-0">Administra las mesas del restaurante</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#mesaModal" onclick="abrirModalCrear()">
                <i class="bi bi-plus-lg me-1"></i> Nueva Mesa
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="premium-card">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Nombre</th>
                            <th>Capacidad</th>
                            <th>Ubicación</th>
                            <th>Categoría</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Activa</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mesas as $mesa)
                        <tr id="row-{{ $mesa->id }}">
                            <td class="fw-semibold">{{ $mesa->numero }}</td>
                            <td>{{ $mesa->nombre ?: '—' }}</td>
                            <td><i class="bi bi-people me-1 text-muted"></i>{{ $mesa->capacidad }}</td>
                            <td>{{ $mesa->ubicacion ?: '—' }}</td>
                            <td>
                                @if($mesa->categoria)
                                    <span class="badge rounded-pill" style="background:{{ $mesa->categoria->color }}20; color:{{ $mesa->categoria->color }};">
                                        <i class="bi {{ $mesa->categoria->icono }} me-1"></i>{{ $mesa->categoria->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $estados = [
                                        'disponible' => ['label' => 'Disponible', 'class' => 'bg-success'],
                                        'ocupada'    => ['label' => 'Ocupada', 'class' => 'bg-danger'],
                                        'reservada'  => ['label' => 'Reservada', 'class' => 'bg-warning text-dark'],
                                        'inactiva'   => ['label' => 'Inactiva', 'class' => 'bg-secondary'],
                                    ];
                                    $e = $estados[$mesa->estado] ?? $estados['disponible'];
                                @endphp
                                <span class="badge {{ $e['class'] }} rounded-pill">{{ $e['label'] }}</span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                        {{ $mesa->activa ? 'checked' : '' }}
                                        onchange="toggleActiva({{ $mesa->id }}, this.checked)">
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="premium-btn-edit"
                                        onclick="editarMesa({{ $mesa->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($mesa->estado !== 'ocupada')
                                    <form action="{{ route('restaurante.mesa.destroy', $mesa) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Eliminar la mesa {{ $mesa->numero }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="premium-btn-delete" disabled title="No se puede eliminar una mesa ocupada">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-grid-3x3-gap display-4 d-block mb-3 opacity-25"></i>
                                <p class="mb-0">No hay mesas registradas.</p>
                                <button class="btn btn-primary btn-sm rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#mesaModal" onclick="abrirModalCrear()">
                                    <i class="bi bi-plus-lg me-1"></i>Crear primera mesa
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal crear/editar --}}
<div class="modal fade" id="mesaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="mesa-form" method="POST" action="{{ route('restaurante.mesa.store') }}">
                @csrf
                <input type="hidden" name="_method" id="mesa-method" value="POST">
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-bold" id="mesa-modal-title">Nueva Mesa</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Número <span class="text-danger">*</span></label>
                        <input type="text" name="numero" id="mesa-numero" class="form-control rounded-3" required placeholder="01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" id="mesa-nombre" class="form-control rounded-3" placeholder="Ej. Terraza, VIP">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" name="capacidad" id="mesa-capacidad" class="form-control rounded-3" value="4" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <input type="text" name="ubicacion" id="mesa-ubicacion" class="form-control rounded-3" placeholder="Interior, terraza">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Categoría</label>
                        <select name="categoria_id" id="mesa-categoria" class="form-select rounded-3">
                            <option value="">Sin categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="mesa-estado-group">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" id="mesa-estado" class="form-select rounded-3">
                            <option value="disponible">Disponible</option>
                            <option value="ocupada">Ocupada</option>
                            <option value="reservada">Reservada</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalCrear() {
    document.getElementById('mesa-modal-title').textContent = 'Nueva Mesa';
    document.getElementById('mesa-method').value = 'POST';
    document.getElementById('mesa-form').action = '{{ route("restaurante.mesa.store") }}';
    document.getElementById('mesa-estado-group').classList.add('d-none');
    document.getElementById('mesa-form').reset();
    document.getElementById('mesa-capacidad').value = '4';
}

function editarMesa(id) {
    fetch('/restaurante/mesas/' + id)
        .then(r => r.json())
        .then(mesa => {
            document.getElementById('mesa-modal-title').textContent = 'Editar Mesa';
            document.getElementById('mesa-method').value = 'PUT';
            document.getElementById('mesa-form').action = '/restaurante/mesa/' + id + '/update';
            document.getElementById('mesa-numero').value = mesa.numero;
            document.getElementById('mesa-nombre').value = mesa.nombre || '';
            document.getElementById('mesa-capacidad').value = mesa.capacidad;
            document.getElementById('mesa-ubicacion').value = mesa.ubicacion || '';
            document.getElementById('mesa-categoria').value = mesa.categoria_id || '';
            document.getElementById('mesa-estado').value = mesa.estado;
            document.getElementById('mesa-estado-group').classList.remove('d-none');
            new bootstrap.Modal(document.getElementById('mesaModal')).show();
        });
}

function toggleActiva(id, activa) {
    fetch('/restaurante/mesa/' + id + '/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ activa: activa, _method: 'PUT' })
    }).then(r => {
        if (!r.ok) Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar' });
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar' });
    });
}

document.getElementById('mesaModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('mesa-modal-title').textContent = 'Nueva Mesa';
    document.getElementById('mesa-method').value = 'POST';
    document.getElementById('mesa-form').action = '{{ route("restaurante.mesa.store") }}';
    document.getElementById('mesa-estado-group').classList.add('d-none');
});
</script>
@endsection
