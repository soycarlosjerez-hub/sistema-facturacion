@extends('layouts.app')
@section('title', 'Nuevo Tipo de Negocio')

@push('styles')
@include('partials.premium-ui')
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
                    <h4 class="fw-bold mb-1">Nuevo Tipo de Negocio</h4>
                    <small class="opacity-75"><i class="bi bi-plus-circle me-1"></i>Crea un nuevo tipo de negocio con sus m&oacute;dulos disponibles</small>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.business-types.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('owner.business-types.store') }}" id="instanceForm">
        @csrf
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
                            <input type="text" name="nombre" class="ui-input rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: Restaurante">
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="ui-input rounded-pill @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="Ej: restaurante">
                            @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="ui-input rounded-4 @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripci&oacute;n del tipo de negocio">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Color Bootstrap</label>
                            <select name="color" class="ui-select rounded-pill">
                                @foreach(['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('color', 'primary') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label fw-bold">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="ui-input rounded-pill" value="{{ old('icon', 'bi-building') }}" placeholder="bi-building">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activo</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="ui-label fw-bold">Orden</label>
                                <input type="number" name="orden" class="ui-input rounded-pill" value="{{ old('orden', 0) }}" min="0">
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="selectAll" class="ui-btn ui-btn-solid btn-sm rounded-pill" style="background:#10b981;border-color:#10b981"><i class="bi bi-check-all me-1"></i>Seleccionar todos</button>
                            <button type="button" id="deselectAll" class="ui-btn ui-btn-ghost btn-sm rounded-pill"><i class="bi bi-x me-1"></i>Deseleccionar todos</button>
                        </div>

                        <div class="row g-2">
                            @foreach($allModules as $module)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check module-check rounded-3 p-2 bg-light" style="cursor:pointer;" onclick="toggleModule(this)">
                                    <input type="checkbox" name="modules[]" value="{{ $module->key }}" class="form-check-input module-input" id="mod_{{ $module->key }}">
                                    <label class="form-check-label small fw-medium" for="mod_{{ $module->key }}">
                                        <i class="bi bi-puzzle me-1 text-muted"></i>{{ $module->label ?? $module->key }}
                                    </label>
                                </div>
                            </div>
                            @endforeach

                        <hr>
                        <div>
                            <span class="text-muted small">
                                <span id="selectedCount">0</span> de {{ $allModules->count() }} seleccionados
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div style="height: 80px;"></div>
</div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando Tipo de Negocio</span>
        </div>
        <div>
            <a href="{{ route('owner.business-types.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleModule(el) {
    const input = el.querySelector('.module-input');
    input.checked = !input.checked;
    if (input.checked) {
        el.classList.add('bg-success', 'bg-opacity-10', 'border', 'border-success', 'border-opacity-25');
        el.classList.remove('bg-light');
    } else {
        el.classList.remove('bg-success', 'bg-opacity-10', 'border', 'border-success', 'border-opacity-25');
        el.classList.add('bg-light');
    }
    updateCount();
}
document.getElementById('selectAll')?.addEventListener('click', function() {
    document.querySelectorAll('.module-input').forEach(i => {
        i.checked = true;
        const parent = i.closest('.module-check');
        if (parent) {
            parent.classList.add('bg-success', 'bg-opacity-10', 'border', 'border-success', 'border-opacity-25');
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
            parent.classList.remove('bg-success', 'bg-opacity-10', 'border', 'border-success', 'border-opacity-25');
            parent.classList.add('bg-light');
        }
    });
    updateCount();
});
function updateCount() {
    const count = document.querySelectorAll('.module-input:checked').length;
    const total = document.querySelectorAll('.module-input').length;
    document.getElementById('selectedCount').textContent = count;
}
</script>
@endpush
