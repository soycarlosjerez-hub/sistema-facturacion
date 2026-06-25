@extends('layouts.app')

@section('title', 'Nuevo Procesador de Pago')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
    background-size: 300% 300%;
    animation: premiumGradientShift 6s ease infinite;
}
.premium-header::before {
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-plus-lg me-1"></i>NUEVO PROCESADOR
                    </span>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Procesador de Pago</h4>
                    <small class="text-white opacity-75">Registra un procesador de pagos con credenciales API</small>
                </div>
            </div>
            <a href="{{ route('payment-processors.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="premium-card overflow-hidden mb-5" style="animation-delay:.1s;">
        <div class="card-accent amber"></div>
        <form id="paymentForm" action="{{ route('payment-processors.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="premium-card-title">
                    <i class="bi bi-credit-card icon-amber"></i>
                    Configuración del Procesador
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Banco Popular, Paypal">
                            @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="tarjeta" {{ old('tipo') === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('tipo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Comisión (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="comision_porcentaje" class="form-control" value="{{ old('comision_porcentaje', '0') }}" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Comisión Fija</label>
                            <div class="input-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" step="0.01" name="comision_fija" class="form-control" value="{{ old('comision_fija', '0') }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="premium-card-title">
                    <i class="bi bi-key icon-amber"></i>
                    Conexión API
                </div>
                <div class="premium-card-subtitle">Credenciales para conectarse al procesador de pagos. El api_secret se almacena encriptado.</div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">API Key / Client ID</label>
                            <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key') }}" placeholder="Ej. pk_live_xxxxx">
                            @error('api_key')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">API Secret</label>
                            <div class="input-group">
                                <input type="password" name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ old('api_secret') }}" placeholder="••••••••">
                                <button class="input-group-text btn btn-outline-secondary" type="button" onclick="toggleSecret(this)"><i class="bi bi-eye"></i></button>
                            </div>
                            @error('api_secret')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">API Endpoint</label>
                            <input type="url" name="api_endpoint" class="form-control @error('api_endpoint') is-invalid @enderror" value="{{ old('api_endpoint') }}" placeholder="https://api.procesador.com/v1">
                            @error('api_endpoint')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Entorno</label>
                            <select name="api_environment" class="form-select @error('api_environment') is-invalid @enderror">
                                <option value="sandbox" {{ old('api_environment', 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Pruebas)</option>
                                <option value="production" {{ old('api_environment') === 'production' ? 'selected' : '' }}>Producción</option>
                            </select>
                            @error('api_environment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Configuración adicional (JSON)</label>
                            <textarea name="config_json" class="form-control @error('config_json') is-invalid @enderror" rows="3" placeholder='{"merchant_id": "123", "terminal": "01"}' style="font-family: monospace;">{{ old('config_json') }}</textarea>
                            @error('config_json')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" checked>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-none d-md-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo procesador de pago</span>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('payment-processors.index') }}" class="btn btn-cancel">Cancelar</a>
            <button type="submit" form="paymentForm" class="btn btn-save">
                <i class="bi bi-check-lg me-1"></i> Guardar Procesador
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