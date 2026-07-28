@extends('layouts.app')

@section('title', 'Orden #' . $orden->numero_orden)

@push('styles')
@include('partials.premium-ui')
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #3b82f6;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
.status-timeline {
    position: relative;
    padding-left: 2rem;
}
.status-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0.5rem;
    bottom: 0.5rem;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.25rem;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot {
    position: absolute;
    left: -1.65rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #cbd5e1;
    border: 2px solid white;
}
.timeline-item.active .timeline-dot { background: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,0.2); }
.timeline-item.completed .timeline-dot { background: #10b981; }
body.dark-mode .info-item { background: rgba(30,41,59,.8); }
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
body.dark-mode .timeline-item::before { background: #334155; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Orden #{{ $orden->numero_orden }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        {{ $orden->cliente->nombre }}
                        <span class="divider">·</span>
                        <i class="bi bi-phone me-1"></i>
                        {{ $orden->equipo ? $orden->equipo->serial_imei : 'Sin equipo registrado' }}
                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-{{ match($orden->estado) {
                            'recibido' => 'secondary',
                            'pendiente' => 'warning',
                            'diagnosticando' => 'info',
                            'en_reparacion' => 'primary',
                            'esperando_piezas' => 'warning',
                            'listo_para_entrega' => 'success',
                            'terminado' => 'success',
                            'entregado' => 'success',
                            'cancelado' => 'danger',
                            default => 'secondary'
                        }}">{{ $orden->estado_label }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnicas.edit')
                @if(!in_array($orden->estado, ['entregado', 'cancelado']))
                <a href="{{ route('tecnicas.edit', $orden) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endif
                @endcan
                <a href="{{ route('tecnicas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Subtotal</div>
                    <div class="ui-stat-value">RD$ {{ number_format($orden->subtotal, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">ITBIS (18%)</div>
                    <div class="ui-stat-value">RD$ {{ number_format($orden->itbis, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Descuento</div>
                    <div class="ui-stat-value text-danger">-RD$ {{ number_format($orden->descuento, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total</div>
                    <div class="ui-stat-value text-primary">RD$ {{ number_format($orden->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="ui-card mb-4" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Cliente</div>
                <div class="ui-card-body">
                    <div class="info-item mb-3">
                        <div class="label">Nombre</div>
                        <div class="value">{{ $orden->cliente->nombre }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">RNC / Cédula</div>
                        <div class="value">{{ $orden->cliente->rnc_cedula ?? '—' }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Teléfono</div>
                        <div class="value">{{ $orden->cliente->telefono ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Email</div>
                        <div class="value">{{ $orden->cliente->email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-phone"></i> Equipo</div>
                <div class="ui-card-body">
                    @if($orden->equipo)
                    <div class="info-item mb-3">
                        <div class="label">Serial / IMEI</div>
                        <div class="value">{{ $orden->equipo->serial_imei }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Marca / Modelo</div>
                        <div class="value">{{ $orden->equipo->marca }} {{ $orden->equipo->modelo }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Color</div>
                        <div class="value">{{ $orden->equipo->color ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Almacenamiento</div>
                        <div class="value">{{ $orden->equipo->almacenamiento_gb ?? '—' }} GB</div>
                    </div>
                    @else
                    <p class="text-muted">Equipo no registrado</p>
                    @endif
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-clock-history"></i> Timeline</div>
                <div class="ui-card-body">
                    <div class="status-timeline">
                        <div class="timeline-item {{ in_array($orden->estado, ['recibido', 'pendiente', 'diagnosticando', 'en_reparacion', 'esperando_piezas', 'listo_para_entrega', 'terminado', 'entregado']) ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <strong>Recibido</strong>
                            <div class="small text-muted">{{ $orden->fecha_recibo ? $orden->fecha_recibo->format('d/m/Y H:i') : '—' }}</div>
                        </div>
                        <div class="timeline-item {{ in_array($orden->estado, ['diagnosticando', 'en_reparacion', 'esperando_piezas', 'listo_para_entrega', 'terminado', 'entregado']) ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <strong>Diagnosticando</strong>
                        </div>
                        <div class="timeline-item {{ in_array($orden->estado, ['en_reparacion', 'esperando_piezas', 'listo_para_entrega', 'terminado', 'entregado']) ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <strong>En Reparación</strong>
                        </div>
                        <div class="timeline-item {{ in_array($orden->estado, ['listo_para_entrega', 'terminado', 'entregado']) ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <strong>Listo para Entrega</strong>
                        </div>
                        <div class="timeline-item {{ $orden->estado === 'entregado' ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <strong>Entregado</strong>
                            <div class="small text-muted">{{ $orden->fecha_entrega_real ? $orden->fecha_entrega_real->format('d/m/Y H:i') : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ui-card mb-4" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-exclamation-triangle"></i> Problema Reportado</div>
                <div class="ui-card-body">
                    <p class="mb-0">{{ $orden->problema_reportado }}</p>
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-search"></i> Diagnóstico</div>
                <div class="ui-card-body">
                    <p class="mb-0">{{ $orden->diagnostico ?? '<span class="text-muted">Sin diagnóstico aún</span>' }}</p>
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.4s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-check-circle"></i> Solución Aplicada</div>
                <div class="ui-card-body">
                    <p class="mb-0">{{ $orden->solucion_aplicada ?? '<span class="text-muted">Sin solución aún</span>' }}</p>
                </div>
            </div>

            @if($orden->detallesPiezas && $orden->detallesPiezas->count() > 0)
            <div class="ui-card mb-4" style="--delay:.45s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-box-seam"></i> Piezas Utilizadas</div>
                <div class="ui-card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->detallesPiezas as $pieza)
                                <tr>
                                    <td>{{ $pieza->producto->nombre ?? 'N/A' }}</td>
                                    <td>{{ $pieza->cantidad }}</td>
                                    <td>RD$ {{ number_format($pieza->precio_unitario, 2) }}</td>
                                    <td>RD$ {{ number_format($pieza->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="ui-card" style="--delay:.5s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-sticky"></i> Notas</div>
                <div class="ui-card-body">
                    <p class="mb-0">{{ $orden->notas ?? '<span class="text-muted">Sin notas</span>' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
