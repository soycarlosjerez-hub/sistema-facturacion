@extends('layouts.app')
@section('title', 'Editar Consignación')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Consignación</h2>
                    <p class="text-white text-opacity-75 mb-0">Consignación #{{ $consignacion->id }}</p>
                </div>
            </div>
            <a href="{{ route('arte.consignaciones.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <form method="POST" action="{{ route('arte.consignaciones.update', $consignacion) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Obra *</label>
                    <select name="obra_id" class="form-select rounded-3 @error('obra_id') is-invalid @enderror" required>
                        <option value="">Seleccionar obra...</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ old('obra_id', $consignacion->obra_id) == $obra->id ? 'selected' : '' }}>{{ $obra->titulo }} — RD$ {{ number_format($obra->precio_venta, 2) }}</option>
                        @endforeach
                    </select>
                    @error('obra_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Consignante *</label>
                    <input type="text" name="consignante" class="form-control rounded-3 @error('consignante') is-invalid @enderror" value="{{ old('consignante', $consignacion->consignante) }}" required>
                    @error('consignante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Comisión (%)</label>
                    <input type="number" name="porcentaje_comision" class="form-control rounded-3" step="0.01" min="0" max="100" value="{{ old('porcentaje_comision', $consignacion->porcentaje_comision) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" class="form-control rounded-3 @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', optional($consignacion->fecha_inicio)->format('Y-m-d')) }}" required>
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control rounded-3" value="{{ old('fecha_fin', optional($consignacion->fecha_fin)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Monto entregado (RD$)</label>
                    <input type="number" name="monto_entregado" class="form-control rounded-3" step="0.01" min="0" value="{{ old('monto_entregado', $consignacion->monto_entregado) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Estado *</label>
                    <select name="estado" class="form-select rounded-3" required>
                        @foreach(['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                            <option value="{{ $k }}" {{ old('estado', $consignacion->estado) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="3">{{ old('notas', $consignacion->notas) }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('arte.consignaciones.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Actualizar Consignación</button>
            </div>
        </form>
    </div>
</div>
@endsection
