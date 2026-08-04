@extends('layouts.app')
@section('title', 'Dueños de Plataforma')

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
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Dueños de Plataforma</h2>
                    <p class="mb-0 opacity-75">Gestiona los dueños del sistema propietario.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.owners.create') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Dueño
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;">Total Dueños</small>
                            <h3 class="fw-bold mb-0">{{ $totalOwners }}</h3>
                        </div>
                        <div class="ui-icon-bg ms-3" style="background:rgba(139,92,246,.12)">
                            <i class="bi bi-shield-lock text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;">Total Instancias</small>
                            <h3 class="fw-bold mb-0">{{ $totalInstances }}</h3>
                        </div>
                        <div class="ui-icon-bg ms-3" style="background:rgba(59,130,246,.12)">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size:.6rem;">Instancias Activas</small>
                            <h3 class="fw-bold mb-0">{{ $activeInstances }}</h3>
                        </div>
                        <div class="ui-icon-bg ms-3" style="background:rgba(16,185,129,.12)">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Lista de Dueños</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Email</th>
                        <th class="text-center">Instancias Vinculadas</th>
                        <th class="text-center">Instancias Activas</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($owners as $owner)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(139,92,246,.12);color:#8b5cf6;font-weight:bold;font-size:.8rem;">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </div>
                                {{ $owner->name }}
                            </div>
                        </td>
                        <td>{{ $owner->email }}</td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-primary rounded-pill">{{ $owner->business_instances_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-success rounded-pill">{{ $owner->assigned_instances_count }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('owner.owners.edit', $owner) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('owner.owners.destroy', $owner) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este dueño de plataforma?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ui-btn ui-btn-ghost btn-sm rounded-pill text-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay dueños de plataforma registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 p-4 pt-0">
            {{ $owners->links() }}
        </div>
    </div>
</div>
</div>
@endsection
