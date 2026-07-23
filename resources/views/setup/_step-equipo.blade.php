<h5 class="fw-bold mb-2"><i class="bi bi-phone me-2 text-primary"></i>Equipo (IMEI)</h5>
<p class="text-muted small mb-4">Registra un equipo/celular con su número IMEI para control de inventario.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-equipo">
    @csrf
    <input type="hidden" name="step" value="equipo">
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Marca *</label>
        <input type="text" name="marca" class="form-control rounded-3" placeholder="Ej. Apple, Samsung, Xiaomi" required maxlength="100">
    </div>
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Modelo *</label>
        <input type="text" name="modelo" class="form-control rounded-3" placeholder="Ej. iPhone 15, Galaxy S24" required maxlength="100">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">IMEI / Serial *</label>
        <input type="text" name="serial_imei" class="form-control rounded-3" placeholder="35-... o serial" required maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">ESN / MEID</label>
        <input type="text" name="serial_esn" class="form-control rounded-3" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Almacenamiento</label>
        <input type="number" name="almacenamiento_gb" class="form-control rounded-3" placeholder="128">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Color</label>
        <input type="text" name="color" class="form-control rounded-3" placeholder="Negro, Blanco..." maxlength="50">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Estado</label>
        <select name="estado" class="form-select rounded-3">
            <option value="disponible">Disponible</option>
            <option value="vendido">Vendido</option>
            <option value="en_reparacion">En Reparación</option>
            <option value="reservado">Reservado</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Precio Compra</label>
        <input type="number" step="0.01" name="precio_compra" class="form-control rounded-3" placeholder="0.00">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Precio Venta</label>
        <input type="number" step="0.01" name="precio_venta" class="form-control rounded-3" placeholder="0.00">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-equipo">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
