@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@push('styles')
@include('partials.premium-ui')
<style>
    .role-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1.5px solid transparent;
        color: #64748b;
        background: rgba(15,23,42,0.04);
        text-decoration: none;
        transition: all 0.2s;
    }
    .role-filter-pill:hover { background: rgba(15,23,42,0.08); color: #1e293b; transform: translateY(-1px); }
    .role-filter-pill.active { background: var(--accent-color, #38bdf8); color: white; border-color: transparent; }
    .role-filter-pill .count {
        background: rgba(255,255,255,0.3);
        border-radius: 999px;
        padding: 1px 8px;
        font-size: 0.7rem;
    }
    .role-filter-pill:not(.active) .count { background: rgba(15,23,42,0.1); }
    body.dark-mode .role-filter-pill { background: rgba(255,255,255,0.06); color: #94a3b8; }
    body.dark-mode .role-filter-pill:hover { background: rgba(255,255,255,0.1); color: #f1f5f9; }
    body.dark-mode .role-filter-pill:not(.active) .count { background: rgba(255,255,255,0.1); }

    .user-row { transition: background 0.15s; }
    .user-row:hover { background: rgba(245,158,11,0.05) !important; }

    .premium-header-amber {
        background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(245,158,11,.25);
    }
    .premium-header-amber::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-amber .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-amber .bubble:nth-child(1) {
        width: 80px; height: 80px; top: -20px; right: 10%;
        animation: premiumFloat 4s ease-in-out infinite;
    }
    .premium-header-amber .bubble:nth-child(2) {
        width: 50px; height: 50px; bottom: 10px; right: 28%;
        animation: premiumFloat 5s ease-in-out infinite 1s;
    }
    .premium-header-amber .bubble:nth-child(3) {
        width: 100px; height: 100px; bottom: -30px; right: 5%;
        animation: premiumFloat 6s ease-in-out infinite .5s;
    }

    .user-avatar {
        width: 56px; height: 56px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.4rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.10);
    }
    .role-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 999px;
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .user-action-btn {
        width: 32px; height: 32px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(15,23,42,0.04); color: #64748b; border: 0;
        transition: all 0.2s;
    }
    .user-action-btn:hover { transform: translateY(-1px); }
    .user-action-btn.view:hover { background: rgba(16,185,129,0.15); color: #059669; }
</style>
@endpush

@php
    $rolConfig = [
        'admin'    => ['color' => 'danger',  'icon' => 'bi-shield-lock-fill',   'label' => 'Admin',            'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'],
        'gerente'  => ['color' => 'warning', 'icon' => 'bi-person-badge-fill',  'label' => 'Gerente',          'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'],
        'vendedor' => ['color' => 'primary', 'icon' => 'bi-cart-check-fill',    'label' => 'Vendedor',         'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)'],
        'almacen'  => ['color' => 'success', 'icon' => 'bi-box-seam-fill',       'label' => 'Almacén',          'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)'],
        'contador' => ['color' => 'info',    'icon' => 'bi-calculator-fill',     'label' => 'Contador',         'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)'],
        'supervisor' => ['color' => 'purple', 'icon' => 'bi-eye-fill',          'label' => 'Supervisor',       'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)'],
        'administrativo' => ['color' => 'teal', 'icon' => 'bi-folder2-open',    'label' => 'Administrativo',   'gradient' => 'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)'],
        'mesero' => ['color' => 'orange',    'icon' => 'bi-person-fill',        'label' => 'Mesero',           'gradient' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)'],
        'cocinero' => ['color' => 'danger',  'icon' => 'bi-fire',               'label' => 'Cocinero',         'gradient' => 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'],
        'delivery' => ['color' => 'info',    'icon' => 'bi-truck',              'label' => 'Delivery',         'gradient' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)'],
        'bartender' => ['color' => 'purple', 'icon' => 'bi-cup-hot-fill',       'label' => 'Bartender',        'gradient' => 'linear-gradient(135deg, #a855f7 0%, #9333ea 100%)'],
        'lavador' => ['color' => 'cyan',     'icon' => 'bi-droplet-fill',       'label' => 'Lavador',          'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'],
        'recepcionista' => ['color' => 'indigo', 'icon' => 'bi-headset',        'label' => 'Recepcionista',    'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #4338ca 100%)'],
        'inspector' => ['color' => 'warning', 'icon' => 'bi-search',            'label' => 'Inspector',        'gradient' => 'linear-gradient(135deg, #eab308 0%, #ca8a04 100%)'],
        'cajero' => ['color' => 'success',   'icon' => 'bi-cash-register',      'label' => 'Cajero',           'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)'],
        'reponedor' => ['color' => 'orange', 'icon' => 'bi-boxes',              'label' => 'Reponedor',        'gradient' => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)'],
        'despachador' => ['color' => 'secondary', 'icon' => 'bi-truck',         'label' => 'Despachador',      'gradient' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)'],
        'vendedor-mayorista' => ['color' => 'primary', 'icon' => 'bi-people-fill', 'label' => 'Vend. Mayorista', 'gradient' => 'linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%)'],
        'consultor' => ['color' => 'secondary', 'icon' => 'bi-chat-dots-fill',  'label' => 'Consultor',        'gradient' => 'linear-gradient(135deg, #475569 0%, #334155 100%)'],
        'facturador' => ['color' => 'pink',   'icon' => 'bi-file-earmark-text-fill', 'label' => 'Facturador',  'gradient' => 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)'],
    ];
@endphp

@section('content')
<div class="container-fluid px-4 premium-page">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold">Gestión de Usuarios</h2>
                    <p class="mb-0 opacity-75">Administra el personal del sistema y sus niveles de acceso</p>
                </div>
            </div>
            <a href="{{ route('usuarios.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
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

    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total</div>
                        <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-danger bg-opacity-10 text-danger" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Admins</div>
                        <div class="stat-value text-danger">{{ $stats['admin'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-warning bg-opacity-10 text-warning" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Gerentes</div>
                        <div class="stat-value text-warning">{{ $stats['gerente'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-info bg-opacity-10 text-info" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-cart-check-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Vendedores</div>
                        <div class="stat-value text-info">{{ $stats['vendedor'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Almacén</div>
                        <div class="stat-value text-success">{{ $stats['almacen'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="premium-stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-secondary bg-opacity-10 text-secondary" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Contadores</div>
                        <div class="stat-value">{{ $stats['contador'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-accent amber"></div>
        <div class="card-body p-3">
            <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
                    <a href="{{ route('usuarios.index') }}" class="role-filter-pill {{ !request('rol') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Todos
                        <span class="count">{{ $stats['total'] }}</span>
                    </a>
                    @foreach($stats as $rolKey => $count)
                        @if(in_array($rolKey, ['total', 'sin_rol'])) @continue @endif
                        @if($count > 0 && isset($rolConfig[$rolKey]))
                            <a href="{{ route('usuarios.index', ['rol' => $rolKey] + request()->except('rol')) }}" class="role-filter-pill {{ request('rol') == $rolKey ? 'active' : '' }}" style="{{ request('rol') == $rolKey ? 'background: ' . $rolConfig[$rolKey]['gradient'] : '' }}">
                                <i class="bi {{ $rolConfig[$rolKey]['icon'] }}"></i> {{ $rolConfig[$rolKey]['label'] }}
                                <span class="count">{{ $count }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="input-group" style="max-width: 280px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="buscar" class="form-control border-0 bg-light" placeholder="Buscar por nombre o email..." value="{{ request('buscar') }}">
                    @if(request('buscar') || request('rol'))
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light border-0" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card overflow-hidden">
        <div class="card-accent amber"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,0.03);">
                    <tr style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th class="ps-4 py-3 text-muted fw-bold">Usuario</th>
                        <th class="py-3 text-muted fw-bold">Rol / Permisos</th>
                        <th class="py-3 text-muted fw-bold">Correo</th>
                        <th class="py-3 text-muted fw-bold">Sucursal</th>
                        <th class="py-3 text-muted fw-bold">Miembro desde</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                        @php
                            $rolName = $user->roles->pluck('name')->first();
                            $cfg = $rolConfig[$rolName] ?? null;
                            $rolPerms = $user->getAllPermissions()->count();
                        @endphp
                        <tr class="user-row">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="background: {{ $cfg['gradient'] ?? 'linear-gradient(135deg, #64748b 0%, #475569 100%)' }};">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">ID #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                @if($cfg)
                                    <span class="role-badge bg-{{ $cfg['color'] }} bg-opacity-10 text-{{ $cfg['color'] }}">
                                        <i class="bi {{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                                    </span>
                                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                        <i class="bi bi-key"></i> {{ $rolPerms }} {{ $rolPerms == 1 ? 'permiso' : 'permisos' }}
                                    </div>
                                @else
                                    <span class="role-badge bg-secondary bg-opacity-10 text-secondary">
                                        <i class="bi bi-question-circle"></i> Sin rol
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="small text-dark">{{ $user->email }}</div>
                            </td>
                            <td class="py-3">
                                @if($user->sucursal)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        <i class="bi bi-building me-1"></i>{{ $user->sucursal->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="small text-muted">{{ $user->created_at->format('d M, Y') }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $user->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <a href="{{ route('usuarios.show', $user->id) }}" class="user-action-btn view me-1" title="Ver perfil">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $user->id) }}" class="premium-btn-edit me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar al usuario &quot;{{ $user->name }}&quot;? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-people display-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2">No hay usuarios</h5>
                                <p class="text-muted mb-3">
                                    @if(request('buscar') || request('rol'))
                                        No se encontraron usuarios con los filtros aplicados.
                                    @else
                                        Comienza creando el primer usuario del sistema.
                                    @endif
                                </p>
                                <a href="{{ route('usuarios.create') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-person-plus me-1"></i>Crear Usuario
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($usuarios->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $usuarios->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection