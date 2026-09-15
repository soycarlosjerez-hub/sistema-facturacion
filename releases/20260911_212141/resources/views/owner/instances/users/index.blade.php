@extends('layouts.app')

@section('title', 'Usuarios - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    .dt-table thead th {
        background: rgba(241,245,249,.8);
        color: #64748b;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: .75rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .dt-table tbody td {
        padding: .75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .85rem;
    }
    .dt-table tbody tr:last-child td { border-bottom: none; }
    .dt-table tbody tr { transition: background .15s; }
    .dt-table tbody tr:hover { background: rgba(245,158,11,.03); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Usuarios</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        {{ $instance->nombre }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.users.create', $instance) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
                </a>
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="users-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instance->users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill me-1">{{ $role->name }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="text-muted small">Sin rol</span>
                            @endif
                        </td>
                        <td>
                            @if($user->activo ?? true)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('owner.instances.users.edit', [$instance, $user]) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('owner.instances.users.destroy', [$instance, $user]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar a {{ $user->name }} de {{ $instance->nombre }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('users-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron usuarios',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            order: [[0, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
@endpush
@endsection
