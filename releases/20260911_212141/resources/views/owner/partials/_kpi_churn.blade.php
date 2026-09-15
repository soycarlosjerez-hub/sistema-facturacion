<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Churn Risk</div>
                <span class="badge {{ $churnRiskCount > 0 ? 'bg-warning' : 'bg-success' }} bg-opacity-10 text-{{ $churnRiskCount > 0 ? 'warning' : 'success' }} rounded-pill px-2 py-1">
                    {{ $churnRiskCount > 0 ? 'Atención' : 'Ok' }}
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#f59e0b;">{{ $churnRiskCount }}</div>
            <div class="ui-stat-sub">Instancias en riesgo de fuga</div>
        </div>
    </div>
</div>
