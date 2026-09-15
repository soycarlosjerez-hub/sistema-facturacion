@extends('layouts.app')
@section('title', $impresora->nombre . ' - Impresora')

@push('styles')
@include('partials.premium-ui')
<style>
.conn-badge {
    padding: 0.25em 0.65em;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.conn-local { background: rgba(100,116,139,0.12); color: #475569; }
.conn-usb { background: rgba(99,102,241,0.12); color: #6366f1; }
.conn-red { background: rgba(6,182,212,0.12); color: #0891b2; }
.conn-pdf { background: rgba(239,68,68,0.12); color: #dc2626; }

.detail-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--dt-gray-500);
    font-weight: 700;
    margin-bottom: 4px;
}
.detail-value {
    font-size: 1rem;
    font-weight: 500;
}
.toggle-card {
    padding: 12px 16px;
    border-radius: 0.75rem;
    border: 2px solid #e2e8f0;
    background: white;
    transition: all 0.15s;
}
body.dark-mode .toggle-card {
    border-color: #334155;
    background: rgba(15,23,42,0.4);
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $impresora->nombre }}</h4>
                    <div class="ui-header-meta">Detalle de la impresora</div>
                </div>
            </div>
            <div class="ui-header-actions d-flex gap-2">
                <a href="{{ route('impresoras.edit', $impresora) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card mb-4" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0 ui-card-title">
                            <i class="bi bi-info-circle me-2"></i>Informacion General
                        </h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value">{{ $impresora->nombre }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Tipo</div>
                            <div class="detail-value">{{ $impresora->tipo ?? 'general' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Sucursal</div>
                            <div class="detail-value">{{ $impresora->sucursal ? $impresora->sucursal->nombre : '<span class="text-muted">Sin sucursal (global)</span>' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Orden</div>
                            <div class="detail-value">{{ $impresora->orden }}</div>
                        </div>
                        @if($impresora->descripcion)
                        <div class="col-md-12">
                            <div class="detail-label">Descripcion</div>
                            <div class="detail-value">{{ $impresora->descripcion }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0 ui-card-title">
                            <i class="bi bi-cpu me-2"></i>Conexion e Hardware
                        </h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="detail-label">Tipo de Conexion</div>
                            <div class="detail-value">
                                <span class="conn-badge conn-{{ $impresora->tipo_conexion }}">
                                    {{ ['local'=>'Local','usb'=>'USB','red'=>'Red','pdf'=>'PDF'][$impresora->tipo_conexion] ?? ucfirst($impresora->tipo_conexion) }}
                                </span>
                            </div>
                        </div>
                        @if($impresora->direccion_ip)
                        <div class="col-md-4">
                            <div class="detail-label">Direccion IP</div>
                            <div class="detail-value">{{ $impresora->direccion_ip }}</div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="detail-label">Puerto</div>
                            <div class="detail-value">{{ $impresora->puerto }}</div>
                        </div>
                        @if($impresora->ruta_compartida)
                        <div class="col-md-6">
                            <div class="detail-label">Ruta Compartida</div>
                            <div class="detail-value">{{ $impresora->ruta_compartida }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="detail-label">Driver</div>
                            <div class="detail-value">{{ $impresora->driver ?? 'escpos' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Tamano de Papel</div>
                            <div class="detail-value">
                                <span class="badge bg-light text-secondary border">{{ $impresora->papel_tamano }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Caracteres por Linea</div>
                            <div class="detail-value">{{ $impresora->caracteres_por_linea }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-4" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0 ui-card-title">
                            <i class="bi bi-gear me-2"></i>Configuracion
                        </h6>
                    </div>

                    <div class="mb-3">
                        <div class="detail-label">Estado</div>
                        <div class="detail-value">
                            <span class="badge rounded-pill {{ $impresora->activo ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                <i class="bi {{ $impresora->activo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                {{ $impresora->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-label mt-3 mb-2">Impresion Automatica</div>
                    <div class="toggle-card mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cart-check text-success me-2"></i>Ventas</span>
                        <span class="badge {{ $impresora->auto_imprimir_ventas ? 'bg-success' : 'bg-secondary' }}">
                            {{ $impresora->auto_imprimir_ventas ? 'SI' : 'NO' }}
                        </span>
                    </div>
                    <div class="toggle-card mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-text text-info me-2"></i>Cotizaciones</span>
                        <span class="badge {{ $impresora->auto_imprimir_cotizaciones ? 'bg-success' : 'bg-secondary' }}">
                            {{ $impresora->auto_imprimir_cotizaciones ? 'SI' : 'NO' }}
                        </span>
                    </div>
                    <div class="toggle-card d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-truck text-warning me-2"></i>Conduces</span>
                        <span class="badge {{ $impresora->auto_imprimir_conduces ? 'bg-success' : 'bg-secondary' }}">
                            {{ $impresora->auto_imprimir_conduces ? 'SI' : 'NO' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Acciones</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('ventas.ticket', 1) }}" target="_blank" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-printer me-2"></i>Probar Impresion
                        </a>
                        <a href="{{ route('impresoras.edit', $impresora) }}" class="ui-btn ui-btn-ghost rounded-pill">
                            <i class="bi bi-pencil me-2"></i>Editar Impresora
                        </a>
                        @can('impresoras.delete')
                        <button type="button" class="ui-btn ui-btn-outline-danger rounded-pill btn-delete" data-id="{{ $impresora->id }}" data-nombre="{{ $impresora->nombre }}">
                            <i class="bi bi-trash me-2"></i>Eliminar
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const csrfToken = '{{ csrf_token() }}';
    const API_BASE = '/impresoras';

    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        const row = $(this).closest('.ui-card');

        Swal.fire({
            title: '¿Eliminar impresora?',
            text: 'Se eliminará: "' + nombre + '". Las ventas impresas mantendran su registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(API_BASE + '/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        window.location.href = '{{ route("impresoras.index") }}';
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
@endsection
