<div class="ui-card-title">
    <i class="bi bi-phone"></i>Equipo (IMEI)
</div>
<div class="ui-card-subtitle">Registra un equipo/celular con su número IMEI para control de inventario.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-equipo">
    @csrf
    <input type="hidden" name="step" value="equipo">
    
    <div class="col-md-6">
        <label class="ui-label">Marca <span class="text-danger">*</span></label>
        <input type="text" name="marca" class="ui-input" placeholder="Ej. Apple, Samsung, Xiaomi" required maxlength="100">
    </div>
    
    <div class="col-md-6">
        <label class="ui-label">Modelo <span class="text-danger">*</span></label>
        <input type="text" name="modelo" class="ui-input" placeholder="Ej. iPhone 15, Galaxy S24" required maxlength="100">
    </div>

    <div class="col-md-6">
        <label class="ui-label">IMEI / Serial <span class="text-danger">*</span></label>
        <input type="text" name="serial_imei" class="ui-input" placeholder="35-... o serial" required maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="ui-label">ESN / MEID</label>
        <input type="text" name="serial_esn" class="ui-input" placeholder="Opcional" maxlength="50">
    </div>

    <div class="col-md-4">
        <label class="ui-label">Almacenamiento</label>
        <input type="number" name="almacenamiento_gb" class="ui-input" placeholder="128">
    </div>

    <div class="col-md-4">
        <label class="ui-label">Color</label>
        <input type="text" name="color" class="ui-input" placeholder="Negro, Blanco..." maxlength="50">
    </div>

    <div class="col-md-4">
        <label class="ui-label">Estado</label>
        <select name="estado" class="ui-select">
            <option value="disponible">Disponible</option>
            <option value="vendido">Vendido</option>
            <option value="en_reparacion">En Reparación</option>
            <option value="reservado">Reservado</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Precio Compra</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="precio_compra" class="ui-input" placeholder="0.00">
        </div>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Precio Venta</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="precio_venta" class="ui-input" placeholder="0.00">
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-equipo" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
