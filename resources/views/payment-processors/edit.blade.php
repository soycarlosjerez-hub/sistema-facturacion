@extends('layouts.app')
@section('title', 'Editar Procesador de Pago')

@push('styles')
<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #8b5cf6;
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .sticky-save-bar .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #a78bfa;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-credit-card text-warning me-2"></i>Editar Procesador</h2>
            <p class="text-muted mb-0">{{ $paymentProcessor->nombre }}</p>
        </div>
        <a href="{{ route('payment-processors.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('payment-processors.update', $paymentProcessor) }}" method="POST" id="instanceForm">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $paymentProcessor->nombre) }}" required>
                        @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="tarjeta" {{ old('tipo', $paymentProcessor->tipo) === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ old('tipo', $paymentProcessor->tipo) === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="otro" {{ old('tipo', $paymentProcessor->tipo) === 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Comisión (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="comision_porcentaje" class="form-control" value="{{ old('comision_porcentaje', $paymentProcessor->comision_porcentaje) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Comisión Fija</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" step="0.01" name="comision_fija" class="form-control" value="{{ old('comision_fija', $paymentProcessor->comision_fija) }}" min="0">
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-key me-2"></i>Conexión API</h5>
                <p class="text-muted small mb-3">Credenciales para conectarse al procesador de pagos. El api_secret se almacena encriptado.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">API Key / Client ID</label>
                        <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key', $paymentProcessor->api_key) }}" placeholder="Ej. pk_live_xxxxx">
                        @error('api_key')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">API Secret</label>
                        <div class="input-group">
                            <input type="password" name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ old('api_secret', $paymentProcessor->api_secret ? '********' : '') }}" placeholder="••••••••">
                            <button class="input-group-text btn btn-outline-secondary" type="button" onclick="toggleSecret(this)"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="small text-muted mt-1">Dejar en blanco para mantener el valor actual</div>
                        @error('api_secret')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">API Endpoint</label>
                        <input type="url" name="api_endpoint" class="form-control @error('api_endpoint') is-invalid @enderror" value="{{ old('api_endpoint', $paymentProcessor->api_endpoint) }}" placeholder="https://api.procesador.com/v1">
                        @error('api_endpoint')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Entorno</label>
                        <select name="api_environment" class="form-select @error('api_environment') is-invalid @enderror">
                            <option value="sandbox" {{ old('api_environment', $paymentProcessor->api_environment) === 'sandbox' ? 'selected' : '' }}>Sandbox (Pruebas)</option>
                            <option value="production" {{ old('api_environment', $paymentProcessor->api_environment) === 'production' ? 'selected' : '' }}>Producción</option>
                        </select>
                        @error('api_environment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Configuración adicional (JSON)</label>
                        <textarea name="config_json" class="form-control @error('config_json') is-invalid @enderror" rows="3" placeholder='{"merchant_id": "123", "terminal": "01"}' style="font-family: monospace;">{{ old('config_json', is_string($paymentProcessor->config_json) ? $paymentProcessor->config_json : json_encode($paymentProcessor->config_json, JSON_PRETTY_PRINT)) }}</textarea>
                        @error('config_json')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $paymentProcessor->activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="sticky-save-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando procesador: {{ $paymentProcessor->nombre }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('payment-processors.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSecret(btn) {
    const input = btn.closest('.input-group').querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush