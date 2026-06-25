@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('styles')
@include('partials.premium-ui')
<style>
/* Profile-specific overrides */
.premium-page .row > .col-lg-6:first-child .premium-card {
    animation-delay: .1s;
}
.premium-page .row > .col-lg-6:last-child .premium-card:first-child {
    animation-delay: .2s;
}
.premium-page .row > .col-lg-6:last-child .premium-card:last-child {
    animation-delay: .3s;
}

.premium-card header { display: none; }

.premium-card section > form { margin-top: 0 !important; }
.premium-card section > form > div { margin-bottom: 1.25rem; }
.premium-card section > form > div:last-child { margin-bottom: 0; }

.premium-card .saved-indicator {
    color: #10b981; font-weight: 600; font-size: .85rem;
    display: inline-flex; align-items: center; gap: .4rem;
}
.premium-card .text-danger.small {
    font-size: .8rem; margin-top: .3rem;
}

/* Profile Delete Modal */
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

body.dark-mode .premium-card .form-label { color: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-white">{{ Auth::user()?->name ?? 'Mi Perfil' }}</h4>
                <small class="text-white opacity-75">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ ucfirst(Auth::user()?->roles?->first()?->name ?? Auth::user()?->role ?? 'Usuario') }}
                    <span class="mx-2">·</span>
                    <i class="bi bi-envelope me-1"></i>
                    {{ Auth::user()?->email ?? '' }}
                </small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="premium-card">
                <div class="card-accent green"></div>
                <div class="premium-card-title">
                    <i class="bi bi-person-badge icon-green"></i>
                    Información del perfil
                </div>
                <div class="premium-card-subtitle">Actualiza tu nombre y correo electrónico</div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="premium-card mb-4">
                <div class="card-accent amber"></div>
                <div class="premium-card-title">
                    <i class="bi bi-shield-lock icon-amber"></i>
                    Cambiar contraseña
                </div>
                <div class="premium-card-subtitle">Asegura tu cuenta con una contraseña segura</div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="premium-card">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-exclamation-triangle icon-red"></i>
                    Eliminar cuenta
                </div>
                <div class="premium-card-subtitle">Esta acción es irreversible</div>
                <div class="card-body">
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
    document.querySelectorAll('.premium-page [x-data]').forEach(function (el) {
        if (el.hasAttribute('x-on:open-modal.window')) {
            el.classList.add('profile-delete-modal');
        }
    });
});
</script>
@endpush
