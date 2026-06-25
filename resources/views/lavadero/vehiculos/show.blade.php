@extends('layouts.app')
@section('title', 'Vehículo - ' . $vehiculo->nombre_completo)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-droplet"></i>
                </div>
                <div>
                    <a href="{{ route('lavadero.vehiculos.index') }}" class="text-decoration-none text-white-50 small mb-1 d-inline-block">
                        <i class="bi bi-arrow-left me-1"></i> Volver a vehículos
                    </a>
                    <h2 class="fw-bold mb-0 text-white">{{ $vehiculo->nombre_completo }}</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $vehiculo->cliente?->nombre ?? 'Sin cliente' }}</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#editVehiculoModal">
                <i class="bi bi-pencil me-1"></i> Editar
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-accent green"></div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información del Vehículo</h6>
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Placa</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->placa ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Marca</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->marca ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Modelo</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->modelo ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Año</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->anio ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Color</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->color ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Tipo</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->tipo ?? '—' }}</dd>
                        <dt class="col-5 text-muted">VIN</dt>
                        <dd class="col-7 fw-medium">{{ $vehiculo->vin ?? '—' }}</dd>
                        <dt class="col-5 text-muted">Notas</dt>
                        <dd class="col-7">{{ $vehiculo->notas ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="premium-card">
                <div class="card-accent green"></div>
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Servicios</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light small">
                            <tr><th>Fecha</th><th>Servicios</th><th>Total</th><th>Pagado</th></tr>
                        </thead>
                        <tbody>
                            @forelse($ventas as $v)
                            <tr>
                                <td class="fw-medium small">{{ $v->created_at->format('d/m/Y h:i A') }}</td>
                                <td>
                                    @foreach($v->detalles as $d)
                                    <span class="badge bg-light text-dark me-1">{{ $d->descripcion ?? $d->producto?->nombre ?? '—' }}</span>
                                    @endforeach
                                </td>
                                <td class="fw-bold">RD$ {{ number_format($v->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-success rounded-pill">Pagada</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Sin servicios registrados</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">{{ $ventas->links() }}</div>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="editVehiculoModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.vehiculos.update', $vehiculo) }}" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Vehículo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Placa</label>
                        <input type="text" name="placa" class="form-control rounded-3" value="{{ $vehiculo->placa }}" maxlength="20">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Color</label>
                        <input type="text" name="color" class="form-control rounded-3" value="{{ $vehiculo->color }}" maxlength="50">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Marca</label>
                        <input type="text" name="marca" class="form-control rounded-3" value="{{ $vehiculo->marca }}" maxlength="100">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Año</label>
                        <input type="number" name="anio" class="form-control rounded-3" value="{{ $vehiculo->anio }}" min="1900" max="2099">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Modelo</label>
                        <input type="text" name="modelo" class="form-control rounded-3" value="{{ $vehiculo->modelo }}" maxlength="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2">{{ $vehiculo->notas }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
