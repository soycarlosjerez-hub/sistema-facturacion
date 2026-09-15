@extends('layouts.app')
@section('title', 'Editar Consignación')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-1"></i>EDITANDO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Editar Consignación</h2>
                    <p class="mb-0 opacity-75">Consignación #{{ $consignacion->id }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.consignaciones.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('arte.consignaciones.update', $consignacion) }}" id="consignacionForm">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="obra_id">Obra *</label>
                        <select name="obra_id" id="obra_id" class="ui-select @error('obra_id') is-invalid @enderror" required>
                            <option value="">Seleccionar obra...</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ old('obra_id', $consignacion->obra_id) == $obra->id ? 'selected' : '' }}>{{ $obra->titulo }} — RD$ {{ number_format($obra->precio_venta, 2) }}</option>
                            @endforeach
                        </select>
                        @error('obra_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="consignante">Consignante *</label>
                        <input type="text" name="consignante" id="consignante" class="ui-input @error('consignante') is-invalid @enderror" value="{{ old('consignante', $consignacion->consignante) }}" required>
                        @error('consignante') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="porcentaje_comision">Comisión (%)</label>
                        <input type="number" name="porcentaje_comision" id="porcentaje_comision" class="ui-input" step="0.01" min="0" max="100" value="{{ old('porcentaje_comision', $consignacion->porcentaje_comision) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="fecha_inicio">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="ui-input @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', optional($consignacion->fecha_inicio)->format('Y-m-d')) }}" required>
                        @error('fecha_inicio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="fecha_fin">Fecha fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="ui-input" value="{{ old('fecha_fin', optional($consignacion->fecha_fin)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="monto_entregado">Monto entregado (RD$)</label>
                        <input type="number" name="monto_entregado" id="monto_entregado" class="ui-input" step="0.01" min="0" value="{{ old('monto_entregado', $consignacion->monto_entregado) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="estado">Estado *</label>
                        <select name="estado" id="estado" class="ui-select" required>
                            @foreach(['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                                <option value="{{ $k }}" {{ old('estado', $consignacion->estado) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label" for="notas">Notas</label>
                        <textarea name="notas" id="notas" class="ui-textarea" rows="3">{{ old('notas', $consignacion->notas) }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent,#8b5cf6)"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando Consignación #{{ $consignacion->id }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('arte.consignaciones.index') }}" class="ui-btn ui-btn-ghost btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" form="consignacionForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Actualizar Consignación
            </button>
        </div>
    </div>
</div>
</div>
@endsection