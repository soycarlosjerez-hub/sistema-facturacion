<h5 class="fw-bold mb-2"><i class="bi bi-tags me-2 text-primary"></i>Categoría de Productos</h5>
<p class="text-muted small mb-4">Crea una categoría para organizar tu inventario de productos.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-categoria-producto">
    @csrf
    <input type="hidden" name="step" value="categoria-producto">
    <div class="col-md-8">
        <label class="form-label small fw-bold">Nombre de la Categoría <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control rounded-3"
               placeholder="Ej. Bebidas, Alimentos, Electrónicos" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Color</label>
        <input type="color" name="color" class="form-control form-control-color rounded-3 w-100"
               value="#3b82f6" title="Color de la categoría">
    </div>
    <div class="col-12">
        <label class="form-label small fw-bold">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
        <input type="text" name="descripcion" class="form-control rounded-3"
               placeholder="Breve descripción de esta categoría">
    </div>
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-categoria-producto">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
