@extends('layouts.app')
@section('title', 'Nuevo Tipo de Negocio')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tags text-success me-2"></i>Nuevo Tipo de Negocio</h2>
            <p class="text-muted mb-0">Crea un nuevo tipo de negocio con sus m&oacute;dulos disponibles</p>
        </div>
        <a href="{{ route('owner.business-types.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form method="POST" action="{{ route('owner.business-types.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informaci&oacute;n General</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: Restaurante">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control rounded-pill @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="Ej: restaurante">
                            @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="form-control rounded-4 @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripci&oacute;n del tipo de negocio">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Color Bootstrap</label>
                            <select name="color" class="form-select rounded-pill">
                                @foreach(['primary','secondary','success','danger','warning','info','dark'] as $c)
                                    <option value="{{ $c }}" {{ old('color', 'primary') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icon" class="form-control rounded-pill" value="{{ old('icon', 'bi-building') }}" placeholder="bi-building">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="activo">Activo</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Orden</label>
                                <input type="number" name="orden" class="form-control rounded-pill" value="{{ old('orden', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-puzzle text-success me-2"></i>M&oacute;dulos Disponibles</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="selectAll" class="btn btn-sm btn-outline-success rounded-pill"><i class="bi bi-check-all me-1"></i>Seleccionar todos</button>
                            <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="bi bi-x me-1"></i>Deseleccionar todos</button>
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
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <span id="selectedCount">0</span> de {{ $allModules->count() }} seleccionados
                            </span>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-2"></i>Crear Tipo de Negocio
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
