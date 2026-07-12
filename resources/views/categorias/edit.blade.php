@extends('layouts.app')
@section('title', 'Editar Categoría')

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
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .premium-card .form-check-input:checked {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
    .producto-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid transparent;
    }
    .producto-toggle:hover { background: rgba(139,92,246,0.05); }
    .producto-toggle input[type="checkbox"] {
        width: 18px; height: 18px;
        cursor: pointer;
        accent-color: #8b5cf6;
    }
    .producto-toggle.is-checked {
        background: rgba(34,197,94,0.08);
        border-color: rgba(34,197,94,0.3);
    }
    .producto-toggle.is-checked .prod-name {
        color: #16a34a;
        font-weight: 700;
    }
    .producto-toggle .prod-name {
        font-size: 0.85rem;
        flex-grow: 1;
    }
    .producto-toggle .prod-cat-badge {
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(139,92,246,0.1);
        color: #7c3aed;
        font-weight: 600;
    }
    .producto-toggle .prod-cat-badge.current {
        background: rgba(34,197,94,0.15);
        color: #16a34a;
    }
    .producto-toggle .prod-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        transition: all 0.15s;
    }
    .producto-toggle:not(.is-checked) .prod-icon {
        background: rgba(15,23,42,0.06);
        color: #94a3b8;
    }
    .producto-toggle.is-checked .prod-icon {
        background: rgba(34,197,94,0.15);
        color: #16a34a;
    }
    .quick-actions .btn {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 8px;
    }
    body.dark-mode .producto-toggle:hover { background: rgba(139,92,246,0.1); }
    body.dark-mode .producto-toggle:not(.is-checked) .prod-icon { background: rgba(255,255,255,0.08); color: #64748b; }
    body.dark-mode .producto-toggle.is-checked .prod-icon { background: rgba(34,197,94,0.2); color: #4ade80; }
    body.dark-mode .producto-toggle .prod-cat-badge { background: rgba(139,92,246,0.2); color: #a78bfa; }
    body.dark-mode .producto-toggle .prod-cat-badge.current { background: rgba(34,197,94,0.2); color: #4ade80; }
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
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Editar Categoría</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-pencil me-1"></i>{{ $categoria->nombre }}
                    </small>
                </div>
            </div>
            <a href="{{ route('categorias.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="categoriaForm" action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="premium-card">
                        <div class="card-accent purple"></div>
                        <div class="card-body p-4">
                            <div class="mb-4 pb-3 border-bottom">
                                <h6 class="fw-bold mb-0" style="color: #8b5cf6;">
                                    <i class="bi bi-info-circle me-2"></i>Información de la Categoría
                                </h6>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" value="{{ old('nombre', $categoria->nombre) }}" required placeholder="Ej. Alimentos, Bebidas, Limpieza">
                                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Descripción</label>
                                <textarea name="descripcion" class="form-control form-control-lg" rows="3" placeholder="Descripción opcional de la categoría">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" {{ $categoria->activa ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-2" for="activa" style="cursor: pointer;">
                                        <i class="bi bi-check-circle text-success me-1"></i>Categoría activa
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-1">Si está activa, los productos podrán asignarse a esta categoría.</small>
                            </div>
                        </div>
                    </div>

                    <div class="premium-card mt-3">
                        <div class="card-accent purple"></div>
                        <div class="card-body text-center">
                            <div class="stat-label">Productos Seleccionados</div>
                            <div class="stat-value" style="color: #8b5cf6;" id="selectedCount">{{ $productos->where('categoria_id', $categoria->id)->count() }}</div>
                            <small class="text-muted">de {{ $productos->count() }} disponibles</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="premium-card">
                        <div class="card-accent purple"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <h5 class="fw-bold mb-0" style="color: #8b5cf6;">
                                        <i class="bi bi-box-seam me-2"></i>Productos en esta Categoría
                                    </h5>
                                    <small class="text-muted">Selecciona los productos que pertenecerán a esta categoría</small>
                                </div>
                                <div class="d-flex gap-2 align-items-center quick-actions">
                                    <button type="button" class="btn btn-outline-success" onclick="selectAll()">
                                        <i class="bi bi-check-all me-1"></i>Todos
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="clearAll()">
                                        <i class="bi bi-x-lg me-1"></i>Limpiar
                                    </button>
                                    <div class="input-group" style="max-width: 220px;">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                        <input type="text" id="productoFilter" class="form-control border-0 bg-light" placeholder="Buscar producto...">
                                    </div>
                                </div>
                            </div>

                            @if($productos->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted fs-1"></i>
                                    <p class="text-muted mt-2 mb-0">No hay productos disponibles para asignar.</p>
                                    <a href="{{ route('productos.create') }}" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                                        <i class="bi bi-plus me-1"></i>Crear Producto
                                    </a>
                                </div>
                            @else
                                <div class="row g-2" id="productosList">
                                    @foreach($productos as $producto)
                                        @php
                                            $isChecked = $producto->categoria_id == $categoria->id;
                                            $catName = $producto->categoria ? $producto->categoria->nombre : null;
                                        @endphp
                                        <div class="col-md-6 producto-filterable" data-text="{{ strtolower($producto->nombre) }}">
                                            <label class="producto-toggle {{ $isChecked ? 'is-checked' : '' }}">
                                                <input type="checkbox" name="productos[]" value="{{ $producto->id }}"
                                                       data-categoria="{{ $producto->categoria_id ?? '' }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <span class="prod-icon"><i class="bi {{ $isChecked ? 'bi-check-circle-fill' : 'bi-box-seam' }}"></i></span>
                                                <span class="prod-name">{{ $producto->nombre }}</span>
                                                @if($catName)
                                                    <span class="prod-cat-badge {{ $isChecked ? 'current' : '' }}">{{ $catName }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: {{ $categoria->nombre }}</span>
            <span class="badge rounded-pill" style="background: rgba(139,92,246,0.15); color: #7c3aed;" id="stickyCount">
                {{ $productos->where('categoria_id', $categoria->id)->count() }} productos
            </span>
        </div>
        <div>
            <a href="{{ route('categorias.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="categoriaForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Actualizar Categoría
            </button>
        </div>
    </div>
</div>

<script>
    const updateCount = () => {
        const n = document.querySelectorAll('input[name="productos[]"]:checked').length;
        document.getElementById('selectedCount').textContent = n;
        document.getElementById('stickyCount').textContent = n + ' producto' + (n !== 1 ? 's' : '');
    };

    const updateVisual = (checkbox) => {
        const toggle = checkbox.closest('.producto-toggle');
        const icon = toggle.querySelector('.prod-icon i');
        const badge = toggle.querySelector('.prod-cat-badge');
        if (checkbox.checked) {
            toggle.classList.add('is-checked');
            icon.className = 'bi bi-check-circle-fill';
            if (badge) badge.classList.add('current');
        } else {
            toggle.classList.remove('is-checked');
            icon.className = 'bi bi-box-seam';
            if (badge) badge.classList.remove('current');
        }
        updateCount();
    };

    document.querySelectorAll('input[name="productos[]"]').forEach(cb => {
        cb.addEventListener('change', () => updateVisual(cb));
    });

    const selectAll = () => {
        document.querySelectorAll('.producto-filterable:not([style*="display: none"]) input[name="productos[]"]').forEach(cb => {
            cb.checked = true;
            updateVisual(cb);
        });
    };

    const clearAll = () => {
        document.querySelectorAll('input[name="productos[]"]').forEach(cb => {
            cb.checked = false;
            updateVisual(cb);
        });
    };

    document.getElementById('productoFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.producto-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>
@endsection