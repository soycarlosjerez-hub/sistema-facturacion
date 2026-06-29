<h5 class="fw-bold mb-2"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Mesas</h5>
<p class="text-muted small mb-4">Crea una mesa para el restaurante.</p>

<form action="{{ route('setup.step') }}" method="POST" class="row g-3" id="form-mesa">
    @csrf
    <input type="hidden" name="step" value="mesa">
    @php
        $siguienteNumero = str_pad(
            \App\Models\Mesa::where('tenant_id', auth()->user()->business_instance_id)->count() + 1,
            2, '0', STR_PAD_LEFT
        );
    @endphp
    <div class="col-md-3">
        <label class="form-label small fw-bold">Número <span class="text-danger">*</span></label>
        <input type="text" name="numero" class="form-control rounded-3"
               placeholder="{{ $siguienteNumero }}" value="{{ $siguienteNumero }}" required maxlength="20">
    </div>
    <div class="col-md-9">
        <label class="form-label small fw-bold">Nombre de la Mesa</label>
        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej. Terraza, VIP">
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-bold">Capacidad</label>
        <input type="number" name="capacidad" class="form-control rounded-3" placeholder="4" min="1">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Ubicación</label>
        <select name="ubicacion_id" class="form-select rounded-3" required>
            @foreach(\App\Models\MesaUbicacion::where('tenant_id', auth()->user()->business_instance_id)->get() as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Categoría</label>
        <select name="categoria_id" class="form-select rounded-3" required>
            @foreach(\App\Models\MesaCategoria::where('tenant_id', auth()->user()->business_instance_id)->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-wizard-next" form="form-mesa">
            <i class="bi bi-check-lg me-1"></i> Guardar y Siguiente
        </button>
    </div>
</form>

<div class="d-flex justify-content-start mt-3">
    <form action="{{ route('setup.skip') }}" method="POST">
        @csrf
        <input type="hidden" name="step" value="mesa">
        <button type="submit" class="btn btn-outline-secondary btn-wizard-skip">
            <i class="bi bi-arrow-right me-1"></i> Omitir paso
        </button>
    </form>
</div>
