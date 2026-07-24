@extends('layouts.app')

@section('title', 'Nuevo Mantenimiento')

@push('styles')
@include('partials.premium-ui')
<style>
    .repuesto-row {
        background: rgba(248,250,252,.6);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: .75rem;
        border: 1px solid #f1f5f9;
        transition: all .2s ease;
        position: relative;
    }
    .repuesto-row:hover {
        border-color: #e2e8f0;
        background: rgba(248,250,252,.9);
    }
    .repuesto-row .remove-repuesto {
        position: absolute;
        top: .5rem;
        right: .5rem;
    }
    body.dark-mode .repuesto-row {
        background: rgba(15,23,42,.4);
        border-color: #1e293b;
    }
    body.dark-mode .repuesto-row:hover {
        background: rgba(15,23,42,.6);
        border-color: #334155;
    }
    .subtotal-display {
        font-size: .85rem;
        color: var(--accent, #3b82f6);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    {{-- HEADER --}}
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Nuevo Mantenimiento</h1>
                    <div class="ui-header-meta">
                        <span>Registrar un nuevo mantenimiento de equipo</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.mantenimientos.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('climatizacion.mantenimientos.store') }}" method="POST">
        @csrf

        <div class="ui-card" style="--delay:.05s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0 0 1rem 0;">
                    <i class="bi bi-info-circle"></i> Información General
                </h5>

                <div class="row g-3">
                    {{-- Cliente --}}
                    <div class="col-md-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="col-md-3">
                        <label class="ui-label">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="ui-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccionar</option>
                            @foreach (\App\Models\Mantenimiento::TIPOS as $val => $label)
                                <option value="{{ $val }}" {{ old('tipo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Técnico --}}
                    <div class="col-md-3">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select @error('tecnico_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach (\App\Models\User::where('activo', true)->get() as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ old('tecnico_id') == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    {{-- Contrato --}}
                    <div class="col-md-6">
                        <label class="ui-label">Contrato de Mantenimiento</label>
                        <select name="contrato_mantenimiento_id" class="ui-select @error('contrato_mantenimiento_id') is-invalid @enderror">
                            <option value="">Sin contrato asociado</option>
                            @foreach ($contratos as $contrato)
                                <option value="{{ $contrato->id }}" {{ old('contrato_mantenimiento_id') == $contrato->id ? 'selected' : '' }}>
                                    {{ $contrato->codigo }} — {{ $contrato->cliente?->nombre ?? 'Cliente #'.$contrato->cliente_id }}
                                </option>
                            @endforeach
                        </select>
                        @error('contrato_mantenimiento_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Programada para --}}
                    <div class="col-md-3">
                        <label class="ui-label">Programada para</label>
                        <input type="datetime-local" name="programada_para"
                               class="ui-input @error('programada_para') is-invalid @enderror"
                               value="{{ old('programada_para') }}">
                        @error('programada_para') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    {{-- Descripción de la falla --}}
                    <div class="col-12">
                        <label class="ui-label">Descripción de la Falla / Trabajo a Realizar</label>
                        <textarea name="descripcion_falla" rows="3"
                                  class="ui-textarea @error('descripcion_falla') is-invalid @enderror"
                                  placeholder="Describa el problema reportado o el trabajo preventivo a realizar...">{{ old('descripcion_falla') }}</textarea>
                        @error('descripcion_falla') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- COSTOS --}}
        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0 0 1rem 0;">
                    <i class="bi bi-cash-stack"></i> Costos
                </h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label">Costo de Repuestos (RD$)</label>
                        <input type="number" step="0.01" min="0" name="costo_repuestos"
                               class="ui-input @error('costo_repuestos') is-invalid @enderror"
                               value="{{ old('costo_repuestos', 0) }}" id="costo_repuestos">
                        @error('costo_repuestos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Mano de Obra (RD$)</label>
                        <input type="number" step="0.01" min="0" name="mano_de_obra"
                               class="ui-input @error('mano_de_obra') is-invalid @enderror"
                               value="{{ old('mano_de_obra', 0) }}" id="mano_de_obra">
                        @error('mano_de_obra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="w-100">
                            <label class="ui-label">Total Estimado</label>
                            <div class="ui-input" style="background:#f8fafc;font-weight:700;font-size:1.1rem;color:var(--accent);" id="total_estimado">
                                RD$ 0.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- REPUESTOS DETALLADOS --}}
        <div class="ui-card" style="--delay:.15s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="ui-card-title" style="padding:0;">
                        <i class="bi bi-box-seam"></i> Repuestos Utilizados
                    </h5>
                    <button type="button" class="ui-btn ui-btn-sm ui-btn-ghost" id="addRepuestoBtn">
                        <i class="bi bi-plus-lg"></i> Agregar Repuesto
                    </button>
                </div>

                <div id="repuestosContainer">
                    {{-- Repuestos existentes (old) --}}
                    @if (old('repuestos_usados'))
                        @foreach (old('repuestos_usados') as $idx => $repuesto)
                        <div class="repuesto-row" data-index="{{ $idx }}">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-repuesto" title="Eliminar repuesto">
                                <i class="bi bi-x"></i>
                            </button>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="ui-label small">Nombre del Repuesto</label>
                                    <input type="text" name="repuestos_usados[{{ $idx }}][nombre]"
                                           class="ui-input" value="{{ $repuesto['nombre'] }}"
                                           placeholder="Ej: Filtro de aire">
                                </div>
                                <div class="col-md-2">
                                    <label class="ui-label small">Cantidad</label>
                                    <input type="number" name="repuestos_usados[{{ $idx }}][cantidad]"
                                           class="ui-input repuesto-cantidad" value="{{ $repuesto['cantidad'] ?? 1 }}" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="ui-label small">Precio Unit. (RD$)</label>
                                    <input type="number" step="0.01" name="repuestos_usados[{{ $idx }}][precio]"
                                           class="ui-input repuesto-precio" value="{{ $repuesto['precio'] ?? 0 }}" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="ui-label small">Subtotal</label>
                                    <div class="subtotal-display">RD$ 0.00</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <div id="noRepuestos" class="ui-empty-state {{ old('repuestos_usados') ? 'd-none' : '' }}" style="padding:1.5rem;">
                    <i class="bi bi-box" style="font-size:1.5rem;"></i>
                    <p style="font-size:.9rem;">No se han agregado repuestos</p>
                    <span class="text-muted small">Haz clic en "Agregar Repuesto" para detallar las piezas utilizadas</span>
                </div>
            </div>
        </div>

        {{-- STICKY BAR --}}
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="{{ route('climatizacion.mantenimientos.index') }}" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid">
                    <i class="bi bi-check-lg"></i> Guardar Mantenimiento
                </button>
            </div>
        </div>
    </form>

    {{-- Spacer for sticky bar --}}
    <div style="height:80px;"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('repuestosContainer');
    const noRepuestos = document.getElementById('noRepuestos');
    const addBtn = document.getElementById('addRepuestoBtn');
    const costInput = document.getElementById('costo_repuestos');
    const laborInput = document.getElementById('mano_de_obra');
    const totalDisplay = document.getElementById('total_estimado');
    let repuestoIndex = container.children.length;

    // Calculate total
    function updateTotal() {
        const cost = parseFloat(costInput.value) || 0;
        const labor = parseFloat(laborInput.value) || 0;
        totalDisplay.textContent = 'RD$ ' + (cost + labor).toFixed(2);
    }
    costInput.addEventListener('input', updateTotal);
    laborInput.addEventListener('input', updateTotal);
    updateTotal();

    // Create repuesto row HTML
    function createRepuestoRow(index, data) {
        data = data || {};
        const div = document.createElement('div');
        div.className = 'repuesto-row';
        div.dataset.index = index;
        div.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger remove-repuesto" title="Eliminar repuesto" style="position:absolute;top:.5rem;right:.5rem;padding:.15rem .4rem;">
                <i class="bi bi-x"></i>
            </button>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="ui-label small">Nombre del Repuesto</label>
                    <input type="text" name="repuestos_usados[${index}][nombre]"
                           class="ui-input" value="${data.nombre || ''}"
                           placeholder="Ej: Filtro de aire">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Cantidad</label>
                    <input type="number" name="repuestos_usados[${index}][cantidad]"
                           class="ui-input repuesto-cantidad" value="${data.cantidad || 1}" min="1">
                </div>
                <div class="col-md-3">
                    <label class="ui-label small">Precio Unit. (RD$)</label>
                    <input type="number" step="0.01" name="repuestos_usados[${index}][precio]"
                           class="ui-input repuesto-precio" value="${data.precio || 0}" min="0">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Subtotal</label>
                    <div class="subtotal-display">RD$ 0.00</div>
                </div>
            </div>
        `;

        // Remove button
        div.querySelector('.remove-repuesto').addEventListener('click', function () {
            div.remove();
            toggleEmptyState();
            updateCostFromRepuestos();
        });

        // Subtotal calculation
        const cantInput = div.querySelector('.repuesto-cantidad');
        const precInput = div.querySelector('.repuesto-precio');
        const subDisplay = div.querySelector('.subtotal-display');

        function updateSubtotal() {
            const cant = parseFloat(cantInput.value) || 0;
            const prec = parseFloat(precInput.value) || 0;
            subDisplay.textContent = 'RD$ ' + (cant * prec).toFixed(2);
            updateCostFromRepuestos();
        }

        cantInput.addEventListener('input', updateSubtotal);
        precInput.addEventListener('input', updateSubtotal);
        updateSubtotal();

        return div;
    }

    // Add repuesto
    addBtn.addEventListener('click', function () {
        const row = createRepuestoRow(repuestoIndex++);
        container.appendChild(row);
        toggleEmptyState();
    });

    // Toggle empty state
    function toggleEmptyState() {
        const hasRows = container.querySelectorAll('.repuesto-row').length > 0;
        noRepuestos.classList.toggle('d-none', hasRows);
    }

    // Update costo_repuestos from sum of repuesto line totals
    function updateCostFromRepuestos() {
        let total = 0;
        container.querySelectorAll('.repuesto-row').forEach(function (row) {
            const cant = parseFloat(row.querySelector('.repuesto-cantidad')?.value) || 0;
            const prec = parseFloat(row.querySelector('.repuesto-precio')?.value) || 0;
            total += cant * prec;
        });
        costInput.value = total.toFixed(2);
        updateTotal();
    }

    // Init existing old rows subtotals
    container.querySelectorAll('.repuesto-row').forEach(function (row) {
        const idx = row.dataset.index;
        const removeBtn = row.querySelector('.remove-repuesto');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                toggleEmptyState();
                updateCostFromRepuestos();
            });
        }
        const cantInput = row.querySelector('.repuesto-cantidad');
        const precInput = row.querySelector('.repuesto-precio');
        const subDisplay = row.querySelector('.subtotal-display');

        function updateSubtotal() {
            const cant = parseFloat(cantInput.value) || 0;
            const prec = parseFloat(precInput.value) || 0;
            subDisplay.textContent = 'RD$ ' + (cant * prec).toFixed(2);
            updateCostFromRepuestos();
        }
        if (cantInput) cantInput.addEventListener('input', updateSubtotal);
        if (precInput) precInput.addEventListener('input', updateSubtotal);
        if (cantInput && precInput) updateSubtotal();
    });

    toggleEmptyState();
});
</script>
@endpush
