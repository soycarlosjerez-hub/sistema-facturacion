<h5 class="fw-bold mb-2"><i class="bi bi-card-checklist me-2 text-primary"></i>Servicio de Lavado</h5>
<p class="text-muted small mb-4">Crea un servicio de lavado básico para el catálogo.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="servicio-lavado">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre del Servicio</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Lavado Premium" required>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control rounded-3" placeholder="500.00" required>
    </div>
    <div class="col-12 d-flex justify-content-between mt-4">
        <div></div>
        <button type="submit" class="btn btn-wizard-next">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
