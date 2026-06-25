@extends('layouts.app')
@section('title', 'Ubicaciones de Mesas')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #10b981;
    --dt-accent-gradient: linear-gradient(135deg, #10b981, #06b6d4);
    --dt-accent-rgb: 16,185,129;
}
.premium-header {
    background: linear-gradient(135deg, #10b981, #059669, #06b6d4, #10b981);
    background-size: 300% 300%;
    box-shadow: 0 8px 32px rgba(16,185,129,.25);
}
.premium-header::before {
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
}
.premium-card .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Ubicaciones de Mesas</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-info-circle me-1"></i>Gestiona las áreas o zonas del restaurante
                    </small>
                </div>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#ubicacionModal" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-plus-lg me-1"></i> Nueva Ubicación
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <table id="ubicaciones-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Mesas</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar --}}
<div class="modal fade" id="ubicacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:1.2rem;border:0;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#10b981,#059669);border-radius:1.2rem 1.2rem 0 0;padding:1.5rem 1.75rem;">
                <div>
                    <h5 class="modal-title fw-bold text-white" id="modalTitle">
                        <i class="bi bi-geo-alt me-2"></i>Nueva Ubicación
                    </h5>
                    <p class="text-white text-opacity-75 small mb-0 mt-1">Área o zona del restaurante</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="ubicacionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="ubicacion_id" id="ubicacionId">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-lg" required placeholder="Ej. Terraza, Salón Principal, VIP">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                    </div>
                    <div class="p-3 bg-light rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" checked role="switch" style="width:3rem;height:1.5rem;cursor:pointer;">
                            <label class="form-check-label fw-semibold ms-2" for="activa" style="cursor:pointer;">
                                <i class="bi bi-check-circle text-success me-1"></i>Ubicación activa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-weight:600;">Cancelar</button>
                    <button type="submit" class="btn rounded-pill px-4 text-white" id="btnSave" style="background:linear-gradient(135deg,#10b981,#059669);font-weight:600;">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const data = @json($ubicaciones);
    const csrfToken = '{{ csrf_token() }}';

    const table = $('#ubicaciones-table').DataTable({
        data: data,
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: false,
                searchable: false,
                width: '50px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>';
                }
            },
            {
                data: 'nombre',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return '<div class="fw-bold fs-6" style="color:#1e293b;">' + escapeHtml(data) + '</div>';
                }
            },
            {
                data: 'descripcion',
                defaultContent: '<span class="text-muted small">—</span>',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">—</span>';
                    return '<div class="text-muted small" title="' + escapeHtml(data) + '">' + escapeHtml(data) + '</div>';
                }
            },
            {
                data: 'mesas_count',
                className: 'text-center',
                render: function(data) {
                    const count = parseInt(data || 0);
                    return '<span class="badge bg-light text-secondary border rounded-pill">' +
                        '<i class="bi bi-table me-1"></i> ' + count + ' mesas</span>';
                }
            },
            {
                data: 'activa',
                className: 'text-center',
                render: function(data) {
                    return data
                        ? '<span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">' +
                            '<i class="bi bi-check-circle-fill me-1"></i> Activa</span>'
                        : '<span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-weight:600;">' +
                            '<i class="bi bi-x-circle-fill me-1"></i> Inactiva</span>';
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    let html = '<div class="d-flex justify-content-end gap-1">';
                    html += '<button type="button" class="premium-btn-edit" title="Editar" onclick="editar(' + data.id + ')">' +
                        '<i class="bi bi-pencil"></i></button>';
                    html += '<form action="/restaurante/ubicaciones/' + data.id + '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar la ubicación ' + escapeHtml(data.nombre) + '? Solo es posible si no tiene mesas asociadas.\');">' +
                        '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<button type="submit" class="premium-btn-delete border-0" title="Eliminar">' +
                        '<i class="bi bi-trash"></i></button></form>';
                    html += '</div>';
                    return html;
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ ubicaciones',
            infoEmpty: 'No hay ubicaciones',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-geo-alt d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron ubicaciones</p>' +
                '<p class="text-muted small mb-0">Crea la primera ubicación para comenzar.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: {
            details: {
                type: 'column',
                target: 'tr',
                renderer: function(api, rowIdx, columns) {
                    let data = '';
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

    window.editar = function(id) {
        fetch('/restaurante/ubicaciones/' + id)
            .then(r => r.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Ubicación';
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('ubicacionId').value = id;
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('descripcion').value = data.descripcion || '';
                document.getElementById('activa').checked = data.activa;
                document.getElementById('btnSave').innerHTML = '<i class="bi bi-check-lg me-1"></i>Actualizar';
                document.getElementById('ubicacionForm').action = '/restaurante/ubicaciones/' + id;
                new bootstrap.Modal(document.getElementById('ubicacionModal')).show();
            });
    };

    document.getElementById('ubicacionModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-geo-alt me-2"></i>Nueva Ubicación';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('ubicacionId').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('activa').checked = true;
        document.getElementById('btnSave').innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardar';
        document.getElementById('ubicacionForm').action = '{{ route("restaurante.ubicaciones.store") }}';
    });

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush