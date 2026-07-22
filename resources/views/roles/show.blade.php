@extends('layouts.app')

@section('title', 'Rol: ' . $role->name)

@php
    use Spatie\Permission\Models\Permission;
    $modulosIconos = [
        'dashboard' => 'bi-speedometer2',
        'reportes'  => 'bi-graph-up',
        'ventas'    => 'bi-cart-check',
        'cajas'     => 'bi-cash-coin',
        'clientes'  => 'bi-people',
        'cobros'    => 'bi-credit-card',
        'productos' => 'bi-box-seam',
        'compras'   => 'bi-cart-plus',
        'proveedores' => 'bi-truck',
        'almacenes' => 'bi-building',
        'kardex'    => 'bi-clipboard-data',
        'ncf'       => 'bi-receipt',
        'configuracion' => 'bi-gear',
        'usuarios'  => 'bi-shield-lock',
        'roles'     => 'bi-shield-shaded',
    ];
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',     'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',   'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor',  'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',   'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador',  'desc' => 'Reportes y consulta fiscal.'],
    ];
    $cfg = $rolConfig[$role->name] ?? ['color' => '#64748b', 'gradient' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)', 'icon' => 'bi-shield', 'label' => ucfirst($role->name), 'desc' => 'Rol personalizado.'];
    $totalPerms = Permission::count();
    $pct = $totalPerms > 0 ? round(($role->permissions->count() / $totalPerms) * 100) : 0;
@endphp

@include('roles._styles')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .role-big-card { background: rgba(30,41,59,.95); }
    body.dark-mode .role-big-card .role-name { color: #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:{{ $cfg['color'] }};--accent-rgb:{{ implode(',', sscanf($cfg['color'], '#%02x%02x%02x')) }};--accent-hover:{{ $cfg['color'] }};">
    <div class="ui-header" style="background: linear-gradient(135deg, {{ $cfg['color'] }}, {{ $cfg['color'] }}cc, {{ $cfg['color'] }}99, {{ $cfg['color'] }});">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi {{ $cfg['icon'] }}"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            ROL
                        </span>
                        @if($isProtected)
                            <span class="protected-badge"><i class="bi bi-lock-fill"></i>Protegido</span>
                        @endif
                    </div>
                    <h2 class="ui-header-title mb-0">{{ $cfg['label'] }}</h2>
                    <div class="ui-header-meta">{{ $cfg['desc'] }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route($routePrefix . 'roles.index') }}" class="ui-btn ui-btn-primary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
                <a href="{{ route($routePrefix . 'roles.edit', $role) }}" class="ui-btn ui-btn-ghost rounded-pill px-3 fw-bold">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble" style="background: {{ $cfg['color'] }}20; color: {{ $cfg['color'] }};">
                        <i class="bi bi-key"></i>
                    </div>
                    <div>
                        <div class="stat-label">Permisos</div>
                        <div class="stat-value" style="color: {{ $cfg['color'] }};">{{ $role->permissions->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-info bg-opacity-10 text-info">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div class="stat-label">Cobertura</div>
                        <div class="stat-value">{{ $pct }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-label">Usuarios</div>
                        <div class="stat-value text-success">{{ $users->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-folder"></i>
                    </div>
                    <div>
                        <div class="stat-label">Módulos</div>
                        <div class="stat-value">{{ $permisosGrouped->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="ui-card-title"><i class="bi bi-key icon-purple"></i>Permisos Asignados</h5>
                            <small class="ui-card-subtitle">Acciones permitidas para los usuarios con este rol</small>
                        </div>
                        <div class="ui-input-group" style="max-width: 280px;">
                            <span class="ui-input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="permFilter" class="ui-input border-0 bg-light" placeholder="Filtrar permisos...">
                        </div>
                    </div>
                    @forelse($permisosGrouped as $modulo => $perms)
                        <div class="perm-module-card mb-3" data-text="{{ strtolower($modulo) }}" style="--accent-color: {{ $cfg['color'] }};">
                            <div class="module-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="module-icon">
                                        <i class="bi {{ $modulosIconos[$modulo] ?? 'bi-folder' }}"></i>
                                    </div>
                                    <div class="module-title">{{ ucfirst($modulo) }}</div>
                                </div>
                                <span class="badge bg-light text-dark">{{ $perms->count() }}</span>
                            </div>
                            <div class="row g-2">
                                @foreach($perms as $p)
                                    @php
                                        $action = explode('.', $p->name)[1] ?? '';
                                        $cls = in_array($action, ['delete','destroy','anular']) ? 'delete' : (in_array($action, ['create','store','update','edit','anular','abrir','cerrar']) ? 'write' : '');
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="perm-toggle is-checked perm-filterable" data-text="{{ strtolower($p->name) }}">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span class="perm-name">{{ str_replace($modulo.'.', '', $p->name) }}</span>
                                            <span class="badge bg-light text-muted" style="font-size: 0.65rem;">{{ $cls ?: 'read' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x display-4 d-block mb-2"></i>
                            <p class="mb-0">Este rol no tiene permisos asignados.</p>
                            <a href="{{ route($routePrefix . 'roles.edit', $role) }}" class="ui-btn ui-btn-solid rounded-pill px-4 mt-3">
                                <i class="bi bi-pencil me-1"></i>Asignar Permisos
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="ui-card-title"><i class="bi bi-people icon-purple"></i>Usuarios Asignados</h5>
                    <small class="ui-card-subtitle">{{ $users->count() }} {{ $users->count() == 1 ? 'persona' : 'personas' }} con este rol</small>
                    @forelse($users as $user)
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3">
                            <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 12px; background: {{ $cfg['gradient'] }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            @if(!request()->routeIs('owner.*'))
                            <a href="{{ route('usuarios.show', $user->id) }}" class="text-decoration-none">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person-x display-6 d-block mb-2"></i>
                            <p class="mb-0">Ningún usuario con este rol todavía.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('permFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.perm-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>
@endsection