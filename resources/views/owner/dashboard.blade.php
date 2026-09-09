@extends('layouts.app')
@section('title', 'Panel Ejecutivo - Dueño del Sistema')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    {{-- ========== HEADER ========== --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Panel Ejecutivo</h2>
                    <p class="mb-0 opacity-75">Resumen general de Erpipos ERP · {{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('owner.instances.create') }}" class="ui-btn ui-btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Instancia
                    </a>
                    <a href="{{ route('owner.plans.index') }}" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-card-checklist me-1"></i>Planes
                    </a>
                    <a href="{{ route('owner.business-types.index') }}" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-tags me-1"></i>Tipos
                    </a>
                    <a href="{{ route('owner.modules.index') }}" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-grid me-1"></i>Módulos
                    </a>
                    <a href="{{ route('owner.activity.history') }}" class="ui-btn ui-btn-ghost ui-btn-primary btn-sm">
                        <i class="bi bi-clock-history me-1"></i>Audit Log
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== ALERT BANNER ========== --}}
    @include('owner.partials._alert_banner')

    {{-- ========== ROW 1: Financial KPIs ========== --}}
    <div class="row g-3 mb-4">
        @include('owner.partials._kpi_mrr')
        @include('owner.partials._kpi_arr')
        @include('owner.partials._kpi_growth')
        @include('owner.partials._kpi_collection')
        @include('owner.partials._kpi_arpu')
        @include('owner.partials._kpi_active')
    </div>

    {{-- ========== ROW 2: Health & Operations KPIs ========== --}}
    <div class="row g-3 mb-4">
        @include('owner.partials._kpi_new')
        @include('owner.partials._kpi_active_users')
        @include('owner.partials._kpi_churn')
        @include('owner.partials._kpi_errors')
        @include('owner.partials._kpi_overdue')
        @include('owner.partials._kpi_pending')
    </div>

    {{-- ========== ROW 3: Charts (3 columns) ========== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            @include('owner.partials._chart_mrr_trend')
        </div>
        <div class="col-lg-4">
            @include('owner.partials._chart_revenue_trend')
        </div>
        <div class="col-lg-3">
            @include('owner.partials._chart_errors')
        </div>
    </div>

    {{-- ========== ROW 4: Health + Plan Distribution ========== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            @include('owner.partials._chart_instance_health')
        </div>
        <div class="col-lg-7">
            @include('owner.partials._chart_plan_distribution')
        </div>
    </div>

    {{-- ========== ROW 5: Activity + Renewals + Action Items ========== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            @include('owner.partials._activity_feed')
        </div>
        <div class="col-lg-4">
            @include('owner.partials._upcoming_renewals')
        </div>
        <div class="col-lg-4">
            @include('owner.partials._action_items')
        </div>
    </div>

    {{-- ========== ROW 6: Overdue Instances ========== --}}
    @include('owner.partials._overdue_table')

    {{-- ========== ROW 7: Recent Instances ========== --}}
    @include('owner.partials._recent_instances_table')

</div>
</div>

{{-- ========== Chart.js Data ========== --}}
<script>
window.ownerDashboard = {
    mrrLabels: {!! json_encode($mrrChartLabels) !!},
    mrrData: {!! json_encode($mrrChartData) !!},
    revenueLabels: {!! json_encode($revenueChartLabels) !!},
    revenueData: {!! json_encode($revenueChartData) !!},
    errorLabels: {!! json_encode($errorLabels) !!},
    errorData: {!! json_encode($errorChartData) !!},
    planDistribution: {!! json_encode($planDistribution) !!},
    healthData: {
        active: {{ $activas }},
        trial: {{ $enPrueba }},
        blocked: {{ $bloqueadas }},
        churnRisk: {{ $churnRiskCount }},
        pending: {{ $pendingApprovals }},
        archived: {{ $archivadas }},
    }
};
</script>

@push('scripts')
@vite(['resources/js/dashboard.js'])
@endpush

@endsection
