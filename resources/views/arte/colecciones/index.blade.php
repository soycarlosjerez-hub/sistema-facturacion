@extends('layouts.app')
@section('title', 'Colecciones')
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
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Colecciones</h2>
                    <p class="text-white text-opacity-75 mb-0">Agrupaciones temáticas de las obras</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#coleccionModal">
                <i class="bi bi-plus-lg me-1"></i> Nueva Colección
            </button>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <form method="GET" action="{{ route('arte.colecciones.index') }}">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control rounded-3" value="{{ request('q') }}" placeholder="Buscar por nombre o descripción...">
                <button class="btn btn-primary rounded-pill ms-2 px-4" type="submit">Buscar</button>
            </div>
        </form>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Colección</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th class="text-center">Obras</th>
                        <th class="text-center">Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colecciones as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td class="fw-medium">{{ $c->nombre }}</td>
                        <td>{{ $c->tipo ? ucfirst($c->tipo) : '—' }}</td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($c->descripcion, 50) ?? '—' }}</td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $c->obras_count }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $c->activo ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $c->activo ? 'Sí' : 'No' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="#" class="premium-btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $c->id }}"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('arte.colecciones.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la colección {{ addslashes($c->nombre) }}?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay colecciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $colecciones->links() }}</div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="coleccionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('arte.colecciones.store') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipo</label>
                    <select name="tipo" class="form-select rounded-3">
                        <option value="">Seleccionar...</option>
                        <option value="reunion">Reunión</option>
                        <option value="tematica">Temática</option>
                        <option value="temporal">Temporal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="3"></textarea>
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
@foreach($colecciones as $c)
<div class="modal fade" id="editModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('arte.colecciones.update', $c) }}" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control rounded-3" value="{{ $c->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipo</label>
                    <select name="tipo" class="form-select rounded-3">
                        <option value="">Seleccionar...</option>
                        @foreach(['reunion' => 'Reunión', 'tematica' => 'Temática', 'temporal' => 'Temporal'] as $k => $v)
                            <option value="{{ $k }}" {{ $c->tipo == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ $c->descripcion }}</textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="cactivo{{ $c->id }}" {{ $c->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="cactivo{{ $c->id }}">Activo</label>
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
@endsection
