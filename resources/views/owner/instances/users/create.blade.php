@extends('layouts.app')
@section('title', 'Nuevo Usuario - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@php
    $hasInstanceRoles = $instanceRoles->isNotEmpty();
@endphp

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Nuevo Usuario</h2>
                    <p class="mb-0 opacity-75"><i class="bi bi-plus-circle me-1"></i>{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.users.store', $instance) }}" id="instanceForm">
                        @csrf

                        <div class="alert alert-info rounded-4 border-0 bg-info bg-opacity-10 small" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Este usuario ser&aacute; asignado a <strong>{{ $instance->nombre }}</strong> con tipo de negocio <strong>{{ $instance->businessType?->nombre ?? '&mdash;' }}</strong>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="ui-input rounded-pill @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="ui-input rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Contrase&ntilde;a <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password" id="passwordField" class="ui-input rounded-pill pe-5 @error('password') is-invalid @enderror" {{ old('auto_password') ? 'readonly' : '' }} placeholder="M&iacute;nimo 12 caracteres">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-decoration-none me-2" onclick="togglePasswordVisibility()" title="Mostrar/Ocultar" style="color:var(--accent);">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="passwordConfirmationField" class="ui-input rounded-pill pe-5" {{ old('auto_password') ? 'readonly' : '' }} placeholder="Repetir contrase&ntilde;a">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-decoration-none me-2" onclick="togglePasswordConfirmationVisibility()" title="Mostrar/Ocultar" style="color:var(--accent);">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:rgba(var(--accent-rgb,245,158,11),.05);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="auto_password" id="autoPasswordCheck" value="1" {{ old('auto_password') ? 'checked' : '' }} onchange="handleAutoPassword()" role="switch" style="width:3em;height:1.5em;">
                                <label class="form-check-label fw-semibold ms-2" for="autoPasswordCheck">Generar contrase&ntilde;a autom&aacute;tica</label>
                            </div>
                            <small class="text-muted">Cumple pol&iacute;tica: 12+ caracteres, may&uacute;sculas, min&uacute;sculas, n&uacute;meros y s&iacute;mbolos</small>
                        </div>

                        <div class="alert alert-warning rounded-4 border-0 mb-3 d-none" id="generatedPasswordAlert" style="background:rgba(var(--accent-rgb,245,158,11),.08);border-left:4px solid #f59e0b;">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                                    <i class="bi bi-key-fill text-warning fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="d-block mb-1">Contrase&ntilde;a Generada</strong>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="ui-input font-monospace bg-white" value="" readonly id="generatedPasswordField">
                                        <button class="ui-btn ui-btn-solid btn-sm" type="button" onclick="copyGeneratedPassword()" style="background:#1e293b;border-color:#1e293b;font-size:.75rem;padding:.3rem .75rem;">Copiar</button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Guarde esta contrase&ntilde;a en un lugar seguro. No podr&aacute; volver a verla despu&eacute;s.</small>
                                </div>
                            </div>
                        </div>

                        @if($hasInstanceRoles)
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol de Instancia (m&oacute;dulos visibles)</label>
                            <select name="instance_role_id" class="ui-select rounded-pill @error('instance_role_id') is-invalid @enderror">
                                <option value="">&mdash; Sin rol de instancia (usa configuraci&oacute;n del tipo de negocio) &mdash;</option>
                                @foreach($instanceRoles as $ir)
                                <option value="{{ $ir->id }}" {{ old('instance_role_id') == $ir->id ? 'selected' : '' }}>
                                    {{ $ir->name }} ({{ $ir->visibleModules()->count() }} m&oacute;dulos)
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Define qu&eacute; m&oacute;dulos ve este usuario en el sidebar. Si no seleccionas, se usar&aacute; la configuraci&oacute;n del tipo de negocio.</small>
                            @error('instance_role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 80px;"></div>
</div>
</div>

<script>
function generateStrongPassword() {
    const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lower = 'abcdefghijklmnopqrstuvwxyz';
    const digits = '0123456789';
    const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    const all = upper + lower + digits + symbols;
    
    let password = '';
    // Ensure at least one of each type
    password += upper[Math.floor(Math.random() * upper.length)];
    password += lower[Math.floor(Math.random() * lower.length)];
    password += digits[Math.floor(Math.random() * digits.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];
    
    // Fill remaining 8 chars randomly
    for (let i = 4; i < 16; i++) {
        password += all[Math.floor(Math.random() * all.length)];
    }
    
    // Shuffle
    password = password.split('').sort(() => Math.random() - 0.5).join('');
    return password;
}

function handleAutoPassword() {
    const checked = document.getElementById('autoPasswordCheck').checked;
    const pwdField = document.getElementById('passwordField');
    const confField = document.getElementById('passwordConfirmationField');
    const alertBox = document.getElementById('generatedPasswordAlert');
    const generatedField = document.getElementById('generatedPasswordField');
    
    if (checked) {
        const pwd = generateStrongPassword();
        pwdField.value = pwd;
        pwdField.setAttribute('readonly', 'readonly');
        confField.value = pwd;
        confField.setAttribute('readonly', 'readonly');
        generatedField.value = pwd;
        alertBox.classList.remove('d-none');
    } else {
        pwdField.removeAttribute('readonly');
        confField.removeAttribute('readonly');
        pwdField.value = '';
        confField.value = '';
        generatedField.value = '';
        alertBox.classList.add('d-none');
    }
}

function togglePasswordVisibility() {
    const pwd = document.getElementById('passwordField');
    const eye = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        eye.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function togglePasswordConfirmationVisibility() {
    const pwd = document.getElementById('passwordConfirmationField');
    const eye = document.getElementById('eyeIcon2');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        eye.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function copyGeneratedPassword() {
    const field = document.getElementById('generatedPasswordField');
    field.select();
    field.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(field.value);
    const btn = field.parentElement.querySelector('.ui-btn');
    const originalText = btn.textContent;
    btn.textContent = '\u2713 Copiado';
    setTimeout(() => { btn.textContent = originalText; }, 2000);
}
</script>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando Usuario</span>
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
