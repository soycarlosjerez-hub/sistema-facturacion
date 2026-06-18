@extends('layouts.app')

@section('title', 'Editar Rol: ' . $role->name)

@php
    use Spatie\Permission\Models\Permission;
    $modulosIconos = [
        'dashboard' => 'bi-speedometer2', 'reportes' => 'bi-graph-up', 'ventas' => 'bi-cart-check',
        'cajas' => 'bi-cash-coin', 'clientes' => 'bi-people', 'cobros' => 'bi-credit-card',
        'productos' => 'bi-box-seam', 'compras' => 'bi-cart-plus', 'proveedores' => 'bi-truck',
        'almacenes' => 'bi-building', 'kardex' => 'bi-clipboard-data', 'ncf' => 'bi-receipt',
        'configuracion' => 'bi-gear', 'usuarios' => 'bi-shield-lock', 'roles' => 'bi-shield-shaded',
    ];
    $modulosColores = [
        'dashboard' => '#38bdf8', 'reportes' => '#a855f7', 'ventas' => '#22c55e', 'cajas' => '#f59e0b',
        'clientes' => '#ec4899', 'cobros' => '#06b6d4', 'productos' => '#3b82f6', 'compras' => '#f97316',
        'proveedores' => '#84cc16', 'almacenes' => '#10b981', 'kardex' => '#14b8a6', 'ncf' => '#ef4444',
        'configuracion' => '#64748b', 'usuarios' => '#6366f1', 'roles' => '#4f46e5',
    ];
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
    $cfg = $rolConfig[$role->name] ?? ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-shield', 'label' => ucfirst($role->name), 'desc' => 'Rol personalizado.'];
    $oldPerms = old('permissions', $permisosAsignados);
@endphp

@include('roles._styles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header gradiente (warning para edición) -->
    <div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3"
         style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); box-shadow: 0 10px 30px rgba(245,158,11,0.25);">
        <div class="d-flex align-items-center gap-3" style="position: relative; z-index: 2;">
            <div class="role-icon-lg" style="background: rgba(255,255,255,0.25); backdrop-filter: blur(10px);">
                <i class="bi {{ $cfg['icon'] }}"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-1"></i>EDITANDO
                    </span>
                    @if($isProtected)
                        <span class="protected-badge"><i class="bi bi-lock-fill"></i>Protegido</span>
                    @endif
                </div>
                <h2 class="fw-bold mb-0">{{ $role->name }}</h2>
                <p class="mb-0 opacity-90 small">{{ $permisosAsignados ? count($permisosAsignados) . ' permisos asignados' : 'Sin permisos' }}</p>
            </div>
        </div>
        <div class="d-flex gap-2" style="position: relative; z-index: 2;">
            <a href="{{ route($routePrefix . 'roles.show', $role) }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-eye me-1"></i>Ver
            </a>
            <a href="{{ route($routePrefix . 'roles.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    @if($isProtected)
        <div class="alert rounded-4 border-0 shadow-sm mb-3 d-flex align-items-center" style="background: rgba(245,158,11,0.1); border-left: 4px solid #f59e0b !important;">
            <i class="bi bi-shield-exclamation text-warning fs-4 me-3"></i>
            <div>
                <strong>Rol Protegido.</strong> El nombre y los permisos de <strong>{{ $role->name }}</strong> están protegidos para garantizar el acceso completo al sistema. Solo puedes renombrar el rol.
            </div>
        </div>
    @endif

    <form action="{{ route($routePrefix . 'roles.update', $role) }}" method="POST" id="roleForm">
        @csrf @method('PUT')

        <div class="row g-4">
            <!-- Columna izquierda: nombre del rol + plantillas -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-tag text-primary me-2"></i>Nombre del Rol</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-floating-modern">
                            <i class="bi bi-shield form-icon"></i>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $role->name) }}" placeholder=" " required maxlength="50" pattern="[a-z0-9_\-]+">
                            <label class="form-label-float" for="name">Nombre del rol</label>
                            @error('name')<div class="text-danger small mt-1 ms-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                        </div>
                        @if($role->users()->count() > 0)
                            <div class="alert alert-warning rounded-3 mt-3 mb-0 d-flex align-items-center gap-2 small">
                                <i class="bi bi-info-circle"></i>
                                <span>{{ $role->users()->count() }} usuario(s) usan este rol. El cambio de nombre los actualizará automáticamente.</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!$isProtected)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-stars text-primary me-2"></i>Plantillas Rápidas</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary text-start rounded-3 py-2 perm-template" data-template="readonly">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-eye fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Solo Lectura</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Solo permisos .view</small>
                                    </div>
                                </div>
                            </button>
                            <button type="button" class="btn btn-outline-success text-start rounded-3 py-2 perm-template" data-template="all">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-all fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Todos los permisos</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Acceso completo</small>
                                    </div>
                                </div>
                            </button>
                            <button type="button" class="btn btn-outline-warning text-start rounded-3 py-2 perm-template" data-template="clear">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-x-lg fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Limpiar selección</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Empezar desde cero</small>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 mt-3" style="background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(79,70,229,0.05));">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Permisos Seleccionados</div>
                        <div class="fs-1 fw-bold" id="permCount" style="color: #4f46e5;">{{ count($oldPerms) }}</div>
                        <small class="text-muted">de {{ Permission::count() }} disponibles</small>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: permission picker -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-key text-primary me-2"></i>Permisos</h5>
                            <small class="text-muted">Modifica los permisos asignados a este rol</small>
                        </div>
                        <div class="input-group" style="max-width: 280px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="permFilter" class="form-control border-0 bg-light" placeholder="Buscar permiso...">
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @foreach($modulos as $modulo => $perms)
                            <div class="perm-module-card mb-3 perm-filterable" data-text="{{ strtolower($modulo) }}" style="--accent-color: {{ $modulosColores[$modulo] ?? '#38bdf8' }};">
                                <div class="module-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="module-icon">
                                            <i class="bi {{ $modulosIconos[$modulo] ?? 'bi-folder' }}"></i>
                                        </div>
                                        <div class="module-title">{{ ucfirst($modulo) }}</div>
                                        <span class="badge bg-light text-muted ms-1">{{ $perms->count() }}</span>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input module-check" data-module="{{ $modulo }}" id="mod-{{ $modulo }}" {{ $isProtected ? 'disabled' : '' }}>
                                        <label class="form-check-label small fw-bold" for="mod-{{ $modulo }}">Todos</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    @foreach($perms as $p)
                                        @php
                                            $action = explode('.', $p->name)[1] ?? '';
                                            $checked = in_array($p->name, $oldPerms);
                                        @endphp
                                        <div class="col-md-6">
                                            <label class="perm-toggle {{ $checked ? 'is-checked' : '' }} perm-filterable" data-text="{{ strtolower($p->name) }} {{ strtolower($modulo) }}">
                                                <input type="checkbox" name="permissions[]" value="{{ $p->name }}"
                                                       data-module="{{ $modulo }}"
                                                       {{ $checked ? 'checked' : '' }}
                                                       {{ $isProtected ? 'disabled' : '' }}>
                                                <i class="bi {{ $checked ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                                                <span class="perm-name">{{ str_replace($modulo.'.', '', $p->name) }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $p->name }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4 flex-wrap gap-2">
            @if($role->name !== 'admin' && $role->users()->count() == 0)
                <form action="{{ route($routePrefix . 'roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Eliminar el rol &quot;{{ $role->name }}&quot;? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        <i class="bi bi-trash me-1"></i>Eliminar Rol
                    </button>
                </form>
            @else
                <div class="text-muted small">
                    @if($role->name === 'admin')
                        <i class="bi bi-shield-check text-success"></i> Rol del sistema, no se puede eliminar
                    @else
                        <i class="bi bi-info-circle"></i> {{ $role->users()->count() }} usuario(s) con este rol, no se puede eliminar
                    @endif
                </div>
            @endif
            <div class="d-flex gap-2">
                <a href="{{ route($routePrefix . 'roles.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

