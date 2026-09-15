@extends('layouts.app')
@section('title', 'Técnicos')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #6366f1;
    --dt-accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
    --dt-accent-rgb: 99,102,241;
}
.estado-badge {
    padding: 0.35em 0.8em;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.72rem;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all .2s;
}
.estado-badge:hover { filter: brightness(1.1); transform: scale(1.03); }
@media (max-width: 767.98px) {
    .dt-table { min-width: 700px; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Técnicos</h4>
                    <div class="ui-header-meta">Administra el personal técnico, especialidades y tarifas</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnicos.create')
                <a href="{{ route('tecnicos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Técnico
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Técnicos</div>
                    <div class="ui-stat-value">{{ $tecnicos->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activos</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $tecnicos->where('activo', true)->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Inactivos</div>
                    <div class="ui-stat-value" style="color:#64748b;">
                        {{ $tecnicos->where('activo', false)->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Tarifa Hora Prom.</div>
                    <div class="ui-stat-value" style="color:#8b5cf6;">
                        RD$ {{ number_format($tecnicos->avg('tarifa_hora') ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tecnicos.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Nombre, cédula, teléfono, email..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Especialidad</label>
                    <select name="especialidad" class="ui-select">
                        <option value="">Todas</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->nombre }}" {{ request('especialidad') === $esp->nombre ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('tecnicos.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <table id="tecnicos-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:45px;">#</th>
                        <th>Técnico</th>
                        <th>Especialidad</th>
                        <th>Tarifa Hora</th>
                        <th>Tarifa Fija</th>
                        <th class="text-center">Órdenes</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const table = $('#tecnicos-table').DataTable({
        serverSide: true,
        ajax: {
            url: '{{ route('tecnicos.index') }}',
            type: 'GET',
            data: function(d) {
                d.search = $('input[name="search"]').val();
                d.especialidad = $('select[name="especialidad"]').val();
                d.activo = $('select[name="activo"]').val();
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: false,
                searchable: false,
                width: '45px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + 1) + '</span>';
                }
            },
            {
                data: 'nombre',
                render: function(data, type, row) {
                    if (!data) return '-';
                    return '<div class="d-flex align-items-center">' +
                        '<div class="avatar-circle text-white me-3 shadow-sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;"><i class="bi bi-person"></i></div>' +
                        '<div><div class="fw-bold text-dark fs-6">' + escapeHtml(data) + '</div>' +
                        '<small class="text-muted">' + escapeHtml(row.cedula || '') + '</small></div></div>';
                }
            },
            {
                data: 'especialidad',
                render: function(data) {
                    if (!data) return '-';
                    return '<span class="fw-semibold">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'tarifa_hora',
                render: function(data) {
                    if (!data || data === '0.00') return '-';
                    return '<span class="fw-bold text-primary">RD$ ' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'tarifa_fija',
                render: function(data) {
                    if (!data || data === '0.00') return '-';
                    return '<span class="fw-bold text-success">RD$ ' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'ordenes_count',
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge bg-light text-secondary border rounded-pill">' + (data || 0) + '</span>';
                }
            },
            {
                data: 'activo',
                className: 'text-center',
                render: function(data) {
                    if (data) {
                        return '<span class="estado-badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i> Activo</span>';
                    }
                    return '<span class="estado-badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-pause-circle-fill"></i> Inactivo</span>';
                }
            },
            {
                data: 'acciones',
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return data || '';
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ técnicos',
            infoEmpty: 'No hay técnicos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-person-gear d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron técnicos</p>' +
                '<p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: {
            details: {
                type: 'column',
                target: 'tr',
                renderer: function(api, rowIdx, columns) {
                    var data = '';
                    columns.forEach(function(col) {
                        if (col.hidden) {
                            data += '<li>' +
                                '<span class="child-label">' + col.title + '</span>' +
                                '<span class="child-value">' + col.data + '</span>' +
                            '</li>';
                        }
                    });
                    return data ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + data + '</ul>') : false;
                }
            }
        },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('input[name="search"], select').on('change input', function() {
        clearTimeout(window._tecnicosTimeout);
        window._tecnicosTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 400);
    });

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush