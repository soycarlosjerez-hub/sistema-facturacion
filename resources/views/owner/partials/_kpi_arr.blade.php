<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#6d28d9"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">ARR</div>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                    <i class="bi bi-arrow-up-short"></i>{{ abs($mrrGrowthRate) }}%
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#6d28d9;">{{ $systemMoneda ?? 'RD$' }} {{ number_format($arr, 2) }}</div>
            <div class="ui-stat-sub">Facturación anual proyectada</div>
        </div>
    </div>
</div>
