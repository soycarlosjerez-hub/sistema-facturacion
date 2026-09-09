<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Collection Rate</div>
                <span class="badge {{ $collectionRate >= 80 ? 'bg-success' : 'bg-warning' }} bg-opacity-10 text-{{ $collectionRate >= 80 ? 'success' : 'warning' }} rounded-pill px-2 py-1">
                    {{ $collectionRate >= 80 ? 'Ok' : 'Bajo' }}
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#3b82f6;">{{ $collectionRate }}%</div>
            <div class="ui-stat-sub">{{ $systemMoneda ?? 'RD$' }} {{ number_format($currentMonthCollected, 2) }} cobrado</div>
        </div>
    </div>
</div>
