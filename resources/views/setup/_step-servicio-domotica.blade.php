<h5 class="fw-bold mb-2"><i class="bi bi-houses me-2 text-primary"></i>Servicio Domótico</h5>
<p class="text-muted small mb-4">Registra un proyecto de domótica para un cliente (opcional).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-servicio-domotica">
    @csrf
    <input type="hidden" name="step" value="servicio-domotica">
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Cliente</label>
        <select name="cliente_id" class="form-select rounded-3">
            <option value="">-- Seleccionar --</option>
            @foreach(\App\Models\Cliente::where('tenant_id', auth()->user()->business_instance_id)->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Tipo de Servicio</label>
        <select name="tipo_servicio" class="form-select rounded-3">
            <option value="camaras_seguridad">Cámaras de Seguridad</option>
            <option value="alarmas">Alarmas</option>
            <option value="control_acceso">Control de Acceso</option>
            <option value="redes">Redes</option>
            <option value="automatizacion">Automatización</option>
            <option value="sonido">Sonido</option>
            <option value="iluminacion">Iluminación</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label small fw-bold">Título del Proyecto</label>
        <input type="text" name="titulo" class="form-control rounded-3" placeholder="Ej. Cámaras para residencia" maxlength="255">
    </div>

    <div class="col-md-12">
        <label class="form-label small fw-bold">Descripción</label>
        <textarea name="descripcion" class="form-control rounded-3" rows="2" placeholder="Detalles del proyecto"></textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Presupuesto</label>
        <input type="number" step="0.01" name="presupuesto" class="form-control rounded-3" placeholder="0.00">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Fecha Programada</label>
        <input type="date" name="fecha_programada" class="form-control rounded-3">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-servicio-domotica">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
