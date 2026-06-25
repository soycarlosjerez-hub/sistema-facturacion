@extends('layouts.app')
@section('title', isset($modulo) ? "Editar Módulo - {$modulo->label}" : 'Nuevo Módulo')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem;">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-grid"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">
                        {{ isset($modulo) ? 'Editar Módulo' : 'Nuevo Módulo' }}
                    </h2>
                    @if(isset($modulo))
                        <p class="mb-0 opacity-75">{{ $modulo->key }} &middot; {{ $modulo->label }}</p>
                    @else
                        <p class="mb-0 opacity-75">Crea un nuevo módulo para asignar a tipos de negocio y roles.</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('owner.modules.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ isset($modulo) ? route('owner.modules.update', $modulo) : route('owner.modules.store') }}">
                        @csrf
                        @if(isset($modulo)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Key <span class="text-danger">*</span></label>
                            <input type="text" name="key" class="form-control rounded-pill @error('key') is-invalid @enderror"
                                   value="{{ old('key', $modulo->key ?? '') }}" required
                                   placeholder="ej: mi-modulo" {{ isset($modulo) ? 'readonly' : '' }}>
                            <small class="text-muted">Identificador único. Usa guiones, sin espacios. No se puede cambiar después de crear.</small>
                            @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control rounded-pill @error('label') is-invalid @enderror"
                                   value="{{ old('label', $modulo->label ?? '') }}" required placeholder="Ej: Mi Módulo">
                            @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="form-control rounded-pill @error('icon') is-invalid @enderror"
                                   value="{{ old('icon', $modulo->icon ?? 'bi-circle') }}" placeholder="Ej: bi-box-seam">
                            <small class="text-muted">Clase del icono Bootstrap. <a href="https://icons.getbootstrap.com/" target="_blank">Ver todos</a></small>
                            @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Categoría <span class="text-danger">*</span></label>
                                <select name="categoria" class="form-select rounded-pill @error('categoria') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $cat)
                                    <option value="{{ $cat }}" {{ old('categoria', $modulo->categoria ?? '') === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                    @endforeach
                                    <option value="otro" {{ old('categoria', $modulo->categoria ?? '') === 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Orden</label>
                                <input type="number" name="orden" class="form-control rounded-pill @error('orden') is-invalid @enderror"
                                       value="{{ old('orden', $modulo->orden ?? 0) }}" min="0">
                                @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activo"
                                           value="1" {{ old('activo', $modulo->activo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.modules.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white">
                                <i class="bi bi-check-lg me-2"></i>{{ isset($modulo) ? 'Guardar Cambios' : 'Crear Módulo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
