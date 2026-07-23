@extends('layouts.app')
@section('title', 'Configuración Inicial')
@push('styles')
<style>
:root {
    --wz-accent: #6366f1;
    --wz-accent-rgb: 99, 102, 241;
    --wz-accent-hover: #4f46e5;
    --wz-accent-light: #eef2ff;
    --wz-radius: .75rem;
    --wz-radius-lg: 1rem;
    --wz-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --wz-shadow-lg: 0 10px 40px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.04);
}

/* ==============================
   STEPPER
   ============================== */
.wz-stepper-card {
    background: #fff;
    border-radius: var(--wz-radius-lg);
    box-shadow: var(--wz-shadow);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,.04);
}
body.dark-mode .wz-stepper-card {
    background: rgba(15,23,42,.6);
    border-color: rgba(255,255,255,.06);
}
.wz-stepper {
    display: flex;
    gap: 0;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
}
.wz-stepper::-webkit-scrollbar { height: 3px; }
.wz-stepper::-webkit-scrollbar-thumb { background: rgba(var(--wz-accent-rgb),.2); border-radius: 4px; }
.wz-step {
    flex: 1;
    min-width: 80px;
    text-align: center;
    position: relative;
    padding-bottom: .35rem;
}
.wz-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 18px;
    left: calc(50% + 16px);
    width: calc(100% - 32px);
    height: 3px;
    background: #e2e8f0;
    z-index: 0;
    border-radius: 2px;
    transition: background .4s ease;
}
body.dark-mode .wz-step:not(:last-child)::after { background: #334155; }
.wz-step.completed:not(:last-child)::after { background: var(--wz-accent); }
.wz-step.current:not(:last-child)::after { background: var(--wz-accent); }
.wz-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    font-size: .75rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
    transition: all .3s ease;
}
.wz-step.completed .wz-circle {
    background: var(--wz-accent);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--wz-accent-rgb),.3);
}
.wz-step.current .wz-circle {
    background: var(--wz-accent);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(var(--wz-accent-rgb),.12);
}
.wz-step.pending .wz-circle {
    background: #f1f5f9;
    color: #94a3b8;
    border: 2px solid #e2e8f0;
}
body.dark-mode .wz-step.pending .wz-circle {
    background: #1e293b;
    border-color: #334155;
    color: #64748b;
}
.wz-label {
    font-size: .6rem;
    display: block;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
    letter-spacing: .01em;
    transition: color .3s ease;
}
.wz-step.current .wz-label { color: var(--wz-accent); font-weight: 700; }
.wz-step.completed .wz-label { color: var(--wz-accent); font-weight: 600; }

/* ==============================
   CARDS
   ============================== */
