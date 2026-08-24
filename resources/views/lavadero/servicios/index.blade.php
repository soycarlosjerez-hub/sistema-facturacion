@extends('layouts.app')
@section('title', 'Servicios de Lavado')
@push('styles')
@include('partials.premium-ui')
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
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Servicios de Lavado</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-card-checklist me-1"></i>
                        <span>Catálogo de servicios disponibles</span>
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $servicios->count() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#servicioModal">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Servicio
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Duración</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td class="fw-medium">{{ $s->nombre }}</td>
                            <td>{{ $s->categoria ?? '—' }}</td>
                            <td class="text-end fw-bold" style="color:#06b6d4;">RD$ {{ number_format($s->precio, 2) }}</td>
                            <td class="text-center">{{ $s->duracion_minutos ? $s->duracion_minutos . ' min' : '—' }}</td>
                            <td class="text-center">
                                <span class="ui-badge {{ $s->activo ? 'ui-badge-success' : 'ui-badge-neutral' }}">
                                    {{ $s->activo ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="#" class="ui-action ui-action-edit me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $s->id }}" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form id="del-servicio-{{ $s->id }}" action="{{ route('lavadero.servicios.destroy', $s) }}" method="POST" class="d-inline">@csrf @method('DELETE')</form>
                                <button type="button" class="ui-action ui-action-delete" title="Eliminar"
                                        onclick="UI.confirm.deleteWithForm('del-servicio-{{ $s->id }}', '{{ addslashes($s->nombre) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="ui-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No hay servicios registrados</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="servicioModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.servicios.store') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Servicio</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre</label>
                    <input type="text" name="nombre" class="ui-input" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="2"></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="ui-label">Precio (RD$)</label>
                        <input type="number" name="precio" class="ui-input" step="0.01" min="0" required>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Costo (RD$)</label>
                        <input type="number" name="precio_compra" class="ui-input" step="0.01" min="0" value="0">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Duración (min)</label>
                        <input type="number" name="duracion_minutos" class="ui-input" min="1">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Categoría</label>
                        <input type="text" name="categoria" class="ui-input" placeholder="Ej: Lavado, Detail, Mecánica">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales Editar --}}
@foreach($servicios as $s)
<div class="modal fade" id="editModal{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.servicios.update', $s) }}" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Servicio</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre</label>
                    <input type="text" name="nombre" class="ui-input" value="{{ $s->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="2">{{ $s->descripcion }}</textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="ui-label">Precio (RD$)</label>
                        <input type="number" name="precio" class="ui-input" step="0.01" min="0" value="{{ $s->precio }}" required>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Costo (RD$)</label>
                        <input type="number" name="precio_compra" class="ui-input" step="0.01" min="0" value="{{ $s->precio_compra }}">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Duración (min)</label>
                        <input type="number" name="duracion_minutos" class="ui-input" min="1" value="{{ $s->duracion_minutos }}">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Categoría</label>
                        <input type="text" name="categoria" class="ui-input" value="{{ $s->categoria }}">
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo{{ $s->id }}" {{ $s->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo{{ $s->id }}">Activo</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
