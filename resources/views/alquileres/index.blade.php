@extends('layouts.app')
@section('title', 'Dashboard Alquileres')

@push('styles')
@include('partials.premium-ui')
<style>
    .card-accent.indigo { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
    .card-accent.emerald { background: linear-gradient(90deg, #10b981, #06b6d4); }
    .card-accent.amber { background: linear-gradient(90deg, #f59e0b, #f97316); }
    .card-accent.rose { background: linear-gradient(90deg, #f43f5e, #ec4899); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.2; }
    .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle"><i class="bi bi-building"></i></div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Alquileres</h4>
                    <small class="text-white opacity-75"><i class="bi bi-info-circle me-1"></i>Gestión de viviendas, inquilinos, contratos y pagos de alquiler</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-accent indigo"></div>
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="bi bi-house-door"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['total_viviendas'] }}</div>
                        <div class="stat-label">Viviendas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-accent emerald"></div>
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['disponibles'] }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-accent amber"></div>
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-file-earmark-text"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['contratos_activos'] }}</div>
                        <div class="stat-label">Contratos Activos</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-accent rose"></div>
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon" style="background:rgba(244,63,94,.1);color:#f43f5e;"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-value">RD$ {{ number_format($stats['pagos_mes'], 2) }}</div>
                        <div class="stat-label">Cobrado este Mes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-accent indigo"></div>
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning text-primary me-2"></i>Acceso Rápido</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('alquileres.viviendas.create') }}" class="btn btn-outline-primary text-start rounded-3 py-2">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Vivienda
                        </a>
                        <a href="{{ route('alquileres.inquilinos.create') }}" class="btn btn-outline-primary text-start rounded-3 py-2">
                            <i class="bi bi-person-plus me-2"></i>Nuevo Inquilino
                        </a>
                        <a href="{{ route('alquileres.contratos.create') }}" class="btn btn-outline-primary text-start rounded-3 py-2">
                            <i class="bi bi-file-earmark-plus me-2"></i>Nuevo Contrato
                        </a>
                        <a href="{{ route('alquileres.pagos.create') }}" class="btn btn-outline-primary text-start rounded-3 py-2">
                            <i class="bi bi-cash-coin me-2"></i>Registrar Pago
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="premium-card">
                <div class="card-accent amber"></div>
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Próximos Vencimientos</h6>
                    <small class="text-muted">Contratos que vencen en los próximos 30 días</small>
                </div>
                <div class="card-body p-0">
                    @if($proximosVencimientos->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-check d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>
                            <p class="fw-semibold text-muted">No hay contratos próximos a vencer</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Vivienda</th>
                                        <th>Inquilino</th>
                                        <th>Inicio</th>
                                        <th>Vence</th>
                                        <th class="pe-4">Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximosVencimientos as $c)
                                        @php $dias = now()->diffInDays($c->fecha_fin, false); @endphp
                                        <tr>
                                            <td class="ps-4 fw-semibold">{{ $c->vivienda->nombre }}</td>
                                            <td>{{ $c->inquilino->nombre }}</td>
                                            <td>{{ $c->fecha_inicio->format('d/m/Y') }}</td>
                                            <td>{{ $c->fecha_fin->format('d/m/Y') }}</td>
                                            <td class="pe-4">
                                                @if($dias <= 0)
                                                    <span class="badge bg-danger rounded-pill">Vencido</span>
                                                @elseif($dias <= 7)
                                                    <span class="badge bg-warning text-dark rounded-pill">{{ $dias }} días</span>
                                                @else
                                                    <span class="badge bg-info rounded-pill">{{ $dias }} días</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
