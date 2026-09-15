<div class="ui-card-title">
    <i class="bi bi-truck"></i>Proveedores
</div>
<div class="ui-card-subtitle">Si gestionarás compras de inventario, necesitas registrar al menos un proveedor.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-proveedor">
    @csrf
    <input type="hidden" name="step" value="proveedor">
    
    <div class="col-md-7">
        <label class="ui-label">Nombre del Proveedor <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input" placeholder="Ej. Distribuidora Central" required maxlength="255">
    </div>
    
    <div class="col-md-5">
        <label class="ui-label">RNC</label>
        <input type="text" name="rnc" class="ui-input" placeholder="Opcional" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="telefono" class="ui-input" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Email</label>
        <input type="email" name="email" class="ui-input" placeholder="Opcional" maxlength="255">
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-proveedor" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
