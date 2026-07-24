@extends('layouts.app')

@section('title', 'Editar ' . $tipo->nombre)

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-cpu me-2"></i>Editar: {{ $tipo->nombre }}</h2>
            <p class="text-muted mb-0">Modificar tipo de equipo de climatización</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('climatizacion.tipos-equipos.update', $tipo) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tipo->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $tipo->slug) }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select name="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                            <option value="residencial" {{ old('categoria', $tipo->categoria) === 'residencial' ? 'selected' : '' }}>Residencial</option>
                            <option value="comercial" {{ old('categoria', $tipo->categoria) === 'comercial' ? 'selected' : '' }}>Comercial</option>
                            <option value="industrial" {{ old('categoria', $tipo->categoria) === 'industrial' ? 'selected' : '' }}>Industrial</option>
                        </select>
                        @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Icono (clase Bootstrap)</label>
                        <input type="text" name="icono" class="form-control @error('icono') is-invalid @enderror" value="{{ old('icono', $tipo->icono) }}" placeholder="ej: bi-snow">
                        @error('icono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Orden</label>
                        <input type="number" name="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $tipo->orden) }}" min="0">
                        @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" {{ old('activo', $tipo->activo) ? 'checked' : '' }} id="activo">
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
                    <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="btn btn-outline-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
