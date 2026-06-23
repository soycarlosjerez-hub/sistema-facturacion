@extends('layouts.app')
@section('title', 'Editar Usuario - ' . $instance->nombre)
@section('content')
@php
    $hasInstanceRoles = $instanceRoles->isNotEmpty();
@endphp
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Usuario</h2>
            <p class="text-muted mb-0">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
        </div>
        <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('owner.instances.users.update', [$instance, $user]) }}">
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

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


