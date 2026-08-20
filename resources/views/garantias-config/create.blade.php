@extends('layouts.app')
@section('title', 'Nueva Configuración de Garantía')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Configuración de Garantía</h4>
                    <div class="ui-header-meta">Define los parámetros de garantía para productos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('garantias-config.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Garantía *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            <small class="text-muted">Ej: Garantía Estándar Laptops, Garantía Extendida Servidores</small>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_producto" class="form-label fw-bold">Tipo de Producto</label>
                                <select name="tipo_producto" id="tipo_producto" class="form-select @error('tipo_producto') is-invalid @enderror">
                                    <option value="">-- General (todos los productos) --</option>
                                    <option value="laptop" {{ old('tipo_producto') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                                    <option value="desktop" {{ old('tipo_producto') == 'desktop' ? 'selected' : '' }}>Desktop/PC</option>
                                    <option value="servidor" {{ old('tipo_producto') == 'servidor' ? 'selected' : '' }}>Servidor</option>
                                    <option value="impresora" {{ old('tipo_producto') == 'impresora' ? 'selected' : '' }}>Impresora</option>
                                    <option value="red" {{ old('tipo_producto') == 'red' ? 'selected' : '' }}>Equipo de Red</option>
                                    <option value="cámara" {{ old('tipo_producto') == 'cámara' ? 'selected' : '' }}>Cámara/Seguridad</option>
                                    <option value="accesorio" {{ old('tipo_producto') == 'accesorio' ? 'selected' : '' }}>Accesorio</option>
                                    <option value="software" {{ old('tipo_producto') == 'software' ? 'selected' : '' }}>Software</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dias_garantia" class="form-label fw-bold">Días de Garantía *</label>
                                <input type="number" name="dias_garantia" id="dias_garantia" class="form-control @error('dias_garantia') is-invalid @enderror" value="{{ old('dias_garantia', 90) }}" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_garantia" class="form-label fw-bold">Tipo de Garantía *</label>
                                <select name="tipo_garantia" id="tipo_garantia" class="form-select @error('tipo_garantia') is-invalid @enderror" required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="fabrica" {{ old('tipo_garantia') == 'fabrica' ? 'selected' : '' }}>Garantía de Fábrica</option>
                                    <option value="extendida" {{ old('tipo_garantia') == 'extendida' ? 'selected' : '' }}>Garantía Extendida</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="orden" class="form-label fw-bold">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cobertura" class="form-label fw-bold">Cobertura</label>
                            <textarea name="cobertura" id="cobertura" class="form-control @error('cobertura') is-invalid @enderror" rows="3" placeholder="Describe qué cubre la garantía y qué no">{{ old('cobertura') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Configuración Activa</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Configuración
                            </button>
                            <a href="{{ route('garantias-config.index') }}" class="btn btn-secondary">
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
