@extends('layouts.app')

@section('title', 'KDS - Pantalla de Cocina')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Pantalla de Cocina</h1>
        <div>
            <button class="btn btn-outline-secondary" onclick="location.reload()">Actualizar</button>
            <span class="badge bg-secondary fs-6" id="order_count">0</span>
        </div>
    </div>

    <div class="mb-3">
        <ul class="nav nav-tabs" id="kdsTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-filter="all">Todos</button></li>
            <li class="nav-item"><button class="nav-link" data-filter="mostrador">Mostrador</button></li>
            <li class="nav-item"><button class="nav-link" data-filter="delivery">Delivery</button></li>
            <li class="nav-item"><button class="nav-link" data-filter="pickup">Pickup</button></li>
        </ul>
    </div>

    <div id="kds_orders" class="row"></div>
</div>

@push('scripts')
<script>
let currentFilter = 'all';

function loadOrders() {
    fetch('{{ route("kds.orders") }}')
        .then(r => r.json())
        .then(data => renderOrders(data.ordenes))
        .catch(console.error);
}

function renderOrders(ordenes) {
    const filtered = currentFilter === 'all' ? ordenes : ordenes.filter(o => o.tipo_orden === currentFilter);
    const container = document.getElementById('kds_orders');
    document.getElementById('order_count').textContent = filtered.length;

    if (filtered.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-success">No hay órdenes pendientes.</div></div>';
        return;
    }

    container.innerHTML = filtered.map(o => {
        const hasOldItems = o.cursos && Object.values(o.cursos).some(curso =>
            curso.some(d => d.estado_cocina === 'pendiente' && Date.now() - new Date(d.created_at).getTime() > 600000)
        );

        return `
        <div class="col-md-4 mb-3">
            <div class="card ${hasOldItems ? 'border-danger' : ''}">
                <div class="card-header d-flex justify-content-between align-items-center ${o.tipo_orden === 'delivery' ? 'bg-info' : o.tipo_orden === 'pickup' ? 'bg-warning' : 'bg-secondary'} text-white">
                    <div>
                        <strong>#${o.id}</strong>
                        <span class="badge bg-light text-dark ms-2">${o.tipo_orden.toUpperCase()}</span>
                    </div>
                    <small>${o.time}</small>
                </div>
                <div class="card-body p-2">
                    ${o.cliente_nombre ? `<small class="text-muted">${o.cliente_nombre}</small>` : ''}
                    ${o.direccion ? `<br><small class="text-muted">📍 ${o.direccion}</small>` : ''}
                    ${o.telefono ? `<br><small class="text-muted">📞 ${o.telefono}</small>` : ''}
                    ${o.hora_retiro ? `<br><small class="text-muted">🕐 ${o.hora_retiro}</small>` : ''}

                    ${o.cursos ? Object.entries(o.cursos).map(([curso, detalles]) => `
                        <div class="mt-2">
                            <small class="fw-bold text-uppercase">${curso}</small>
                            ${detalles.map(d => `
                                <div class="d-flex justify-content-between align-items-center mt-1 p-1 rounded
                                    ${d.estado_cocina === 'pendiente' ? 'bg-warning text-dark' : ''}
                                    ${d.estado_cocina === 'en_preparacion' ? 'bg-primary text-white' : ''}
                                    ${d.estado_cocina === 'listo' ? 'bg-success text-white' : ''}">
                                    <span>${d.producto?.nombre || '—'} x${d.cantidad}
                                        ${d.notas ? `<br><small>${d.notas}</small>` : ''}
                                    </span>
                                    <div>
                                        ${d.estado_cocina === 'pendiente' ? `
                                            <button onclick="updateDetalle(${d.id}, 'en_preparacion')" class="btn btn-sm btn-light">
                                                Preparar
                                            </button>` : ''}
                                        ${d.estado_cocina === 'en_preparacion' ? `
                                            <button onclick="updateDetalle(${d.id}, 'listo')" class="btn btn-sm btn-light">
                                                Listo
                                            </button>` : ''}
                                        ${d.estado_cocina === 'listo' ? `
                                            <button onclick="updateDetalle(${d.id}, 'entregado')" class="btn btn-sm btn-light">
                                                Servir
                                            </button>` : ''}
                                        ${d.estado_cocina === 'entregado' ? `<span class="badge bg-light text-dark">✅</span>` : ''}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `).join('') : '<div class="text-muted mt-2">Sin productos</div>'}
                </div>
            </div>
        </div>`;
    }).join('');
}

function updateDetalle(detalleId, estado) {
    fetch('{{ route("kds.update") }}'.replace('detalle', detalleId), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ estado_cocina: estado })
    })
    .then(r => r.json())
    .then(data => { if (data.success) loadOrders(); })
    .catch(console.error);
}

document.querySelectorAll('#kdsTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('#kdsTabs .nav-link').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        loadOrders();
    });
});

loadOrders();
setInterval(loadOrders, 5000);
</script>
@endpush
@endsection
