<h5 class="fw-bold mb-2"><i class="bi bi-tools me-2 text-primary"></i>Instalación</h5>
<p class="text-muted small mb-4">Registra una instalación de equipo de climatización para un cliente.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-instalacion">
    @csrf
    <input type="hidden" name="step" value="instalacion">
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Cliente *</label>
        <select name="cliente_id" class="form-select rounded-3" required>
            @foreach(\App\Models\Cliente::where('tenant_id', auth()->user()->business_instance_id)->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-6">
        <label class="form-label small fw-bold">Dirección de Instalación</label>
        <input type="text" name="direccion_instalacion" class="form-control rounded-3" placeholder="Dirección del equipo" maxlength="500">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Tipo de Inmueble</label>
        <select name="tipo_inmueble" class="form-select rounded-3">
            <option value="casa">Casa</option>
            <option value="apartamento">Apartamento</option>
            <option value="local">Local Comercial</option>
            <option value="industrial">Industrial</option>
        </select>
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-instalacion">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
