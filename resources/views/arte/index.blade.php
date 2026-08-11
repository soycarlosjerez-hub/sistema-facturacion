@extends('layouts.app')
@section('title', 'Galería de Arte')
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
                    <i class="bi bi-palette"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Galería de Arte</h2>
                    <p class="text-white text-opacity-75 mb-0">Terminal de gestión de obras, artistas y exhibiciones</p>
                </div>
            </div>
            <a href="{{ route('arte.obras.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-brush me-1"></i> Gestionar Obras
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="premium-card h-100">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle bg-white bg-opacity-10">
                        <i class="bi bi-images text-white"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $totalObras }}</div>
                        <div class="text-muted small">Obras en catálogo</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card h-100">
                <div class="card-accent green"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle bg-white bg-opacity-10">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $disponibles }}</div>
                        <div class="text-muted small">Disponibles</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card h-100">
                <div class="card-accent red"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle bg-white bg-opacity-10">
                        <i class="bi bi-bag-check text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $vendidas }}</div>
                        <div class="text-muted small">Vendidas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card h-100">
                <div class="card-accent blue"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle bg-white bg-opacity-10">
                        <i class="bi bi-easel text-info"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $enExhibicion }}</div>
                        <div class="text-muted small">En exhibición</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="small text-muted">Artistas</div>
                <div class="fs-4 fw-bold">{{ $totalArtistas }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="small text-muted">Colecciones</div>
                <div class="fs-4 fw-bold">{{ $totalColecciones }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="small text-muted">Exhibiciones activas</div>
                <div class="fs-4 fw-bold">{{ $exhibicionesActivas }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="small text-muted">Valor inventario</div>
                <div class="fs-4 fw-bold">RD$ {{ number_format($valorInventario, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Obras recientes</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Obra</th>
                                <th>Artista</th>
                                <th>Estado</th>
                                <th class="text-end">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasObras as $obra)
                            <tr>
                                <td class="fw-medium">{{ $obra->titulo }}</td>
                                <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                                <td><span class="badge bg-{{ $obra->estado_badge_class }} rounded-pill">{{ $obra->estado_label }}</span></td>
                                <td class="text-end fw-bold">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay obras registradas todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2"></i>Accesos rápidos</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('arte.obras.create') }}" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg me-1"></i>Nueva Obra</a>
                    <a href="{{ route('arte.artistas.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-person-badge me-1"></i>Artistas</a>
                    <a href="{{ route('arte.colecciones.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-collection me-1"></i>Colecciones</a>
                    <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-easel me-1"></i>Exhibiciones</a>
                    <a href="{{ route('arte.consignaciones.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left-right me-1"></i>Consignaciones</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
