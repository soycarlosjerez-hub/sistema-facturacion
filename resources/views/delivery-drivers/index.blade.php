@extends('layouts.app')

@section('title', 'Repartidores')

@push('styles')
@include('partials.premium-ui')
<style>
/* Delivery Drivers Module Styles */
.driver-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem;
    background: rgba(14,165,233,.1); color: #0ea5e9;
    flex-shrink: 0;
}
.drivers-active-count {
    background: rgba(34,197,94,.12); color: #16a34a;
    padding: .2rem .6rem; border-radius: 9999px; font-size: .75rem; font-weight: 600;
}
@media (max-width: 767.98px) {
    .drivers-stats .ui-stat { margin-bottom: .75rem; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-people me-1"></i>
                        <span>{{ $drivers->total() }} repartidor(es)</span>
                        @if(isset($activeCount))
                        <span class="divider">·</span>
                        <span class="drivers-active-count"><i class="bi bi-check-circle me-1"></i>{{ $activeCount }} activos</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('delivery-drivers.create')
                <a href="{{ route('delivery-drivers.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Repartidor
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Stats Row --}}
    @if(isset($stats))
    <div class="row g-3 mb-4 drivers-stats">
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Repartidores</div>
                    <div class="ui-stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activos</div>
                    <div class="ui-stat-value" style="color:#16a34a;">{{ $stats['activos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Inactivos</div>
                    <div class="ui-stat-value" style="color:#64748b;">{{ $stats['inactivos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Órdenes en Curso</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;">{{ $stats['ordenes_en_curso'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Repartidor</th>
                        <th>Cédula</th>
                        <th>Contacto</th>
                        <th>Licencia</th>
                        <th class="text-center">Órdenes</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="driver-avatar">
                                    {{ strtoupper(substr($driver->nombre, 0, 1) . substr($driver->apellido, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $driver->nombre }} {{ $driver->apellido }}</div>
                                    <small class="text-muted" style="font-size:.75rem;">
                                        <i class="bi bi-clock me-1"></i>Registrado {{ $driver->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td><span class="ui-badge ui-badge-neutral">{{ $driver->cedula }}</span></td>
                        <td>
                            <div class="small">
                                <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $driver->telefono }}</div>
                                @if($driver->whatsapp)
                                <div>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $driver->whatsapp) }}" target="_blank" class="text-decoration-none" title="Enviar WhatsApp">
                                        <i class="bi bi-whatsapp me-1" style="color:#25D366;"></i>{{ $driver->whatsapp }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td><span class="ui-badge ui-badge-neutral">{{ $driver->licencia_conducir ?? '—' }}</span></td>
                        <td class="text-center">
                            @if($driver->ordenes_activas > 0)
                                <span class="ui-badge ui-badge-info">{{ $driver->ordenes_activas }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td>
                            @if($driver->activo)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                            @else
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                @can('delivery-drivers.edit')
                                <a href="{{ route('delivery-drivers.edit', $driver) }}" class="ui-action ui-action-edit" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delivery-drivers.delete')
                                <form action="{{ route('delivery-drivers.destroy', $driver) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar este repartidor?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            No hay repartidores registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($drivers->hasPages())
        <div class="card-footer bg-transparent border-0 p-3">
            {{ $drivers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
