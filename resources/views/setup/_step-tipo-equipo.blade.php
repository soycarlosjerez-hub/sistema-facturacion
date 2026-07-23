<div class="ui-card-title">
    <i class="bi bi-cpu"></i>Tipo de Equipo
</div>
<div class="ui-card-subtitle">Crea un tipo de equipo de climatización para el catálogo.</div>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-tipo-equipo">
    @csrf
    <input type="hidden" name="step" value="tipo-equipo">
    
    <div class="col-md-8">
        <label class="ui-label">Nombre del Tipo <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="ui-input" placeholder="Ej. Split Pared, Central, Ventana" required maxlength="255">
    </div>
    
    <div class="col-md-4">
        <label class="ui-label">Categoría</label>
        <select name="categoria" class="ui-select">
            <option value="split">Split</option>
            <option value="central">Central</option>
            <option value="ventana">Ventana</option>
            <option value="portatil">Portátil</option>
            <option value="cassette">Cassette</option>
            <option value="conducto">Conducto</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost ui-btn-pill">Cancelar</a>
                <button type="submit" form="form-tipo-equipo" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-check-lg me-2"></i>Guardar y Siguiente
                </button>
            </div>
        </div>
    </div>
</form>
