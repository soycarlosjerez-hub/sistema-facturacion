@extends('layouts.app')
@section('title', 'Editar Presupuesto')

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
                    <h4 class="ui-header-title">Editar Presupuesto: {{ $presupuesto->numero }}</h4>
                    <div class="ui-header-meta">Actualiza los datos del presupuesto técnico</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="fw-bold mb-0">Datos del Presupuesto</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presupuestos.update', $presupuesto) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label fw-bold">Cliente *</label>
                                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">-- Seleccionar cliente --</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id', $presupuesto->cliente_id) == $cliente->id ? 'selected' : '' }}>
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
                                <input type="date" name="valido_hasta" id="valido_hasta" class="form-control @error('valido_hasta') is-invalid @enderror" value="{{ old('valido_hasta', $presupuesto->valido_hasta?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas</label>
                            <textarea name="notas" id="notas" class="form-control @error('notas') is-invalid @enderror" rows="3">{{ old('notas', $presupuesto->notas) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Presupuesto
                            </button>
                            <a href="{{ route('presupuestos.show', $presupuesto) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="fw-bold mb-0">Ítems del Presupuesto</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Los ítems se gestionan desde la vista de detalle del presupuesto.
                    </div>
                    
                    <a href="{{ route('presupuestos.show', $presupuesto) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Gestionar Ítems
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
