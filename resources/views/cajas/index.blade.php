@extends('layouts.app')

@section('title', 'Gestión de Cajas')

@section('content')
<div class="container-fluid px-4">
    <style>
        .caja-stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 4px 12px rgba(15,23,42,0.04);
            transition: all 0.3s;
        }
        .caja-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,23,42,0.08); }
        .caja-stat-card .icon-bubble {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        body.dark-mode .caja-stat-card { background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.9)); }

        .caja-card {
            background: var(--card-bg, white);
            border-radius: 20px;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 4px 12px rgba(15,23,42,0.04);
            transition: all 0.3s;
            overflow: hidden;
            position: relative;
        }
        .caja-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(15,23,42,0.10); }
        body.dark-mode .caja-card { background: rgba(30,41,59,0.95); border-color: rgba(255,255,255,0.05); }

        .caja-card-header {
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .caja-card-header.abierta { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .caja-card-header.cerrada { background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; }
        .caja-card-header.inactiva { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: white; opacity: 0.7; }
        .caja-card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pill.abierta { background: rgba(255,255,255,0.25); color: white; }
        .status-pill.cerrada { background: rgba(0,0,0,0.2); color: white; }
        .status-pill.inactiva { background: rgba(239,68,68,0.3); color: white; }

        .caja-stat-mini {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            font-size: 0.85rem;
            color: var(--bs-secondary, #64748b);
        }
        .caja-stat-mini strong { color: var(--bs-dark, #0f172a); font-weight: 700; }
        body.dark-mode .caja-stat-mini { color: rgba(255,255,255,0.7); }
        body.dark-mode .caja-stat-mini strong { color: white; }

        .caja-actions {
            display: flex;
            gap: 6px;
            padding: 0.75rem 1.5rem;
            background: rgba(15,23,42,0.02);
            border-top: 1px solid rgba(15,23,42,0.06);
        }
        body.dark-mode .caja-actions { background: rgba(255,255,255,0.02); border-top-color: rgba(255,255,255,0.05); }

        .caja-action-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .caja-action-btn:hover { transform: translateY(-1px); }
        .caja-action-btn.edit { background: rgba(56,189,248,0.1); color: #0284c7; border-color: rgba(56,189,248,0.2); }
        .caja-action-btn.edit:hover { background: rgba(56,189,248,0.2); }
        .caja-action-btn.toggle { background: rgba(168,85,247,0.1); color: #7c3aed; border-color: rgba(168,85,247,0.2); }
        .caja-action-btn.toggle:hover { background: rgba(168,85,247,0.2); }
        .caja-action-btn.delete { background: rgba(239,68,68,0.1); color: #dc2626; border-color: rgba(239,68,68,0.2); }
        .caja-action-btn.delete:hover { background: rgba(239,68,68,0.2); }
        .caja-action-btn.quick-edit { background: rgba(245,158,11,0.1); color: #d97706; border-color: rgba(245,158,11,0.2); }
        .caja-action-btn.quick-edit:hover { background: rgba(245,158,11,0.2); }
    </style>

    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1 fw-bold"><i class="bi bi-cash-register text-primary me-2"></i>Cajas y Turnos</h2>
            <p class="text-muted mb-0">Administra múltiples cajas registradoras. Cada cajero abre su propia caja al iniciar el turno.</p>
        </div>
        <a href="{{ route('cajas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Nueva Caja
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #198754 !important;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($sesionActivaUsuario)
        <div class="alert rounded-4 shadow-sm border-0 mb-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, rgba(34,197,94,0.1), rgba(56,189,248,0.1)); border-left: 4px solid #22c55e !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-success bg-opacity-25 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <div class="small text-muted">Sesión activa</div>
                    <strong>{{ $sesionActivaUsuario->caja->nombre }}</strong>
                    @if($sesionActivaUsuario->caja->codigo)
                        <span class="badge bg-dark ms-1">{{ $sesionActivaUsuario->caja->codigo }}</span>
                    @endif
                    <span class="text-muted small ms-2">desde {{ $sesionActivaUsuario->fecha_apertura->format('h:i A') }}</span>
                </div>
            </div>
            <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-cart-plus me-1"></i>Ir al POS
            </a>
        </div>
    @endif

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="caja-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-cash-register"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total</div>
                        <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="caja-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Abiertas</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['abiertas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="caja-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-stop-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cerradas</div>
                        <div class="fs-3 fw-bold">{{ $stats['cerradas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="caja-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-pause-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Inactivas</div>
                        <div class="fs-3 fw-bold text-warning">{{ $stats['inactivas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Caja Cards -->
    <div class="row g-4">
        @foreach($cajasConStats as $caja)
            @php
                $sesionActiva = $caja->sesionActiva();
                $isMySession = $sesionActiva && $sesionActiva->user_id == auth()->id();
                $esAdmin = auth()->user()->role === 'admin';
                $estadoClass = !$caja->activo ? 'inactiva' : $caja->estado;
            @endphp
            <div class="col-lg-4 col-md-6" data-card-id="{{ $caja->id }}">
                <div class="caja-card h-100">
                    <!-- Header con gradiente -->
                    <div class="caja-card-header {{ $estadoClass }}">
                        <div class="d-flex justify-content-between align-items-start position-relative" style="z-index: 2;">
                            <div>
                                <div class="small opacity-75 fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                                    @if($caja->codigo){{ $caja->codigo }}@else C{{ str_pad($caja->id, 2, '0', STR_PAD_LEFT) }}@endif
                                </div>
                                <h4 class="fw-bold mb-0 text-white">{{ $caja->nombre }}</h4>
                            </div>
                            <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.4;"></i>
                        </div>
                        <div class="mt-3 position-relative" style="z-index: 2;">
                            @if(! $caja->activo)
                                <span class="status-pill inactiva"><i class="bi bi-pause-fill"></i>INACTIVA</span>
                            @elseif($caja->estado == 'abierta')
                                <span class="status-pill abierta"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>ABIERTA</span>
                            @else
                                <span class="status-pill cerrada"><i class="bi bi-circle"></i>CERRADA</span>
                            @endif
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        @if($caja->ubicacion)
                            <div class="caja-stat-mini mb-2">
                                <i class="bi bi-geo-alt text-muted"></i>
                                <span>{{ $caja->ubicacion }}</span>
                            </div>
                        @endif
                        @if($caja->sucursal)
                            <div class="caja-stat-mini mb-2">
                                <i class="bi bi-building text-muted"></i>
                                <span>{{ $caja->sucursal->nombre }}</span>
                            </div>
                        @endif

                        @if($sesionActiva)
                            <div class="p-2 rounded-3 mb-3" style="background: rgba(34,197,94,0.08); border-left: 3px solid #22c55e;">
                                <div class="small fw-bold text-success mb-1">
                                    <i class="bi bi-person-circle me-1"></i>{{ $sesionActiva->user->name }}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $sesionActiva->fecha_apertura->format('h:i A') }}
                                    · Fondo: <strong>RD$ {{ number_format($sesionActiva->monto_inicial, 0) }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="caja-stat-mini">
                            <i class="bi bi-graph-up text-primary"></i>
                            <span>Ventas históricas: <strong>RD$ {{ number_format($caja->ventas_historico, 0) }}</strong></span>
                        </div>
                        <div class="caja-stat-mini">
                            <i class="bi bi-clock-history text-info"></i>
                            <span>Total de turnos: <strong>{{ $caja->total_sesiones }}</strong></span>
                        </div>
                        @if($caja->ultima_sesion)
                            <div class="caja-stat-mini">
                                <i class="bi bi-calendar text-muted"></i>
                                <span>Última: <strong>{{ $caja->ultima_sesion->created_at->diffForHumans() }}</strong></span>
                            </div>
                        @endif

                        <!-- Acciones según estado -->
                        <div class="mt-3">
                            @if($caja->estado == 'abierta' && $isMySession)
                                <div class="d-grid gap-2">
                                    <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill fw-bold">
                                        <i class="bi bi-cart-plus me-1"></i>IR AL POS
                                    </a>
                                    <a href="{{ route('cajas.cierre', $caja->id) }}" class="btn btn-warning rounded-pill fw-bold">
                                        <i class="bi bi-lock me-1"></i>CERRAR TURNO
                                    </a>
                                </div>
                            @elseif($caja->estado == 'abierta')
                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                    <i class="bi bi-lock-fill me-1"></i>EN USO POR OTRO CAJERO
                                </button>
                            @elseif($caja->activo)
                                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalAbrir{{ $caja->id }}">
                                    <i class="bi bi-play-circle me-1"></i>ABRIR CAJA
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                    <i class="bi bi-pause-circle me-1"></i>CAJA INACTIVA
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones Admin -->
                    @if($esAdmin)
                        <div class="caja-actions">
                            <button type="button" class="caja-action-btn quick-edit" title="Edición rápida"
                                    data-bs-toggle="modal" data-bs-target="#modalQuickEdit"
                                    data-id="{{ $caja->id }}"
                                    data-nombre="{{ $caja->nombre }}"
                                    data-codigo="{{ $caja->codigo }}"
                                    data-ubicacion="{{ $caja->ubicacion }}"
                                    data-activo="{{ $caja->activo ? '1' : '0' }}">
                                <i class="bi bi-lightning-charge-fill"></i> Rápida
                            </button>
                            <a href="{{ route('cajas.edit', $caja->id) }}" class="caja-action-btn edit" title="Edición completa">
                                <i class="bi bi-pencil-square"></i> Completa
                            </a>
                            <form action="{{ route('cajas.destroy', $caja->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('¿Eliminar la caja {{ $caja->nombre }}? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="caja-action-btn delete w-100" {{ $caja->estado == 'abierta' ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Abrir Caja -->
            @if($caja->activo && $caja->estado == 'cerrada')
            <div class="modal fade" id="modalAbrir{{ $caja->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form action="{{ route('cajas.abrir', $caja->id) }}" method="POST">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <h5 class="fw-bold"><i class="bi bi-play-circle text-primary me-2"></i>Abrir Caja</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 text-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="bi bi-cash-register" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-1">{{ $caja->nombre }}</h5>
                                @if($caja->codigo)<span class="badge bg-dark mb-3">{{ $caja->codigo }}</span>@endif
                                <p class="text-muted small mb-3">Indica el fondo inicial (efectivo en la gaveta al iniciar el turno)</p>
                                <label class="form-label small fw-bold text-muted text-uppercase">Fondo Inicial</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-0 fw-bold">RD$</span>
                                    <input type="number" name="monto_inicial" class="form-control bg-light border-0 fw-bold" value="0" min="0" step="0.01" required autofocus>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                    <i class="bi bi-play-fill me-1"></i>Abrir Caja
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>

<!-- Modal Edición Rápida -->
<div class="modal fade" id="modalQuickEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="quickEditForm">
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill me-2"></i>Edición Rápida</h5>
                        <small class="opacity-75">Modifica los datos básicos sin recargar la página</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="qe-id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag-fill text-warning"></i></span>
                            <input type="text" id="qe-nombre" name="nombre" class="form-control border-start-0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Código</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc text-warning"></i></span>
                            <input type="text" id="qe-codigo" name="codigo" class="form-control border-start-0" placeholder="C01, C02...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Ubicación</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt-fill text-warning"></i></span>
                            <input type="text" id="qe-ubicacion" name="ubicacion" class="form-control border-start-0" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="p-2 rounded-3 d-flex align-items-center gap-2 mb-2" style="background: rgba(34,197,94,0.08);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="qe-activo" name="activo" value="1">
                        </div>
                        <label class="form-check-label fw-bold mb-0" for="qe-activo">Caja activa</label>
                    </div>
                    <div id="qe-error" class="alert alert-danger rounded-3 d-none small mb-0"></div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark" id="qe-submit">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($esAdmin)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalQuickEdit');
    const form = document.getElementById('quickEditForm');
    const errorBox = document.getElementById('qe-error');
    const submitBtn = document.getElementById('qe-submit');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    modal.addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        const id = btn.dataset.id;
        const nombre = btn.dataset.nombre;
        const codigo = btn.dataset.codigo || '';
        const ubicacion = btn.dataset.ubicacion || '';
        const activo = btn.dataset.activo === '1';

        document.getElementById('qe-id').value = id;
        document.getElementById('qe-nombre').value = nombre;
        document.getElementById('qe-codigo').value = codigo;
        document.getElementById('qe-ubicacion').value = ubicacion;
        document.getElementById('qe-activo').checked = activo;
        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        // Header muestra a qué caja se le edita
        const header = modal.querySelector('.modal-header small');
        if (header) header.textContent = 'Editando: ' + nombre;

        setTimeout(() => document.getElementById('qe-nombre').focus(), 300);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('qe-id').value;
        const data = new FormData();
        data.append('_token', csrfToken);
        data.append('_method', 'PUT');
        data.append('nombre', document.getElementById('qe-nombre').value.trim());
        data.append('codigo', document.getElementById('qe-codigo').value.trim());
        data.append('ubicacion', document.getElementById('qe-ubicacion').value.trim());
        if (document.getElementById('qe-activo').checked) {
            data.append('activo', '1');
        }

        const nombre = document.getElementById('qe-nombre').value.trim();
        if (!nombre) {
            errorBox.textContent = 'El nombre es obligatorio.';
            errorBox.classList.remove('d-none');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
        errorBox.classList.add('d-none');

        try {
            const resp = await fetch(`/cajas/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: data,
            });

            const result = await resp.json();

            if (!resp.ok) {
                let msg = 'Error al guardar.';
                if (result.errors) {
                    msg = Object.values(result.errors).flat().join(' ');
                } else if (result.message) {
                    msg = result.message;
                }
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
                return;
            }

            // Éxito: actualizar la card en el DOM sin recargar
            const card = document.querySelector(`[data-card-id="${id}"]`);
            if (card && result.caja) {
                const c = result.caja;
                const h4 = card.querySelector('.caja-card-header h4');
                if (h4) h4.textContent = c.nombre;
                const codeEl = card.querySelector('.caja-card-header .small');
                if (codeEl) codeEl.textContent = c.codigo || ('C' + String(c.id).padStart(2, '0'));
                const locLine = card.querySelector('.caja-stat-mini .bi-geo-alt')?.parentElement;
                if (locLine) {
                    if (c.ubicacion) {
                        locLine.querySelector('span').textContent = c.ubicacion;
                        locLine.style.display = '';
                    } else {
                        locLine.style.display = 'none';
                    }
                }
                const qeBtn = card.querySelector('.quick-edit');
                if (qeBtn) {
                    qeBtn.dataset.nombre = c.nombre;
                    qeBtn.dataset.codigo = c.codigo || '';
                    qeBtn.dataset.ubicacion = c.ubicacion || '';
                    qeBtn.dataset.activo = c.activo ? '1' : '0';
                }
                card.style.transition = 'box-shadow 0.4s';
                card.style.boxShadow = '0 0 0 4px rgba(245,158,11,0.4)';
                setTimeout(() => card.style.boxShadow = '', 800);
            }

            bootstrap.Modal.getInstance(modal).hide();
            showToast('Caja actualizada correctamente', 'success');
        } catch (err) {
            errorBox.textContent = 'Error de red: ' + err.message;
            errorBox.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardar Cambios';
        }
    });

    function showToast(msg, type) {
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-white border-0 bg-${type}" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-bold">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1200';
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
});
</script>
@endif
@endsection
