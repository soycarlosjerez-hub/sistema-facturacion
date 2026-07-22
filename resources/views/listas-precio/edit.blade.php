@extends('layouts.app')
@section('title', $listaPrecio->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 44%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .premium-card .form-check-input:checked {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
    .margin-positive { color: #198754; font-weight: 600; }
    .margin-negative { color: #dc3545; font-weight: 600; }
    .margin-zero { color: #6c757d; font-weight: 600; }
    .premium-sticky-bar { border-top-color: #8b5cf6 !important; }
    .premium-sticky-bar .btn-save { background: linear-gradient(135deg, #8b5cf6, #a855f7) !important; box-shadow: 0 4px 14px rgba(139,92,246,.3) !important; }
    .precio-lista:focus { border-color: #8b5cf6 !important; box-shadow: 0 0 0 3px rgba(139,92,246,.15) !important; }
    .producto-no-lista:not(.d-none) td { opacity: 0.5; font-style: italic; }
    .estado-badge { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; font-size: .65rem; }
    .estado-badge-sin { background: #f1f5f9; color: #94a3b8; }
    body.dark-mode .premium-sticky-bar { border-top-color: #a855f7 !important; }
    body.dark-mode .precio-lista:focus { border-color: #a855f7 !important; box-shadow: 0 0 0 3px rgba(139,92,246,.25) !important; }
    body.dark-mode .estado-badge-sin { background: rgba(30,41,59,.8); color: #475569; }
    .precios-table thead th {
        background: rgba(241,245,249,.8);
        color: #64748b;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: .85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .precios-table tbody td {
        padding: .85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .9rem;
    }
    .precios-table tbody tr:last-child td { border-bottom: none; }
    .precios-table tbody tr { transition: background .15s; }
    body.dark-mode .precios-table thead th {
        background: rgba(15,23,42,.5);
        color: #94a3b8;
        border-color: #1e293b;
    }
    body.dark-mode .precios-table tbody td {
        border-bottom-color: #1e293b;
        color: #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Editar Lista de Precios</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-pencil me-1"></i>
                        {{ $listaPrecio->nombre }}
                    </small>
                </div>
            </div>
            <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="premium-card" style="animation-delay:.1s;">
                <div class="card-accent purple"></div>
                <div class="d-flex justify-content-between align-items-center p-4 pb-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2" style="color: #8b5cf6;"></i>Productos y Precios</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 precios-table" id="tablaPrecios">
                        <thead class="table-light">
                            <tr class="text-muted text-uppercase small">
                                <th class="ps-4">C&oacute;digo</th>
                                <th>Producto</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Margen %</th>
                                <th class="text-end">Precio Actual</th>
                                <th class="text-end">Precio Lista</th>
                                <th class="text-center pe-4" style="width:50px;"><i class="bi bi-circle-fill" style="font-size:.4rem;color:#94a3b8;"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center py-3">
                                    <div class="input-group" style="max-width:400px;margin:0 auto;">
                                        <input type="text" id="filtroProducto" class="form-control" placeholder="Filtrar productos...">
                                        <button class="btn btn-outline-primary" id="btnAgregarProducto" type="button"><i class="bi bi-plus-lg"></i> Agregar</button>
                                    </div>
                                </td>
                            </tr>
                            @foreach($productos as $p)
                            @php
                                $item = $listaPrecio->items->firstWhere('producto_id', $p->id);
                                $costo = (float) ($p->precio_compra ?? 0);
                                $precioLista = $item ? (float) $item->precio : 0;
                                $margen = $costo > 0 && $precioLista > 0
                                    ? ((($precioLista - $costo) / $costo) * 100)
                                    : 0;
                                $marginClass = $margen > 0 ? 'margin-positive' : ($margen < 0 ? 'margin-negative' : 'margin-zero');
                                $marginSign = $margen > 0 ? '+' : '';
                            @endphp
                            <tr data-producto-id="{{ $p->id }}" class="{{ $item ? '' : 'd-none producto-no-lista' }}">
                                <td class="ps-4"><span class="badge bg-light text-muted rounded-pill">{{ $p->codigo_barras ?? '&mdash;' }}</span></td>
                                <td class="fw-bold small">{{ $p->nombre }}</td>
                                <td class="text-end text-muted">RD$ {{ number_format($costo, 2) }}</td>
                                <td class="text-end {{ $marginClass }}">
                                    @if($costo > 0 && $precioLista > 0)
                                        {{ $marginSign }}{{ number_format($margen, 1) }}%
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="text-end text-muted">RD$ {{ number_format($p->precio, 2) }}</td>
                                <td class="text-end">
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end precio-lista"
                                           style="width:130px;display:inline-block;"
                                           value="{{ $item ? number_format($item->precio, 2, '.', '') : '' }}"
                                           placeholder="0.00"
                                           data-producto-id="{{ $p->id }}">
                                </td>
                                <td class="text-center pe-4">
                                    <span class="estado-badge estado-badge-sin"><i class="bi bi-dash-lg"></i></span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card mb-3" style="animation-delay:.15s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-gear icon-purple"></i>
                    Informaci&oacute;n
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2"><span class="text-muted">Estado:</span>
                            @if($listaPrecio->activa)
                                <span class="badge bg-success rounded-pill">Activa</span>
                            @else
                                <span class="badge bg-danger rounded-pill">Inactiva</span>
                            @endif
                        </div>
                        @if($listaPrecio->vigencia_desde)
                        <div class="mb-2"><span class="text-muted">Vigencia:</span> {{ $listaPrecio->vigencia_desde->format('d/m/Y') }} - {{ $listaPrecio->vigencia_hasta?->format('d/m/Y') ?? 'Indefinida' }}</div>
                        @endif
                    </div>
                    <hr>
                    <form id="listaPrecioForm" action="{{ route('listas-precio.update', $listaPrecio) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-2">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $listaPrecio->nombre }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">C&oacute;digo</label>
                            <input type="text" name="codigo" class="form-control" value="{{ $listaPrecio->codigo }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="form-control" rows="2">{{ $listaPrecio->descripcion }}</textarea>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label">Vigencia desde</label>
                                <input type="date" name="vigencia_desde" class="form-control" value="{{ $listaPrecio->vigencia_desde?->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Vigencia hasta</label>
                                <input type="date" name="vigencia_hasta" class="form-control" value="{{ $listaPrecio->vigencia_hasta?->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ $listaPrecio->activa ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activa">Activa</label>
                        </div>
                    </form>
                </div>
            </div>

            <div class="premium-card" style="animation-delay:.2s;">
                <div class="card-accent purple"></div>
                <div class="premium-card-title">
                    <i class="bi bi-lightning icon-purple"></i>
                    Acciones R&aacute;pidas
                </div>
                <div class="card-body">
                    <a href="{{ route('listas-precio.impacto', $listaPrecio) }}" class="btn btn-outline-warning w-100 rounded-pill btn-sm mb-2">
                        <i class="bi bi-graph-up me-1"></i>Impacto de Precios
                    </a>
                    <a href="{{ route('listas-precio.logs', $listaPrecio) }}" class="btn btn-outline-secondary w-100 rounded-pill btn-sm mb-2">
                        <i class="bi bi-clock-history me-1"></i>Historial de Cambios
                    </a>
                    <form action="{{ route('listas-precio.duplicar', $listaPrecio) }}" method="POST" class="mb-2">
                        @csrf
                        <button class="btn btn-outline-info w-100 rounded-pill btn-sm">
                            <i class="bi bi-copy me-1"></i>Duplicar lista
                        </button>
                    </form>
                    <form action="{{ route('listas-precio.destroy', $listaPrecio) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="button" class="premium-btn-delete w-100 rounded-pill btn-sm" onclick="confirmDelete('{{ route('listas-precio.destroy', $listaPrecio) }}', '{{ addslashes($listaPrecio->nombre) }}')">
                            <i class="bi bi-trash me-1"></i>Eliminar lista
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>

    <div id="stickySaveBar" class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color: #8b5cf6;"></i>
                <span class="fw-semibold d-none d-sm-inline">Editando: {{ $listaPrecio->nombre }}</span>
                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill small px-2 border border-warning border-opacity-25" id="cambiosPendientesBadge" style="display:none;">
                    <i class="bi bi-exclamation-circle me-1"></i>Precios sin guardar
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnGuardarPrecios" class="btn-save">
                    <i class="bi bi-coin me-1"></i>Guardar Precios
                </button>
                <button type="submit" form="listaPrecioForm" class="btn-save">
                    <i class="bi bi-save me-1"></i>Guardar Datos
                </button>
                <button type="button" class="btn-close ms-2" aria-label="Cerrar" onclick="document.getElementById('stickySaveBar').style.display='none'" style="filter:invert(0.5);"></button>
            </div>
        </div>
    </div>
</div>

<form id="formActualizarPrecios" action="{{ route('listas-precio.actualizar-precios', $listaPrecio) }}" method="POST">
    @csrf
    <div id="preciosContainer"></div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filtro = document.getElementById('filtroProducto');
    const btnGuardar = document.getElementById('btnGuardarPrecios');
    const form = document.getElementById('formActualizarPrecios');
    const container = document.getElementById('preciosContainer');

    filtro.addEventListener('input', () => {
        const q = filtro.value.trim().toLowerCase();
        document.querySelectorAll('#tablaPrecios tbody tr[data-producto-id]').forEach(row => {
            const nombre = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
            row.classList.toggle('d-none', q.length > 0 && !nombre.includes(q));
        });
    });

    document.getElementById('btnAgregarProducto').addEventListener('click', () => {
        const q = filtro.value.trim().toLowerCase();
        document.querySelectorAll('.producto-no-lista').forEach(row => {
            const nombre = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
            if (q.length > 0 && nombre.includes(q)) {
                row.classList.remove('d-none');
            }
        });
    });

    btnGuardar.addEventListener('click', () => {
        container.innerHTML = '';
        const inputs = document.querySelectorAll('.precio-lista');
        let count = 0;
        inputs.forEach(input => {
            const val = input.value.trim();
            if (val !== '' && parseFloat(val) >= 0) {
                const pid = input.dataset.productoId;
                const h = document.createElement('input');
                h.type = 'hidden';
                h.name = `precios[${count}][producto_id]`;
                h.value = pid;
                container.appendChild(h);
                const h2 = document.createElement('input');
                h2.type = 'hidden';
                h2.name = `precios[${count}][precio]`;
                h2.value = val;
                container.appendChild(h2);
                count++;
            }
        });
        if (count === 0) { alert('No hay precios para guardar. Ingresa al menos un precio.'); return; }
        form.submit();
    });

    document.querySelectorAll('.precio-lista').forEach(input => {
        input.addEventListener('input', () => {
            const row = input.closest('tr');
            if (input.value.trim() !== '' && parseFloat(input.value) > 0) {
                row.style.background = '#f0fdf4';
            } else {
                row.style.background = '';
            }
        });
    });
});
</script>
@endpush
@push('scripts')
<script>
function confirmDelete(url, nombre) {
    Swal.fire({
        title: '¿Eliminar lista?',
        text: `Se eliminará: "${nombre}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection