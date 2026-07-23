<h5 class="fw-bold mb-2"><i class="bi bi-tools me-2 text-primary"></i>Orden de Reparación</h5>
<p class="text-muted small mb-4">Crea una orden técnica para reparación de equipos (opcional).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-orden-tecnica">
    @csrf
    <input type="hidden" name="step" value="orden-tecnica">
    
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
        <label class="form-label small fw-bold">Equipo</label>
        <select name="equipo_id" class="form-select rounded-3">
            <option value="">-- Seleccionar --</option>
            @foreach(\App\Models\Equipo::where('tenant_id', auth()->user()->business_instance_id)->get() as $e)
                <option value="{{ $e->id }}">{{ $e->marca }} {{ $e->modelo }} ({{ $e->serial_imei }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Tipo de Servicio</label>
        <select name="tipo_servicio" class="form-select rounded-3">
            <option value="hardware">Hardware</option>
            <option value="software">Software</option>
            <option value="desbloqueo">Desbloqueo</option>
            <option value="recuperacion_datos">Recuperación de Datos</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Técnico Asignado</label>
        <select name="tecnico_id" class="form-select rounded-3">
            <option value="">-- Seleccionar --</option>
            @foreach(\App\Models\Tecnico::where('tenant_id', auth()->user()->business_instance_id)->get() as $t)
                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label small fw-bold">Problema Reportado</label>
        <textarea name="problema_reportado" class="form-control rounded-3" rows="2" placeholder="Describe el problema"></textarea>
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-orden-tecnica">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
