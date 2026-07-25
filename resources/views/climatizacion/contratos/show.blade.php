@extends('layouts.app')
@section('title', 'Contrato: '.$contrato->codigo)

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
body.dark-mode .ui-user-avatar-green { background: rgba(34,211,238,.15); border-color: rgba(34,211,238,.3); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $contrato->codigo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <i class="bi bi-person me-1"></i>{{ $contrato->cliente?->nombre ?? 'Sin cliente' }}
                        <span class="mx-2">·</span>
                        <a href="{{ route('climatizacion.contratos.index') }}" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @if($contrato->estado === 'borrador')
                    <form action="{{ route('climatizacion.contratos.activar', $contrato) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"
                                onclick="return confirm('¿Activar este contrato?')">
                            <i class="bi bi-play-circle me-1"></i> Activar
                        </button>
                    </form>
                @endif
                @if(in_array($contrato->estado, ['activo','borrador']))
                    <form action="{{ route('climatizacion.contratos.cancelar', $contrato) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill"
                                onclick="return confirm('¿Cancelar este contrato?')">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                    </form>
                @endif
                @if($contrato->estado !== 'cancelado')
                    <a href="{{ route('climatizacion.contratos.edit', $contrato) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Columna izquierda: Detalles del contrato --}}
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-info-circle"></i> Información del Contrato
                    </h5>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            @php
                                $estadoBadge = match($contrato->estado) {
                                    'borrador' => 'neutral',
                                    'activo' => 'success',
                                    'vencido' => 'danger',
                                    'cancelado' => 'neutral',
                                    default => 'neutral',
                                };
                                $proximo = $contrato->estaActivo() && $contrato->vigencia_hasta <= now()->addDays(30);
                                if ($proximo) $estadoBadge = 'warning';
                            @endphp
                            @if($proximo)
                                <span class="ui-badge ui-badge-warning">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ \App\Models\ContratoMantenimiento::ESTADOS[$contrato->estado] ?? $contrato->estado }}
                                </span>
                            @else
                                <span class="ui-badge ui-badge-{{ $estadoBadge }}">
                                    {{ \App\Models\ContratoMantenimiento::ESTADOS[$contrato->estado] ?? $contrato->estado }}
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cliente</span>
                        <span class="ui-detail-value">
                            {{ $contrato->cliente?->nombre ?? '-' }}
                            @if($contrato->cliente)
                                <br><small class="text-muted">{{ $contrato->cliente->identificacion ?? '' }}</small>
                            @endif
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Periodicidad</span>
                        <span class="ui-detail-value">{{ \App\Models\ContratoMantenimiento::PERIODICIDADES[$contrato->tipo_periodicidad] ?? $contrato->tipo_periodicidad }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Vigencia</span>
                        <span class="ui-detail-value">
                            {{ $contrato->vigencia_desde?->format('d/m/Y') ?? '-' }}
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            {{ $contrato->vigencia_hasta?->format('d/m/Y') ?? '-' }}
                            @if($proximo)
                                <span class="ui-badge ui-badge-warning ms-2">Próximo a vencer</span>
                            @endif
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Valor Mensual</span>
                        <span class="ui-detail-value fw-bold" style="color:var(--accent,#06b6d4);">
                            RD$ {{ number_format($contrato->valor_mensual ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado por</span>
                        <span class="ui-detail-value">
                            {{ $contrato->creadoPor?->name ?? 'Sistema' }}
                            <br><small class="text-muted">{{ $contrato->created_at?->format('d/m/Y h:i A') }}</small>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Cobertura --}}
            <div class="ui-card" style="--delay:.15s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-shield-check"></i> Cobertura
                    </h5>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Incluye Visitas</span>
                        <span class="ui-detail-value">
                            @if($contrato->incluye_visitas)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Sí</span>
                            @else
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i> No</span>
                            @endif
                        </span>
                    </div>

                    @if($contrato->incluye_visitas)
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Visitas Realizadas</span>
                        <span class="ui-detail-value">
                            <span class="ui-badge ui-badge-primary">{{ $contrato->visitas_realizadas ?? 0 }}/{{ $contrato->num_visitas_anuales ?? 0 }}</span>
                        </span>
                    </div>
                    @endif

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Deducible</span>
                        <span class="ui-detail-value">RD$ {{ number_format($contrato->deducible ?? 0, 2) }}</span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cobertura Máxima</span>
                        <span class="ui-detail-value">RD$ {{ number_format($contrato->cobertura_maxima ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Equipos, Visitas y Mantenimientos --}}
        <div class="col-lg-7">

            {{-- Equipos Cubiertos --}}
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-cpu"></i> Equipos Cubiertos
                    </h5>
                    @if($contrato->equipos_cubiertos)
                        @if(is_array($contrato->equipos_cubiertos))
                            <ul class="mb-0">
                                @foreach($contrato->equipos_cubiertos as $equipo)
                                    <li>{{ $equipo }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-muted">{{ $contrato->equipos_cubiertos }}</p>
                        @endif
                    @else
                        <p class="mb-0 text-muted"><i class="bi bi-dash-circle me-1"></i> No se especificaron equipos.</p>
                    @endif
                </div>
            </div>

            {{-- Visitas Programadas --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-calendar-check"></i> Visitas Programadas
                        <span class="ui-badge ui-badge-primary ms-2">{{ $contrato->visitas->count() }}</span>
                    </h5>
                    @if($contrato->visitas->count() > 0)
                        <div class="table-responsive">
                            <table class="ui-table dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contrato->visitas as $visita)
                                        <tr>
                                            <td>{{ $visita->fecha_programada?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $visita->tecnico ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $vBadge = match($visita->estado ?? 'pendiente') {
                                                        'realizada' => 'success',
                                                        'pendiente' => 'warning',
                                                        'cancelada' => 'danger',
                                                        default => 'neutral',
                                                    };
                                                @endphp
                                                <span class="ui-badge ui-badge-{{ $vBadge }}">
                                                    {{ ucfirst($visita->estado ?? 'pendiente') }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($visita->observaciones ?? '-', 50) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ui-empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <p>No hay visitas programadas</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mantenimientos --}}
            <div class="ui-card" style="--delay:.25s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-wrench-adjustable"></i> Mantenimientos Realizados
                        <span class="ui-badge ui-badge-primary ms-2">{{ $contrato->mantenimientos->count() }}</span>
                    </h5>
                    @if($contrato->mantenimientos->count() > 0)
                        <div class="table-responsive">
                            <table class="ui-table dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Descripción</th>
                                        <th>Técnico</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contrato->mantenimientos as $m)
                                        <tr>
                                            <td>{{ $m->fecha?->format('d/m/Y') ?? $m->created_at?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $m->descripcion ?? $m->observaciones ?? '-' }}</td>
                                            <td>{{ $m->tecnico ?? '-' }}</td>
                                            <td class="fw-semibold">RD$ {{ number_format($m->costo ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ui-empty-state">
                            <i class="bi bi-tools"></i>
                            <p>No hay mantenimientos registrados</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Botones inferiores --}}
    <div class="d-flex gap-2 mt-4 mb-3">
        <a href="{{ route('climatizacion.contratos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
        @if($contrato->estado !== 'cancelado')
            <a href="{{ route('climatizacion.contratos.edit', $contrato) }}" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-pencil"></i> Editar Contrato
            </a>
        @endif

        @if($contrato->estado === 'activo')
            @php
                $yaFacturado = \App\Models\ClimatizacionFactura::where('origen', 'contrato_cuota')
                    ->where('origen_id', $contrato->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->exists();
            @endphp
            @if($yaFacturado)
                @php
                    $factura = \App\Models\ClimatizacionFactura::where('origen', 'contrato_cuota')
                        ->where('origen_id', $contrato->id)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->first();
                @endphp
                <a href="{{ route('climatizacion.facturas.show', $factura) }}" class="ui-btn ui-btn-primary rounded-pill">
                    <i class="bi bi-receipt me-1"></i> Ver Factura del Mes
                </a>
            @else
                <form action="{{ route('climatizacion.facturas.desde.contrato', $contrato) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-primary rounded-pill"
                            onclick="return confirm('¿Generar factura de cuota mensual por RD$ {{ number_format($contrato->valor_mensual, 2) }}?');">
                        <i class="bi bi-receipt-cutoff me-1"></i> Facturar Cuota
                    </button>
                </form>
            @endif
        @endif
    </div>
</div>
@endsection
