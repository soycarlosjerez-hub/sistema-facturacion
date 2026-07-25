@extends('layouts.app')

@section('title', 'Factura #' . $climatizacionFactura->id)

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-row { display: flex; justify-content: space-between; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
    .detail-row:last-child { border-bottom: none; }
    body.dark-mode .detail-row { border-color: #334155; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    {{-- HEADER --}}
    <div class="ui-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-receipt"></i></div>
                <div>
                    <h1 class="ui-header-title">Factura #{{ $climatizacionFactura->id }}</h1>
                    <div class="ui-header-meta">
                        <span>Creada {{ optional($climatizacionFactura->created_at)->format('d/m/Y h:i A') }}</span>
                        <span class="divider">|</span>
                        <span>
                            <span class="ui-badge ui-badge-{{ match($climatizacionFactura->estado) {
                                'borrador' => 'secondary',
                                'generada' => 'info',
                                'enviada' => 'success',
                                'anulada' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ \App\Models\ClimatizacionFactura::ESTADOS[$climatizacionFactura->estado] ?? $climatizacionFactura->estado }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.facturas.index') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">
            {{-- CLIENT INFO --}}
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-person"></i> Datos del Cliente</h5>
                    <div class="detail-row">
                        <span class="text-muted">Nombre</span>
                        <strong>{{ $climatizacionFactura->cliente->nombre ?? 'Consumidor Final' }}</strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Origen</span>
                        <span class="ui-badge ui-badge-{{ match($climatizacionFactura->origen) {
                            'mantenimiento' => 'info',
                            'contrato_cuota' => 'success',
                            'instalacion' => 'warning',
                            'emergencia' => 'danger',
                            default => 'secondary'
                        } }}">{{ \App\Models\ClimatizacionFactura::ORIGENES[$climatizacionFactura->origen] ?? $climatizacionFactura->origen }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Referencia</span>
                        <strong>{{ $climatizacionFactura->referencia ?? '-' }}</strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Creada por</span>
                        <span>{{ $climatizacionFactura->creadoPor?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- DETAIL LINES --}}
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-0">
                    <h5 class="ui-card-title px-3 pt-3 pb-2"><i class="bi bi-list-ul"></i> Detalle de Conceptos</h5>
                    <div class="table-responsive">
                        <table class="ui-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concepto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($climatizacionFactura->detalle && count($climatizacionFactura->detalle) > 0)
                                    @foreach ($climatizacionFactura->detalle as $idx => $linea)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-medium">{{ $linea['descripcion'] ?? '-' }}</td>
                                        <td class="text-center">{{ $linea['cantidad'] ?? 1 }}</td>
                                        <td class="text-end">RD$ {{ number_format($linea['precio_unitario'] ?? 0, 2) }}</td>
                                        <td class="text-end fw-semibold" style="color:var(--accent);">
                                            RD$ {{ number_format($linea['subtotal'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="5" class="text-center text-muted py-3">Sin detalle</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">
            {{-- TOTALS --}}
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-calculator"></i> Totales</h5>
                    <div class="detail-row">
                        <span class="text-muted">Subtotal</span>
                        <strong>RD$ {{ number_format($climatizacionFactura->subtotal, 2) }}</strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">ITBIS (18%)</span>
                        <strong>RD$ {{ number_format($climatizacionFactura->itbis, 2) }}</strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Descuento</span>
                        <strong>RD$ {{ number_format($climatizacionFactura->descuento, 2) }}</strong>
                    </div>
                    <div class="detail-row" style="border-top:2px solid var(--accent);padding-top:.75rem;margin-top:.5rem;">
                        <span class="fw-bold">TOTAL</span>
                        <span class="fw-bold" style="font-size:1.3rem;color:var(--accent);">
                            RD$ {{ number_format($climatizacionFactura->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-gear"></i> Acciones</h5>

                    @if ($climatizacionFactura->estado === 'borrador')
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle"></i> Borrador listo para generar factura DGII.
                        </div>
                        <div class="mb-2">
                            <a href="#" id="btnGenerar" class="ui-btn ui-btn-solid w-100">
                                <i class="bi bi-send"></i> Generar Factura DGII
                            </a>
                        </div>
                        <form action="{{ route('climatizacion.facturas.anular', $climatizacionFactura) }}"
                              method="POST" class="d-inline w-100"
                              onsubmit="return confirm('¿Anular esta factura? Esta acción no se puede deshacer.');">
                            @csrf @method('PUT')
                            <button type="submit" class="ui-btn ui-btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Anular
                            </button>
                        </form>
                    @elseif ($climatizacionFactura->estado === 'anulada')
                        <div class="text-center py-3">
                            <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle-fill"></i> ANULADA</span>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle-fill"></i> GENERADA</span>
                            @if ($climatizacionFactura->referencia)
                                <div class="mt-2 small text-muted">NCF: {{ $climatizacionFactura->referencia }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- LINKED RECORD --}}
    @php
        $origenModelo = null;
        if ($climatizacionFactura->origen === 'mantenimiento') {
            $origenModelo = \App\Models\Mantenimiento::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'contrato_cuota') {
            $origenModelo = \App\Models\ContratoMantenimiento::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'emergencia') {
            $origenModelo = \App\Models\OrdenEmergencia::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'instalacion') {
            $origenModelo = \App\Models\Instalacion::find($climatizacionFactura->origen_id);
        }
    @endphp

    @if ($origenModelo)
    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h5 class="ui-card-title"><i class="bi bi-link-45deg"></i> Registro Origen</h5>
            <div class="detail-row">
                <span class="text-muted">Tipo</span>
                <strong>{{ \App\Models\ClimatizacionFactura::ORIGENES[$climatizacionFactura->origen] }}</strong>
            </div>
            <div class="detail-row">
                <span class="text-muted">Identificador</span>
                <strong>{{ $origenModelo->numero ?? $origenModelo->codigo ?? '#' . $origenModelo->id }}</strong>
            </div>
            <div class="detail-row">
                <span class="text-muted">Estado Original</span>
                <span>
                    @if (isset($origenModelo->estado))
                        @php
                            $constName = $climatizacionFactura->origen === 'mantenimiento' ? '\App\Models\Mantenimiento::ESTADOS' :
                                         ($climatizacionFactura->origen === 'contrato_cuota' ? '\App\Models\ContratoMantenimiento::ESTADOS' :
                                         ($climatizacionFactura->origen === 'emergencia' ? '\App\Models\OrdenEmergencia::ESTADOS' :
                                         '\App\Models\Instalacion::ESTADOS'));
                        @endphp
                        {{ ($constName[$origenModelo->estado] ?? $origenModelo->estado) }}
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="mt-2">
                <a href="{{ match($climatizacionFactura->origen) {
                    'mantenimiento' => route('climatizacion.mantenimientos.show', $origenModelo),
                    'contrato_cuota' => route('climatizacion.contratos.show', $origenModelo),
                    'emergencia' => route('climatizacion.ordenes-emergencia.show', $origenModelo),
                    'instalacion' => route('climatizacion.instalaciones.show', $origenModelo),
                    default => '#'
                } }}" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-box-arrow-up-right"></i> Ir al Registro
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('btnGenerar')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!confirm('¿Confirmar generación de factura DGII para esta factura?')) return;

    fetch('{{ route("climatizacion.facturas.generar", $climatizacionFactura) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al generar factura.');
        }
    })
    .catch(err => alert('Error de conexión.'));
});
</script>
@endpush
@endsection
