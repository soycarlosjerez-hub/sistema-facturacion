@extends('layouts.app')

@section('title', 'Nuevo Reclamo')

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
                    <h4 class="ui-header-title">Nuevo Reclamo</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.satisfaccion.reclamos') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar un reclamo, queja o sugerencia del cliente
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.satisfaccion.reclamos.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select form-select-custom" required>
                            <option value="reclamo" {{ old('tipo')=='reclamo' ? 'selected' : '' }}>Reclamo</option>
                            <option value="queja" {{ old('tipo')=='queja' ? 'selected' : '' }}>Queja</option>
                            <option value="sugerencia" {{ old('tipo')=='sugerencia' ? 'selected' : '' }}>Sugerencia</option>
                            <option value="cumpliment" {{ old('tipo')=='cumpliment' ? 'selected' : '' }}>Cumplido</option>
                        </select>
                        @error('tipo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Canal <span class="text-danger">*</span></label>
                        <select name="canal" class="form-select form-select-custom" required>
                            <option value="web" {{ old('canal')=='web' ? 'selected' : '' }}>Sitio Web</option>
                            <option value="telefono" {{ old('canal')=='telefono' ? 'selected' : '' }}>Teléfono</option>
                            <option value="presencial" {{ old('canal')=='presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="email" {{ old('canal')=='email' ? 'selected' : '' }}>Email</option>
                            <option value="redes_sociales" {{ old('canal')=='redes_sociales' ? 'selected' : '' }}>Redes Sociales</option>
                        </select>
                        @error('canal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Cliente</label>
                        <select name="cliente_id" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach($clientes ?? [] as $cli)
                            <option value="{{ $cli->id }}" {{ old('cliente_id') == $cli->id ? 'selected' : '' }}>{{ $cli->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="abierto" {{ old('estado', 'abierto')=='abierto' ? 'selected' : '' }}>Abierto</option>
                            <option value="en_tramite" {{ old('estado')=='en_tramite' ? 'selected' : '' }}>En Trámite</option>
                            <option value="resuelto" {{ old('estado')=='resuelto' ? 'selected' : '' }}>Resuelto</option>
                            <option value="cerrado" {{ old('estado')=='cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="4" required placeholder="Describe detalladamente el reclamo, queja o sugerencia...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.satisfaccion.reclamos') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
