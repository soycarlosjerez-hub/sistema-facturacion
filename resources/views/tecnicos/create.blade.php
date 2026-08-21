@extends('layouts.app')
@section('title', 'Nuevo Técnico')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nuevo Técnico</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra un técnico en el sistema
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnicos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <i class="bi bi-exclamation-triangle me-2"></i>Por favor corrige los errores del formulario.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="tecnicoForm" action="{{ route('tecnicos.store') }}" method="POST">
                    @csrf

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #4f46e5;">
                            <i class="bi bi-person-vcard me-2"></i>Datos Personales
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="ui-label small fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cedula" class="ui-label small fw-semibold">Cédula</label>
                                <input type="text" name="cedula" id="cedula" class="ui-input @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}">
                                @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="ui-label small fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="ui-input @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="ui-label small fw-semibold">Email</label>
                                <input type="email" name="email" id="email" class="ui-input @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                            <i class="bi bi-tools me-2"></i>Información Profesional
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="especialidad" class="ui-label small fw-semibold">Especialidad Principal <span class="text-danger">*</span></label>
                                <select name="especialidad" id="especialidad" class="ui-select @error('especialidad') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->nombre }}" {{ old('especialidad') === $esp->nombre ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                                    @endforeach
                                    <option value="General" {{ old('especialidad') === 'General' ? 'selected' : '' }}>General</option>
                                </select>
                                @error('especialidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="ui-label small fw-semibold d-block">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(99,102,241,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', true) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="activo">Activo</label>
                                    </div>
                                    <small class="text-muted">Si está inactivo no aparecerá en las listas.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tarifa_hora" class="ui-label small fw-semibold">Tarifa por Hora (RD$)</label>
                                <input type="number" step="0.01" min="0" name="tarifa_hora" id="tarifa_hora" class="ui-input @error('tarifa_hora') is-invalid @enderror" value="{{ old('tarifa_hora') }}">
                                @error('tarifa_hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tarifa_fija" class="ui-label small fw-semibold">Tarifa Fija (RD$)</label>
                                <input type="number" step="0.01" min="0" name="tarifa_fija" id="tarifa_fija" class="ui-input @error('tarifa_fija') is-invalid @enderror" value="{{ old('tarifa_fija') }}">
                                @error('tarifa_fija')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($especialidades->count())
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3" style="color: #4f46e5;">
                            <i class="bi bi-award me-2"></i>Especialidades Adicionales
                        </h6>
                        <div class="row g-2">
                            @foreach($especialidades as $esp)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="{{ $esp->id }}" id="esp-{{ $esp->id }}"
                                        {{ in_array($esp->id, old('especialidades', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="esp-{{ $esp->id }}">{{ $esp->nombre }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #64748b;">
                            <i class="bi bi-journal-text me-2"></i>Notas
                        </h6>
                        <textarea name="notas" id="notas" class="ui-input @error('notas') is-invalid @enderror" rows="3" placeholder="Observaciones adicionales del técnico">{{ old('notas') }}</textarea>
                        @error('notas')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <i class="bi bi-info-circle" style="color:#6366f1;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo técnico</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tecnicos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="tecnicoForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Técnico
            </button>
        </div>
    </div>
</div>
@endsection
