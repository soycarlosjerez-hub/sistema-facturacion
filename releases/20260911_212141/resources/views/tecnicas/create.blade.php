@extends('layouts.app')

@section('title', 'Nueva Orden de Reparación')

@push('styles')
@include('partials.premium-ui')
<style>
.imei-autocomplete { position: relative; }
.autocomplete-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 100;
    background: white;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.5rem 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    display: none;
}
body.dark-mode .autocomplete-results { background: #1e293b; border-color: #334155; }
.autocomplete-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
}
.autocomplete-item:hover { background: #f8fafc; }
body.dark-mode .autocomplete-item { border-bottom-color: #334155; }
body.dark-mode .autocomplete-item:hover { background: #334155; }
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
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nueva Orden de Reparación</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registrar una nueva orden de reparación técnica
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnicas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="tecnicasForm" method="POST" action="{{ route('tecnicas.store') }}">
            @csrf

            {{-- Información del Cliente --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #0891b2;">
                    <i class="bi bi-person-vcard me-2"></i>Información del Cliente
                </h6>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $clientePreselect) == $cliente->id ? 'selected' : '' }}>
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
                            <option value="hardware" {{ old('tipo_servicio') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="software" {{ old('tipo_servicio') == 'software' ? 'selected' : '' }}>Software</option>
                            <option value="desbloqueo" {{ old('tipo_servicio') == 'desbloqueo' ? 'selected' : '' }}>Desbloqueo</option>
                            <option value="recuperacion_datos" {{ old('tipo_servicio') == 'recuperacion_datos' ? 'selected' : '' }}>Recuperación de Datos</option>
                            <option value="mantenimiento" {{ old('tipo_servicio') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="personalizacion" {{ old('tipo_servicio') == 'personalizacion' ? 'selected' : '' }}>Personalización</option>
                            <option value="otro" {{ old('tipo_servicio') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_servicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Información del Equipo --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                    <i class="bi bi-phone me-2"></i>Información del Equipo
                </h6>
                <div class="row g-3">
                    <div class="col-lg-6 imei-autocomplete">
                        <label class="ui-label">Buscar por Serial/IMEI</label>
                        <input type="text" id="imei_search" class="ui-input" placeholder="Escribe al menos 4 caracteres..." autocomplete="off">
                        <input type="hidden" name="equipo_id" id="equipo_id" value="{{ old('equipo_id') }}">
                        <div class="autocomplete-results" id="autocomplete_results"></div>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tech)
                                <option value="{{ $tech->id }}" {{ old('tecnico_id') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Problema Reportado <span class="text-danger">*</span></label>
                        <textarea name="problema_reportado" class="ui-input @error('problema_reportado') is-invalid @enderror" rows="3" required placeholder="Describe el problema reportado por el cliente...">{{ old('problema_reportado') }}</textarea>
                        @error('problema_reportado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha de Recepción</label>
                        <input type="date" name="fecha_recibo" class="ui-input @error('fecha_recibo') is-invalid @enderror" value="{{ old('fecha_recibo', date('Y-m-d')) }}">
                        @error('fecha_recibo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha Estimada de Entrega</label>
                        <input type="date" name="fecha_entrega_estimada" class="ui-input @error('fecha_entrega_estimada') is-invalid @enderror" value="{{ old('fecha_entrega_estimada') }}">
                        @error('fecha_entrega_estimada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Método de Pago</label>
                        <select name="metodo_pago" class="ui-select @error('metodo_pago') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="NCF" {{ old('metodo_pago') == 'NCF' ? 'selected' : '' }}>NCF</option>
                        </select>
                        @error('metodo_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Costos --}}
            <div class="ui-card-body pb-4 mb-4 border-bottom">
                <h6 class="fw-bold mb-3" style="color: #059669;">
                    <i class="bi bi-currency-dollar me-2"></i>Costos
                </h6>
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="ui-label">Costo Piezas</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="costo_piezas" class="ui-input" step="0.01" min="0" value="{{ old('costo_piezas', '0') }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Mano de Obra</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="mano_obra" class="ui-input" step="0.01" min="0" value="{{ old('mano_obra', '0') }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="{{ old('descuento', '0') }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Total</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="total_display" class="form-control" readonly value="0.00" style="font-weight: 700;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Garantía y Notas --}}
            <div class="ui-card-body">
                <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                    <i class="bi bi-shield-check me-2"></i>Garantía y Notas
                </h6>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(34,197,94,.05);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="garantia_extendida" id="garantia_extendida" value="1" {{ old('garantia_extendida') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold ms-2" for="garantia_extendida">Garantía Extendida</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
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
            <span class="fw-semibold d-none d-sm-inline">Creando nueva orden de reparación</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tecnicas.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="tecnicasForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Orden
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // IMEI Autocomplete
    let debounceTimer;
    $('#imei_search').on('keyup', function() {
        clearTimeout(debounceTimer);
        const q = $(this).val().trim();
        if (q.length < 4) {
            $('#autocomplete_results').hide();
            return;
        }
        debounceTimer = setTimeout(() => {
            $.get('{{ route("tecnicas.buscar-imei") }}', { q: q }, function(data) {
                const $results = $('#autocomplete_results');
                $results.empty();
                if (data.length === 0) {
                    $results.hide();
                    return;
                }
                data.forEach(item => {
                    $results.append(`<div class="autocomplete-item" data-id="${item.id}">${item.equipo_modelo} (${item.equipo_serial}) - ${item.estado}</div>`);
                });
                $results.show();
            });
        }, 300);
    });

    $(document).on('click', '.autocomplete-item', function() {
        const id = $(this).data('id');
        $('#equipo_id').val(id);
        $('#imei_search').val('');
        $('#autocomplete_results').hide();
    });

    // Calculate totals
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
    calcularTotal();
});
</script>
@endpush
