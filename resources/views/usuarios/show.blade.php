@extends('layouts.app')

@section('title', $usuario->name)

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

<style>
    .profile-hero {
        background: {{ $cfg['gradient'] ?? 'linear-gradient(135deg, #64748b 0%, #475569 100%)' }};
        border-radius: 24px;
        padding: 2.5rem 2rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden;
    }
    .profile-hero::after {
        content: "";
        position: absolute;
        right: -100px; top: -100px;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    }
    .profile-avatar {
        width: 120px; height: 120px;
        border-radius: 28px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 800;
        border: 4px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        border: 1px solid rgba(15,23,42,0.06);
        box-shadow: 0 4px 12px rgba(15,23,42,0.04);
        height: 100%;
    }
    body.dark-mode .info-card { background: rgba(30,41,59,0.95); border-color: rgba(255,255,255,0.05); }
    .perm-section {
        background: rgba(15,23,42,0.03);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 10px;
    }
    body.dark-mode .perm-section { background: rgba(15,23,42,0.3); }
    .perm-section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 800;
        color: {{ $cfg['color'] ?? '#38bdf8' }};
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .perm-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        font-size: 0.7rem;
        font-weight: 600;
        border-radius: 6px;
        background: rgba(56,189,248,0.1);
        color: #0284c7;
        margin: 2px;
    }
    .perm-pill.write { background: rgba(245,158,11,0.1); color: #d97706; }
    .perm-pill.delete { background: rgba(239,68,68,0.1); color: #dc2626; }
</style>

<div class="container-fluid px-4">
    <!-- Hero / Profile header -->
    <div class="profile-hero position-relative">
        <div class="row align-items-center">
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
                    <a href="{{ route('usuarios.index') }}" class="btn btn-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-dark rounded-pill px-3 fw-bold">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="info-card">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-check me-1"></i>Permisos
                </div>
                <div class="fs-3 fw-bold">{{ $permisos->count() }}</div>
                <small class="text-muted">de {{ Spatie\Permission\Models\Permission::count() }} totales</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="info-card">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <i class="bi bi-diagram-3 me-1"></i>Módulos
                </div>
                <div class="fs-3 fw-bold">{{ $permGrouped->count() }}</div>
                <small class="text-muted">con acceso</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="info-card">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <i class="bi bi-calendar-plus me-1"></i>Miembro desde
                </div>
                <div class="fs-5 fw-bold">{{ $usuario->created_at->format('d M Y') }}</div>
                <small class="text-muted">{{ $usuario->created_at->diffForHumans() }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="info-card">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <i class="bi bi-clock-history me-1"></i>Última edición
                </div>
                <div class="fs-5 fw-bold">{{ $usuario->updated_at->format('d M Y') }}</div>
                <small class="text-muted">{{ $usuario->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <!-- Permisos detallados -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-key text-primary me-2"></i>Permisos del Usuario</h5>
                <small class="text-muted">Acciones permitidas a través del rol asignado</small>
            </div>
            <div class="input-group" style="max-width: 280px;">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" id="permFilter" class="form-control border-0 bg-light" placeholder="Filtrar permisos...">
            </div>
        </div>
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
                                <i class="bi bi-check2"></i> {{ str_replace($modulo.'.', '', $p->name) }}
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
