@extends('layouts.app')
@section('title', 'Módulos del Sistema')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

<div class="ui-header mb-4" style="--delay:0s">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-grid"></i>
            </div>
            <div>
                <h4 class="ui-header-title">Módulos del Sistema</h4>
                <div class="ui-header-meta">
                    <i class="bi bi-box-seam me-1"></i>Gestiona los módulos disponibles para asignar a Tipos de Negocio y Roles de Instancia.
                </div>
            </div>
        </div>
        <div class="ui-header-actions">
            <a href="{{ route('owner.modules.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Módulo
            </a>
            <a href="{{ route('owner.dashboard') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
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

@foreach($modulos->groupBy('categoria') as $categoria => $modulosCat)
<div class="ui-card mb-4" style="--delay:.1s">
    <div class="ui-card-accent"></div>
    <div class="ui-card-title">
        <i class="bi bi-folder2-open"></i>{{ ucfirst($categoria) }}
    </div>
    <div class="ui-card-subtitle">{{ $modulosCat->count() }} módulo(s)</div>
    <div class="ui-card-body p-0">
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Módulo</th>
                        <th>Key</th>
                        <th>Icono</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modulosCat as $modulo)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <i class="bi {{ $modulo->icon ?? 'bi-circle' }} me-2" style="color:var(--accent)"></i>
                            {{ $modulo->label }}
                        </td>
                        <td><code>{{ $modulo->key }}</code></td>
                        <td><code>{{ $modulo->icon }}</code></td>
                        <td>{{ $modulo->orden }}</td>
                        <td>
                            @if($modulo->activo)
                                <span class="ui-badge ui-badge-success">
                                    <i class="bi bi-check-circle me-1"></i>Activo
                                </span>
                            @else
                                <span class="ui-badge ui-badge-neutral">
                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('owner.modules.edit', $modulo) }}" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($modulo->activo)
                            <form method="POST" action="{{ route('owner.modules.destroy', $modulo) }}" class="d-inline" onsubmit="return confirm('¿Desactivar el módulo {{ $modulo->label }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ui-action ui-action-delete" title="Desactivar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

</div>
@endsection
