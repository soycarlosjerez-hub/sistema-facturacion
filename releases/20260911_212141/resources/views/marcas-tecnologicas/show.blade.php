@extends('layouts.app')
@section('title', 'Ver Marca Tecnológica')

@push('styles')
@include('partials.premium-ui')
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
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $marcaTecnologica->nombre }}</h4>
                    <div class="ui-header-meta">Detalles de la marca tecnológica</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('marca-tecnologicas.edit')
                <a href="{{ route('marcas-tecnologicas.edit', $marcaTecnologica) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('marcas-tecnologicas.index') }}" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información General</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong>{{ $marcaTecnologica->nombre }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Website</small>
                        @if($marcaTecnologica->website)
                        <a href="{{ $marcaTecnologica->website }}" target="_blank" class="text-decoration-none">
                            <i class="bi bi-globe me-1"></i>{{ $marcaTecnologica->website }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">País</small>
                        <strong>{{ $marcaTecnologica->pais ?? '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Email de Contacto</small>
                        @if($marcaTecnologica->contacto_email)
                        <a href="mailto:{{ $marcaTecnologica->contacto_email }}" class="text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>{{ $marcaTecnologica->contacto_email }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge {{ $marcaTecnologica->activo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $marcaTecnologica->activo_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden</small>
                        <strong>{{ $marcaTecnologica->orden }}</strong>
                    </div>

                    @if($marcaTecnologica->logo_url)
                    <div class="text-center mb-3">
                        <img src="{{ $marcaTecnologica->logo_url }}" alt="{{ $marcaTecnologica->nombre }}" class="img-fluid rounded" style="max-height: 150px;" onerror="this.style.display='none'">
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block">Productos Registrados</small>
                        <span class="badge bg-info fs-6">{{ $marcaTecnologica->productos_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Productos de esta Marca</h6>
                </div>
                <div class="card-body">
                    @if($marcaTecnologica->productos && $marcaTecnologica->productos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marcaTecnologica->productos as $producto)
                                <tr>
                                    <td>
                                        <a href="{{ route('productos.show', $producto) }}" class="text-decoration-none">
                                            {{ $producto->nombre }}
                                        </a>
                                    </td>
                                    <td><code>{{ $producto->codigo_barras ?? '-' }}</code></td>
                                    <td>RD$ {{ number_format($producto->precio, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $producto->stock > 10 ? 'bg-success' : ($producto->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $producto->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $producto->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $producto->activo_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                        No hay productos registrados para esta marca
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
