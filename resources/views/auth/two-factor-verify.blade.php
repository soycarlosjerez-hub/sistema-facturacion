@extends('layouts.app')

@section('title', 'Verificación de Seguridad')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-5">
                    {{-- Icono y titulo --}}
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-4 mb-3">
                            <i class="fas fa-lock fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Verificación de Seguridad</h4>
                        <p class="text-muted">
                            Ingrese el código de 6 dígitos de su app autenticadora.
                        </p>
                        <small class="text-muted">
                            Cuenta: {{ $user->email }}
                        </small>
                    </div>

                    {{-- Error de validación --}}
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 py-2 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        {{ $errors->first('two_factor_code', 'Código incorrecto. Intente nuevamente.') }}
                    </div>
                    @endif

                    {{-- Formulario --}}
                    <form action="{{ route('two-factor.verify') }}" method="POST">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" name="two_factor_code" class="form-control form-control-lg text-center fs-3 fw-bold letter-spacing"
                                   id="twoFactorCode" placeholder="000000" maxlength="6" required
                                   value="{{ old('two_factor_code') }}"
                                   autofocus autocomplete="one-time-code">
                            <label for="twoFactorCode">Código de 6 dígitos</label>
                        </div>

                        {{-- Boton verificar --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                                <i class="fas fa-check-circle me-2"></i>Verificar
                            </button>
                        </div>
                    </form>

                    {{-- Codigo de recuperacion --}}
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            ¿No puede acceder a su app?
                        </small>
                        <br>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none"
                                data-bs-toggle="modal" data-bs-target="#recoveryModal">
                            Usar código de recuperación
                        </button>
                    </div>

                    {{-- Cerrar sesion --}}
                    <div class="text-center mt-3">
                        <form action="{{ route('two-factor.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Codigo de recuperacion --}}
<div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="recoveryModalLabel">
                    <i class="fas fa-file-alt me-2 text-warning"></i>
                    Código de Recuperación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    Ingrese uno de los códigos de 8 dígitos que guardó al activar 2FA.
                    Cada código solo puede usarse una vez.
                </p>
                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" name="two_factor_code" class="form-control form-control-lg text-center fs-3 fw-bold letter-spacing"
                               id="recoveryCode" placeholder="00000000" maxlength="8" required
                               autofocus autocomplete="one-time-code">
                        <label for="recoveryCode">Código de recuperación</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                            <i class="fas fa-check me-2"></i>Verificar
                        </button>
                    </div>
                </form>
            </div>
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
// Auto-focus al cargar
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('twoFactorCode');
    if (input) {
        input.focus();
        // Auto-select all on focus
        input.addEventListener('focus', function() {
            this.select();
        });
        // Auto-advance (opcional)
        input.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    }
});
</script>
@endpush
