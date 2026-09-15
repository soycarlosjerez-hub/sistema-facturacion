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
                    <div class="ui-header-title">Nueva Licencia de Software</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva clave de licencia
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('licencias-software.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="licenciaForm" action="{{ route('licencias-software.store') }}" method="POST">
                    @csrf

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #0891b2;">
                            <i class="bi bi-box-seam me-2"></i>Producto
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="producto_id" class="ui-label">Producto</label>
                                <select name="producto_id" id="producto_id" class="ui-select @error('producto_id') is-invalid @enderror">
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
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                            <i class="bi bi-shield-lock me-2"></i>Clave de Licencia
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="clave_licencia" class="ui-label">Clave de Licencia <span class="text-danger">*</span></label>
                                <input type="text" name="clave_licencia" id="clave_licencia" class="ui-input @error('clave_licencia') is-invalid @enderror" value="{{ old('clave_licencia') }}" required>
                                <small class="text-muted">Ingresa la clave única de activación del software</small>
                                @error('clave_licencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #059669;">
                            <i class="bi bi-gear me-2"></i>Configuración
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipo_licencia" class="ui-label">Tipo de Licencia</label>
                                <select name="tipo_licencia" id="tipo_licencia" class="ui-select @error('tipo_licencia') is-invalid @enderror">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="perpetua" {{ old('tipo_licencia') == 'perpetua' ? 'selected' : '' }}>Perpetua</option>
                                    <option value="suscripcion" {{ old('tipo_licencia') == 'suscripcion' ? 'selected' : '' }}>Suscripción</option>
                                    <option value="open_source" {{ old('tipo_licencia') == 'open_source' ? 'selected' : '' }}>Open Source</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="plataforma" class="ui-label">Plataforma</label>
                                <select name="plataforma" id="plataforma" class="ui-select @error('plataforma') is-invalid @enderror">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Windows" {{ old('plataforma') == 'Windows' ? 'selected' : '' }}>Windows</option>
                                    <option value="macOS" {{ old('plataforma') == 'macOS' ? 'selected' : '' }}>macOS</option>
                                    <option value="Linux" {{ old('plataforma') == 'Linux' ? 'selected' : '' }}>Linux</option>
                                    <option value="Cloud" {{ old('plataforma') == 'Cloud' ? 'selected' : '' }}>Cloud/Web</option>
                                    <option value="Multi-plataforma" {{ old('plataforma') == 'Multi-plataforma' ? 'selected' : '' }}>Multi-plataforma</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="usuario_asignado" class="ui-label">Usuario Asignado</label>
                                <input type="text" name="usuario_asignado" id="usuario_asignado" class="ui-input @error('usuario_asignado') is-invalid @enderror" value="{{ old('usuario_asignado') }}" placeholder="Nombre del usuario o empresa">
                            </div>
                            <div class="col-12">
                                <label for="fecha_vencimiento" class="ui-label">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="ui-input @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}">
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                            <i class="bi bi-journal-text me-2"></i>Notas y Estado
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notas" class="ui-label">Notas</label>
                                <textarea name="notas" id="notas" class="ui-textarea @error('notas') is-invalid @enderror" rows="3" placeholder="Notas adicionales sobre la licencia">{{ old('notas') }}</textarea>
                                @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="licencia_activa" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="licencia_activa" id="licencia_activa" {{ old('licencia_activa', true) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="licencia_activa">Licencia Activa</label>
                                    </div>
                                    <small class="text-muted">Si está inactiva no podrá usarse.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#06b6d4;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva licencia</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('licencias-software.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="licenciaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Licencia
            </button>
        </div>
    </div>
</div>
@endsection
