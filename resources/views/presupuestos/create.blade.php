@extends('layouts.app')
@section('title', 'Nuevo Presupuesto')

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
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Presupuesto Técnico</h4>
                    <div class="ui-header-meta">Crea un nuevo presupuesto para servicios o productos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="presupuestoForm" action="{{ route('presupuestos.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label fw-bold">Cliente *</label>
                                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">-- Seleccionar cliente --</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} - {{ $cliente->rnc_cedula }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="valido_hasta" class="form-label fw-bold">Válido Hasta</label>
                                <input type="date" name="valido_hasta" id="valido_hasta" class="form-control @error('valido_hasta') is-invalid @enderror" value="{{ old('valido_hasta') }}">
                                <small class="text-muted">Fecha de validez de la oferta</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas</label>
                            <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror" rows="3" placeholder="Notas adicionales del presupuesto">{{ old('notas') }}</textarea>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" form="presupuestoForm" class="ui-btn ui-btn-ghost rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo presupuesto</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('presupuestos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="presupuestoForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Presupuesto
            </button>
        </div>
    </div>
</div>
@endsection
