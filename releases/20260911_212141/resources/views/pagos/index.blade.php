<div class="ui-card" style="--delay:0s">
    <div class="ui-card-accent green"></div>
    <div class="ui-card-body">
        <div class="ui-card-title mb-3"><i class="bi bi-credit-card me-2"></i>Registrar nuevo pago</div>
        <form action="{{ route('pagos.store') }}" method="POST" class="row g-3">
            @csrf
            <input type="hidden" name="venta_id" value="{{ $venta->id }}">

            <div class="col-md-4">
                <label class="ui-label fw-semibold">Monto</label>
                <input type="number" step="0.01" name="monto" class="ui-input" required>
            </div>

            <div class="col-md-6">
                <label class="ui-label fw-semibold">Nota (opcional)</label>
                <input type="text" name="nota" class="ui-input" placeholder="Descripción del pago">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill w-100">
                    <i class="bi bi-check-lg me-1"></i> Pagar
                </button>
            </div>
        </form>
    </div>
</div>
