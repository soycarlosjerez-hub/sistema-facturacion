<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">MRR</div>
                <span class="badge {{ $mrrGrowthRate >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $mrrGrowthRate >= 0 ? 'success' : 'danger' }} rounded-pill px-2 py-1">
                    <i class="bi bi-arrow-{{ $mrrGrowthRate >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($mrrGrowthRate) }}%
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#8b5cf6;">{{ $systemMoneda ?? 'RD$' }} {{ number_format($mrr, 2) }}</div>
            <div class="ui-stat-sub">Facturación recurrente mensual</div>
        </div>
    </div>
</div>
