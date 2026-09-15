@extends('layouts.app')
@section('title', 'Nueva Lista de Precios')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Lista de Precios</h4>
                    <div class="ui-header-meta">Define una nueva lista con precios especiales</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('listas-precio.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-info-circle me-2"></i>
            Información de la Lista
        </div>
        <div class="ui-card-subtitle">Completa los datos de la nueva lista de precios</div>
        <form id="listaPrecioForm" action="{{ route('listas-precio.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">C&oacute;digo <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="ui-input" value="{{ old('codigo') }}" required maxlength="20" placeholder="MAYORISTA">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input" value="{{ old('nombre') }}" required maxlength="255" placeholder="Precio Mayorista">
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Descripci&oacute;n</label>
                        <textarea name="descripcion" class="ui-textarea" rows="2" placeholder="Opcional: descripci&oacute;n de la lista">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Vigencia desde</label>
                        <input type="date" name="vigencia_desde" class="ui-input" value="{{ old('vigencia_desde') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Vigencia hasta</label>
                        <input type="date" name="vigencia_hasta" class="ui-input" value="{{ old('vigencia_hasta') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activa">Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="height: 80px;"></div>

    <div class="ui-sticky-bar">
        <div class="ui-sticky-bar-inner">
            <a href="{{ route('listas-precio.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="listaPrecioForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>Guardar Lista
            </button>
        </div>
    </div>
</div>
@endsection