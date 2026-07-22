@extends('layouts.app')

@section('title', $usuario->name)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header-amber {
        background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2.5rem 2rem;
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
    .profile-avatar {
        width: 120px; height: 120px; border-radius: 28px;
        background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 3rem; font-weight: 800;
        border: 4px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .perm-section {
        background: rgba(15,23,42,0.03);
        border-radius: 12px; padding: 12px 14px; margin-bottom: 10px;
    }
    body.dark-mode .perm-section { background: rgba(15,23,42,0.3); }
    .perm-section-title {
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
        font-weight: 800; color: {{ $cfg['color'] ?? '#f59e0b' }};
        margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
    }
    .perm-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; font-size: 0.7rem; font-weight: 600; border-radius: 6px;
        background: rgba(56,189,248,0.1); color: #0284c7; margin: 2px;
    }
    .perm-pill.write { background: rgba(245,158,11,0.1); color: #d97706; }
    .perm-pill.delete { background: rgba(239,68,68,0.1); color: #dc2626; }
</style>
@endpush

@section('content')
@php
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
    $rolName = $usuario->roles->pluck('name')->first();
    $cfg = $rolConfig[$rolName] ?? null;
    $permisos = $usuario->getAllPermissions();
    $permGrouped = $permisos->groupBy(fn($p) => explode('.', $p->name)[0]);
    $isSelf = $usuario->id === auth()->id();
@endphp

<div class="container-fluid px-4 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="row align-items-center" style="position:relative; z-index:2;">
            <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                <div class="profile-avatar mx-auto mx-md-0">
                    {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $usuario->name)[1] ?? '', 0, 1)) }}
                </div>
            </div>
            <div class="col-md text-center text-md-start" style="position: relative; z-index: 2;">
                <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start mb-2 flex-wrap">
                    @if($cfg)
                        <span class="badge bg-white text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                            <i class="bi {{ $cfg['icon'] }} me-1" style="color: {{ $cfg['color'] }};"></i>{{ $cfg['label'] }}
                        </span>
                    @else
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">Sin rol</span>
                    @endif
                    @if($isSelf)
                        <span class="badge bg-success px-3 py-1 rounded-pill" style="font-size: 0.7rem;">TÚ</span>
                    @endif
                </div>
                <h2 class="fw-bold mb-1">{{ $usuario->name }}</h2>
                <p class="mb-0 opacity-90"><i class="bi bi-envelope me-1"></i>{{ $usuario->email }}</p>
            </div>
            <div class="col-md-auto mt-3 mt-md-0 text-md-end" style="position: relative; z-index: 2;">
                <div class="d-flex gap-2 justify-content-center justify-content-md-end flex-wrap">
                    <a href="{{ route('usuarios.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="ui-stat p-3" style="--delay:.1s">
                <div class="ui-stat-label"><i class="bi bi-shield-check me-1"></i>Permisos</div>
                <div class="ui-stat-value text-primary">{{ $permisos->count() }}</div>
                <small class="text-muted">de {{ Spatie\Permission\Models\Permission::count() }} totales</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat p-3" style="--delay:.15s">
                <div class="ui-stat-label"><i class="bi bi-diagram-3 me-1"></i>Módulos</div>
                <div class="ui-stat-value text-info">{{ $permGrouped->count() }}</div>
                <small class="text-muted">con acceso</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat p-3" style="--delay:.2s">
                <div class="ui-stat-label"><i class="bi bi-calendar-plus me-1"></i>Miembro desde</div>
                <div class="ui-stat-value text-success">{{ $usuario->created_at->format('d M Y') }}</div>
                <small class="text-muted">{{ $usuario->created_at->diffForHumans() }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat p-3" style="--delay:.25s">
                <div class="ui-stat-label"><i class="bi bi-clock-history me-1"></i>Última edición</div>
                <div class="ui-stat-value text-warning">{{ $usuario->updated_at->format('d M Y') }}</div>
                <small class="text-muted">{{ $usuario->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="premium-card-title d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-key icon-amber"></i> Permisos del Usuario
            </div>
            <div class="ui-input-group" style="max-width: 280px;">
                <span class="ui-input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" id="permFilter" class="ui-input border-0 bg-light" placeholder="Filtrar permisos...">
            </div>
        </div>
        <div class="premium-card-subtitle">Acciones permitidas a través del rol asignado</div>
        <div class="card-body p-4">
            @forelse($permGrouped as $modulo => $perms)
                <div class="perm-section perm-filterable" data-text="{{ strtolower($modulo) }}">
                    <div class="perm-section-title">
                        <i class="bi bi-folder2-open"></i>
                        {{ ucfirst($modulo) }} <span class="text-muted ms-1" style="font-size: 0.7rem;">({{ $perms->count() }})</span>
                    </div>
                    <div>
                        @foreach($perms as $p)
                            @php
                                $action = explode('.', $p->name)[1] ?? '';
                                $cls = in_array($action, ['delete','destroy','anular']) ? 'delete' : (in_array($action, ['create','store','update','edit','anular','abrir','cerrar']) ? 'write' : '');
                            @endphp
                            <span class="perm-pill {{ $cls }} perm-filterable" data-text="{{ strtolower($p->name) }}">
                                <i class="bi bi-check2"></i> {{ str_replace($module.'.', '', $p->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-shield-x display-4 d-block mb-2"></i>
                    Este usuario no tiene permisos asignados.
                </div>
            @endforelse
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