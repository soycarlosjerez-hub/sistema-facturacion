@extends('layouts.app')
@section('title', 'Colecciones')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-collection me-1"></i>AGRUPACIONES
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Colecciones</h2>
                    <p class="mb-0 opacity-75">Agrupaciones temáticas de las obras</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#coleccionModal">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Colección
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('arte.colecciones.index') }}">
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="ui-input" value="{{ request('q') }}" placeholder="Buscar por nombre o descripción...">
                    <button class="ui-btn ui-btn-solid rounded-pill ms-2 px-4" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Colección</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($colecciones as $c)
                        <tr>
                            <td class="ps-4">{{ $c->id }}</td>
                            <td class="fw-semibold">{{ $c->nombre }}</td>
                            <td>{{ $c->tipo ? ucfirst($c->tipo) : '—' }}</td>
                            <td class="text-muted small">{{ \Illuminate\Support\Str::limit($c->descripcion, 50) ?? '—' }}</td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $c->obras_count }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $c->activo ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $c->activo ? 'Sí' : 'No' }}</span>
                            </td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="#" class="ui-action ui-action-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $c->id }}" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('arte.colecciones.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la colección {{ addslashes($c->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay colecciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">{{ $colecciones->links() }}</div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="coleccionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('arte.colecciones.store') }}" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            @csrf
            <div class="ui-card-accent" style="background:#8b5cf6"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2" style="color:var(--accent,#8b5cf6)"></i>Nueva Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre *</label>
                    <input type="text" name="nombre" class="ui-input" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Seleccionar...</option>
                        <option value="reunion">Reunión</option>
                        <option value="tematica">Temática</option>
                        <option value="temporal">Temporal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales Editar --}}
@foreach($colecciones as $c)
<div class="modal fade" id="editModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('arte.colecciones.update', $c) }}" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            @csrf @method('PUT')
            <div class="ui-card-accent" style="background:#e1306c"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2" style="color:var(--accent,#8b5cf6)"></i>Editar Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre *</label>
                    <input type="text" name="nombre" class="ui-input" value="{{ $c->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Seleccionar...</option>
                        @foreach(['reunion' => 'Reunión', 'tematica' => 'Temática', 'temporal' => 'Temporal'] as $k => $v)
                            <option value="{{ $k }}" {{ $c->tipo == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="3">{{ $c->descripcion }}</textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="cactivo{{ $c->id }}" {{ $c->activo ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="cactivo{{ $c->id }}">Activo</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endforeach
</div>
@endsection