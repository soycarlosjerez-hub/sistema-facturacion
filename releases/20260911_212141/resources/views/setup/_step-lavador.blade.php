<div class="ui-card-title">
    <i class="bi bi-people"></i>Lavadores
</div>
<div class="ui-card-subtitle">Registra un lavador para asignarlo a los servicios.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-lavador">
    @csrf
    <input type="hidden" name="step" value="lavador">
    <div class="col-md-6">
        <label class="ui-label">Nombre del Lavador</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Juan Pérez" required>
    </div>
    <div class="col-md-6">
        <label class="ui-label">Teléfono</label>
        <input type="text" name="telefono" class="ui-input" placeholder="809-000-0000">
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-lavador" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
