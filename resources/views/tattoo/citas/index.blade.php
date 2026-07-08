@extends('layouts.app')

@section('title', 'Citas Agendadas')

@section('extra_css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .badge-estado { font-size: 0.75rem; padding: 0.35em 0.75em; }
    .badge-estado.pendiente { background:#fef3c7; color:#92400e; }
    .badge-estado.confirmada { background:#dbeafe; color:#1e40af; }
    .badge-estado.en_progreso { background:#ede9fe; color:#5b21b6; }
    .badge-estado.completada { background:#d1fae5; color:#065f46; }
    .badge-estado.cancelada { background:#fee2e2; color:#991b1b; }
    .badge-estado.no_show { background:#f3f4f6; color:#374151; }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2"></i>Citas</h2>
            <p class="text-muted mb-0">Gestión de agenda del estudio</p>
        </div>
        <a href="{{ route('tattoo.citas.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Nueva Cita
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-2 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <h6 class="text-muted mb-0">Pendientes</h6>
                <span class="fw-bold display-6" style="color:#f59e0b;">{{ $contadores['pendiente'] ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <h6 class="text-muted mb-0">Confirmadas</h6>
                <span class="fw-bold display-6" style="color:#3b82f6;">{{ $contadores['confirmada'] ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <h6 class="text-muted mb-0">Hoy</h6>
                <span class="fw-bold display-6" style="color:#8b5cf6;">{{ $contadores['hoy'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="citasTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Artista</th>
                            <th>Servicio</th>
                            <th>Fecha / Hora</th>
                            <th>Total</th>
                            <th>Depósito</th>
                            <th>Estado</th>
                            <th style="width:180px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->id }}</td>
                                <td>{{ $c->cliente?->nombre ?? '—' }}</td>
                                <td>
                                    @if($c->cliente?->telefono)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$c->cliente->telefono) }}" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-whatsapp text-success me-1"></i>{{ $c->cliente->telefono }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($c->artista)
                                        <span class="badge bg-dark rounded-pill px-2">{{ $c->artista->nombre_completo }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>RD${{ number_format($c->total_servicio, 0) }}</td>
                                <td>
                                    <div>{{ $c->fecha_hora_inicio?->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $c->fecha_hora_inicio?->format('h:i A') }}</small>
                                </td>
                                <td class="fw-bold">RD${{ number_format($c->total_final, 0) }}</td>
                                <td>
                                    @if($c->deposito_monto > 0)
                                        <span class="fw-bold" style="color:#a855f7;">RD${{ number_format($c->deposito_monto, 0) }}</span>
                                        @if($c->deposito_pagado)
                                            <br><span class="badge bg-success rounded-pill"><i class="bi bi-check"></i> Pagado</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-estado {{ $c->estado }} rounded-pill px-3">
                                        {{ match($c->estado) {
                                            'pendiente' => 'Pendiente',
                                            'confirmada' => 'Confirmada',
                                            'en_progreso' => 'En Progreso',
                                            'completada' => 'Completada',
                                            'cancelada' => 'Cancelada',
                                            'no_show' => 'No Show',
                                            default => $c->estado
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('tattoo.citas.edit', $c) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($c->estado === 'pendiente')
                                            <form action="{{ route('tattoo.citas.cambiar-estado', $c) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button class="btn btn-sm btn-outline-info rounded-pill ms-1" title="Confirmar"><i class="bi bi-check2"></i></button>
                                            </form>
                                        @endif
                                        @if(in_array($c->estado, ['pendiente','confirmada']))
                                            <form action="{{ route('tattoo.citas.cambiar-estado', $c) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="cancelada">
                                                <button class="btn btn-sm btn-outline-danger rounded-pill ms-1" title="Cancelar" onclick="return confirm('¿Cancelar esta cita?')"><i class="bi bi-x"></i></button>
                                            </form>
                                        @endif
                                        @if(in_array($c->estado, ['pendiente','confirmada', 'en_progreso']) && $c->saldo_pendiente > 0)
                                            <button class="btn btn-sm btn-outline-success rounded-pill ms-1" title="Pagar" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $c->id }}">
                                                <i class="bi bi-cash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    @if(in_array($c->estado, ['pendiente','confirmada', 'en_progreso']) && $c->saldo_pendiente > 0)
                                    <div class="modal fade" id="paymentModal{{ $c->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content rounded-4">
                                                <form action="{{ route('tattoo.citas.pagar', $c) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header border-0">
                                                        <h6 class="fw-bold">Registrar Pago</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted">Cita: <strong>#{{ $c->id }}</strong><br>
                                                        Total: <strong>RD${{ number_format($c->total_final, 0) }}</strong><br>
                                                        Pendiente: <strong style="color:#a855f7;">RD${{ number_format($c->saldo_pendiente, 0) }}</strong></p>
                                                        <label class="form-label small fw-bold">Monto</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">RD$</span>
                                                            <input type="number" name="monto" class="form-control" step="0.01" min="0.01" max="{{ $c->saldo_pendiente }}" value="{{ $c->saldo_pendiente }}" required>
                                                        </div>
                                                        <label class="form-label small fw-bold mt-2">Método</label>
                                                        <select name="metodo_pago" class="form-select">
                                                            <option value="efectivo">Efectivo</option>
                                                            <option value="tarjeta">Tarjeta</option>
                                                            <option value="transferencia">Transferencia</option>
                                                            <option value="mixto">Mixto</option>
                                                        </select>
                                                        <label class="form-label small fw-bold mt-2">Tipo</label>
                                                        <select name="tipo" class="form-select">
                                                            <option value="saldo">Saldo</option>
                                                            <option value="deposito">Depósito</option>
                                                            <option value="parcial">Parcial</option>
                                                        </select>
                                                        <label class="form-label small fw-bold mt-2">Referencia</label>
                                                        <input type="text" name="referencia" class="form-control" placeholder="Nº transacción...">
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold">
                                                            <i class="bi bi-check2-circle me-1"></i> Registrar Pago
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($citas->hasPages())
            <div class="card-footer bg-transparent">
                {{ $citas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('extra_js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#citasTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[5, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [9] }]
    });
});
</script>
@endsection
