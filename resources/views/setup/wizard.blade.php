@extends('layouts.app')
@section('title', 'Configuración Inicial')

@push('styles')
@include('partials.premium-ui')
<style>
/* ============================================================
   SETUP WIZARD — Premium v2 overrides
   ============================================================ */

/* Page override for wizard */
.setup-page {
    --accent: #8b5cf6;
    --accent-rgb: 139, 92, 246;
    --accent-hover: #7c3aed;
    --accent-light: #ede9fe;
}

/* ============================================================
   STEPPER BAR — Glassmorphism horizontal
   ============================================================ */
.wizard-stepper-wrapper {
    background: rgba(255,255,255,.5);
    backdrop-filter: blur(12px);
    border-radius: var(--radius-xl);
    border: 1px solid rgba(255,255,255,.6);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}
body.dark-mode .wizard-stepper-wrapper {
    background: rgba(15,23,42,.5);
    border-color: rgba(255,255,255,.06);
}

.wizard-stepper {
    display: flex;
    gap: 0;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(139,92,246,.2) transparent;
    padding-bottom: 2px;
}
.wizard-stepper::-webkit-scrollbar { height: 4px; }
.wizard-stepper::-webkit-scrollbar-track { background: transparent; }
.wizard-stepper::-webkit-scrollbar-thumb { background: rgba(139,92,246,.25); border-radius: 4px; }

.wizard-step {
    flex: 1;
    min-width: 85px;
    text-align: center;
    position: relative;
    padding-bottom: .5rem;
}
.wizard-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 17px;
    left: calc(50% + 14px);
    width: calc(100% - 28px);
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
    transition: background .5s ease;
}
body.dark-mode .wizard-step:not(:last-child)::after {
    background: #334155;
}

