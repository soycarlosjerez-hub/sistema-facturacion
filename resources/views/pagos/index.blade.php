<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Registrar nuevo pago</h5>
        <form action="{{ route('pagos.store') }}" method="POST" class="row g-3">
            @csrf
            <input type="hidden" name="venta_id" value="{{ $venta->id }}">

            <div class="col-md-4">
                <label class="form-label fw-semibold">Monto</label>
                <input type="number" step="0.01" name="monto" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nota (opcional)</label>
                <input type="text" name="nota" class="form-control" placeholder="Descripción del pago">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill w-100">
                    <i class="bi bi-check-lg me-1"></i> Pagar
                </button>
            </div>
        </form>
    </div>
</div>
