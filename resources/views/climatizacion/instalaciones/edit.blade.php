@extends('layouts.app')

@section('title', 'Editar ' . $instalacion->numero)

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-tools me-2"></i>Editar: {{ $instalacion->numero }}</h2>
            <p class="text-muted mb-0">Modificar instalación de climatización</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('climatizacion.instalaciones.update', $instalacion) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $instalacion->cliente_id) == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de Inmueble <span class="text-danger">*</span></label>
                        <select name="tipo_inmueble" class="form-select @error('tipo_inmueble') is-invalid @enderror" required>
                            <option value="">Seleccionar</option>
                            @foreach (\App\Models\Instalacion::TIPOS_INMUEBLE as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo_inmueble', $instalacion->tipo_inmueble) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_inmueble') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            @foreach (\App\Models\Instalacion::ESTADOS as $key => $label)
                                <option value="{{ $key }}" {{ old('estado', $instalacion->estado) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Programada Para</label>
                        <input type="datetime-local" name="programada_para" class="form-control @error('programada_para') is-invalid @enderror"
                            value="{{ old('programada_para', $instalacion->programada_para ? $instalacion->programada_para->format('Y-m-d\TH:i') : '') }}">
                        @error('programada_para') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Completada En</label>
                        <input type="datetime-local" name="completada_en" class="form-control @error('completada_en') is-invalid @enderror"
                            value="{{ old('completada_en', $instalacion->completada_en ? $instalacion->completada_en->format('Y-m-d\TH:i') : '') }}">
                        @error('completada_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="form-control @error('direccion_instalacion') is-invalid @enderror"
                            value="{{ old('direccion_instalacion', $instalacion->direccion_instalacion) }}" placeholder="Dirección donde se realizará la instalación" maxlength="300">
                        @error('direccion_instalacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nota Interna</label>
                        <textarea name="nota_interna" class="form-control @error('nota_interna') is-invalid @enderror" rows="3" placeholder="Instrucciones u observaciones internas..." maxlength="2000">{{ old('nota_interna', $instalacion->nota_interna) }}</textarea>
                        @error('nota_interna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Productos de la Instalación</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addProductRow">
                            <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="productosTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:45%;">Producto</th>
                                    <th style="width:15%;">Cantidad</th>
                                    <th style="width:20%;">Precio Unitario</th>
                                    <th style="width:15%;">Subtotal</th>
                                    <th style="width:5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="productosContainer">
                                @if (old('productos'))
                                    @foreach (old('productos') as $index => $prod)
                                    <tr class="product-row">
                                        <td>
                                            <select name="productos[{{ $index }}][producto_id]" class="form-select form-select-sm product-select">
                                                <option value="">Seleccionar</option>
                                                @foreach ($productos as $p)
                                                    <option value="{{ $p->id }}" {{ $prod['producto_id'] == $p->id ? 'selected' : '' }} data-precio="{{ $p->precio_venta ?? 0 }}">
                                                        {{ $p->nombre }} @if($p->codigo)- {{ $p->codigo }}@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="productos[{{ $index }}][cantidad]" class="form-control form-control-sm product-cantidad" value="{{ $prod['cantidad'] ?? 1 }}" min="1" step="1">
                                        </td>
                                        <td>
                                            <input type="number" name="productos[{{ $index }}][precio_unitario]" class="form-control form-control-sm product-precio" value="{{ $prod['precio_unitario'] ?? 0 }}" min="0" step="0.01">
                                        </td>
                                        <td class="product-subtotal text-end fw-medium">$0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    @foreach ($instalacion->productos as $producto)
                                    <tr class="product-row">
                                        <td>
                                            <select name="productos[{{ $loop->index }}][producto_id]" class="form-select form-select-sm product-select">
                                                <option value="">Seleccionar</option>
                                                @foreach ($productos as $p)
                                                    <option value="{{ $p->id }}" {{ $producto->id == $p->id ? 'selected' : '' }} data-precio="{{ $p->precio_venta ?? 0 }}">
                                                        {{ $p->nombre }} @if($p->codigo)- {{ $p->codigo }}@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="productos[{{ $loop->index }}][cantidad]" class="form-control form-control-sm product-cantidad" value="{{ $producto->pivot->cantidad }}" min="1" step="1">
                                        </td>
                                        <td>
                                            <input type="number" name="productos[{{ $loop->index }}][precio_unitario]" class="form-control form-control-sm product-precio" value="{{ $producto->pivot->precio_unitario }}" min="0" step="0.01">
                                        </td>
                                        <td class="product-subtotal text-end fw-medium">$0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end" id="productosTotal">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('productos') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('productos.*.producto_id') <div class="text-danger small">Debe seleccionar un producto válido.</div> @enderror
                </div>

                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
                    <a href="{{ route('climatizacion.instalaciones.show', $instalacion) }}" class="btn btn-outline-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productosContainer');
    const addBtn = document.getElementById('addProductRow');
    const productosOptions = `@foreach($productos as $p)
        <option value="{{ $p->id }}" data-precio="{{ $p->precio_venta ?? 0 }}">{{ $p->nombre }} @if($p->codigo)- {{ $p->codigo }}@endif</option>
    @endforeach`;

    let rowIndex = container.querySelectorAll('.product-row').length;

    function updateSubtotal(row) {
        const cantidad = parseFloat(row.querySelector('.product-cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.product-precio').value) || 0;
        const subtotal = cantidad * precio;
        row.querySelector('.product-subtotal').textContent = '$' + subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        container.querySelectorAll('.product-row').forEach(function (row) {
            const cantidad = parseFloat(row.querySelector('.product-cantidad').value) || 0;
            const precio = parseFloat(row.querySelector('.product-precio').value) || 0;
            total += cantidad * precio;
        });
        document.getElementById('productosTotal').textContent = '$' + total.toFixed(2);
    }

    function addRow(data) {
        const tr = document.createElement('tr');
        tr.className = 'product-row';
        const i = rowIndex++;
        tr.innerHTML = `
            <td>
                <select name="productos[${i}][producto_id]" class="form-select form-select-sm product-select">
                    <option value="">Seleccionar</option>
                    ${productosOptions}
                </select>
            </td>
            <td>
                <input type="number" name="productos[${i}][cantidad]" class="form-control form-control-sm product-cantidad" value="${data ? data.cantidad : 1}" min="1" step="1">
            </td>
            <td>
                <input type="number" name="productos[${i}][precio_unitario]" class="form-control form-control-sm product-precio" value="${data ? data.precio_unitario : 0}" min="0" step="0.01">
            </td>
            <td class="product-subtotal text-end fw-medium">$0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-product-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        container.appendChild(tr);

        if (data && data.producto_id) {
            const select = tr.querySelector('.product-select');
            select.value = data.producto_id;
        }

        tr.querySelector('.product-cantidad').addEventListener('input', function () { updateSubtotal(tr); });
        tr.querySelector('.product-precio').addEventListener('input', function () { updateSubtotal(tr); });
        tr.querySelector('.product-select').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.precio) {
                const precioInput = tr.querySelector('.product-precio');
                if (!precioInput.value || parseFloat(precioInput.value) === 0) {
                    precioInput.value = parseFloat(opt.dataset.precio).toFixed(2);
                }
            }
            updateSubtotal(tr);
        });
        tr.querySelector('.remove-product-row').addEventListener('click', function () {
            tr.remove();
            updateTotal();
        });

        const select = tr.querySelector('.product-select');
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        }
        updateSubtotal(tr);
    }

    addBtn.addEventListener('click', function () {
        addRow();
    });

    // Initialize existing rows
    container.querySelectorAll('.product-row').forEach(function (row) {
        const cantidad = row.querySelector('.product-cantidad');
        const precio = row.querySelector('.product-precio');
        const select = row.querySelector('.product-select');

        cantidad.addEventListener('input', function () { updateSubtotal(row); });
        precio.addEventListener('input', function () { updateSubtotal(row); });
        select.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.precio) {
                const precioInput = row.querySelector('.product-precio');
                if (!precioInput.value || parseFloat(precioInput.value) === 0) {
                    precioInput.value = parseFloat(opt.dataset.precio).toFixed(2);
                }
            }
            updateSubtotal(row);
        });

        const btn = row.querySelector('.remove-product-row');
        if (btn) {
            btn.addEventListener('click', function () {
                row.remove();
                updateTotal();
            });
        }

        updateSubtotal(row);
    });

    container.querySelectorAll('.product-row .product-select').forEach(function (sel) {
        if (sel.value) {
            sel.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endpush
