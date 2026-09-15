@extends('layouts.app')

@section('title', 'Editar No Conformidad')

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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar {{ $noConformidad->numero_label }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.no-conformidades.show', $noConformidad) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando no conformidad
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.no-conformidades.update', $noConformidad) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Ocurrencia</label>
                        <input type="date" name="fecha_ocurrencia" class="form-control form-control-custom" value="{{ old('fecha_ocurrencia', $noConformidad->fecha_ocurrencia?->format('Y-m-d')) }}">
                        @error('fecha_ocurrencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Identificación</label>
                        <input type="date" name="fecha_identificacion" class="form-control form-control-custom" value="{{ old('fecha_identificacion', $noConformidad->fecha_identificacion?->format('Y-m-d')) }}">
                        @error('fecha_identificacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="{{ old('fecha_limite', $noConformidad->fecha_limite?->format('Y-m-d')) }}">
                        @error('fecha_limite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Origen</label>
                        <select name="origen" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\NoConformidad::getOrigenOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('origen', $noConformidad->origen) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('origen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Gravedad</label>
                        <select name="gravedad" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\NoConformidad::getGravedadOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('gravedad', $noConformidad->gravedad) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gravedad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            @foreach(\App\Models\NoConformidad::getBadgesForSelect() as $key => $opt)
                            <option value="{{ $key }}" {{ old('estado', $noConformidad->estado) == $key ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Asignado A</label>
                        <select name="asignado_a" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('asignado_a', $noConformidad->asignado_a) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('asignado_a')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Método Análisis</label>
                        <select name="analisis_causa_metodo" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            <option value="5_for_why" {{ old('analisis_causa_metodo', $noConformidad->analisis_causa_metodo) == '5_for_why' ? 'selected' : '' }}>5 Por Qué</option>
                            <option value="ishikawa" {{ old('analisis_causa_metodo', $noConformidad->analisis_causa_metodo) == 'ishikawa' ? 'selected' : '' }}>Ishikawa</option>
                            <option value="8d" {{ old('analisis_causa_metodo', $noConformidad->analisis_causa_metodo) == '8d' ? 'selected' : '' }}>8D</option>
                            <option value="otro" {{ old('analisis_causa_metodo', $noConformidad->analisis_causa_metodo) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('analisis_causa_metodo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3">{{ old('descripcion', $noConformidad->descripcion) }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Evidencia</label>
                        <textarea name="evidencia" class="form-control form-control-custom" rows="2">{{ old('evidencia', $noConformidad->evidencia) }}</textarea>
                        @error('evidencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Acción Contención</label>
                        <input type="text" name="accion_contencion" class="form-control form-control-custom" value="{{ old('accion_contencion', $noConformidad->accion_contencion) }}">
                        @error('accion_contencion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Detalle Análisis de Causa</label>
                        <textarea name="analisis_causa_detalle" class="form-control form-control-custom" rows="3">{{ old('analisis_causa_detalle', $noConformidad->analisis_causa_detalle) }}</textarea>
                        @error('analisis_causa_detalle')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('sgc.no-conformidades.show', $noConformidad) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
