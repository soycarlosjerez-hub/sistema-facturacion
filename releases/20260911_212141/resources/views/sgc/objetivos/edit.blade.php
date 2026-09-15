@extends('layouts.app')

@section('title', 'Editar Objetivo de Calidad')

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
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar {{ $objetivo->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.objetivos.show', $objetivo) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando objetivo de calidad
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.objetivos.update', $objetivo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Código</label>
                        <input type="text" class="form-control form-control-custom" value="{{ $objetivo->codigo }}" readonly>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-custom">Título</label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo', $objetivo->titulo) }}">
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2">{{ old('descripcion', $objetivo->descripcion) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Indicador</label>
                        <input type="text" name="indicador" class="form-control form-control-custom" value="{{ old('indicador', $objetivo->indicador) }}">
                        @error('indicador')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Meta</label>
                        <input type="number" name="meta" class="form-control form-control-custom" value="{{ old('meta', $objetivo->meta) }}" step="0.01">
                        @error('meta')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Unidad</label>
                        <input type="text" name="unidad" class="form-control form-control-custom" value="{{ old('unidad', $objetivo->unidad) }}">
                        @error('unidad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Inicio</label>
                        <input type="date" name="periodo_inicio" class="form-control form-control-custom" value="{{ old('periodo_inicio', $objetivo->periodo_inicio?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Fin</label>
                        <input type="date" name="periodo_fin" class="form-control form-control-custom" value="{{ old('periodo_fin', $objetivo->periodo_fin?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id', $objetivo->responsable_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsable_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="en_curso" {{ old('estado', $objetivo->estado) == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="cumplido" {{ old('estado', $objetivo->estado) == 'cumplido' ? 'selected' : '' }}>Cumplido</option>
                            <option value="no_cumplido" {{ old('estado', $objetivo->estado) == 'no_cumplido' ? 'selected' : '' }}>No Cumplido</option>
                            <option value="atrasado" {{ old('estado', $objetivo->estado) == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Evidencias</label>
                        <textarea name="evidencias" class="form-control form-control-custom" rows="2">{{ old('evidencias', $objetivo->evidencias) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Acciones de Mejora</label>
                        <textarea name="acciones_mejora" class="form-control form-control-custom" rows="2">{{ old('acciones_mejora', $objetivo->acciones_mejora) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('sgc.objetivos.show', $objetivo) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
