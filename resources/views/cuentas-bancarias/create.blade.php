@extends('layouts.app')

@section('title', 'Nueva Cuenta Bancaria')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#059669,#10b981,#34d399,#059669);box-shadow:0 8px 32px rgba(5,150,105,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-bank"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Cuenta Bancaria</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva cuenta bancaria para pagos por transferencia
                    </small>
                </div>
            </div>
            <a href="{{ route('cuentas-bancarias.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form id="cuentaForm" action="{{ route('cuentas-bancarias.store') }}" method="POST">
        @csrf

        <div class="premium-card" style="animation-delay:.1s;">
            <div class="card-accent green"></div>
            <div class="premium-card-title">
                <i class="bi bi-info-circle icon-blue"></i>
                Información de la Cuenta
            </div>
            <div class="premium-card-subtitle">Completa los datos de la nueva cuenta bancaria</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre de la cuenta <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Cuenta Corriente BHD">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banco</label>
                        <input type="text" name="banco" class="form-control @error('banco') is-invalid @enderror" value="{{ old('banco') }}" placeholder="Ej. Banco Popular, BHD, Scotiabank">
                        @error('banco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Cuenta</label>
                        <select name="tipo_cuenta" class="form-select @error('tipo_cuenta') is-invalid @enderror">
                            <option value="">Seleccionar</option>
                            <option value="ahorros" {{ old('tipo_cuenta') === 'ahorros' ? 'selected' : '' }}>Ahorros</option>
                            <option value="corriente" {{ old('tipo_cuenta') === 'corriente' ? 'selected' : '' }}>Corriente</option>
                        </select>
                        @error('tipo_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número de Cuenta</label>
                        <input type="text" name="numero_cuenta" class="form-control @error('numero_cuenta') is-invalid @enderror" value="{{ old('numero_cuenta') }}" placeholder="000-000000-0">
                        @error('numero_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Moneda</label>
                        <select name="moneda" class="form-select @error('moneda') is-invalid @enderror">
                            <option value="RD" {{ old('moneda') === 'RD' ? 'selected' : '' }}>RD (Peso Dominicano)</option>
                            <option value="USD" {{ old('moneda') === 'USD' ? 'selected' : '' }}>USD (Dólar)</option>
                            <option value="EUR" {{ old('moneda') === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        </select>
                        @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Titular de la Cuenta</label>
                        <input type="text" name="titular" class="form-control @error('titular') is-invalid @enderror" value="{{ old('titular') }}" placeholder="Nombre del titular">
                        @error('titular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cédula / RUC del Titular</label>
                        <input type="text" name="cedula_ruc" class="form-control @error('cedula_ruc') is-invalid @enderror" value="{{ old('cedula_ruc') }}" placeholder="000-0000000-0">
                        @error('cedula_ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Saldo Inicial</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="saldo_inicial" class="form-control @error('saldo_inicial') is-invalid @enderror" value="{{ old('saldo_inicial', '0.00') }}">
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

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('cuentas-bancarias.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="cuentaForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Cuenta
        </button>
    </div>
</div>
@endsection
