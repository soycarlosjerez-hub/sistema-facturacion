<div class="card-body p-4 p-md-5">
    <div class="row g-4">
        <!-- Columna izquierda: Información básica -->
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-control @error('nombre') is-invalid @enderror" required placeholder="Ej. Arroz Campo 5lbs">
                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Código de barras</label>
                <input type="text" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" class="form-control @error('codigo_barras') is-invalid @enderror" placeholder="Escanear o generar" autocomplete="off">
                    <button type="button" class="btn btn-outline-primary px-3 rounded-pill" id="btnGenerarBarcode" title="Generar código automático">
                        <i class="bi bi-magic"></i>
                    </button>
                @error('codigo_barras')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">Escanea con tu lector o haz clic en <i class="bi bi-magic"></i> para generar uno único.</small>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Detalles del producto...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Categoría</label>
                <select name="categoria_id" class="form-select">
                    <option value="">Sin categoría</option>
                    @if(isset($categorias))
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Unidad de medida</label>
                <select name="unidad_medida" class="form-select">
                        @php $unidad = old('unidad_medida', $producto->unidad_medida ?? 'Unidad'); @endphp
                        @foreach(['Unidad', 'Libra', 'Kilogramo', 'Litro', 'Galón', 'Caja', 'Paquete', 'Docena', 'Bulto'] as $op)
                            <option value="{{ $op }}" {{ $unidad == $op ? 'selected' : '' }}>{{ $op }}</option>
                        @endforeach
                    </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Imagen del producto</label>
                <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                @error('imagen')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                @if(isset($producto))
                    <div class="mt-2 p-2 border rounded-3 bg-light d-inline-block position-relative">
                        <img src="{{ $producto->imagen_url }}" width="100" height="100" style="object-fit: cover;" class="rounded shadow-sm" alt="Imagen actual">
                        <small class="d-block text-muted mt-1">{{ $producto->tiene_imagen ? 'Imagen actual' : 'Sin imagen (se mostrará placeholder)' }}</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Columna derecha: Precios y Stock -->
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Precio de venta <span class="text-danger">*</span></label>
                <input type="number" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="form-control @error('precio') is-invalid @enderror" step="0.01" min="0" required placeholder="0.00">
                @error('precio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Precio de compra</label>
                <input type="number" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}" class="form-control @error('precio_compra') is-invalid @enderror" step="0.01" min="0" placeholder="0.00">
                @error('precio_compra')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">ITBIS %</label>
                <input type="number" name="itbis_porcentaje" value="{{ old('itbis_porcentaje', $producto->itbis_porcentaje ?? '18.00') }}" class="form-control @error('itbis_porcentaje') is-invalid @enderror" step="0.01" min="0" max="100" placeholder="18">
                @error('itbis_porcentaje')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <small class="text-muted">Por defecto 18% (República Dominicana). Use 0 para productos exentos.</small>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Stock actual <span class="text-danger">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? '') }}" class="form-control @error('stock') is-invalid @enderror" required min="0" placeholder="0">
                        @error('stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Stock mínimo</label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? '0') }}" class="form-control" min="0" placeholder="0">
                        <small class="text-muted">Alerta cuando el stock baje de este valor</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
