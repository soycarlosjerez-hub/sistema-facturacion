@extends('layouts.app')
@section('title', $proveedore->nombre)
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-truck text-primary me-2"></i>{{ $proveedore->nombre }}</h2>
            <p class="text-muted mb-0">Detalle del proveedor</p>
        </div>
        <div>
            <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('proveedores.edit', $proveedore) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Editar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                    <h4 class="fw-bold">{{ $proveedore->nombre }}</h4>
                    <p class="text-muted small mb-1"><i class="bi bi-geo-alt"></i> {{ $proveedore->direccion ?? 'Sin dirección' }}</p>
                    <p class="text-muted small mb-1"><i class="bi bi-envelope"></i> {{ $proveedore->email ?? '—' }}</p>
                    <p class="text-muted small mb-3"><i class="bi bi-telephone"></i> {{ $proveedore->telefono ?? '—' }}</p>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Activo</span>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Información Fiscal</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">RNC</small>
                            <span class="fw-bold">{{ $proveedore->rnc ?? '—' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Tipo de Persona</small>
                            <span class="fw-bold">{{ $proveedore->tipo_persona === 'juridica' ? 'Jurídica' : ($proveedore->tipo_persona === 'fisica' ? 'Física' : '—') }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Sujeto a Retención ISR</small>
                            @if($proveedore->sujeto_retencion_isr)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Sí</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">No</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block">Sujeto a Retención ITBIS</small>
                            @if($proveedore->sujeto_retencion_itbis)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Sí</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success me-2"></i>Compras Registradas</h5>
                </div>
                <div class="card-body p-0">
                    @if($proveedore->compras->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase text-muted">
                                        <th class="ps-4">#</th>
                                        <th>Fecha</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proveedore->compras->take(10) as $c)
                                        <tr>
                                            <td class="ps-4">{{ $c->id }}</td>
                                            <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($c->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted p-4 mb-0">No hay compras registradas para este proveedor.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection