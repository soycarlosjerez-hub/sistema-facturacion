@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Nueva Orden</h1>
        <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5>Datos de la Orden</h5>
                </div>
                <div class="card-body">
                    <form id="ordenForm" action="{{ route('ordenes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="items" id="itemsInput" value="[]">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Orden</label>
                            <select name="tipo_orden" class="form-select" required id="tipo_orden">
                                <option value="mostrador">Mostrador</option>
                                <option value="delivery">Delivery</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>

                        <div id="delivery_fields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Dirección de Entrega</label>
                                <textarea name="direccion_entrega" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Empresa de Delivery</label>
                                <select name="entrega_empresa_id" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(\App\Models\DeliveryCompany::where('activo', true)->get() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="pickup_fields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Hora de Retiro</label>
                                <input type="datetime-local" name="hora_retiro" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3" id="contacto_fields">
                            <label class="form-label">Teléfono de Contacto</label>
                            <input type="text" name="telefono_contacto" class="form-control" maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="cliente_id" class="form-select" id="cliente_select">
                                <option value="">Consumidor Final</option>
                                @foreach(\App\Models\Cliente::orderBy('nombre')->limit(50)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Crear Orden
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Buscar Productos</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" id="producto_search" class="form-control" placeholder="Buscar por nombre o código...">
                        <button class="btn btn-outline-secondary" type="button" id="search_btn">Buscar</button>
                    </div>
                    <div id="producto_resultados" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Productos en la Orden</h5>
                    <span class="badge bg-primary rounded-pill" id="cart_count">0</span>
                </div>
                <div class="card-body">
                    <div id="cart_empty" class="text-muted text-center py-3">
                        No hay productos seleccionados
                    </div>
                    <div id="cart_container" style="display:none;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th width="100">Cant</th>
                                    <th width="90">Precio</th>
                                    <th width="90">Subtotal</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody id="cart_items"></tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">Total</th>
                                    <th id="cart_total">RD$ 0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

document.getElementById('tipo_orden').addEventListener('change', function() {
    document.getElementById('delivery_fields').style.display = this.value === 'delivery' ? 'block' : 'none';
    document.getElementById('pickup_fields').style.display = this.value === 'pickup' ? 'block' : 'none';
    document.getElementById('contacto_fields').style.display = this.value !== 'mostrador' ? 'block' : 'none';
});

let searchTimeout;
document.getElementById('producto_search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const term = this.value;
    if (term.length < 2) return;
    searchTimeout = setTimeout(() => buscarProductos(term), 300);
});

document.getElementById('search_btn').addEventListener('click', function() {
    const term = document.getElementById('producto_search').value;
    if (term.length >= 2) buscarProductos(term);
});

function buscarProductos(q) {
    fetch(`{{ route('ordenes.buscarProducto') }}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('producto_resultados');
            container.innerHTML = data.map(p => {
                const inCart = cart.find(c => c.producto_id === p.id);
                return `<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${inCart ? 'active' : ''}"
                   data-id="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio}" data-stock="${p.stock}">
                    <div>
                        <strong>${p.nombre}</strong>
                        <small class="d-block text-muted">${p.codigo_barras || ''} ${inCart ? '(en carrito)' : ''}</small>
                    </div>
                    <span class="badge bg-primary rounded-pill fs-6">RD$ ${p.precio}</span>
                </a>`;
            }).join('');
            if (data.length === 0) {
                container.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
            }
        });
}

document.getElementById('producto_resultados').addEventListener('click', function(e) {
    const item = e.target.closest('.list-group-item');
    if (!item) return;
    e.preventDefault();
    const id = parseInt(item.dataset.id);
    const nombre = item.dataset.nombre;
    const precio = parseFloat(item.dataset.precio);
    agregarAlCarrito(id, nombre, precio);
    // Re-render search to update active state
    const q = document.getElementById('producto_search').value;
    if (q.length >= 2) buscarProductos(q);
});

function agregarAlCarrito(id, nombre, precio) {
    const existente = cart.find(c => c.producto_id === id);
    if (existente) {
        existente.cantidad += 1;
    } else {
        cart.push({ producto_id: id, nombre, precio, cantidad: 1, notas: '', curso: 'fuerte' });
    }
    renderCart();
}

function cambiarCantidad(id, delta) {
    const item = cart.find(c => c.producto_id === id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) {
        cart = cart.filter(c => c.producto_id !== id);
    }
    renderCart();
    const q = document.getElementById('producto_search').value;
    if (q.length >= 2) buscarProductos(q);
}

function quitarDelCarrito(id) {
    cart = cart.filter(c => c.producto_id !== id);
    renderCart();
    const q = document.getElementById('producto_search').value;
    if (q.length >= 2) buscarProductos(q);
}

function renderCart() {
    const container = document.getElementById('cart_items');
    const empty = document.getElementById('cart_empty');
    const cartDiv = document.getElementById('cart_container');
    const count = document.getElementById('cart_count');
    const total = document.getElementById('cart_total');

    count.textContent = cart.length;

    if (cart.length === 0) {
        empty.style.display = 'block';
        cartDiv.style.display = 'none';
        document.getElementById('itemsInput').value = '[]';
        return;
    }

    empty.style.display = 'none';
    cartDiv.style.display = 'block';

    let sum = 0;
    container.innerHTML = cart.map(c => {
        const sub = c.precio * c.cantidad;
        sum += sub;
        return `<tr>
            <td>${c.nombre}</td>
            <td>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${c.producto_id}, -1)">−</button>
                    <input type="text" class="form-control text-center" value="${c.cantidad}" readonly style="min-width:35px">
                    <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${c.producto_id}, 1)">+</button>
                </div>
            </td>
            <td>RD$ ${c.precio.toFixed(2)}</td>
            <td>RD$ ${sub.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger" type="button" onclick="quitarDelCarrito(${c.producto_id})">×</button></td>
        </tr>`;
    }).join('');

    total.textContent = `RD$ ${sum.toFixed(2)}`;

    // Serialize cart to hidden input
    const itemsData = cart.map(c => ({
        producto_id: c.producto_id,
        cantidad: c.cantidad,
        notas: c.notas,
        curso: c.curso,
    }));
    document.getElementById('itemsInput').value = JSON.stringify(itemsData);
}
</script>
@endpush
@endsection
