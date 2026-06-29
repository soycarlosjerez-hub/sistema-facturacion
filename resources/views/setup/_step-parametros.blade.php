<h5 class="fw-bold mb-1"><i class="bi bi-gear text-primary me-2"></i>Parámetros del Sistema</h5>
<p class="text-muted small mb-4">Configura la información básica de la empresa y los parámetros predeterminados para las operaciones.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-4">
    @csrf
    <input type="hidden" name="step" value="parametros">

    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-building form-icon"></i>
            <input type="text" name="empresa_nombre" id="empresa_nombre" class="form-control" placeholder=" " value="Mi Negocio" required>
            <label class="form-label-float" for="empresa_nombre">Nombre Comercial / Empresa *</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-telephone form-icon"></i>
            <input type="text" name="empresa_telefono" id="empresa_telefono" class="form-control" placeholder=" ">
            <label class="form-label-float" for="empresa_telefono">Teléfono</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-currency-dollar form-icon"></i>
            <input type="text" name="moneda_simbolo" id="moneda_simbolo" class="form-control" placeholder=" " value="RD$" required>
            <label class="form-label-float" for="moneda_simbolo">Símbolo de Moneda *</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-percent form-icon"></i>
            <input type="number" step="0.01" name="impuesto_itbis" id="impuesto_itbis" class="form-control" placeholder=" " value="18" required>
            <label class="form-label-float" for="impuesto_itbis">ITBIS por defecto (%) *</label>
        </div>
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
