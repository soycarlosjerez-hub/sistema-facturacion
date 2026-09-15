@extends('layouts.app')

@section('title', 'Nueva Capacitación')

@push('styles')
@include('partials.premium-ui')
<style>
    .form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
    }
</style>
@endpush

@section('content')
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Capacitación</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.capacitaciones.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Programar una nueva capacitación
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.capacitaciones.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-info-circle me-1"></i> Información General
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo') }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control form-control-custom" value="{{ old('fecha') }}" required>
                        @error('fecha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-custom">Hora Inicio</label>
                        <input type="time" name="hora_inicio" class="form-control form-control-custom" value="{{ old('hora_inicio') }}">
                        @error('hora_inicio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Hora Fin</label>
                        <input type="time" name="hora_fin" class="form-control form-control-custom" value="{{ old('hora_fin') }}">
                        @error('hora_fin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Duración (horas)</label>
                        <input type="number" name="duracion_horas" class="form-control form-control-custom" value="{{ old('duracion_horas') }}" min="1">
                        @error('duracion_horas')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Modalidad <span class="text-danger">*</span></label>
                        <select name="modalidad" class="form-select form-select-custom" required>
                            <option value="presencial" {{ old('modalidad')=='presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="virtual" {{ old('modalidad')=='virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="hibrido" {{ old('modalidad')=='hibrido' ? 'selected' : '' }}>Híbrido</option>
                        </select>
                        @error('modalidad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Lugar</label>
                        <input type="text" name="lugar" class="form-control form-control-custom" value="{{ old('lugar') }}">
                        @error('lugar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Instructor (Usuario)</label>
                        <select name="instructor_id" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('instructor_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('instructor_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Nombre Instructor (externo)</label>
                        <input type="text" name="instructor_nombre" class="form-control form-control-custom" value="{{ old('instructor_nombre') }}">
                        @error('instructor_nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Temas</label>
                        <textarea name="temas" class="form-control form-control-custom" rows="2" placeholder="Temas a cubrir">{{ old('temas') }}</textarea>
                        @error('temas')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="programada" {{ old('estado')=='programada' ? 'selected' : '' }}>Programada</option>
                            <option value="en_curso" {{ old('estado')=='en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="completada" {{ old('estado')=='completada' ? 'selected' : '' }}>Completada</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.capacitaciones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
