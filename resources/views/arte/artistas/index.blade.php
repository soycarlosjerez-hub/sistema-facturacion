@extends('layouts.app')
@section('title', 'Artistas')
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
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Artistas</h2>
                    <p class="text-white text-opacity-75 mb-0">Artistas y autores de las obras de la galería</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#artistaModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Artista
            </button>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <form method="GET" action="{{ route('arte.artistas.index') }}">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control rounded-3" value="{{ request('q') }}" placeholder="Buscar por nombre, email o nacionalidad...">
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
                        <th>Artista</th>
                        <th>Nacionalidad</th>
                        <th>Contacto</th>
                        <th class="text-center">Obras</th>
                        <th class="text-center">Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($artistas as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($a->foto)
                                    <img src="{{ asset('storage/' . $a->foto) }}" width="36" height="36" class="rounded-circle object-fit-cover" alt="{{ $a->nombre }}">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-person text-muted"></i></div>
                                @endif
                                <span class="fw-medium">{{ $a->nombre }}</span>
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
                        <td class="text-end">
                            <a href="#" class="premium-btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('arte.artistas.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar al artista {{ addslashes($a->nombre) }}?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay artistas registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $artistas->links() }}</div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="artistaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('arte.artistas.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Artista</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nacionalidad</label>
                        <input type="text" name="nacionalidad" class="form-control rounded-3" placeholder="Dominicana, Española...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Año de nacimiento</label>
                        <input type="number" name="ano_nacimiento" class="form-control rounded-3" min="1000" max="2100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Foto</label>
                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Biografía</label>
                        <textarea name="bio" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2"></textarea>
                    </div>
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
@foreach($artistas as $a)
<div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('arte.artistas.update', $a) }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Artista</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $a->nombre }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nacionalidad</label>
                        <input type="text" name="nacionalidad" class="form-control rounded-3" value="{{ $a->nacionalidad }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ $a->email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3" value="{{ $a->telefono }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Año de nacimiento</label>
                        <input type="number" name="ano_nacimiento" class="form-control rounded-3" min="1000" max="2100" value="{{ $a->ano_nacimiento }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Foto</label>
                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Biografía</label>
                        <textarea name="bio" class="form-control rounded-3" rows="3">{{ $a->bio }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2">{{ $a->notas }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="aactivo{{ $a->id }}" {{ $a->activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="aactivo{{ $a->id }}">Activo</label>
                        </div>
                    </div>
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
