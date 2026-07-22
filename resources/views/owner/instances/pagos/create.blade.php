@extends('layouts.app')
@section('title', 'Registrar Pago - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Registrar Pago</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; Costo mensual: {{ $systemMoneda ?? 'RD$' }} {{ number_format($instance->costo_mensual ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#10b981"></div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('owner.instances.pagos.store', $instance) }}" id="instanceForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">Mes a Pagar <span class="text-danger">*</span></label>
                    <select name="mes_pagado" class="ui-select rounded-pill @error('mes_pagado') is-invalid @enderror" required>
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
                        <input type="number" name="monto" class="ui-input rounded-end-pill @error('monto') is-invalid @enderror" value="{{ old('monto', $instance->costo_mensual) }}" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">M&eacute;todo de Pago</label>
                    <select name="metodo_pago" class="ui-select rounded-pill">
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
                    <textarea name="notas" class="ui-input rounded-4 @error('notas') is-invalid @enderror" rows="2" placeholder="Notas opcionales...">{{ old('notas') }}</textarea>
                </div>
            </form>
        </div>
    </div>

</div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#10b981;"></i>
            <span class="fw-semibold d-none d-sm-inline">Registrando Pago</span>
        </div>
        <div>
            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-outline me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid">
                <i class="bi bi-check-lg me-2"></i>Guardar
            </button>
        </div>
    </div>
</div>
@endsection
