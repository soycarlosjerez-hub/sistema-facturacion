<h5 class="fw-bold mb-2"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Secuencias NCF</h5>
<p class="text-muted small mb-4">Configura una secuencia de comprobante fiscal para facturación electrónica.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="ncf">
    <div class="col-md-4">
        <label class="form-label small fw-bold">Tipo de Comprobante</label>
        <select name="tipo_comprobante" class="form-select rounded-3" required>
            <option value="01">01 - Factura de Crédito Fiscal</option>
            <option value="02">02 - Factura de Consumo</option>
            <option value="03">03 - Nota de Débito</option>
            <option value="04">04 - Nota de Crédito</option>
            <option value="11">11 - Comprobante de Compras</option>
            <option value="15">15 - Factura Gubernamental</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small fw-bold">Prefijo</label>
        <input type="text" name="prefijo" class="form-control rounded-3" placeholder="B01" required>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">Desde (Número Inicial)</label>
        <input type="number" name="desde" class="form-control rounded-3" placeholder="1" required>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">Hasta (Número Final)</label>
        <input type="number" name="hasta" class="form-control rounded-3" placeholder="100" required>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Fecha de Vencimiento</label>
        <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
    </div>
    <div class="col-12 d-flex justify-content-between mt-4">
        <form action="{{ route('setup.skip') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="step" value="ncf">
            <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
                <i class="bi bi-arrow-right me-1"></i> Omitir paso
            </button>
        </form>
        <button type="submit" class="btn btn-wizard-next">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
