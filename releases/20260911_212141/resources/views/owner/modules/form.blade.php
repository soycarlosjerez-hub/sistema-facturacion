@extends('layouts.app')
@section('title', isset($modulo) ? "Editar Módulo - {$modulo->label}" : 'Nuevo Módulo')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

<div class="ui-header mb-4" style="--delay:0s">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-grid"></i>
            </div>
            <div>
                <h4 class="ui-header-title">
                    {{ isset($modulo) ? 'Editar Módulo' : 'Nuevo Módulo' }}
                </h4>
                @if(isset($modulo))
                    <div class="ui-header-meta">
                        <i class="bi bi-code-slash me-1"></i>{{ $modulo->key }} · {{ $modulo->label }}
                    </div>
                @else
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>Crea un nuevo módulo para asignar a tipos de negocio y roles.
                    </div>
                @endif
            </div>
        </div>
        <div class="ui-header-actions">
            <a href="{{ route('owner.modules.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body p-4">
                <form method="POST" action="{{ isset($modulo) ? route('owner.modules.update', $modulo) : route('owner.modules.store') }}">
                    @csrf
                    @if(isset($modulo)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Key <span class="text-danger">*</span></label>
                        <input type="text" name="key" class="form-control form-control-modern @error('key') is-invalid @enderror"
                               value="{{ old('key', $modulo->key ?? '') }}" required
                               placeholder="ej: mi-modulo" {{ isset($modulo) ? 'readonly' : '' }}>
                        <small class="text-muted">Identificador único. Usa guiones, sin espacios. No se puede cambiar después de crear.</small>
                        @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control form-control-modern @error('label') is-invalid @enderror"
                               value="{{ old('label', $modulo->label ?? '') }}" required placeholder="Ej: Mi Módulo">
                        @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Icono (Bootstrap Icons)</label>
                        <input type="text" name="icon" class="form-control form-control-modern @error('icon') is-invalid @enderror"
                               value="{{ old('icon', $modulo->icon ?? 'bi-circle') }}" placeholder="Ej: bi-box-seam">
                        <small class="text-muted">Clase del icono Bootstrap. <a href="https://icons.getbootstrap.com/" target="_blank">Ver todos</a></small>
                        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" class="form-select form-select-modern @error('categoria') is-invalid @enderror" required>
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
                            <input type="number" name="orden" class="form-control form-control-modern @error('orden') is-invalid @enderror"
                                   value="{{ old('orden', $modulo->orden ?? 0) }}" min="0">
                            @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="activo" class="form-check-input-modern" id="activo"
                                       value="1" {{ old('activo', $modulo->activo ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small" for="activo">Activo</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('owner.modules.index') }}" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-check-lg me-2"></i>{{ isset($modulo) ? 'Guardar Cambios' : 'Crear Módulo' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
