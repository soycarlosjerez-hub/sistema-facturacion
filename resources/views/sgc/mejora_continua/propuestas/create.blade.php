@extends('layouts.app')

@section('title', 'Nueva Propuesta de Mejora')

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
                    <i class="bi bi-lightbulb"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Propuesta de Mejora</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.mejora.propuestas') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Envía tu propuesta de mejora al SGC
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.mejora.propuestas.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo') }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha</label>
                        <input type="date" name="fecha" class="form-control form-control-custom" value="{{ old('fecha', date('Y-m-d')) }}">
                        @error('fecha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="4" required placeholder="Describe detalladamente tu propuesta de mejora...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Mejora Asociada</label>
                        <select name="mejora_continua_id" class="form-select form-select-custom">
                            <option value="">Sin asociar a una mejora</option>
                            @foreach($mejoras ?? [] as $m)
                            <option value="{{ $m->id }}" {{ old('mejora_continua_id') == $m->id ? 'selected' : '' }}>{{ $m->numero_label }} - {{ $m->titulo_truncado }}</option>
                            @endforeach
                        </select>
                        @error('mejora_continua_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-send me-1"></i> Enviar Propuesta
                    </button>
                    <a href="{{ route('sgc.mejora.propuestas') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
