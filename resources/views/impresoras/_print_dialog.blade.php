<div class="mb-3">
    <label class="form-label fw-semibold">Impresora</label>
    <select name="impresora_id" class="form-select border-0 bg-light" id="selectImpresora">
        @foreach($impresoras as $imp)
            <option value="{{ $imp->id }}" {{ ($impresoraPorDefecto ?? null)?->id === $imp->id ? 'selected' : '' }}
                data-papel="{{ $imp->papel_tamano }}">
                {{ $imp->nombre }} ({{ $imp->conexion_resumen }})
            </option>
        @endforeach
    </select>
</div>

<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label fw-semibold">Formato</label>
        <select name="formato" class="form-select border-0 bg-light">
            <option value="ticket">Ticket Térmico</option>
            <option value="pdf">PDF</option>
        </select>
    </div>
    <div class="col-3">
        <label class="form-label fw-semibold">Copias</label>
        <input type="number" name="copias" class="form-control border-0 bg-light" value="1" min="1" max="10">
    </div>
    <div class="col-3">
        <label class="form-label fw-semibold">Papel</label>
        <select name="papel_tamano" class="form-select border-0 bg-light">
            <option value="80mm">80 mm</option>
            <option value="58mm">58 mm</option>
            <option value="letter">Carta</option>
        </select>
    </div>
</div>

<input type="hidden" name="tipo" value="{{ $tipo }}">
<input type="hidden" name="id" value="{{ $id }}">
