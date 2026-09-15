@extends('layouts.app')

@section('title', 'Nueva No Conformidad')

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
                    <h4 class="ui-header-title">Nueva No Conformidad</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.no-conformidades.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar una nueva no conformidad
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.no-conformidades.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- Datos Generales --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-info-circle me-1"></i> Datos Generales
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Ocurrencia <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_ocurrencia" class="form-control form-control-custom" value="{{ old('fecha_ocurrencia') }}" required>
                        @error('fecha_ocurrencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Identificación</label>
                        <input type="date" name="fecha_identificacion" class="form-control form-control-custom" value="{{ old('fecha_identificacion') }}">
                        @error('fecha_identificacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Origen <span class="text-danger">*</span></label>
                        <select name="origen" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\NoConformidad::getOrigenOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('origen') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('origen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Gravedad <span class="text-danger">*</span></label>
                        <select name="gravedad" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\NoConformidad::getGravedadOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('gravedad') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gravedad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Auditoría Asociada</label>
                        <select name="auditoria_id" class="form-select form-select-custom">
                            <option value="">Sin asociar</option>
                            @foreach($auditorias ?? [] as $aud)
                            <option value="{{ $aud->id }}" {{ old('auditoria_id') == $aud->id ? 'selected' : '' }}>{{ $aud->codigo }} - {{ $aud->area_auditar }}</option>
                            @endforeach
                        </select>
                        @error('auditoria_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="{{ old('fecha_limite') }}">
                        @error('fecha_limite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Evidencia</label>
                        <textarea name="evidencia" class="form-control form-control-custom" rows="2">{{ old('evidencia') }}</textarea>
                        @error('evidencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Análisis de Causa --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-search me-1"></i> Análisis de Causa
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Método de Análisis</label>
                        <select name="analisis_causa_metodo" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            <option value="5_for_why" {{ old('analisis_causa_metodo') == '5_for_why' ? 'selected' : '' }}>5 Por Qué</option>
                            <option value="ishikawa" {{ old('analisis_causa_metodo') == 'ishikawa' ? 'selected' : '' }}>Ishikawa (Espina de Pescado)</option>
                            <option value="8d" {{ old('analisis_causa_metodo') == '8d' ? 'selected' : '' }}>8D</option>
                            <option value="otro" {{ old('analisis_causa_metodo') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('analisis_causa_metodo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Acción de Contención</label>
                        <input type="text" name="accion_contencion" class="form-control form-control-custom" value="{{ old('accion_contencion') }}" placeholder="Acción inmediata tomada">
                        @error('accion_contencion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Detalle del Análisis de Causa</label>
                        <textarea name="analisis_causa_detalle" class="form-control form-control-custom" rows="3" placeholder="Describe el análisis de causa realizado...">{{ old('analisis_causa_detalle') }}</textarea>
                        @error('analisis_causa_detalle')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Asignación --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-person me-1"></i> Asignación
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Asignado A</label>
                        <select name="asignado_a" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('asignado_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('asignado_a')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.no-conformidades.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
