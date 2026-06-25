@extends('layouts.app')
@section('title', 'Servicios de Lavado')
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
                    <h2 class="fw-bold mb-0 text-white">Servicios de Lavado</h2>
                    <p class="text-white text-opacity-75 mb-0">Catálogo de servicios disponibles</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#servicioModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Servicio
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
                        <th>Categoría</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Duración</th>
                        <th class="text-center">Activo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicios as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td class="fw-medium">{{ $s->nombre }}</td>
                        <td>{{ $s->categoria ?? '—' }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($s->precio, 2) }}</td>
                        <td class="text-center">{{ $s->duracion_minutos ? $s->duracion_minutos . ' min' : '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $s->activo ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                {{ $s->activo ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="#" class="premium-btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $s->id }}">Editar</a>
                            <form action="{{ route('lavadero.servicios.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                    <label class="form-label small fw-bold">Nombre</label>
                    <input type="text" name="nombre" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="2"></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Precio (RD$)</label>
                        <input type="number" name="precio" class="form-control rounded-3" step="0.01" min="0" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Costo (RD$)</label>
                        <input type="number" name="precio_compra" class="form-control rounded-3" step="0.01" min="0" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Duración (min)</label>
                        <input type="number" name="duracion_minutos" class="form-control rounded-3" min="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Categoría</label>
                        <input type="text" name="categoria" class="form-control rounded-3" placeholder="Ej: Lavado, Detail, Mecánica">
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
                    <label class="form-label small fw-bold">Nombre</label>
                    <input type="text" name="nombre" class="form-control rounded-3" value="{{ $s->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="2">{{ $s->descripcion }}</textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Precio (RD$)</label>
                        <input type="number" name="precio" class="form-control rounded-3" step="0.01" min="0" value="{{ $s->precio }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Costo (RD$)</label>
                        <input type="number" name="precio_compra" class="form-control rounded-3" step="0.01" min="0" value="{{ $s->precio_compra }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Duración (min)</label>
                        <input type="number" name="duracion_minutos" class="form-control rounded-3" min="1" value="{{ $s->duracion_minutos }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Categoría</label>
                        <input type="text" name="categoria" class="form-control rounded-3" value="{{ $s->categoria }}">
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo{{ $s->id }}" {{ $s->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo{{ $s->id }}">Activo</label>
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
