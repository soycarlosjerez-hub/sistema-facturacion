@extends('layouts.app')
@section('title', 'Nuevo Usuario - ' . $instance->nombre)

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
                    <h2 class="fw-bold mb-1">Nuevo Usuario</h2>
                    <p class="mb-0 opacity-75"><i class="bi bi-plus-circle me-1"></i>{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
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
                    <form method="POST" action="{{ route('owner.instances.users.store', $instance) }}" id="instanceForm">
                        @csrf

                        <div class="alert alert-info rounded-4 border-0 bg-info bg-opacity-10 small" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Este usuario ser&aacute; asignado a <strong>{{ $instance->nombre }}</strong> con tipo de negocio <strong>{{ $instance->businessType?->nombre ?? '&mdash;' }}</strong>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="ui-input rounded-pill @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="ui-input rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Contrase&ntilde;a <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="ui-input rounded-pill @error('password') is-invalid @enderror" required placeholder="M&iacute;nimo 6 caracteres">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="ui-input rounded-pill" required placeholder="Repetir contrase&ntilde;a">
                            </div>
                        </div>

                        @if($hasInstanceRoles)
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol de Instancia (m&oacute;dulos visibles)</label>
                            <select name="instance_role_id" class="ui-select rounded-pill @error('instance_role_id') is-invalid @enderror">
                                <option value="">&mdash; Sin rol de instancia (usa configuraci&oacute;n del tipo de negocio) &mdash;</option>
                                @foreach($instanceRoles as $ir)
                                <option value="{{ $ir->id }}" {{ old('instance_role_id') == $ir->id ? 'selected' : '' }}>
                                    {{ $ir->name }} ({{ $ir->visibleModules()->count() }} m&oacute;dulos)
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Define qu&eacute; m&oacute;dulos ve este usuario en el sidebar. Si no seleccionas, se usar&aacute; la configuraci&oacute;n del tipo de negocio.</small>
                            @error('instance_role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 80px;"></div>
</div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando Usuario</span>
        </div>
        <div>
            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-outline me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid">
                <i class="bi bi-check-lg me-2"></i>Guardar
            </button>
        </div>
    </div>
</div>
@endsection
