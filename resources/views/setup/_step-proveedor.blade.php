<h5 class="fw-bold mb-2"><i class="bi bi-truck me-2 text-primary"></i>Proveedores</h5>
<p class="text-muted small mb-4">Si gestionarás compras de inventario, necesitas registrar al menos un proveedor.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-proveedor">
    @csrf
    <input type="hidden" name="step" value="proveedor">
    
    <div class="col-md-7">
        <label class="form-label small fw-bold">Nombre del Proveedor <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej. Distribuidora Central" required maxlength="255">
    </div>
    
    <div class="col-md-5">
        <label class="form-label small fw-bold">RNC</label>
        <input type="text" name="rnc" class="form-control rounded-3" placeholder="Opcional" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="text" name="telefono" class="form-control rounded-3" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Email</label>
        <input type="email" name="email" class="form-control rounded-3" placeholder="Opcional" maxlength="255">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-proveedor">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>

<div class="d-flex justify-content-start mt-3">
    <form action="{{ route('setup.skip') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="proveedor">
        <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
            <i class="bi bi-arrow-right me-1"></i> Omitir paso
        </button>
    </form>
</div>
