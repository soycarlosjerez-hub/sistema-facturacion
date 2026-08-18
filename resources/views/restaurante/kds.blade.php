@extends('layouts.app')
@section('title', 'Pantalla de Cocina — KDS')
@section('content_class', 'px-0')
@push('styles')
@include('partials.premium-ui')
<style>
    /* Estado y urgencia de las tarjetas de cocina (presentación — no lo usa el JS como selector) */
    .kds-card { border-left: 4px solid #eab308; transition: all .2s ease; }
    .kds-card.urgent { border-left-color: #ef4444; animation: kds-pulse 2s infinite; }
    .kds-card.done { border-left-color: #22c55e; opacity: .7; }

    .kds-card-head {
        display: flex; justify-content: space-between; align-items: center;
        padding: .9rem 1rem .75rem;
        background: rgba(255,255,255,.6);
        border-bottom: 1px solid #f1f5f9;
    }
    .kds-card-meta { color: #64748b; font-size: .78rem; }
    .kds-card-num {
        font-weight: 700; font-size: .85rem; color: #1e293b;
        background: rgba(var(--accent-rgb, 16,185,129), .1);
        border: 1px solid rgba(var(--accent-rgb, 16,185,129), .25);
        border-radius: 999px; padding: .25rem .7rem;
    }
    .kds-card-body { padding: .5rem 1rem 1rem; }

    .kds-item { border-left: 3px solid transparent; padding: 4px 8px; margin: 2px 0; border-radius: 4px; font-size: .9rem; }
    .kds-item.entrada { border-left-color: #3b82f6; background: rgba(59,130,246,.05); }
    .kds-item.fuerte { border-left-color: #eab308; background: rgba(234,179,8,.05); }
    .kds-item.postre { border-left-color: #ec4899; background: rgba(236,72,153,.05); }
    .kds-item.bebida { border-left-color: #06b6d4; background: rgba(6,182,212,.05); }

    @keyframes kds-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.4); } 50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); } }
    .kds-btn-group { display: flex; gap: 4px; flex-wrap: wrap; }
    .kds-btn-group .ui-btn { font-size: .7rem; padding: .25rem .6rem; }

    /* Botón "Listo" en verde semántico (el accent del módulo es verde claro) */
    .kds-btn-listo, .kds-btn-listo:hover {
        background: linear-gradient(135deg, #16a34a, #15803d) !important;
        box-shadow: 0 4px 14px rgba(22,163,74,.3) !important;
        color: #fff !important;
    }

    .kds-empty i { color: #22c55e; }

    /* Dark mode — clases propias del KDS (el partial cubre .ui-*) */
    body.dark-mode .kds-card-head { background: rgba(30,41,59,.5); border-bottom-color: #1e293b; }
    body.dark-mode .kds-card-meta { color: #94a3b8; }
    body.dark-mode .kds-card-num { color: #f1f5f9; }
    body.dark-mode .kds-item.entrada { background: rgba(59,130,246,.12); }
    body.dark-mode .kds-item.fuerte { background: rgba(234,179,8,.12); }
    body.dark-mode .kds-item.postre { background: rgba(236,72,153,.12); }
    body.dark-mode .kds-item.bebida { background: rgba(6,182,212,.12); }
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
                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fs-6 border border-white border-opacity-25" id="kds-count">0 pendientes</span>
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Recargar
                </button>
            </div>
        </div>
    </div>
    <div class="row g-3" id="kds-orders"></div>
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
                container.innerHTML = '<div class="ui-empty-state kds-empty"><i class="bi bi-check2-circle"></i><p>Todas las órdenes están listas</p></div>';
                return;
            }

            container.innerHTML = ordenes.map(o => {
                let cursosHtml = '';
                const cursoOrden = ['entrada', 'fuerte', 'postre', 'bebida'];
                const cursoLabels = { entrada: 'Entradas', fuerte: 'Platos Fuertes', postre: 'Postres', bebida: 'Bebidas' };
                cursoOrden.forEach(cur => {
                    const items = o.cursos[cur];
                    if (!items || items.length === 0) return;
                    cursosHtml += `
                        <div class="mb-2">
                            <small class="fw-bold text-muted text-uppercase" style="font-size:.65rem;">${cursoLabels[cur] || cur}</small>
                            ${items.map(d => `
                                <div class="kds-item ${cur}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${d.cantidad}x</strong> ${d.producto?.nombre || '—'}
                                            ${d.notas ? `<br><small class="text-muted fst-italic">📝 ${d.notas}</small>` : ''}
                                        </div>
                                        <div class="kds-btn-group">
                                            ${d.estado_cocina === 'pendiente' ? `<button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" onclick="kdsActualizar(${d.id}, 'preparando')">Preparando</button>` : ''}
                                            ${d.estado_cocina === 'preparando' ? `<span class="badge bg-warning text-dark d-flex align-items-center rounded-pill px-2">Preparando</span> <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill kds-btn-listo" onclick="kdsActualizar(${d.id}, 'listo')">Listo</button>` : ''}
                                            ${d.estado_cocina === 'listo' ? `<span class="badge bg-success rounded-pill px-2">Listo</span> <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="kdsActualizar(${d.id}, 'servido')">Servido</button>` : ''}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>`;
                });

                const totalItems = Object.values(o.cursos || {}).flat().length;
                const tienePendientes = Object.values(o.cursos || {}).flat().some(d => d.estado_cocina === 'pendiente');
                const todosListos = Object.values(o.cursos || {}).flat().every(d => d.estado_cocina === 'listo' || d.estado_cocina === 'servido');

                return `
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="ui-card h-100 kds-card ${tienePendientes ? 'urgent' : todosListos ? 'done' : ''}">
                        <div class="ui-card-accent"></div>
                        <div class="kds-card-head">
                            <div>
                                <h5 class="fw-bold mb-0">${o.mesa}</h5>
                                <small class="kds-card-meta">${o.time} · ${totalItems} items</small>
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

function kdsActualizar(detalleId, estado) {
    fetch(`/restaurante/kds/update/${detalleId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado })
    })
    .then(r => r.json())
    .then(data => { if (data.success) cargarKds(); });
}

// Polling cada 10 segundos
document.addEventListener('DOMContentLoaded', function () {
    cargarKds();
    setInterval(cargarKds, 10000);
});
</script>
@endsection