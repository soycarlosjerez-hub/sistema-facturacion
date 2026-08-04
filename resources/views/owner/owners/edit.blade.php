@extends('layouts.app')
@section('title', 'Editar Dueño de Plataforma')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Dueño de Plataforma</h2>
                    <p class="mb-0 opacity-75">Actualiza la información del dueño del sistema propietario.</p>
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
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('owner.owners.update', $owner) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ old('name', $owner->name) }}" placeholder="Ej: Juan Pérez" required>
                            @error('name')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email', $owner->email) }}" placeholder="ejemplo@email.com" required>
                            @error('email')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Nueva Contraseña (opcional)</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Dejar vacío para mantener la actual">
                            @error('password')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg" placeholder="Repite la nueva contraseña">
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <small>Esta cuenta tiene acceso total al sistema. Los cambios aplicarán inmediatamente.</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('owner.owners.index') }}" class="ui-btn ui-btn-ghost">Cancelar</a>
                            <button type="submit" class="ui-btn ui-btn-primary">
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
