@extends('layouts.app')

@section('title', 'Autenticación de Dos Factores')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-shield-halved fa-xl text-warning"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h3 class="mb-1 fw-bold text-dark">Autenticación de Dos Factores</h3>
                    <p class="text-muted mb-0">Proteja su cuenta con un nivel de seguridad adicional</p>
                </div>
            </div>

            {{-- Alertas --}}
            @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li><i class="fas fa-times-circle me-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (!$two_factor_enabled)
            {{-- CARD: Estado Inactivo --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">2FA: Desactivado</h5>
                        <div class="ms-auto">
                            <span class="badge bg-danger bg-opacity-10 text-danger">Vulnerable</span>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Su cuenta actualmente solo usa contraseña. Active la autenticación de dos factores
                        para agregar una capa extra de seguridad. Se requerirá un código temporal de su app autenticadora
                        (Google Authenticator, Authy, Microsoft Authenticator) al iniciar sesión.
                    </p>

                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-warning btn-lg fw-semibold py-3"
                                onclick="enableTwoFactor()">
                            <i class="fas fa-qrcode me-2"></i>Activar Autenticación de Dos Factores
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal: Generar QR --}}
            <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="qrModalLabel">
                                <i class="fas fa-qrcode me-2 text-warning"></i>
                                Configurar Autenticador
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4" id="qrContent">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-warning fw-semibold" id="btnConfirm2FA" style="display:none" data-bs-dismiss="modal">
                                <i class="fas fa-check me-1"></i>Entendido, Verificar Código
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal: Verificar Código --}}
            <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="verifyModalLabel">
                                <i class="fas fa-keyboard me-2 text-warning"></i>
                                Verificar Código
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted text-center mb-3">
                                Abra su app autenticadora en el teléfono y escriba el código de 6 dígitos.
                            </p>
                            <form id="verifyForm" action="{{ route('two-factor.confirm') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="text" name="code" class="form-control form-control-lg text-center fs-3 fw-bold letter-spacing"
                                           id="twoFactorCode" placeholder="000000" maxlength="6" required
                                           autocomplete="one-time-code">
                                    <label for="twoFactorCode">Código de 6 dígitos</label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                                        <i class="fas fa-check me-2"></i>Verificar y Activar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal: Códigos de Recuperación --}}
            <div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="recoveryModalLabel">
                                <i class="fas fa-file-alt me-2 text-warning"></i>
                                Códigos de Recuperación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning border-0 shadow-sm mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Guarde estos códigos en un lugar seguro.
                                Cada código puede usarse solo una vez. Si pierde acceso a su dispositivo autenticador,
                                necesitará estos códigos para recuperar su cuenta.
                            </div>

                            <!-- Paso 1: Verificar contraseña -->
                            <div id="recoveryPasswordStep">
                                <p class="text-muted mb-3">
                                    Ingrese su contraseña para ver los códigos de recuperación:
                                </p>
                                <form id="recoveryPasswordForm">
                                    @csrf
                                    <div class="input-group input-group-lg">
                                        <input type="password" class="form-control" id="recoveryPassword"
                                               placeholder="Su contraseña actual" required>
                                        <button type="button" class="btn btn-warning fw-semibold" onclick="fetchRecoveryCodes()">
                                            <i class="fas fa-eye me-1"></i>Mostrar Códigos
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Paso 2: Mostrar códigos -->
                            <div id="recoveryCodesDisplay" style="display: none;">
                                <div class="row g-2 justify-content-center mt-3" id="recoveryCodesGrid">
                                    <!-- Se llena dinámicamente -->
                                </div>
                                <div class="mt-3 text-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadRecoveryCodes()">
                                        <i class="fas fa-download me-1"></i>Descargar TXT
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm ms-2" onclick="showRegenerateModal()">
                                        <i class="fas fa-sync me-1"></i>Regenerar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal: Regenerar Códigos --}}
            <div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="regenerateModalLabel">
                                <i class="fas fa-sync me-2 text-warning"></i>
                                Regenerar Códigos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger border-0">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Los códigos actuales quedarán invalidados. ¿Desea continuar?
                            </div>
                            <form id="regenerateForm">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="regeneratePassword"
                                           placeholder="Su contraseña" required>
                                    <label for="regeneratePassword">Confirmar contraseña</label>
                                </div>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-warning btn-lg fw-semibold" onclick="doRegenerate()">
                                        <i class="fas fa-sync me-2"></i>Regenerar Códigos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @else
            {{-- CARD: Estado Activo --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">2FA: Activado</h5>
                        <div class="ms-auto">
                            <span class="badge bg-success bg-opacity-10 text-success">Protegido</span>
                        </div>
                    </div>
                    <p class="text-muted mb-2">
                        Su cuenta está protegida con autenticación de dos factores.
                        Cada vez que inicie sesión se le pedirá un código de su app autenticadora.
                    </p>
                    <small class="text-muted">
                        Activado el: {{ $user->two_factor_confirmed_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Acciones de Seguridad</h6>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning py-2" data-bs-toggle="modal" data-bs-target="#recoveryModal">
                            <i class="fas fa-file-alt me-2"></i>Ver / Descargar Códigos de Recuperación
                        </button>
                        <button type="button" class="btn btn-outline-warning py-2" onclick="resetTwoFactor()">
                            <i class="fas fa-redo me-2"></i>Reconfigurar 2FA (generar nuevo QR)
                        </button>
                        <button type="button" class="btn btn-outline-danger py-2" data-bs-toggle="modal" data-bs-target="#disableModal">
                            <i class="fas fa-times me-2"></i>Desactivar Autenticación de Dos Factores
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal: Desactivar 2FA --}}
            <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-danger" id="disableModalLabel">
                                <i class="fas fa-shield-slash me-2"></i>
                                Desactivar 2FA
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning border-0 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Está a punto de desactivar la autenticación de dos factores.
                                Su cuenta quedará menos segura.
                            </div>
                            <form id="disableForm" action="{{ route('two-factor.disable') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" class="form-control"
                                           id="disablePassword" placeholder="Su contraseña" required>
                                    <label for="disablePassword">Ingrese su contraseña para confirmar</label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger btn-lg fw-semibold">
                                        <i class="fas fa-times me-2"></i>Confirmar Desactivación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Fin modal disable --}}

            @endif
            {{-- Fin if two_factor_enabled --}}

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.letter-spacing {
    letter-spacing: 0.5em;
    font-size: 1.5rem;
    text-align: center;
}
.card {
    border-radius: 1rem !important;
}
</style>
@endpush

