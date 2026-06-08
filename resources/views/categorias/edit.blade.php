@extends('layouts.app')
@section('title', 'Editar Categoría')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tag text-warning me-2"></i>Editar Categoría</h2>
            <p class="text-muted mb-0">{{ $categoria->nombre }}</p>
        </div>
        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $categoria->nombre) }}" required>
                    @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" {{ $categoria->activa ? 'checked' : '' }}>
                        <label class="form-check-label" for="activa">Categoría activa</label>
                    </div>
                </div>
                <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Actualizar</button>
            </form>
        </div>
    </div>
</div>
@endsection