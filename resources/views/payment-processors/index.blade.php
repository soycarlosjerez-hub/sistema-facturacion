@extends('layouts.app')
@section('title', 'Procesadores de Pago')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-credit-card text-primary me-2"></i>Procesadores de Pago</h2>
            <p class="text-muted mb-0">Gestiona los procesadores y sus credenciales API</p>
        </div>
        <a href="{{ route('payment-processors.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Procesador
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted text-uppercase small">
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
                            <td><span class="badge bg-light text-dark border rounded-pill">{{ ucfirst($p->tipo) }}</span></td>
                            <td>
                                <small class="text-muted">{{ number_format($p->comision_porcentaje, 2) }}% + RD$ {{ number_format($p->comision_fija, 2) }}</small>
                            </td>
                            <td>
                                @if($p->api_environment === 'production')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Producción</span>
                                @elseif($p->api_environment === 'sandbox')
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Sandbox</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->activo)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Activo</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('payment-processors.edit', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('payment-processors.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este procesador?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-credit-card fs-1"></i><p class="mt-2 mb-0">No hay procesadores registrados</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($procesadores->hasPages())<div class="mt-3">{{ $procesadores->links() }}</div>@endif
</div>
@endsection