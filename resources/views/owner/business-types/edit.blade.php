@extends('layouts.app')
@section('title', 'Editar Tipo de Negocio')

@push('styles')
<style>
.module-check {
    border: 1px solid rgba(0,0,0,0.06);
    background-color: #f8f9fa;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    position: relative;
}
.module-check:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    border-color: rgba(25, 135, 84, 0.15) !important;
}
.module-check.is-checked {
    background-color: rgba(25, 135, 84, 0.08) !important;
    border-color: rgba(25, 135, 84, 0.25) !important;
}
.module-check.is-checked i {
    color: #198754 !important;
}
.module-check .form-check-input {
    margin-top: 0;
    border-radius: 4px;
    width: 1.15em;
    height: 1.15em;
    cursor: pointer;
    border: 1.5px solid #ced4da;
}
.module-check .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
.category-section {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.04);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.01);
}
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #22c55e;
    padding: 0.75rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}
.sticky-save-bar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #4ade80;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tags text-primary me-2"></i>Editar Tipo de Negocio</h2>
            <p class="text-muted mb-0">{{ $businessType->nombre }}</p>
        </div>
        <a href="{{ route('owner.business-types.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form method="POST" action="{{ route('owner.business-types.update', $businessType) }}" id="instanceForm">
        @csrf @method('PUT')
        
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body p-4 pt-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre</label>
                            <input type="text" name="nombre" class="form-control rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre', $businessType->nombre) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Descripción</label>
                            <textarea name="descripcion" class="form-control rounded-4 @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $businessType->descripcion) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Color Bootstrap</label>
                            <select name="color" class="form-select rounded-pill">
                                @foreach(['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('color', $businessType->color) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="form-control rounded-pill" value="{{ old('icon', $businessType->icon) }}" placeholder="bi-building">
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check form-switch pt-2">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $businessType->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activo</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Orden</label>
                                <input type="number" name="orden" class="form-control rounded-pill" value="{{ old('orden', $businessType->orden) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-puzzle text-success me-2"></i>Módulos Disponibles</h5>
                    </div>
                    <div class="card-body p-4 pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="selectAll" class="btn btn-sm btn-outline-success rounded-pill"><i class="bi bi-check-all me-1"></i>Seleccionar todos</button>
                            <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="bi bi-x me-1"></i>Deseleccionar todos</button>
                        </div>

                        @php
                            $selectedKeys = $businessType->modules->where('visible', true)->pluck('modulo_key')->toArray();
                        @endphp

                        <div class="modules-container">
                            @foreach($modulesByCategory as $categoria => $modules)
                                @php
                                    $catInfo = $categoryLabels[$categoria] ?? ['label' => ucfirst($categoria), 'icon' => 'bi-puzzle'];
                                    $selectedInCat = $modules->whereIn('key', $selectedKeys)->count();
                                    $totalInCat = $modules->count();
                                @endphp
                                <div class="category-section mb-4" id="cat_section_{{ $categoria }}">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi {{ $catInfo['icon'] }} text-primary fs-5"></i>
                                            <span class="fw-semibold fs-6">{{ $catInfo['label'] }}</span>
                                            <span class="badge {{ $selectedInCat > 0 ? 'bg-success' : 'bg-secondary' }} ms-2 category-badge" id="badge_{{ $categoria }}">{{ $selectedInCat }} / {{ $totalInCat }}</span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill select-category" data-category="{{ $categoria }}" title="Seleccionar todos">
                                                <i class="bi bi-check-all"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill deselect-category" data-category="{{ $categoria }}" title="Deseleccionar todos">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        @foreach($modules as $module)
                                            @php
                                                $current = $businessType->modules->where('modulo_key', $module->key)->first();
                                                $visible = $current ? $current->visible : false;
                                            @endphp
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                                                <div class="form-check module-check rounded-3 p-2 h-100 {{ $visible ? 'is-checked' : '' }}" style="cursor:pointer; min-height: 80px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; text-align: center;" data-category="{{ $categoria }}">
                                                    <label class="d-flex align-items-center justify-content-center w-100 h-100 m-0" style="cursor:pointer;">
                                                        <input type="checkbox" name="modules[]" value="{{ $module->key }}" class="form-check-input module-input me-2" id="mod_{{ $module->key }}" {{ $visible ? 'checked' : '' }}>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="bi {{ $module->icon ?? 'bi-puzzle' }} fs-5 text-muted"></i>
                                                            <span class="fw-medium small text-wrap text-dark">{{ $module->label ?? $module->key }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <span id="selectedCount">{{ $businessType->modules->where('visible', true)->count() }}</span> de {{ $modulesByCategory->flatten()->count() }} seleccionados
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="sticky-save-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando tipo de negocio: {{ $businessType->nombre }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('owner.business-types.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Todo
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCount() {
    const count = document.querySelectorAll('.module-input:checked').length;
    const total = document.querySelectorAll('.module-input').length;
    const selectedCountEl = document.getElementById('selectedCount');
    if (selectedCountEl) {
        selectedCountEl.textContent = count;
    }
    
    // Update category counts
    document.querySelectorAll('.category-section').forEach(section => {
        const cat = section.id.replace('cat_section_', '');
        const checked = section.querySelectorAll('.module-input:checked').length;
        const total = section.querySelectorAll('.module-input').length;
        const badge = document.getElementById(`badge_${cat}`);
        if (badge) {
            badge.textContent = `${checked} / ${total}`;
            if (checked > 0) {
                badge.classList.remove('bg-secondary');
                badge.classList.add('bg-success');
            } else {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-secondary');
            }
        }
    });
}

// Handle module checkbox changes (triggered by label click)
document.addEventListener('change', function(e) {
    if (e.target.matches('.module-input')) {
        const card = e.target.closest('.module-check');
        if (card) {
            if (e.target.checked) {
                card.classList.add('is-checked');
            } else {
                card.classList.remove('is-checked');
            }
            updateCount();
        }
    }
});

document.getElementById('selectAll')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelectorAll('.module-input').forEach(i => {
        i.checked = true;
        const parent = i.closest('.module-check');
        if (parent) {
            parent.classList.add('is-checked');
        }
    });
    updateCount();
});

document.getElementById('deselectAll')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelectorAll('.module-input').forEach(i => {
        i.checked = false;
        const parent = i.closest('.module-check');
        if (parent) {
            parent.classList.remove('is-checked');
        }
    });
    updateCount();
});

// Category select/deselect
document.querySelectorAll('.select-category').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const cat = this.dataset.category;
        document.querySelectorAll(`.module-check[data-category="${cat}"] .module-input`).forEach(i => {
            i.checked = true;
            const parent = i.closest('.module-check');
            if (parent) {
                parent.classList.add('is-checked');
            }
        });
        updateCount();
    });
});

document.querySelectorAll('.deselect-category').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const cat = this.dataset.category;
        document.querySelectorAll(`.module-check[data-category="${cat}"] .module-input`).forEach(i => {
            i.checked = false;
            const parent = i.closest('.module-check');
            if (parent) {
                parent.classList.remove('is-checked');
            }
        });
        updateCount();
    });
});

// Keyboard support for module cards
document.addEventListener('keydown', function(e) {
    if (e.target.matches('.module-check') && (e.key === 'Enter' || e.key === ' ')) {
        e.preventDefault();
        const input = e.target.querySelector('.module-input');
        if (input) {
            input.checked = !input.checked;
            input.dispatchEvent(new Event('change'));
        }
    }
});

// Initialize count on load
document.addEventListener('DOMContentLoaded', function() {
    updateCount();
});
</script>
@endpush