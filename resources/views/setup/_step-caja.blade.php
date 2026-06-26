<h5 class="fw-bold mb-2"><i class="bi bi-cash-stack me-2 text-primary"></i>Caja</h5>
<p class="text-muted small mb-4">Crea la caja registradora para el punto de venta.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="step" value="caja">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre de la Caja</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Caja Principal" required>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Sucursal</label>
        <select name="sucursal_id" class="form-select rounded-3" required>
            @foreach(\App\Models\Sucursal::where('tenant_id', auth()->user()->business_instance_id)->get() as $s)
                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 d-flex justify-content-between mt-4">
        <div></div>
        <button type="submit" class="btn btn-wizard-next">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>
