@extends('layouts.app')
@section('title', 'Editar Tipo de Negocio')

@push('styles')
@include('partials.premium-ui')
<style>
.module-check {
    border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
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
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Tipo de Negocio</h2>
                    <p class="mb-0 opacity-75">{{ $businessType->nombre }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.business-types.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('owner.business-types.update', $businessType) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="ui-card h-100" style="--delay:.1s">
                    <div class="ui-card-accent" style="background:#8b5cf6"></div>
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informaci&oacute;n General</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="ui-input rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre', $businessType->nombre) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="ui-input rounded-4 @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $businessType->descripcion) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Color Bootstrap</label>
                            <select name="color" class="ui-select rounded-pill">
                                @foreach(['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('color', $businessType->color) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="ui-input rounded-pill" value="{{ old('icon', $businessType->icon) }}" placeholder="bi-building">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $businessType->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activo</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="ui-label fw-bold">Orden</label>
                                <input type="number" name="orden" class="ui-input rounded-pill" value="{{ old('orden', $businessType->orden) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ui-card h-100" style="--delay:.15s">
                    <div class="ui-card-accent" style="background:#10b981"></div>
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-puzzle text-success me-2"></i>M&oacute;dulos Disponibles</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @php $selectedKeys = $businessType->modules->where('visible', true)->pluck('modulo_key')->toArray(); @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="selectAll" class="ui-btn ui-btn-solid btn-sm rounded-pill" style="background:#10b981;border-color:#10b981"><i class="bi bi-check-all me-1"></i>Seleccionar todos</button>
                            <button type="button" id="deselectAll" class="ui-btn ui-btn-ghost btn-sm rounded-pill"><i class="bi bi-x me-1"></i>Deseleccionar todos</button>
                        </div>

                        <div class="row g-2">
                            @foreach($allModules as $module)
                            @php $checked = in_array($module->key, $selectedKeys); @endphp
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check module-check rounded-3 p-2 {{ $checked ? 'is-checked' : 'bg-light' }}" style="cursor:pointer;" onclick="toggleModule(this)">
                                    <input type="checkbox" name="modules[]" value="{{ $module->key }}" class="form-check-input module-input" id="mod_{{ $module->key }}" {{ $checked ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-medium" for="mod_{{ $module->key }}">
                                        <i class="bi bi-puzzle me-1 text-muted"></i>{{ $module->label ?? $module->key }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <span id="selectedCount">{{ count($selectedKeys) }}</span> de {{ $allModules->count() }} seleccionados
                            </span>
                            <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold" style="background:#10b981;border-color:#10b981">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleModule(el) {
    const input = el.querySelector('.module-input');
    input.checked = !input.checked;
    if (input.checked) {
        el.classList.add('is-checked');
        el.classList.remove('bg-light');
    } else {
        el.classList.remove('is-checked');
        el.classList.add('bg-light');
    }
    updateCount();
}
document.getElementById('selectAll')?.addEventListener('click', function() {
    document.querySelectorAll('.module-input').forEach(i => {
        i.checked = true;
        const parent = i.closest('.module-check');
        if (parent) {
            parent.classList.add('is-checked');
            parent.classList.remove('bg-light');
        }
    });
    updateCount();
});
document.getElementById('deselectAll')?.addEventListener('click', function() {
    document.querySelectorAll('.module-input').forEach(i => {
        i.checked = false;
        const parent = i.closest('.module-check');
        if (parent) {
            parent.classList.remove('is-checked');
            parent.classList.add('bg-light');
        }
    });
    updateCount();
});
function updateCount() {
    const count = document.querySelectorAll('.module-input:checked').length;
    document.getElementById('selectedCount').textContent = count;
}
document.addEventListener('DOMContentLoaded', updateCount);
</script>
@endpush
