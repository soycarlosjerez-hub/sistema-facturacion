@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('styles')
<style>
@keyframes profileGradientShift {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes profileFloat {
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-12px)}
}
@keyframes profileShine {
    0%{transform:translateX(-100%)}
    100%{transform:translateX(100%)}
}
@keyframes profileSlideUp {
    from{opacity:0;transform:translateY(24px)}
    to{opacity:1;transform:translateY(0)}
}
@keyframes profileGlow {
    0%,100%{box-shadow:0 0 20px rgba(16,185,129,.15)}
    50%{box-shadow:0 0 40px rgba(16,185,129,.3)}
}

.profile-premium-page {
    animation: profileSlideUp .5s ease;
}

.profile-premium-header {
    background: linear-gradient(135deg, #059669, #10b981, #06b6d4, #059669);
    background-size: 300% 300%;
    animation: profileGradientShift 6s ease infinite;
    border-radius: 1.2rem;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 8px 32px rgba(5,150,105,.25);
}
.profile-premium-header::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
    pointer-events: none;
}
.profile-premium-header .bubble {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    pointer-events: none;
}
.profile-premium-header .bubble:nth-child(1) {
    width: 80px; height: 80px; top: -20px; right: 10%;
    animation: profileFloat 4s ease-in-out infinite;
}
.profile-premium-header .bubble:nth-child(2) {
    width: 50px; height: 50px; bottom: 10px; right: 28%;
    animation: profileFloat 5s ease-in-out infinite 1s;
}
.profile-premium-header .bubble:nth-child(3) {
    width: 100px; height: 100px; bottom: -30px; right: 5%;
    animation: profileFloat 6s ease-in-out infinite .5s;
}

.profile-avatar-circle {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    border: 2px solid rgba(255,255,255,.35);
    flex-shrink: 0;
}

.profile-premium-card {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: 1.2rem;
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: 0 8px 32px rgba(0,0,0,.06);
    overflow: hidden;
    transition: all .3s ease;
    animation: profileSlideUp .5s ease both;
}
.profile-premium-page .row > .col-lg-6:first-child .profile-premium-card {
    animation-delay: .1s;
}
.profile-premium-page .row > .col-lg-6:last-child .profile-premium-card:first-child {
    animation-delay: .2s;
}
.profile-premium-page .row > .col-lg-6:last-child .profile-premium-card:last-child {
    animation-delay: .3s;
}
.profile-premium-card:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

.card-accent {
    height: 4px;
}
.card-accent.green { background: linear-gradient(90deg, #10b981, #06b6d4); }
.card-accent.amber { background: linear-gradient(90deg, #f59e0b, #f97316); }
.card-accent.red { background: linear-gradient(90deg, #ef4444, #f97316); }

.profile-premium-card .card-body {
    padding: 1.5rem 1.75rem 1.75rem;
}

.profile-premium-card .premium-card-title {
    font-weight: 700; font-size: 1.05rem;
    display: flex; align-items: center; gap: .75rem;
    padding: 1.25rem 1.75rem .25rem;
    margin: 0;
    color: #1e293b;
}
.profile-premium-card .premium-card-title i {
    font-size: 1.25rem;
}
.profile-premium-card .premium-card-title i.icon-green { color: #10b981; }
.profile-premium-card .premium-card-title i.icon-amber { color: #f59e0b; }
.profile-premium-card .premium-card-title i.icon-red { color: #ef4444; }

.profile-premium-card .premium-card-subtitle {
    color: #64748b; font-size: .85rem;
    padding: 0 1.75rem; margin-bottom: .6rem;
}

.profile-premium-card header {
    display: none;
}

.profile-premium-card section > form {
    margin-top: 0 !important;
}
.profile-premium-card section > form > div {
    margin-bottom: 1.25rem;
}
.profile-premium-card section > form > div:last-child {
    margin-bottom: 0;
}

.profile-premium-card .form-label {
    font-weight: 600; font-size: .85rem;
    color: #334155; margin-bottom: .35rem;
}

.profile-premium-card .form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem;
    padding: .6rem 1rem;
    font-size: .95rem;
    transition: all .2s ease;
    background: #fff;
}
.profile-premium-card .form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,.15);
    background: #fff;
}

.profile-premium-card .btn-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none; border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600; text-transform: none;
    box-shadow: 0 4px 14px rgba(16,185,129,.3);
    transition: all .25s ease;
    color: #fff;
}
.profile-premium-card .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16,185,129,.45);
    color: #fff;
}

.profile-premium-card .btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none; border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(239,68,68,.3);
    transition: all .25s ease;
    color: #fff;
}
.profile-premium-card .btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(239,68,68,.45);
    color: #fff;
}

.profile-premium-card .saved-indicator {
    color: #10b981; font-weight: 600; font-size: .85rem;
    display: inline-flex; align-items: center; gap: .4rem;
}

.profile-premium-card .text-danger.small {
    font-size: .8rem;
    margin-top: .3rem;
}

/* Premium Delete Confirmation Modal (Breeze Alpine.js) */
.profile-delete-modal {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    z-index: 1055 !important;
    overflow-y: auto;
    padding: 1.5rem;
}
.profile-delete-modal > div:first-child {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    background: rgba(0,0,0,.4);
}
.profile-delete-modal > div:first-child > div {
    display: none !important;
}
.profile-delete-modal > div:last-child {
    position: relative;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(20px);
    border-radius: 1.2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
    max-width: 480px;
    min-width: 320px;
    margin: 2rem auto;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.8);
}
body.dark-mode .profile-delete-modal > div:last-child {
    background: rgba(15,23,42,.95);
    border-color: rgba(255,255,255,.08);
}
.profile-delete-modal h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: .5rem;
}
.profile-delete-modal p {
    font-size: .9rem;
    color: #64748b;
}
body.dark-mode .profile-delete-modal h2 { color: #f1f5f9; }
body.dark-mode .profile-delete-modal p { color: #94a3b8; }

.profile-delete-modal .btn-outline-secondary {
    background: rgba(255,255,255,.8);
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    color: #475569;
    transition: all .2s ease;
}
.profile-delete-modal .btn-outline-secondary:hover {
    background: #fff;
    border-color: #cbd5e1;
    color: #1e293b;
}
.profile-delete-modal .btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none; border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(239,68,68,.3);
    transition: all .25s ease;
}
.profile-delete-modal .btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(239,68,68,.45);
}

body.dark-mode .profile-premium-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .profile-premium-card .premium-card-title { color: #f1f5f9; }
body.dark-mode .profile-premium-card .form-label { color: #cbd5e1; }
body.dark-mode .profile-premium-card .form-control {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
body.dark-mode .profile-premium-card .form-control:focus {
    background: rgba(15,23,42,.8);
    border-color: #10b981;
}
body.dark-mode .profile-premium-card .premium-card-subtitle { color: #94a3b8; }
body.dark-mode .profile-delete-modal .btn-outline-secondary {
    background: rgba(255,255,255,.05);
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode .profile-delete-modal .btn-outline-secondary:hover {
    background: rgba(255,255,255,.1);
    border-color: #475569;
    color: #f1f5f9;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 profile-premium-page">

    <div class="profile-premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="profile-avatar-circle">
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
            <div class="profile-premium-card">
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
            <div class="profile-premium-card mb-4">
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

            <div class="profile-premium-card">
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
    document.querySelectorAll('.profile-premium-page [x-data]').forEach(function (el) {
        if (el.hasAttribute('x-on:open-modal.window')) {
            el.classList.add('profile-delete-modal');
        }
    });
});
</script>
@endpush
