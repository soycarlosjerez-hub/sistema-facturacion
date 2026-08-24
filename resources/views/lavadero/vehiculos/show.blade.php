@extends('layouts.app')
@section('title', 'Vehículo - ' . $vehiculo->nombre_completo)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $vehiculo->nombre_completo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        <span>{{ $vehiculo->cliente?->nombre ?? 'Sin cliente' }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('lavadero.vehiculos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editVehiculoModal">
                    <i class="bi bi-pencil me-1"></i> Editar
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Columna izquierda: resumen --}}
        <div class="col-lg-4">
            <div class="ui-card mb-4" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="ui-user-avatar ui-user-avatar-blue mx-auto mb-3" style="width:96px;height:96px;font-size:2.5rem;">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $vehiculo->nombre_completo }}</h5>
                    <p class="text-muted mb-2">{{ $vehiculo->cliente?->nombre ?? 'Sin cliente' }}</p>
                    <span class="ui-badge ui-badge-primary"><i class="bi bi-patch-check me-1"></i>{{ $vehiculo->tipo ?? 'Vehículo' }}</span>
                </div>
            </div>

            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title"><i class="bi bi-bar-chart me-2"></i>Resumen</div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                <small class="text-muted text-uppercase d-block fw-semibold">Placa</small>
                                <span class="fs-5 fw-bold" style="color:#06b6d4;">{{ $vehiculo->placa ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                <small class="text-muted text-uppercase d-block fw-semibold">Visitas</small>
                                <span class="fs-5 fw-bold" style="color:#06b6d4;">{{ $vehiculo->ventas_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: información + historial --}}
        <div class="col-lg-8">
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title"><i class="bi bi-info-circle me-2"></i>Información del Vehículo</div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Placa</span>
                        <span class="ui-detail-value">{{ $vehiculo->placa ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Marca</span>
                        <span class="ui-detail-value">{{ $vehiculo->marca ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Modelo</span>
                        <span class="ui-detail-value">{{ $vehiculo->modelo ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Año</span>
                        <span class="ui-detail-value">{{ $vehiculo->anio ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Color</span>
                        <span class="ui-detail-value">{{ $vehiculo->color ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Tipo</span>
                        <span class="ui-detail-value">{{ $vehiculo->tipo ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">VIN</span>
                        <span class="ui-detail-value">{{ $vehiculo->vin ?? '—' }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Notas</span>
                        <span class="ui-detail-value">{{ $vehiculo->notas ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-0">
                    <div class="ui-card-title"><i class="bi bi-clock-history me-2"></i>Historial de Servicios</div>
                    <div class="table-responsive">
                        <table class="ui-table mb-0">
                            <thead>
                                <tr><th>Fecha</th><th>Servicios</th><th>Total</th><th>Pagado</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventas as $v)
                                <tr>
                                    <td class="fw-medium small">{{ $v->created_at->format('d/m/Y h:i A') }}</td>
                                    <td>
                                        @foreach($v->detalles as $d)
                                        <span class="ui-badge ui-badge-neutral me-1">{{ $d->descripcion ?? $d->producto?->nombre ?? '—' }}</span>
                                        @endforeach
                                    </td>
                                    <td class="fw-bold" style="color:#06b6d4;">RD$ {{ number_format($v->total, 2) }}</td>
                                    <td>
                                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Pagada</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="ui-empty-state">
                                            <i class="bi bi-clock-history"></i>
                                            <p>Sin servicios registrados</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                        <label class="ui-label">Placa</label>
                        <input type="text" name="placa" class="ui-input" value="{{ $vehiculo->placa }}" maxlength="20">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Color</label>
                        <input type="text" name="color" class="ui-input" value="{{ $vehiculo->color }}" maxlength="50">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Marca</label>
                        <input type="text" name="marca" class="ui-input" value="{{ $vehiculo->marca }}" maxlength="100">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Año</label>
                        <input type="number" name="anio" class="ui-input" value="{{ $vehiculo->anio }}" min="1900" max="2099">
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Modelo</label>
                        <input type="text" name="modelo" class="ui-input" value="{{ $vehiculo->modelo }}" maxlength="100">
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-textarea" rows="2">{{ $vehiculo->notas }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
