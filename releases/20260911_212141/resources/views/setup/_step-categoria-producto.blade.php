<div class="ui-card-title">
    <i class="bi bi-tags"></i>Categoría de Productos
</div>
<div class="ui-card-subtitle">Crea una categoría para organizar tu inventario de productos.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-categoria-producto">
    @csrf
    <input type="hidden" name="step" value="categoria-producto">
    <div class="col-md-8">
        <label class="ui-label">Nombre de la Categoría <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input"
               placeholder="Ej. Bebidas, Alimentos, Electrónicos" required maxlength="255">
    </div>
    <div class="col-md-4">
        <label class="ui-label">Color</label>
        <input type="color" name="color" class="ui-input w-100"
               value="#3b82f6" title="Color de la categoría">
    </div>
    <div class="col-12">
        <label class="ui-label">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
        <input type="text" name="descripcion" class="ui-input"
               placeholder="Breve descripción de esta categoría">
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-categoria-producto" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
