@extends('layouts.app')
@section('title', 'Inquilinos')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #10b981;
    --dt-accent-gradient: linear-gradient(135deg, #10b981, #06b6d4);
    --dt-accent-rgb: 16,185,129;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #10b981, #06b6d4);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle"><i class="bi bi-people"></i></div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Inquilinos</h4>
                    <small class="text-white opacity-75">{{ $inquilinos->count() }} inquilinos registrados</small>
                </div>
            </div>
            <div>
                <a href="{{ route('alquileres.inquilinos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Inquilino
                </a>
            </div>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <table id="inquilinos-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Contratos</th>
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
    const data = @json($inquilinos);
    const csrfToken = '{{ csrf_token() }}';

    $('#inquilinos-table').DataTable({
        data: data,
        columns: [
            { data: null, className: 'text-center ps-4', orderable: false, searchable: false, width: '50px',
                render: function(data, type, row, meta) { return '<span class="text-muted fw-bold">' + (meta.row + 1) + '</span>'; }
            },
            { data: null, orderable: true, searchable: true,
                render: function(data) { return '<div class="fw-bold">' + escapeHtml(data.nombre) + '</div>'; }
            },
            { data: 'cedula', render: function(d) { return d || '<span class="text-muted small">—</span>'; } },
            { data: 'telefono', render: function(d) { return d || '<span class="text-muted small">—</span>'; } },
            { data: 'email', render: function(d) { return d || '<span class="text-muted small">—</span>'; } },
            { data: 'contratos_count', className: 'text-center',
                render: function(d) { return '<span class="badge rounded-pill bg-light text-dark px-3">' + (d || 0) + '</span>'; }
            },
            { data: null, className: 'text-end pe-4', orderable: false, searchable: false,
                render: function(data) {
                    return '<div class="d-flex justify-content-end gap-1">' +
                        '<a href="/alquileres/inquilinos/' + data.id + '/editar" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                        '<form action="/alquileres/inquilinos/' + data.id + '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar ' + escapeHtml(data.nombre) + '? Esta acción no se puede deshacer.\');">' +
                        '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<button type="submit" class="premium-btn-delete border-0" title="Eliminar"><i class="bi bi-trash"></i></button></form></div>';
                }
            }
        ],
        language: {
            search: '', lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ inquilinos',
            infoEmpty: 'No hay inquilinos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: { first: '<i class="bi bi-chevron-double-left"></i>', last: '<i class="bi bi-chevron-double-right"></i>', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-people d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron inquilinos</p><p class="text-muted small mb-0">Registra un nuevo inquilino para empezar.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: { details: { type: 'column', target: 'tr', renderer: function(api, rowIdx, columns) { let data = ''; columns.forEach(function(col) { if (col.hidden) { data += '<li><span class="child-label">' + col.title + '</span><span class="child-value">' + col.data + '</span></li>'; } }); return data ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + data + '</ul>') : false; } } },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-12"tr>><"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush
