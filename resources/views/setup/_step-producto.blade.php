<h5 class="fw-bold mb-2"><i class="bi bi-box-seam me-2 text-primary"></i>Productos</h5>
<p class="text-muted small mb-4">Agrega al menos un producto para poder facturar.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-producto">
    @csrf
    <input type="hidden" name="step" value="producto">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre del Producto</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Producto de ejemplo" required>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">Precio de Venta</label>
        <input type="number" step="0.01" name="precio" class="form-control rounded-3" placeholder="100.00" required>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">ITBIS</label>
        <select name="itbis_porcentaje" class="form-select rounded-3" required>
            <option value="18">18%</option>
            <option value="0">0% (Exento)</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">Stock Inicial</label>
        <input type="number" name="stock" class="form-control rounded-3" placeholder="0">
    </div>
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-producto">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>

<div class="d-flex justify-content-start mt-3">
    <form action="{{ route('setup.skip') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="producto">
        <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
            <i class="bi bi-arrow-right me-1"></i> Omitir paso
        </button>
    </form>
</div>
