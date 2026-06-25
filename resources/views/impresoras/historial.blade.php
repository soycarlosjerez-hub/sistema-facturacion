@extends('layouts.app')

@section('title', 'Historial de Impresión')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .premium-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Historial de Impresión</h3>
                    <p class="mb-0 opacity-75">Registro de todos los documentos impresos</p>
                </div>
            </div>
            <a href="{{ route('impresoras.index') }}" class="btn btn-light rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Impresoras
            </a>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Fecha</th>
                        <th>Documento</th>
                        <th>Tipo</th>
                        <th>Impresora</th>
                        <th>Usuario</th>
                        <th class="text-center">Copias</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Tamaño</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $h)
                    <tr>
                        <td class="ps-4 small">{{ $h->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="fw-semibold small">
                            {{ $h->documento_numero ?? 'N/A' }}
                            @if($h->imprimible_type)
                            <br><small class="text-muted">#{{ $h->imprimible_id }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill">{{ ucfirst($h->tipo_documento) }}</span></td>
                        <td class="small">{{ $h->impresora?->nombre ?? '<sin impresora>' }}</td>
                        <td class="small">{{ $h->usuario?->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $h->copias }}</td>
                        <td class="text-center">
                            @if($h->exitoso)
                                <span class="badge rounded-pill bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i>Éxito
                                </span>
                            @else
                                <span class="badge rounded-pill bg-danger-subtle text-danger" title="{{ $h->error_mensaje }}">
                                    <i class="bi bi-x-circle me-1"></i>Falló
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4 small">{{ $h->tamanio_humano }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-printer fs-1 d-block mb-2"></i>
                        Sin historial de impresión
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $historial->links() }}
    </div>
</div>
@endsection