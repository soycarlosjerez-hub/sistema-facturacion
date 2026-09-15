@extends('layouts.app')

@section('title', 'Nuevo Procesador de Pago')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <span class="ui-badge ui-badge-primary px-3 py-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-plus-lg me-1"></i>NUEVO PROCESADOR
                    </span>
                    <h4 class="ui-header-title">Nuevo Procesador de Pago</h4>
                    <div class="ui-header-meta">Registra un procesador de pagos con credenciales API</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('payment-processors.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
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

    <div class="ui-card overflow-hidden mb-5" style="--delay:.1s">
        <div class="ui-card-accent blue"></div>
        <form id="paymentForm" action="{{ route('payment-processors.store') }}" method="POST">
            @csrf
            <div class="ui-card-body p-4 p-md-5">
                <div class="ui-card-title px-0 pt-0">
                    <i class="bi bi-credit-card"></i>
                    Configuración del Procesador
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Banco Popular, Paypal">
                        @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">Tipo</label>
                        <select name="tipo" class="ui-select">
                            <option value="tarjeta" {{ old('tipo') === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ old('tipo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">Comisión (%)</label>
                        <div class="ui-input-group">
                            <input type="number" step="0.01" name="comision_porcentaje" class="ui-input" value="{{ old('comision_porcentaje', '0') }}" min="0" max="100" style="border-right:0;border-radius:var(--radius) 0 0 var(--radius);">
                            <span class="ui-input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">Comisión Fija</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" name="comision_fija" class="ui-input" value="{{ old('comision_fija', '0') }}" min="0" style="border-left:0;border-radius:0 var(--radius) var(--radius) 0;">
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="ui-card-title px-0 pt-0">
                    <i class="bi bi-key"></i>
                    Conexión API
                </div>
                <div class="ui-card-subtitle px-0">Credenciales para conectarse al procesador de pagos. El api_secret se almacena encriptado.</div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">API Key / Client ID</label>
                        <input type="text" name="api_key" class="ui-input @error('api_key') is-invalid @enderror" value="{{ old('api_key') }}" placeholder="Ej. pk_live_xxxxx">
                        @error('api_key')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">API Secret</label>
                        <div class="ui-input-group">
                            <input type="password" name="api_secret" class="ui-input @error('api_secret') is-invalid @enderror" value="{{ old('api_secret') }}" placeholder="••••••••" style="border-right:0;border-radius:var(--radius) 0 0 var(--radius);">
                            <button class="ui-input-group-text btn ui-btn-ghost" type="button" onclick="toggleSecret(this)"><i class="bi bi-eye"></i></button>
                        </div>
                        @error('api_secret')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">API Endpoint</label>
                        <input type="url" name="api_endpoint" class="ui-input @error('api_endpoint') is-invalid @enderror" value="{{ old('api_endpoint') }}" placeholder="https://api.procesador.com/v1">
                        @error('api_endpoint')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-semibold">Entorno</label>
                        <select name="api_environment" class="ui-select @error('api_environment') is-invalid @enderror">
                            <option value="sandbox" {{ old('api_environment', 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Pruebas)</option>
                            <option value="production" {{ old('api_environment') === 'production' ? 'selected' : '' }}>Producción</option>
                        </select>
                        @error('api_environment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-semibold">Configuración adicional (JSON)</label>
                        <textarea name="config_json" class="ui-input @error('config_json') is-invalid @enderror" rows="3" placeholder='{"merchant_id": "123", "terminal": "01"}' style="font-family: monospace;">{{ old('config_json') }}</textarea>
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
        </form>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-none d-md-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent);"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo procesador de pago</span>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('payment-processors.index') }}" class="ui-btn ui-btn-ghost">Cancelar</a>
            <button type="submit" form="paymentForm" class="ui-btn ui-btn-solid">
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