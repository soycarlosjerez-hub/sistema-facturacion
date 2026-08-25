@extends('layouts.app')

@section('title', 'Nuevo Paquete')

@push('styles')
@include('partials.premium-ui')
<style>
.paquete-item-row { background: rgba(6,182,212,0.04); border-radius: 0.75rem; padding: 1rem; border: 1px solid rgba(6,182,212,0.1); margin-bottom: 0.75rem; }
.paquete-item-type-badge { font-size: 0.7rem; }
.paquete-total-preview { background: rgba(34,197,94,0.06); border-radius: 1rem; padding: 1.5rem; text-align: center; border: 2px solid rgba(34,197,94,0.15); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-gift"></i></div>
                <div>
                    <h4 class="ui-header-title">Nuevo Paquete</h4>
                    <div class="ui-header-meta"><i class="bi bi-box-seam me-1"></i><span>Configurar servicio y precios</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero.paquetes.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form id="paquete-form" action="{{ route('lavadero.paquetes.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-0" style="color:#06b6d4;"><i class="bi bi-box-seam me-2"></i>Información Básica</h6>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Nombre del Paquete <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="ui-input" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label">Descripción</label>
                            <textarea name="descripcion" class="ui-textarea" rows="2">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label">Tipo de Vehículo</label>
                                <select name="aplicable_a_tipo" class="ui-select">
                                    <option value="todos" {{ old('aplicable_a_tipo', 'todos') == 'todos' ? 'selected' : '' }}>Todos</option>
                                    <option value="sedan" {{ old('aplicable_a_tipo') == 'sedan' ? 'selected' : '' }}>Sedán / Auto Pequeño</option>
                                    <option value="suv" {{ old('aplicable_a_tipo') == 'suv' ? 'selected' : '' }}>SUV</option>
                                    <option value="pickup" {{ old('aplicable_a_tipo') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                    <option value="van" {{ old('aplicable_a_tipo') == 'van' ? 'selected' : '' }}>Van / Minivan</option>
                                    <option value="moto" {{ old('aplicable_a_tipo') == 'moto' ? 'selected' : '' }}>Moto / Scooter</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Duración (minutos)</label>
                                <input type="number" name="duracion_minutos" class="ui-input" value="{{ old('duracion_minutos') }}" min="5">
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,0.05);">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" checked role="switch" style="width:3em;height:1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="chk-activo">Paquete Activo</label>
                                </div>
                                <small class="text-muted">Si está inactivo no aparecerá en el POS.</small>
                            </div>
                        </div>

                        <div class="mb-4 pb-3 border-bottom mt-4">
                            <h6 class="fw-bold mb-0" style="color:#059669;"><i class="bi bi-list-check me-2"></i>Items del Paquete</h6>
                        </div>
                        <div id="paquete-items-container">
                            <div class="text-center py-3 text-muted"><small>Agrega servicios al paquete</small></div>
                        </div>
                        <button type="button" class="ui-btn ui-btn-ghost btn-sm rounded-pill mt-3" onclick="addItem()">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Item
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body p-4">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-0" style="color:#f59e0b;"><i class="bi bi-currency-dollar me-2"></i>Precios</h6>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Precio del Paquete</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" name="precio" id="paquete-precio" class="ui-input" value="{{ old('precio') }}" step="0.01" oninput="calcTotal()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="ui-label">Precio Anterior (opcional)</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" name="precio_anterior" class="ui-input" value="{{ old('precio_anterior') }}" step="0.01">
                            </div>
                        </div>

                        <div class="paquete-total-preview">
                            <small class="text-muted d-block">Total calculado de items</small>
                            <div class="fs-3 fw-bold text-success" id="paquete-total-preview">RD$ 0.00</div>
                            <small class="text-muted" id="paquete-diff"></small>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-0" style="color:#8b5cf6;"><i class="bi bi-tags me-2"></i>Tags</h6>
                            <input type="text" name="tags[]" class="ui-input mt-2" placeholder="premium, popular, nuevo">
                            <small class="text-muted">Separados por coma</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('lavadero.paquetes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="paquete-form" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Crear
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
let itemCounter = 0;
const servicios = @json($servicios);
const productos = @json($productos);

function addItem() {
    const idx = ++itemCounter;
    const servicesOptions = servicios.map(s => `<option value="${s.id}">${escapeHtml(s.nombre)}</option>`).join('');
    const productsOptions = productos.map(p => `<option value="${p.id}">${escapeHtml(p.nombre)}</option>`).join('');

    const html = `<div class="paquete-item-row">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="ui-label small">Tipo</label>
                <select name="items[${idx}][tipo]" class="ui-select ui-select-sm" onchange="toggleItemField(this)">
                    <option value="servicio">Servicio</option>
                    <option value="producto">Producto</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="ui-label small">Servicio</label>
                <select name="items[${idx}][servicio_id]" class="form-select form-select-sm item-servicio-field">
                    <option value="">Seleccionar...</option>
                    ${servicesOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label class="ui-label small">Producto</label>
                <select name="items[${idx}][producto_id]" class="form-select form-select-sm item-producto-field" style="display:none;">
                    <option value="">Seleccionar...</option>
                    ${productsOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="ui-label small">Cantidad</label>
                <input type="number" name="items[${idx}][cantidad]" class="ui-input ui-input-sm" value="1" min="1" step="0.5">
            </div>
            <div class="col-md-3">
                <label class="ui-label small">Precio Ind.</label>
                <input type="number" name="items[${idx}][precio_individual]" class="ui-input ui-input-sm" value="" step="0.01">
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <button type="button" class="btn btn-sm btn-danger rounded-pill" onclick="this.closest('.paquete-item-row').remove(); calcTotal();"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>`;
    document.getElementById('paquete-items-container').insertAdjacentHTML('beforeend', html);
}

function toggleItemField(sel) {
    const row = sel.closest('.paquete-item-row');
    const isServicio = sel.value === 'servicio';
    row.querySelector('.item-servicio-field').style.display = isServicio ? '' : 'none';
    row.querySelector('.item-producto-field').style.display = isServicio ? 'none' : '';
    if (!isServicio) row.querySelector('.item-servicio-field').value = '';
    else row.querySelector('.item-producto-field').value = '';
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('#paquete-items-container .paquete-item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('[name*="[cantidad]"]')?.value) || 0;
        const price = parseFloat(row.querySelector('[name*="[precio_individual]"]')?.value) || 0;
        total += qty * price;
    });
    document.getElementById('paquete-total-preview').textContent = 'RD$ ' + total.toFixed(2);
    const pkgPrice = parseFloat(document.getElementById('paquete-precio')?.value) || 0;
    const diff = pkgPrice - total;
    document.getElementById('paquete-diff').textContent = pkgPrice > 0 ? (diff >= 0 ? 'Ahorro: RD$ ' + diff.toFixed(2) : 'Excede: RD$ ' + Math.abs(diff).toFixed(2)) : '';
}
calcTotal();
</script>
@endsection
