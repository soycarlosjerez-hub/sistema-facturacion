@extends('layouts.app')
@section('title', 'Artistas')
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
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-people me-1"></i>DIRECTORIO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Artistas</h2>
                    <p class="mb-0 opacity-75">Artistas y autores de las obras de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#artistaModal">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Artista
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('arte.artistas.index') }}">
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="ui-input" value="{{ request('q') }}" placeholder="Buscar por nombre, email o nacionalidad...">
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
                            <th>Artista</th>
                            <th>Nacionalidad</th>
                            <th>Contacto</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($artistas as $a)
                        <tr>
                            <td class="ps-4">{{ $a->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($a->foto)
                                        <img src="{{ asset('storage/' . $a->foto) }}" width="36" height="36" class="rounded-circle object-fit-cover" alt="{{ $a->nombre }}">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-person text-muted"></i></div>
                                    @endif
                                    <span class="fw-semibold">{{ $a->nombre }}</span>
                                </div>
                            </td>
                            <td>{{ $a->nacionalidad ?? '—' }}</td>
                            <td>
                                @if($a->email)<div class="small">{{ $a->email }}</div>@endif
                                @if($a->telefono)<div class="small text-muted">{{ $a->telefono }}</div>@endif
                            </td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $a->obras_count }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $a->activo ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $a->activo ? 'Sí' : 'No' }}</span>
                            </td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="#" class="ui-action ui-action-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('arte.artistas.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar al artista {{ addslashes($a->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay artistas registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">{{ $artistas->links() }}</div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="artistaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('arte.artistas.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            @csrf
            <div class="ui-card-accent" style="background:#8b5cf6"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2" style="color:var(--accent,#8b5cf6)"></i>Nuevo Artista</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre *</label>
                        <input type="text" name="nombre" class="ui-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Nacionalidad</label>
                        <input type="text" name="nacionalidad" class="ui-input" placeholder="Dominicana, Española...">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Año de nacimiento</label>
                        <input type="number" name="ano_nacimiento" class="ui-input" min="1000" max="2100">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Foto</label>
                        <input type="file" name="foto" class="ui-input" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Biografía</label>
                        <textarea name="bio" class="ui-textarea" rows="3"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-textarea" rows="2"></textarea>
                    </div>
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
@foreach($artistas as $a)
<div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('arte.artistas.update', $a) }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            @csrf @method('PUT')
            <div class="ui-card-accent" style="background:#e1306c"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2" style="color:var(--accent,#8b5cf6)"></i>Editar Artista</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre *</label>
                        <input type="text" name="nombre" class="ui-input" value="{{ $a->nombre }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Nacionalidad</label>
                        <input type="text" name="nacionalidad" class="ui-input" value="{{ $a->nacionalidad }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input" value="{{ $a->email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input" value="{{ $a->telefono }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Año de nacimiento</label>
                        <input type="number" name="ano_nacimiento" class="ui-input" min="1000" max="2100" value="{{ $a->ano_nacimiento }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Foto</label>
                        <input type="file" name="foto" class="ui-input" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Biografía</label>
                        <textarea name="bio" class="ui-textarea" rows="3">{{ $a->bio }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-textarea" rows="2">{{ $a->notas }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="aactivo{{ $a->id }}" {{ $a->activo ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="aactivo{{ $a->id }}">Activo</label>
                        </div>
                    </div>
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