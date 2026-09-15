@extends('layouts.app')
@section('title', 'Detalle de Transferencia')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <h4 class="ui-header-title">Transferencia {{ $stockTransfer->codigo }}</h4>
                    <div class="ui-header-meta">
                        <span class="badge bg-{{ $stockTransfer->estado_badge_color }} me-2">{{ $stockTransfer->estado_label }}</span>
                        Creada el {{ $stockTransfer->created_at->format('d/m/Y H:i') }} por {{ $stockTransfer->user?->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-transfers.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #10b981 !important;">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Productos -->
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3">Productos a Transferir</h6>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-0">#</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th class="text-center">Recibido</th>
                                    <th class="text-end pe-0">% Recibido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockTransfer->detalle as $i => $d)
                                <tr>
                                    <td class="ps-0 font-monospace">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold small">{{ $d->producto?->nombre ?? 'N/A' }}</div>
                                        @if($d->producto?->codigo_barras)
                                        <small class="text-muted font-monospace">{{ $d->producto->codigo_barras }}</small>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $d->cantidad }}</td>
                                    <td class="text-center">{{ $d->recibido }}</td>
                                    <td class="text-end pe-0">
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar {{ $d->recibido >= $d->cantidad ? 'bg-success' : 'bg-warning' }}"
                                                 style="width: {{ $d->cantidad > 0 ? min(100, ($d->recibido / $d->cantidad) * 100) : 0 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3">Detalles</h6>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Desde</div>
                        <div class="fw-semibold">{{ $stockTransfer->sucursal_origen?->nombre ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label mb-1">Hacia</div>
                        <div class="fw-semibold">{{ $stockTransfer->sucursal_destino?->nombre ?? '-' }}</div>
                    </div>
                    @if($stockTransfer->notas)
                    <div class="mb-3">
                        <div class="detail-label mb-1">Notas</div>
                        <div>{{ $stockTransfer->notas }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($stockTransfer->estado === 'borrador' || $stockTransfer->estado === 'aprobada')
            <div class="mt-3">
                <form action="{{ route('stock-transfers.enviar', $stockTransfer) }}" method="POST">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill w-100" onclick="return confirm('¿Enviar transferencia?')">
                        <i class="bi bi-send me-2"></i>Enviar
                    </button>
                </form>
            </div>
            @endif

            @if($stockTransfer->estado === 'enviada' || $stockTransfer->estado === 'aprobada')
            <div class="mt-3">
                <form action="{{ route('stock-transfers.recibir', $stockTransfer) }}" method="POST" id="recibirForm">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-ghost rounded-pill w-100" style="border: 2px solid #10b981; color: #10b981;" onclick="return confirm('¿Marcar como recibida?')">
                        <i class="bi bi-box-seam me-2"></i>Recibir Todo
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
