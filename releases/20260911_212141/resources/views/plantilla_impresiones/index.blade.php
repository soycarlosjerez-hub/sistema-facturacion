@extends('layouts.app')
@section('title', 'Plantillas de Impresion')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --dt-accent: #f59e0b;
    --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
    --dt-accent-rgb: 245,158,11;
    --dt-success: #22c55e;
    --dt-gray-100: #f1f5f9;
    --dt-gray-200: #e2e8f0;
    --dt-gray-300: #cbd5e1;
    --dt-gray-400: #94a3b8;
    --dt-gray-500: #64748b;
    --dt-gray-600: #475569;
    --dt-gray-700: #334155;
    --dt-radius: 0.5rem;
    --dt-transition: 0.15s;
}
.plantillas-table {
    width: 100%;
    margin: 0;
    table-layout: auto;
}
.plantillas-table thead th {
    background: rgba(241,245,249,.8);
    color: var(--dt-gray-500);
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 2px solid var(--dt-gray-200);
}
.plantillas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--dt-gray-100);
    font-size: .9rem;
}
.plantillas-table tbody tr:hover { background: rgba(245,158,11,.03); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    cursor: pointer;
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
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Plantillas de Impresion</h4>
                    <div class="ui-header-meta">Configura los formatos de impresion para tickets y comprobantes</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver a Impresoras
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <div class="mb-3 pb-3 border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-list-check me-2" style="color:#f59e0b;"></i>Plantillas Disponibles
                </h6>
                <small class="text-muted">Las plantillas predefinidas se crean automaticamente al registrar una impresora.</small>
            </div>

            @if($plantillas->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted fs-1"></i>
                    <p class="text-muted mt-2 mb-0">No hay plantillas registradas. Registra una impresora para crear plantillas automaticamente.</p>
                    <a href="{{ route('impresoras.create') }}" class="ui-btn ui-btn-primary rounded-pill mt-3">
                        <i class="bi bi-plus me-1"></i>Nueva Impresora
                    </a>
                </div>
            @else
                <table class="table plantillas-table no-footer">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="ps-3">Código</th>
                            <th>Nombre</th>
                            <th style="width:120px;">Modulo</th>
                            <th style="width:100px;">Formato</th>
                            <th style="width:90px;" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plantillas as $plantilla)
                        <tr>
                            <td class="ps-3">
                                <code style="font-size:0.8rem;">{{ $plantilla->codigo }}</code>
                            </td>
                            <td>
                                <strong>{{ $plantilla->nombre }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $plantilla->modulo }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $plantilla->tipo_formato }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge badge rounded-pill {{ $plantilla->activo ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                    <i class="bi {{ $plantilla->activo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const csrfToken = '{{ csrf_token() }}';
    const API_BASE = '/plantilla-impresiones';

    $('.status-badge').on('click', function() {
        const badge = $(this);
        const isActive = badge.find('i.check-circle-fill').length > 0;

        Swal.fire({
            title: isActive ? '¿Desactivar plantilla?' : '¿Activar plantilla?',
            text: isActive ? 'La plantilla quedará inactiva.' : 'La plantilla volverá a estar activa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#dc2626' : '#22c55e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí'
        }).then(function(result) {
            if (result.isConfirmed) {
                const id = badge.closest('tr').find('code').first().text().replace('-', '');
                fetch(API_BASE + '/' + id + '/toggle', {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        badge.removeClass('bg-success bg-opacity-10 text-success bg-secondary bg-opacity-10 text-secondary');
                        if (res.activo) {
                            badge.closest('td').html('<span class="status-badge badge rounded-pill bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>');
                        } else {
                            badge.closest('td').html('<span class="status-badge badge rounded-pill bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-x-circle-fill me-1"></i>Inactiva</span>');
                        }
                        Swal.fire({ icon: 'success', title: res.activo ? 'Plantilla activada' : 'Plantilla desactivada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
