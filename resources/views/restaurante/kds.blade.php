@extends('layouts.app')
@section('title', 'Pantalla de Cocina — KDS')
@section('content_class', 'px-0')
@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        border-radius: 1rem;
        padding: 1.25rem 2rem;
        color: white;
        margin-bottom: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(217, 119, 6, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .kds-card { transition: all .2s ease; border-left: 4px solid #eab308; }
.kds-card.urgent { border-left-color: #ef4444; animation: kds-pulse 2s infinite; }
.kds-card.done { border-left-color: #22c55e; opacity: .7; }
.kds-item { border-left: 3px solid transparent; padding: 4px 8px; margin: 2px 0; border-radius: 4px; font-size: .9rem; }
.kds-item.entrada { border-left-color: #3b82f6; background: rgba(59,130,246,.05); }
.kds-item.fuerte { border-left-color: #eab308; background: rgba(234,179,8,.05); }
.kds-item.postre { border-left-color: #ec4899; background: rgba(236,72,153,.05); }
.kds-item.bebida { border-left-color: #06b6d4; background: rgba(6,182,212,.05); }
@keyframes kds-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.4); } 50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); } }
.kds-btn-group { display: flex; gap: 4px; flex-wrap: wrap; }
.kds-btn-group .btn { font-size: .7rem; padding: 2px 8px; }
</style>
@endpush
@section('content')
<div class="container-fluid py-3 px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-egg-fried fs-3 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Pantalla de Cocina</h2>
                    <small class="text-white text-opacity-75" id="kds-clock">{{ now()->format('d/m/Y h:i:s A') }}</small>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fs-6 border border-white border-opacity-25" id="kds-count">0 pendientes</span>
                <button class="btn btn-light rounded-pill btn-sm fw-bold shadow-sm" onclick="location.reload()">
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
                container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i><h5>Todas las órdenes están listas</h5></div>';
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
                                            ${d.estado_cocina === 'pendiente' ? `<button class="btn btn-warning btn-sm rounded-pill" onclick="kdsActualizar(${d.id}, 'preparando')">Preparando</button>` : ''}
                                            ${d.estado_cocina === 'preparando' ? `<span class="badge bg-warning text-dark d-flex align-items-center rounded-pill px-2">Preparando</span> <button class="btn btn-success btn-sm rounded-pill" onclick="kdsActualizar(${d.id}, 'listo')">Listo</button>` : ''}
                                            ${d.estado_cocina === 'listo' ? `<span class="badge bg-success rounded-pill px-2">Listo</span> <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="kdsActualizar(${d.id}, 'servido')">Servido</button>` : ''}
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
                    <div class="card border-0 shadow-sm rounded-4 h-100 kds-card ${tienePendientes ? 'urgent' : todosListos ? 'done' : ''}">
                        <div class="card-header bg-white rounded-top-4 border-0 d-flex justify-content-between align-items-center pt-3 px-3 pb-2">
                            <div>
                                <h5 class="fw-bold mb-0">${o.mesa}</h5>
                                <small class="text-muted">${o.time} · ${totalItems} items</small>
                            </div>
                            <span class="badge bg-dark rounded-pill">#${o.id}</span>
                        </div>
                        <div class="card-body pt-1 px-3">
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