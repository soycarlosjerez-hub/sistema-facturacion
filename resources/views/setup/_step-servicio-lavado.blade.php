<div class="ui-card-title">
    <i class="bi bi-card-checklist"></i>Servicio de Lavado
</div>
<div class="ui-card-subtitle">Crea un servicio de lavado básico para el catálogo.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="servicio-lavado">
    <div class="col-md-6">
        <label class="ui-label">Nombre del Servicio</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Lavado Premium" required>
    </div>
    <div class="col-md-6">
        <label class="ui-label">Precio</label>
        <div class="ui-input-group">
            <span class="ui-input-group-text">RD$</span>
            <input type="number" step="0.01" name="precio" class="ui-input" placeholder="500.00" required>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
