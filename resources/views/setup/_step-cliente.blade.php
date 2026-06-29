<h5 class="fw-bold mb-2"><i class="bi bi-people me-2 text-primary"></i>Clientes</h5>
<p class="text-muted small mb-4">El sistema utiliza "Consumidor Final" por defecto, pero puedes registrar tu primer cliente empresarial (opcional).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-cliente">
    @csrf
    <input type="hidden" name="step" value="cliente">
    
    <div class="col-md-7">
        <label class="form-label small fw-bold">Nombre o Razón Social <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej. Empresa Comercial S.R.L." required maxlength="255">
    </div>
    
    <div class="col-md-5">
        <label class="form-label small fw-bold">RNC o Cédula</label>
        <input type="text" name="rnc_cedula" class="form-control rounded-3" placeholder="Opcional" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Tipo de Cliente</label>
        <select name="tipo_cliente" class="form-select rounded-3">
            <option value="consumo">Consumo (Consumidor Final)</option>
            <option value="credito_fiscal" selected>Crédito Fiscal (B2B)</option>
            <option value="gubernamental">Gubernamental</option>
            <option value="especial">Regímenes Especiales</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="text" name="telefono" class="form-control rounded-3" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-cliente">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>

<div class="d-flex justify-content-start mt-3">
    <form action="{{ route('setup.skip') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="cliente">
        <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
            <i class="bi bi-arrow-right me-1"></i> Omitir paso
        </button>
    </form>
</div>
