@extends('layouts.app')

@section('title', 'Historial de Impresión')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Historial de Impresión</h2>
            <p class="text-muted mb-0">Registro de todos los documentos impresos</p>
        </div>
        <a href="{{ route('impresoras.index') }}" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Impresoras
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
