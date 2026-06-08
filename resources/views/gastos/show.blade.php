@extends('layouts.app')

@section('title', 'Detalle del Gasto')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-eye text-warning me-2"></i>
                Detalle del Gasto
            </h2>
            <p class="text-muted mb-0">{{ $gasto->descripcion }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('gastos.edit')
            <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-warning rounded-pill">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            @endcan
            <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Información del Gasto</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Descripción</div>
                        <div class="col-md-8 fw-semibold">{{ $gasto->descripcion }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Monto</div>
                        <div class="col-md-8 fw-bold text-warning fs-5">RD$ {{ number_format($gasto->monto, 2) }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Categoría</div>
                        <div class="col-md-8">
                            @if($gasto->categoria)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ \App\Models\Gasto::categorias()[$gasto->categoria] ?? $gasto->categoria }}</span>
                            @else
                                <span class="text-muted">Sin categoría</span>
                            @endif
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Método de Pago</div>
                        <div class="col-md-8">{{ $gasto->metodo_pago ? ucfirst($gasto->metodo_pago) : '—' }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">N° Comprobante</div>
                        <div class="col-md-8">{{ $gasto->comprobante ?: '—' }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Fecha del Gasto</div>
                        <div class="col-md-8">{{ $gasto->fecha_gasto->format('d/m/Y') }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Notas</div>
                        <div class="col-md-8">{{ $gasto->notas ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Registrado por</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-person-circle fs-2 text-warning"></i>
                    </div>
                    <h6 class="fw-bold">{{ $gasto->user?->name ?? '—' }}</h6>
                    <small class="text-muted">{{ $gasto->created_at->format('d/m/Y h:i A') }}</small>
                    @if($gasto->caja)
                        <hr class="my-3">
                        <div class="text-start">
                            <small class="text-muted d-block">Caja: <span class="fw-semibold">{{ $gasto->caja->nombre }}</span></small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
