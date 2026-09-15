@extends('layouts.app')

@section('title', 'Nueva Encuesta de Satisfacción')

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
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Encuesta de Satisfacción</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.satisfaccion.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Crear encuesta para medir satisfacción del cliente
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.satisfaccion.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo') }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="borrador" {{ old('estado')=='borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="activa" {{ old('estado')=='activa' ? 'selected' : '' }}>Activa</option>
                            <option value="cerrada" {{ old('estado')=='cerrada' ? 'selected' : '' }}>Cerrada</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control form-control-custom" value="{{ old('fecha_inicio') }}">
                        @error('fecha_inicio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control form-control-custom" value="{{ old('fecha_fin') }}">
                        @error('fecha_fin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Instrucciones</label>
                        <textarea name="instrucciones" class="form-control form-control-custom" rows="3" placeholder="Instrucciones para los encuestados...">{{ old('instrucciones') }}</textarea>
                        @error('instrucciones')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.satisfaccion.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