@push('scripts')
<script>
// Función para activar 2FA
function enableTwoFactor() {
    fetch('{{ route("two-factor.enable") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Mostrar QR en modal
            document.getElementById('qrContent').innerHTML = `
                <img src="${data.qr_code_url}" alt="QR Code 2FA" class="img-fluid mb-3" style="max-width: 250px; border-radius: 0.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <p class="text-muted small mb-2">Escanee este código QR con Google Authenticator, Authy o similar</p>
            `;
            document.getElementById('btnConfirm2FA').style.display = 'inline-block';
            $('#qrModal').modal('show');

            // Guardar códigos de recuperación temporalmente
            if (data.recovery_codes && data.recovery_codes.length) {
                window._recoveryCodes = data.recovery_codes;
            }
        }
    })
    .catch(err => {
        alert('Error al generar el código QR. Intente nuevamente.');
        console.error(err);
    });
}

// Mostrar códigos de recuperación
function showRecoveryCodes(codes) {
    let grid = '';
    for (let i = 0; i < codes.length; i += 2) {
        grid += `<div class="col-6 col-md-3">
                    <div class="bg-light rounded p-2 text-center fw-bold fs-5" style="letter-spacing: 0.15em;">
                        ${codes[i]}
                        ${codes[i+1] ? `<br>${codes[i+1]}` : ''}
                    </div>
                </div>`;
    }
    window._recoveryCodesHTML = grid;
}

// Obtener códigos de recuperación (cuando el 2FA está activo)
function fetchRecoveryCodes() {
    const pwd = document.getElementById('recoveryPassword').value;
    if (!pwd) return;

    document.getElementById('recoveryPasswordStep').innerHTML = `
        <div class="d-flex justify-content-center py-3">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>`;

    fetch('{{ route("two-factor.recovery") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ password: pwd })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recoveryPasswordStep').style.display = 'none';
            document.getElementById('recoveryCodesDisplay').style.display = 'block';
            document.getElementById('recoveryCodesGrid').innerHTML = buildRecoveryGrid(data.recovery_codes);
            window._recoveryCodes = data.recovery_codes;
        } else {
            document.getElementById('recoveryPasswordStep').innerHTML = `
                <div class="alert alert-danger border-0">
                    ${data.error || 'Error desconocido'}
                </div>
                <form id="recoveryPasswordForm">
                    <div class="input-group input-group-lg">
                        <input type="password" class="form-control" id="recoveryPassword"
                               placeholder="Su contraseña actual" required>
                        <button type="button" class="btn btn-warning fw-semibold" onclick="fetchRecoveryCodes()">
                            <i class="fas fa-eye me-1"></i>Mostrar Códigos
                        </button>
                    </div>
                </form>`;
        }
    });
}

function buildRecoveryGrid(codes) {
    let grid = '';
    for (let i = 0; i < codes.length; i += 2) {
        grid += `<div class="col-6 col-md-4 col-lg-2">
                    <div class="bg-light rounded p-3 text-center fw-bold fs-5" style="letter-spacing: 0.15em;">
                        ${codes[i]}
                        ${codes[i+1] ? `<br>${codes[i+1]}` : ''}
                    </div>
                </div>`;
    }
    return grid;
}

function downloadRecoveryCodes() {
    if (!window._recoveryCodes) return;
    const blob = new Blob([window._recoveryCodes.join('\n')], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'codigos-recuperacion-2fa.txt';
    a.click();
    URL.revokeObjectURL(url);
}

function showRegenerateModal() {
    $('#recoveryModal').modal('hide');
    $('#regenerateModal').modal('show');
}

function doRegenerate() {
    const pwd = document.getElementById('regeneratePassword').value;
    if (!pwd) return;

    fetch('{{ route("two-factor.recovery") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ password: pwd })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            $('#regenerateModal').modal('hide');
            // Reabrir recovery modal con nuevos codigos
            setTimeout(() => {
                $('#recoveryModal').modal('show');
                document.getElementById('recoveryPasswordStep').style.display = 'none';
                document.getElementById('recoveryCodesDisplay').style.display = 'block';
                document.getElementById('recoveryCodesGrid').innerHTML = buildRecoveryGrid(data.recovery_codes);
                window._recoveryCodes = data.recovery_codes;
            }, 500);
        }
    });
}

// Reconfigurar 2FA (regenerar QR)
function resetTwoFactor() {
    if (!confirm('Se generará un nuevo código QR. Los códigos actuales quedarán invalidados. ¿Continuar?')) return;
    enableTwoFactor();
}
</script>
@endpush
