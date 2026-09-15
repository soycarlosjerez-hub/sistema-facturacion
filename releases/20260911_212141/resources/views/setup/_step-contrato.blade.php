<div class="ui-card-title">
    <i class="bi bi-file-earmark-text"></i>Contrato de Mantenimiento
</div>
<div class="ui-card-subtitle">Crea un contrato de mantenimiento preventivo para tus clientes (opcional).</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-contrato">
    @csrf
    <input type="hidden" name="step" value="contrato">
    
    <div class="col-md-6">
        <label class="ui-label">Cliente</label>
        <select name="cliente_id" class="ui-select">
            <option value="">-- Seleccionar --</option>
            @foreach(\App\Models\Cliente::where('tenant_id', auth()->user()->business_instance_id)->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-6">
        <label class="ui-label">Periodicidad</label>
        <select name="tipo_periodicidad" class="ui-select">
            <option value="mensual">Mensual</option>
            <option value="trimestral">Trimestral</option>
            <option value="semestral">Semestral</option>
            <option value="anual">Anual</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="ui-label">Vigencia Desde</label>
        <input type="date" name="vigencia_desde" class="ui-input" value="{{ date('Y-m-d') }}">
    </div>

    <div class="col-md-4">
        <label class="ui-label">Vigencia Hasta</label>
        <input type="date" name="vigencia_hasta" class="ui-input" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
    </div>

    <div class="col-md-4">
        <label class="ui-label">Valor Mensual</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="valor_mensual" class="ui-input" placeholder="0.00">
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-contrato" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
