@extends('layouts.app')
@section('title', 'Registrar Pago - ' . $instance->nombre)
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-success me-2"></i>Registrar Pago</h2>
            <p class="text-muted mb-0">{{ $instance->nombre }} &middot; Costo mensual: {{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</p>
        </div>
        <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.pagos.store', $instance) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mes a Pagar <span class="text-danger">*</span></label>
                            <select name="mes_pagado" class="form-select rounded-pill @error('mes_pagado') is-invalid @enderror" required>
                                <option value="">Seleccionar mes...</option>
                                @foreach($mesesDisponibles as $val => $label)
                                    <option value="{{ $val }}" {{ old('mes_pagado') === $val ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                @endforeach
                            </select>
                            @error('mes_pagado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-pill">{{ $systemMoneda ?? 'RD$' }}</span>
                                <input type="number" name="monto" class="form-control rounded-end-pill @error('monto') is-invalid @enderror" value="{{ old('monto', $instance->costo_mensual) }}" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">M&eacute;todo de Pago</label>
                            <select name="metodo_pago" class="form-select rounded-pill">
                                <option value="">Seleccionar...</option>
                                <option value="Transferencia" {{ old('metodo_pago') === 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="Efectivo" {{ old('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Cheque" {{ old('metodo_pago') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Tarjeta" {{ old('metodo_pago') === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="PayPal" {{ old('metodo_pago') === 'PayPal' ? 'selected' : '' }}>PayPal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Notas</label>
                            <textarea name="notas" class="form-control rounded-4 @error('notas') is-invalid @enderror" rows="2" placeholder="Notas opcionales...">{{ old('notas') }}</textarea>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-2"></i>Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
