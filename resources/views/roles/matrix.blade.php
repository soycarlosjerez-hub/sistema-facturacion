@extends('layouts.app')

@section('title', 'Matriz de Permisos')

@php
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
@endphp

@include('roles._styles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="position: relative; z-index: 2;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-grid-3x3-gap me-1"></i>VISUALIZACIÓN
                </span>
            </div>
            <h2 class="fw-bold mb-1">Matriz de Permisos</h2>
            <p class="mb-0 opacity-75">Vista comparativa de todos los roles y sus permisos</p>
        </div>
        <div class="d-flex gap-2" style="position: relative; z-index: 2;">
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
            <a href="{{ route('roles.create') }}" class="btn btn-dark rounded-pill px-3 fw-bold">
                <i class="bi bi-plus-lg me-1"></i>Nuevo
            </a>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3 d-flex flex-wrap gap-3 align-items-center">
            <span class="d-flex align-items-center gap-2 small">
                <span class="perm-check on" style="display: inline-block; width: 22px; height: 22px; border-radius: 6px; background: rgba(34,197,94,0.15); color: #16a34a; text-align: center; line-height: 22px;">
                    <i class="bi bi-check-lg"></i>
                </span>
                <strong>Permitido</strong>
            </span>
            <span class="d-flex align-items-center gap-2 small">
                <span class="perm-check off" style="display: inline-block; width: 22px; height: 22px; border-radius: 6px; background: rgba(239,68,68,0.05); color: #cbd5e1; text-align: center; line-height: 22px;">
                    <i class="bi bi-x"></i>
                </span>
                <strong>No permitido</strong>
            </span>
            <div class="ms-auto small text-muted">
                <i class="bi bi-info-circle me-1"></i>Esta matriz es de solo lectura. Para editar, ve al detalle de cada rol.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table matrix-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 240px;">Permiso</th>
                            @foreach($roles as $rol)
                                @php $cfg = $rolConfig[$rol->name] ?? null; @endphp
                                <th class="text-center" style="min-width: 110px;">
                                    <a href="{{ route('roles.show', $rol) }}" class="text-decoration-none">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mb-1" style="width: 32px; height: 32px; background: {{ $cfg['gradient'] ?? '#64748b' }};">
                                                <i class="bi {{ $cfg['icon'] ?? 'bi-shield' }}"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.7rem; text-transform: uppercase;">{{ ucfirst($rol->name) }}</span>
                                        </div>
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modulos as $modulo => $perms)
                            <tr class="module-row">
                                <td colspan="{{ count($roles) + 1 }}" class="ps-4">
                                    <i class="bi bi-folder2-open me-2"></i>{{ ucfirst($modulo) }}
                                </td>
                            </tr>
                            @foreach($perms as $p)
                                <tr>
                                    <td class="ps-4 perm-name-cell">
                                        <code class="text-muted">{{ $p->name }}</code>
                                    </td>
                                    @foreach($roles as $rol)
                                        @php $has = $matrix[$rol->name]->has($p->name); @endphp
                                        <td class="perm-cell">
                                            <span class="perm-check {{ $has ? 'on' : 'off' }}">
                                                <i class="bi {{ $has ? 'bi-check-lg' : 'bi-x' }}"></i>
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
