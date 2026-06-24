@php
    $isEdit = !empty($conduce);
    $items = old('items', $isEdit ? $conduce->items->toArray() : []);
@endphp

<div class="row g-3">
    {{-- Información General --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-1"></i>Información General</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cliente_id" class="form-label">Cliente <span class="required-indicator">*</span></label>
                        <select id="cliente_id" name="cliente_id" class="form-select" required>
                            <option value="">Seleccionar cliente…</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}"
                                    {{ (old('cliente_id', $conduce->cliente_id ?? '') == $c->id) ? 'selected' : '' }}>
                                    {{ $c->nombre }}{{ $c->rnc_cedula ? ' · ' . $c->rnc_cedula : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha" class="form-label">Fecha de emisión <span class="required-indicator">*</span></label>
                        <input type="date" id="fecha" name="fecha" class="form-control"
                               value="{{ old('fecha', $conduce?->fecha?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_entrega" class="form-label">Fecha estimada</label>
                        <input type="date" id="fecha_entrega" name="fecha_entrega" class="form-control"
                               value="{{ old('fecha_entrega', $conduce?->fecha_entrega?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado" class="form-select">
                            @foreach(\App\Models\Conduce::ESTADOS as $key => $est)
                                @if($key !== 'entregado' || ($isEdit && $conduce->estado === 'entregado'))
                                <option value="{{ $key }}"
                                    {{ old('estado', $conduce->estado ?? 'borrador') === $key ? 'selected' : '' }}>
                                    {{ $est['label'] }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dirección de Entrega --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-1"></i>Dirección de Entrega</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="direccion_entrega" class="form-label">Dirección <span class="required-indicator">*</span></label>
                    <input type="text" id="direccion_entrega" name="direccion_entrega" class="form-control"
                           value="{{ old('direccion_entrega', $conduce->direccion_entrega ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label for="referencia" class="form-label">Referencia</label>
                    <input type="text" id="referencia" name="referencia" class="form-control"
                           value="{{ old('referencia', $conduce->referencia ?? '') }}"
                           placeholder="Ej: Casa con reja azul, frente al colmado...">
                </div>
                <div class="row g-2">
                    <div class="col-md-7">
                        <label for="contacto_entrega" class="form-label">Contacto</label>
                        <input type="text" id="contacto_entrega" name="contacto_entrega" class="form-control"
                               value="{{ old('contacto_entrega', $conduce->contacto_entrega ?? '') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="telefono_entrega" class="form-label">Teléfono</label>
                        <input type="tel" id="telefono_entrega" name="telefono_entrega" class="form-control"
                               value="{{ old('telefono_entrega', $conduce->telefono_entrega ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transporte --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-truck me-1"></i>Transporte</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="transportista" class="form-label">Transportista / Empresa</label>
                    <input type="text" id="transportista" name="transportista" class="form-control"
                           value="{{ old('transportista', $conduce->transportista ?? '') }}">
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="vehiculo" class="form-label">Vehículo</label>
                        <input type="text" id="vehiculo" name="vehiculo" class="form-control"
                               value="{{ old('vehiculo', $conduce->vehiculo ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" id="placa" name="placa" class="form-control"
                               value="{{ old('placa', $conduce->placa ?? '') }}">
                    </div>
                    <div class="col-md-7">
                        <label for="chofer" class="form-label">Chofer</label>
                        <input type="text" id="chofer" name="chofer" class="form-control"
                               value="{{ old('chofer', $conduce->chofer ?? '') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="chofer_cedula" class="form-label">Cédula</label>
                        <input type="text" id="chofer_cedula" name="chofer_cedula" class="form-control"
                               value="{{ old('chofer_cedula', $conduce->chofer_cedula ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam me-1"></i>Productos
                    <span class="badge bg-secondary ms-2" id="itemsCount">0 items</span>
                </h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalProductos"
                        aria-label="Buscar y agregar productos">
                    <i class="bi bi-plus-circle me-1"></i>Agregar Productos
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaItems">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Producto</th>
                            <th class="text-center" style="width: 130px;">Cantidad</th>
                            <th class="text-center" style="width: 100px;">Unidad</th>
                            <th class="text-center" style="width: 110px;">Peso (kg)</th>
                            <th class="text-center" style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- Render via JS --}}
                    </tbody>
                </table>
            </div>
            <div class="card-body bg-light">
                <div class="row g-2 text-center">
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold d-block">Total Items</small>
                        <span class="fs-5 fw-bold" id="totalItemsLabel">0</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold d-block">Cantidad Total</small>
                        <span class="fs-5 fw-bold" id="totalCantidadLabel">0</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold d-block">Peso Total</small>
                        <span class="fs-5 fw-bold" id="totalPesoLabel">0.00 kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Observaciones --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                <textarea id="observaciones" name="observaciones" class="form-control" rows="3"
                          placeholder="Notas internas o indicaciones especiales...">{{ old('observaciones', $conduce->observaciones ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Buscar Productos --}}
<div class="modal fade" id="modalProductos" tabindex="-1" aria-labelledby="modalProductosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductosLabel">Buscar Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="searchProducto" class="form-label">Buscar por código o nombre</label>
                    <input type="search" id="searchProducto" class="form-control" placeholder="Escribe para buscar...">
                </div>
                <div id="productosResultados" class="list-group" style="max-height: 50vh; overflow-y: auto;">
                    {{-- Resultados via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .required-indicator { color: #dc3545; }
    #itemsBody tr td { vertical-align: middle; }
    .producto-search-item { cursor: pointer; }
    .producto-search-item:hover { background: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
window.productosCatalogo = @json($productos);
</script>
@endpush
@endonce
