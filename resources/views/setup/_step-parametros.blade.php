<div class="ui-card-title">
    <i class="bi bi-gear"></i>Parámetros del Sistema
</div>
<div class="ui-card-subtitle">Configura la información básica de la empresa y los parámetros predeterminados.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-4">
    @csrf
    <input type="hidden" name="step" value="parametros">

    <div class="col-md-6">
        <label class="ui-label">Nombre Comercial / Empresa <span class="text-danger">*</span></label>
        <input type="text" name="empresa_nombre" id="empresa_nombre" class="ui-input" placeholder="Mi Negocio" value="Mi Negocio" required>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="empresa_telefono" id="empresa_telefono" class="ui-input" placeholder="+1 (809) 000-0000">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Símbolo de Moneda <span class="text-danger">*</span></label>
        <input type="text" name="moneda_simbolo" id="moneda_simbolo" class="ui-input" value="RD$" required>
    </div>

    <div class="col-md-6">
        <label class="ui-label">ITBIS por defecto (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="impuesto_itbis" id="impuesto_itbis" class="ui-input" value="18" required>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
