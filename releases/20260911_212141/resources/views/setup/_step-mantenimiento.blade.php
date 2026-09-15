<div class="ui-card-title">
    <i class="bi bi-wrench-adjustable"></i>Mantenimiento
</div>
<div class="ui-card-subtitle">Registra un mantenimiento preventivo o correctivo (opcional).</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-mantenimiento">
    @csrf
    <input type="hidden" name="step" value="mantenimiento">
    
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
        <label class="ui-label">Tipo</label>
        <select name="tipo" class="ui-select">
            <option value="preventivo">Preventivo</option>
            <option value="correctivo">Correctivo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Descripción de Falla</label>
        <textarea name="descripcion_falla" class="ui-textarea" placeholder="Describe el problema o tarea"></textarea>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Programada Para</label>
        <input type="datetime-local" name="programada_para" class="ui-input">
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-mantenimiento" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
