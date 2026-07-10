@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Nueva Orden</h1>
        <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    @if(!$sesion)
    <div class="alert alert-warning">
        No tienes una sesión de caja abierta. <a href="{{ route('cajas.index') }}">Abrir caja</a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5>Datos de la Orden</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('ordenes.store') }}" method="POST">
                        @csrf
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

                        <button type="submit" class="btn btn-primary w-100" {{ !$sesion ? 'disabled' : '' }}>
                            Crear Orden
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5>Buscador de Productos</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="producto_search" class="form-control" placeholder="Buscar producto por nombre o código...">
                            <button class="btn btn-outline-secondary" type="button" id="search_btn">Buscar</button>
                        </div>
                    </div>
                    <div id="producto_resultados" class="list-group" style="max-height: 400px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
            container.innerHTML = data.map(p => `
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                   data-id="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio}">
                    <div>
                        <strong>${p.nombre}</strong>
                        <small class="d-block text-muted">${p.codigo_barras || ''}</small>
                    </div>
                    <span class="badge bg-primary rounded-pill fs-6">RD$ ${p.precio}</span>
                </a>
            `).join('');
        });
}

document.getElementById('producto_resultados').addEventListener('click', function(e) {
    const item = e.target.closest('.list-group-item');
    if (!item) return;
    e.preventDefault();
    alert(`Producto seleccionado: ${item.dataset.nombre} - RD$ ${item.dataset.precio}\nGuarda la orden primero para agregar productos.`);
});
</script>
@endpush
@endsection
