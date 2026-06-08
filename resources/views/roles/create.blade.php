@extends('layouts.app')

@section('title', 'Crear Rol')

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
    $modulosColores = [
        'dashboard' => '#38bdf8', 'reportes' => '#a855f7', 'ventas' => '#22c55e', 'cajas' => '#f59e0b',
        'clientes' => '#ec4899', 'cobros' => '#06b6d4', 'productos' => '#3b82f6', 'compras' => '#f97316',
        'proveedores' => '#84cc16', 'almacenes' => '#10b981', 'kardex' => '#14b8a6', 'ncf' => '#ef4444',
        'configuracion' => '#64748b', 'usuarios' => '#6366f1', 'roles' => '#4f46e5',
    ];
    $oldPerms = old('permissions', []);
@endphp

@include('roles._styles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header gradiente -->
    <div class="page-header-gradient d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="position: relative; z-index: 2;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-plus me-1"></i>NUEVO ROL
                </span>
            </div>
            <h2 class="fw-bold mb-1">Crear Rol</h2>
            <p class="mb-0 opacity-75">Define un nuevo rol y selecciona los permisos que tendrá</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-4 fw-bold" style="position: relative; z-index: 2;">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
        @csrf

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
                                   value="{{ old('name') }}" placeholder=" " required maxlength="50" pattern="[a-z0-9_\-]+">
                            <label class="form-label-float" for="name">Nombre (ej: supervisor, cajero)</label>
                            @error('name')<div class="text-danger small mt-1 ms-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                            <small class="text-muted d-block mt-1 ms-1" style="font-size: 0.7rem;">
                                <i class="bi bi-info-circle me-1"></i>Solo minúsculas, números, guiones.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-stars text-primary me-2"></i>Plantillas Rápidas</h6>
                        <small class="text-muted">Inicia con permisos predefinidos</small>
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
                                        <small class="text-muted" style="font-size: 0.7rem;">Acceso completo al sistema</small>
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

                <!-- Contador -->
                <div class="card border-0 shadow-sm rounded-4 mt-3" style="background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(79,70,229,0.05));">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Permisos Seleccionados</div>
                        <div class="fs-1 fw-bold" id="permCount" style="color: #4f46e5;">0</div>
                        <small class="text-muted">de {{ Permission::count() }} disponibles</small>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: permission picker -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-key text-primary me-2"></i>Asignar Permisos</h5>
                            <small class="text-muted">Marca los permisos que tendrá este rol</small>
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
                                        <input type="checkbox" class="form-check-input module-check" data-module="{{ $modulo }}" id="mod-{{ $modulo }}">
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
                                                       {{ $checked ? 'checked' : '' }}>
                                                <i class="bi {{ $checked ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                                                <span class="perm-name">{{ str_replace($modulo.'.', '', $p->name) }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $p->name }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-4">
                                <i class="bi bi-x-lg me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: 0;">
                                <i class="bi bi-check-lg me-1"></i>Crear Rol
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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

    // Perm toggle change
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

    // Module "select all" toggle
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

    // Templates
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

    // Filtro
    document.getElementById('permFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.perm-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });

    updateCount();
</script>
@endsection
