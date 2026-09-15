<div class="ui-card h-100" style="--delay:.15s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2" style="color:#10b981"></i>Ingresos Cobrados</h5>
                <small class="text-muted">Últimos 6 meses</small>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="color:#10b981">{{ $systemMoneda ?? 'RD$' }} {{ number_format($revenueChartData[array_key_last($revenueChartData)] ?? 0, 0) }}</div>
                <small class="text-muted">Este mes</small>
            </div>
        </div>
        <div style="height:220px;">
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>
</div>
