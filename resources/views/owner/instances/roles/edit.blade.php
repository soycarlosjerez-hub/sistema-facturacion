@extends('layouts.app')
@section('title', "Editar Rol - {$role->name}")

@php
    $categoriaIconos = [
        'core' => 'bi-box-seam', 'operaciones' => 'bi-cart-check', 'clientes' => 'bi-people',
        'organizacion' => 'bi-building', 'lavadero' => 'bi-droplet',
        'restaurante' => 'bi-cup-straw', 'reportes' => 'bi-graph-up',
        'sistema' => 'bi-gear', 'configuracion' => 'bi-sliders',
    ];
    $categoriaColores = [
        'core' => '#3b82f6', 'operaciones' => '#22c55e', 'clientes' => '#ec4899',
        'organizacion' => '#10b981', 'lavadero' => '#06b6d4',
        'restaurante' => '#f97316', 'reportes' => '#a855f7',
        'sistema' => '#64748b', 'configuracion' => '#6366f1',
    ];
    $selectedMods = old('modulos', $selectedModulos);
@endphp

@push('styles')
@include('partials.premium-ui')
@include('roles._styles')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-shield"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            <i class="bi bi-pencil-square me-1"></i>EDITANDO
                        </span>
                    </div>
                    <h2 class="fw-bold mb-0">{{ $role->name }}</h2>
                    <p class="mb-0 opacity-90 small">{{ count($selectedMods) }} módulos asignados</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.instances.roles', $instance) }}" class="btn btn-light rounded-pill px-3 text-dark">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('owner.instances.roles.update', [$instance, $role]) }}" method="POST" id="roleForm">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="premium-card mb-3">
                    <div class="card-accent purple"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-tag text-primary me-2"></i>Nombre del Rol</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-floating-modern">
                            <i class="bi bi-shield form-icon"></i>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $role->name) }}" placeholder=" " required>
                            <label class="form-label-float" for="name">Nombre del rol</label>
                            @error('name')<div class="text-danger small mt-1 ms-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                        </div>
                        @if($role->users()->count() > 0)
                            <div class="alert alert-warning rounded-3 mt-3 mb-0 d-flex align-items-center gap-2 small">
                                <i class="bi bi-info-circle"></i>
                                <span>{{ $role->users()->count() }} usuario(s) usan este rol.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent green"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-stars text-primary me-2"></i>Acciones Rápidas</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success text-start rounded-3 py-2 mod-template" data-template="all">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-all fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Todos los módulos</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Acceso completo</small>
                                    </div>
                                </div>
                            </button>
                            <button type="button" class="btn btn-outline-warning text-start rounded-3 py-2 mod-template" data-template="clear">
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

                <div class="premium-card mt-3" style="background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(79,70,229,0.05));">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Módulos Seleccionados</div>
                        <div class="fs-1 fw-bold" id="modCount" style="color: #4f46e5;">{{ count($selectedMods) }}</div>
                        <small class="text-muted">de {{ $totalModulos }} disponibles</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent blue"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-grid text-primary me-2"></i>Módulos</h5>
                            <small class="text-muted">Selecciona los módulos que los usuarios con este rol podrán ver</small>
                        </div>
                        <div class="input-group" style="max-width: 280px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="modFilter" class="form-control border-0 bg-light" placeholder="Buscar módulo...">
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @foreach($modulos as $categoria => $modulosCategoria)
                            <div class="perm-module-card mb-3 mod-filterable" data-text="{{ strtolower($categoria) }}" style="--accent-color: {{ $categoriaColores[$categoria] ?? '#38bdf8' }};">
                                <div class="module-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="module-icon">
                                            <i class="bi {{ $categoriaIconos[$categoria] ?? 'bi-folder' }}"></i>
                                        </div>
                                        <div class="module-title">{{ ucfirst($categoria) }}</div>
                                        <span class="badge bg-light text-muted ms-1">{{ $modulosCategoria->count() }}</span>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input module-check" data-module="{{ $categoria }}" id="cat-{{ $categoria }}">
                                        <label class="form-check-label small fw-bold" for="cat-{{ $categoria }}">Todos</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    @foreach($modulosCategoria as $modulo)
                                        @php $checked = in_array($modulo->key, $selectedMods); @endphp
                                        <div class="col-md-6">
                                            <label class="perm-toggle {{ $checked ? 'is-checked' : '' }} mod-filterable" data-text="{{ strtolower($modulo->key) }} {{ strtolower($modulo->label) }} {{ strtolower($categoria) }}">
                                                <input type="checkbox" name="modulos[]" value="{{ $modulo->key }}"
                                                       data-module="{{ $categoria }}"
                                                       {{ $checked ? 'checked' : '' }}>
                                                <i class="bi {{ $checked ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                                                <span class="perm-name">{{ $modulo->label }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $modulo->key }}</small>
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

    </form>

    @if($role->users()->count() == 0)
    <form action="{{ route('owner.instances.roles.destroy', [$instance, $role]) }}" method="POST" onsubmit="return confirm('¿Eliminar el rol &quot;{{ $role->name }}&quot;? Esta acción no se puede deshacer.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
            <i class="bi bi-trash me-1"></i>Eliminar Rol
        </button>
    </form>
    @else
    <div class="text-muted small mt-4 mb-4">
        <i class="bi bi-info-circle"></i> {{ $role->users()->count() }} usuario(s) con este rol, no se puede eliminar
    </div>
    @endif
</div>

<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" id="saveBarLeft">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando Rol: {{ $role->name }}</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="roleForm" class="btn btn-save rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
</div>

<script>
    const updateCount = () => {
        document.getElementById('modCount').textContent = document.querySelectorAll('input[name="modulos[]"]:checked').length;
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

    document.querySelectorAll('input[name="modulos[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            updateVisual(cb);
            const mod = cb.dataset.module;
            const all = document.querySelectorAll(`input[name="modulos[]"][data-module="${mod}"]`);
            const checked = document.querySelectorAll(`input[name="modulos[]"][data-module="${mod}"]:checked`);
            const modCheck = document.querySelector(`.module-check[data-module="${mod}"]`);
            if (modCheck) modCheck.checked = all.length === checked.length;
        });
        if (cb.checked) updateVisual(cb);
    });

    document.querySelectorAll('.module-check').forEach(mc => {
        mc.addEventListener('change', () => {
            document.querySelectorAll(`input[name="modulos[]"][data-module="${mc.dataset.module}"]`).forEach(cb => {
                cb.checked = mc.checked;
                updateVisual(cb);
            });
        });
    });

    document.querySelectorAll('.mod-template').forEach(btn => {
        btn.addEventListener('click', () => {
            const t = btn.dataset.template;
            document.querySelectorAll('input[name="modulos[]"]').forEach(cb => {
                cb.checked = t === 'all';
                updateVisual(cb);
            });
            document.querySelectorAll('.module-check').forEach(mc => {
                const mod = mc.dataset.module;
                const all = document.querySelectorAll(`input[name="modulos[]"][data-module="${mod}"]`);
                const checked = document.querySelectorAll(`input[name="modulos[]"][data-module="${mod}"]:checked`);
                mc.checked = all.length === checked.length;
            });
        });
    });

    document.getElementById('modFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.mod-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
