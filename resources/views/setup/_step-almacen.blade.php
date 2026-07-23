<div class="ui-card-title">
    <i class="bi bi-buildings"></i>Almacén
</div>
<div class="ui-card-subtitle">Crea el almacén principal para el control de inventario.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="almacen">
    <div class="col-md-6">
        <label class="ui-label">Nombre del Almacén</label>
        <input type="text" name="nombre" class="ui-input" placeholder="Almacén General" required>
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
