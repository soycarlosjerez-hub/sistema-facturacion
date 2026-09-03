@extends('layouts.app')

@section('title', 'Nuevo Riesgo')

@push('styles')
@include('partials.premium-ui')
<style>
    .form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
    }
    .risk-matrix { display: grid; grid-template-columns: repeat(5,1fr); gap: 4px; max-width: 300px; }
    .risk-cell { padding: .5rem; text-align: center; border-radius: .5rem; font-size: .75rem; font-weight: 600; }
    .risk-low { background: #dcfce7; color: #16a34a; }
    .risk-med { background: #fef3c7; color: #d97706; }
    .risk-high { background: #fed7aa; color: #ea580c; }
    .risk-crit { background: #fee2e2; color: #dc2626; }
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
                    <h4 class="ui-header-title">Nuevo Riesgo</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.riesgos.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar un nuevo riesgo en el SGC
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.riesgos.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-info-circle me-1"></i> Identificación del Riesgo
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="{{ old('codigo') }}" placeholder="AUTO si se deja vacío">
                        @error('codigo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Área <span class="text-danger">*</span></label>
                        <input type="text" name="area" class="form-control form-control-custom" value="{{ old('area') }}" required>
                        @error('area')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="{{ old('fecha_limite') }}">
                        @error('fecha_limite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Descripción del Riesgo <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Causa</label>
                        <textarea name="causa" class="form-control form-control-custom" rows="2">{{ old('causa') }}</textarea>
                        @error('causa')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Consecuencia</label>
                        <textarea name="consecuencia" class="form-control form-control-custom" rows="2">{{ old('consecuencia') }}</textarea>
                        @error('consecuencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Evaluación del Riesgo --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-calculator me-1"></i> Evaluación del Riesgo
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Probabilidad (1-5) <span class="text-danger">*</span></label>
                        <select name="probabilidad" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('probabilidad') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('probabilidad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Impacto (1-5) <span class="text-danger">*</span></label>
                        <select name="impacto" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('impacto') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('impacto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Clasificación</label>
                        <select name="clasificacion" class="form-select form-select-custom">
                            <option value="bajo" {{ old('clasificacion')=='bajo' ? 'selected' : '' }}>Bajo</option>
                            <option value="medio" {{ old('clasificacion', 'medio')=='medio' ? 'selected' : '' }}>Medio</option>
                            <option value="alto" {{ old('clasificacion')=='alto' ? 'selected' : '' }}>Alto</option>
                            <option value="critico" {{ old('clasificacion')=='critico' ? 'selected' : '' }}>Crítico</option>
                        </select>
                        @error('clasificacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tratamiento --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-gear me-1"></i> Tratamiento
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Controles Existentes</label>
                        <textarea name="controles_existentes" class="form-control form-control-custom" rows="2">{{ old('controles_existentes') }}</textarea>
                        @error('controles_existentes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Acción</label>
                        <textarea name="plan_accion" class="form-control form-control-custom" rows="2">{{ old('plan_accion') }}</textarea>
                        @error('plan_accion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin responsable</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsable_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="identificado" {{ old('estado', 'identificado')=='identificado' ? 'selected' : '' }}>Identificado</option>
                            <option value="en_tratamiento" {{ old('estado')=='en_tratamiento' ? 'selected' : '' }}>En Tratamiento</option>
                            <option value="cerrado" {{ old('estado')=='cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.riesgos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
