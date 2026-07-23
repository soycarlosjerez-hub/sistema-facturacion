<h5 class="fw-bold mb-2"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Contrato de Mantenimiento</h5>
<p class="text-muted small mb-4">Crea un contrato de mantenimiento preventivo para tus clientes (opcional).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-contrato">
    @csrf
    <input type="hidden" name="step" value="contrato">
    
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
        <label class="form-label small fw-bold">Periodicidad</label>
        <select name="tipo_periodicidad" class="form-select rounded-3">
            <option value="mensual">Mensual</option>
            <option value="trimestral">Trimestral</option>
            <option value="semestral">Semestral</option>
            <option value="anual">Anual</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Vigencia Desde</label>
        <input type="date" name="vigencia_desde" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Vigencia Hasta</label>
        <input type="date" name="vigencia_hasta" class="form-control rounded-3" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Valor Mensual</label>
        <input type="number" step="0.01" name="valor_mensual" class="form-control rounded-3" placeholder="0.00">
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-contrato">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
