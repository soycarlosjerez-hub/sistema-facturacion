@extends('layouts.app')

@section('title', 'Editar Diseño')

@section('content')
<div class="container-fluid px-4">
    <a href="{{ route('tattoo.disenos.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-transparent pt-4 px-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Editar: {{ $diseno->titulo }}</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tattoo.disenos.update', $diseno) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control rounded-3" required value="{{ old('titulo', $diseno->titulo) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Estilo</label>
                        <input type="text" name="estilo" class="form-control rounded-3" value="{{ old('estilo', $diseno->estilo) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Artista</label>
                        <select name="artist_id" class="form-select rounded-3">
                            <option value="">— Sin artista —</option>
                            @foreach($artistas as $a)
                                <option value="{{ $a->id }}" {{ old('artist_id', $diseno->artist_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Duración (min) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_estimada_min" class="form-control rounded-3" min="15" value="{{ old('duracion_estimada_min', $diseno->duracion_estimada_min) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">URL Imagen Portada</label>
                        <input type="text" name="imagen_portada" class="form-control rounded-3" value="{{ old('imagen_portada', $diseno->imagen_portada) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Precio Mínimo <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="precio_minimo" class="form-control" min="0" step="0.01" required value="{{ old('precio_minimo', $diseno->precio_minimo) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Precio Máximo <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="precio_maximo" class="form-control" min="0" step="0.01" required value="{{ old('precio_maximo', $diseno->precio_maximo) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ old('descripcion', $diseno->descripcion) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="popular" class="form-check-input" id="popular" value="1" {{ old('popular', $diseno->popular) ? 'checked' : '' }}>
                            <label class="form-check-label" for="popular">Popular</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" {{ old('activo', $diseno->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Actualizar Diseño</button>
                    <a href="{{ route('tattoo.disenos.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
