<div class="ui-card-title">
    <i class="bi bi-box-seam"></i>Productos
</div>
<div class="ui-card-subtitle">Agrega al menos un producto para poder facturar.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-producto">
    @csrf
    <input type="hidden" name="step" value="producto">
    <div class="col-md-6">
        <label class="ui-label">Nombre del Producto <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input" placeholder="Producto de ejemplo" required>
    </div>
    <div class="col-md-3">
        <label class="ui-label">Precio de Venta</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="precio" class="ui-input" placeholder="100.00" required>
        </div>
    </div>
    <div class="col-md-3">
        <label class="ui-label">ITBIS</label>
        <select name="itbis_porcentaje" class="ui-select" required>
            <option value="18">18%</option>
            <option value="0">0% (Exento)</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="ui-label">Stock Inicial</label>
        <input type="number" name="stock" class="ui-input" placeholder="0">
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-producto" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
