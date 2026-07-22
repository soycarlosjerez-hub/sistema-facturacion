@extends('layouts.app')
@section('title', 'Contratos de Alquiler')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #f59e0b;
    --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #f97316);
    --dt-accent-rgb: 245,158,11;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Contratos</h4>
                    <small class="text-white opacity-75">{{ $contratos->count() }} contratos registrados</small>
                </div>
            </div>
            <div>
                <a href="{{ route('alquileres.contratos.create') }}" class="ui-btn ui-btn-primary rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Contrato
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s;">
        <div class="ui-card-accent amber"></div>
        <div class="card-body p-0">
            <table id="contratos-table" class="ui-table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Vivienda</th>
                        <th>Inquilino</th>
                        <th>Inicio</th>
                        <th>Vence</th>
                        <th>Monto</th>
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
    const data = @json($contratos);
    const csrfToken = '{{ csrf_token() }}';

    $('#contratos-table').DataTable({
        data: data,
        columns: [
            { data: null, className: 'text-center ps-4', orderable: false, searchable: false, width: '50px',
                render: function(d, t, r, m) { return '<span class="text-muted fw-bold">' + (m.row + 1) + '</span>'; }
            },
            { data: null, orderable: true, searchable: true,
                render: function(d) { return '<div class="fw-bold">' + escapeHtml(d.vivienda?.nombre || '—') + '</div>'; }
            },
            { data: null,
                render: function(d) { return escapeHtml(d.inquilino?.nombre || '—'); }
            },
            { data: 'fecha_inicio',
                render: function(d) { return d ? new Date(d).toLocaleDateString('es-DO') : '—'; }
            },
            { data: 'fecha_fin',
                render: function(d) { return d ? new Date(d).toLocaleDateString('es-DO') : '<span class="text-muted small">Indefinido</span>'; }
            },
            { data: 'monto_alquiler', className: 'text-end',
                render: function(d) { return '<div class="fw-bold" style="color:#f59e0b;">RD$ ' + parseFloat(d||0).toLocaleString('en-US', {minimumFractionDigits:2}) + '</div>'; }
            },
            { data: 'estado', className: 'text-center',
                render: function(d) {
                    const map = { activo:'Activo', vencido:'Vencido', cancelado:'Cancelado', finalizado:'Finalizado' };
                    const colors = { activo:'#10b981', vencido:'#ef4444', cancelado:'#f59e0b', finalizado:'#64748b' };
                    return '<span class="badge rounded-pill px-3" style="background:rgba(' + hexToRgb(colors[d]||'#94a3b8') + ',.1);color:' + (colors[d]||'#94a3b8') + ';font-weight:600;">' + (map[d]||d) + '</span>';
                }
            },
            { data: null, className: 'text-end pe-4', orderable: false, searchable: false,
                render: function(d) {
                    return '<div class="d-flex justify-content-end gap-1">' +
                        '<a href="/alquileres/contratos/' + d.id + '/editar" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                        '<form action="/alquileres/contratos/' + d.id + '" method="POST" class="d-inline" onsubmit="return UI.confirm.delete(\'¿Eliminar este contrato? Esta acción no se puede deshacer.\');">' +
                        '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<button type="submit" class="ui-action ui-action-delete border-0" title="Eliminar"><i class="bi bi-trash"></i></button></form></div>';
                }
            }
        ],
        language: {
            search: '', lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ contratos',
            infoEmpty: 'No hay contratos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: { first: '<i class="bi bi-chevron-double-left"></i>', last: '<i class="bi bi-chevron-double-right"></i>', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-file-earmark-text d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron contratos</p><p class="text-muted small mb-0">Crea un nuevo contrato para empezar.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[3, 'desc']],
        responsive: { details: { type: 'column', target: 'tr', renderer: function(api, rowIdx, columns) { let d = ''; columns.forEach(function(c) { if (c.hidden) { d += '<li><span class="child-label">' + c.title + '</span><span class="child-value">' + c.data + '</span></li>'; } }); return d ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + d + '</ul>') : false; } } },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-12"tr>><"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function escapeHtml(s) { return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
    function hexToRgb(h) { return parseInt(h.slice(1,3),16)+','+parseInt(h.slice(3,5),16)+','+parseInt(h.slice(5,7),16); }
});
</script>
@endpush
