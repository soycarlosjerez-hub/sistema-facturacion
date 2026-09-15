<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#ef4444"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Errores (7d)</div>
                <span class="badge {{ $erroresNoResueltos7d > 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 text-{{ $erroresNoResueltos7d > 0 ? 'danger' : 'success' }} rounded-pill px-2 py-1">
                    {{ $erroresNoResueltos7d > 0 ? 'Activos' : 'Sin errores' }}
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#ef4444;">{{ $erroresNoResueltos7d }}</div>
            <div class="ui-stat-sub">{{ $totalSinResolver }} sin resolver total</div>
        </div>
    </div>
</div>