@if(!$isProtected)
<script>
    const updateCount = () => {
        const n = document.querySelectorAll('input[name="permissions[]"]:checked').length;
        document.getElementById('permCount').textContent = n;
    };

    const updateVisual = (checkbox) => {
        const toggle = checkbox.closest('.perm-toggle');
        const icon = toggle.querySelector('i');
        if (checkbox.checked) {
            toggle.classList.add('is-checked');
            icon.className = 'bi bi-check-circle-fill text-success';
        } else {
            toggle.classList.remove('is-checked');
            icon.className = 'bi bi-circle text-muted';
        }
        updateCount();
    };

    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            updateVisual(cb);
            const mod = cb.dataset.module;
            const allInModule = document.querySelectorAll(`input[data-module="${mod}"]`);
            const checked = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]:checked`);
            const modCheck = document.querySelector(`.module-check[data-module="${mod}"]`);
            if (modCheck) modCheck.checked = allInModule.length === checked.length;
        });
    });

    document.querySelectorAll('.module-check').forEach(mc => {
        mc.addEventListener('change', () => {
            const mod = mc.dataset.module;
            const cbs = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]`);
            cbs.forEach(cb => {
                cb.checked = mc.checked;
                updateVisual(cb);
            });
        });
    });

    document.querySelectorAll('.perm-template').forEach(btn => {
        btn.addEventListener('click', () => {
            const t = btn.dataset.template;
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                if (t === 'all') {
                    cb.checked = true;
                } else if (t === 'clear') {
                    cb.checked = false;
                } else if (t === 'readonly') {
                    const action = cb.value.split('.')[1] || '';
                    cb.checked = action === 'view' || action === 'view.own' || action === 'view.report';
                }
                updateVisual(cb);
            });
            document.querySelectorAll('.module-check').forEach(mc => {
                const mod = mc.dataset.module;
                const all = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]`);
                const checked = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]:checked`);
                mc.checked = all.length === checked.length;
            });
        });
    });

    document.getElementById('permFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.perm-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>

<!-- Sticky Bottom Save Bar -->
<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" id="saveBarLeft">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Editar Rol</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="roleForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>

<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 0px);
        right: 0;
        background: #fff;
        border-top: 2px solid var(--bs-primary, #0d6efd);
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #38bdf8;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endif
@endsection
