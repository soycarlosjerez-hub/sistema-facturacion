<div class="ui-card-title">
    <i class="bi bi-people"></i>Clientes
</div>
<div class="ui-card-subtitle">El sistema utiliza "Consumidor Final" por defecto, pero puedes registrar tu primer cliente empresarial (opcional).</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-cliente">
    @csrf
    <input type="hidden" name="step" value="cliente">
    
    <div class="col-md-7">
        <label class="ui-label">Nombre o Razón Social <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input" placeholder="Ej. Empresa Comercial S.R.L." required maxlength="255">
    </div>
    
    <div class="col-md-5">
        <label class="ui-label">RNC o Cédula</label>
        <input type="text" name="rnc_cedula" class="ui-input" placeholder="Opcional" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Tipo de Cliente</label>
        <select name="tipo_cliente" class="ui-select">
            <option value="consumo">Consumo (Consumidor Final)</option>
            <option value="credito_fiscal" selected>Crédito Fiscal (B2B)</option>
            <option value="gubernamental">Gubernamental</option>
            <option value="especial">Regímenes Especiales</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="telefono" class="ui-input" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-cliente" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
