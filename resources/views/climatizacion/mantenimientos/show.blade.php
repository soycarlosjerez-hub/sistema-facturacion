@extends('layouts.app')

@section('title', 'Mantenimiento #' . $mantenimiento->numero)

@push('styles')
@include('partials.premium-ui')
<style>
    .ui-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .ui-detail-group {
        padding: .5rem 0;
    }
    .ui-detail-group .ui-detail-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: .25rem;
    }
    .ui-detail-group .ui-detail-value {
        font-size: 1rem;
        color: #1e293b;
        font-weight: 500;
    }
    body.dark-mode .ui-detail-group .ui-detail-label { color: #94a3b8; }
    body.dark-mode .ui-detail-group .ui-detail-value { color: #f1f5f9; }

    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: .5rem;
    }
    .timeline-dot.pendiente { background: #94a3b8; }
    .timeline-dot.programada { background: #3b82f6; }
    .timeline-dot.en_curso { background: #f59e0b; }
    .timeline-dot.completado { background: #10b981; }
    .timeline-dot.cancelado { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    {{-- HEADER --}}
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Mantenimiento {{ $mantenimiento->numero }}</h1>
                    <div class="ui-header-meta">
                        <span>Creado {{ optional($mantenimiento->created_at)->format('d/m/Y h:i A') ?? '-' }}</span>
                        <span class="divider">|</span>
                        <span>
                            @php
                                $estadoColor = match ($mantenimiento->estado) {
                                    'pendiente' => 'neutral',
                                    'programada' => 'info',
                                    'en_curso' => 'warning',
                                    'completado' => 'success',
                                    'cancelado' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp
                            <span class="ui-badge ui-badge-{{ $estadoColor }}">
                                <span class="timeline-dot {{ $mantenimiento->estado }}"></span>
                                {{ \App\Models\Mantenimiento::ESTADOS[$mantenimiento->estado] ?? $mantenimiento->estado }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.mantenimientos.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                @if (!in_array($mantenimiento->estado, ['completado', 'cancelado']))
                    <a href="{{ route('climatizacion.mantenimientos.edit', $mantenimiento) }}" class="ui-btn ui-btn-solid">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.05s;">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0 0 .75rem 0;">
                        <i class="bi bi-info-circle"></i> Información General
                    </h5>

                    <div class="ui-detail-grid">
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Número</div>
                            <div class="ui-detail-value">{{ $mantenimiento->numero }}</div>
                        </div>
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Tipo</div>
                            <div class="ui-detail-value">
                                @php $tipoColor = $mantenimiento->tipo === 'preventivo' ? 'info' : 'warning'; @endphp
                                <span class="ui-badge ui-badge-{{ $tipoColor }}">
                                    {{ \App\Models\Mantenimiento::TIPOS[$mantenimiento->tipo] ?? $mantenimiento->tipo }}
                                </span>
                            </div>
                        </div>
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Cliente</div>
                            <div class="ui-detail-value">
                                @if ($mantenimiento->cliente)
                                    <a href="{{ route('clientes.show', $mantenimiento->cliente) }}" class="text-decoration-none" style="color:var(--accent);">
                                        {{ $mantenimiento->cliente->nombre }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Técnico Asignado</div>
                            <div class="ui-detail-value">{{ $mantenimiento->tecnico?->name ?? 'Sin asignar' }}</div>
                        </div>
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Contrato Asociado</div>
                            <div class="ui-detail-value">
                                @if ($mantenimiento->contrato)
                                    {{ $mantenimiento->contrato->codigo }}
                                @else
                                    <span class="text-muted">Sin contrato</span>
                                @endif
                            </div>
                        </div>
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Programada para</div>
                            <div class="ui-detail-value">
                                {{ $mantenimiento->programada_para ? optional($mantenimiento->programada_para)->format('d/m/Y h:i A') : 'No programada' }}
                            </div>
                        </div>
                        @if ($mantenimiento->completada_en)
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Completada en</div>
                            <div class="ui-detail-value">{{ optional($mantenimiento->completada_en)->format('d/m/Y h:i A') }}</div>
                        </div>
                        @endif
                        <div class="ui-detail-group">
                            <div class="ui-detail-label">Creado por</div>
                            <div class="ui-detail-value">{{ $mantenimiento->creadoPor?->name ?? '-' }}</div>
                        </div>
                    </div>

                    @if ($mantenimiento->descripcion_falla)
                    <div class="mt-3 pt-3 border-top" style="border-color:#f1f5f9;">
                        <div class="ui-detail-label mb-2">Descripción de la Falla / Trabajo Realizado</div>
                        <div class="p-3 rounded" style="background:#f8fafc;color:#334155;font-size:.9rem;line-height:1.6;">
                            {{ $mantenimiento->descripcion_falla }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- COSTOS CARD --}}
            <div class="ui-card" style="--delay:.1s;">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0 0 .75rem 0;">
                        <i class="bi bi-cash-stack"></i> Costos
                    </h5>

                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f5f9;">
                        <span class="text-muted">Repuestos</span>
                        <span class="fw-semibold">RD$ {{ number_format($mantenimiento->costo_repuestos ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f5f9;">
                        <span class="text-muted">Mano de Obra</span>
                        <span class="fw-semibold">RD$ {{ number_format($mantenimiento->mano_de_obra ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold" style="font-size:1.2rem;color:var(--accent);">
                            RD$ {{ number_format($mantenimiento->total ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ESTADO / ACCIONES CARD --}}
            <div class="ui-card" style="--delay:.15s;">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0 0 .75rem 0;">
                        <i class="bi bi-arrow-right-circle"></i> Acciones
                    </h5>

                    @if (!in_array($mantenimiento->estado, ['completado', 'cancelado']))
                        @php
                            $nextState = match ($mantenimiento->estado) {
                                'pendiente' => 'programada',
                                'programada' => 'en_curso',
                                'en_curso' => 'completado',
                                default => null,
                            };
                        @endphp
                        @if ($nextState)
                            <form action="{{ route('climatizacion.mantenimientos.advance', $mantenimiento) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="next_state" value="{{ $nextState }}">
                                <button type="submit" class="ui-btn ui-btn-solid w-100 mb-2">
                                    <i class="bi bi-forward"></i>
                                    Avanzar a {{ \App\Models\Mantenimiento::ESTADOS[$nextState] }}
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('climatizacion.mantenimientos.edit', $mantenimiento) }}" class="ui-btn ui-btn-ghost w-100 mb-2">
                            <i class="bi bi-pencil"></i> Editar Mantenimiento
                        </a>

                        @if ($mantenimiento->estado !== 'completado')
                            <form action="{{ route('climatizacion.mantenimientos.destroy', $mantenimiento) }}"
                                  method="POST" onsubmit="return confirm('¿Eliminar este mantenimiento? Esta acción no se puede deshacer.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ui-btn ui-btn-danger w-100">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="text-center py-2 mb-2">
                            <span class="text-muted small">
                                <i class="bi bi-check-circle-fill text-{{ $mantenimiento->estado === 'completado' ? 'success' : 'secondary' }} me-1"></i>
                                Este mantenimiento está {{ strtolower(\App\Models\Mantenimiento::ESTADOS[$mantenimiento->estado]) }}.
                            </span>
                        </div>

                        @php
                            $yaFacturado = \App\Models\ClimatizacionFactura::where('origen', 'mantenimiento')
                                ->where('origen_id', $mantenimiento->id)->exists();
                        @endphp

                        @if ($yaFacturado)
                            @php
                                $factura = \App\Models\ClimatizacionFactura::where('origen', 'mantenimiento')
                                    ->where('origen_id', $mantenimiento->id)->first();
                            @endphp
                            <a href="{{ route('climatizacion.facturas.show', $factura) }}" class="ui-btn ui-btn-solid w-100 mb-2">
                                <i class="bi bi-receipt"></i> Ver Factura Generada
                            </a>
                        @elseif ($mantenimiento->total > 0)
                            <form action="{{ route('climatizacion.facturas.desde.mantenimiento', $mantenimiento) }}" method="POST">
                                @csrf
                                <button type="submit" class="ui-btn ui-btn-solid w-100 mb-2" onclick="return confirm('¿Generar factura por RD$ {{ number_format($mantenimiento->total, 2) }}?')">
                                    <i class="bi bi-receipt-cutoff"></i> Facturar
                                </button>
                            </form>
                        @else
                            <div class="text-center py-2">
                                <span class="text-muted small">Sin montos para facturar</span>
                            </div>
                        @endif

                        <a href="{{ route('climatizacion.facturas.index') }}" class="ui-btn ui-btn-ghost w-100">
                            <i class="bi bi-list-ul"></i> Ver Todas las Facturas
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- REPUESTOS TABLE --}}
    @if ($mantenimiento->repuestos_usados && count($mantenimiento->repuestos_usados) > 0)
    <div class="ui-card" style="--delay:.2s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <h5 class="ui-card-title">
                <i class="bi bi-box-seam"></i> Repuestos Utilizados
            </h5>
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Repuesto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mantenimiento->repuestos_usados as $idx => $repuesto)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="fw-medium">{{ $repuesto['nombre'] ?? '-' }}</td>
                            <td class="text-center">{{ $repuesto['cantidad'] ?? 1 }}</td>
                            <td class="text-end">RD$ {{ number_format($repuesto['precio'] ?? 0, 2) }}</td>
                            <td class="text-end fw-semibold" style="color:var(--accent);">
                                RD$ {{ number_format(($repuesto['cantidad'] ?? 0) * ($repuesto['precio'] ?? 0), 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Repuestos</td>
                            <td class="text-end fw-bold" style="color:var(--accent);">
                                RD$ {{ number_format($mantenimiento->costo_repuestos ?? 0, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TIMELINE / HISTORIAL --}}
    <div class="ui-card" style="--delay:.25s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h5 class="ui-card-title" style="padding:0 0 .75rem 0;">
                <i class="bi bi-clock-history"></i> Línea de Tiempo
            </h5>
            <div class="d-flex flex-wrap gap-3 align-items-center">
                @foreach (\App\Models\Mantenimiento::ESTADOS as $val => $label)
                    @php
                        $estadosOrdenados = array_keys(\App\Models\Mantenimiento::ESTADOS);
                        $currentIdx = array_search($mantenimiento->estado, $estadosOrdenados);
                        $thisIdx = array_search($val, $estadosOrdenados);
                        $isPast = $thisIdx <= $currentIdx;
                        $isCurrent = $val === $mantenimiento->estado;
                        $dotColor = match ($val) {
                            'pendiente' => '#94a3b8',
                            'programada' => '#3b82f6',
                            'en_curso' => '#f59e0b',
                            'completado' => '#10b981',
                            'cancelado' => '#ef4444',
                            default => '#94a3b8',
                        };
                    @endphp
                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 {{ $isCurrent ? 'border' : '' }}"
                         style="{{ $isCurrent ? 'border-color:' . $dotColor . '!important;background:rgba(' . ($val === 'pendiente' ? '148,163,184' : ($val === 'programada' ? '59,130,246' : ($val === 'en_curso' ? '245,158,11' : ($val === 'completado' ? '16,185,129' : '239,68,68'))) ) . ',.05);' : '' }}">
                        <span class="timeline-dot {{ $val }}" style="background:{{ $dotColor }};opacity:{{ $isPast ? 1 : .3 }};"></span>
                        <span style="font-weight:{{ $isCurrent ? 700 : 500 }};color:{{ $isPast ? '#1e293b' : '#94a3b8' }};font-size:.85rem;">
                            {{ $label }}
                        </span>
                        @if ($isCurrent)
                            <span class="ui-badge ui-badge-primary" style="font-size:.6rem;padding:.15rem .5rem;">ACTUAL</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
