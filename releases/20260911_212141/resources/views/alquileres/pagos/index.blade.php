@extends('layouts.app')
@section('title', 'Pagos de Alquiler')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #f43f5e;
    --dt-accent-gradient: linear-gradient(135deg, #f43f5e, #ec4899);
    --dt-accent-rgb: 244,63,94;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #f43f5e, #ec4899);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Pagos de Alquiler</h4>
                    <small class="text-white opacity-75">{{ $pagos->count() }} pagos registrados</small>
                </div>
            </div>
            <div>
                <a href="{{ route('alquileres.pagos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Registrar Pago
                </a>
            </div>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent red"></div>
        <div class="card-body p-0">
            <table id="pagos-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Vivienda</th>
                        <th>Inquilino</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Período</th>
                        <th>Método</th>
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
    const data = @json($pagos);
    const csrfToken = '{{ csrf_token() }}';
    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    $('#pagos-table').DataTable({
        data: data,
        columns: [
            { data: null, className: 'text-center ps-4', orderable: false, searchable: false, width: '50px',
                render: function(d, t, r, m) { return '<span class="text-muted fw-bold">' + (m.row + 1) + '</span>'; }
            },
            { data: null,
                render: function(d) { return '<div class="fw-bold">' + escapeHtml(d.contrato?.vivienda?.nombre || '—') + '</div>'; }
            },
            { data: null,
                render: function(d) { return escapeHtml(d.contrato?.inquilino?.nombre || '—'); }
            },
            { data: 'monto', className: 'text-end',
                render: function(d) { return '<div class="fw-bold" style="color:var(--dt-accent);">RD$ ' + parseFloat(d||0).toLocaleString('en-US', {minimumFractionDigits:2}) + '</div>'; }
            },
            { data: 'fecha_pago',
                render: function(d) { return d ? new Date(d).toLocaleDateString('es-DO') : '—'; }
            },
            { data: null,
                render: function(d) { return (meses[(d.mes_cobrado||1)-1] || '') + ' ' + (d.ano_cobrado||''); }
            },
            { data: 'metodo_pago',
                render: function(d) {
                    const map = { efectivo:'Efectivo', tarjeta:'Tarjeta', transferencia:'Transferencia', deposito:'Depósito', otro:'Otro' };
                    return '<span class="badge rounded-pill bg-light text-dark px-3">' + (map[d]||d) + '</span>';
                }
            },
            { data: null, className: 'text-end pe-4', orderable: false, searchable: false,
                render: function(d) {
                    return '<div class="d-flex justify-content-end gap-1">' +
                        '<a href="/alquileres/pagos/' + d.id + '/editar" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>' +
                        '<form action="/alquileres/pagos/' + d.id + '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este pago? Esta acción no se puede deshacer.\');">' +
                        '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<button type="submit" class="premium-btn-delete border-0" title="Eliminar"><i class="bi bi-trash"></i></button></form></div>';
                }
            }
        ],
        language: {
            search: '', lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ pagos',
            infoEmpty: 'No hay pagos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: { first: '<i class="bi bi-chevron-double-left"></i>', last: '<i class="bi bi-chevron-double-right"></i>', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-cash-coin d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron pagos</p><p class="text-muted small mb-0">Registra un nuevo pago para empezar.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[4, 'desc']],
        responsive: { details: { type: 'column', target: 'tr', renderer: function(api, rowIdx, columns) { let d = ''; columns.forEach(function(c) { if (c.hidden) { d += '<li><span class="child-label">' + c.title + '</span><span class="child-value">' + c.data + '</span></li>'; } }); return d ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + d + '</ul>') : false; } } },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-12"tr>><"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function escapeHtml(s) { return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
});
</script>
@endpush