.wizard-step.completed:not(:last-child)::after {
    background: linear-gradient(90deg, #22c55e, #e2e8f0);
}
body.dark-mode .wizard-step.completed:not(:last-child)::after {
    background: linear-gradient(90deg, #22c55e, #334155);
}

.wizard-step.current:not(:last-child)::after {
    background: linear-gradient(90deg, var(--accent, #8b5cf6), #e2e8f0);
}
body.dark-mode .wizard-step.current:not(:last-child)::after {
    background: linear-gradient(90deg, var(--accent, #8b5cf6), #334155);
}

.wizard-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-size: .72rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
    transition: all .4s cubic-bezier(.4,0,.2,1);
}

.wizard-step.completed .wizard-circle {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    border: 3px solid #dcfce7;
    box-shadow: 0 3px 10px rgba(34,197,94,.3);
}
body.dark-mode .wizard-step.completed .wizard-circle {
    border-color: rgba(34,197,94,.35);
    box-shadow: 0 3px 10px rgba(34,197,94,.2);
}

.wizard-step.current .wizard-circle {
    background: linear-gradient(135deg, var(--accent, #8b5cf6), var(--accent-hover, #7c3aed));
    color: #fff;
    border: 3px solid rgba(139,92,246,.2);
    box-shadow: 0 0 0 5px rgba(139,92,246,.12);
    animation: wizardPulse 2s infinite;
}

.wizard-step.pending .wizard-circle {
    background: #f8fafc;
    color: #94a3b8;
    border: 2px solid #e2e8f0;
}
body.dark-mode .wizard-step.pending .wizard-circle {
    background: #1e293b;
    color: #64748b;
    border-color: #334155;
}

.wizard-label {
    font-size: .62rem;
    display: block;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
    margin-top: 2px;
    transition: color .3s ease;
}
.wizard-step.current .wizard-label {
    color: var(--accent, #8b5cf6);
    font-weight: 700;
}
.wizard-step.completed .wizard-label {
    color: #22c55e;
    font-weight: 600;
}

@keyframes wizardPulse {
    0% { box-shadow: 0 0 0 0 rgba(139,92,246,.3); }
    70% { box-shadow: 0 0 0 8px rgba(139,92,246,0); }
    100% { box-shadow: 0 0 0 0 rgba(139,92,246,0); }
}

/* ============================================================
   SUCCESS SCREEN — Confetti-like decoration
   ============================================================ */
.success-icon-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(34,197,94,.1), rgba(34,197,94,.05));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    animation: successPop .6s cubic-bezier(.4,0,.2,1);
}
.success-icon-container i {
    font-size: 2.5rem;
    color: #22c55e;
}
@keyframes successPop {
    0% { transform: scale(0); opacity: 0; }
    60% { transform: scale(1.15); }
    100% { transform: scale(1); opacity: 1; }
}

/* Checklist items */
.wizard-checklist .check-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .65rem 1rem;
    border-radius: var(--radius);
    margin-bottom: .35rem;
    background: rgba(248,250,252,.6);
    transition: all .25s ease;
    border: 1px solid transparent;
}
.wizard-checklist .check-item:hover {
    background: rgba(241,245,249,.8);
    transform: translateX(4px);
    border-color: rgba(34,197,94,.15);
}
body.dark-mode .wizard-checklist .check-item {
    background: rgba(30,41,59,.5);
}
body.dark-mode .wizard-checklist .check-item:hover {
    background: rgba(51,65,85,.5);
    border-color: rgba(34,197,94,.2);
}
.wizard-checklist .check-item i {
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* ============================================================
   PREMIUM ALERTS
   ============================================================ */
.wizard-alert {
    border-radius: var(--radius-lg);
    border: none;
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    font-weight: 500;
    animation: uiSlideUp .3s ease;
    backdrop-filter: blur(12px);
}
.wizard-alert i {
    font-size: 1.2rem;
    flex-shrink: 0;
}
.wizard-alert-success {
    background: rgba(34,197,94,.08);
    color: #16a34a;
    border: 1px solid rgba(34,197,94,.2);
}
.wizard-alert-danger {
    background: rgba(239,68,68,.08);
    color: #dc2626;
    border: 1px solid rgba(239,68,68,.2);
}
body.dark-mode .wizard-alert-success {
    background: rgba(34,197,94,.12);
    border-color: rgba(34,197,94,.25);
}
body.dark-mode .wizard-alert-danger {
    background: rgba(239,68,68,.12);
    border-color: rgba(239,68,68,.25);
}

/* ============================================================
   FORM SPACING FIXES FOR WIZARD
   ============================================================ */
.wizard-form {
    padding: .25rem 0;
}
.wizard-form .mt-4 {
    margin-top: 1.5rem !important;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 575.98px) {
    .wizard-step { min-width: 65px; }
    .wizard-circle { width: 28px; height: 28px; font-size: .65rem; }
    .wizard-label { font-size: .55rem; }
    .wizard-stepper-wrapper { padding: .75rem .75rem; }
}
</style>
@endpush

@section('content')
<div class="setup-page ui-page">

    @if(session('setup_completed') || (Auth::user()->businessInstance->setup_completed ?? false))
        {{-- ========== PANTALLA FINAL — COMPLETADO ========== --}}
        <div class="ui-header mb-4" style="--delay:0s">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="ui-header-body">
                <div class="ui-header-left">
                    <div class="ui-avatar-circle">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <h4 class="ui-header-title">¡Todo listo para facturar!</h4>
                        <div class="ui-header-meta">
                            <i class="bi bi-stars me-1"></i>
                            La instancia ha sido configurada correctamente
                        </div>
                    </div>
                </div>
                <div class="ui-header-actions">
                    <a href="{{ route('dashboard') }}" class="ui-btn ui-btn-primary ui-btn-pill">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body text-center py-4">
                        <div class="success-icon-container">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Configuración completada</h5>
                        <p class="text-muted mb-4">Tu sistema está preparado para comenzar a operar</p>

                        <div class="wizard-checklist text-start mb-4 mx-auto" style="max-width:450px;">
                            @foreach($steps as $step)
                                @if($step['completed'])
                                    <div class="check-item">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span>{{ $step['label'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('setup.abrir-caja') }}" class="ui-btn ui-btn-solid ui-btn-pill">
                                <i class="bi bi-cash-stack me-1"></i> Abrir Caja y Vender
                            </a>
                            <a href="{{ route('ncf.index') }}" class="ui-btn ui-btn-ghost ui-btn-pill">
                                <i class="bi bi-receipt-cutoff me-1"></i> Configurar NCF
                            </a>
                        </div>

                        <hr class="my-4">
                        <a href="#" class="ui-btn-link small"
                           onclick="event.preventDefault(); confirmAction({title:'Reiniciar Asistente', text:'¿Reiniciar la configuración inicial? Se marcarán como pendientes los pasos incompletos.', icon:'warning', color:'#f59e0b', confirmText:'Reiniciar', onSubmit:function(){ var f=document.createElement('form');f.method='POST';f.action='{{ route('setup.restart') }}';f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @elseif(!$current)
        {{-- ========== TODO COMPLETADO PERO NO MARcado ========== --}}
        <div class="ui-header mb-4" style="--delay:0s">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="ui-header-body">
                <div class="ui-header-left">
                    <div class="ui-avatar-circle">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <h4 class="ui-header-title">Todos los pasos completados</h4>
                        <div class="ui-header-meta">
                            <i class="bi bi-info-circle me-1"></i>
                            Todos los datos requeridos ya existen
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body text-center py-4">
                        <div class="success-icon-container" style="background:linear-gradient(135deg,rgba(139,92,246,.1),rgba(139,92,246,.05));">
                            <i class="bi bi-check2-all" style="color:var(--accent,#8b5cf6);"></i>
                        </div>
                        <p class="text-muted mb-4">Marca la configuración como completada y empieza a trabajar.</p>
                        <form action="{{ route('setup.complete') }}" method="POST">
                            @csrf
                            <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                                <i class="bi bi-check-lg me-1"></i> Marcar como Completado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- ========== WIZARD EN PROGRESO ========== --}}
        @if(session('error'))
            <div class="wizard-alert wizard-alert-danger mb-4">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="wizard-alert wizard-alert-success mb-4">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stepper bar --}}
        <div class="wizard-stepper-wrapper mb-4" style="--delay:0s">
            <div class="wizard-stepper">
                @foreach($steps as $i => $step)
                    <div class="wizard-step {{ $step['completed'] ? 'completed' : ($current && $step['key'] === $current['key'] ? 'current' : 'pending') }}">
                        <div class="wizard-circle">
                            @if($step['completed'])
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="wizard-label">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Current step form --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body wizard-form">
                        @include("setup._step-{$current['key']}", ['step' => $current])
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection