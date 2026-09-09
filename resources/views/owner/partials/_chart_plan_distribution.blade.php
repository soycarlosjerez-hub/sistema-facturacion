<div class="ui-card h-100" style="--delay:.3s">
    <div class="ui-card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-card-list me-2" style="color:#06b6d4"></i>Distribución por Plan</h5>
        <div style="height:200px;">
            <canvas id="planDistributionChart"></canvas>
        </div>
        <div class="mt-3">
            @forelse($planDistribution as $planName => $count)
                @php
                    $total = array_sum($planDistribution);
                    $pct = $total > 0 ? round($count / $total * 100) : 0;
                    $colors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#84cc16'];
                @endphp
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:10px;height:10px;border-radius:3px;background:{{ $colors[$loop->index % count($colors)] }};display:inline-block;"></span>
                        <span class="small fw-semibold">{{ $planName }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="width:80px;height:5px;">
                            <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $colors[$loop->index % count($colors)] }};"></div>
                        </div>
                        <span class="small fw-bold">{{ $count }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-inbox"></i>
                    <small>Sin planes configurados</small>
                </div>
            @endforelse
        </div>
    </div>
</div>
