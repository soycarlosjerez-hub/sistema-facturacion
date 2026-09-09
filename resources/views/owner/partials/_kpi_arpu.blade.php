<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#06b6d4"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">ARPU</div>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1">
                    Promedio
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#06b6d4;">{{ $systemMoneda ?? 'RD$' }} {{ number_format($arpu, 2) }}</div>
            <div class="ui-stat-sub">Ingreso promedio por instancia</div>
        </div>
    </div>
</div>
