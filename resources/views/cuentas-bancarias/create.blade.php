@extends('layouts.app')

@section('title', 'Nueva Cuenta Bancaria')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bank"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Cuenta Bancaria</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva cuenta bancaria para pagos por transferencia
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('cuentas-bancarias.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <form id="cuentaForm" action="{{ route('cuentas-bancarias.store') }}" method="POST">
        @csrf

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-info-circle"></i>
                Información de la Cuenta
            </div>
            <div class="ui-card-subtitle">Completa los datos de la nueva cuenta bancaria</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre de la cuenta <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Cuenta Corriente BHD">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Banco</label>
                        <input type="text" name="banco" class="ui-input @error('banco') is-invalid @enderror" value="{{ old('banco') }}" placeholder="Ej. Banco Popular, BHD, Scotiabank">
                        @error('banco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Tipo de Cuenta</label>
                        <select name="tipo_cuenta" class="ui-select @error('tipo_cuenta') is-invalid @enderror">
                            <option value="">Seleccionar</option>
                            <option value="ahorros" {{ old('tipo_cuenta') === 'ahorros' ? 'selected' : '' }}>Ahorros</option>
                            <option value="corriente" {{ old('tipo_cuenta') === 'corriente' ? 'selected' : '' }}>Corriente</option>
                        </select>
                        @error('tipo_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Número de Cuenta</label>
                        <input type="text" name="numero_cuenta" class="ui-input @error('numero_cuenta') is-invalid @enderror" value="{{ old('numero_cuenta') }}" placeholder="000-000000-0">
                        @error('numero_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Moneda</label>
                        <select name="moneda" class="ui-select @error('moneda') is-invalid @enderror">
                            <option value="RD" {{ old('moneda') === 'RD' ? 'selected' : '' }}>RD (Peso Dominicano)</option>
                            <option value="USD" {{ old('moneda') === 'USD' ? 'selected' : '' }}>USD (Dólar)</option>
                            <option value="EUR" {{ old('moneda') === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        </select>
                        @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Titular de la Cuenta</label>
                        <input type="text" name="titular" class="ui-input @error('titular') is-invalid @enderror" value="{{ old('titular') }}" placeholder="Nombre del titular">
                        @error('titular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Cédula / RUC del Titular</label>
                        <input type="text" name="cedula_ruc" class="ui-input @error('cedula_ruc') is-invalid @enderror" value="{{ old('cedula_ruc') }}" placeholder="000-0000000-0">
                        @error('cedula_ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Saldo Inicial</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="saldo_inicial" class="ui-input @error('saldo_inicial') is-invalid @enderror" value="{{ old('saldo_inicial', '0.00') }}">
                            @error('saldo_inicial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-4">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" checked>
                            <label class="form-check-label fw-bold small" for="chk-activo">Cuenta Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('cuentas-bancarias.index') }}" class="ui-btn ui-btn-ghost me-2">Cancelar</a>
        <button type="submit" form="cuentaForm" class="ui-btn ui-btn-solid">
            <i class="bi bi-check-lg me-2"></i>Guardar Cuenta
        </button>
    </div>
</div>
@endsection