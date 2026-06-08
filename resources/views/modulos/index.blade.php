@extends('layouts.app')

@section('title', 'Módulos del Sistema')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-grid text-primary me-2"></i>Módulos del Sistema</h2>
            <p class="text-muted mb-0">Define los módulos disponibles y su configuración en el sidebar</p>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Módulo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Módulo</th>
                            <th>Clave</th>
                            <th>Categoría</th>
                            <th>Ruta Sidebar</th>
                            <th>Permiso</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Orden</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modulos as $categoria => $items)
                            <tr class="table-light">
                                <td colspan="9" class="fw-bold text-primary">
                                    <i class="bi bi-folder2 me-1"></i>{{ ucfirst($categoria) }}
                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ count($items) }}</span>
                                </td>
                            </tr>
                            @foreach($items as $modulo)
                                <tr id="row-{{ $modulo->id }}">
                                    <td class="text-muted">{{ $modulo->orden }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi {{ $modulo->icon }} text-muted"></i>
                                            <span class="fw-semibold">{{ $modulo->label }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $modulo->key }}</code></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $modulo->categoria }}</span></td>
                                    <td><small class="text-muted">{{ $modulo->sidebar_route ?? '-' }}</small></td>
                                    <td><small class="text-muted">{{ $modulo->sidebar_permission ?? '-' }}</small></td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                {{ $modulo->activo ? 'checked' : '' }}
                                                onchange="toggleModulo({{ $modulo->id }}, this.checked)">
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $modulo->orden }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary rounded-start-pill"
                                                onclick="openEditModal({{ $modulo->toJson() }})" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('modulos.destroy', $modulo) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Eliminar este módulo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger rounded-end-pill" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-grid display-4 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0">No hay módulos configurados.</p>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('modulos.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Nuevo Módulo</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Clave *</label>
                            <input type="text" name="key" class="form-control rounded-3" required
                                placeholder="mi-modulo" pattern="[a-z0-9\-]+">
                            <div class="form-text">Minúsculas, números y guiones.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nombre *</label>
                            <input type="text" name="label" class="form-control rounded-3" required
                                placeholder="Mi Módulo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="form-control rounded-3" value="bi-grid"
                                placeholder="bi-grid">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Categoría *</label>
                            <input type="text" name="categoria" class="form-control rounded-3" required
                                placeholder="core" list="categoriasList">
                            <datalist id="categoriasList">
                                <option value="core">
                                <option value="operaciones">
                                <option value="clientes">
                                <option value="organizacion">
                                <option value="restaurante">
                                <option value="reportes">
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Orden</label>
                            <input type="number" name="orden" class="form-control rounded-3" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Permiso Sidebar</label>
                            <input type="text" name="sidebar_permission" class="form-control rounded-3"
                                placeholder="modulos.view">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ruta Sidebar</label>
                            <input type="text" name="sidebar_route" class="form-control rounded-3"
                                placeholder="modulos.index">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">is_route (Sidebar)</label>
                            <input type="text" name="sidebar_is_route" class="form-control rounded-3"
                                placeholder="modulos.*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">exact_route (Sidebar)</label>
                            <input type="text" name="sidebar_exact_route" class="form-control rounded-3"
                                placeholder="modulos.index">
                        </div>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Editar Módulo</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Clave</label>
                            <input type="text" class="form-control rounded-3" id="edit_key" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nombre *</label>
                            <input type="text" name="label" id="edit_label" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Icono</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Categoría *</label>
                            <input type="text" name="categoria" id="edit_categoria" class="form-control rounded-3" required
                                list="categoriasList2">
                            <datalist id="categoriasList2">
                                <option value="core">
                                <option value="operaciones">
                                <option value="clientes">
                                <option value="organizacion">
                                <option value="restaurante">
                                <option value="reportes">
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Orden</label>
                            <input type="number" name="orden" id="edit_orden" class="form-control rounded-3" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Permiso Sidebar</label>
                            <input type="text" name="sidebar_permission" id="edit_sidebar_permission" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ruta Sidebar</label>
                            <input type="text" name="sidebar_route" id="edit_sidebar_route" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">is_route</label>
                            <input type="text" name="sidebar_is_route" id="edit_sidebar_is_route" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">exact_route</label>
                            <input type="text" name="sidebar_exact_route" id="edit_sidebar_exact_route" class="form-control rounded-3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('editForm').action = '/modulos/' + data.id;
    document.getElementById('edit_key').value = data.key;
    document.getElementById('edit_label').value = data.label;
    document.getElementById('edit_icon').value = data.icon;
    document.getElementById('edit_categoria').value = data.categoria;
    document.getElementById('edit_orden').value = data.orden;
    document.getElementById('edit_sidebar_permission').value = data.sidebar_permission || '';
    document.getElementById('edit_sidebar_route').value = data.sidebar_route || '';
    document.getElementById('edit_sidebar_is_route').value = data.sidebar_is_route || '';
    document.getElementById('edit_sidebar_exact_route').value = data.sidebar_exact_route || '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function toggleModulo(id, activo) {
    fetch('/modulos/' + id + '/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(r => r.json()).then(data => {
        if (!data.success) Swal.fire({ icon: 'error', title: 'Error' });
    });
}
</script>
@endsection
