@extends('layouts.app')
@section('title', 'Editar Especialidad Técnica')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Especialidad: {{ $tecnicaEspecialidad->nombre }}</h4>
                    <div class="ui-header-meta">Actualiza los datos de la especialidad técnica</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tecnica-especialidades.update', $tecnicaEspecialidad) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Especialidad *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tecnicaEspecialidad->nombre) }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $tecnicaEspecialidad->descripcion) }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="orden" class="form-label fw-bold">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $tecnicaEspecialidad->orden) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="form-label fw-bold">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', $tecnicaEspecialidad->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Especialidad
                            </button>
                            <a href="{{ route('tecnica-especialidades.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
