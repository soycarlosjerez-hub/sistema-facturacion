<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#dc2626"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Deuda Pendiente</div>
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">
                    {{ $instanciasConAtraso->count() }} instancias
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.35rem;color:#dc2626;">{{ $systemMoneda ?? 'RD$' }} {{ number_format($deudaTotal, 2) }}</div>
            <div class="ui-stat-sub">Total deudas por cobrar</div>
        </div>
    </div>
</div>
