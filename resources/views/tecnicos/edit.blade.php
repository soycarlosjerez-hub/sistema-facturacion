@extends('layouts.app')
@section('title', 'Editar Técnico')

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
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Técnico</h4>
                    <div class="ui-header-meta">{{ $tecnico->nombre }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnicos.show', $tecnico) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>Por favor corrige los errores del formulario.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <form action="{{ route('tecnicos.update', $tecnico) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="ui-card mb-4" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-primary"></i>Datos Personales</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-bold">Nombre Completo *</label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tecnico->nombre) }}" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cedula" class="form-label fw-bold">Cédula</label>
                                <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula', $tecnico->cedula) }}">
                                @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $tecnico->telefono) }}">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $tecnico->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card mb-4" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-tools me-2 text-primary"></i>Información Profesional</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="especialidad" class="form-label fw-bold">Especialidad Principal *</label>
                                <select name="especialidad" id="especialidad" class="form-select @error('especialidad') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($especialidades as $esp)
                                        <option value="{{ $esp->nombre }}" {{ old('especialidad', $tecnico->especialidad) === $esp->nombre ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                                    @endforeach
                                    <option value="General" {{ old('especialidad', $tecnico->especialidad) === 'General' ? 'selected' : '' }}>General</option>
                                </select>
                                @error('especialidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="form-label fw-bold d-block">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', $tecnico->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tarifa_hora" class="form-label fw-bold">Tarifa por Hora (RD$)</label>
                                <input type="number" step="0.01" min="0" name="tarifa_hora" id="tarifa_hora" class="form-control @error('tarifa_hora') is-invalid @enderror" value="{{ old('tarifa_hora', $tecnico->tarifa_hora) }}">
                                @error('tarifa_hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tarifa_fija" class="form-label fw-bold">Tarifa Fija (RD$)</label>
                                <input type="number" step="0.01" min="0" name="tarifa_fija" id="tarifa_fija" class="form-control @error('tarifa_fija') is-invalid @enderror" value="{{ old('tarifa_fija', $tecnico->tarifa_fija) }}">
                                @error('tarifa_fija')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($especialidades->count())
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-award me-2 text-primary"></i>Especialidades Adicionales</h6>
                        <div class="row g-2">
                            @foreach($especialidades as $esp)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="{{ $esp->id }}" id="esp-{{ $esp->id }}"
                                        {{ in_array($esp->id, old('especialidades', $tecnicoEspecialidadIds)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="esp-{{ $esp->id }}">{{ $esp->nombre }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="ui-card mb-4" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Notas</h6>
                        <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror" rows="3" placeholder="Observaciones adicionales del técnico">{{ old('notas', $tecnico->notas) }}</textarea>
                        @error('notas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('tecnicos.show', $tecnico) }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Actualizar Técnico
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection