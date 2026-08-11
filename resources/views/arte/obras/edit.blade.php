@extends('layouts.app')
@section('title', 'Editar Obra')
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
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Obra</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $obra->titulo }}</p>
                </div>
            </div>
            <a href="{{ route('arte.obras.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <form method="POST" action="{{ route('arte.obras.update', $obra) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Título *</label>
                    <input type="text" name="titulo" class="form-control rounded-3 @error('titulo') is-invalid @enderror" value="{{ old('titulo', $obra->titulo) }}" required>
                    @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Artista *</label>
                    <select name="artista_id" class="form-select rounded-3 @error('artista_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($artistas as $a)
                            <option value="{{ $a->id }}" {{ old('artista_id', $obra->artista_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                    @error('artista_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ old('descripcion', $obra->descripcion) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Colección</label>
                    <select name="coleccion_id" class="form-select rounded-3">
                        <option value="">Sin colección</option>
                        @foreach($colecciones as $c)
                            <option value="{{ $c->id }}" {{ old('coleccion_id', $obra->coleccion_id) == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Técnica</label>
                    <input type="text" name="tecnica" class="form-control rounded-3" value="{{ old('tecnica', $obra->tecnica) }}" placeholder="Óleo, bronce, acrílico...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Año de creación</label>
                    <input type="number" name="ano_creacion" class="form-control rounded-3" value="{{ old('ano_creacion', $obra->ano_creacion) }}" min="1000" max="2100">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Dimensiones</label>
                    <input type="text" name="dimensiones" class="form-control rounded-3" value="{{ old('dimensiones', $obra->dimensiones) }}" placeholder="50 x 40 x 5">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Material</label>
                    <input type="text" name="material" class="form-control rounded-3" value="{{ old('material', $obra->material) }}" placeholder="Lienzo, mármol, madera...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha de adquisición</label>
                    <input type="date" name="fecha_adquisicion" class="form-control rounded-3" value="{{ old('fecha_adquisicion', optional($obra->fecha_adquisicion)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Precio compra (RD$)</label>
                    <input type="number" name="precio_compra" class="form-control rounded-3" step="0.01" min="0" value="{{ old('precio_compra', $obra->precio_compra) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Precio venta (RD$) *</label>
                    <input type="number" name="precio_venta" class="form-control rounded-3 @error('precio_venta') is-invalid @enderror" step="0.01" min="0" value="{{ old('precio_venta', $obra->precio_venta) }}" required>
                    @error('precio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Estado *</label>
                    <select name="estado" class="form-select rounded-3" required>
                        @foreach(['disponible' => 'Disponible', 'vendida' => 'Vendida', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta'] as $k => $v)
                            <option value="{{ $k }}" {{ old('estado', $obra->estado) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Imagen</label>
                    <input type="file" name="imagen" class="form-control rounded-3" accept="image/*">
                    @if($obra->imagen)
                        <div class="mt-2"><img src="{{ asset('storage/' . $obra->imagen) }}" width="80" class="rounded-3" alt="Imagen actual"></div>
                    @endif
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $obra->activo) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="activo">Activo</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <input type="number" name="orden" class="form-control rounded-3" placeholder="Orden" value="{{ old('orden', $obra->orden) }}">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('arte.obras.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Actualizar Obra</button>
            </div>
        </form>
    </div>
</div>
@endsection
