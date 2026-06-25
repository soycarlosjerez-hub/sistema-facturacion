@extends('layouts.app')
@section('title', "Roles - {$instance->nombre}")

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem;">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-shield"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Roles de Instancia</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.instances.roles.create', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Rol
                </a>
                <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <div class="card-body p-0">
            @forelse($roles as $role)
            <div class="d-flex align-items-center justify-content-between p-4 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-shield text-info fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $role->name }}</h5>
                        <small class="text-muted">
                            {{ $role->users_count }} usuario(s) asignados &middot;
                            {{ $role->visibleModules()->count() }} módulo(s)
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.instances.roles.edit', [$instance, $role]) }}" class="premium-btn-edit">
                        <i class="bi bi-pencil me-1"></i>Editar Módulos
                    </a>
                    @if($role->users_count === 0)
                    <form method="POST" action="{{ route('owner.instances.roles.destroy', [$instance, $role]) }}" onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="premium-btn-delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-0">No hay roles definidos para esta instancia.</p>
                <a href="{{ route('owner.instances.roles.create', $instance) }}" class="btn btn-info rounded-pill mt-2 fw-bold text-white">
                    <i class="bi bi-plus-lg me-1"></i>Crear Primer Rol
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
</div>
@endsection
