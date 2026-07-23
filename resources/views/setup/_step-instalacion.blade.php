<div class="ui-card-title">
    <i class="bi bi-tools"></i>Instalación
</div>
<div class="ui-card-subtitle">Registra una instalación de equipo de climatización para un cliente.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-instalacion">
    @csrf
    <input type="hidden" name="step" value="instalacion">
    
    <div class="col-md-6">
        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
        <select name="cliente_id" class="ui-select" required>
            @foreach(\App\Models\Cliente::where('tenant_id', auth()->user()->business_instance_id)->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-6">
        <label class="ui-label">Dirección de Instalación</label>
        <input type="text" name="direccion_instalacion" class="ui-input" placeholder="Dirección del equipo" maxlength="500">
    </div>

    <div class="col-md-6">
        <label class="ui-label">Tipo de Inmueble</label>
        <select name="tipo_inmueble" class="ui-select">
            <option value="casa">Casa</option>
            <option value="apartamento">Apartamento</option>
            <option value="local">Local Comercial</option>
            <option value="industrial">Industrial</option>
        </select>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-instalacion" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
