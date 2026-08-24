@extends('layouts.app')

@section('title', 'POS — Punto de Venta')

@push('styles')
@include('partials.premium-ui')
<style>
/* POS Layout */
.pos-main .col-lg-4, .pos-main .col-lg-5 { min-height: 600px; }
.pos-carrito-container { position: sticky; top: 90px; }

/* Servicio Cards */
.pos-servicio-card {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(20px);
    border: 1.5px solid rgba(255,255,255,0.7);
    border-radius: 1rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}
.pos-servicio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(14,165,233,0.15);
    border-color: rgba(59,130,246,0.3);
    border-left-color: #0ea5e9;
}
.pos-servicio-card:active { transform: scale(0.98); }
.pos-servicio-icon {
    width: 42px; height: 42px; border-radius: 0.75rem;
    background: rgba(14,165,233,0.1); color: #0ea5e9;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; margin-bottom: 0.75rem;
}
.pos-servicio-precio { font-weight: 800; font-size: 1.1rem; color: #0ea5e9; }

/* Producto Cards */
.pos-producto-card {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(20px);
    border: 1.5px solid rgba(255,255,255,0.7);
    border-radius: 1rem;
    padding: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.pos-producto-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(59,130,246,0.12);
    border-color: rgba(59,130,246,0.25);
}
.pos-producto-card:active { transform: scale(0.98); }
.pos-producto-nombre { font-weight: 700; font-size: 0.85rem; color: #0f172a; }
.pos-producto-precio { font-weight: 800; font-size: 1rem; color: #3b82f6; }

/* Carrito */
.pos-carrito-item {
    background: rgba(255,255,255,0.6);
    border-radius: 0.75rem;
    padding: 0.65rem 0.85rem;
    margin-bottom: 0.4rem;
    border: 1px solid rgba(0,0,0,0.05);
}
.pos-qty-btn {
    width: 24px; height: 24px; border-radius: 0.4rem;
    border: 1px solid #e2e8f0; background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.85rem; cursor: pointer;
}
.pos-qty-btn:hover { background: #f1f5f9; }

/* Checkout Modal */
.pos-checkout-total {
    font-size: 2.5rem; font-weight: 800; text-align: center;
    color: #0ea5e9; padding: 1.5rem;
    background: rgba(14,165,233,0.05); border-radius: 1rem;
}
.pos-metodo-pago {
    border: 2px solid #e2e8f0; border-radius: 1rem;
    padding: 1.5rem; text-align: center; cursor: pointer;
    transition: all 0.2s;
}
.pos-metodo-pago:hover { border-color: #cbd5e1; background: #f8fafc; }
.pos-metodo-pago.active { border-color: #3b82f6; background: rgba(59,130,246,0.05); }
.pos-metodo-pago i { font-size: 2rem; color: #3b82f6; display: block; margin-bottom: 0.5rem; }

.pos-cambio-preview {
    text-align: center; padding: 1rem;
    background: rgba(34,197,94,0.06); border-radius: 0.75rem;
    border: 1px solid rgba(34,197,94,0.12);
}
.pos-cambio-valor { font-size: 1.75rem; font-weight: 800; color: #22c55e; }

.pos-btn-cobrar {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff; border: none; border-radius: 1rem;
    padding: 1rem 2rem; font-weight: 700; font-size: 1.1rem;
    width: 100%; cursor: pointer; transition: all 0.2s;
}
.pos-btn-cobrar:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(34,197,94,0.3); }
.pos-btn-cobrar:disabled { opacity: 0.5; cursor: not-allowed; }

/* Tabs tienda */
.pos-tab-btn {
    border: 1.5px solid #e2e8f0; background: rgba(255,255,255,0.7);
    border-radius: 0.65rem; padding: 0.4rem 1rem;
    font-size: 0.82rem; font-weight: 600; color: #475569;
    cursor: pointer; transition: all 0.2s;
}
.pos-tab-btn:hover { background: #f1f5f9; }
.pos-tab-btn.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }

/* Scrollbar carrito */
.pos-carrito-scroll::-webkit-scrollbar { width: 5px; }
.pos-carrito-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 3px; }

@media (max-width: 991px) { .pos-carrito-container { position: static; } }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    @include('partials.premium-ui')

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-cart3"></i></div>
                <div>
                    <h4 class="ui-header-title">POS — Punto de Venta</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shop me-1"></i>
                        <span>Lavadero + Tienda</span>
                        <span class="divider">·</span>
                        <i class="bi bi-clock"></i>
                        <span id="pos-clock">{{ now()->format('h:i A') }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="app.limpiar()" title="Limpiar">
                    <i class="bi bi-trash3 me-1"></i>
                    <span class="d-none d-sm-inline">Limpiar</span>
                </button>
                <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" onclick="app.mantener()" title="Esperar">
                    <i class="bi bi-pause-circle me-1"></i>
                    <span class="d-none d-sm-inline">Esperar</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3 pos-main">
        {{-- SERVICIOS --}}
        <div class="col-lg-4 col-xl-3">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-droplet-fill" style="color:#0ea5e9;font-size:1.15rem;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Servicios de Lavado</h6>
                    </div>
                    <div class="row g-2" id="servicios-list">
                        @forelse($servicios as $svc)
                        <div class="col-6">
                            <div class="pos-servicio-card" data-servicio="{{ $svc->id }}" onclick="app.addServicio({{ $svc->id }}, '{{ addslashes($svc->nombre) }}', {{ $svc->precio }}, 'servicio')">
                                <div class="pos-servicio-icon"><i class="bi bi-droplet"></i></div>
                                <div class="pos-servicio-nombre text-dark mb-1" style="font-size:0.85rem;">{{ $svc->nombre }}</div>
                                <div class="pos-servicio-precio small">RD$ {{ number_format($svc->precio, 2) }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4 text-muted"><small>No hay servicios activos</small></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCTOS --}}
        <div class="col-lg-5 col-xl-4">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-bag-fill" style="color:#3b82f6;font-size:1.15rem;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Productos de Tienda</h6>
                    </div>
                    <div class="d-flex gap-2 mb-2 flex-wrap" id="productos-tabs">
                        <button class="pos-tab-btn active" data-linea="todos" onclick="app.filtrarLinea('todos')">Todos</button>
                        <button class="pos-tab-btn" data-linea="alimentos" onclick="app.filtrarLinea('alimentos')">Alimentos</button>
                        <button class="pos-tab-btn" data-linea="bebidas" onclick="app.filtrarLinea('bebidas')">Bebidas</button>
                        <button class="pos-tab-btn" data-linea="accesorios" onclick="app.filtrarLinea('accesorios')">Accesorios</button>
                    </div>
                    <div class="pos-search-wrap mb-3">
                        <i class="bi bi-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                        <input type="text" class="form-control rounded-3 ps-5" id="search-input" placeholder="Buscar producto..." oninput="app.buscar(this.value)">
                    </div>
                    <div class="row g-2" id="productos-list" style="max-height: 500px; overflow-y: auto;">
                        @forelse($productos as $prod)
                        @if($prod->linea_negocio && $prod->linea_negocio !== 'todos')
                        <div class="col-6 col-md-4 producto-item" data-linea="{{ $prod->linea_negocio }}">
                            <div class="pos-producto-card" onclick="app.addProducto({{ $prod->id }}, '{{ addslashes($prod->nombre) }}', {{ $prod->precio }}, {{ $prod->stock }}, 'producto')">
                                <div class="pos-producto-nombre text-dark mb-1" style="font-size:0.82rem;">{{ $prod->nombre }}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="pos-producto-precio small">RD$ {{ number_format($prod->precio, 2) }}</div>
                                    <span class="badge rounded-pill {{ $prod->stock <= $prod->stock_minimo ? 'bg-danger' : 'bg-success' }}" style="font-size:0.65rem;">{{ $prod->stock }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <div class="col-12 text-center py-4 text-muted"><small>No hay productos de tienda</small></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- CARRITO --}}
        <div class="col-lg-3 col-xl-5">
            <div class="pos-carrito-container ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3 d-flex flex-column" style="min-height: 400px; max-height: calc(100vh - 200px);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-2"></i>Orden Actual</h6>
                        <button class="btn btn-sm btn-link text-danger p-0" onclick="app.limpiar()"><i class="bi bi-trash3"></i></button>
                    </div>
                    <div class="pos-carrito-scroll flex-grow-1 overflow-auto" id="carrito-items">
                        <div class="text-center py-5 text-muted" id="carrito-vacio">
                            <i class="bi bi-cart4 mb-2" style="font-size:2.5rem;"></i>
                            <p class="mb-0 small">Carrito vacío</p>
                        </div>
                    </div>
                    <div class="border-top mt-3 pt-3" id="carrito-total" style="display:none;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="subtotal-display">RD$ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">ITBIS</span>
                            <span class="fw-bold" id="itbis-display">RD$ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:1.15rem;">
                            <span class="fw-bold text-dark">TOTAL</span>
                            <span class="fw-bold text-primary" id="total-display">RD$ 0.00</span>
                        </div>
                    </div>
                    <button class="pos-btn-cobrar mt-3" id="btn-cobrar" onclick="app.abrirCobro()" disabled>
                        <i class="bi bi-cash-coin me-2"></i>Cobrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL COBRO --}}
<div class="modal fade" id="modalCobro" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2" style="color:#22c55e;"></i>Registrar Cobro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pos-checkout-total">
                    <small class="text-muted d-block">Total a cobrar</small>
                    <span id="cobro-total">RD$ 0.00</span>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-12"><label class="fw-bold small">Método de pago</label></div>
                    <div class="col-md-4">
                        <div class="pos-metodo-pago active" data-metodo="efectivo" onclick="app.setMetodo('efectivo')">
                            <i class="bi bi-cash-stack"></i>
                            <span class="fw-bold">Efectivo</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pos-metodo-pago" data-metodo="tarjeta" onclick="app.setMetodo('tarjeta')">
                            <i class="bi bi-credit-card"></i>
                            <span class="fw-bold">Tarjeta</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pos-metodo-pago" data-metodo="transferencia" onclick="app.setMetodo('transferencia')">
                            <i class="bi bi-bank"></i>
                            <span class="fw-bold">Transferencia</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4" id="monto-section">
                    <label class="fw-bold small">Monto recibido</label>
                    <div class="input-group input-group-lg mt-2">
                        <span class="input-group-text bg-light fw-bold">RD$</span>
                        <input type="number" class="form-control text-center" id="monto-recibido" placeholder="0.00" step="0.01" oninput="app.calcularCambio()">
                    </div>
                    <div class="pos-cambio-preview mt-3" id="cambio-preview" style="display:none;">
                        <small class="text-muted d-block">Cambio a devolver</small>
                        <div class="pos-cambio-valor" id="cambio-valor">RD$ 0.00</div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="fw-bold small">Notas (opcional)</label>
                    <textarea class="form-control rounded-3" id="cobro-notas" rows="2" placeholder="Notas de la orden..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                <button class="pos-btn-cobrar w-auto px-5" id="btn-confirmar" onclick="app.confirmar()" disabled>
                    <i class="bi bi-check-lg me-2"></i>Confirmar Cobro
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const app = (() => {
    let carrito = [], metodoPago = 'efectivo', totalServicios = 0, totalProductos = 0, totalITBIS = 0;
    const ITBIS_PRODUCTOS = 0.18;

    function init() {
        setInterval(() => { const el = document.getElementById('pos-clock'); if(el) el.textContent = new Date().toLocaleTimeString('es-DO', {hour:'2-digit', minute:'2-digit'}); }, 60000);
        restoreHold();
    }

    function restoreHold() {
        const saved = sessionStorage.getItem('pos_hold');
        if (saved) { try { carrito = JSON.parse(saved); renderCarrito(); } catch(e) {} }
    }

    window.app = {
        addServicio(id, nombre, precio, tipo) {
            const existing = carrito.find(i => i.id === id && i.tipo === 'servicio');
            if (existing) { existing.cantidad++; }
            else { carrito.push({ id, nombre, precio, cantidad: 1, tipo, itbis: 0 }); }
            renderCarrito();
        },
        addProducto(id, nombre, precio, stock, tipo) {
            if (stock <= 0) return alert('Sin stock');
            const existing = carrito.find(i => i.id === id);
            if (existing) { if (existing.cantidad >= stock) return alert('Stock insuficiente'); existing.cantidad++; }
            else { carrito.push({ id, nombre, precio, cantidad: 1, tipo, itbis: ITBIS_PRODUCTOS }); }
            renderCarrito();
        },
        cambiarCant(id, delta) {
            const item = carrito.find(i => i.id === id);
            if (!item) return;
            item.cantidad += delta;
            if (item.cantidad <= 0) carrito = carrito.filter(i => i.id !== id);
            renderCarrito();
        },
        eliminar(id) {
            carrito = carrito.filter(i => i.id !== id);
            renderCarrito();
        },
        limpiar() {
            carrito = []; renderCarrito(); sessionStorage.removeItem('pos_hold');
        },
        mantener() {
            sessionStorage.setItem('pos_hold', JSON.stringify(carrito));
            UI.toast.success('Orden guardada en espera');
        },
        filtrarLinea(linea) {
            document.querySelectorAll('.pos-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.linea === linea));
            document.querySelectorAll('.producto-item').forEach(el => {
                el.style.display = (linea === 'todos' || el.dataset.linea === linea) ? '' : 'none';
            });
        },
        buscar(term) {
            term = term.toLowerCase();
            document.querySelectorAll('.producto-item').forEach(el => {
                el.style.display = el.querySelector('.pos-producto-nombre').textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        },
        abrirCobro() {
            if (carrito.length === 0) return;
            document.getElementById('cobro-total').textContent = `RD$ ${totalPuntos.toFixed(2)}`;
            new bootstrap.Modal(document.getElementById('modalCobro')).show();
        },
        setMetodo(m) {
            metodoPago = m;
            document.querySelectorAll('.pos-metodo-pago').forEach(b => b.classList.toggle('active', b.dataset.metodo === m));
            document.getElementById('monto-section').style.display = m === 'efectivo' ? '' : 'none';
        },
        calcularCambio() {
            const recibido = parseFloat(document.getElementById('monto-recibido').value) || 0;
            const cambio = recibido - totalPuntos;
            const preview = document.getElementById('cambio-preview');
            if (cambio > 0) { preview.style.display = ''; document.getElementById('cambio-valor').textContent = `RD$ ${cambio.toFixed(2)}`; }
            else { preview.style.display = 'none'; }
            document.getElementById('btn-confirmar').disabled = recibido < totalPuntos;
        },
        confirmar() {
            const btn = document.getElementById('btn-confirmar'); btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></i> Procesando...';
            const data = { carrito, metodo_pago: metodoPago, notas: document.getElementById('cobro-notas').value };
            fetch('{{ route("pos.checkout") }}', {
                method: 'POST', headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: JSON.stringify(data)
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCobro')).hide();
                    carrito = []; renderCarrito(); sessionStorage.removeItem('pos_hold');
                    UI.toast.success('¡Cobro exitoso!');
                } else { UI.toast.error(res.error); }
                btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Confirmar Cobro';
            }).catch(() => { UI.toast.error('Error de conexión'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Confirmar Cobro'; });
        }
    };

    function renderCarrito() {
        totalServicios = 0; totalProductos = 0; totalITBIS = 0;
        const container = document.getElementById('carrito-items');
        const vacio = document.getElementById('carrito-vacio');
        if (carrito.length === 0) { vacio.style.display = ''; document.getElementById('carrito-total').style.display = 'none'; document.getElementById('btn-cobrar').disabled = true; container.innerHTML = vacio.outerHTML; return; }
        vacio.style.display = 'none'; document.getElementById('carrito-total').style.display = ''; document.getElementById('btn-cobrar').disabled = false;
        container.innerHTML = carrito.map(item => {
            const subtotal = item.precio * item.cantidad;
            totalServicios += item.tipo === 'servicio' ? subtotal : subtotal * (1 - item.itbis);
            totalProductos += item.tipo === 'servicio' ? 0 : subtotal;
            totalITBIS += subtotal * item.itbis;
            return `<div class="pos-carrito-item d-flex align-items-center justify-content-between">
                <div class="flex-grow-1">
                    <div class="fw-bold small text-dark" style="font-size:0.82rem;">${item.nombre}</div>
                    <div class="small text-muted">RD$ ${item.precio.toFixed(2)} × ${item.cantidad}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-1">
                        <button class="pos-qty-btn" onclick="app.cambiarCant(${item.id}, -1)">−</button>
                        <span class="fw-bold small" style="min-width:20px;text-align:center;">${item.cantidad}</span>
                        <button class="pos-qty-btn" onclick="app.cambiarCant(${item.id}, 1)">+</button>
                    </div>
                    <div class="fw-bold small text-primary" style="min-width:70px;text-align:right;">RD$ ${subtotal.toFixed(2)}</div>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="app.eliminar(${item.id})"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>`;
        }).join('');
        const totalGlobal = totalServicios + totalProductos + totalITBIS;
        document.getElementById('subtotal-display').textContent = `RD$ ${(totalServicios + totalProductos).toFixed(2)}`;
        document.getElementById('itbis-display').textContent = `RD$ ${totalITBIS.toFixed(2)}`;
        document.getElementById('total-display').textContent = `RD$ ${totalGlobal.toFixed(2)}`;
    }

    const totalPuntos = (() => totalServicios + totalProductos + totalITBIS)();
    init();
})();
</script>
@endsection
