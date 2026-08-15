@extends('layouts.app')
@section('title', 'Nueva Obra')
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
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-plus-circle me-1"></i>NUEVO REGISTRO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Nueva Obra</h2>
                    <p class="mb-0 opacity-75">Registra una nueva obra o pieza de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.obras.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('arte.obras.store') }}" enctype="multipart/form-data" id="obraForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label" for="titulo">Título *</label>
                        <input type="text" name="titulo" id="titulo" class="ui-input @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                        @error('titulo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="artista_id">Artista *</label>
                        <select name="artista_id" id="artista_id" class="ui-select @error('artista_id') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            @foreach($artistas as $a)
                                <option value="{{ $a->id }}" {{ old('artista_id') == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                            @endforeach
                        </select>
                        @error('artista_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="ui-textarea" rows="3">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="coleccion_id">Colección</label>
                        <select name="coleccion_id" id="coleccion_id" class="ui-select">
                            <option value="">Sin colección</option>
                            @foreach($colecciones as $c)
                                <option value="{{ $c->id }}" {{ old('coleccion_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="tecnica">Técnica</label>
                        <input type="text" name="tecnica" id="tecnica" class="ui-input" value="{{ old('tecnica') }}" placeholder="Óleo, bronce, acrílico...">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="ano_creacion">Año de creación</label>
                        <input type="number" name="ano_creacion" id="ano_creacion" class="ui-input" value="{{ old('ano_creacion') }}" min="1000" max="2100">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="dimensiones">Dimensiones</label>
                        <input type="text" name="dimensiones" id="dimensiones" class="ui-input" value="{{ old('dimensiones') }}" placeholder="50 x 40 x 5">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="material">Material</label>
                        <input type="text" name="material" id="material" class="ui-input" value="{{ old('material') }}" placeholder="Lienzo, mármol, madera...">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="fecha_adquisicion">Fecha de adquisición</label>
                        <input type="date" name="fecha_adquisicion" id="fecha_adquisicion" class="ui-input" value="{{ old('fecha_adquisicion') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="precio_compra">Precio compra (RD$)</label>
                        <input type="number" name="precio_compra" id="precio_compra" class="ui-input" step="0.01" min="0" value="{{ old('precio_compra', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="precio_venta">Precio venta (RD$) *</label>
                        <input type="number" name="precio_venta" id="precio_venta" class="ui-input @error('precio_venta') is-invalid @enderror" step="0.01" min="0" value="{{ old('precio_venta') }}" required>
                        @error('precio_venta') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="estado">Estado *</label>
                        <select name="estado" id="estado" class="ui-select" required>
                            @foreach(['disponible' => 'Disponible', 'vendida' => 'Vendida', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta'] as $k => $v)
                                <option value="{{ $k }}" {{ old('estado', 'disponible') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="ui-label" for="imagen">Imagen</label>
                        <input type="file" name="imagen" id="imagen" class="ui-input" accept="image/*">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="activo">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <input type="number" name="orden" class="ui-input" placeholder="Orden" value="{{ old('orden', 0) }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent,#8b5cf6)"></i>
            <span class="fw-semibold d-none d-sm-inline">Nueva Obra</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('arte.obras.index') }}" class="ui-btn ui-btn-ghost btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" form="obraForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Obra
            </button>
        </div>
    </div>
</div>
</div>
@endsection