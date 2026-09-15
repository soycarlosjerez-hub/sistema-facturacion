@extends('layouts.app')
@section('title', 'Nueva Transferencia')

@push('styles')
@include('partials.premium-ui')
<style>
.producto-row { transition: background .15s; }
.producto-row:hover { background: rgba(59,130,246,.04); }
#productosTable tbody td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <h4 class="ui-header-title">Nueva Transferencia</h4>
                    <div class="ui-header-meta"><i class="bi bi-plus-circle me-1"></i><span>Transfiere productos entre sucursales</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-transfers.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

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
        <div class="ui-card-body">
            <form action="{{ route('stock-transfers.store') }}" method="POST" id="transferForm">
                @csrf

                <!-- Sucursales -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="ui-label">Sucursal Origen <span class="text-danger">*</span></label>
                        <select name="sucursal_origen_id" id="origenSelect" class="ui-select" required>
                            <option value="" disabled selected>Seleccionar origen...</option>
                            @foreach($sucursales as $s)
                                <option value="{{ $s->id }}" {{ old('sucursal_origen_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Sucursal Destino <span class="text-danger">*</span></label>
                        <select name="sucursal_destino_id" id="destinoSelect" class="ui-select" required>
                            <option value="" disabled selected>Seleccionar destino...</option>
                            @foreach($sucursales as $s)
                                <option value="{{ $s->id }}" {{ old('sucursal_destino_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Productos a Transferir</h6>
                        <button type="button" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" id="addProductoBtn">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Producto
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" id="productosTable">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Producto</th>
                                    <th style="width: 25%">Cantidad</th>
                                    <th style="width: 10%"></th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <!-- Se agregan filas dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notas -->
                <div class="mb-4">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" class="ui-input" rows="3" maxlength="500" placeholder="Notas adicionales...">{{ old('notas') }}</textarea>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('stock-transfers.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="transferForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Crear Transferencia
        </button>
    </div>
</div>

@push('scripts')
<script>
let productoIndex = 0;

function addProductoRow() {
    const row = document.createElement('tr');
    row.className = 'producto-row';
    row.innerHTML = `
        <td>
            <select name="productos[${productoIndex}][id]" class="ui-select form-select" required>
                <option value="">Seleccionar...</option>
                @foreach($productos as $prod)
                <option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="productos[${productoIndex}][cantidad]" class="ui-input" value="1" min="1" required></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('productosBody').appendChild(row);
    productoIndex++;
}

document.getElementById('addProductoBtn').addEventListener('click', addProductoRow);

document.getElementById('productosBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        e.target.closest('.remove-row').closest('tr').remove();
    }
});

// Prevent origen = destino
document.getElementById('origenSelect')?.addEventListener('change', function() {
    const destino = document.getElementById('destinoSelect');
    for (let opt of destino.options) {
        if (opt.value === this.value) {
            opt.disabled = true;
            if (destino.value === this.value) destino.value = '';
        } else {
            opt.disabled = false;
        }
    }
});

// Add first row on load
if (document.getElementById('productosBody').children.length === 0) {
    addProductoRow();
}
</script>
@endpush
@endsection
