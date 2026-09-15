@extends('layouts.app')
@section('title', 'Plantilla: ' . $plantilla->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .info-row { padding: 0.75rem 0; border-bottom: 1px solid rgba(0,0,0,0.06); }
    .info-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .info-value { font-size: 0.95rem; color: #1e293b; font-weight: 500; }
    .badge-template { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; }
    .badge-default { background: rgba(139,92,246,0.15); color: #7c3aed; }
    .badge-not-default { background: rgba(100,116,139,0.1); color: #64748b; }
    .toggle-switch { position: relative; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none; }
    .toggle-switch input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .toggle-switch .toggle-slider { width: 2.75rem; height: 1.5rem; background: #cbd5e1; border-radius: 1rem; position: relative; transition: background 0.2s; }
    .toggle-switch .toggle-slider::before { content: ''; position: absolute; width: 1.25rem; height: 1.25rem; background: #fff; border-radius: 50%; top: 2px; left: 2px; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
    .toggle-switch input:checked + .toggle-slider { background: #7c3aed; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(1.25rem); }
    .color-preview { display: inline-block; width: 24px; height: 24px; border-radius: 4px; border: 1px solid #e2e8f0; vertical-align: middle; margin-right: 6px; }
    .sucursal-tag { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.8rem; background: rgba(139,92,246,0.08); color: #6d28d9; margin: 0.25rem; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Detalle de Plantilla</h4>
                    <div class="ui-header-meta">
                        @if($plantilla->es_default)
                            <span class="badge-template badge-default me-2"><i class="bi bi-star-fill"></i>Predeterminada</span>
                        @endif
                        {{ $plantilla->nombre }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('plantillas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
                @can('plantillas.edit')
                <a href="{{ route('plantillas.edit', $plantilla) }}" class="ui-btn ui-btn-solid btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left column: info -->
        <div class="col-lg-8">
            <div class="ui-card mb-4">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Informacion General</h6>
                    <div class="row g-3">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Nombre</div>
                            <div class="info-value">{{ $plantilla->nombre }}</div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Codigo</div>
                            <div class="info-value"><code>{{ $plantilla->codigo }}</code></div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Modulo</div>
                            <div class="info-value">{{ \App\Models\PlantillaImpresion::MODULOS[$plantilla->modulo] ?? $plantilla->modulo }}</div>
                        </div>
                        <div class="col-md-3 info-row">
                            <div class="info-label">Formato</div>
                            <div class="info-value">{{ \App\Models\PlantillaImpresion::FORMATOS_PAPEL[$plantilla->formato_papel] ?? $plantilla->formato_papel }}</div>
                        </div>
                        <div class="col-md-3 info-row">
                            <div class="info-label">Orientacion</div>
                            <div class="info-value">{{ $plantilla->orientation === 'landscape' ? 'Horizontal' : 'Vertical' }}</div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Color Primario</div>
                            <div class="info-value"><span class="color-preview" style="background: {{ $plantilla->color_primario }};"></span><code>{{ $plantilla->color_primario }}</code></div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Color Secundario</div>
                            <div class="info-value"><span class="color-preview" style="background: {{ $plantilla->color_secundario }};"></span><code>{{ $plantilla->color_secundario }}</code></div>
                        </div>
                        @if($plantilla->logo_path)
                        <div class="col-md-6 info-row">
                            <div class="info-label">Logo</div>
                            <div class="info-value">
                                <img src="{{ asset('storage/' . $plantilla->logo_path) }}" style="max-height: 40px; max-width: 120px; object-fit: contain;" alt="Logo">
                            </div>
                        </div>
                        @endif
                        @if($plantilla->encabezado_texto)
                        <div class="col-12 info-row">
                            <div class="info-label">Texto de Encabezado</div>
                            <div class="info-value">{{ $plantilla->encabezado_texto }}</div>
                        </div>
                        @endif
                        @if($plantilla->pie_pagina_texto)
                        <div class="col-12 info-row">
                            <div class="info-label">Texto de Pie de Pagina</div>
                            <div class="info-value">{{ $plantilla->pie_pagina_texto }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-card mb-4">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-toggle-off me-2 text-primary"></i>Opciones de Visualizacion</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Mostrar Logo</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_logo ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_logo ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Encabezado</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_encabezado ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_encabezado ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Pie</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_pie ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_pie ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Datos Fiscales</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_datos_fiscales ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_datos_fiscales ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Datos Cliente</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_datos_cliente ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_datos_cliente ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Cant.</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_columna_cantidad ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_columna_cantidad ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">P. Unit.</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_columna_precio ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_columna_precio ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Subtotal</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_columna_subtotal ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_columna_subtotal ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">ITBIS</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_columna_itbis ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_columna_itbis ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Garantias</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_garantias ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_garantias ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Pagos</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_pagos ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_pagos ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <div class="info-label">Notas</div>
                                <div class="info-value"><span class="badge bg-{{ $plantilla->mostrar_notas ? 'success' : 'secondary' }} bg-opacity-10">{{ $plantilla->mostrar_notas ? 'Si' : 'No' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sucursales -->
            <div class="ui-card mb-4">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i>Sucursales Asignadas</h6>
                    @if($plantilla->sucursales->count() > 0)
                        <div>{{ $plantilla->sucursales->pluck('nombre')->join(', ') }}</div>
                    @else
                        <span class="text-muted">Global (todas las sucursales)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right column: actions + preview -->
        <div class="col-lg-4">
            <div class="ui-card mb-4">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-warning"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('plantillas.edit', $plantilla) }}" class="ui-btn ui-btn-ghost btn-sm">
                            <i class="bi bi-pencil me-2"></i>Editar
                        </a>
                        <a href="{{ route('plantillas.duplicate', $plantilla) }}" class="ui-btn ui-btn-ghost btn-sm" onclick="return confirm('¿Duplicar esta plantilla?')">
                            <i class="bi bi-file-earmark-copy me-2"></i>Duplicar
                        </a>
                        <a href="{{ route('plantillas.pdf-preview', $plantilla) }}" target="_blank" class="ui-btn ui-btn-ghost btn-sm">
                            <i class="bi bi-eye me-2"></i>Vista Previa Web
                        </a>
                        <a href="{{ route('plantillas.pdf-preview', $plantilla) }}" target="_blank" class="ui-btn ui-btn-solid btn-sm">
                            <i class="bi bi-download me-2"></i>Descargar PDF
                        </a>
                        @if(!$plantilla->es_default)
                        <form action="{{ route('plantillas.set-default', $plantilla) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Establecer como predeterminada?')">
                            @csrf
                            <button type="submit" class="ui-btn btn-sm w-100" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff;">
                                <i class="bi bi-star me-2"></i>Establecer como Predeterminada
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('plantillas.toggle', $plantilla) }}" class="ui-btn btn-sm w-100 {{ $plantilla->activo ? 'ui-btn-ghost text-danger' : 'ui-btn-solid text-success' }}" style="font-size: 0.85rem;" onclick="return confirm('¿{{ $plantilla->activo ? 'Desactivar' : 'Activar' }} esta plantilla?')">
                            <i class="bi bi-{{ $plantilla->activo ? 'power' : 'check-lg' }} me-2"></i>{{ $plantilla->activo ? 'Desactivar' : 'Activar' }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card mb-4">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-muted"></i>Metadata</h6>
                    <div class="info-row">
                        <div class="info-label">Creada</div>
                        <div class="info-value small">{{ $plantilla->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Modificada</div>
                        <div class="info-value small">{{ $plantilla->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
