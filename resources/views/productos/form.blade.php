<div class="card-body p-4 p-md-5">
    {{-- Section 1: Información Básica --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #4f46e5;">
            <i class="bi bi-box-seam me-2"></i>Información Básica
        </h6>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-control form-control-lg @error('nombre') is-invalid @enderror" required placeholder="Ej. Arroz Campo 5lbs">
                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Código de Barras</label>
                <div class="input-group input-group-lg">
                    <input type="text" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" class="form-control @error('codigo_barras') is-invalid @enderror" placeholder="Escanear o generar" autocomplete="off">
                    <button class="btn btn-outline-primary px-3" type="button" id="btnGenerarBarcode" title="Generar código de barras">
                        <i class="bi bi-magic"></i>
                    </button>
                </div>
                @error('codigo_barras')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">Escanea con tu lector o haz clic en <i class="bi bi-magic"></i> para generar uno único.</small>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Detalles del producto...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Categoría</label>
                <select name="categoria_id" class="form-select form-select-lg">
                    <option value="">Sin categoría</option>
                    @if(isset($categorias))
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Unidad de Medida</label>
                <select name="unidad_medida" class="form-select form-select-lg">
                    @php $unidad = old('unidad_medida', $producto->unidad_medida ?? 'Unidad'); @endphp
                    @foreach(['Unidad', 'Libra', 'Kilogramo', 'Litro', 'Galón', 'Caja', 'Paquete', 'Docena', 'Bulto'] as $op)
                        <option value="{{ $op }}" {{ $unidad == $op ? 'selected' : '' }}>{{ $op }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Section 2: Precios y Existencias --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #059669;">
            <i class="bi bi-currency-dollar me-2"></i>Precios y Existencias
        </h6>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light fw-bold">$</span>
                    <input type="number" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="form-control @error('precio') is-invalid @enderror" step="0.01" min="0" required placeholder="0.00">
                </div>
                @error('precio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Precio de Compra</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light fw-bold">$</span>
                    <input type="number" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}" class="form-control @error('precio_compra') is-invalid @enderror" step="0.01" min="0" placeholder="0.00">
                </div>
                @error('precio_compra')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">ITBIS</label>
                <div class="input-group input-group-lg">
                    <input type="number" name="itbis_porcentaje" value="{{ old('itbis_porcentaje', $producto->itbis_porcentaje ?? '18.00') }}" class="form-control @error('itbis_porcentaje') is-invalid @enderror" step="0.01" min="0" max="100" placeholder="18">
                    <span class="input-group-text bg-light fw-bold">%</span>
                </div>
                @error('itbis_porcentaje')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">18% por defecto. Usa 0 para productos exentos.</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Stock Actual <span class="text-danger">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? '') }}" class="form-control form-control-lg @error('stock') is-invalid @enderror" required min="0" placeholder="0">
                @error('stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Stock Mínimo</label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? '0') }}" class="form-control form-control-lg" min="0" placeholder="0">
                <small class="text-muted">Recibirás alerta cuando el stock baje de este valor.</small>
            </div>
        </div>
    </div>

    {{-- Section 3: Imagen del Producto --}}
    <div class="mb-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color: #0891b2;">
            <i class="bi bi-image me-2"></i>Imagen del Producto
        </h6>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <input type="file" name="imagen" class="form-control form-control-lg @error('imagen') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
            @error('imagen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted d-block">Formatos: JPG, PNG, WEBP. Máx. 2 MB.</small>
            <small class="text-success-emphasis fw-semibold"><i class="bi bi-arrow-down-circle me-1"></i>Se comprimirá automáticamente a WebP (máx. 800px, calidad 70%)</small>
        </div>
        @if(isset($producto))
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <img src="{{ $producto->imagen_url }}" width="100" height="100" style="object-fit: cover; border-radius: 10px;" class="shadow-sm border" alt="Imagen actual">
                <div>
                    <strong class="d-block small">{{ $producto->tiene_imagen ? 'Imagen actual' : 'Sin imagen' }}</strong>
                    <span class="text-muted small">Sube una nueva imagen para reemplazarla.</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
