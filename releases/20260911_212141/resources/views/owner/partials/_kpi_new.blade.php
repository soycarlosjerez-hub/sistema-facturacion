<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Nuevas Este Mes</div>
                <span class="badge {{ $growthRate >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $growthRate >= 0 ? 'success' : 'danger' }} rounded-pill px-2 py-1">
                    <i class="bi bi-arrow-{{ $growthRate >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($growthRate) }}%
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#f59e0b;">{{ $nuevasEsteMes }}</div>
            <div class="ui-stat-sub">vs {{ $nuevasMesAnt }} el mes anterior</div>
        </div>
    </div>
</div>
