<h5 class="fw-bold mb-2"><i class="bi bi-person-gear me-2 text-primary"></i>Técnico</h5>
<p class="text-muted small mb-4">Registra un técnico para reparaciones e instalaciones.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-tecnico">
    @csrf
    <input type="hidden" name="step" value="tecnico">
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre Completo *</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Juan Pérez" required maxlength="255">
    </div>
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Cédula</label>
        <input type="text" name="cedula" class="form-control rounded-3" placeholder="001-0000000-0" maxlength="20">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="text" name="telefono" class="form-control rounded-3" placeholder="809-000-0000" maxlength="50">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Email</label>
        <input type="email" name="email" class="form-control rounded-3" placeholder="tecnico@email.com" maxlength="255">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Especialidad</label>
        <select name="especialidad" class="form-select rounded-3">
            <option value="reparacion">Reparación</option>
            <option value="instalacion">Instalación</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="domotica">Domótica</option>
            <option value="electronica">Electrónica</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Tarifa por Hora</label>
        <input type="number" step="0.01" name="tarifa_hora" class="form-control rounded-3" placeholder="500.00">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-tecnico">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
