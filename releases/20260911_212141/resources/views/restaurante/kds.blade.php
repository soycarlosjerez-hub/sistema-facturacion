@extends('layouts.app')
@section('title', 'Pantalla de Cocina — KDS')
@section('content_class', 'px-0')
@push('styles')
@include('partials.premium-ui')
<style>
    /* ============================================================
       KDS — Tarjetas de cocina (presentación; el JS no usa estos selectores)
       Semántica: pendiente/preparando = ámbar · urgente (hay pendientes) = rojo suave
       · todos listos = verde + atenuado
       ============================================================ */
    .kds-card { border-left: 4px solid #f59e0b; transition: all .25s ease; }
    .kds-card.urgent { border-left-color: #ef4444; animation: kds-pulse 1.8s infinite; }
    .kds-card.done   { border-left-color: #10b981; opacity: .75; }

    /* En polling cada 10s se re-renderizan las tarjetas: fade suave en vez de slide-up */
    #kds-orders .ui-card { animation: uiFadeIn .3s ease both; }

    @keyframes kds-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.35); }
        50%      { box-shadow: 0 0 0 10px rgba(239,68,68,0); }
    }

    /* Encabezado de tarjeta (mesa · meta · número) */
    .kds-card-head {
        display: flex; justify-content: space-between; align-items: center; gap: .75rem;
        padding: .9rem 1rem .75rem;
        background: rgba(255,255,255,.5);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(241,245,249,.9);
    }
    .kds-card-head h5 { color: #0f172a; font-size: 1.05rem; }
    .kds-card-meta { color: #64748b; font-size: .78rem; display: flex; align-items: center; gap: .3rem; flex-wrap: wrap; }
    .kds-card-num {
        font-weight: 700; font-size: .85rem; color: #1e293b;
        background: rgba(var(--accent-rgb, 16,185,129), .1);
        border: 1px solid rgba(var(--accent-rgb, 16,185,129), .25);
        border-radius: 999px; padding: .25rem .7rem;
        white-space: nowrap; flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(var(--accent-rgb, 16,185,129), .12);
    }
    .kds-card-body { padding: .5rem 1rem 1rem; }

    /* Items agrupados por curso */
    .kds-item {
        border-left: 3px solid transparent;
        padding: .45rem .6rem;
        margin: .3rem 0;
        border-radius: .5rem;
        font-size: .9rem;
        background: rgba(255,255,255,.4);
        transition: background .2s ease;
    }
    .kds-item:hover { background: rgba(255,255,255,.75); }
    .kds-item strong { color: #0f172a; }
    .kds-item .text-muted { color: #64748b; }
    .kds-qty { color: var(--accent, #10b981); font-weight: 800; }
    .kds-item.entrada { border-left-color: #3b82f6; background: rgba(59,130,246,.06); }
    .kds-item.fuerte  { border-left-color: #eab308; background: rgba(234,179,8,.06); }
    .kds-item.postre  { border-left-color: #ec4899; background: rgba(236,72,153,.06); }
    .kds-item.bebida  { border-left-color: #06b6d4; background: rgba(6,182,212,.06); }

    /* Punto de color en la etiqueta de curso */
    .kds-curso-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

    /* Grupo de botones de la cocina */
    .kds-btn-group { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; justify-content: flex-end; }
    .kds-btn-group .ui-btn,
    .kds-btn-group .ui-badge { font-size: .7rem; padding: .3rem .65rem; }

    /* Jerarquía de estados: Preparando = ámbar (arranca) · Listo = verde (destaca) · Servido = ghost */
    .kds-btn-preparando, .kds-btn-preparando:hover {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        box-shadow: 0 4px 14px rgba(245,158,11,.3) !important;
        color: #fff !important;
    }
    .kds-btn-listo, .kds-btn-listo:hover {
        background: linear-gradient(135deg, #16a34a, #15803d) !important;
        box-shadow: 0 4px 14px rgba(22,163,74,.35) !important;
        color: #fff !important;
    }

    /* Contador del header — chip glass con pulso */
    .kds-count-badge {
        background: rgba(255,255,255,.18) !important;
        backdrop-filter: blur(10px);
        border: 1.5px solid rgba(255,255,255,.35);
        color: #fff !important;
        font-weight: 700;
        font-size: .85rem;
        padding: .55rem 1.1rem;
        box-shadow: 0 4px 18px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.2);
    }
    .kds-count-badge::before {
        content: '';
        display: inline-block;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #fff;
        margin-right: .5rem;
        box-shadow: 0 0 0 0 rgba(255,255,255,.55);
        animation: kds-count-pulse 2s infinite;
        vertical-align: 1px;
    }
    @keyframes kds-count-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,.55); }
        50%      { box-shadow: 0 0 0 7px rgba(255,255,255,0); }
    }

    /* Empty state — tarjeta glass centrada */
    .kds-empty-card { max-width: 560px; margin: 2rem auto; }
    .kds-empty-card .ui-empty-state { padding: 2.5rem 1rem; }
    .kds-empty-icon {
        width: 72px; height: 72px;
        margin: 0 auto .9rem;
        border-radius: 50%;
        background: rgba(16,185,129,.1);
        border: 2px solid rgba(16,185,129,.25);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 8px rgba(16,185,129,.05);
    }
    .kds-empty-icon i { font-size: 2.2rem; color: #10b981; margin-bottom: 0; }
    .kds-empty-card h5 { color: #0f172a; }
    .kds-empty-card p { color: #64748b; margin-bottom: 0; }

    /* ============================================================
       Dark mode — clases propias del KDS (el partial cubre .ui-*)
       ============================================================ */
    body.dark-mode .kds-card-head { background: rgba(30,41,59,.55); border-bottom-color: rgba(255,255,255,.06); }
    body.dark-mode .kds-card-head h5 { color: #f1f5f9; }
    body.dark-mode .kds-card-meta { color: #94a3b8; }
    body.dark-mode .kds-card-num { color: #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,.3); }
    body.dark-mode .kds-item { background: rgba(255,255,255,.03); }
    body.dark-mode .kds-item strong { color: #f1f5f9; }
    body.dark-mode .kds-item .text-muted { color: #94a3b8; }
    body.dark-mode .kds-qty { color: #34d399; }
    body.dark-mode .kds-item.entrada { background: rgba(59,130,246,.14); }
    body.dark-mode .kds-item.fuerte  { background: rgba(234,179,8,.14); }
    body.dark-mode .kds-item.postre  { background: rgba(236,72,153,.14); }
    body.dark-mode .kds-item.bebida  { background: rgba(6,182,212,.14); }
    body.dark-mode .kds-empty-icon { background: rgba(16,185,129,.15); border-color: rgba(16,185,129,.3); }
    body.dark-mode .kds-empty-icon i { color: #34d399; }
    body.dark-mode .kds-empty-card h5 { color: #f1f5f9; }
    body.dark-mode .kds-empty-card p { color: #94a3b8; }

    /* ============================================================
       Responsivo — móviles
       ============================================================ */
    @media (max-width: 575.98px) {
        .kds-card-head { padding: .75rem .85rem .6rem; }
        .kds-card-body { padding: .4rem .85rem .85rem; }
        .kds-item .d-flex { flex-wrap: wrap; gap: .35rem; }
        .kds-btn-group { justify-content: flex-start; }
        .kds-count-badge { font-size: .75rem; padding: .45rem .85rem; }
    }

    /* ============================================================
       Historial — pestaña de órdenes servidas/entregadas
       ============================================================ */
    .kds-tab-row { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
    .kds-tab-btn {
        font-size: .82rem; font-weight: 600; padding: .4rem 1rem; border-radius: .5rem;
        border: 1.5px solid rgba(255,255,255,.2); background: rgba(255,255,255,.08);
        color: var(--foreground-muted, #64748b); cursor: pointer; transition: all .2s;
        display: inline-flex; align-items: center; gap: .35rem;
    }
    .kds-tab-btn:hover { background: rgba(255,255,255,.16); color: var(--foreground, #0f172a); }
    .kds-tab-btn.active {
        background: var(--accent, #10b981); border-color: var(--accent, #10b981);
        color: #fff; box-shadow: 0 2px 10px rgba(16,185,129,.3);
    }
    .kds-tab-btn.active i { font-size: .9rem; }
    .kds-tab-label { font-size: .72rem; color: var(--foreground-muted, #94a3b8); margin-left: .5rem; }

    /* Tarjetas de historial */
    .kds-hist-card {
        border-left: 4px solid #8b5cf6; background: rgba(255,255,255,.45);
        backdrop-filter: blur(8px); border-radius: .75rem; overflow: hidden;
        transition: all .2s ease;
    }
    .kds-hist-card:hover { box-shadow: 0 4px 20px rgba(139,92,246,.15); transform: translateY(-2px); }

    .kds-hist-head {
        display: flex; justify-content: space-between; align-items: center;
        padding: .65rem .85rem; background: rgba(139,92,246,.08);
        border-bottom: 1px solid rgba(139,92,246,.12);
    }
    .kds-hist-head h6 { color: #0f172a; font-size: .85rem; font-weight: 700; margin: 0; }
    .kds-hist-badge {
        font-size: .7rem; font-weight: 700; padding: .2rem .6rem; border-radius: 999px;
        background: rgba(139,92,246,.12); color: #7c3aed;
        border: 1px solid rgba(139,92,246,.2);
    }
    .kds-hist-meta {
        display: flex; gap: .75rem; padding: .5rem .85rem; font-size: .72rem; color: #64748b;
        border-bottom: 1px solid rgba(241,245,249,.6);
    }
    .kds-hist-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: .3rem .85rem .3rem 2.5rem; font-size: .8rem; color: #334155;
        border-bottom: 1px solid rgba(241,245,249,.4);
    }
    .kds-hist-item:last-child { border-bottom: none; }
    .kds-hist-item strong { color: #0f172a; font-weight: 600; }
    .kds-hist-total {
        display: flex; justify-content: space-between; align-items: center;
        padding: .5rem .85rem; font-size: .82rem; background: rgba(139,92,246,.04);
        border-top: 2px dashed rgba(139,92,246,.15);
    }
    .kds-hist-total strong { color: #7c3aed; }
    .kds-hist-tipo {
        font-size: .62rem; padding: .15rem .45rem; border-radius: 999px;
        background: rgba(59,130,246,.1); color: #2563eb; font-weight: 600;
    }

    /* Animación de entrada */
    .kds-hist-card { animation: uiFadeIn .3s ease both; }

    /* Empty state historial */
    .kds-hist-empty { border-left-color: #94a3b8; }

    /* Dark mode historial */
    body.dark-mode .kds-hist-card { background: rgba(15,23,42,.7); border-color: rgba(255,255,255,.08); }
    body.dark-mode .kds-hist-head { background: rgba(139,92,246,.12); border-bottom-color: rgba(139,92,246,.2); }
    body.dark-mode .kds-hist-head h6 { color: #f1f5f9; }
    body.dark-mode .kds-hist-badge { background: rgba(139,92,246,.2); color: #a78bfa; border-color: rgba(139,92,246,.3); }
    body.dark-mode .kds-hist-meta { color: #94a3b8; border-bottom-color: rgba(255,255,255,.06); }
    body.dark-mode .kds-hist-item { color: #cbd5e1; border-bottom-color: rgba(255,255,255,.05); }
    body.dark-mode .kds-hist-item strong { color: #f1f5f9; }
    body.dark-mode .kds-hist-total { background: rgba(139,92,246,.08); border-top-color: rgba(139,92,246,.2); }
    body.dark-mode .kds-hist-total strong { color: #a78bfa; }
    body.dark-mode .kds-hist-tipo { background: rgba(59,130,246,.2); color: #60a5fa; }

    /* Responsivo historial */
    @media (max-width: 575.98px) {
        .kds-hist-card { font-size: .78rem; }
        .kds-hist-item { padding-left: .85rem; }
        .kds-tab-btn { font-size: .75rem; padding: .35rem .75rem; }
    }
</style>
@endpush
@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cup-straw"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Pantalla de Cocina</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-clock me-1"></i>
                        <span id="kds-clock">{{ now()->format('d/m/Y h:i:s A') }}</span>
                        <span class="divider">·</span>
                        <span>Órdenes en tiempo real</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex gap-2 align-items-center">
                    <div class="kds-tab-row">
                        <button class="kds-tab-btn active" data-tab="active" onclick="mostrarTabKds('active')">
                            <i class="bi bi-fire"></i> Activas
                        </button>
                        <button class="kds-tab-btn" data-tab="historial" onclick="mostrarTabKds('historial')">
                            <i class="bi bi-clock-history"></i> Historial
                        </button>
                    </div>
                    <span class="badge kds-count-badge rounded-pill" id="kds-count">0 pendientes</span>
                </div>
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="limpiarKds()">
                    <i class="bi bi-eraser me-1"></i> Limpiar
                </button>
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Recargar
                </button>
            </div>
        </div>
    </div>
    <div class="row g-3" id="kds-orders"></div>

    <!-- Historial -->
    <div id="kds-historial-section" style="display:none;">
        <div class="ui-card mb-3" style="background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.15);">
            <div class="card-body py-2 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-time-history" style="color:#8b5cf6;"></i>
                    <strong style="color:#0f172a;">Órdenes servidas / entregadas</strong>
                    <span class="kds-tab-label" id="kds-hist-count">0 órdenes · RD$ 0.00</span>
                </div>
                <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="cargarHistorial()" style="font-size:.72rem;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                </button>
            </div>
        </div>
        <div class="row g-3" id="kds-historial"></div>
    </div>
</div>
<script>
let kdsUltimoConteo = 0;

function kdsBeep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const g = ctx.createGain();
        g.connect(ctx.destination);
        g.gain.value = 0.12;
        const o = ctx.createOscillator();
        o.type = 'sine';
        o.frequency.value = 880;
        o.connect(g);
        o.start();
        o.stop(ctx.currentTime + 0.15);
        setTimeout(() => {
            const o2 = ctx.createOscillator();
            o2.type = 'sine';
            o2.frequency.value = 1100;
            o2.connect(g);
            o2.start();
            o2.stop(ctx.currentTime + 0.2);
        }, 200);
    } catch(e) {}
}

function actualizarReloj() {
    document.getElementById('kds-clock').textContent = new Date().toLocaleString('es-DO', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' });
}
setInterval(actualizarReloj, 1000);

function cargarKds() {
    fetch('{{ route("restaurante.kds.orders") }}')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('kds-orders');
            const ordenes = data.ordenes || [];
            document.getElementById('kds-count').textContent = ordenes.length + ' pendientes';

            if (ordenes.length > kdsUltimoConteo && kdsUltimoConteo > 0) {
                kdsBeep();
            }
            kdsUltimoConteo = ordenes.length;

            if (ordenes.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="ui-card kds-empty-card">
                            <div class="ui-empty-state kds-empty">
                                <div class="kds-empty-icon"><i class="bi bi-check2-circle"></i></div>
                                <h5 class="fw-bold mb-1">¡Cocina al día!</h5>
                                <p>Todas las órdenes están listas</p>
                            </div>
                        </div>
                    </div>`;
                return;
            }

            container.innerHTML = ordenes.map(o => {
                let cursosHtml = '';
                const cursoOrden = ['entrada', 'fuerte', 'postre', 'bebida', 'General'];
                const cursoLabels = { entrada: 'Entradas', fuerte: 'Platos Fuertes', postre: 'Postres', bebida: 'Bebidas', General: 'General' };
                const cursoColors = { entrada: '#3b82f6', fuerte: '#eab308', postre: '#ec4899', bebida: '#06b6d4', General: '#64748b' };
                cursoOrden.forEach(cur => {
                    const items = o.cursos[cur];
                    if (!items || items.length === 0) return;
                    cursosHtml += `
                        <div class="mb-2">
                            <small class="fw-bold text-muted text-uppercase d-flex align-items-center gap-1 mb-1" style="font-size:.65rem;">
                                <span class="kds-curso-dot" style="background:${cursoColors[cur] || '#64748b'}"></span>${cursoLabels[cur] || cur}
                            </small>
                            ${items.map(d => {
                                const progBtn = o.origen === 'orden' ? 'en_preparacion' : 'preparando';
                                const finBtn   = o.origen === 'orden' ? 'entregado' : 'servido';
                                const preLabel = o.origen === 'orden' ? 'Preparar' : 'Preparando';
                                const finLabel = o.origen === 'orden' ? 'Entregar' : 'Servido';
                                return `
                                <div class="kds-item ${cur}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="kds-qty">${d.cantidad}x</strong> <strong>${d.producto?.nombre || '—'}</strong>
                                            ${d.notas ? `<br><small class="text-muted fst-italic">📝 ${d.notas}</small>` : ''}
                                        </div>
                                        <div class="kds-btn-group">
                                            ${d.estado_cocina === 'pendiente' ? `<button class="ui-btn kds-btn-preparando ui-btn-sm rounded-pill" onclick="kdsActualizar('${o.origen}', ${d.id}, '${progBtn}')"><i class="bi bi-fire me-1"></i>${preLabel}</button>` : ''}
                                            ${d.estado_cocina === progBtn ? `<span class="ui-badge ui-badge-warning"><i class="bi bi-fire me-1"></i>Preparando</span> <button class="ui-btn kds-btn-listo ui-btn-sm rounded-pill" onclick="kdsActualizar('${o.origen}', ${d.id}, 'listo')"><i class="bi bi-check-lg me-1"></i>Listo</button>` : ''}
                                            ${d.estado_cocina === 'listo' ? `<span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Listo</span> <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="kdsActualizar('${o.origen}', ${d.id}, '${finBtn}')"><i class="bi bi-hand-thumbs-up me-1"></i>${finLabel}</button>` : ''}
                                        </div>
                                    </div>
                                </div>`;
                            }).join('')}
                        </div>`;
                });

                const totalItems = Object.values(o.cursos || {}).flat().length;
                const tienePendientes = Object.values(o.cursos || {}).flat().some(d => d.estado_cocina === 'pendiente');
                const todosListos = Object.values(o.cursos || {}).flat().every(d => d.estado_cocina === 'listo' || d.estado_cocina === 'servido' || d.estado_cocina === 'entregado');

                const esMesa = o.origen === 'mesa';
                const titulo = esMesa ? (o.mesa || 'Mesa —') : `Orden ${o.tipo_orden ? '· ' + o.tipo_orden : ''}`;
                const subInfo = esMesa
                    ? `<i class="bi bi-clock"></i>${o.time} · ${totalItems} items`
                    : `<i class="bi bi-clock"></i>${o.time} · ${totalItems} items ${o.cliente_nombre && o.cliente_nombre !== '—' ? '· ' + o.cliente_nombre : ''}`;

                return `
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="ui-card h-100 kds-card ${tienePendientes ? 'urgent' : todosListos ? 'done' : ''}">
                        <div class="ui-card-accent"></div>
                        <div class="kds-card-head">
                            <div>
                                <h5 class="fw-bold mb-0">${titulo}</h5>
                                <small class="kds-card-meta">${subInfo}</small>
                            </div>
                            <span class="kds-card-num">#${o.id}</span>
                        </div>
                        <div class="kds-card-body">
                            ${cursosHtml}
                        </div>
                    </div>
                </div>`;
            }).join('');
        });
}

function kdsActualizar(origen, detalleId, estado) {
    fetch(`/restaurante/kds/update/${origen}/${detalleId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado })
    })
    .then(r => r.json())
    .then(data => { if (data.success) cargarKds(); });
}

function limpiarKds() {
    if (typeof Swal === 'undefined') {
        if (!confirm('¿Vaciar el KDS? Todos los platos pendientes se marcarán como servidos.')) return;
        fetch('{{ route("restaurante.kds.limpiar") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => cargarKds());
        return;
    }
    Swal.fire({
        title: '¿Limpiar el KDS?',
        text: 'Todos los platos pendientes/preparando/listos se marcarán como servidos y desaparecerán de la pantalla.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then(function (r) {
        if (!r.isConfirmed) return;
        fetch('{{ route("restaurante.kds.limpiar") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (typeof UI !== 'undefined' && UI.toast) {
                UI.toast.success('KDS limpiado (' + (data.limpiados || 0) + ' plato(s))');
            }
            cargarKds();
        });
    });
}

// Polling cada 10 segundos
document.addEventListener('DOMContentLoaded', function () {
    cargarKds();
    setInterval(cargarKds, 10000);
});

/* ============================================================
   Historial — pestaña de órdenes servidas/entregadas
   ============================================================ */
function mostrarTabKds(tab) {
    const ordersDiv  = document.getElementById('kds-orders');
    const histSection = document.getElementById('kds-historial-section');
    const btns        = document.querySelectorAll('.kds-tab-btn');

    btns.forEach(b => {
        b.classList.toggle('active', b.dataset.tab === tab);
    });

    if (tab === 'active') {
        ordersDiv.style.display = '';
        histSection.style.display = 'none';
    } else {
        ordersDiv.style.display = 'none';
        histSection.style.display = '';
        if (!window._kdsHistorialCargado) {
            cargarHistorial();
            window._kdsHistorialCargado = true;
        }
    }
}

function cargarHistorial() {
    fetch('{{ route("restaurante.kds.historial") }}')
        .then(r => r.json())
        .then(function(data) {
            const ordenes = data.ordenes || [];
            const total   = data.total || 0;
            const totalM  = data['total$'] ?? data['total_$'] ?? 0;

            // Actualizar resumen
            const countEl = document.getElementById('kds-hist-count');
            if (countEl) {
                countEl.textContent = total + ' órdenes · Rd$ ' + renderMoneda(totalM);
            }

            const container = document.getElementById('kds-historial');
            if (ordenes.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="ui card kds-hist-card kds-hist-empty" style="max-width:560px;margin:2rem auto;text-align:center;">
                            <div class="card-body py-5">
                                <i class="bi bi-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                                <p class="text-muted mt-3 mb-0">No hay órdenes servidas en el último período</p>
                            </div>
                        </div>
                    </div>`;
                return;
            }

            container.innerHTML = ordenes.map(function(o, idx) {
                const esMesa = o.origen === 'mesa';
                const titulo = esMesa
                    ? (o.mesa || 'Mesa —')
                    : ('Orden #' + o.id + (o.tipo_orden ? ' · ' + o.tipo_orden : ''));

                let subInfo = '';
                if (!esMesa && o.cliente && o.cliente !== '—') {
                    subInfo = ' · Cliente: ' + o.cliente;
                } else if (esMesa && o.mesa) {
                    subInfo = ' · ' + o.mesa;
                }

                let tipoBadge = '';
                if (!esMesa) {
                    tipoBadge = o.tipo_orden ? `<span class="kds-hist-tipo">${o.tipo_orden}</span>` : '';
                }

                const productosHtml = o.productos.map(function(p) {
                    return `
                        <div class="kds-hist-item">
                            <span><strong>${p.cantidad}x</strong> ${p.nombre}</span>
                            ${p.notas ? '<small class="text-muted fst-italic">📝 ' + p.notas + '</small>' : ''}
                        </div>`;
                }).join('');

                return `
                <div class="col-xl-3 col-lg-4 col-md-6" style="animation-delay:${(idx % 8) * .05}s;">
                    <div class="kds-hist-card">
                        <div class="kds-hist-head">
                            <div>
                                <h6>${titulo}</h6>
                                ${subInfo ? '<small class="kds-card-meta">' + subInfo + '</small>' : ''}
                            </div>
                            <span class="kds-hist-badge">${esMesa ? '🍽 Mesa' : '🛵 Entrega'}</span>
                        </div>
                        <div class="kds-hist-meta">
                            <div><i class="bi bi-clock me-1"></i>${o.servido_at || '—'}</div>
                            <div><i class="bi bi-hourglass-split me-1"></i>${o.tiempo || '-'}</div>
                            ${tipoBadge ? '<div>' + tipoBadge + '</div>' : ''}
                        </div>
                        <div>${productosHtml}</div>
                        <div class="kds-hist-total">
                            <span class="fw-medium">${o.items_count} artículo(s)</span>
                            <strong>Rd$ ${renderMoneda(o.total)}</strong>
                        </div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(function(err) {
            console.error('Error cargando historial KDS:', err);
            document.getElementById('kds-historial').innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger py-2 mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar el historial
                    </div>
                </div>`;
        });
}
</script>
@endsection