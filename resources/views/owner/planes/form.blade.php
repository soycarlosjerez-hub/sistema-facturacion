@extends('layouts.app')
@section('title', isset($plan) ? 'Editar Plan' : 'Nuevo Plan')

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
                    <i class="bi bi-card-checklist"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">{{ isset($plan) ? 'Editar Plan' : 'Nuevo Plan' }}</h3>
                    <p class="mb-0 opacity-75">{{ isset($plan) ? $plan->nombre : 'Configura los límites y precios del plan' }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.plans.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-4">
            <form method="POST" action="{{ isset($plan) ? route('owner.plans.update', $plan) : route('owner.plans.store') }}" id="planForm">
                @csrf
                @isset($plan) @method('PUT') @endisset
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input rounded-pill @error('nombre') is-invalid @enderror" value="{{ old('nombre', $plan->nombre ?? '') }}" required placeholder="Ej: Profesional">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="ui-input rounded-pill @error('slug') is-invalid @enderror" value="{{ old('slug', $plan->slug ?? '') }}" required placeholder="profesional">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Descripci&oacute;n</label>
                        <input type="text" name="descripcion" class="ui-input rounded-pill @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $plan->descripcion ?? '') }}" placeholder="Ideal para PYMES">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Mensual <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="precio_mensual" class="ui-input rounded-end-pill @error('precio_mensual') is-invalid @enderror" value="{{ old('precio_mensual', $plan->precio_mensual ?? '') }}" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Implementaci&oacute;n</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="precio_implementacion" class="ui-input rounded-end-pill" value="{{ old('precio_implementacion', $plan->precio_implementacion ?? '') }}" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Lanzamiento (Oferta)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="precio_lanzamiento" class="ui-input rounded-end-pill" value="{{ old('precio_lanzamiento', $plan->precio_lanzamiento ?? '') }}" step="0.01" min="0">
                        </div>
                        <small class="text-muted">Implementaci&oacute;n + primer mes desde RD$ 7,500</small>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">M&aacute;x. Usuarios</label>
                        <input type="number" name="max_usuarios" class="ui-input rounded-pill" value="{{ old('max_usuarios', $plan->max_usuarios ?? '') }}" min="0" placeholder="Vacío = ilimitado">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">M&aacute;x. Sucursales</label>
                        <input type="number" name="max_sucursales" class="ui-input rounded-pill" value="{{ old('max_sucursales', $plan->max_sucursales ?? '') }}" min="0" placeholder="Vacío = ilimitado">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">M&aacute;x. Empresas</label>
                        <input type="number" name="max_empresas" class="ui-input rounded-pill" value="{{ old('max_empresas', $plan->max_empresas ?? '') }}" min="0" placeholder="Vacío = ilimitado">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Orden</label>
                        <input type="number" name="orden" class="ui-input rounded-pill" value="{{ old('orden', $plan->orden ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-8 d-flex align-items-end gap-4 pb-1">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $plan->activo ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="activo">Activo</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="recomendado" class="form-check-input" value="1" id="recomendado" {{ old('recomendado', $plan->recomendado ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="recomendado">Recomendado</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Selector de Tipo de Negocio para auto-seleccionar módulos -->
                <div class="mb-4">
                    <label class="ui-label fw-bold">Auto-seleccionar m&oacute;dulos por Tipo de Negocio</label>
                    <select id="businessTypeSelector" class="ui-select rounded-pill" aria-label="Seleccionar tipo de negocio">
                        <option value="">— Seleccionar tipo para auto-llenar m&oacute;dulos —</option>
                        @foreach($businessTypes as $bt)
                            <option value="{{ $bt->id }}" {{ (isset($preSelectedBusinessType) && $preSelectedBusinessType == $bt->id) ? 'selected' : '' }}>{{ $bt->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Selecciona un tipo para auto-marcar sus m&oacute;dulos. Puedes ajustar despu&eacute;s manualmente.</small>
                </div>

                <h5 class="fw-bold mb-3"><i class="bi bi-check2-circle me-2"></i>Features (incluidos en el plan)</h5>
                <div class="mb-2">
                    <button type="button" class="ui-btn ui-btn-ghost btn-sm" onclick="addFeature()"><i class="bi bi-plus-lg me-1"></i>Agregar feature</button>
                </div>
                <div id="featuresContainer" class="d-flex flex-column gap-2 mb-4">
                    @php $features = old('features', $plan->features ?? []); @endphp
                    @forelse($features as $feature)
                        <div class="input-group">
                            <input type="text" name="features[]" class="ui-input rounded-pill" value="{{ $feature }}">
                            <button type="button" class="ui-btn ui-btn-danger btn-sm ms-2 rounded-pill" onclick="this.closest('.input-group').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @empty
                        <div class="input-group">
                            <input type="text" name="features[]" class="ui-input rounded-pill" placeholder="Ej: Facturación + e-CF/DGII">
                            <button type="button" class="ui-btn ui-btn-danger btn-sm ms-2 rounded-pill" onclick="this.closest('.input-group').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforelse
                </div>

                <h5 class="fw-bold mb-3"><i class="bi bi-grid me-2"></i>M&oacute;dulos permitidos</h5>
                <small class="text-muted d-block mb-2">Si no marcas ninguno, el plan permite todos los m&oacute;dulos del tipo de negocio.</small>
                <div class="row g-2 mb-4">
                    @php $planModulos = old('modulos', $plan->modulos ?? []); @endphp
                    @foreach($modulos->groupBy('categoria') as $categoria => $modulosCat)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-4 h-100 bg-light bg-opacity-50">
                                <div class="fw-bold small text-uppercase text-muted mb-2">{{ $categoria }}</div>
                                @foreach($modulosCat as $modulo)
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="modulos[]" class="form-check-input" value="{{ $modulo->key }}" id="mod_{{ $modulo->key }}" {{ in_array($modulo->key, $planModulos) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="mod_{{ $modulo->key }}">{{ $modulo->label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ isset($plan) ? 'Editando plan' : 'Creando plan' }}</span>
        <button type="submit" form="planForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:#8b5cf6;border-color:#8b5cf6;color:#fff;">
            <i class="bi bi-save me-2"></i>Guardar Plan
        </button>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
function addFeature() {
    const div = document.createElement('div');
    div.className = 'input-group';
    div.innerHTML = '<input type="text" name="features[]" class="ui-input rounded-pill" placeholder="Ej: Reportes avanzados">' +
                    '<button type="button" class="ui-btn ui-btn-danger btn-sm ms-2 rounded-pill" onclick="this.closest(\'.input-group\').remove()"><i class="bi bi-x-lg"></i></button>';
    document.getElementById('featuresContainer').appendChild(div);
}

document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('businessTypeSelector');
    if (!selector) return;

    selector.addEventListener('change', async function() {
        const typeId = this.value;
        const checkboxes = document.querySelectorAll('input[name="modulos[]"]');
        
        // Desmarcar todos primero
        checkboxes.forEach(cb => cb.checked = false);
        
        if (!typeId) return;
        
        try {
            const resp = await fetch(`/business-types/${typeId}/modules-data`);
            const data = await resp.json();
            const modulos = data.modulos || [];
            
            // Marcar los que coincidan
            checkboxes.forEach(cb => {
                if (modulos.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        } catch (e) {
            console.error('Error cargando m&oacute;dulos:', e);
        }
    });
});
</script>
@endpush
