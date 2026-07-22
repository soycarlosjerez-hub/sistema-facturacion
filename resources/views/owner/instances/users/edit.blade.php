@extends('layouts.app')
@section('title', 'Editar Usuario - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@php
    $hasInstanceRoles = $instanceRoles->isNotEmpty();
@endphp

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Usuario</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.users.update', [$instance, $user]) }}" id="instanceForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="ui-input rounded-4 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="ui-input rounded-4 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nueva Contrase&ntilde;a <small class="text-muted fw-normal">(dejar en blanco para no cambiar)</small></label>
                                <input type="password" name="password" class="ui-input rounded-4 @error('password') is-invalid @enderror" placeholder="Nueva contrase&ntilde;a">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a</label>
                                <input type="password" name="password_confirmation" class="ui-input rounded-4" placeholder="Confirmar contrase&ntilde;a">
                            </div>
                        </div>

                        @if($hasInstanceRoles)
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol de Instancia (m&oacute;dulos visibles)</label>
                            <select name="instance_role_id" class="ui-select rounded-4 @error('instance_role_id') is-invalid @enderror">
                                <option value="">— Sin rol de instancia (usa configuraci&oacute;n del tipo de negocio) —</option>
                                @foreach($instanceRoles as $ir)
                                <option value="{{ $ir->id }}" {{ old('instance_role_id', $user->instance_role_id) == $ir->id ? 'selected' : '' }}>
                                    {{ $ir->name }} ({{ $ir->visibleModules()->count() }} m&oacute;dulos)
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Define qu&eacute; m&oacute;dulos ve este usuario en el sidebar.</small>
                            @error('instance_role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando usuario: {{ $user->name }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-outline rounded-4 px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-4 px-5 fw-bold shadow-sm" style="background:#f59e0b;border-color:#f59e0b;color:#000">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
</div>
@endsection
