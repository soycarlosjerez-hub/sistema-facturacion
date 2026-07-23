<div class="ui-card-title">
    <i class="bi bi-person-gear"></i>Técnico
</div>
<div class="ui-card-subtitle">Registra un técnico para reparaciones e instalaciones.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-tecnico">
    @csrf
    <input type="hidden" name="step" value="tecnico">
    
    <div class="col-md-6">
        <label class="ui-label">Nombre Completo <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input" placeholder="Juan Pérez" required maxlength="255">
    </div>
    
    <div class="col-md-6">
        <label class="ui-label">Cédula</label>
        <input type="text" name="cedula" class="ui-input" placeholder="001-0000000-0" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="telefono" class="ui-input" placeholder="809-000-0000" maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Email</label>
        <input type="email" name="email" class="ui-input" placeholder="tecnico@email.com" maxlength="255">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Especialidad</label>
        <select name="especialidad" class="ui-select">
            <option value="reparacion">Reparación</option>
            <option value="instalacion">Instalación</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="domotica">Domótica</option>
            <option value="electronica">Electrónica</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Tarifa por Hora</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="tarifa_hora" class="ui-input" placeholder="500.00">
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-tecnico" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
