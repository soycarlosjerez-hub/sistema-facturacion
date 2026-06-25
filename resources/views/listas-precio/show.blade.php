@extends('layouts.app')

@section('title', $listaPrecio->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
</style>
@endpush

@section('content')
<div class="premium-page">
    <div class="container-fluid px-4">
        <div class="premium-header">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $listaPrecio->nombre }}</h2>
                        <p class="mb-0 opacity-75">{{ $listaPrecio->codigo }} &middot; {{ $listaPrecio->items->count() }} productos</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold me-2">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                    @can('listas-precio.edit')
                    <a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" style="background: linear-gradient(135deg, #8b5cf6, #a855f7); border: none;">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="p-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2" style="color: #8b5cf6;"></i>Productos en esta lista</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaPrecios">
                            <thead class="table-light">
                                <tr class="text-muted text-uppercase small">
                                    <th class="ps-4">C&oacute;digo</th>
                                    <th>Producto</th>
                                    <th class="text-end">Precio Actual</th>
                                    <th class="text-end pe-4">Precio Lista</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $itemsList = $listaPrecio->items; @endphp
                                @forelse($itemsList as $item)
                                <tr>
                                    <td class="ps-4"><span class="badge bg-light text-muted rounded-pill">{{ $item->producto?->codigo ?? $item->producto?->codigo_barras ?? '—' }}</span></td>
                                    <td class="fw-bold small">{{ $item->producto?->nombre ?? 'Producto Eliminado' }}</td>
                                    <td class="text-end text-muted">RD$ {{ number_format($item->producto?->precio ?? 0, 2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        RD$ {{ number_format($item->precio, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Esta lista a&uacute;n no tiene productos configurados.
                                        @can('listas-precio.edit')
                                        <br><a href="{{ route('listas-precio.edit', $listaPrecio) }}" class="btn btn-sm btn-outline-primary mt-2" style="border-color: #8b5cf6; color: #8b5cf6;">Agregar productos</a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card mb-3">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2" style="color: #8b5cf6;"></i>Informaci&oacute;n</h6>
                        <div class="small">
                            <div class="mb-2"><span class="text-muted">Estado:</span>
                                @if($listaPrecio->activa)
                                    <span class="badge bg-success rounded-pill">Activa</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Inactiva</span>
                                @endif
                            </div>
                            @if($listaPrecio->vigencia_desde)
                            <div class="mb-2"><span class="text-muted">Vigencia:</span> {{ $listaPrecio->vigencia_desde->format('d/m/Y') }} - {{ $listaPrecio->vigencia_hasta?->format('d/m/Y') ?? 'Indefinida' }}</div>
                            @endif
                            @if($listaPrecio->descripcion)
                            <div class="mb-2"><span class="text-muted">Descripci&oacute;n:</span> {{ $listaPrecio->descripcion }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection