@extends('layouts.app')
@section('title', 'Editar Dueño de Plataforma')

@push('styles')
@include('partials.premium-ui')

<style>
/* ============================================================
   OWNER EDIT — Custom Premium Styles
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

/* Owner profile hero card */
.owner-hero-card {
    background: linear-gradient(135deg, rgba(139,92,246,.08), rgba(139,92,246,.02));
    border: 1px solid rgba(139,92,246,.15);
    border-radius: var(--radius-2xl);
    padding: 1.5rem 1.75rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 1.75rem;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, 0s);
    transition: all .3s ease;
}
.owner-hero-card:hover {
    border-color: rgba(139,92,246,.25);
    box-shadow: 0 4px 20px rgba(139,92,246,.08);
}

/* Large owner avatar */
.owner-hero-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 4px 16px rgba(139,92,246,.3);
    flex-shrink: 0;
    transition: transform .3s ease;
}
.owner-hero-avatar:hover {
    transform: scale(1.05);
}

.owner-hero-info { flex: 1; min-width: 0; }
.owner-hero-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    line-height: 1.3;
}
.owner-hero-email {
    color: #64748b;
    font-size: .9rem;
    margin: .15rem 0 0;
    display: flex;
    align-items: center;
    gap: .35rem;
}
.owner-hero-meta {
    display: flex;
    gap: 1rem;
    margin-top: .5rem;
    flex-wrap: wrap;
}
.owner-hero-meta-item {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .78rem;
    color: #64748b;
    background: rgba(139,92,246,.06);
    padding: .25rem .65rem;
    border-radius: var(--radius-pill);
}
.owner-hero-meta-item i {
    color: #8b5cf6;
    font-size: .85rem;
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

/* Info banner */
.info-banner {
    background: linear-gradient(135deg, rgba(245,158,11,.06), rgba(245,158,11,.02));
    border: 1px solid rgba(245,158,11,.15);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: .75rem;
}
.info-banner__icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(245,158,11,.1);
    display: flex; align-items: center; justify-content: center;
    color: #f59e0b;
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

/* Dark mode */
body.dark-mode .form-glass-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .owner-hero-card {
    background: rgba(139,92,246,.08);
    border-color: rgba(139,92,246,.2);
}
body.dark-mode .owner-hero-name { color: #f1f5f9; }
body.dark-mode .owner-hero-email { color: #94a3b8; }
body.dark-mode .owner-hero-meta-item {
    background: rgba(139,92,246,.12);
}
body.dark-mode .info-banner {
    background: rgba(245,158,11,.08);
    border-color: rgba(245,158,11,.2);
}
body.dark-mode .info-banner__text strong { color: #f1f5f9; }
body.dark-mode .info-banner__text small { color: #94a3b8; }
body.dark-mode .info-banner__icon {
    background: rgba(245,158,11,.15);
    color: #fbbf24;
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
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Dueño de Plataforma</h2>
                    <p class="mb-0 opacity-75">Actualiza la información del propietario del sistema.</p>
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

            {{-- Owner Profile Hero Card --}}
            <div class="owner-hero-card" style="--delay:.15s">
                <div class="owner-hero-avatar">
                    {{ strtoupper(substr(explode(' ', $owner->name)[0], 0, 1)) }}
                </div>
                <div class="owner-hero-info">
                    <h3 class="owner-hero-name">{{ $owner->name }}</h3>
                    <p class="owner-hero-email">
                        <i class="bi bi-envelope" style="font-size:.8rem;"></i>
                        {{ $owner->email }}
                    </p>
                    <div class="owner-hero-meta">
                        <span class="owner-hero-meta-item">
                            <i class="bi bi-box-seam"></i>
                            {{ $owner->business_instances_count }} inst. vinculadas
                        </span>
                        <span class="owner-hero-meta-item">
                            <i class="bi bi-check-circle"></i>
                            {{ $owner->assigned_instances_count }} activas
                        </span>
                        <span class="owner-hero-meta-item">
                            <i class="bi bi-hash"></i>
                            ID #{{ $owner->id }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Edit Form Card --}}
            <div class="form-glass-card" style="--delay:.2s">
                {{-- Decorative blobs --}}
                <div class="form-decor form-decor--1"></div>
                <div class="form-decor form-decor--2"></div>

                {{-- Accent strip --}}
                <div class="ui-card-accent" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>

                <div class="card-body p-4 p-md-5 position-relative" style="z-index:1;">
                    <form action="{{ route('owner.owners.update', $owner) }}" method="POST" id="ownerEditForm">
                        @csrf
                        @method('PUT')

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
                                   value="{{ old('name', $owner->name) }}"
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
                                   value="{{ old('email', $owner->email) }}"
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
                            <label for="password" class="ui-label">Nueva Contraseña <span class="text-muted fw-normal">(opcional)</span></label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="ui-input @error('password') is-invalid @enderror"
                                   placeholder="Dejar vacío para mantener la actual"
                                   minlength="12"
                                   autocomplete="new-password">
                            <i class="bi bi-lock field-icon"></i>
                            @error('password')<div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field-floating">
                            <label for="password_confirmation" class="ui-label">Confirmar Nueva Contraseña</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="ui-input"
                                   placeholder="Repite la nueva contraseña"
                                   minlength="12"
                                   autocomplete="new-password">
                            <i class="bi bi-lock-fill field-icon"></i>
                        </div>

                        {{-- Warning Banner --}}
                        <div class="info-banner">
                            <div class="info-banner__icon">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <div class="info-banner__text">
                                <strong>Acceso Total al Sistema</strong>
                                <small>Esta cuenta tiene acceso total al sistema. Los cambios aplicarán inmediatamente. Si cambias la contraseña, las sesiones activas se invalidarán.</small>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-3 justify-content-end mt-4 pt-2">
                            <a href="{{ route('owner.owners.index') }}" class="ui-btn ui-btn-ghost">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn-submit-glow">
                                <i class="bi bi-check-lg me-2"></i>Actualizar Dueño
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
@endsection
