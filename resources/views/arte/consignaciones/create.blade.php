@extends('layouts.app')
@section('title', 'Nueva Consignación')
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
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Nueva Consignación</h2>
                    <p class="text-white text-opacity-75 mb-0">Registra una obra en consignación</p>
                </div>
            </div>
            <a href="{{ route('arte.consignaciones.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <form method="POST" action="{{ route('arte.consignaciones.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Obra *</label>
                    <select name="obra_id" class="form-select rounded-3 @error('obra_id') is-invalid @enderror" required>
                        <option value="">Seleccionar obra...</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ old('obra_id') == $obra->id ? 'selected' : '' }}>{{ $obra->titulo }} — RD$ {{ number_format($obra->precio_venta, 2) }}</option>
                        @endforeach
                    </select>
                    @error('obra_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Consignante *</label>
                    <input type="text" name="consignante" class="form-control rounded-3 @error('consignante') is-invalid @enderror" value="{{ old('consignante') }}" placeholder="Nombre del consignante" required>
                    @error('consignante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Comisión (%)</label>
                    <input type="number" name="porcentaje_comision" class="form-control rounded-3" step="0.01" min="0" max="100" value="{{ old('porcentaje_comision', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" class="form-control rounded-3 @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control rounded-3" value="{{ old('fecha_fin') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Monto entregado (RD$)</label>
                    <input type="number" name="monto_entregado" class="form-control rounded-3" step="0.01" min="0" value="{{ old('monto_entregado', 0) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Estado *</label>
                    <select name="estado" class="form-select rounded-3" required>
                        @foreach(['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                            <option value="{{ $k }}" {{ old('estado', 'activa') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="3">{{ old('notas') }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2 justify-content-end">
                <a href="{{ route('arte.consignaciones.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar Consignación</button>
            </div>
        </form>
    </div>
</div>
@endsection
