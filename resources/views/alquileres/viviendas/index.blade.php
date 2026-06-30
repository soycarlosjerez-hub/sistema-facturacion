@extends('layouts.app')
@section('title', 'Viviendas')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #6366f1;
    --dt-accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
    --dt-accent-rgb: 99,102,241;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle"><i class="bi bi-house-door"></i></div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Viviendas</h4>
                    <small class="text-white opacity-75">{{ $viviendas->count() }} viviendas registradas</small>
                </div>
            </div>
            <div>
                <a href="{{ route('alquileres.viviendas.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Vivienda
                </a>
            </div>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent purple"></div>
        <div class="card-body p-0">
            <table id="viviendas-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Habitaciones</th>
                        <th>Alquiler</th>
                        <th>Estado</th>
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
    const data = @json($viviendas);
    const csrfToken = '{{ csrf_token() }}';

    const table = $('#viviendas-table').DataTable({
        data: data,
        columns: [
            { data: null, className: 'text-center ps-4', orderable: false, searchable: false, width: '50px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>';
                }
            },
            { data: null, orderable: true, searchable: true,
                render: function(data) {
                    return '<div class="fw-bold">' + escapeHtml(data.nombre) + '</div>';
                }
            },
            { data: 'tipo', className: 'text-center',
                render: function(data) {
                    const tipos = { apartamento:'Apartamento', casa:'Casa', local:'Local', habitacion:'Habitación', oficina:'Oficina', otro:'Otro' };
                    return '<span class="badge rounded-pill bg-light text-dark px-3">' + (tipos[data] || data) + '</span>';
                }
            },
            { data: 'habitaciones', className: 'text-center',
                render: function(data) { return '<span class="fw-semibold">' + (data || 0) + '</span>'; }
            },
            { data: 'monto_alquiler', className: 'text-end',
                render: function(data) { return renderMoneda(data); }
            },
            { data: 'estado', className: 'text-center',
                render: function(data) {
                    const estados = { disponible:'Disponible', alquilado:'Alquilado', mantenimiento:'Mantenimiento', inactivo:'Inactivo' };
                    const colors = { disponible:'#10b981', alquilado:'#3b82f6', mantenimiento:'#f59e0b', inactivo:'#94a3b8' };
                    return '<span class="badge rounded-pill px-3" style="background:rgba(' + hexToRgb(colors[data] || '#94a3b8') + ',.1);color:' + (colors[data] || '#94a3b8') + ';font-weight:600;">' + (estados[data] || data) + '</span>';
                }
            },
            { data: null, className: 'text-end pe-4', orderable: false, searchable: false,
                render: function(data) {
                    return renderAcciones(data.id, {
                        edit: '/alquileres/viviendas/' + data.id + '/editar',
                        delete: '/alquileres/viviendas/' + data.id,
                        csrf: csrfToken,
                        nombre: data.nombre
                    });
                }
            }
        ],
        language: {
            search: '', lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ viviendas',
            infoEmpty: 'No hay viviendas',
            infoFiltered: '(de _MAX_ totales)',
            paginate: { first: '<i class="bi bi-chevron-double-left"></i>', last: '<i class="bi bi-chevron-double-right"></i>', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-house d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron viviendas</p><p class="text-muted small mb-0">Crea una nueva vivienda para empezar.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: { details: { type: 'column', target: 'tr', renderer: function(api, rowIdx, columns) { let data = ''; columns.forEach(function(col) { if (col.hidden) { data += '<li><span class="child-label">' + col.title + '</span><span class="child-value">' + col.data + '</span></li>'; } }); return data ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + data + '</ul>') : false; } } },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-12"tr>><"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function renderMoneda(valor) {
        const num = parseFloat(valor || 0);
        return '<div class="fw-bold" style="color:var(--dt-accent, #6366f1);">RD$ ' + num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</div>';
    }

    function renderAcciones(id, opts) {
        let html = '<div class="d-flex justify-content-end gap-1">';
        if (opts.edit) {
            html += '<a href="' + opts.edit + '" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if (opts.delete) {
            html += '<form action="' + opts.delete + '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar ' + escapeHtml(opts.nombre || 'esta vivienda') + '? Esta acción no se puede deshacer.\');">' +
                '<input type="hidden" name="_token" value="' + opts.csrf + '">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="premium-btn-delete border-0" title="Eliminar"><i class="bi bi-trash"></i></button></form>';
        }
        html += '</div>';
        return html;
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function hexToRgb(hex) {
        const r = parseInt(hex.slice(1,3), 16), g = parseInt(hex.slice(3,5), 16), b = parseInt(hex.slice(5,7), 16);
        return r + ',' + g + ',' + b;
    }
});
</script>
@endpush
