<div class="ui-card-title">
    <i class="bi bi-receipt-cutoff"></i>Secuencias NCF
</div>
<div class="ui-card-subtitle">Configura una secuencia de comprobante fiscal para facturación electrónica.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-ncf">
    @csrf
    <input type="hidden" name="step" value="ncf">
    <div class="col-md-4">
        <label class="ui-label">Tipo de Comprobante</label>
        <select name="tipo_comprobante" class="ui-select" required>
            <option value="01">01 - Factura de Crédito Fiscal</option>
            <option value="02">02 - Factura de Consumo</option>
            <option value="03">03 - Nota de Débito</option>
            <option value="04">04 - Nota de Crédito</option>
            <option value="11">11 - Comprobante de Compras</option>
            <option value="15">15 - Factura Gubernamental</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="ui-label">Prefijo</label>
        <input type="text" name="prefijo" class="ui-input" placeholder="B01" required>
    </div>
    <div class="col-md-3">
        <label class="ui-label">Desde (Número Inicial)</label>
        <input type="number" name="desde" class="ui-input" placeholder="1" required>
    </div>
    <div class="col-md-3">
        <label class="ui-label">Hasta (Número Final)</label>
        <input type="number" name="hasta" class="ui-input" placeholder="100" required>
    </div>
    <div class="col-md-4">
        <label class="ui-label">Fecha de Vencimiento</label>
        <input type="date" name="fecha_vencimiento" class="ui-input" required>
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-ncf" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
