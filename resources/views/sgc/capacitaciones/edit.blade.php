@extends('layouts.app')

@section('title', 'Editar Capacitación')

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
                    <h4 class="ui-header-title">Editar {{ Str::limit($capacitacion->titulo, 40) }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.capacitaciones.show', $capacitacion) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando capacitación
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.capacitaciones.update', $capacitacion) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label-custom">Título</label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo', $capacitacion->titulo) }}">
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha</label>
                        <input type="date" name="fecha" class="form-control form-control-custom" value="{{ old('fecha', $capacitacion->fecha?->format('Y-m-d')) }}">
                        @error('fecha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2">{{ old('descripcion', $capacitacion->descripcion) }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Hora Inicio</label>
                        <input type="time" name="hora_inicio" class="form-control form-control-custom" value="{{ old('hora_inicio', $capacitacion->hora_inicio ? date('H:i', strtotime($capacitacion->hora_inicio)) : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Hora Fin</label>
                        <input type="time" name="hora_fin" class="form-control form-control-custom" value="{{ old('hora_fin', $capacitacion->hora_fin ? date('H:i', strtotime($capacitacion->hora_fin)) : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Duración (horas)</label>
                        <input type="number" name="duracion_horas" class="form-control form-control-custom" value="{{ old('duracion_horas', $capacitacion->duracion_horas) }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Modalidad</label>
                        <select name="modalidad" class="form-select form-select-custom">
                            <option value="presencial" {{ old('modalidad', $capacitacion->modalidad) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="virtual" {{ old('modalidad', $capacitacion->modalidad) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="hibrido" {{ old('modalidad', $capacitacion->modalidad) == 'hibrido' ? 'selected' : '' }}>Híbrido</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Lugar</label>
                        <input type="text" name="lugar" class="form-control form-control-custom" value="{{ old('lugar', $capacitacion->lugar) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Instructor</label>
                        <select name="instructor_id" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('instructor_id', $capacitacion->instructor_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Nombre Instructor (externo)</label>
                        <input type="text" name="instructor_nombre" class="form-control form-control-custom" value="{{ old('instructor_nombre', $capacitacion->instructor_nombre) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Temas</label>
                        <textarea name="temas" class="form-control form-control-custom" rows="2">{{ old('temas', $capacitacion->temas) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="programada" {{ old('estado', $capacitacion->estado) == 'programada' ? 'selected' : '' }}>Programada</option>
                            <option value="en_curso" {{ old('estado', $capacitacion->estado) == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="completada" {{ old('estado', $capacitacion->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelada" {{ old('estado', $capacitacion->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('sgc.capacitaciones.show', $capacitacion) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
