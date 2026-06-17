@extends('layouts.app')

@section('title', 'Manejador de Roles')

@php
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',   'label' => 'Admin',        'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill',  'label' => 'Gerente',      'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',   'label' => 'Vendedor',     'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',      'label' => 'Almacén',      'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',    'label' => 'Contador',     'desc' => 'Reportes y consulta fiscal.'],
        'supervisor' => ['color' => '#8b5cf6', 'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'icon' => 'bi-eye-fill',       'label' => 'Supervisor',   'desc' => 'Supervisión y anulaciones.'],
        'administrativo' => ['color' => '#14b8a6', 'gradient' => 'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)', 'icon' => 'bi-folder2-open', 'label' => 'Administrativo', 'desc' => 'Clientes, cobros, compras.'],
        'mesero' => ['color' => '#f97316', 'gradient' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', 'icon' => 'bi-person-fill',         'label' => 'Mesero',       'desc' => 'Toma pedidos y comandas.'],
        'cocinero' => ['color' => '#dc2626', 'gradient' => 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)', 'icon' => 'bi-fire',            'label' => 'Cocinero',     'desc' => 'KDS y comandas de cocina.'],
        'delivery' => ['color' => '#0ea5e9', 'gradient' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)', 'icon' => 'bi-truck',            'label' => 'Delivery',     'desc' => 'Pedidos para entrega.'],
        'bartender' => ['color' => '#a855f7', 'gradient' => 'linear-gradient(135deg, #a855f7 0%, #9333ea 100%)', 'icon' => 'bi-cup-hot-fill',    'label' => 'Bartender',    'desc' => 'Barra y comandas de bebidas.'],
        'lavador' => ['color' => '#06b6d4', 'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)', 'icon' => 'bi-droplet-fill',       'label' => 'Lavador',      'desc' => 'Servicios y vehículos.'],
        'recepcionista' => ['color' => '#4f46e5', 'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #4338ca 100%)', 'icon' => 'bi-headset',     'label' => 'Recepcionista', 'desc' => 'Citas y recepción de vehículos.'],
        'inspector' => ['color' => '#eab308', 'gradient' => 'linear-gradient(135deg, #eab308 0%, #ca8a04 100%)', 'icon' => 'bi-search',          'label' => 'Inspector',    'desc' => 'Inspección de vehículos.'],
        'cajero' => ['color' => '#10b981', 'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'icon' => 'bi-cash-register',       'label' => 'Cajero',       'desc' => 'POS y cobro en terminal.'],
        'reponedor' => ['color' => '#d97706', 'gradient' => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)', 'icon' => 'bi-boxes',           'label' => 'Reponedor',    'desc' => 'Inventario y estantes.'],
        'despachador' => ['color' => '#64748b', 'gradient' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)', 'icon' => 'bi-truck',          'label' => 'Despachador',  'desc' => 'Conduces y despachos.'],
        'vendedor-mayorista' => ['color' => '#1e40af', 'gradient' => 'linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%)', 'icon' => 'bi-people-fill', 'label' => 'Vend. Mayorista', 'desc' => 'Cotizaciones y listas precio.'],
        'consultor' => ['color' => '#475569', 'gradient' => 'linear-gradient(135deg, #475569 0%, #334155 100%)', 'icon' => 'bi-chat-dots-fill',   'label' => 'Consultor',    'desc' => 'Cotizaciones y proyectos.'],
        'facturador' => ['color' => '#ec4899', 'gradient' => 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)', 'icon' => 'bi-file-earmark-text-fill', 'label' => 'Facturador', 'desc' => 'Facturación NCF y cobros.'],
    ];
    $defaultCfg = ['color' => '#64748b', 'gradient' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)', 'icon' => 'bi-shield', 'label' => '', 'desc' => 'Rol personalizado.'];
    $protected = ['admin'];
@endphp

@include('roles._styles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-shield-shaded text-primary me-2"></i>Manejador de Roles</h2>
            <p class="text-muted mb-0">Define roles y asigna permisos granulares a cada uno</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($routePrefix . 'roles.matrix') }}" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-grid-3x3-gap me-1"></i>Matriz
            </a>
            <a href="{{ route($routePrefix . 'roles.create') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Rol
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="role-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-shield-shaded"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Roles</div>
                        <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="role-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-info bg-opacity-10 text-info">
                        <i class="bi bi-key"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Permisos</div>
                        <div class="fs-3 fw-bold">{{ $stats['permisos'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="role-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Usuarios</div>
                        <div class="fs-3 fw-bold">{{ $stats['usuarios'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="role-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Con Usuarios</div>
                        <div class="fs-3 fw-bold">{{ $stats['con_usuarios'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles grid -->
    <div class="row g-3">
        @forelse($roles as $rol)
            @php
                $cfg = $rolConfig[$rol->name] ?? $defaultCfg;
                $pct = $stats['permisos'] > 0 ? round(($rol->permissions_count / $stats['permisos']) * 100) : 0;
                $esProtegido = in_array($rol->name, $protected);
            @endphp
            <div class="col-xl-4 col-md-6">
                <div class="role-big-card {{ $esProtegido ? 'protected' : '' }}"
                     style="--role-color: {{ $cfg['color'] }}; --role-gradient: {{ $cfg['gradient'] }};">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="role-icon-lg" style="background: {{ $cfg['gradient'] }};">
                            <i class="bi {{ $cfg['icon'] }}"></i>
                        </div>
                        @if($esProtegido)
                            <span class="protected-badge"><i class="bi bi-lock-fill"></i>Sistema</span>
                        @endif
                    </div>
                    <div class="role-name text-dark">{{ $cfg['label'] ?: ucfirst($rol->name) }}</div>
                    <p class="text-muted small mb-3">{{ $cfg['desc'] }}</p>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                            <i class="bi bi-key"></i> {{ $rol->permissions_count }} / {{ $stats['permisos'] }} permisos
                        </small>
                        <small class="fw-bold" style="color: {{ $cfg['color'] }};">{{ $pct }}%</small>
                    </div>
                    <div class="perm-bar">
                        <div class="perm-bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill text-muted"></i>
                            <span class="fw-bold text-dark">{{ $rol->users_count }}</span>
                            <small class="text-muted">{{ $rol->users_count == 1 ? 'usuario' : 'usuarios' }}</small>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route($routePrefix . 'roles.show', $rol) }}" class="role-action-btn view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route($routePrefix . 'roles.edit', $rol) }}" class="role-action-btn edit" title="Editar permisos">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($rol->name !== 'admin' && $rol->users_count == 0)
                                <form action="{{ route($routePrefix . 'roles.destroy', $rol) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el rol &quot;{{ $rol->name }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="role-action-btn delete" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-shield display-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-bold">No hay roles definidos</h5>
                        <p class="text-muted">Crea tu primer rol para empezar a gestionar permisos.</p>
                        <a href="{{ route($routePrefix . 'roles.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-plus-lg me-1"></i>Crear Rol
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
