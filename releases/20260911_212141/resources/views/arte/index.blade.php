@extends('layouts.app')
@section('title', 'Galería de Arte')
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
                    <i class="bi bi-palette"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-speedometer2 me-1"></i>DASHBOARD
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Galería de Arte</h2>
                    <p class="mb-0 opacity-75">Terminal de gestión de obras, artistas y exhibiciones</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.obras.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-brush me-1"></i> Gestionar Obras
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.15s;--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-images me-1"></i>Obras en catálogo</div>
                    <div class="ui-stat-value">{{ $totalObras }}</div>
                    <div class="ui-stat-sub">Piezas registradas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.2s;--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-check-circle me-1"></i>Disponibles</div>
                    <div class="ui-stat-value">{{ $disponibles }}</div>
                    <div class="ui-stat-sub">A la venta</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.25s;--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626">
                <div class="ui-card-accent" style="background:#ef4444"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-bag-check me-1"></i>Vendidas</div>
                    <div class="ui-stat-value">{{ $vendidas }}</div>
                    <div class="ui-stat-sub">Historial de ventas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.3s;--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-easel me-1"></i>En exhibición</div>
                    <div class="ui-stat-value">{{ $enExhibicion }}</div>
                    <div class="ui-stat-sub">Obras en salas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.35s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-person-badge me-1"></i>Artistas</div>
                    <div class="ui-stat-value">{{ $totalArtistas }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.4s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-collection me-1"></i>Colecciones</div>
                    <div class="ui-stat-value">{{ $totalColecciones }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.45s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-calendar-event me-1"></i>Exhibiciones activas</div>
                    <div class="ui-stat-value">{{ $exhibicionesActivas }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat h-100" style="--delay:.5s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label"><i class="bi bi-cash-stack me-1"></i>Valor inventario</div>
                    <div class="ui-stat-value" style="font-size:1.15rem;">RD$ {{ number_format($valorInventario, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.55s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <h5 class="ui-card-title"><i class="bi bi-clock-history"></i>Obras recientes</h5>
                <div class="ui-card-body p-0">
                    <div class="table-responsive">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Obra</th>
                                    <th>Artista</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasObras as $obra)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $obra->titulo }}</td>
                                    <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                                    <td><span class="badge bg-{{ $obra->estado_badge_class }} rounded-pill">{{ $obra->estado_label }}</span></td>
                                    <td class="text-end pe-4 fw-bold">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No hay obras registradas todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.6s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <h5 class="ui-card-title"><i class="bi bi-lightning"></i>Accesos rápidos</h5>
                <div class="ui-card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('arte.obras.create') }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-plus-lg me-1"></i>Nueva Obra</a>
                        <a href="{{ route('arte.artistas.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-person-badge me-1"></i>Artistas</a>
                        <a href="{{ route('arte.colecciones.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-collection me-1"></i>Colecciones</a>
                        <a href="{{ route('arte.exhibiciones.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-easel me-1"></i>Exhibiciones</a>
                        <a href="{{ route('arte.consignaciones.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-arrow-left-right me-1"></i>Consignaciones</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection