@extends('layouts.app')

@section('title', 'Editar Riesgo')

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
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar {{ $riesgo->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.riesgos.show', $riesgo) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando riesgo — {{ $riesgo->area }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.riesgos.update', $riesgo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Código</label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="{{ old('codigo', $riesgo->codigo) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Área</label>
                        <input type="text" name="area" class="form-control form-control-custom" value="{{ old('area', $riesgo->area) }}">
                        @error('area')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="{{ old('fecha_limite', $riesgo->fecha_limite?->format('Y-m-d')) }}">
                        @error('fecha_limite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3">{{ old('descripcion', $riesgo->descripcion) }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Causa</label>
                        <textarea name="causa" class="form-control form-control-custom" rows="2">{{ old('causa', $riesgo->causa) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Consecuencia</label>
                        <textarea name="consecuencia" class="form-control form-control-custom" rows="2">{{ old('consecuencia', $riesgo->consecuencia) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Probabilidad (1-5)</label>
                        <select name="probabilidad" class="form-select form-select-custom">
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('probabilidad', $riesgo->probabilidad) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('probabilidad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Impacto (1-5)</label>
                        <select name="impacto" class="form-select form-select-custom">
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('impacto', $riesgo->impacto) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('impacto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Clasificación</label>
                        <select name="clasificacion" class="form-select form-select-custom">
                            <option value="bajo" {{ old('clasificacion', $riesgo->clasificacion) == 'bajo' ? 'selected' : '' }}>Bajo</option>
                            <option value="medio" {{ old('clasificacion', $riesgo->clasificacion) == 'medio' ? 'selected' : '' }}>Medio</option>
                            <option value="alto" {{ old('clasificacion', $riesgo->clasificacion) == 'alto' ? 'selected' : '' }}>Alto</option>
                            <option value="critico" {{ old('clasificacion', $riesgo->clasificacion) == 'critico' ? 'selected' : '' }}>Crítico</option>
                        </select>
                        @error('clasificacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Controles Existentes</label>
                        <textarea name="controles_existentes" class="form-control form-control-custom" rows="2">{{ old('controles_existentes', $riesgo->controles_existentes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Acción</label>
                        <textarea name="plan_accion" class="form-control form-control-custom" rows="2">{{ old('plan_accion', $riesgo->plan_accion) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Mitigación</label>
                        <textarea name="plan_mitigacion" class="form-control form-control-custom" rows="2">{{ old('plan_mitigacion', $riesgo->plan_mitigacion) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin responsable</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id', $riesgo->responsable_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsable_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="identificado" {{ old('estado', $riesgo->estado) == 'identificado' ? 'selected' : '' }}>Identificado</option>
                            <option value="en_tratamiento" {{ old('estado', $riesgo->estado) == 'en_tratamiento' ? 'selected' : '' }}>En Tratamiento</option>
                            <option value="cerrado" {{ old('estado', $riesgo->estado) == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-custom" rows="2">{{ old('observaciones', $riesgo->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('sgc.riesgos.show', $riesgo) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
