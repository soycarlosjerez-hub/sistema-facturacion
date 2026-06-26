<h5 class="fw-bold mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i>Ubicación de Mesas</h5>
<p class="text-muted small mb-4">Define las áreas o zonas del restaurante (salón principal, balcón, terraza, etc.).</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="ubicacion-mesa">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre de la Ubicación</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Salón Principal" required>
    </div>
    <div class="col-12 d-flex justify-content-between mt-4">
        <div></div>
        <button type="submit" class="btn btn-wizard-next">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
