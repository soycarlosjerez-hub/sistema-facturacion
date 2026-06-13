@extends('layouts.app')
@section('title', 'Terminal Lavadero')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-droplet text-primary me-2"></i>Terminal de Lavado</h2>
            <p class="text-muted mb-0">Registro de servicios de lavado y detallado</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Panel izquierdo: Cliente + Vehículo + Servicios --}}
        <div class="col-lg-8">
            {{-- Cliente --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Cliente</h6>
                    <div class="row g-2">
                        <div class="col-8">
                            <input type="text" id="buscar-cliente-lav" class="form-control rounded-3" placeholder="Buscar cliente por nombre, teléfono o RNC...">
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary rounded-pill w-100" onclick="crearClienteLav()">
                                <i class="bi bi-plus-circle me-1"></i> Nuevo
                            </button>
                        </div>
                        <div class="col-12" id="cliente-resultados-lav" style="display:none;"></div>
                        <div class="col-12" id="cliente-seleccionado-lav" style="display:none;">
                            <div class="bg-light rounded-3 p-2 d-flex justify-content-between align-items-center">
                                <span id="cliente-info-lav" class="fw-medium"></span>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="limpiarClienteLav()">Cambiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vehículo --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3" id="vehiculo-card" style="display:none;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-car-front me-2"></i>Vehículo</h6>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="text" id="vehiculo-placa" class="form-control rounded-3" placeholder="Placa" maxlength="10">
                        </div>
                        <div class="col-4">
                            <select id="vehiculo-marca" class="form-select rounded-3">
                                <option value="">Seleccionar marca</option>
                                <option>Toyota</option><option>Honda</option><option>Hyundai</option>
                                <option>Nissan</option><option>Suzuki</option><option>Kia</option>
                                <option>Ford</option><option>Chevrolet</option><option>Mercedes-Benz</option>
                                <option>BMW</option><option>Volkswagen</option><option>Mitsubishi</option>
                                <option>Mazda</option><option>Renault</option><option>Otra</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="text" id="vehiculo-modelo" class="form-control rounded-3" placeholder="Modelo">
                        </div>
                        <div class="col-3">
                            <input type="number" id="vehiculo-anio" class="form-control rounded-3" placeholder="Año" min="1990" max="2099">
                        </div>
                        <div class="col-3">
                            <input type="text" id="vehiculo-color" class="form-control rounded-3" placeholder="Color">
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary rounded-pill w-100" onclick="guardarVehiculoLav()">
                                <i class="bi bi-check-lg me-1"></i> Registrar Vehículo
                            </button>
                        </div>
                    </div>
                    <div id="vehiculo-historial" class="mt-2" style="display:none;"></div>
                </div>
            </div>

            {{-- Servicios --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-card-checklist me-2"></i>Servicios</h6>
                    <div class="row g-2" id="servicios-grid">
                        @foreach($servicios as $s)
                        <div class="col-md-4 col-6">
                            <div class="border rounded-3 p-3 text-center h-100 servicio-card"
                                 style="cursor:pointer;transition:all .15s;"
                                 data-id="{{ $s->id }}"
                                 data-nombre="{{ $s->nombre }}"
                                 data-precio="{{ $s->precio }}"
                                 onclick="agregarServicioLav(this)">
                                <div class="fw-bold small">{{ $s->nombre }}</div>
                                <div class="text-primary fw-bold mt-1">RD$ {{ number_format($s->precio, 0) }}</div>
                                @if($s->duracion_minutos)
                                <small class="text-muted">{{ $s->duracion_minutos }} min</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel derecho: Resumen de venta --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Resumen</h6>
                </div>
                <div class="card-body px-4">
                    <div id="servicios-seleccionados">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-hand-index fs-1 d-block mb-2"></i>
                            <small>Selecciona un cliente y sus servicios</small>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal</span>
                        <span class="fw-bold" id="lav-subtotal">RD$ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>ITBIS (18%)</span>
                        <span id="lav-itbis">RD$ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-primary" id="lav-total">RD$ 0.00</span>
                    </div>
                    <hr>
                    <label class="form-label small fw-bold">Método de pago</label>
                    <div class="d-flex gap-1 mb-3 flex-wrap">
                        <button class="btn btn-sm btn-outline-success rounded-pill pago-metodo-lav active" data-metodo="efectivo" onclick="selectMetodoLav(this)">Efectivo</button>
                        <button class="btn btn-sm btn-outline-primary rounded-pill pago-metodo-lav" data-metodo="tarjeta" onclick="selectMetodoLav(this)">Tarjeta</button>
                        <button class="btn btn-sm btn-outline-info rounded-pill pago-metodo-lav" data-metodo="transferencia" onclick="selectMetodoLav(this)">Transferencia</button>
                    </div>
                    <div id="lavador-selector" style="display:none;" class="mb-3">
                        <label class="form-label small fw-bold"><i class="bi bi-people me-1"></i>Lavador(es)</label>
                        <div id="lavador-checkboxes" class="d-flex flex-wrap gap-1"></div>
                    </div>
                    <button class="btn btn-success rounded-pill w-100 fw-bold py-2" onclick="cobrarLav()" id="btn-cobrar-lav" disabled>
                        <i class="bi bi-check-lg me-1"></i> Cobrar RD$ 0.00
                    </button>
                </div>
            </div>

            {{-- Citas del día --}}
            <div class="card border-0 shadow-sm rounded-4 mt-3">
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2"></i>Citas de Hoy</h6>
                </div>
                <div class="card-body px-4" id="citas-hoy-lav">
                    <div class="text-center text-muted py-3"><small>Cargando...</small></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.servicio-card:hover { border-color: var(--bs-primary) !important; background: rgba(var(--bs-primary-rgb),.03); transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.servicio-card.seleccionado { border-color: var(--bs-primary) !important; background: rgba(var(--bs-primary-rgb),.1); }
.pago-metodo-lav.active { transform: scale(1.05); box-shadow: 0 2px 6px rgba(0,0,0,.15); }
#servicios-seleccionados .list-group-item { border-left: 3px solid var(--bs-primary); }
</style>

<script>
let clienteLavId = null;
let vehiculoLavId = null;
let serviciosLav = [];
let lavadoresActivos = [];

document.addEventListener('DOMContentLoaded', function () {
    cargarLavadores();
});

document.getElementById('buscar-cliente-lav').addEventListener('input', function () {
    const q = this.value.trim();
    const container = document.getElementById('cliente-resultados-lav');
    if (q.length < 2) { container.style.display = 'none'; return; }
    fetch(`/lavadero/clientes?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="text-muted small py-2 text-center">Sin resultados</div>';
                container.style.display = 'block';
                return;
            }
            container.innerHTML = data.map(c => `
                <div class="border-bottom py-2 px-2" style="cursor:pointer;" onclick="seleccionarClienteLav(${c.id}, '${escapeHtml(c.nombre)}', '${c.telefono || ''}')">
                    <div class="fw-medium small">${escapeHtml(c.nombre)}</div>
                    <small class="text-muted">${c.rnc_cedula || ''} ${c.telefono ? '· ' + c.telefono : ''}</small>
                </div>
            `).join('');
            container.style.display = 'block';
        });
});

function seleccionarClienteLav(id, nombre, telefono) {
    clienteLavId = id;
    document.getElementById('buscar-cliente-lav').value = nombre;
    document.getElementById('cliente-resultados-lav').style.display = 'none';
    document.getElementById('cliente-seleccionado-lav').style.display = 'block';
    document.getElementById('cliente-info-lav').textContent = nombre + (telefono ? ' · ' + telefono : '');
    document.getElementById('vehiculo-card').style.display = 'block';
    document.getElementById('buscar-cliente-lav').disabled = true;
    if (lavadoresActivos.length > 0) document.getElementById('lavador-selector').style.display = 'block';
    cargarCitasHoy();
}

function limpiarClienteLav() {
    clienteLavId = null;
    vehiculoLavId = null;
    document.getElementById('buscar-cliente-lav').value = '';
    document.getElementById('buscar-cliente-lav').disabled = false;
    document.getElementById('buscar-cliente-lav').focus();
    document.getElementById('cliente-seleccionado-lav').style.display = 'none';
    document.getElementById('vehiculo-card').style.display = 'none';
    document.getElementById('vehiculo-historial').style.display = 'none';
    document.getElementById('lavador-selector').style.display = 'none';
    document.querySelectorAll('.lavador-checkbox').forEach(el => {
        el.classList.remove('active', 'btn-primary');
        el.classList.add('btn-outline-secondary');
        el.querySelector('input').checked = false;
    });
    document.getElementById('btn-cobrar-lav').disabled = true;
}

function guardarVehiculoLav() {
    const placa = document.getElementById('vehiculo-placa').value.trim();
    const marca = document.getElementById('vehiculo-marca').value;
    const modelo = document.getElementById('vehiculo-modelo').value.trim();
    const anio = document.getElementById('vehiculo-anio').value;
    const color = document.getElementById('vehiculo-color').value.trim();

    if (!placa && !marca) { alert('Ingresa al menos la placa o la marca'); return; }

    fetch('/lavadero/vehiculos/crear', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ cliente_id: clienteLavId, placa, marca, modelo, anio, color })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        vehiculoLavId = data.id;
        ['placa','marca','modelo','anio','color'].forEach(id => {
            document.getElementById('vehiculo-' + id).disabled = true;
        });
    });
}

function agregarServicioLav(el) {
    const id = parseInt(el.dataset.id);
    const nombre = el.dataset.nombre;
    const precio = parseFloat(el.dataset.precio);
    const idx = serviciosLav.findIndex(s => s.id === id);
    if (idx >= 0) {
        serviciosLav.splice(idx, 1);
        el.classList.remove('seleccionado');
    } else {
        serviciosLav.push({ id, nombre, precio, cantidad: 1 });
        el.classList.add('seleccionado');
    }
    renderResumenLav();
}

function renderResumenLav() {
    const container = document.getElementById('servicios-seleccionados');
    const btn = document.getElementById('btn-cobrar-lav');
    if (serviciosLav.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-hand-index fs-1 d-block mb-2"></i><small>Selecciona un cliente y sus servicios</small></div>';
        btn.disabled = true;
        document.getElementById('lav-subtotal').textContent = 'RD$ 0.00';
        document.getElementById('lav-itbis').textContent = 'RD$ 0.00';
        document.getElementById('lav-total').textContent = 'RD$ 0.00';
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Cobrar RD$ 0.00';
        return;
    }

    let html = '<div class="list-group list-group-flush">';
    serviciosLav.forEach(s => {
        html += `<div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
            <div><small>${escapeHtml(s.nombre)}</small></div>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold small">RD$ ${s.precio.toFixed(2)}</span>
                <button class="btn btn-sm btn-outline-danger rounded-circle p-0" style="width:22px;height:22px;" onclick="quitarServicioLav(${s.id})">&times;</button>
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;

    const subtotal = serviciosLav.reduce((a, s) => a + s.precio, 0);
    const itbis = subtotal * 0.18;
    const total = subtotal + itbis;
    document.getElementById('lav-subtotal').textContent = 'RD$ ' + subtotal.toFixed(2);
    document.getElementById('lav-itbis').textContent = 'RD$ ' + itbis.toFixed(2);
    document.getElementById('lav-total').textContent = 'RD$ ' + total.toFixed(2);
    btn.disabled = !clienteLavId;
    btn.innerHTML = `<i class="bi bi-check-lg me-1"></i> Cobrar RD$ ${total.toFixed(2)}`;
}

function quitarServicioLav(id) {
    serviciosLav = serviciosLav.filter(s => s.id !== id);
    document.querySelector(`.servicio-card[data-id="${id}"]`)?.classList.remove('seleccionado');
    renderResumenLav();
}

function selectMetodoLav(el) {
    document.querySelectorAll('.pago-metodo-lav').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}

function cargarLavadores() {
    fetch('/lavadero/lavadores/activos')
        .then(r => r.json())
        .then(data => {
            lavadoresActivos = data || [];
            if (lavadoresActivos.length > 0) {
                const container = document.getElementById('lavador-checkboxes');
                container.innerHTML = lavadoresActivos.map(l => `
                    <label class="btn btn-sm btn-outline-secondary rounded-pill lavador-checkbox" data-id="${l.id}" onclick="toggleLavador(this)">
                        <input type="checkbox" class="d-none" value="${l.id}">
                        ${escapeHtml(l.nombre)} <small class="text-muted">(${l.tipo === 'fijo' ? 'F' : 'T'})</small>
                    </label>
                `).join('');
            }
        });
}

function toggleLavador(el) {
    el.classList.toggle('active');
    el.classList.toggle('btn-outline-secondary');
    el.classList.toggle('btn-primary');
    el.querySelector('input').checked = !el.querySelector('input').checked;
}

function getLavadorIds() {
    return Array.from(document.querySelectorAll('.lavador-checkbox.active input')).map(i => parseInt(i.value));
}

function cobrarLav() {
    if (!clienteLavId || serviciosLav.length === 0) return;
    const metodo = document.querySelector('.pago-metodo-lav.active').dataset.metodo;
    const total = serviciosLav.reduce((a, s) => a + s.precio, 0) * 1.18;

    document.getElementById('btn-cobrar-lav').disabled = true;
    document.getElementById('btn-cobrar-lav').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

    fetch('/lavadero/cobrar', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cliente_id: clienteLavId,
            vehiculo_id: vehiculoLavId,
            metodo_pago: metodo,
            servicios: serviciosLav,
            total: total,
            lavador_ids: getLavadorIds(),
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        Swal.fire({icon:'success', title:'Cobrado', text:'Servicio registrado exitosamente', timer: 1500, showConfirmButton: false});
        limpiarPantallaLav();
    })
    .catch(() => {
        document.getElementById('btn-cobrar-lav').disabled = false;
        document.getElementById('btn-cobrar-lav').innerHTML = '<i class="bi bi-check-lg me-1"></i> Cobrar';
    });
}

function limpiarPantallaLav() {
    clienteLavId = null;
    vehiculoLavId = null;
    serviciosLav = [];
    document.querySelectorAll('.servicio-card.seleccionado').forEach(el => el.classList.remove('seleccionado'));
    document.getElementById('buscar-cliente-lav').value = '';
    document.getElementById('buscar-cliente-lav').disabled = false;
    document.getElementById('cliente-seleccionado-lav').style.display = 'none';
    document.getElementById('vehiculo-card').style.display = 'none';
    document.getElementById('vehiculo-historial').style.display = 'none';
    document.getElementById('lavador-selector').style.display = 'none';
    document.querySelectorAll('.lavador-checkbox').forEach(el => {
        el.classList.remove('active', 'btn-primary');
        el.classList.add('btn-outline-secondary');
        el.querySelector('input').checked = false;
    });
    ['placa','marca','modelo','anio','color'].forEach(id => {
        const el = document.getElementById('vehiculo-' + id);
        el.value = '';
        el.disabled = false;
    });
    renderResumenLav();
}

function cargarCitasHoy() {
    fetch('/lavadero/citas/hoy')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('citas-hoy-lav');
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3"><small>Sin citas para hoy</small></div>';
                return;
            }
            container.innerHTML = '<div class="list-group list-group-flush">' +
                data.map(c => `
                    <div class="list-group-item px-0 py-1 border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="fw-medium">${new Date(c.fecha_hora).toLocaleTimeString('es-DO', {hour:'2-digit', minute:'2-digit'})}</small>
                            <small class="ms-2">${escapeHtml(c.cliente?.nombre || '—')}</small>
                        </div>
                        <span class="badge ${c.estado === 'pendiente' ? 'bg-warning text-dark' : c.estado === 'confirmada' ? 'bg-info' : c.estado === 'completada' ? 'bg-success' : 'bg-secondary'} rounded-pill">${c.estado}</span>
                    </div>
                `).join('') +
                '</div>';
        });
}

function crearClienteLav() {
    const nombre = prompt('Nombre del cliente:');
    if (!nombre) return;
    const telefono = prompt('Teléfono (opcional):');
    fetch('/lavadero/clientes/crear', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, telefono })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        seleccionarClienteLav(data.id, data.nombre, data.telefono);
    });
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
}
</script>
@endsection
