@extends('layouts.app')

@section('title', 'Centro de Notificaciones')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="mb-1"><i class="bi bi-bell me-2"></i>Centro de Notificaciones</h2>
                    <p class="text-muted mb-0">Gestiona tus notificaciones del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="markAllRead">
                        <i class="bi bi-check2-all me-1"></i>Marcar todas leídas
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="cleanOld">
                        <i class="bi bi-trash me-1"></i>Limpiar antigüas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="all">Todos</option>
                                <option value="unread">No leídas</option>
                                <option value="read">Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="categoryFilter">
                                <option value="">Todas las categorías</option>
                                <option value="sale">Ventas</option>
                                <option value="order">Órdenes</option>
                                <option value="payment">Pagos</option>
                                <option value="inventory">Inventario</option>
                                <option value="cash">Caja</option>
                                <option value="fiscal">Fiscal</option>
                                <option value="system">Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary w-100" id="applyFilters">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="notificationsContainer">
                @forelse($notifications as $notification)
                    <div class="notification-item d-flex align-items-start p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light-subtle' }}" 
                         data-id="{{ $notification->id }}"
                         data-category="{{ $notification->data['category'] ?? 'system' }}"
                         data-read="{{ $notification->read_at ? 'true' : 'false' }}">
                        <div class="flex-shrink-0 me-3">
                            <span class="notification-icon d-inline-flex align-items-center justify-content-center rounded-circle"
                                  style="width: 40px; height: 40px; background-color: {{ $notification->data['color'] ?? '#3b82f6' }}20; color: {{ $notification->data['color'] ?? '#3b82f6' }};">
                                <i class="{{ $notification->data['icon'] ?? 'bi-bell' }}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1 fw-semibold text-truncate">
                                    {{ $notification->data['title'] ?? 'Notificación' }}
                                </h6>
                                <small class="text-muted ms-2 flex-shrink-0">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <p class="mb-1 text-muted small text-break">{{ $notification->data['body'] ?? '' }}</p>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge rounded-pill bg-light text-dark border">
                                    <i class="{{ $notification->data['category_icon'] ?? 'bi-bell' }} me-1"></i>
                                    {{ $notification->data['category_label'] ?? 'Sistema' }}
                                </span>
                                <span class="badge rounded-pill {{ $notification->read_at ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $notification->read_at ? 'Leída' : 'No leída' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            @if(!$notification->read_at)
                                <button class="btn btn-sm btn-link text-decoration-none mark-read-btn" data-id="{{ $notification->id }}" title="Marcar como leída">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            @endif
                            <button class="btn btn-sm btn-link text-danger delete-notification-btn" data-id="{{ $notification->id }}" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash display-1 text-muted"></i>
                        <p class="mt-3 text-muted">No tienes notificaciones aún</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white border-top-0">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/notifications.js') }}"></script>
@endpush
