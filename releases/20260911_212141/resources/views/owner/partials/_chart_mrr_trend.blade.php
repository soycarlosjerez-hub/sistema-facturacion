<div class="ui-card h-100" style="--delay:.1s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color:#8b5cf6"></i>Tendencia MRR</h5>
                <small class="text-muted">Últimos 12 meses · Cobrado</small>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="color:#8b5cf6">{{ $systemMoneda ?? 'RD$' }} {{ number_format($currentMonthCollected, 0) }}</div>
                <small class="text-muted">Este mes</small>
            </div>
        </div>
        <div style="height:220px;">
            <canvas id="mrrTrendChart"></canvas>
        </div>
    </div>
</div>
