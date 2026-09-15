@extends('layouts.app')

@section('title', 'Nuevo Objetivo de Calidad')

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
                    <h4 class="ui-header-title">Nuevo Objetivo de Calidad</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.objetivos.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Definir un nuevo objetivo medible del SGC
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.objetivos.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="{{ old('codigo') }}" placeholder="AUTO si se deja vacío">
                        @error('codigo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo') }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Indicador <span class="text-danger">*</span></label>
                        <input type="text" name="indicador" class="form-control form-control-custom" value="{{ old('indicador') }}" required placeholder="Ej: % satisfacción clientes">
                        @error('indicador')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Meta <span class="text-danger">*</span></label>
                        <input type="number" name="meta" class="form-control form-control-custom" value="{{ old('meta') }}" step="0.01" required>
                        @error('meta')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Unidad</label>
                        <input type="text" name="unidad" class="form-control form-control-custom" value="{{ old('unidad') }}" placeholder="%, unidades, días...">
                        @error('unidad')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Inicio</label>
                        <input type="date" name="periodo_inicio" class="form-control form-control-custom" value="{{ old('periodo_inicio') }}">
                        @error('periodo_inicio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Fin</label>
                        <input type="date" name="periodo_fin" class="form-control form-control-custom" value="{{ old('periodo_fin') }}">
                        @error('periodo_fin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios ?? [] as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsable_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="en_curso" {{ old('estado')=='en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="cumplido" {{ old('estado')=='cumplido' ? 'selected' : '' }}>Cumplido</option>
                            <option value="no_cumplido" {{ old('estado')=='no_cumplido' ? 'selected' : '' }}>No Cumplido</option>
                            <option value="atrasado" {{ old('estado')=='atrasado' ? 'selected' : '' }}>Atrasado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.objetivos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
