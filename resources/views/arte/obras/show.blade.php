@extends('layouts.app')
@section('title', $obra->titulo)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-image"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">{{ $obra->titulo }}</h2>
                    <p class="text-white text-opacity-75 mb-0">Detalle de la obra</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('arte.obras.edit', $obra) }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('arte.obras.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="premium-card text-center p-0 overflow-hidden">
                <div class="card-accent purple"></div>
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
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Información de la obra</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Técnica</div>
                        <div class="fw-medium">{{ $obra->tecnica ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Año de creación</div>
                        <div class="fw-medium">{{ $obra->ano_creacion ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Dimensiones</div>
                        <div class="fw-medium">{{ $obra->dimensiones ? $obra->dimensiones . ' cm' : '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Material</div>
                        <div class="fw-medium">{{ $obra->material ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Colección</div>
                        <div class="fw-medium">{{ $obra->coleccion?->nombre ?? 'Sin colección' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Fecha de adquisición</div>
                        <div class="fw-medium">{{ optional($obra->fecha_adquisicion)->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted">Descripción</div>
                        <div class="fw-medium">{{ $obra->descripcion ?? 'Sin descripción' }}</div>
                    </div>
                </div>
            </div>

            @if($obra->consignacion)
            <div class="premium-card mt-3">
                <div class="card-accent green"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-right me-2"></i>Consignación activa</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Consignante</div>
                        <div class="fw-medium">{{ $obra->consignacion->consignante }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Comisión</div>
                        <div class="fw-medium">{{ $obra->consignacion->porcentaje_comision }}%</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="premium-card mt-3">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-easel me-2"></i>Exhibiciones</h6>
                @forelse($obra->exhibiciones as $exhibicion)
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill me-1 mb-1">{{ $exhibicion->nombre }}</span>
                @empty
                    <span class="text-muted small">No está asignada a ninguna exhibición.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
