@extends('layouts.app')
@section('title', $obra->titulo)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-image"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-eye me-1"></i>DETALLE
                    </span>
                    <h2 class="fw-bold mb-0 text-white">{{ $obra->titulo }}</h2>
                    <p class="mb-0 opacity-75">Detalle de la obra</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.obras.edit', $obra) }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('arte.obras.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="ui-card text-center p-0 overflow-hidden" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                @if($obra->imagen)
                    <img src="{{ asset('storage/' . $obra->imagen) }}" class="w-100" style="max-height:380px;object-fit:cover;" alt="{{ $obra->titulo }}">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height:320px;">
                        <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                    </div>
                @endif
                <div class="p-4">
                    <span class="badge bg-{{ $obra->estado_badge_class }} rounded-pill mb-2">{{ $obra->estado_label }}</span>
                    <h4 class="fw-bold mb-1">{{ $obra->titulo }}</h4>
                    <p class="text-muted mb-0">por <strong>{{ $obra->artista?->nombre ?? '—' }}</strong></p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Precio venta</span>
                        <span class="fw-bold">RD$ {{ number_format($obra->precio_venta, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted small">Precio compra</span>
                        <span>RD$ {{ number_format($obra->precio_compra, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted small">Margen</span>
                        <span class="fw-bold text-success">RD$ {{ number_format(max(0, $obra->precio_venta - $obra->precio_compra), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <h5 class="ui-card-title"><i class="bi bi-info-circle"></i>Información de la obra</h5>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Técnica</div>
                            <div class="fw-semibold">{{ $obra->tecnica ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Año de creación</div>
                            <div class="fw-semibold">{{ $obra->ano_creacion ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Dimensiones</div>
                            <div class="fw-semibold">{{ $obra->dimensiones ? $obra->dimensiones . ' cm' : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Material</div>
                            <div class="fw-semibold">{{ $obra->material ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Colección</div>
                            <div class="fw-semibold">{{ $obra->coleccion?->nombre ?? 'Sin colección' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Fecha de adquisición</div>
                            <div class="fw-semibold">{{ optional($obra->fecha_adquisicion)->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Descripción</div>
                            <div class="fw-semibold">{{ $obra->descripcion ?? 'Sin descripción' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($obra->consignacion)
            <div class="ui-card mt-3" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <h5 class="ui-card-title"><i class="bi bi-arrow-left-right"></i>Consignación activa</h5>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Consignante</div>
                            <div class="fw-semibold">{{ $obra->consignacion->consignante }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Comisión</div>
                            <div class="fw-semibold">{{ $obra->consignacion->porcentaje_comision }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="ui-card mt-3" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <h5 class="ui-card-title"><i class="bi bi-easel"></i>Exhibiciones</h5>
                <div class="ui-card-body">
                    @forelse($obra->exhibiciones as $exhibicion)
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill me-1 mb-1">{{ $exhibicion->nombre }}</span>
                    @empty
                        <span class="text-muted small">No está asignada a ninguna exhibición.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection