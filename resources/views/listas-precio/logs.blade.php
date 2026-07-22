@extends('layouts.app')
@section('title', 'Historial de Cambios — ' . $listaPrecio->nombre)

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
            radial-gradient(circle at 30% 44%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .log-timeline {
        position: relative;
        padding-left: 2rem;
    }
    .log-timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #8b5cf6, rgba(139,92,246,0.1));
    }
    .log-entry {
        position: relative;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(139,92,246,0.1);
        border-radius: 0.75rem;
        transition: all 0.2s ease;
    }
    .log-entry:hover {
        border-color: rgba(139,92,246,0.3);
        box-shadow: 0 4px 12px rgba(139,92,246,0.08);
    }
    .log-entry::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 1.35rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #8b5cf6;
        border: 2px solid white;
        box-shadow: 0 0 0 2px rgba(139,92,246,0.2);
    }
    .change-badge {
        font-size: 0.7rem;
        padding: 0.3em 0.7em;
        border-radius: 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .change-price { background: rgba(25,135,84,0.1); color: #198754; }
    .change-vigencia { background: rgba(13,202,240,0.1); color: #0dcaf0; }
    .change-activo { background: rgba(108,117,125,0.1); color: #6c757d; }
    .change-codigo { background: rgba(255,193,7,0.1); color: #b8860b; }
    .change-nombre { background: rgba(111,66,193,0.1); color: #6f42c1; }
    .price-change {
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 0.85rem;
    }
    .arrow-icon { color: #8b5cf6; margin: 0 0.5rem; }
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
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0 text-white">Historial de Cambios</h2>
                        <p class="text-white text-opacity-75 mb-0">{{ $listaPrecio->nombre }}</p>
                    </div>
                </div>
                <a href="{{ route('listas-precio.show', $listaPrecio) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        @if($logs->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-clock text-muted opacity-25" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Sin registros de cambios</h5>
                                <p class="text-muted small">Los cambios de precio e información aparecerán aquí.</p>
                            </div>
                        @else
                            <div class="log-timeline">
                                @foreach($logs as $log)
                                <div class="log-entry">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="change-badge change-{{ str_replace(',', '-', $log->cambio_en ?? 'precio') }}">
                                                {{ str_replace(',', ', ', $log->cambio_en) }}
                                            </span>
                                            @if($log->producto)
                                            <span class="small fw-semibold text-dark">
                                                <i class="bi bi-box-seam me-1"></i>{{ $log->producto->nombre }}
                                            </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>

                                    @if($log->precio_anterior !== null && $log->precio_nuevo !== null)
                                    <div class="price-change mt-2">
                                        <span class="text-muted text-decoration-line-through">
                                            RD$ {{ number_format($log->precio_anterior, 2) }}
                                        </span>
                                        <i class="bi bi-arrow-right arrow-icon"></i>
                                        <span class="text-success fw-bold">
                                            RD$ {{ number_format($log->precio_nuevo, 2) }}
                                        </span>
                                    </div>
                                    @elseif($log->precio_nuevo === null)
                                    <div class="small text-danger mt-2">
                                        <i class="bi bi-trash me-1"></i>Producto removido de la lista
                                    </div>
                                    @endif

                                    @if($log->observacion)
                                    <div class="small text-muted mt-2">
                                        <i class="bi bi-chat-dots me-1"></i>{{ $log->observacion }}
                                    </div>
                                    @endif

                                    @if($log->usuario)
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-person me-1"></i>Por: {{ $log->usuario->name ?? 'N/A' }}
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
