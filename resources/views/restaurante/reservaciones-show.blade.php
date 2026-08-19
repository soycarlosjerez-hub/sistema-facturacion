@extends('layouts.app')

@section('title', 'Detalle de Reservación')

@push('styles')
@include('partials.premium-ui')
<style>
    /* ============================================================
       DETALLE DE RESERVACIÓN — Vista Show
       ============================================================ */
    .reservacion-header-card {
        background: rgba(255,255,255,.75);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-2xl);
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: uiSlideUp .5s ease both;
    }
    .reservacion-header-card .ribbon {
        height: 6px;
        background: linear-gradient(90deg, var(--accent, #10b981), rgba(255,255,255,.3));
    }
    .reservacion-header-body {
        padding: 1.75rem;
    }
    .reservacion-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .reservacion-actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }
    .reservacion-actions .ui-btn {
        gap: .4rem;
    }

    /* Mesa visual card */
    .mesa-show-card {
        background: rgba(255,255,255,.7);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-2xl);
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        transition: all .3s ease;
        animation: uiSlideUp .5s ease both;
        animation-delay: .1s;
    }
    .mesa-show-card:hover {
        box-shadow: 0 12px 48px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }
    .mesa-show-number {
        width: 72px; height: 72px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 800; color: #fff;
        background: linear-gradient(135deg, var(--accent, #10b981), var(--accent-hover, #059669));
        box-shadow: 0 4px 16px rgba(var(--accent-rgb, 16,185,129), .3);
    }
    .mesa-show-info {
        padding: .5rem 0;
    }
    .mesa-show-name {
        font-weight: 700; font-size: 1.1rem; color: #1e293b; margin: 0;
    }
    .mesa-show-cap {
        color: #64748b; font-size: .85rem; margin: 0;
    }

    /* Info detail groups */
    .info-detail-card {
        background: rgba(255,255,255,.7);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-2xl);
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: uiSlideUp .5s ease both;
        animation-delay: .15s;
    }
    .info-detail-card:hover {
        box-shadow: 0 12px 48px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }
    .info-detail-title {
        font-weight: 700; font-size: 1rem; color: #1e293b;
        padding: 1.25rem 1.5rem .25rem; display: flex; align-items: center; gap: .6rem;
        margin: 0;
    }
    .info-detail-title i { color: var(--accent, #10b981); font-size: 1.15rem; }
    .info-detail-body { padding: 0 1.5rem 1.25rem; }

    .field-row {
        display: flex;
        align-items: baseline;
        padding: .6rem 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 1rem;
    }
    .field-row:last-child { border-bottom: none; }
    .field-label {
        font-weight: 600; font-size: .82rem; color: #64748b;
        text-transform: uppercase; letter-spacing: .4px;
        min-width: 120px; flex-shrink: 0;
        display: flex; align-items: center; gap: .4rem;
    }
    .field-label i { font-size: .9rem; }
    .field-value {
        font-size: .92rem; color: #1e293b; flex: 1;
    }
    .field-value a { color: var(--accent, #10b981); text-decoration: none; font-weight: 600; }
    .field-value a:hover { text-decoration: underline; color: var(--accent-hover, #059669); }

    .notas-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius);
        padding: .85rem 1rem;
        font-size: .9rem;
        color: #334155;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    /* Timestamps */
    .timestamp-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .55rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .timestamp-item:last-child { border-bottom: none; }
    .timestamp-icon {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0;
    }
    .timestamp-icon.created { background: rgba(34,197,94,.1); color: #16a34a; }
    .timestamp-icon.modified { background: rgba(59,130,246,.1); color: #3b82f6; }
    .timestamp-timeline { color: #475569; font-size: .82rem; }
    .timestamp-meta { font-size: .75rem; color: #94a3b8; }

    /* Dark mode */
    body.dark-mode .reservacion-header-card {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
    }
    body.dark-mode .mesa-show-card {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
    }
    body.dark-mode .info-detail-card {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
    }
    body.dark-mode .mesa-show-name,
    body.dark-mode .info-detail-title,
    body.dark-mode .timestamp-timeline { color: #f1f5f9; }
    body.dark-mode .mesa-show-cap,
    body.dark-mode .info-detail-body { color: #94a3b8; }
    body.dark-mode .field-row { border-bottom-color: #1e293b; }
    body.dark-mode .timestamp-item { border-bottom-color: #1e293b; }
    body.dark-mode .field-value { color: #cbd5e1; }
    body.dark-mode .notas-box { background: rgba(30,41,59,.6); border-color: #334155; color: #cbd5e1; }
    body.dark-mode .timestamp-meta { color: #64748b; }
    body.dark-mode .timestamp-icon.created { background: rgba(34,197,94,.15); color: #4ade80; }
    body.dark-mode .timestamp-icon.modified { background: rgba(59,130,246,.15); color: #60a5fa; }

    @media (max-width: 767.98px) {
        .reservacion-header-body { padding: 1.25rem; }
        .mesa-show-card, .info-detail-card .ui-card-body { padding: 1.25rem; }
        .field-row { flex-direction: column; gap: .2rem; }
        .field-label { min-width: auto; }
    }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    {{-- Header premium --}}
    <div class="reservacion-header-card mb-4">
        <div class="ribbon"></div>
        <div class="reservacion-header-body">
            <div class="reservacion-header-right">
                <div>
                    <nav aria-label="breadcrumb mb-1">
                        <ol class="breadcrumb mb-0" style="font-size:.78rem;">
                            <li class="breadcrumb-item"><a href="{{ route('restaurante.reservaciones.index') }}" style="color:var(--accent);">Reservaciones</a></li>
                            <li class="breadcrumb-item active">{{ $reservacion->cliente_nombre }}</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0 fw-bold" style="color:#1e293b; font-size:1.3rem;">
                        <i class="bi bi-calendar-check me-2" style="color:var(--accent);"></i>Reservación #{{ $reservacion->id }}
                    </h3>
                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size:.82rem; color:#64748b;">
                        <i class="bi bi-clock"></i>
                        {{ $reservacion->fecha_hora->format('d \d\e F \d\e Y \a \l\a\s h:i A') }}
                    </div>
                </div>
                <div class="reservacion-actions ms-auto">
                    {{-- Botón Editar --}}
                    <a href="{{ route('restaurante.reservaciones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <button type="button" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#reservaModal">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                    {{-- Cambios de estado según estado actual --}}
                    @if($reservacion->estado === 'pendiente')
                        <button type="button" class="ui-btn ui-btn-sm rounded-pill" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;" onclick="cambiarEstado({{ $reservacion->id }}, 'confirmada')">
                            <i class="bi bi-check-circle"></i> Confirmar
                        </button>
                    @endif
                    @if($reservacion->estado === 'confirmada')
                        <button type="button" class="ui-btn ui-btn-sm rounded-pill" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;" onclick="cambiarEstado({{ $reservacion->id }}, 'cumplida')">
                            <i class="bi bi-check-all"></i> Marcar Cumplida
                        </button>
                    @endif
                    @if($reservacion->estado !== 'cancelada' && $reservacion->estado !== 'cumplida')
                        <button type="button" class="ui-btn ui-btn-sm rounded-pill ui-btn-danger" onclick="cambiarEstado({{ $reservacion->id }}, 'cancelada')">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                    @endif
                    <button type="button" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill" onclick="eliminarReservacion()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid principal --}}
    <div class="row g-4 mb-4">
        {{-- Columna Izq: Mesa --}}
        <div class="col-lg-4 col-md-6">
            <div class="mesa-show-card">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="mesa-show-number">
                            {{ $reservacion->mesa->numero ?? '#' }}
                        </div>
                        <div class="mesa-show-info flex-grow-1">
                            <p class="mesa-show-name">{{ $reservacion->mesa->nombre ?? 'Mesa ' . ($reservacion->mesa->numero ?? '') }}</p>
                            <p class="mesa-show-cap">
                                <i class="bi bi-people me-1"></i>Capacidad: {{ $reservacion->mesa->capacidad }} personas
                                @if($reservacion->mesa->ubicacion)
                                    · <i class="bi bi-geo-alt me-1"></i>{{ $reservacion->mesa->ubicacion->nombre }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($reservacion->mesa->categoria)
                        <div class="mt-3">
                            <span class="ui-badge" style="background:{{ $reservacion->mesa->categoria->color }}20; color:{{ $reservacion->mesa->categoria->color }}; border:1px solid {{ $reservacion->mesa->categoria->color }}40;">
                                {{ $reservacion->mesa->categoria->nombre }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Der: Cliente --}}
        <div class="col-lg-4 col-md-6">
            <div class="info-detail-card">
                <h5 class="info-detail-title"><i class="bi bi-person"></i> Cliente</h5>
                <div class="info-detail-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="ui-user-avatar ui-user-avatar-sm ui-user-avatar-green" style="font-size:.9rem; width:44px; height:44px;">
                            {{ strtoupper(substr($reservacion->cliente_nombre, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $reservacion->cliente_nombre }}</div>
                        </div>
                    </div>
                    @if($reservacion->cliente)
                        <div class="field-row">
                            <span class="field-label"><i class="bi bi-link-45deg"></i> Perfil</span>
                            <span class="field-value">
                                <a href="{{ route('clientes.show', $reservacion->cliente) }}">Ver perfil completo <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Centro: Estado --}}
        <div class="col-lg-4 col-md-6">
            <div class="mesa-show-card">
                <div class="ui-card-body text-center">
                    <div class="mb-3">
                        <span class="badge rounded-pill fs-6 px-4 py-2
                            {{ $reservacion->estado === 'pendiente' ? 'bg-warning' : '' }}
                            {{ $reservacion->estado === 'confirmada' ? 'bg-success' : '' }}
                            {{ $reservacion->estado === 'cumplida' ? 'bg-info text-white' : '' }}
                            {{ $reservacion->estado === 'cancelada' ? 'bg-secondary' : '' }}
                            " style="font-size:1.1rem; font-weight:700;">
                            @php
                                $iconos = [
                                    'pendiente' => '⏳',
                                    'confirmada' => '✅',
                                    'cumplida' => '🎉',
                                    'cancelada' => '❌',
                                ];
                            @endphp
                            {{ $iconos[$reservacion->estado] ?? '📋' }} {{ ucfirst($reservacion->estado) }}
                        </span>
                    </div>
                    <div class="row g-2 text-start">
                        <div class="col-6 field-row px-0 border-0 mb-0">
                            <span class="field-label"><i class="bi bi-people"></i> Personas</span>
                            <span class="field-value fw-bold" style="font-size:1.3rem;">{{ $reservacion->personas }}</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Mesa asignada</small><br>
                        <strong style="font-size:1.3rem; color:var(--accent);">
                            <i class="bi bi-table me-1"></i>Mesa {{ $reservacion->mesa->numero }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalles de contacto --}}
    <div class="info-detail-card mb-4" style="animation-delay:.2s;">
        <h5 class="info-detail-title"><i class="bi bi-telephone"></i> Detalle de Contacto</h5>
        <div class="info-detail-body">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="field-row" style="flex-direction:column; align-items:flex-start; gap:.5rem;">
                        <span class="field-label mb-0"><i class="bi bi-person-vcard"></i> Nombre</span>
                        <span class="field-value fw-semibold">{{ $reservacion->cliente_nombre }}</span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="field-row" style="flex-direction:column; align-items:flex-start; gap:.5rem;">
                        <span class="field-label mb-0"><i class="bi bi-whatsapp"></i> Teléfono</span>
                        <span class="field-value">{{ $reservacion->cliente_telefono ?: '—' }}</span>
                        @if($reservacion->cliente_telefono)
                            <div class="d-flex gap-2 mt-1">
                                <a href="tel:{{ $reservacion->cliente_telefono }}" class="ui-action ui-action-view" title="Llamar" style="width:30px;height:30px;font-size:.75rem;">
                                    <i class="bi bi-telephone"></i>
                                </a>
                                @php $cleanPhone = preg_replace('/[^0-9]/', '', $reservacion->cliente_telefono); @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Hola ' . $reservacion->cliente_nombre . ', te confirmo tu reservación para ' . $reservacion->fecha_hora->format('d/m/Y h:i A') . '.') }}" target="_blank" class="ui-action" title="Enviar WhatsApp" style="width:30px;height:30px;font-size:.75rem;background:rgba(37,211,102,.1);color:#25d366;border-color:rgba(37,211,102,.2);" onmouseover="this.style.background='#25d366';this.style.color='#fff';this.style.borderColor='#25d366';" onmouseout="this.style.background='rgba(37,211,102,.1)';this.style.color='#25d366';this.style.borderColor='rgba(37,211,102,.2)';">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="field-row" style="flex-direction:column; align-items:flex-start; gap:.5rem;">
                        <span class="field-label mb-0"><i class="bi bi-envelope"></i> Email</span>
                        <span class="field-value">{{ $reservacion->cliente_email ?: '—' }}</span>
                        @if($reservacion->cliente_email)
                            <a href="mailto:{{ $reservacion->cliente_email }}" class="mt-1" style="font-size:.78rem;">
                                <i class="bi bi-send me-1"></i>Enviar correo
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notas --}}
    @if($reservacion->notas)
    <div class="info-detail-card mb-4" style="animation-delay:.3s;">
        <h5 class="info-detail-title"><i class="bi bi-journal-text"></i> Notas</h5>
        <div class="info-detail-body">
            <div class="notas-box">{{ $reservacion->notas }}</div>
        </div>
    </div>
    @endif

    {{-- Timestamps --}}
    <div class="info-detail-card" style="animation-delay:.35s;">
        <h5 class="info-detail-title"><i class="bi bi-clock-history"></i> Histórico</h5>
        <div class="info-detail-body">
            <div class="timestamp-item">
                <div class="timestamp-icon created"><i class="bi bi-plus-circle"></i></div>
                <div>
                    <div class="timestamp-timeline">Creada por {{ $reservacion->user->name ?? 'Usuario' }}</div>
                    <div class="timestamp-meta">{{ $reservacion->created_at->format('d/m/Y h:i A') }} · {{ $reservacion->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @if($reservacion->updated_at && $reservacion->updated_at->gt($reservacion->created_at))
            <div class="timestamp-item">
                <div class="timestamp-icon modified"><i class="bi bi-pencil"></i></div>
                <div>
                    <div class="timestamp-timeline">Ultima modificación por {{ $reservacion->user->name ?? 'Usuario' }}</div>
                    <div class="timestamp-meta">{{ $reservacion->updated_at->format('d/m/Y h:i A') }} · {{ $reservacion->updated_at->diffForHumans() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal de edición --}}
<div class="modal fade" id="reservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('restaurante.reservaciones.update', $reservacion) }}" class="modal-content rounded-4 border-0 shadow" id="form-editar-reserva">
            @csrf
            @method('PUT')
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#10b981,#059669);border-radius:1rem 1rem 0 0;">
                <h5 class="fw-bold text-white"><i class="bi bi-pencil me-2"></i>Editar Reservación #{{ $reservacion->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" id="edit-mesa-id" class="ui-select" required>
                        <option value="">Seleccionar mesa</option>
                        @foreach($reservacion->mesa ? [$reservacion->mesa] : [] as $m)
                        @endforeach
                        @foreach(\App\Models\Mesa::deSucursal()->orderBy('numero')->get() as $m)
                            <option value="{{ $m->id }}" {{ $reservacion->mesa_id == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre ?? 'Mesa '.$m->numero }} (Cap. {{ $m->capacidad }}, Est. {{ $m->estado }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Nombre del cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" id="edit-cliente-nombre" class="ui-input" value="{{ $reservacion->cliente_nombre }}" required maxlength="200">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="cliente_telefono" id="edit-cliente-telefono" class="ui-input" value="{{ $reservacion->cliente_telefono }}" maxlength="30">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="cliente_email" id="edit-cliente-email" class="ui-input" value="{{ $reservacion->cliente_email }}" maxlength="200">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="ui-label">Personas <span class="text-danger">*</span></label>
                        <input type="number" name="personas" id="edit-personas" class="ui-input" value="{{ $reservacion->personas }}" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora" id="edit-fecha-hora" class="ui-input" value="{{ $reservacion->fecha_hora->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" id="edit-notas" class="ui-textarea" rows="3" maxlength="500">{{ $reservacion->notas }}</textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function cambiarEstado(id, nuevoEstado) {
    if (nuevoEstado === 'cancelada') {
        if (!confirm('¿Estás seguro de cancelar esta reservación?')) return;
    }
    if (nuevoEstado === 'cumplida') {
        if (!confirm('¿Marcar esta reservación como cumplida?')) return;
    }
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/restaurante/reservaciones/' + id + '/estado';
    form.style.display = 'none';
    var h1 = document.createElement('input'); h1.type = 'hidden'; h1.name = '_token'; h1.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var h2 = document.createElement('input'); h2.type = 'hidden'; h2.name = '_method'; h2.value = 'PATCH';
    var h3 = document.createElement('input'); h3.type = 'hidden'; h3.name = 'estado'; h3.value = nuevoEstado;
    form.append(h1, h2, h3);
    document.body.appendChild(form);
    form.submit();
}

function eliminarReservacion() {
    if (!confirm('¿Eliminar la reservación de ' + '{{ addslashes($reservacion->cliente_nombre) }}'? Esta acción no se puede deshacer.')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/restaurante/reservaciones/{{ $reservacion->id }}';
    form.style.display = 'none';
    var h1 = document.createElement('input'); h1.type = 'hidden'; h1.name = '_token'; h1.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var h2 = document.createElement('input'); h2.type = 'hidden'; h2.name = '_method'; h2.value = 'DELETE';
    form.append(h1, h2);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
