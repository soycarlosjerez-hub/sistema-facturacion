<div class="ui-card-title">
    <i class="bi bi-building"></i>Sucursal
</div>
<div class="ui-card-subtitle">Crea la sucursal principal del negocio.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="sucursal">
    <div class="col-md-6">
        <label class="ui-label">Nombre de la Sucursal</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Sucursal Principal" required>
    </div>
    <div class="col-md-6">
        <label class="ui-label">Código</label>
        <input type="text" name="codigo" class="ui-input" placeholder="SUC-001" required>
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
