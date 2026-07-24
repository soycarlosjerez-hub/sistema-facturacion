@extends('layouts.app')

@section('title', 'Climatización - Dashboard')

@push('styles')
@include('partials.premium-ui')
<style>
/* ============================================================
   CLIMATIZACIÓN — Dashboard Specific Styles
   ============================================================ */

/* Quick-link stat cards become clickable with a subtle lift */
a .ui-stat {
    transition: all .3s ease;
    cursor: pointer;
}
a:hover .ui-stat {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0,0,0,.12);
}

/* Remove bottom margin on grid stat items used inside quick-links */
.ui-card-body .ui-stat {
    margin-bottom: 0;
}

/* Dark mode overrides for the module accent */
body.dark-mode .ui-page {
    --accent: #06b6d4;
    --accent-rgb: 6, 182, 212;
    --accent-hover: #0891b2;
}

/* Responsive stat value sizing */
@media (max-width: 575.98px) {
    a .ui-stat .ui-stat-body {
        padding: 1rem 1.25rem;
    }
    a .ui-stat .ui-stat-body [style*="font-size:1.8rem"] {
        font-size: 1.4rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- ============================================================
         HEADER — Animated gradient + floating bubbles
         ============================================================ --}}
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-wind"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Climatización</h1>
                    <div class="ui-header-meta">
                        <span>Panel de control de mantenimiento y servicios</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STAT CARDS — 4 key metrics
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Contratos Activos --}}
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.1s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">
                        <i class="bi bi-file-earmark-text me-1"></i>Contratos Activos
                    </div>
                    <div class="ui-stat-value">{{ \App\Models\ContratoMantenimiento::activos()->count() }}</div>
                    <div class="ui-stat-sub">Contratos de mantenimiento vigentes</div>
                </div>
            </div>
        </div>

        {{-- Próximos a Vencer --}}
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.2s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">
                        <i class="bi bi-calendar-event me-1"></i>Próximos a Vencer
                    </div>
                    <div class="ui-stat-value">{{ \App\Models\ContratoMantenimiento::proximosAVencer(30)->count() }}</div>
                    <div class="ui-stat-sub">Vencen en los próximos 30 días</div>
                </div>
            </div>
        </div>

        {{-- Órdenes Críticas --}}
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.3s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">
                        <i class="bi bi-exclamation-triangle me-1"></i>Órdenes Críticas
                    </div>
                    <div class="ui-stat-value">{{ \App\Models\OrdenEmergencia::criticas()->count() }}</div>
                    <div class="ui-stat-sub">Atención inmediata requerida</div>
                </div>
            </div>
        </div>

        {{-- Tickets Abiertos --}}
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.4s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">
                        <i class="bi bi-ticket-perforated me-1"></i>Tickets Abiertos
                    </div>
                    <div class="ui-stat-value">{{ \App\Models\TicketGarantia::abiertos()->count() }}</div>
                    <div class="ui-stat-sub">Garantías con atención pendiente</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         QUICK LINKS — Accesos directos a módulos
         ============================================================ --}}
    <div class="ui-card" style="--delay:.5s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            Accesos Rápidos
        </div>
        <div class="ui-card-body">
            <div class="row g-3">

                {{-- Contratos --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.contratos.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:0s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:var(--accent);">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Contratos</div>
                                    <div class="ui-stat-sub mb-0">Gestionar contratos de mantenimiento</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Instalaciones --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.instalaciones.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:.1s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:#10b981;">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Instalaciones</div>
                                    <div class="ui-stat-sub mb-0">Registro de instalaciones realizadas</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Mantenimientos --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.mantenimientos.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:.2s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:#f59e0b;">
                                    <i class="bi bi-wrench-adjustable"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Mantenimientos</div>
                                    <div class="ui-stat-sub mb-0">Órdenes de mantenimiento programado</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Emergencias --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:.3s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:#ef4444;">
                                    <i class="bi bi-exclamation-octagon"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Emergencias</div>
                                    <div class="ui-stat-sub mb-0">Órdenes de emergencia activas</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Garantías --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.tickets-garantia.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:.4s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:#3b82f6;">
                                    <i class="bi bi-ticket-perforated"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Garantías</div>
                                    <div class="ui-stat-sub mb-0">Tickets de garantía y servicio</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Tipos de Equipo --}}
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="text-decoration-none d-block">
                        <div class="ui-stat mb-0" style="--delay:.5s;">
                            <div class="ui-stat-body d-flex align-items-center gap-3">
                                <div style="font-size:1.8rem;color:#64748b;">
                                    <i class="bi bi-cpu"></i>
                                </div>
                                <div>
                                    <div class="ui-stat-label">Tipos de Equipo</div>
                                    <div class="ui-stat-sub mb-0">Catálogo de equipos de climatización</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
