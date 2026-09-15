@extends('layouts.app')

@section('title', 'Editar Cuenta Bancaria')

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
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Cuenta Bancaria</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-bank me-1"></i>
                        {{ $cuentasBancarium->nombre }}
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

    <form id="cuentaForm" action="{{ route('cuentas-bancarias.update', $cuentasBancarium) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title">
                <i class="bi bi-info-circle"></i>
                Datos de la Cuenta
            </div>
            <div class="ui-card-subtitle">Actualiza la información de la cuenta bancaria</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre de la cuenta <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre', $cuentasBancarium->nombre) }}" required placeholder="Ej. Cuenta Corriente BHD">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Banco</label>
                        <input type="text" name="banco" class="ui-input @error('banco') is-invalid @enderror" value="{{ old('banco', $cuentasBancarium->banco) }}" placeholder="Ej. Banco Popular, BHD, Scotiabank">
                        @error('banco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Tipo de Cuenta</label>
                        <select name="tipo_cuenta" class="ui-select @error('tipo_cuenta') is-invalid @enderror">
                            <option value="">Seleccionar</option>
                            <option value="ahorros" {{ old('tipo_cuenta', $cuentasBancarium->tipo_cuenta) === 'ahorros' ? 'selected' : '' }}>Ahorros</option>
                            <option value="corriente" {{ old('tipo_cuenta', $cuentasBancarium->tipo_cuenta) === 'corriente' ? 'selected' : '' }}>Corriente</option>
                        </select>
                        @error('tipo_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Número de Cuenta</label>
                        <input type="text" name="numero_cuenta" class="ui-input @error('numero_cuenta') is-invalid @enderror" value="{{ old('numero_cuenta', $cuentasBancarium->numero_cuenta) }}" placeholder="000-000000-0">
                        @error('numero_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Moneda</label>
                        <select name="moneda" class="ui-select @error('moneda') is-invalid @enderror">
                            <option value="RD" {{ old('moneda', $cuentasBancarium->moneda) === 'RD' ? 'selected' : '' }}>RD (Peso Dominicano)</option>
                            <option value="USD" {{ old('moneda', $cuentasBancarium->moneda) === 'USD' ? 'selected' : '' }}>USD (Dólar)</option>
                            <option value="EUR" {{ old('moneda', $cuentasBancarium->moneda) === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        </select>
                        @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Titular de la Cuenta</label>
                        <input type="text" name="titular" class="ui-input @error('titular') is-invalid @enderror" value="{{ old('titular', $cuentasBancarium->titular) }}" placeholder="Nombre del titular">
                        @error('titular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Cédula / RUC del Titular</label>
                        <input type="text" name="cedula_ruc" class="ui-input @error('cedula_ruc') is-invalid @enderror" value="{{ old('cedula_ruc', $cuentasBancarium->cedula_ruc) }}" placeholder="000-0000000-0">
                        @error('cedula_ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Saldo Inicial</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="saldo_inicial" class="ui-input @error('saldo_inicial') is-invalid @enderror" value="{{ old('saldo_inicial', $cuentasBancarium->saldo_inicial) }}">
                            @error('saldo_inicial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-4">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" {{ $cuentasBancarium->activo ? 'checked' : '' }}>
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
            <i class="bi bi-check-lg me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection