@extends('layouts.app')

@push('styles')
<style>
:root {
    --pos-accent: #0ea5e9;
    --pos-bg: #0f172a;
    --pos-card: #1e293b;
    --pos-border: #334155;
    --pos-text: #f1f5f9;
    --pos-text-muted: #94a3b8;
}
#productosModal .modal-content { background: var(--pos-bg); color: var(--pos-text); }
#productosModal .modal-header { background: linear-gradient(135deg, var(--pos-accent), #0284c7); }
#productosModal .form-control { background: var(--pos-card); border-color: var(--pos-border); color: var(--pos-text); }
#productosModal .form-control::placeholder { color: var(--pos-text-muted); }
#productosModal .form-control:focus { border-color: var(--pos-accent); box-shadow: 0 0 0 3px rgba(14,165,233,0.15); color: var(--pos-text); }

.tecla {
    flex: 1; height: 52px; border-radius: 10px;
    border: 1px solid var(--pos-border);
    background: var(--pos-card); color: var(--pos-text);
    font-size: 1.15rem; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    touch-action: manipulation; user-select: none; -webkit-user-select: none;
    transition: background .08s, transform .08s; padding: 0 4px; min-width: 0;
}
.tecla:active { background: rgba(14,165,233,0.2); transform: scale(0.93); box-shadow: 0 0 0 2px rgba(14,165,233,0.2); }
.tecla-func { background: rgba(255,255,255,0.06); font-size: 1rem; }
.tecla-shift { flex: 1.6; }
.tecla-shift.active { background: rgba(14,165,233,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
.tecla-backspace { flex: 1.3; }
.tecla-space { flex: 4; }
.tecla-enter { flex: 1.3; background: var(--pos-accent); color: #fff; border-color: var(--pos-accent); }
.tecla-punct { flex: 1; }
.tecla-func:active { background: rgba(14,165,233,0.2); }
.tecla-func.active { background: rgba(14,165,233,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
.tecla-row { display: flex; gap: 6px; justify-content: center; margin-bottom: 6px; }
#teclado-rows { max-width: 100%; }
#teclado-rows::-webkit-scrollbar { height: 0; }

.modal-prod-card {
    background: var(--pos-card); border: 1px solid var(--pos-border); border-radius: 14px;
    padding: 12px 10px; cursor: pointer; text-align: center; position: relative;
    transition: transform .15s, box-shadow .15s; height: 100%; display: flex; flex-direction: column; align-items: center;
}
.modal-prod-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); border-color: var(--pos-accent); }
.modal-prod-card.out-of-stock { opacity: 0.4; cursor: not-allowed; }
.modal-prod-card.out-of-stock:hover { transform: none; box-shadow: none; }
.modal-prod-img { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; background: rgba(255,255,255,0.05); margin-bottom: 8px; }
.modal-prod-img-placeholder {
    width: 80px; height: 80px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;
}
.modal-prod-name { font-size: .9rem; font-weight: 600; color: var(--pos-text); line-height: 1.2; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; }
.modal-prod-price { font-size: 1rem; font-weight: 800; color: var(--pos-accent); font-variant-numeric: tabular-nums; }
.modal-prod-stock-badge { font-size: .7rem; padding: 2px 8px; border-radius: 6px; font-weight: 700; position: absolute; top: 8px; right: 8px; }
.modal-prod-qty { display: flex; align-items: center; gap: 8px; margin-top: 6px; }
.modal-prod-qty button {
    width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--pos-border);
    background: rgba(255,255,255,0.06); color: var(--pos-text); font-weight: 700; font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .15s;
}
.modal-prod-qty button:hover { background: rgba(14,165,233,0.15); border-color: var(--pos-accent); }
.modal-prod-qty span { font-weight: 800; font-size: 1rem; min-width: 24px; text-align: center; color: var(--pos-text); }
</style>
@endpush

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
                    <button type="button" class="btn btn-success w-100 btn-lg mt-2" onclick="abrirModalProductos()">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Productos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ Modal Productos con Teclado Virtual ============ -->
<div class="modal fade" id="productosModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-4 border-0 shadow" style="max-height:95vh;">
            <div class="modal-header border-0 rounded-top-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar Producto</h5>
                <button type="button" class="btn-close btn-close-white" style="width:36px;height:36px;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 d-flex flex-column" style="height: calc(95vh - 60px);">
                <div class="input-group shadow-sm rounded-3 mb-2">
                    <span class="input-group-text" style="background: var(--pos-card); border-color: var(--pos-border); color: var(--pos-text-muted); min-height:48px;"><i class="bi bi-search fs-5"></i></span>
                    <input type="text" id="modal-buscar-producto" class="form-control" placeholder="Buscar producto..." autocomplete="off" oninput="modalBuscarProductos()" style="min-height:48px; font-size:1.05rem;">
                    <button class="btn" type="button" id="modal-btn-limpiar" style="display:none; color: var(--pos-text-muted); min-width:48px;" onclick="modalLimpiarBusqueda()"><i class="bi bi-x-lg fs-5"></i></button>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <select id="modal-item-curso" class="form-select form-select-sm rounded-3" style="max-width:120px;background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);">
                        <option value="entrada">Entrada</option>
                        <option value="fuerte" selected>Plato Fuerte</option>
                        <option value="postre">Postre</option>
                        <option value="bebida">Bebida</option>
                    </select>
                    <input type="text" id="modal-item-notas" class="form-control form-control-sm rounded-3" placeholder="Notas" maxlength="200" style="background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);">
                </div>
                <div id="modal-productos-grid" class="row g-2 overflow-auto mb-2" style="flex:1; min-height:0;"></div>
                <div class="border-top pt-2 mt-2" id="teclado-virtual" style="border-color: var(--pos-border) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold" style="font-size:.8rem; color: var(--pos-text-muted);">Teclado</small>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary rounded-start-pill" style="font-size:.8rem;padding:4px 12px;border-color: var(--pos-border);color: var(--pos-text-muted);" onclick="tecladoIdioma('us')" id="btn-idioma-us">US</button>
                            <button class="btn btn-outline-secondary rounded-end-pill" style="font-size:.8rem;padding:4px 12px;border-color: var(--pos-border);color: var(--pos-text-muted);" onclick="tecladoIdioma('es')" id="btn-idioma-es">ES</button>
                        </div>
                    </div>
                    <div id="teclado-rows"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];
let productos = [];
let cantidadesModal = {};
const PALETA_COLORES_MODAL = [
    { bg: '#fee2e2', fg: '#dc2626' }, { bg: '#ffedd5', fg: '#ea580c' },
    { bg: '#fef9c3', fg: '#ca8a04' }, { bg: '#dcfce7', fg: '#16a34a' },
    { bg: '#cffafe', fg: '#0891b2' }, { bg: '#dbeafe', fg: '#2563eb' },
    { bg: '#ede9fe', fg: '#7c3aed' }, { bg: '#fce7f3', fg: '#db2777' },
    { bg: '#ccfbf1', fg: '#0d9488' }, { bg: '#faf5ff', fg: '#a21caf' },
];
const TECLADO_LAYOUTS = {
    us: [['q','w','e','r','t','y','u','i','o','p'],['a','s','d','f','g','h','j','k','l'],['z','x','c','v','b','n','m']],
    es: [['q','w','e','r','t','y','u','i','o','p'],['a','s','d','f','g','h','j','k','l','ñ'],['z','x','c','v','b','n','m']]
};
let tecladoIdiomaActual = 'es';
let teclaShiftActivo = false;

// ============ Order Form ============
document.getElementById('tipo_orden').addEventListener('change', function() {
    document.getElementById('delivery_fields').style.display = this.value === 'delivery' ? 'block' : 'none';
    document.getElementById('pickup_fields').style.display = this.value === 'pickup' ? 'block' : 'none';
    document.getElementById('contacto_fields').style.display = this.value !== 'mostrador' ? 'block' : 'none';
});

// ============ Modal Productos + Teclado Virtual ============
function colorProductoModal(nombre) {
    let h = 0;
    for (let i = 0; i < nombre.length; i++) h = nombre.charCodeAt(i) + ((h << 5) - h);
    return PALETA_COLORES_MODAL[Math.abs(h) % PALETA_COLORES_MODAL.length];
}

function abrirModalProductos() {
    const modalEl = document.getElementById('productosModal');
    const old = bootstrap.Modal.getInstance(modalEl);
    if (old) old.dispose();
    const modal = new bootstrap.Modal(modalEl, { keyboard: false });
    document.getElementById('modal-buscar-producto').value = '';
    document.getElementById('modal-btn-limpiar').style.display = 'none';
    document.getElementById('modal-item-notas').value = '';
    document.getElementById('modal-item-curso').value = 'fuerte';
    cantidadesModal = {};
    teclaShiftActivo = false;
    renderizarTecladoModal();
    tecladoIdioma('es');
    renderizarProductosModal('');
    // Fetch products
    fetch('{{ route("ordenes.buscarProducto") }}?q=')
        .then(r => r.json())
        .then(data => { productos = data; renderizarProductosModal(''); });
    modal.show();
    setTimeout(() => document.getElementById('modal-buscar-producto').focus(), 300);
}

function cerrarModalProductos() {
    const el = document.getElementById('productosModal');
    const m = bootstrap.Modal.getInstance(el);
    if (m) m.hide();
}

function modalBuscarProductos() {
    const q = document.getElementById('modal-buscar-producto').value.trim();
    document.getElementById('modal-btn-limpiar').style.display = q.length > 0 ? 'inline-block' : 'none';
    renderizarProductosModal(q);
}

function modalLimpiarBusqueda() {
    document.getElementById('modal-buscar-producto').value = '';
    document.getElementById('modal-btn-limpiar').style.display = 'none';
    modalBuscarProductos();
    document.getElementById('modal-buscar-producto').focus();
}

const validaStock = false;

function renderizarProductosModal(filtro) {
    const container = document.getElementById('modal-productos-grid');
    const q = (filtro || '').toLowerCase();
    const results = productos.filter(p => {
        if (validaStock && p.stock <= 0) return false;
        const matchNombre = (p.nombre || '').toLowerCase().includes(q);
        const matchCodigo = (p.codigo_barras || '').toLowerCase().includes(q);
        return matchNombre || matchCodigo;
    });
    if (results.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-4" style="color:var(--pos-text-muted);"><i class="bi bi-search" style="font-size:2.5rem;opacity:.4;display:block;margin-bottom:8px;"></i>Sin resultados</div>';
        return;
    }
    let html = '';
    results.forEach(p => {
        const id = p.id;
        if (cantidadesModal[id] === undefined) cantidadesModal[id] = 1;
        const qty = cantidadesModal[id];
        const c = colorProductoModal(p.nombre);
        const initial = (p.nombre || '?').charAt(0).toUpperCase();
        const stockCls = !validaStock ? 'bg-warning text-dark' : (p.stock <= 0 ? 'bg-secondary' : p.stock <= 5 ? 'bg-danger' : 'bg-warning text-dark');
        const stockTxt = p.stock <= 0 ? 'Sin stock' : p.stock + ' uds';
        const outCls = (validaStock && p.stock <= 0) ? ' out-of-stock' : '';
        let imgHtml;
        if (p.imagen_url) {
            imgHtml = `<img class="modal-prod-img" src="${p.imagen_url}" alt="" onerror="this.onerror=null;this.remove();this.nextElementSibling.style.display='flex';">`;
            imgHtml += `<div class="modal-prod-img-placeholder" style="background:${c.bg};color:${c.fg};display:none;">${initial}</div>`;
        } else {
            imgHtml = `<div class="modal-prod-img-placeholder" style="background:${c.bg};color:${c.fg};">${initial}</div>`;
        }
        html += `
        <div class="col-4 col-md-3 col-lg-2">
            <div class="modal-prod-card${outCls}" onclick="agregarProductoDesdeModal(${id})">
                <span class="modal-prod-stock-badge badge ${stockCls}">${stockTxt}</span>
                ${imgHtml}
                <div class="modal-prod-name">${escHtml(p.nombre)}</div>
                <div class="modal-prod-price">RD$ ${Number(p.precio).toFixed(2)}</div>
                <div class="modal-prod-qty" onclick="event.stopPropagation()">
                    <button type="button" onpointerdown="cambiarQtyModal(${id}, -1)">−</button>
                    <span id="mqty-${id}">${qty}</span>
                    <button type="button" onpointerdown="cambiarQtyModal(${id}, 1)">+</button>
                </div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function cambiarQtyModal(productoId, delta) {
    if (cantidadesModal[productoId] === undefined) cantidadesModal[productoId] = 1;
    let nueva = cantidadesModal[productoId] + delta;
    if (nueva < 1) nueva = 1;
    if (nueva > 99) nueva = 99;
    cantidadesModal[productoId] = nueva;
    const span = document.getElementById('mqty-' + productoId);
    if (span) span.textContent = nueva;
}

function showToast(msg, tipo) {
    // no-op; cart feedback is sufficient
}

function agregarProductoDesdeModal(id) {
    const p = productos.find(x => x.id === id);
    if (!p) return;
    if (validaStock && p.stock <= 0) { showToast('Producto sin stock', 'warning'); return; }
    const qty = cantidadesModal[id] || 1;
    const curso = document.getElementById('modal-item-curso').value;
    const notas = document.getElementById('modal-item-notas').value.trim();
    const existente = cart.find(c => c.producto_id === id);
    if (existente) {
        existente.cantidad += qty;
        if (notas) existente.notas = notas;
        if (curso) existente.curso = curso;
    } else {
        cart.push({ producto_id: id, nombre: p.nombre, precio: Number(p.precio), cantidad: qty, notas: notas, curso: curso });
    }
    renderCart();
    cerrarModalProductos();
}

// Teclado virtual
function renderizarTecladoModal() {
    const container = document.getElementById('teclado-rows');
    if (!container) return;
    const layout = TECLADO_LAYOUTS[tecladoIdiomaActual] || TECLADO_LAYOUTS.es;
    let html = '<div class="tecla-row">';
    ['1','2','3','4','5','6','7','8','9','0'].forEach(n => {
        html += `<button class="tecla" onpointerdown="teclaPulsar('${n}')" type="button">${n}</button>`;
    });
    html += '</div>';
    layout.slice(0, -1).forEach(fila => {
        html += '<div class="tecla-row">';
        fila.forEach(letra => {
            const display = teclaShiftActivo ? letra.toUpperCase() : letra;
            html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
        });
        html += '</div>';
    });
    html += '<div class="tecla-row">';
    const shiftCls = teclaShiftActivo ? ' active' : '';
    html += `<button class="tecla tecla-func tecla-shift${shiftCls}" onpointerdown="teclaMayusculas()" type="button"><i class="bi bi-arrow-up-short fs-4"></i></button>`;
    layout[layout.length - 1].forEach(letra => {
        const display = teclaShiftActivo ? letra.toUpperCase() : letra;
        html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
    });
    html += `<button class="tecla tecla-func tecla-backspace" onpointerdown="teclaBorrar()" type="button"><i class="bi bi-backspace fs-4"></i></button>`;
    html += '</div>';
    html += '<div class="tecla-row">';
    html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar(',')" type="button">,</button>`;
    html += `<button class="tecla tecla-func tecla-space" onpointerdown="teclaPulsar(' ')" type="button"><span class="fw-normal" style="font-size:1rem;">Espacio</span></button>`;
    html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar('.')" type="button">.</button>`;
    html += `<button class="tecla tecla-enter" onpointerdown="teclaEnter()" type="button"><i class="bi bi-arrow-return-left fs-4"></i></button>`;
    html += '</div>';
    container.innerHTML = html;
}

function tecladoIdioma(idioma) {
    tecladoIdiomaActual = idioma;
    const usBtn = document.getElementById('btn-idioma-us');
    const esBtn = document.getElementById('btn-idioma-es');
    if (usBtn) usBtn.classList.toggle('active', idioma === 'us');
    if (esBtn) esBtn.classList.toggle('active', idioma === 'es');
    renderizarTecladoModal();
}

function teclaPulsar(caracter) {
    const input = document.getElementById('modal-buscar-producto');
    const start = input.selectionStart || input.value.length;
    const end = input.selectionEnd || input.value.length;
    const letra = teclaShiftActivo ? caracter.toUpperCase() : caracter;
    input.value = input.value.substring(0, start) + letra + input.value.substring(end);
    const newPos = start + letra.length;
    input.setSelectionRange(newPos, newPos);
    input.focus();
    if (teclaShiftActivo) { teclaShiftActivo = false; renderizarTecladoModal(); }
    modalBuscarProductos();
}

function teclaMayusculas() { teclaShiftActivo = !teclaShiftActivo; renderizarTecladoModal(); }

function teclaBorrar() {
    const input = document.getElementById('modal-buscar-producto');
    const start = input.selectionStart || input.value.length;
    const end = input.selectionEnd || input.value.length;
    if (start === 0 && end === 0) return;
    if (start !== end) {
        input.value = input.value.substring(0, start) + input.value.substring(end);
        input.setSelectionRange(start, start);
    } else {
        input.value = input.value.substring(0, start - 1) + input.value.substring(start);
        input.setSelectionRange(start - 1, start - 1);
    }
    input.focus();
    modalBuscarProductos();
}

function teclaEnter() { cerrarModalProductos(); }

// ============ Cart ============
function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

function cambiarCantidad(id, delta) {
    const item = cart.find(c => c.producto_id === id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) {
        cart = cart.filter(c => c.producto_id !== id);
    }
    renderCart();
}

function quitarDelCarrito(id) {
    cart = cart.filter(c => c.producto_id !== id);
    renderCart();
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
            <td>${escHtml(c.nombre)}${c.notas ? '<br><small class="text-muted">'+escHtml(c.notas)+'</small>' : ''}</td>
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

    const itemsData = cart.map(c => ({
        producto_id: c.producto_id,
        cantidad: c.cantidad,
        notas: c.notas || '',
        curso: c.curso || 'fuerte',
    }));
    document.getElementById('itemsInput').value = JSON.stringify(itemsData);
}
</script>
@endpush
@endsection