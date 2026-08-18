@extends('layouts.app')

@section('title', 'KDS - Pantalla de Cocina')

@push('styles')
@include('partials.premium-ui')
<style>
    /* Tabs premium (el JS usa #kdsTabs .nav-link y data-filter — no renombrar) */
    #kdsTabs.nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        gap: .35rem;
        flex-wrap: wrap;
    }
    #kdsTabs.nav-tabs .nav-link {
        border: 0;
        color: #64748b;
        font-weight: 600;
        font-size: .85rem;
        padding: .55rem 1.1rem;
        border-radius: 999px;
        background: rgba(255,255,255,.7);
        transition: all .2s ease;
    }
    #kdsTabs.nav-tabs .nav-link:hover { color: #1e293b; background: #fff; }
    #kdsTabs.nav-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 4px 14px rgba(245,158,11,.3);
    }

    /* Cabecera de cada orden según tipo (presentación — no lo usa el JS como selector) */
    .kds-order-head {
        display: flex; justify-content: space-between; align-items: center;
        padding: .75rem 1rem; color: #fff;
    }
    .kds-order-head.head-delivery { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
    .kds-order-head.head-pickup { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .kds-order-head.head-mostrador { background: linear-gradient(135deg, #64748b, #475569); }

    /* Fila de detalle según estado */
    .kds-detail { border-radius: .5rem; transition: all .2s ease; }
    .kds-detail.pendiente { background: rgba(245,158,11,.12); }
    .kds-detail.en_preparacion { background: rgba(59,130,246,.1); }
    .kds-detail.listo { background: rgba(34,197,94,.12); }
    .kds-detail.entregado { background: rgba(148,163,184,.15); }

    /* Orden vieja (más de 10 min pendiente) */
    .kds-order-card.kds-old { border: 2px solid #ef4444; animation: kds-pulse 2s infinite; }
    @keyframes kds-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.4);} 50%{box-shadow:0 0 0 8px rgba(239,68,68,0);} }

    /* Botón "Listo" en verde semántico (el accent del módulo es ámbar) */
    .kds-btn-listo, .kds-btn-listo:hover {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        box-shadow: 0 4px 14px rgba(34,197,94,.3) !important;
        color: #fff !important;
    }

    body.dark-mode #kdsTabs.nav-tabs { border-bottom-color: #1e293b; }
    body.dark-mode #kdsTabs.nav-tabs .nav-link { color: #94a3b8; background: rgba(15,23,42,.5); }
    body.dark-mode #kdsTabs.nav-tabs .nav-link:hover { color: #f1f5f9; background: rgba(30,41,59,.8); }
    body.dark-mode .kds-detail.pendiente { background: rgba(245,158,11,.18); }
    body.dark-mode .kds-detail.en_preparacion { background: rgba(59,130,246,.18); }
    body.dark-mode .kds-detail.listo { background: rgba(34,197,94,.18); }
    body.dark-mode .kds-detail.entregado { background: rgba(148,163,184,.15); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-display"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Pantalla de Cocina</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-egg-fried me-1"></i>
                        <span>Órdenes en preparación</span>
                        <span class="divider">·</span>
                        <span><span class="badge bg-white text-dark rounded-pill px-2" id="order_count">0</span> orden(es)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                </button>
            </div>
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

    <div id="kds_orders" class="row g-3"></div>
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
        container.innerHTML = '<div class="col-12"><div class="ui-empty-state"><i class="bi bi-inbox"></i><p>No hay órdenes pendientes.</p></div></div>';
        return;
    }

    container.innerHTML = filtered.map(o => {
        const hasOldItems = o.cursos && Object.values(o.cursos).some(curso =>
            curso.some(d => d.estado_cocina === 'pendiente' && Date.now() - new Date(d.created_at).getTime() > 600000)
        );

        return `
        <div class="col-md-4 mb-3">
            <div class="ui-card h-100 kds-order-card ${hasOldItems ? 'kds-old' : ''}">
                <div class="ui-card-accent"></div>
                <div class="kds-order-head ${o.tipo_orden === 'delivery' ? 'head-delivery' : o.tipo_orden === 'pickup' ? 'head-pickup' : 'head-mostrador'}">
                    <div>
                        <strong>#${o.id}</strong>
                        <span class="badge bg-light text-dark ms-2">${o.tipo_orden.toUpperCase()}</span>
                    </div>
                    <small>${o.time}</small>
                </div>
                <div class="ui-card-body p-2">
                    ${o.cliente_nombre ? `<small class="text-muted">${o.cliente_nombre}</small>` : ''}
                    ${o.direccion ? `<br><small class="text-muted">📍 ${o.direccion}</small>` : ''}
                    ${o.telefono ? `<br><small class="text-muted">📞 ${o.telefono}</small>` : ''}
                    ${o.hora_retiro ? `<br><small class="text-muted">🕐 ${o.hora_retiro}</small>` : ''}

                    ${o.cursos ? Object.entries(o.cursos).map(([curso, detalles]) => `
                        <div class="mt-2">
                            <small class="fw-bold text-uppercase" style="font-size:.65rem;color:#94a3b8;">${curso}</small>
                            ${detalles.map(d => `
                                <div class="d-flex justify-content-between align-items-center mt-1 p-1 rounded kds-detail ${d.estado_cocina}">
                                    <span class="small">${d.producto?.nombre || '—'} x${d.cantidad}
                                        ${d.notas ? `<br><small>${d.notas}</small>` : ''}
                                    </span>
                                    <div class="d-flex gap-1 align-items-center flex-wrap justify-content-end">
                                        ${d.estado_cocina === 'pendiente' ? `
                                            <button onclick="updateDetalle(${d.id}, 'en_preparacion')" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                                                Preparar
                                            </button>` : ''}
                                        ${d.estado_cocina === 'en_preparacion' ? `
                                            <button onclick="updateDetalle(${d.id}, 'listo')" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill kds-btn-listo">
                                                Listo
                                            </button>` : ''}
                                        ${d.estado_cocina === 'listo' ? `
                                            <button onclick="updateDetalle(${d.id}, 'entregado')" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
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
    fetch('{{ route("kds.update", ["detalle" => "_DETALLE_"]) }}'.replace('_DETALLE_', detalleId), {
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