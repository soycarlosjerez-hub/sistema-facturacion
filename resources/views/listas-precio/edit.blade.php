@extends('layouts.app')
@section('title', $listaPrecio->nombre)

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(14,165,233, 0.4);
    position: relative;
    overflow: hidden;
}
.premium-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #0ea5e9;
    padding: 0.75rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}
.sticky-save-bar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #38bdf8;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-tags fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Lista de Precios</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $listaPrecio->nombre }}</p>
                </div>
            </div>
            <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Productos y Precios</h5>
                    <button class="btn btn-success rounded-pill px-3" id="btnGuardarPrecios">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaPrecios">
                        <thead class="table-light">
                            <tr class="text-muted text-uppercase small">
                                <th class="ps-4">C&oacute;digo</th>
                                <th>Producto</th>
                                <th class="text-end">Precio Actual</th>
                                <th class="text-end pe-4">Precio Lista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    <div class="input-group mb-3" style="max-width:400px;margin:0 auto;">
                                        <input type="text" id="filtroProducto" class="form-control" placeholder="Filtrar productos...">
                                        <button class="btn btn-outline-primary" id="btnAgregarProducto" type="button"><i class="bi bi-plus-lg"></i> Agregar</button>
                                    </div>
                                </td>
                            </tr>
                            @foreach($productos as $p)
                            @php $item = $listaPrecio->items->firstWhere('producto_id', $p->id); @endphp
                            <tr data-producto-id="{{ $p->id }}" class="{{ $item ? '' : 'd-none producto-no-lista' }}">
                                <td class="ps-4"><span class="badge bg-light text-muted rounded-pill">{{ $p->codigo_barras ?? '—' }}</span></td>
                                <td class="fw-bold small">{{ $p->nombre }}</td>
                                <td class="text-end text-muted">RD$ {{ number_format($p->precio, 2) }}</td>
                                <td class="text-end pe-4">
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end precio-lista" 
                                           style="width:130px;display:inline-block;"
                                           value="{{ $item ? number_format($item->precio, 2, '.', '') : '' }}"
                                           placeholder="0.00"
                                           data-producto-id="{{ $p->id }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Informaci&oacute;n</h6>
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
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input type="text" name="nombre" class="form-control form-control-sm" value="{{ $listaPrecio->nombre }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">C&oacute;digo</label>
                            <input type="text" name="codigo" class="form-control form-control-sm" value="{{ $listaPrecio->codigo }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="form-control form-control-sm" rows="2">{{ $listaPrecio->descripcion }}</textarea>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Vigencia desde</label>
                                <input type="date" name="vigencia_desde" class="form-control form-control-sm" value="{{ $listaPrecio->vigencia_desde?->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Vigencia hasta</label>
                                <input type="date" name="vigencia_hasta" class="form-control form-control-sm" value="{{ $listaPrecio->vigencia_hasta?->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ $listaPrecio->activa ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="activa">Activa</label>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2"></i>Acciones R&aacute;pidas</h6>
                    <form action="{{ route('listas-precio.duplicar', $listaPrecio) }}" method="POST" class="mb-2">
                        @csrf
                        <button class="btn btn-outline-info w-100 rounded-pill btn-sm">
                            <i class="bi bi-copy me-1"></i>Duplicar lista
                        </button>
                    </form>
                    <form action="{{ route('listas-precio.destroy', $listaPrecio) }}" method="POST" onsubmit="return confirm('¿Eliminar esta lista?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100 rounded-pill btn-sm">
                            <i class="bi bi-trash me-1"></i>Eliminar lista
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="formActualizarPrecios" action="{{ route('listas-precio.actualizar-precios', $listaPrecio) }}" method="POST">
    @csrf
    <div id="preciosContainer"></div>
</form>

<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: {{ $listaPrecio->nombre }}</span>
        </div>
        <button type="submit" form="listaPrecioForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0ea5e9, #2563eb); border: none;">
            <i class="bi bi-save me-1"></i>Actualizar Lista
        </button>
    </div>
</div>

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
@endsection
