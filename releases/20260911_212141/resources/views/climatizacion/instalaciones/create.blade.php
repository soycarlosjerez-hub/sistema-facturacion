@extends('layouts.app')

@section('title', 'Nueva Instalación')

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-card-title { color: #f1f5f9; }
body.dark-mode .ui-card-subtitle { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Nueva Instalación</h1>
                    <div class="ui-header-meta">
                        <span>Registrar una nueva instalación de climatización</span>
                        <span class="divider">·</span>
                        <a href="{{ route('climatizacion.instalaciones.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s;max-width:960px;margin:0 auto;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-file-earmark-plus"></i> Datos de la Instalación
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Completa la información para registrar la instalación</div>
        </div>

        <div class="ui-card-body">
            <form action="{{ route('climatizacion.instalaciones.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="ui-label">Cliente</label>
                        <select name="cliente_id" class="ui-select @error('cliente_id') is-invalid @enderror">
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label">Tipo de Inmueble <span class="text-danger">*</span></label>
                        <select name="tipo_inmueble" class="ui-select @error('tipo_inmueble') is-invalid @enderror" required>
                            <option value="">Seleccionar</option>
                            @foreach (\App\Models\Instalacion::TIPOS_INMUEBLE as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo_inmueble') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_inmueble') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="ui-label">Programada Para</label>
                        <input type="datetime-local" name="programada_para" class="ui-input @error('programada_para') is-invalid @enderror" value="{{ old('programada_para') }}">
                        @error('programada_para') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="ui-input @error('direccion_instalacion') is-invalid @enderror" value="{{ old('direccion_instalacion') }}" placeholder="Dirección donde se realizará la instalación" maxlength="300">
                        @error('direccion_instalacion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="ui-label">Nota Interna</label>
                        <textarea name="nota_interna" class="ui-textarea @error('nota_interna') is-invalid @enderror" rows="3" placeholder="Instrucciones u observaciones internas..." maxlength="2000">{{ old('nota_interna') }}</textarea>
                        @error('nota_interna') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-size:.95rem;font-weight:600;"><i class="bi bi-box-seam me-2" style="color:#06b6d4;"></i>Productos de la Instalación</h5>
                        <button type="button" class="ui-btn ui-btn-primary ui-btn-sm" id="addProductRow">
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

                <div class="ui-sticky-bar" style="position:sticky;bottom:0;left:0;right:0;background:rgba(255,255,255,.85);backdrop-filter:blur(20px);border-top:2px solid #06b6d4;padding:.7rem 1.5rem;z-index:1050;box-shadow:0 -4px 20px rgba(0,0,0,.08);margin:0 -1.75rem -1.5rem;border-radius:0 0 var(--radius-2xl) var(--radius-2xl);">
                    <div class="ui-sticky-bar-inner">
                        <a href="{{ route('climatizacion.instalaciones.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i> Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-solid"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
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
        row.querySelector('.product-subtotal').textContent = '$' + (cantidad * precio).toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        container.querySelectorAll('.product-row').forEach(function (row) {
            total += (parseFloat(row.querySelector('.product-cantidad').value) || 0) * (parseFloat(row.querySelector('.product-precio').value) || 0);
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
        tr.querySelector('.remove-product-row').addEventListener('click', function () { tr.remove(); updateTotal(); });

        const select = tr.querySelector('.product-select');
        if (select.value) select.dispatchEvent(new Event('change'));
        updateSubtotal(tr);
    }

    addBtn.addEventListener('click', function () { addRow(); });

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
        if (btn) btn.addEventListener('click', function () { row.remove(); updateTotal(); });
        updateSubtotal(row);
    });

    container.querySelectorAll('.product-row .product-select').forEach(function (sel) {
        if (sel.value) sel.dispatchEvent(new Event('change'));
    });
});
</script>
@endpush
