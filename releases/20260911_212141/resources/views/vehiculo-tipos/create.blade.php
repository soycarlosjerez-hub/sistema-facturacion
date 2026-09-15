@extends('layouts.app')
@section('title', 'Nuevo Tipo de Vehículo')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Tipo de Vehículo</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Registra una nueva categoría de vehículo</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('vehiculo-tipos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('vehiculo-tipos.store') }}" method="POST" id="instanceForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-truck"></i> Datos del Tipo de Vehículo</div>
                        <div class="ui-card-subtitle">Complete los campos para registrar el tipo</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: Camión, Moto, Carro">
                                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Código</label>
                                    <input type="text" name="codigo" class="ui-input @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" placeholder="Ej: CAM, MOT, CAR">
                                    @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="ui-label">Descripción</label>
                                    <textarea name="descripcion" class="ui-input @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripción del tipo de vehículo">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Capacidad Máxima (kg)</label>
                                    <input type="number" name="capacidad_maxima" class="ui-input @error('capacidad_maxima') is-invalid @enderror" value="{{ old('capacidad_maxima') }}" min="0" step="0.01" placeholder="Ej: 5000">
                                    @error('capacidad_maxima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Estado</label>
                                    <select name="activo" class="ui-select @error('activo') is-invalid @enderror">
                                        <option value="1" {{ old('activo', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('activo') == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('activo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('vehiculo-tipos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Tipo
        </button>
    </div>
</div>
@endsection
