@extends('layouts.app')
@section('title', 'Nueva Evaluación Periódica')

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
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Evaluación Periódica</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Registra una evaluación periódica a un proveedor</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.evaluaciones-proveedores.periodico.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
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

    <form action="{{ route('sgc.evaluaciones-proveedores.periodico.store') }}" method="POST" id="instanceForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-clipboard-check"></i> Datos de la Evaluación Periódica</div>
                        <div class="ui-card-subtitle">Complete los campos para registrar la evaluación</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Proveedor <span class="text-danger">*</span></label>
                                    <select name="proveedor_id" class="ui-select @error('proveedor_id') is-invalid @enderror" required>
                                        <option value="">Seleccionar proveedor...</option>
                                        @foreach($proveedores as $prov)
                                            <option value="{{ $prov->id }}" {{ old('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Período <span class="text-danger">*</span></label>
                                    <input type="text" name="periodo" class="ui-input @error('periodo') is-invalid @enderror" value="{{ old('periodo') }}" placeholder="Ej: Q1-2026, Enero 2026" required>
                                    @error('periodo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Fecha Inicio <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_inicio" class="ui-input @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                                    @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Fecha Fin <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_fin" class="ui-input @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin') }}" required>
                                    @error('fecha_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Puntuación (0-100)</label>
                                    <input type="number" name="puntuacion" class="ui-input @error('puntuacion') is-invalid @enderror" value="{{ old('puntuacion') }}" min="0" max="100" step="0.01" placeholder="Ej: 85">
                                    @error('puntuacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="ui-label">Estado</label>
                                    <select name="estado" class="ui-select @error('estado') is-invalid @enderror">
                                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_curso" {{ old('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                        <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                                    </select>
                                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="ui-label">Observaciones</label>
                                    <textarea name="observaciones" class="ui-input @error('observaciones') is-invalid @enderror" rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        <a href="{{ route('sgc.evaluaciones-proveedores.periodico.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Evaluación
        </button>
    </div>
</div>
@endsection
