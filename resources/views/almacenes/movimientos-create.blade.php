@extends('layouts.app')
@section('title', 'Nuevo movimiento')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .status-badge {
        padding: 0.4em 0.8em; border-radius: 2rem;
        font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

            {{-- Header --}}
            <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="bi bi-arrow-down-up me-3 fs-1 opacity-75"></i>Nuevo Movimiento
                    </h2>
                    <p class="mb-0 opacity-75 fs-5">Registra una entrada, salida o traslado de inventario</p>
                </div>
                <div>
                    <a href="{{ route('almacenes.movimientos') }}" class="btn btn-light text-white bg-white bg-opacity-25 border-0 rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            {{-- Error general --}}
            @error('error')
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}
                </div>
            @enderror

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Detalles del movimiento</h5>
                </div>

                <form action="{{ route('almacenes.movimientos.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">

                        {{-- Producto --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Producto</label>
                            <select name="producto_id" id="select-producto" class="form-select @error('producto_id') is-invalid @enderror" required>
                                <option value="">Seleccione un producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" @selected(old('producto_id') == $producto->id)>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Stock info panel --}}
                        <div id="stock-info" class="mb-4 d-none">
                            <div class="bg-light rounded-3 p-3 border">
                                <h6 class="fw-bold mb-2"><i class="bi bi-box-seam me-1"></i>Stock disponible por almacén</h6>
                                <div id="stock-list" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tipo de movimiento</label>
                                    <select name="tipo" id="select-tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="entrada" @selected(old('tipo') == 'entrada')> Entrada</option>
                                        <option value="salida" @selected(old('tipo') == 'salida')> Salida</option>
                                        <option value="traslado" @selected(old('tipo') == 'traslado')> Traslado</option>
                                    </select>
                                    @error('tipo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <input type="number" name="cantidad" id="input-cantidad" class="form-control @error('cantidad') is-invalid @enderror" min="1" value="{{ old('cantidad') }}" required placeholder="0">
                                    @error('cantidad')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Almacén (entrada/salida) --}}
                        <div id="field-almacen-simple" class="mb-4">
                            <label class="form-label fw-semibold">Almacén</label>
                            <select name="almacen_id" class="form-select @error('almacen_id') is-invalid @enderror" required>
                                <option value="">Seleccione un almacén</option>
                                @foreach($almacenes as $a)
                                    <option value="{{ $a->id }}" @selected(old('almacen_id') == $a->id)>
                                        {{ $a->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Almacén origen / destino (traslado) --}}
                        <div id="field-almacen-traslado" class="row g-3 mb-4 d-none">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Almacén origen <i class="bi bi-arrow-right text-muted small"></i></label>
                                <select name="almacen_origen_id" class="form-select @error('almacen_origen_id') is-invalid @enderror">
                                    <option value="">Seleccione origen</option>
                                    @foreach($almacenes as $a)
                                        <option value="{{ $a->id }}" @selected(old('almacen_origen_id') == $a->id)>
                                            {{ $a->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('almacen_origen_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="bi bi-arrow-right text-muted small"></i> Almacén destino</label>
                                <select name="almacen_destino_id" class="form-select @error('almacen_destino_id') is-invalid @enderror">
                                    <option value="">Seleccione destino</option>
                                    @foreach($almacenes as $a)
                                        <option value="{{ $a->id }}" @selected(old('almacen_destino_id') == $a->id)>
                                            {{ $a->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('almacen_destino_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nota --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nota / Motivo</label>
                            <input type="text" name="nota" class="form-control @error('nota') is-invalid @enderror" value="{{ old('nota') }}" placeholder="Ej: Ajuste de inventario, compra a proveedor, etc.">
                            @error('nota')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('almacenes.movimientos') }}" class="btn btn-light rounded-pill px-4 fw-semibold me-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold" id="btn-submit">
                            <i class="bi bi-check-lg me-2"></i>Guardar Movimiento
                        </button>
                    </div>
                </form>
            </div>

</div>

@push('scripts')
<script>
var almacenes = @json($almacenes->map(function($a) { return ['id' => $a->id, 'nombre' => $a->nombre]; }));
var stocksData = @json($stocksData ?? []);

function actualizarStock() {
    var productoId = document.getElementById('select-producto').value;
    var panel = document.getElementById('stock-info');
    var lista = document.getElementById('stock-list');

    if (!productoId) {
        panel.classList.add('d-none');
        return;
    }

    var stocks = stocksData[productoId] || {};
    var html = '';
    var algunStock = false;

    almacenes.forEach(function(a) {
        var stock = stocks[a.id] || 0;
        if (stock > 0) algunStock = true;
        var badgeClass = stock > 0 ? 'bg-success text-white' : 'bg-light text-muted';
        html += '<span class="badge rounded-pill px-3 py-2 ' + badgeClass + '">'
            + a.nombre + ': <strong>' + stock + '</strong></span>';
    });

    lista.innerHTML = html;
    panel.classList.remove('d-none');

    document.getElementById('field-almacen-traslado').querySelectorAll('select').forEach(function(sel) {
        sel.querySelectorAll('option').forEach(function(opt) {
            if (opt.value) {
                var almacenId = parseInt(opt.value);
                if (stocks[almacenId] === undefined || stocks[almacenId] === 0) {
                    opt.textContent = opt.textContent.replace(/ \(.*\)/, '') + ' (Stock: 0)';
                } else {
                    opt.textContent = opt.textContent.replace(/ \(.*\)/, '') + ' (Stock: ' + stocks[almacenId] + ')';
                }
            }
        });
    });
}

function toggleTipo() {
    var tipo = document.getElementById('select-tipo').value;
    var simple = document.getElementById('field-almacen-simple');
    var traslado = document.getElementById('field-almacen-traslado');
    var btn = document.getElementById('btn-submit');

    simple.querySelector('select').required = (tipo !== 'traslado');
    traslado.querySelectorAll('select').forEach(function(s) { s.required = (tipo === 'traslado'); });

    if (tipo === 'traslado') {
        simple.classList.add('d-none');
        traslado.classList.remove('d-none');
        btn.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Realizar Traslado';
    } else {
        traslado.classList.add('d-none');
        simple.classList.remove('d-none');
        btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Guardar Movimiento';
    }
}

function validarFormulario(e) {
    var tipo = document.getElementById('select-tipo').value;
    if (tipo === 'traslado') {
        var origen = document.querySelector('[name="almacen_origen_id"]').value;
        var destino = document.querySelector('[name="almacen_destino_id"]').value;
        if (origen === destino) {
            e.preventDefault();
            alert('El almacén de origen y destino deben ser diferentes.');
            return false;
        }
        var productoId = document.getElementById('select-producto').value;
        var cantidad = parseInt(document.getElementById('input-cantidad').value);
        var stocks = stocksData[productoId] || {};
        var stockOrigen = stocks[parseInt(origen)] || 0;
        if (cantidad > stockOrigen) {
            e.preventDefault();
            alert('Stock insuficiente en el almacén de origen. Disponible: ' + stockOrigen);
            return false;
        }
    }
}

document.getElementById('select-producto').addEventListener('change', actualizarStock);
document.getElementById('select-tipo').addEventListener('change', toggleTipo);

document.querySelector('form').addEventListener('submit', validarFormulario);

// Inicializar estado según old values
toggleTipo();
if (document.getElementById('select-producto').value) actualizarStock();
</script>
@endpush
@endsection