.wz-card {
    background: #fff;
    border-radius: var(--wz-radius-lg);
    box-shadow: var(--wz-shadow-lg);
    border: none;
    overflow: hidden;
}
body.dark-mode .wz-card {
    background: rgba(15,23,42,.7);
    backdrop-filter: blur(16px);
}
.wz-card-accent {
    height: 4px;
    background: linear-gradient(90deg, var(--wz-accent), #818cf8);
}
.wz-card-body {
    padding: 1.75rem 2rem;
}
@media (max-width: 575.98px) {
    .wz-card-body { padding: 1.25rem 1.25rem; }
}

/* ==============================
   HEADER
   ============================== */
.wz-header {
    padding: 1.25rem 1.5rem;
    border-radius: var(--wz-radius-lg);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    box-shadow: var(--wz-shadow);
    border: 1px solid rgba(0,0,0,.04);
}
body.dark-mode .wz-header {
    background: rgba(15,23,42,.5);
    border-color: rgba(255,255,255,.06);
}
.wz-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(var(--wz-accent-rgb),.1);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.wz-header-icon i { font-size: 1.4rem; color: var(--wz-accent); }
.wz-header-body { flex: 1; min-width: 200px; }
.wz-header-title { font-weight: 700; margin-bottom: .1rem; font-size: 1.1rem; }
.wz-header-meta { font-size: .82rem; color: #64748b; }
body.dark-mode .wz-header-meta { color: #94a3b8; }
.wz-header-actions { flex-shrink: 0; }

/* ==============================
   BUTTONS
   ============================== */
.wz-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .5rem 1.25rem;
    border-radius: .5rem;
    font-weight: 600;
    font-size: .875rem;
    transition: all .2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.wz-btn-primary {
    background: var(--wz-accent);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--wz-accent-rgb),.25);
}
.wz-btn-primary:hover { background: var(--wz-accent-hover); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(var(--wz-accent-rgb),.35); }
.wz-btn-success {
    background: #10b981;
    color: #fff;
    box-shadow: 0 2px 8px rgba(16,185,129,.25);
}
.wz-btn-success:hover { background: #059669; color: #fff; transform: translateY(-1px); }
.wz-btn-outline {
    background: transparent;
    color: #475569;
    border: 1.5px solid #e2e8f0;
}
body.dark-mode .wz-btn-outline { color: #cbd5e1; border-color: #334155; }
.wz-btn-outline:hover { border-color: var(--wz-accent); color: var(--wz-accent); background: rgba(var(--wz-accent-rgb),.04); }
.wz-btn-sm { padding: .35rem .85rem; font-size: .8rem; }
.wz-btn-lg { padding: .65rem 1.75rem; font-size: .95rem; }

/* ==============================
   SUCCESS
   ============================== */
.wz-success-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #ecfdf5;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}
body.dark-mode .wz-success-icon { background: rgba(16,185,129,.12); }
.wz-success-icon i { font-size: 2.2rem; color: #10b981; }
.wz-check-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .55rem .85rem;
    border-radius: .5rem;
    margin-bottom: .25rem;
    background: #f8fafc;
    transition: background .2s ease;
}
body.dark-mode .wz-check-item { background: rgba(30,41,59,.5); }
.wz-check-item:hover { background: #f1f5f9; }
body.dark-mode .wz-check-item:hover { background: rgba(51,65,85,.5); }

/* ==============================
   ALERTS
   ============================== */
.wz-alert { border-radius: var(--wz-radius); border: none; padding: .85rem 1.1rem; display: flex; align-items: center; gap: .65rem; font-weight: 500; }
.wz-alert i { font-size: 1.1rem; flex-shrink: 0; }
.wz-alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
body.dark-mode .wz-alert-success { background: rgba(16,185,129,.1); border-color: rgba(16,185,129,.2); color: #6ee7b7; }
.wz-alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
body.dark-mode .wz-alert-danger { background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.2); color: #fca5a5; }

/* ==============================
   UI COMPONENTES (para _step-* partials)
   ============================== */
.ui-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: .15rem;
    color: #1e293b;
}
.ui-card-title i { margin-right: .5rem; color: var(--wz-accent); }
body.dark-mode .ui-card-title { color: #f1f5f9; }
.ui-card-subtitle {
    font-size: .85rem;
    color: #64748b;
    margin-bottom: 1.25rem;
}
body.dark-mode .ui-card-subtitle { color: #94a3b8; }

.ui-label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    margin-bottom: .35rem;
    color: #334155;
}
body.dark-mode .ui-label { color: #cbd5e1; }

.ui-input,
.ui-select,
.ui-textarea {
    width: 100%;
    padding: .55rem .85rem;
    font-size: .875rem;
    border-radius: .5rem;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #1e293b;
    transition: all .2s ease;
    outline: none;
}
body.dark-mode .ui-input,
body.dark-mode .ui-select,
body.dark-mode .ui-textarea {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
.ui-input:focus,
.ui-select:focus,
.ui-textarea:focus {
    border-color: var(--wz-accent);
    box-shadow: 0 0 0 3px rgba(var(--wz-accent-rgb),.12);
}
.ui-input::placeholder,
.ui-textarea::placeholder { color: #94a3b8; }
body.dark-mode .ui-input::placeholder,
body.dark-mode .ui-textarea::placeholder { color: #64748b; }
.ui-textarea { min-height: 90px; resize: vertical; }

.ui-input-group {
    display: flex;
    align-items: stretch;
    border-radius: .5rem;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
    transition: border-color .2s ease;
}
body.dark-mode .ui-input-group { border-color: #334155; }
.ui-input-group:focus-within {
    border-color: var(--wz-accent);
    box-shadow: 0 0 0 3px rgba(var(--wz-accent-rgb),.12);
}
.ui-input-group .ui-input,
.ui-input-group .ui-select {
    border: none;
    border-radius: 0;
    flex: 1;
}
.ui-input-group .ui-input:focus,
.ui-input-group .ui-select:focus { box-shadow: none; }
.ui-input-group-text {
    display: flex;
    align-items: center;
    padding: .55rem .85rem;
    font-size: .875rem;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    white-space: nowrap;
}
body.dark-mode .ui-input-group-text {
    color: #94a3b8;
    background: rgba(30,41,59,.5);
}

.ui-sticky-bar {
    position: sticky;
    bottom: 0;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(12px);
    padding: 1rem 0 0;
    margin-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
    z-index: 10;
}
body.dark-mode .ui-sticky-bar {
    background: rgba(15,23,42,.85);
    border-color: rgba(255,255,255,.06);
}
.ui-sticky-bar-inner {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    align-items: center;
}

.ui-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem 1.25rem;
    border-radius: .5rem;
    font-weight: 600;
    font-size: .875rem;
    transition: all .2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.ui-btn-solid {
    background: var(--wz-accent);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--wz-accent-rgb),.25);
}
.ui-btn-solid:hover { background: var(--wz-accent-hover); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(var(--wz-accent-rgb),.35); }
.ui-btn-ghost {
    background: transparent;
    color: #475569;
    border: 1.5px solid #e2e8f0;
}
body.dark-mode .ui-btn-ghost { color: #cbd5e1; border-color: #334155; }
.ui-btn-ghost:hover { border-color: var(--wz-accent); color: var(--wz-accent); background: rgba(var(--wz-accent-rgb),.04); }
.ui-btn-pill { border-radius: 999px; }

/* ==============================
   RESPONSIVE
   ============================== */
@media (max-width: 575.98px) {
    .wz-step { min-width: 60px; }
    .wz-circle { width: 30px; height: 30px; font-size: .7rem; }
    .wz-label { font-size: .55rem; }
    .wz-stepper-card { padding: .85rem .85rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if(session('setup_completed') || (Auth::user()->businessInstance->setup_completed ?? false))
        {{-- ========== COMPLETADO ========== --}}
        <div class="wz-header">
            <div class="wz-header-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="wz-header-body">
                <div class="wz-header-title">¡Todo listo para facturar!</div>
                <div class="wz-header-meta">La instancia ha sido configurada correctamente</div>
            </div>
            <div class="wz-header-actions">
                <a href="{{ route('dashboard') }}" class="wz-btn wz-btn-primary wz-btn-sm">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="wz-card">
                    <div class="wz-card-accent"></div>
                    <div class="wz-card-body text-center py-4">
                        <div class="wz-success-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Configuración completada</h5>
                        <p class="text-muted mb-4">Tu sistema está preparado para comenzar a operar</p>

                        <div class="text-start mb-4 mx-auto" style="max-width:440px;">
                            @foreach($steps as $step)
                                @if($step['completed'])
                                    <div class="wz-check-item">
                                        <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                                        <span>{{ $step['label'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('setup.abrir-caja') }}" class="wz-btn wz-btn-success wz-btn-lg">
                                <i class="bi bi-cash-stack"></i> Abrir Caja y Vender
                            </a>
                            <a href="{{ route('ncf.index') }}" class="wz-btn wz-btn-outline wz-btn-lg">
                                <i class="bi bi-receipt-cutoff"></i> Configurar NCF
                            </a>
                        </div>

                        <hr class="my-4" style="opacity:.5;">
                        <a href="#" class="text-decoration-none small text-muted"
                           onclick="event.preventDefault(); confirmAction({title:'Reiniciar Asistente', text:'¿Reiniciar la configuración inicial? Se marcarán como pendientes los pasos incompletos.', icon:'warning', color:'#f59e0b', confirmText:'Reiniciar', onSubmit:function(){ var f=document.createElement('form');f.method='POST';f.action='{{ route('setup.restart') }}';f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @elseif(!$current)
        {{-- ========== TODOS COMPLETADOS ========== --}}
        <div class="wz-header">
            <div class="wz-header-icon"><i class="bi bi-check2-all"></i></div>
            <div class="wz-header-body">
                <div class="wz-header-title">Todos los pasos completados</div>
                <div class="wz-header-meta">Todos los datos requeridos ya existen</div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="wz-card">
                    <div class="wz-card-accent"></div>
                    <div class="wz-card-body text-center py-4">
                        <div class="wz-success-icon">
                            <i class="bi bi-check2-all" style="color:var(--wz-accent);"></i>
                        </div>
                        <p class="text-muted mb-4">Marca la configuración como completada y empieza a trabajar.</p>
                        <form action="{{ route('setup.complete') }}" method="POST">
                            @csrf
                            <button type="submit" class="wz-btn wz-btn-primary wz-btn-lg px-5">
                                <i class="bi bi-check-lg"></i> Marcar como Completado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- ========== WIZARD EN PROGRESO ========== --}}
        @if(session('error'))
            <div class="wz-alert wz-alert-danger mb-4">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="wz-alert wz-alert-success mb-4">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stepper --}}
        <div class="wz-stepper-card">
            <div class="wz-stepper">
                @foreach($steps as $i => $step)
                    <div class="wz-step {{ $step['completed'] ? 'completed' : ($current && $step['key'] === $current['key'] ? 'current' : 'pending') }}">
                        <div class="wz-circle">
                            @if($step['completed'])
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="wz-label">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Current step --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="wz-card">
                    <div class="wz-card-accent"></div>
                    <div class="wz-card-body">
                        @include("setup._step-{$current['key']}", ['step' => $current])
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection