@extends('layouts.app')

@section('title', 'e-CF ' . $ecf->encf)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .card { background: rgba(15,23,42,.8); }
body.dark-mode .table { color: #cbd5e1; }
body.dark-mode .table-light { background: rgba(30,41,59,.6); }
body.dark-mode .table-light th { color: #94a3b8; border-color: #334155; }
body.dark-mode .table td { border-color: #1e293b; }
body.dark-mode .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .text-dark { color: #f1f5f9 !important; }
body.dark-mode .alert-success { background: rgba(16,185,129,.15); color: #6ee7b7; }
body.dark-mode .alert-warning { background: rgba(245,158,11,.15); color: #fcd34d; }
body.dark-mode .alert-danger { background: rgba(239,68,68,.15); color: #fca5a5; }
body.dark-mode .alert-info { background: rgba(59,130,246,.15); color: #93c5fd; }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .btn-success { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 14px rgba(16,185,129,.3); }
body.dark-mode .premium-card .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 14px rgba(239,68,68,.3); }
body.dark-mode .premium-card .btn-warning { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode code { background: rgba(30,41,59,.8); color: #93c5fd; padding: .2rem .4rem; border-radius: .25rem; }
</style>
@endpush

@section('content')
@php
    $estadoInfo = $ecf->estado_info;
    $venta = $ecf->venta;
    $cliente = $venta?->cliente;
@endphp

<div class="container-fluid px-4 premium-page animate__animated animate__fadeIn">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('ecf.index') }}" class="text-decoration-none text-white-50">e-CF</a></li>
                        <li class="breadcrumb-item active text-white">{{ $ecf->encf }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Detalle del Comprobante Electrónico</h3>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            <a href="{{ route('ventas.show', $ecf->venta_id) }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-receipt me-1"></i>Ver Venta
            </a>
            <a href="{{ route('ecf.pdf', $ecf) }}" target="_blank" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </a>
            @if($ecf->xml_content)
            <a href="{{ route('ecf.xml', $ecf) }}" target="_blank" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-filetype-xml me-1"></i>XML
            </a>
            @endif
        </div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-3"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning border-0 rounded-3 mb-3"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 mb-3"><i class="bi bi-x-circle me-1"></i>{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="premium-card">
                <div class="card-accent amber"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Comprobante</small>
                        <span class="badge bg-{{ $estadoInfo['color'] }} rounded-pill px-3">
                            <i class="bi {{ $estadoInfo['icon'] }} me-1"></i>{{ $estadoInfo['label'] }}
                        </span>
                    </div>
                    <h2 class="fw-bold text-primary mb-0" style="letter-spacing:2px;">{{ $ecf->encf }}</h2>
                    <small class="text-muted">{{ $ecf->tipo_nombre }}</small>

                    <hr class="my-3">

                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Tipo e-CF</span>
                            <span class="fw-bold">{{ $ecf->tipo_ecf }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Venta</span>
                            <span class="fw-bold">#{{ str_pad($ecf->venta_id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Fecha Emisión</span>
                            <span class="fw-bold">{{ $ecf->fecha_emision->format('d/m/Y h:i A') }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Vencimiento Secuencia</span>
                            <span class="fw-bold">{{ $ecf->secuencia->fecha_vencimiento->format('d/m/Y') }}</span>
                        </div>
                        @if($ecf->fecha_firma)
                        <div class="col-6">
                            <span class="text-muted d-block">Firmado</span>
                            <span class="fw-bold">{{ $ecf->fecha_firma->format('d/m/Y h:i A') }}</span>
                        </div>
                        @endif
                        @if($ecf->fecha_envio)
                        <div class="col-6">
                            <span class="text-muted d-block">Enviado DGII</span>
                            <span class="fw-bold">{{ $ecf->fecha_envio->format('d/m/Y h:i A') }}</span>
                        </div>
                        @endif
                        @if($ecf->fecha_aprobacion)
                        <div class="col-12">
                            <span class="text-muted d-block">Aprobado por DGII</span>
                            <span class="fw-bold text-success">{{ $ecf->fecha_aprobacion->format('d/m/Y h:i A') }}</span>
                        </div>
                        @endif
                        @if($ecf->track_id_dgii)
                        <div class="col-12">
                            <span class="text-muted d-block">Track ID DGII</span>
                            <code class="small">{{ $ecf->track_id_dgii }}</code>
                        </div>
                        @endif
                    </div>

                    <hr class="my-3">

                    <div class="text-center p-3 bg-light rounded-3">
                        <img src="{{ $qrUrl }}" alt="QR DGII" class="img-fluid" style="max-width:180px;">
                        <p class="small text-muted mb-0 mt-2">
                            <i class="bi bi-qr-code me-1"></i>QR de consulta DGII
                        </p>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        @if(in_array($ecf->estado, ['borrador', 'generado', 'rechazado'], true))
                            <form action="{{ route('ecf.firmar', $ecf) }}" method="POST">
                                @csrf
                                <button class="btn btn-primary w-100 rounded-pill">
                                    <i class="bi bi-pen me-1"></i>Firmar Documento
                                </button>
                            </form>
                        @endif

                        @if(in_array($ecf->estado, ['firmado', 'generado', 'rechazado'], true))
                            <form action="{{ route('ecf.enviar', $ecf) }}" method="POST">
                                @csrf
                                <button class="btn btn-success w-100 rounded-pill">
                                    <i class="bi bi-cloud-upload me-1"></i>Enviar a DGII
                                </button>
                            </form>
                        @endif

                        @if($ecf->track_id_dgii && $ecf->estado !== 'aprobado')
                            <form action="{{ route('ecf.consultar', $ecf) }}" method="POST">
                                @csrf
                                <button class="btn btn-warning w-100 rounded-pill">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Consultar Estado DGII
                                </button>
                            </form>
                        @endif

                        @if($ecf->notaCredito)
                            <a href="{{ route('ecf.show', $ecf->notaCredito) }}" class="btn btn-outline-warning w-100 rounded-pill">
                                <i class="bi bi-file-earmark-minus me-1"></i>Nota de Crédito E34: {{ $ecf->notaCredito->encf }}
                                <span class="badge bg-{{ $ecf->notaCredito->estado_info['color'] }} ms-1">{{ $ecf->notaCredito->estado_info['label'] }}</span>
                            </a>
                        @endif
                        @if($ecf->documentoOriginal)
                            <a href="{{ route('ecf.show', $ecf->documentoOriginal) }}" class="btn btn-outline-info w-100 rounded-pill">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i>Doc. Original: {{ $ecf->documentoOriginal->encf }}
                            </a>
                        @endif
                        @if($ecf->puedeAnular())
                            <button class="btn btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAnular">
                                <i class="bi bi-slash-circle me-1"></i>Anular e-CF (genera E34)
                            </button>
                        @endif
                        @if($ecf->estado === 'aprobado' && $ecf->tipo_ecf !== 'E33')
                            <form action="{{ route('ecf.nota-debito', $ecf) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-warning w-100 rounded-pill" onclick="return confirm('¿Generar Nota de Débito (E33) para corregir al alza este comprobante?')">
                                    <i class="bi bi-file-earmark-plus me-1"></i>Nota de Débito E33
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="premium-card mb-3">
                <div class="card-accent amber"></div>
                <div class="premium-card-title"><i class="bi bi-person icon-amber"></i> Datos del Cliente</div>
                <div class="card-body p-4">
                    @if($cliente)
                    <div class="row g-2 small">
                        <div class="col-md-6"><span class="text-muted d-block">Nombre</span><span class="fw-bold">{{ $cliente->nombre }}</span></div>
                        <div class="col-md-3"><span class="text-muted d-block">Documento</span><span class="fw-bold">{{ ucfirst($cliente->tipo_documento ?? 'N/A') }}</span></div>
                        <div class="col-md-3"><span class="text-muted d-block">RNC/Cédula</span><span class="fw-bold">{{ $cliente->rnc_cedula ?: 'N/A' }}</span></div>
                        <div class="col-md-6"><span class="text-muted d-block">Email</span><span>{{ $cliente->email ?: 'N/A' }}</span></div>
                        <div class="col-md-6"><span class="text-muted d-block">Tipo Cliente</span><span class="badge bg-info">{{ ucfirst(str_replace('_',' ', $cliente->tipo_cliente)) }}</span></div>
                    </div>
                    @else
                    <p class="text-muted mb-0">Sin cliente asociado</p>
                    @endif
                </div>
            </div>

            <div class="premium-card mb-3">
                <div class="card-accent amber"></div>
                <div class="premium-card-title"><i class="bi bi-cash-stack icon-amber"></i> Totales</div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Monto Gravado</span><span>RD$ {{ number_format($ecf->monto_gravado_total, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Monto Exento</span><span>RD$ {{ number_format($ecf->monto_exento_total, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">ITBIS</span><span>RD$ {{ number_format($ecf->itbis_total, 2) }}</span></div>
                    <div class="d-flex justify-content-between mt-3 pt-3 border-top"><span class="fw-bold h5 mb-0">TOTAL</span><span class="fw-bold h5 mb-0 text-primary">RD$ {{ number_format($ecf->monto_total, 2) }}</span></div>
                </div>
            </div>

            <div class="premium-card mb-3">
                <div class="card-accent amber"></div>
                <div class="premium-card-title"><i class="bi bi-list-ul icon-amber"></i> Items del Comprobante</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size:0.7rem; text-transform:uppercase;">
                                <th class="ps-3">#</th>
                                <th>Descripción</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $i => $d)
                            <tr>
                                <td class="ps-3 small">{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-bold small">{{ $d->producto->nombre }}</span>
                                    <br><small class="text-muted">{{ $d->producto->codigo_barras ?? 'N/A' }}</small>
                                </td>
                                <td class="text-end small">{{ $d->cantidad }}</td>
                                <td class="text-end small">RD$ {{ number_format($d->precio_unitario, 2) }}</td>
                                <td class="text-end pe-3 small fw-bold">RD$ {{ number_format($d->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($ecf->mensaje_dgii)
            <div class="alert alert-{{ $ecf->estado === 'aprobado' ? 'success' : 'danger' }} border-0 rounded-3">
                <strong>DGII:</strong> {{ $ecf->mensaje_dgii }}
            </div>
            @endif

            @if($ecf->logs->count())
            <div class="premium-card">
                <div class="card-accent amber"></div>
                <div class="premium-card-title"><i class="bi bi-list-task icon-amber"></i> Historial de Operaciones</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr style="font-size:0.7rem; text-transform:uppercase;">
                                <th class="ps-3">Fecha</th>
                                <th>Acción</th>
                                <th>Resultado</th>
                                <th class="text-end pe-3">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ecf->logs as $log)
                            <tr class="small">
                                <td class="ps-3">{{ $log->created_at->format('d/m/Y h:i:s') }}</td>
                                <td>{{ ucfirst($log->accion) }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->estado_resultado === 'exito' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $log->estado_resultado === 'exito' ? 'success' : 'danger' }} rounded-pill">
                                        {{ ucfirst($log->estado_resultado) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">{{ $log->duracion_ms ? $log->duracion_ms . ' ms' : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($ecf->puedeAnular())
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('ecf.anular', $ecf) }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-slash-circle me-2"></i>Anular e-CF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 small mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Proceso fiscal:</strong> Al anular este e-CF se generará automáticamente una
                        <strong>Nota de Crédito (E34)</strong> con los mismos montos, que será firmada y enviada a DGII.
                        Esta Nota de Crédito anula fiscalmente el comprobante original.
                    </div>
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>{{ $ecf->encf }}</strong> — Monto: RD$ {{ number_format($ecf->monto_total, 2) }}
                    </div>
                    <label class="form-label fw-bold small text-uppercase">Motivo <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control border-0 bg-light" rows="3" required minlength="5"></textarea>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Anular</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
