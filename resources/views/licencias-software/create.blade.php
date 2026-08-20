@extends('layouts.app')
@section('title', 'Nueva Licencia de Software')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Licencia de Software</h4>
                    <div class="ui-header-meta">Registra una nueva clave de licencia</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('licencias-software.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="producto_id" class="form-label fw-bold">Producto</label>
                            <select name="producto_id" id="producto_id" class="form-select @error('producto_id') is-invalid @enderror">
                                <option value="">-- Seleccionar producto --</option>
                                @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                    {{ $producto->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('producto_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="clave_licencia" class="form-label fw-bold">Clave de Licencia *</label>
                            <input type="text" name="clave_licencia" id="clave_licencia" class="form-control @error('clave_licencia') is-invalid @enderror" value="{{ old('clave_licencia') }}" required>
                            <small class="text-muted">Ingresa la clave única de activación del software</small>
                            @error('clave_licencia')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_licencia" class="form-label fw-bold">Tipo de Licencia</label>
                                <select name="tipo_licencia" id="tipo_licencia" class="form-select @error('tipo_licencia') is-invalid @enderror">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="perpetua" {{ old('tipo_licencia') == 'perpetua' ? 'selected' : '' }}>Perpetua</option>
                                    <option value="suscripcion" {{ old('tipo_licencia') == 'suscripcion' ? 'selected' : '' }}>Suscripción</option>
                                    <option value="open_source" {{ old('tipo_licencia') == 'open_source' ? 'selected' : '' }}>Open Source</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="plataforma" class="form-label fw-bold">Plataforma</label>
                                <select name="plataforma" id="plataforma" class="form-select @error('plataforma') is-invalid @enderror">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Windows" {{ old('plataforma') == 'Windows' ? 'selected' : '' }}>Windows</option>
                                    <option value="macOS" {{ old('plataforma') == 'macOS' ? 'selected' : '' }}>macOS</option>
                                    <option value="Linux" {{ old('plataforma') == 'Linux' ? 'selected' : '' }}>Linux</option>
                                    <option value="Cloud" {{ old('plataforma') == 'Cloud' ? 'selected' : '' }}>Cloud/Web</option>
                                    <option value="Multi-plataforma" {{ old('plataforma') == 'Multi-plataforma' ? 'selected' : '' }}>Multi-plataforma</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="usuario_asignado" class="form-label fw-bold">Usuario Asignado</label>
                            <input type="text" name="usuario_asignado" id="usuario_asignado" class="form-control @error('usuario_asignado') is-invalid @enderror" value="{{ old('usuario_asignado') }}" placeholder="Nombre del usuario o empresa">
                        </div>

                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label fw-bold">Fecha de Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}">
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas</label>
                            <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror" rows="3" placeholder="Notas adicionales sobre la licencia">{{ old('notas') }}</textarea>
                            @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="licencia_activa" id="licencia_activa" {{ old('licencia_activa', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="licencia_activa">Licencia Activa</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Licencia
                            </button>
                            <a href="{{ route('licencias-software.index') }}" class="btn btn-secondary">
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
