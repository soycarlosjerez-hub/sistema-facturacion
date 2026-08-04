@extends('layouts.app')
@section('title', 'Nuevo Dueño de Plataforma')

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
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Nuevo Dueño de Plataforma</h2>
                    <p class="mb-0 opacity-75">Crea un nuevo dueño del sistema propietario.</p>
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
                    <form action="{{ route('owner.owners.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required>
                            @error('name')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email') }}" placeholder="ejemplo@email.com" required>
                            @error('email')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Mínimo 12 caracteres" required>
                                @error('password')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg" placeholder="Repite la contraseña" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('owner.owners.index') }}" class="ui-btn ui-btn-ghost">Cancelar</a>
                            <button type="submit" class="ui-btn ui-btn-primary">
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
@endsection
