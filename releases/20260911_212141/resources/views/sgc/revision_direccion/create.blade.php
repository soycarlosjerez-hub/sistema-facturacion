@extends('layouts.app')

@section('title', 'Nueva Revisión por Dirección')

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
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Revisión por Dirección</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.revision-direccion.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Crear acta de revisión por la dirección
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.revision-direccion.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control form-control-custom" value="{{ old('fecha') }}" required>
                        @error('fecha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select form-select-custom" required>
                            @foreach(\App\Models\RevisionDireccion::getTiposOpciones() as $key => $label)
                            <option value="{{ $key }}" {{ old('tipo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Duración (horas)</label>
                        <input type="number" name="duracion_horas" class="form-control form-control-custom" value="{{ old('duracion_horas') }}" step="0.5" min="0.5">
                        @error('duracion_horas')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            @foreach(\App\Models\RevisionDireccion::getBadgesForSelect() as $key => $opt)
                            <option value="{{ $key }}" {{ old('estado') == $key ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Resumen</label>
                        <textarea name="resumen" class="form-control form-control-custom" rows="4" placeholder="Resumen de la reunión de revisión por dirección...">{{ old('resumen') }}</textarea>
                        @error('resumen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Resumen de Resoluciones</label>
                        <textarea name="resumen_resoluciones" class="form-control form-control-custom" rows="3" placeholder="Decisiones y acuerdos tomados...">{{ old('resumen_resoluciones') }}</textarea>
                        @error('resumen_resoluciones')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.revision-direccion.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
