<h5 class="fw-bold mb-2"><i class="bi bi-people me-2 text-primary"></i>Lavadores</h5>
<p class="text-muted small mb-4">Registra un lavador para asignarlo a los servicios.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-lavador">
    @csrf
    <input type="hidden" name="step" value="lavador">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre del Lavador</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Juan Pérez" required>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="text" name="telefono" class="form-control rounded-3" placeholder="809-000-0000">
    </div>
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-lavador">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>

<div class="d-flex justify-content-start mt-3">
    <form action="{{ route('setup.skip') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="lavador">
        <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
            <i class="bi bi-arrow-right me-1"></i> Omitir paso
        </button>
    </form>
</div>
