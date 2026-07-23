@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@push('styles')
@include('partials.premium-ui')
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
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
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Cliente</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registrar un nuevo cliente en el directorio
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('clientes.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
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

    <form id="clienteForm" method="POST" action="{{ route('clientes.store') }}">
        @csrf

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información General</div>
            <div class="ui-card-subtitle">Datos principales del cliente</div>
            <div class="ui-card-body">
                <div class="form-section-title">Identificación</div>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Tipo de Persona <span class="text-danger">*</span></label>
                        <select name="tipo_persona" class="ui-select @error('tipo_persona') is-invalid @enderror">
                            <option value="fisica" {{ old('tipo_persona') === 'fisica' ? 'selected' : '' }}>Física</option>
                            <option value="juridica" {{ old('tipo_persona') === 'juridica' ? 'selected' : '' }}>Jurídica</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">RNC / Cédula</label>
                        <input type="text" name="rnc" class="ui-input @error('rnc') is-invalid @enderror" value="{{ old('rnc') }}" placeholder="000-0000000-0" maxlength="20">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Nombre / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required maxlength="255" placeholder="Nombre completo">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-lg-4">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="809-555-0100">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="cliente@ejemplo.com">
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Persona de Contacto</label>
                        <input type="text" name="persona_contacto" class="ui-input @error('persona_contacto') is-invalid @enderror" value="{{ old('persona_contacto') }}" placeholder="Nombre de contacto">
                    </div>
                </div>

                <div class="form-section-title mt-4">Ubicación</div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección</label>
                        <textarea name="direccion" rows="2" class="ui-textarea @error('direccion') is-invalid @enderror" placeholder="Calle, número, sector...">{{ old('direccion') }}</textarea>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Ciudad</label>
                        <input type="text" name="ciudad" class="ui-input @error('ciudad') is-invalid @enderror" value="{{ old('ciudad') }}" placeholder="Santo Domingo">
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Código Postal</label>
                        <input type="text" name="codigo_postal" class="ui-input @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal') }}" placeholder="10101">
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card" style="--delay:.15s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-gear"></i> Configuración Comercial</div>
            <div class="ui-card-subtitle">Clasificación y límites de crédito</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="ui-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <select name="tipo_cliente" class="ui-select @error('tipo_cliente') is-invalid @enderror">
                            <option value="regular" {{ old('tipo_cliente') === 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="premium" {{ old('tipo_cliente') === 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="mayorista" {{ old('tipo_cliente') === 'mayorista' ? 'selected' : '' }}>Mayorista</option>
                            <option value="gobierno" {{ old('tipo_cliente') === 'gobierno' ? 'selected' : '' }}>Gobierno</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Segmento</label>
                        <select name="segmento" class="ui-select @error('segmento') is-invalid @enderror">
                            <option value="micro" {{ old('segmento') === 'micro' ? 'selected' : '' }}>Micro</option>
                            <option value="pequeno" {{ old('segmento') === 'pequeno' ? 'selected' : '' }}>Pequeño</option>
                            <option value="mediano" {{ old('segmento') === 'mediano' ? 'selected' : '' }}>Mediano</option>
                            <option value="grande" {{ old('segmento') === 'grande' ? 'selected' : '' }}>Grande</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Límite de Crédito</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" step="0.01" min="0" name="limite_credito" class="ui-input @error('limite_credito') is-invalid @enderror" value="{{ old('limite_credito', 0) }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Días de Crédito</label>
                        <input type="number" min="0" max="365" name="dias_credito" class="ui-input @error('dias_credito') is-invalid @enderror" value="{{ old('dias_credito', 0) }}" placeholder="30">
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" rows="2" class="ui-textarea @error('notas') is-invalid @enderror" placeholder="Información adicional...">{{ old('notas') }}</textarea>
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="check-activo" {{ old('activo', '1') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="check-activo">Cliente activo</label>
                        </div>
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="acceso_api" class="form-check-input" value="1" id="check-api" {{ old('acceso_api') ? 'checked' : '' }}>
                            <label class="form-check-label" for="check-api">Acceso API</label>
                        </div>
                    </div>
                </div>

                <div id="api-password-section" class="mt-4" style="display: none;">
                    <div class="form-section-title">Credenciales API</div>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="ui-label">Contraseña API <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="input-password" class="ui-input @error('password') is-invalid @enderror" placeholder="Mínimo 12 caracteres" minlength="12">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="ui-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="input-password-confirm" class="ui-input" placeholder="Repite la contraseña" minlength="12">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="ui-sticky-bar">
        <div class="ui-sticky-bar-inner">
            <a href="{{ route('clientes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="clienteForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>Guardar Cliente
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiCheckbox = document.getElementById('check-api');
    const passwordSection = document.getElementById('api-password-section');
    const passwordInput = document.getElementById('input-password');
    const passwordConfirmInput = document.getElementById('input-password-confirm');

    function togglePasswordSection() {
        if (apiCheckbox.checked) {
            passwordSection.style.display = 'block';
            passwordInput.required = true;
            passwordConfirmInput.required = true;
        } else {
            passwordSection.style.display = 'none';
            passwordInput.required = false;
            passwordConfirmInput.required = false;
            passwordInput.value = '';
            passwordConfirmInput.value = '';
        }
    }

    togglePasswordSection();
    apiCheckbox.addEventListener('change', togglePasswordSection);
});
</script>
@endsection
