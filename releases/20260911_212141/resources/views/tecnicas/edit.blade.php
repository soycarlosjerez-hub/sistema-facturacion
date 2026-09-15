@extends('layouts.app')

@section('title', 'Editar Orden #' . $orden->numero_orden)

@push('styles')
@include('partials.premium-ui')
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Orden #{{ $orden->numero_orden }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-tools me-1"></i>
                        Modificando orden de reparación
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnicas.show', $orden) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(59,130,246,.05);border-left:4px solid #3b82f6 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#3b82f6;background:rgba(59,130,246,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando la orden:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">#{{ $orden->numero_orden }} - {{ $orden->cliente }}</strong>
            </div>
        </div>
    </div>

    <form id="tecnicasForm" method="POST" action="{{ route('tecnicas.update', $orden) }}">
        @csrf @method('PUT')

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información del Cliente</div>
            <div class="ui-card-subtitle">Selecciona o modifica el cliente</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $orden->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} - {{ $cliente->rnc_cedula }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="ui-select @error('tipo_servicio') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="hardware" {{ old('tipo_servicio', $orden->tipo_servicio) == 'hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="software" {{ old('tipo_servicio', $orden->tipo_servicio) == 'software' ? 'selected' : '' }}>Software</option>
                            <option value="desbloqueo" {{ old('tipo_servicio', $orden->tipo_servicio) == 'desbloqueo' ? 'selected' : '' }}>Desbloqueo</option>
                            <option value="recuperacion_datos" {{ old('tipo_servicio', $orden->tipo_servicio) == 'recuperacion_datos' ? 'selected' : '' }}>Recuperación de Datos</option>
                            <option value="mantenimiento" {{ old('tipo_servicio', $orden->tipo_servicio) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="personalizacion" {{ old('tipo_servicio', $orden->tipo_servicio) == 'personalizacion' ? 'selected' : '' }}>Personalización</option>
                            <option value="otro" {{ old('tipo_servicio', $orden->tipo_servicio) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_servicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-phone"></i> Información del Equipo</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Equipo</label>
                        <select name="equipo_id" class="ui-select @error('equipo_id') is-invalid @enderror">
                            <option value="">Sin equipo</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->id }}" {{ old('equipo_id', $orden->equipo_id) == $equipo->id ? 'selected' : '' }}>
                                    {{ $equipo->serial_imei }} - {{ $equipo->marca }} {{ $equipo->modelo }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tech)
                                <option value="{{ $tech->id }}" {{ old('tecnico_id', $orden->tecnico_id) == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Problema Reportado <span class="text-danger">*</span></label>
                        <textarea name="problema_reportado" class="ui-input @error('problema_reportado') is-invalid @enderror" rows="3" required>{{ old('problema_reportado', $orden->problema_reportado) }}</textarea>
                        @error('problema_reportado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Diagnóstico</label>
                        <textarea name="diagnostico" class="ui-input @error('diagnostico') is-invalid @enderror" rows="2">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                        @error('diagnostico')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Solución Aplicada</label>
                        <textarea name="solucion_aplicada" class="ui-input @error('solucion_aplicada') is-invalid @enderror" rows="2">{{ old('solucion_aplicada', $orden->solucion_aplicada) }}</textarea>
                        @error('solucion_aplicada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha Estimada de Entrega</label>
                        <input type="date" name="fecha_entrega_estimada" class="ui-input @error('fecha_entrega_estimada') is-invalid @enderror" value="{{ old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d')) }}">
                        @error('fecha_entrega_estimada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Método de Pago</label>
                        <select name="metodo_pago" class="ui-select @error('metodo_pago') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo" {{ old('metodo_pago', $orden->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago', $orden->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="tarjeta" {{ old('metodo_pago', $orden->metodo_pago) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="NCF" {{ old('metodo_pago', $orden->metodo_pago) == 'NCF' ? 'selected' : '' }}>NCF</option>
                        </select>
                        @error('metodo_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.3s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-currency-dollar"></i> Costos</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="ui-label">Costo Piezas</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="costo_piezas" class="ui-input" step="0.01" min="0" value="{{ old('costo_piezas', $orden->costo_piezas) }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Mano de Obra</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="mano_obra" class="ui-input" step="0.01" min="0" value="{{ old('mano_obra', $orden->mano_obra) }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="{{ old('descuento', $orden->descuento) }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Total Calculado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="total_display" class="form-control" readonly value="{{ number_format($orden->total, 2) }}" style="font-weight: 700;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.4s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-shield-check"></i> Garantía y Notas</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="garantia_extendida" id="garantia_extendida" value="1" {{ old('garantia_extendida', $orden->garantia_extendida) ? 'checked' : '' }}>
                            <label class="form-check-label" for="garantia_extendida">Garantía Extendida</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3">{{ old('notas', $orden->notas) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#3b82f6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando orden #{{ $orden->numero_orden }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tecnicas.show', $orden) }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="tecnicasForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calcularTotal() {
        const piezas = parseFloat($('input[name="costo_piezas"]').val()) || 0;
        const manoObra = parseFloat($('input[name="mano_obra"]').val()) || 0;
        const descuento = parseFloat($('input[name="descuento"]').val()) || 0;
        const subtotal = piezas + manoObra;
        const base = Math.max(subtotal - descuento, 0);
        const itbis = base * 0.18;
        const total = base + itbis;
        $('#total_display').val(total.toFixed(2));
    }

    $('input[name="costo_piezas"], input[name="mano_obra"], input[name="descuento"]').on('input', calcularTotal);
});
</script>
@endpush
