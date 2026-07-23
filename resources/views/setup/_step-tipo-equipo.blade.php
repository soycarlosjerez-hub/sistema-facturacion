<h5 class="fw-bold mb-2"><i class="bi bi-cpu me-2 text-primary"></i>Tipo de Equipo</h5>
<p class="text-muted small mb-4">Crea un tipo de equipo de climatización para el catálogo.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-tipo-equipo">
    @csrf
    <input type="hidden" name="step" value="tipo-equipo">
    
    <div class="col-md-8">
        <label class="form-label small fw-bold">Nombre del Tipo *</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej. Split Pared, Central, Ventana" required maxlength="255">
    </div>
    
    <div class="col-md-4">
        <label class="form-label small fw-bold">Categoría</label>
        <select name="categoria" class="form-select rounded-3">
            <option value="split">Split</option>
            <option value="central">Central</option>
            <option value="ventana">Ventana</option>
            <option value="portatil">Portátil</option>
            <option value="cassette">Cassette</option>
            <option value="conducto">Conducto</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-tipo-equipo">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
