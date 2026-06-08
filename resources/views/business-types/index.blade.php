@extends('layouts.app')

@section('title', 'Tipos de Negocio')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-diagram-3 text-primary me-2"></i>Tipos de Negocio</h2>
            <p class="text-muted mb-0">Define los tipos de negocio del sistema y sus módulos asociados</p>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Tipo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Tipo</th>
                            <th>Slug</th>
                            <th class="text-center">Módulos</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Orden</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                            <tr id="row-{{ $tipo->id }}">
                                <td class="text-muted">{{ $tipo->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $tipo->color }} bg-opacity-10 text-{{ $tipo->color }} rounded-circle p-2">
                                            <i class="bi {{ $tipo->icon }}"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">{{ $tipo->nombre }}</div>
                                            @if($tipo->descripcion)
                                                <small class="text-muted">{{ $tipo->descripcion }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><code>{{ $tipo->slug }}</code></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="openModulesModal({{ $tipo->id }}, '{{ $tipo->nombre }}')">
                                        <i class="bi bi-grid-3x3-gap me-1"></i>{{ $tipo->modules->count() }} módulos
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if($tipo->activo)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $tipo->orden }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary rounded-start-pill" onclick="openEditModal({{ $tipo->toJson() }})" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('business-types.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tipo de negocio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-end-pill" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-diagram-3 display-4 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0">No hay tipos de negocio configurados.</p>
                                    <p class="small">Crea el primero haciendo clic en "Nuevo Tipo".</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('business-types.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Nuevo Tipo de Negocio</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control rounded-3" required placeholder="Ej: Restaurante / Bar / Café">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Slug</label>
                            <input type="text" name="slug" class="form-control rounded-3" required placeholder="Ej: restaurante" pattern="[a-z0-9\-]+">
                            <div class="form-text">Minúsculas, números y guiones.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Icono</label>
                            <input type="text" name="icon" class="form-control rounded-3" required value="bi-grid" placeholder="bi-cup-straw">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Color</label>
                            <select name="color" class="form-select rounded-3">
                                <option value="info">Azul (info)</option>
                                <option value="success">Verde (success)</option>
                                <option value="warning">Amarillo (warning)</option>
                                <option value="primary">Primario (primary)</option>
                                <option value="secondary">Gris (secondary)</option>
                                <option value="danger">Rojo (danger)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Orden</label>
                            <input type="number" name="orden" class="form-control rounded-3" value="0" min="0">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <input type="text" name="descripcion" class="form-control rounded-3" placeholder="Descripción corta (opcional)">
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" checked>
                        <label class="form-check-label small fw-bold">Activo</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i>Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Editar Tipo de Negocio</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control rounded-3" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Slug</label>
                            <input type="text" name="slug" id="edit_slug" class="form-control rounded-3" required pattern="[a-z0-9\-]+">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Icono</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Color</label>
                            <select name="color" id="edit_color" class="form-select rounded-3">
                                <option value="info">Azul (info)</option>
                                <option value="success">Verde (success)</option>
                                <option value="warning">Amarillo (warning)</option>
                                <option value="primary">Primario (primary)</option>
                                <option value="secondary">Gris (secondary)</option>
                                <option value="danger">Rojo (danger)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Orden</label>
                            <input type="number" name="orden" id="edit_orden" class="form-control rounded-3" min="0">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <input type="text" name="descripcion" id="edit_descripcion" class="form-control rounded-3">
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="activo" id="edit_activo" value="1">
                        <label class="form-check-label small fw-bold">Activo</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modules Modal -->
<div class="modal fade" id="modulesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Módulos: <span id="modulesTipoName"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Selecciona los módulos que estarán visibles para este tipo de negocio.</p>
                <div id="modulesLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                </div>
                <div id="modulesContent" style="display:none;">
                    @php
                        $modulosPorCategoria = [];
                        foreach ($modulosDisponibles as $key => $mod) {
                            $cat = $mod['categoria'] ?? 'otros';
                            $modulosPorCategoria[$cat][$key] = $mod;
                        }
                    @endphp
                    @foreach($modulosPorCategoria as $categoria => $modulos)
                        <div class="mb-3">
                            <h6 class="text-muted small fw-bold text-uppercase mb-2">{{ ucfirst($categoria) }}</h6>
                            <div class="row g-2">
                                @foreach($modulos as $key => $mod)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input module-check" type="checkbox"
                                                   data-modulo="{{ $key }}" id="mod-{{ $key }}">
                                            <label class="form-check-label small" for="mod-{{ $key }}">
                                                <i class="bi {{ $mod['icon'] }} me-1 text-muted"></i>{{ $mod['label'] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" id="modulesFooter" style="display:none;">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveModules()">
                    <i class="bi bi-save me-1"></i>Guardar Módulos
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
</style>

<script>
let currentModulesTypeId = null;

function openEditModal(data) {
    document.getElementById('editForm').action = '/business-types/' + data.id;
    document.getElementById('edit_nombre').value = data.nombre;
    document.getElementById('edit_slug').value = data.slug;
    document.getElementById('edit_icon').value = data.icon;
    document.getElementById('edit_color').value = data.color;
    document.getElementById('edit_orden').value = data.orden;
    document.getElementById('edit_descripcion').value = data.descripcion || '';
    document.getElementById('edit_activo').checked = data.activo;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function openModulesModal(id, name) {
    currentModulesTypeId = id;
    document.getElementById('modulesTipoName').textContent = name;
    document.getElementById('modulesLoading').style.display = 'block';
    document.getElementById('modulesContent').style.display = 'none';
    document.getElementById('modulesFooter').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modulesModal')).show();

    fetch('/business-types/' + id + '/modules-data')
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.module-check').forEach(cb => {
                cb.checked = data.modulos.includes(cb.dataset.modulo);
            });
            document.getElementById('modulesLoading').style.display = 'none';
            document.getElementById('modulesContent').style.display = 'block';
            document.getElementById('modulesFooter').style.display = 'flex';
        })
        .catch(() => {
            document.getElementById('modulesLoading').innerHTML = '<p class="text-danger">Error al cargar módulos.</p>';
        });
}

function saveModules() {
    const modulos = {};
    document.querySelectorAll('.module-check:checked').forEach(cb => {
        modulos[cb.dataset.modulo] = { visible: true, orden: 0 };
    });

    fetch('/business-types/' + currentModulesTypeId + '/modules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ modulos })
    })
    .then(r => {
        if (!r.ok) {
            return r.text().then(t => { throw new Error('HTTP ' + r.status + ': ' + t.substring(0, 200)); });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modulesModal')).hide();
            Swal.fire({ icon: 'success', title: 'Módulos actualizados', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1200);
        }
    })
    .catch(err => {
        console.error('Error guardando módulos:', err);
        Swal.fire({ icon: 'error', title: 'Error al guardar', text: err.message || 'Error de red' });
    });
}
</script>
@endsection
