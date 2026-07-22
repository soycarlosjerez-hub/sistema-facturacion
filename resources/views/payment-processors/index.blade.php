@extends('layouts.app')

@section('title', 'Procesadores de Pago')

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
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Procesadores de Pago</h4>
                    <div class="ui-header-meta">Gestiona los procesadores y sus credenciales API</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('payment-processors.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Procesador
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden" style="--delay:.1s">
        <div class="ui-card-accent blue"></div>
        <div class="table-responsive">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Tipo</th>
                        <th>Comisión</th>
                        <th>Entorno</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procesadores as $p)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold">{{ $p->nombre }}</span>
                                @if($p->api_key)
                                    <i class="bi bi-key text-muted ms-1 small" title="API configurada"></i>
                                @endif
                            </td>
                            <td><span class="ui-badge ui-badge-neutral">{{ ucfirst($p->tipo) }}</span></td>
                            <td>
                                <small class="text-muted">{{ number_format($p->comision_porcentaje, 2) }}% + RD$ {{ number_format($p->comision_fija, 2) }}</small>
                            </td>
                            <td>
                                @if($p->api_environment === 'production')
                                    <span class="ui-badge ui-badge-danger">Producción</span>
                                @elseif($p->api_environment === 'sandbox')
                                    <span class="ui-badge ui-badge-success">Sandbox</span>
                                @else
                                    <span class="ui-badge ui-badge-neutral">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->activo)
                                    <span class="ui-badge ui-badge-success">Activo</span>
                                @else
                                    <span class="ui-badge ui-badge-neutral">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('payment-processors.edit', $p) }}" class="ui-action ui-action-edit me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('payment-processors.destroy', $p) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="ui-action ui-action-delete" title="Eliminar" onclick="event.preventDefault();UI.confirm.delete('{{ route('payment-processors.destroy', $p) }}', '{{ addslashes($p->nombre) }}')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="ui-empty-state">
                                    <i class="bi bi-credit-card"></i>
                                    <p>No hay procesadores registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($procesadores->hasPages())
        <div class="border-0 py-3 px-4" style="background:transparent;">
            {{ $procesadores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection