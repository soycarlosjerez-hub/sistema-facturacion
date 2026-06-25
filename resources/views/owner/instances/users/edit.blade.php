@extends('layouts.app')
@section('title', 'Editar Usuario - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@php
    $hasInstanceRoles = $instanceRoles->isNotEmpty();
@endphp

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem; background: linear-gradient(135deg, #f59e0b, #f97316, #ef4444, #f59e0b);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Usuario</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
                </div>
            </div>
            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="premium-card">
                <div class="card-accent amber"></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.users.update', [$instance, $user]) }}" id="instanceForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-pill @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nueva Contrase&ntilde;a <small class="text-muted fw-normal">(dejar en blanco para no cambiar)</small></label>
                                <input type="password" name="password" class="form-control rounded-pill @error('password') is-invalid @enderror" placeholder="Nueva contraseña">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-pill" placeholder="Confirmar contraseña">
                            </div>
                        </div>

                        @if($hasInstanceRoles)
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol de Instancia (módulos visibles)</label>
                            <select name="instance_role_id" class="form-select rounded-pill @error('instance_role_id') is-invalid @enderror">
                                <option value="">— Sin rol de instancia (usa configuración del tipo de negocio) —</option>
                                @foreach($instanceRoles as $ir)
                                <option value="{{ $ir->id }}" {{ old('instance_role_id', $user->instance_role_id) == $ir->id ? 'selected' : '' }}>
                                    {{ $ir->name }} ({{ $ir->visibleModules()->count() }} módulos)
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Define qué módulos ve este usuario en el sidebar.</small>
                            @error('instance_role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando usuario: {{ $user->name }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-cancel rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-save rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
</div>
@endsection
