@extends('layouts.app')

@section('title', 'UI System Demo')

@push('styles')
@include('partials.premium-ui')
<style>
.demo-section { margin-bottom: 2.5rem; }
.demo-section h5 { font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; font-weight: 700; margin-bottom: 1rem; }
.color-swatch { display: inline-flex; align-items: center; gap: .5rem; padding: .35rem .75rem; border-radius: .5rem; font-size: .8rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-palette"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">UI System v2</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-grid me-1"></i>
                        Catálogo interactivo de componentes
                        <span class="divider">·</span>
                        <i class="bi bi-palette me-1"></i>
                        <span>Tema: Purple</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="#" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="UI.toast.success('Demo toast!')">
                    <i class="bi bi-bell me-1"></i> Notificar
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Stat cards --}}
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Componentes</div>
                    <div class="ui-stat-value">24</div>
                    <div class="ui-stat-sub">clases .ui-*</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">CSS Variables</div>
                    <div class="ui-stat-value">32</div>
                    <div class="ui-stat-sub">design tokens</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Módulos</div>
                    <div class="ui-stat-value">0/12</div>
                    <div class="ui-stat-sub">migrados</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Dark Mode</div>
                    <div class="ui-stat-value">100%</div>
                    <div class="ui-stat-sub">cobertura</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- BUTTONS --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-ui-checks"></i>Botones</h6>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">Primary</button>
                        <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">Solid</button>
                        <button class="ui-btn ui-btn-danger ui-btn-sm rounded-pill">Danger</button>
                        <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Ghost</button>
                        <button class="ui-btn ui-btn-link ui-btn-sm">Link</button>
                        <button class="ui-btn ui-btn-solid ui-btn-sm" disabled>Disabled</button>
                    </div>
                    <h6 class="ui-card-title mt-4"><i class="bi bi-grid-3x3-gap"></i>Acciones</h6>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                        <a href="#" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                        <a href="#" class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></a>
                        <a href="#" class="ui-action ui-action-print" title="Imprimir"><i class="bi bi-printer"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- BADGES --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-tags"></i>Badges Semánticos</h6>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle"></i> Activo</span>
                        <span class="ui-badge ui-badge-warning"><i class="bi bi-exclamation-circle"></i> Pendiente</span>
                        <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle"></i> Inactivo</span>
                        <span class="ui-badge ui-badge-info"><i class="bi bi-info-circle"></i> Info</span>
                        <span class="ui-badge ui-badge-neutral">Neutral</span>
                        <span class="ui-badge ui-badge-primary"><i class="bi bi-star"></i> Primario</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM ELEMENTS --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-input-cursor"></i>Formularios</h6>
                    <div class="mt-3">
                        <label class="ui-label">Nombre</label>
                        <input type="text" class="ui-input mb-3" placeholder="Escribe algo..." value="Ejemplo de texto">
                        <label class="ui-label">Categoría</label>
                        <select class="ui-select mb-3">
                            <option>Categoría 1</option>
                            <option>Categoría 2</option>
                            <option>Categoría 3</option>
                        </select>
                        <div class="ui-input-group mb-3">
                            <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="ui-input" placeholder="Buscar...">
                        </div>
                        <label class="ui-label">Notas</label>
                        <textarea class="ui-textarea" placeholder="Escribe notas aquí..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-0">
                    <h6 class="ui-card-title"><i class="bi bi-table"></i>Tabla</h6>
                    <div class="table-responsive">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nombre</th>
                                    <th>Estado</th>
                                    <th>Valor</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-semibold">Producto Demo 1</td>
                                    <td><span class="ui-badge ui-badge-success">Activo</span></td>
                                    <td>RD$ 1,500.00</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="#" class="ui-action ui-action-view"><i class="bi bi-eye"></i></a>
                                            <a href="#" class="ui-action ui-action-edit"><i class="bi bi-pencil"></i></a>
                                            <a href="#" class="ui-action ui-action-delete"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-semibold">Producto Demo 2</td>
                                    <td><span class="ui-badge ui-badge-warning">Pendiente</span></td>
                                    <td>RD$ 850.00</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="#" class="ui-action ui-action-view"><i class="bi bi-eye"></i></a>
                                            <a href="#" class="ui-action ui-action-edit"><i class="bi bi-pencil"></i></a>
                                            <a href="#" class="ui-action ui-action-delete"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-semibold">Producto Demo 3</td>
                                    <td><span class="ui-badge ui-badge-danger">Inactivo</span></td>
                                    <td>RD$ 2,300.00</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="#" class="ui-action ui-action-view"><i class="bi bi-eye"></i></a>
                                            <a href="#" class="ui-action ui-action-edit"><i class="bi bi-pencil"></i></a>
                                            <a href="#" class="ui-action ui-action-delete"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL ROWS --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-layout-text-window"></i>Detail Rows</h6>
                    <div class="mt-3">
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Nombre</span>
                            <span class="ui-detail-value">Juan Pérez</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Email</span>
                            <span class="ui-detail-value">juan@ejemplo.com</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Teléfono</span>
                            <span class="ui-detail-value">(809) 555-0100</span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Estado</span>
                            <span class="ui-detail-value"><span class="ui-badge ui-badge-success">Activo</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EMPTY STATE --}}
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-inbox"></i>Empty State</h6>
                    <div class="ui-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No hay registros disponibles</p>
                        <span class="text-muted small">Crea tu primer registro para comenzar</span>
                        <div class="mt-3">
                            <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                                <i class="bi bi-plus-lg"></i> Crear primero
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONFIRM DIALOG --}}
        <div class="col-md-12">
            <div class="ui-card" style="--delay:.4s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-chat-square-dots"></i>SweetAlert2 Unificado</h6>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" onclick="UI.confirm.action({ title:'¿Confirmar?', text:'Acción personalizada', icon:'question', color:'#8b5cf6', confirmText:'Sí, hacerlo' })">
                            <i class="bi bi-question-circle"></i> Confirmar acción
                        </button>
                        <button class="ui-btn ui-btn-danger ui-btn-sm rounded-pill" onclick="UI.confirm.delete('/ui-demo', 'Registro demo')">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                        <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="UI.toast.success('Operación completada exitosamente')">
                            <i class="bi bi-check-circle"></i> Toast Success
                        </button>
                        <button class="ui-btn ui-btn-danger ui-btn-sm rounded-pill" onclick="UI.toast.error('Ocurrió un error inesperado')">
                            <i class="bi bi-exclamation-triangle"></i> Toast Error
                        </button>
                        <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="UI.toast.warning('Este es un aviso importante')">
                            <i class="bi bi-exclamation-circle"></i> Toast Warning
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TEMAS --}}
        <div class="col-md-12">
            <div class="ui-card" style="--delay:.45s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title"><i class="bi bi-palette2"></i>Temas por Módulo</h6>
                    <p class="text-muted small mb-3">Cada módulo define solo <code>--accent</code> y <code>--accent-rgb</code> en su wrapper.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="color-swatch" style="background:#10b981;color:#fff">Gastos #10b981</div>
                        <div class="color-swatch" style="background:#3b82f6;color:#fff">Ventas #3b82f6</div>
                        <div class="color-swatch" style="background:#6366f1;color:#fff">Productos #6366f1</div>
                        <div class="color-swatch" style="background:#10b981;color:#fff">Clientes #10b981</div>
                        <div class="color-swatch" style="background:#f59e0b;color:#fff">Compras #f59e0b</div>
                        <div class="color-swatch" style="background:#8b5cf6;color:#fff">Cajas #8b5cf6</div>
                        <div class="color-swatch" style="background:#ec4899;color:#fff">Categorías #ec4899</div>
                        <div class="color-swatch" style="background:#8b5cf6;color:#fff">Lista Precio #8b5cf6</div>
                        <div class="color-swatch" style="background:#f59e0b;color:#fff">Alquileres #f59e0b</div>
                        <div class="color-swatch" style="background:#6366f1;color:#fff">Cotizaciones #6366f1</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
