<h5 class="fw-bold mb-2"><i class="bi bi-wrench-adjustable me-2 text-primary"></i>Mantenimiento</h5>
<p class="text-muted small mb-4">Registra un mantenimiento preventivo o correctivo (opcional).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-mantenimiento">
    @csrf
    <input type="hidden" name="step" value="mantenimiento">
    
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
        <label class="form-label small fw-bold">Tipo</label>
        <select name="tipo" class="form-select rounded-3">
            <option value="preventivo">Preventivo</option>
            <option value="correctivo">Correctivo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Descripción de Falla</label>
        <textarea name="descripcion_falla" class="form-control rounded-3" rows="2" placeholder="Describe el problema o tarea"></textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold">Programada Para</label>
        <input type="datetime-local" name="programada_para" class="form-control rounded-3">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-mantenimiento">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
