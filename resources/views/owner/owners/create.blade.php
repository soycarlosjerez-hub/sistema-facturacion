@extends('layouts.app')
@section('title', 'Nuevo Dueño de Plataforma')

@push('styles')
@include('partials.premium-ui')

<style>
/* ============================================================
   OWNER CREATE — Custom Premium Styles
   ============================================================ */

/* Decorative background blobs */
.form-decor {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: .12;
    pointer-events: none;
    z-index: 0;
}
.form-decor--1 {
    width: 200px; height: 200px;
    background: #8b5cf6;
    top: -40px; right: -40px;
}
.form-decor--2 {
    width: 150px; height: 150px;
    background: #a78bfa;
    bottom: -30px; left: -30px;
}

/* Glass form card */
.form-glass-card {
    position: relative;
    overflow: hidden;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: var(--shadow-xl);
    transition: all .3s ease;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, 0s);
}
.form-glass-card:hover {
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
}

/* Floating label effect */
.form-field-floating {
    position: relative;
    margin-bottom: 1.5rem;
}
.form-field-floating .field-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1rem;
    transition: color .2s ease;
    pointer-events: none;
    z-index: 2;
}
.form-field-floating textarea ~ .field-icon {
    top: 1.25rem;
    transform: none;
}
.form-field-floating .ui-input,
.form-field-floating .ui-select,
.form-field-floating .ui-textarea {
    padding-left: 3rem;
}
.form-field-floating .ui-input:focus ~ .field-icon,
.form-field-floating .ui-select:focus ~ .field-icon,
.form-field-floating .ui-textarea:focus ~ .field-icon {
    color: var(--accent, #8b5cf6);
}

/* Password strength bar */
.password-strength {
    height: 3px;
    border-radius: 2px;
    margin-top: .5rem;
    transition: all .3s ease;
    background: #e2e8f0;
    overflow: hidden;
}
.password-strength__bar {
    height: 100%;
    border-radius: 2px;
    transition: width .3s ease, background .3s ease;
    width: 0%;
}
.password-strength__bar.weak   { width: 33%; background: #ef4444; }
.password-strength__bar.medium { width: 66%; background: #f59e0b; }
.password-strength__bar.strong { width: 100%; background: #10b981; }

/* Submit button glow */
.btn-submit-glow {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, var(--accent, #8b5cf6), var(--accent-hover, #7c3aed));
    color: #fff;
    border: none;
    font-weight: 700;
    padding: .7rem 2rem;
    border-radius: var(--radius);
    transition: all .3s ease;
    box-shadow: 0 4px 16px rgba(139,92,246,.3);
}
.btn-submit-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(139,92,246,.45);
    color: #fff;
}
.btn-submit-glow:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(139,92,246,.3);
}
.btn-submit-glow::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.2), transparent);
    opacity: 0;
    transition: opacity .3s ease;
}
.btn-submit-glow:hover::after {
    opacity: 1;
}

/* Info banner */
.info-banner {
    background: linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));
    border: 1px solid rgba(139,92,246,.15);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: .75rem;
}
.info-banner__icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(139,92,246,.1);
    display: flex; align-items: center; justify-content: center;
    color: #8b5cf6;
    font-size: 1rem;
    flex-shrink: 0;
}
.info-banner__text strong {
    display: block;
    font-size: .85rem;
    color: #1e293b;
    margin-bottom: .15rem;
}
.info-banner__text small {
    color: #64748b;
    font-size: .8rem;
    line-height: 1.5;
}

/* Section divider */
.section-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1.75rem 0;
}
.section-divider::before,
.section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(139,92,246,.15), transparent);
}
.section-divider span {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .8px;
    font-weight: 700;
    color: #94a3b8;
    white-space: nowrap;
}

/* Dark mode */
body.dark-mode .form-glass-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .info-banner {
    background: rgba(139,92,246,.08);
    border-color: rgba(139,92,246,.2);
}
body.dark-mode .info-banner__text strong { color: #f1f5f9; }
body.dark-mode .info-banner__text small { color: #94a3b8; }
body.dark-mode .info-banner__icon {
    background: rgba(139,92,246,.15);
    color: #a78bfa;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Nuevo Dueño de Plataforma</h2>
                    <p class="mb-0 opacity-75">Crea un nuevo propietario con acceso total al sistema.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.owners.index') }}" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 justify-content-center">
        <div class="col-lg-8">
            <div class="form-glass-card" style="--delay:.2s">
                {{-- Decorative blobs --}}
                <div class="form-decor form-decor--1"></div>
                <div class="form-decor form-decor--2"></div>

                {{-- Accent strip --}}
                <div class="ui-card-accent" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>

                <div class="card-body p-4 p-md-5 position-relative" style="z-index:1;">
                    <form action="{{ route('owner.owners.store') }}" method="POST" id="ownerForm">
                        @csrf

                        {{-- Personal Info Section --}}
                        <div class="section-divider">
                            <span><i class="bi bi-person me-1"></i>Información Personal</span>
                        </div>

                        <div class="form-field-floating">
                            <label for="name" class="ui-label">Nombre Completo</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="ui-input @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Ej: Juan Pérez"
                                   required
                                   autofocus>
                            <i class="bi bi-person field-icon"></i>
                            @error('name')<div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field-floating">
                            <label for="email" class="ui-label">Correo Electrónico</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="ui-input @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="ejemplo@email.com"
                                   required>
                            <i class="bi bi-envelope field-icon"></i>
                            @error('email')<div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        {{-- Security Section --}}
                        <div class="section-divider">
                            <span><i class="bi bi-shield-lock me-1"></i>Seguridad</span>
                        </div>

                        <div class="form-field-floating">
                            <label for="password" class="ui-label">Contraseña</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="ui-input @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 12 caracteres"
                                   required
                                   minlength="12"
                                   autocomplete="new-password">
                            <i class="bi bi-lock field-icon"></i>
                            <div class="password-strength">
                                <div class="password-strength__bar" id="strengthBar"></div>
                            </div>
                            @error('password')<div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field-floating">
                            <label for="password_confirmation" class="ui-label">Confirmar Contraseña</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="ui-input"
                                   placeholder="Repite la contraseña"
                                   required
                                   minlength="12"
                                   autocomplete="new-password">
                            <i class="bi bi-lock-fill field-icon"></i>
                        </div>

                        {{-- Warning Banner --}}
                        <div class="info-banner">
                            <div class="info-banner__icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="info-banner__text">
                                <strong>Acceso Total al Sistema</strong>
                                <small>Este dueño tendrá permisos administrativos completos sobre todas las instancias, usuarios y configuraciones del sistema.</small>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-3 justify-content-end mt-4 pt-2">
                            <a href="{{ route('owner.owners.index') }}" class="ui-btn ui-btn-ghost">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn-submit-glow">
                                <i class="bi bi-check-lg me-2"></i>Crear Dueño
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Password strength indicator script --}}
<script>
(function() {
    const passwordInput = document.getElementById('password');
    const bar = document.getElementById('strengthBar');
    if (!passwordInput || !bar) return;

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (val.length >= 12) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        bar.className = 'password-strength__bar';
        if (val.length === 0) {
            bar.style.width = '0%';
        } else if (score <= 2) {
            bar.classList.add('weak');
        } else if (score <= 3) {
            bar.classList.add('medium');
        } else {
            bar.classList.add('strong');
        }
    });
})();
</script>
@endsection
