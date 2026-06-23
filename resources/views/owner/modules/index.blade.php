@extends('layouts.app')
@section('title', 'Módulos del Sistema')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-grid text-info me-2"></i>Módulos del Sistema
            </h2>
            <p class="text-muted mb-0">Gestiona los módulos disponibles para asignar a Tipos de Negocio y Roles de Instancia.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owner.modules.create') }}" class="btn btn-info rounded-pill px-4 shadow-sm fw-bold text-white">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Módulo
            </a>
            <a href="{{ route('owner.dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    @foreach($modulos->groupBy('categoria') as $categoria => $modulosCat)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 p-4 pb-3">
            <h5 class="fw-bold mb-0 text-uppercase text-muted small">{{ $categoria }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <i class="bi {{ $modulo->icon ?? 'bi-circle' }} me-2"></i>
                                {{ $modulo->label }}
                            </td>
                            <td><code>{{ $modulo->key }}</code></td>
                            <td><code>{{ $modulo->icon }}</code></td>
                            <td>{{ $modulo->orden }}</td>
                            <td>
                                @if($modulo->activo)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('owner.modules.edit', $modulo) }}" class="btn btn-sm btn-outline-warning rounded-pill me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($modulo->activo)
                                <form method="POST" action="{{ route('owner.modules.destroy', $modulo) }}" class="d-inline" onsubmit="return confirm('¿Desactivar el módulo {{ $modulo->label }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Desactivar">
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
