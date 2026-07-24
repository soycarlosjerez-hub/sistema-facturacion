@extends('layouts.app')
@section('title', 'Editar ' . $tipo->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: {{ $tipo->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('climatizacion.tipos-equipos.update', $tipo) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Datos del Tipo de Equipo --}}
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-info-circle"></i> Información del Tipo de Equipo
                </h5>
                <div class="row g-3">
                    {{-- Nombre --}}
                    <div class="col-md-6">
                        <label class="ui-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               class="ui-input @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $tipo->nombre) }}" placeholder="Nombre del tipo de equipo" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-6">
                        <label class="ui-label" for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug"
                               class="ui-input @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $tipo->slug) }}" placeholder="ej: minisplit-inverter" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Categoría --}}
                    <div class="col-md-4">
                        <label class="ui-label" for="categoria">Categoría <span class="text-danger">*</span></label>
                        <select name="categoria" id="categoria"
                                class="ui-select @error('categoria') is-invalid @enderror" required>
                            <option value="">Seleccionar categoría...</option>
                            <option value="residencial" {{ old('categoria', $tipo->categoria) === 'residencial' ? 'selected' : '' }}>Residencial</option>
                            <option value="comercial" {{ old('categoria', $tipo->categoria) === 'comercial' ? 'selected' : '' }}>Comercial</option>
                            <option value="industrial" {{ old('categoria', $tipo->categoria) === 'industrial' ? 'selected' : '' }}>Industrial</option>
                        </select>
                        @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Icono --}}
                    <div class="col-md-4">
                        <label class="ui-label" for="icono">Icono (clase Bootstrap)</label>
                        <input type="text" name="icono" id="icono"
                               class="ui-input @error('icono') is-invalid @enderror"
                               value="{{ old('icono', $tipo->icono) }}" placeholder="ej: bi-snow">
                        @error('icono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Clase de Bootstrap Icons, ej: <code>bi-snow</code>, <code>bi-thermometer</code></div>
                    </div>

                    {{-- Orden --}}
                    <div class="col-md-2">
                        <label class="ui-label" for="orden">Orden</label>
                        <input type="number" name="orden" id="orden"
                               class="ui-input @error('orden') is-invalid @enderror"
                               value="{{ old('orden', $tipo->orden) }}" min="0" placeholder="0">
                        @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Activo --}}
                    <div class="col-md-2 d-flex align-items-end pb-2">
                        <div class="form-check form-switch">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                   value="1" {{ old('activo', $tipo->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="bi bi-toggle-on me-1"></i> Activo
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Bar --}}
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg"></i> Actualizar
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
