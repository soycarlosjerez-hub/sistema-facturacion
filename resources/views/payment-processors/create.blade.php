@extends('layouts.app')
@section('title', 'Nuevo Procesador de Pago')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-credit-card text-primary me-2"></i>Nuevo Procesador de Pago</h2>
                    <p class="text-muted mb-0">Registra un procesador de pagos con credenciales API</p>
                </div>
                <a href="{{ route('payment-processors.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('payment-processors.store') }}" method="POST">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-credit-card me-2"></i>Configuración del Procesador</h5>
                    </div>
                    <div class="card-body p-4">
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

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-key me-2"></i>Conexión API</h5>
                        <p class="text-muted small mb-3">Credenciales para conectarse al procesador de pagos. El api_secret se almacena encriptado.</p>

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
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('payment-processors.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
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
