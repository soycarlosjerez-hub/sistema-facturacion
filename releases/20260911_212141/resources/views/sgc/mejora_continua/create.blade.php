@extends('layouts.app')

@section('title', 'Nueva Mejora Continua')

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
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Mejora Continua</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.mejora.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar una nueva mejora continua
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.mejora.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-info-circle me-1"></i> Información General
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Número</label>
                        <input type="text" name="numero" class="form-control form-control-custom" value="{{ old('numero') }}" placeholder="AUTO si se deja vacío">
                        @error('numero')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo') }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Origen <span class="text-danger">*</span></label>
                        <select name="origen" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\MejoraContinua::getOrigenOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('origen') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('origen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Prioridad <span class="text-danger">*</span></label>
                        <select name="prioridad" class="form-select form-select-custom" required>
                            @foreach(\App\Models\MejoraContinua::getPrioridadOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('prioridad') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('prioridad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Impacto</label>
                        <select name="impacto" class="form-select form-select-custom">
                            @foreach(\App\Models\MejoraContinua::getImpactoOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('impacto') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('impacto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsable_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="{{ old('fecha_limite') }}">
                        @error('fecha_limite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-currency-dollar me-1"></i> Beneficios Estimados
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Beneficios Esperados</label>
                        <textarea name="beneficios_esperados" class="form-control form-control-custom" rows="2">{{ old('beneficios_esperados') }}</textarea>
                        @error('beneficios_esperados')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Ahorro Estimado (RD$)</label>
                        <input type="number" name="ahorro_estimado" class="form-control form-control-custom" value="{{ old('ahorro_estimado') }}" step="0.01">
                        @error('ahorro_estimado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Costo Estimado (RD$)</label>
                        <input type="number" name="costo_estimado" class="form-control form-control-custom" value="{{ old('costo_estimado') }}" step="0.01">
                        @error('costo_estimado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.mejora.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
