@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('styles')
@include('partials.premium-ui')
<style>
.profile-delete-modal {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    z-index: 1055 !important;
    overflow-y: auto; padding: 1.5rem;
}
.profile-delete-modal > div:first-child {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    background: rgba(0,0,0,.4);
}
.profile-delete-modal > div:first-child > div { display: none !important; }
.profile-delete-modal > div:last-child {
    position: relative;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(20px);
    border-radius: 1.2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
    max-width: 480px; min-width: 320px;
    margin: 2rem auto; overflow: hidden;
    border: 1px solid rgba(255,255,255,.8);
}
body.dark-mode .profile-delete-modal > div:last-child {
    background: rgba(15,23,42,.95);
    border-color: rgba(255,255,255,.08);
}
.profile-delete-modal h2 {
    font-size: 1.1rem; font-weight: 700;
    color: #1e293b; margin-bottom: .5rem;
}
.profile-delete-modal p {
    font-size: .9rem; color: #64748b;
}
body.dark-mode .profile-delete-modal h2 { color: #f1f5f9; }
body.dark-mode .profile-delete-modal p { color: #94a3b8; }

/* Premium form inputs */
.ui-label {
    display: block; font-weight: 600; font-size: .85rem;
    color: #334155; margin-bottom: .35rem;
}
body.dark-mode .ui-label { color: #cbd5e1; }

.ui-input-group { display: flex; align-items: stretch; }
.ui-input-group-text {
    display: flex; align-items: center; justify-content: center;
    padding: .6rem 1rem;
    border: 1.5px solid #e2e8f0; border-right: 0;
    border-radius: .65rem 0 0 .65rem;
    background: #f8fafc; color: #64748b; font-size: .9rem;
    white-space: nowrap; flex-shrink: 0;
}
.ui-input {
    flex: 1; display: block;
    border: 1.5px solid #e2e8f0; border-left: 0;
    border-radius: 0 .65rem .65rem 0;
    padding: .6rem 1rem;
    font-size: .9rem;
    background: #fff; color: #1e293b;
    transition: all .2s ease;
}
.ui-input:focus {
    border-color: #3b82f6; outline: 0;
    box-shadow: 0 0 0 .2rem rgba(59,130,246,.12);
    background: #fff;
}
.ui-input::placeholder { color: #94a3b8; }

body.dark-mode .ui-input-group-text {
    background: rgba(30,41,59,.8); border-color: #334155; color: #94a3b8;
}
body.dark-mode .ui-input {
    background: rgba(15,23,42,.6); border-color: #334155; color: #f1f5f9;
}
body.dark-mode .ui-input:focus {
    border-color: #60a5fa; background: rgba(15,23,42,.8);
    box-shadow: 0 0 0 .2rem rgba(96,165,250,.15);
}

.saved-indicator {
    color: #10b981; font-weight: 600; font-size: .85rem;
    display: inline-flex; align-items: center; gap: .4rem;
}

/* Premium Buttons */
.ui-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
    font-weight: 600; border: none; cursor: pointer;
    border-radius: .65rem; padding: .6rem 1.5rem; font-size: .9rem;
    transition: all .2s ease; text-decoration: none;
}
.ui-btn:disabled, .ui-btn.disabled { opacity: .55; pointer-events: none; }

.ui-btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff; box-shadow: 0 4px 14px rgba(59,130,246,.3);
}
.ui-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(59,130,246,.45);
    color: #fff;
}

.ui-btn-solid {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff; box-shadow: 0 4px 14px rgba(99,102,241,.3);
}
.ui-btn-solid:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99,102,241,.45);
    color: #fff;
}

.ui-btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff; box-shadow: 0 4px 14px rgba(239,68,68,.3);
}
.ui-btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(239,68,68,.45);
    color: #fff;
}

.ui-btn-ghost {
    background: #fff; border: 1.5px solid #e2e8f0;
    color: #475569;
}
.ui-btn-ghost:hover {
    background: #f8fafc; border-color: #cbd5e1; color: #1e293b;
}
body.dark-mode .ui-btn-ghost {
    background: rgba(255,255,255,.05); border-color: #334155; color: #94a3b8;
}
body.dark-mode .ui-btn-ghost:hover {
    background: rgba(255,255,255,.1); border-color: #475569; color: #f1f5f9;
}

.ui-btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; box-shadow: 0 4px 14px rgba(245,158,11,.3);
}
.ui-btn-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245,158,11,.45);
    color: #fff;
}

.ui-btn-outline-warning {
    background: #fff; border: 1.5px solid #f59e0b;
    color: #d97706;
}
.ui-btn-outline-warning:hover {
    background: #fef3c7; border-color: #d97706; color: #92400e;
}
body.dark-mode .ui-btn-outline-warning {
    background: rgba(245,158,11,.1); border-color: #f59e0b; color: #fbbf24;
}
body.dark-mode .ui-btn-outline-warning:hover {
    background: rgba(245,158,11,.2); border-color: #fbbf24; color: #fde68a;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ Auth::user()?->name ?? 'Mi Perfil' }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ ucfirst(Auth::user()?->roles?->first()?->name ?? Auth::user()?->role ?? 'Usuario') }}
                        <span class="mx-2">·</span>
                        <i class="bi bi-envelope me-1"></i>
                        {{ Auth::user()?->email ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.1s;">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #3b82f6, rgba(59,130,246,.3));"></div>
                <div class="ui-card-title">
                    <i class="bi bi-person-badge" style="color:#3b82f6;"></i>
                    Información del perfil
                </div>
                <div class="ui-card-subtitle">Actualiza tu nombre y correo electrónico</div>
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card mb-4" style="--delay:.2s;">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #f59e0b, rgba(245,158,11,.3));"></div>
                <div class="ui-card-title">
                    <i class="bi bi-shield-lock" style="color:#f59e0b;"></i>
                    Cambiar contraseña
                </div>
                <div class="ui-card-subtitle">Asegura tu cuenta con una contraseña segura</div>
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.25s;">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #6366f1, rgba(99,102,241,.3));"></div>
                <div class="ui-card-title">
                    <i class="bi bi-shield-check" style="color:#6366f1;"></i>
                    Autenticación de Dos Factores (2FA)
                </div>
                <div class="ui-card-subtitle">Protege tu cuenta con un código temporal desde Google Authenticator</div>
                <div class="card-body p-4">
                    @if(auth()->user()->two_factor_secret)
                        <div class="alert alert-success border-0 mb-3 rounded-4">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>2FA activado</strong> — Tu cuenta está protegida.
                        </div>
                    @else
                        <div class="alert alert-warning border-0 mb-3 rounded-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>2FA desactivado</strong> — Tu cuenta solo usa contraseña.
                        </div>
                    @endif
                    <div class="d-grid">
                        <a href="{{ route('two-factor.index') }}" class="ui-btn {{ auth()->user()->two_factor_secret ? 'ui-btn-ghost' : 'ui-btn-solid' }} fw-semibold">
                            <i class="bi bi-shield-fill-check me-2"></i>
                            {{ auth()->user()->two_factor_secret ? 'Administrar 2FA' : 'Activar 2FA' }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.3s;">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #ef4444, rgba(239,68,68,.3));"></div>
                <div class="ui-card-title">
                    <i class="bi bi-exclamation-triangle" style="color:#ef4444;"></i>
                    Eliminar cuenta
                </div>
                <div class="ui-card-subtitle">Esta acción es irreversible</div>
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ui-page [x-data]').forEach(function (el) {
        if (el.hasAttribute('x-on:open-modal.window')) {
            el.classList.add('profile-delete-modal');
        }
    });
});
</script>
@endpush
