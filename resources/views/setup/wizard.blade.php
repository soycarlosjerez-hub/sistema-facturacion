@extends('layouts.app')
@section('title', 'Configuración Inicial')

@push('styles')
@include('partials.premium-ui')
<style>
/* ============================================================
   SETUP WIZARD — Overrides específicos
   ============================================================ */

/* Page override for wizard */
.setup-page {
    --accent: #8b5cf6;
    --accent-rgb: 139, 92, 246;
    --accent-hover: #7c3aed;
    --accent-light: #ede9fe;
}

/* ============================================================
   STEPPER BAR
   ============================================================ */
.wizard-stepper {
    display: flex;
    gap: 0;
    overflow-x: auto;
    padding: .75rem 0;
    scrollbar-width: thin;
    scrollbar-color: rgba(139,92,246,.2) transparent;
}
.wizard-stepper::-webkit-scrollbar { height: 4px; }
.wizard-stepper::-webkit-scrollbar-track { background: transparent; }
.wizard-stepper::-webkit-scrollbar-thumb { background: rgba(139,92,246,.25); border-radius: 4px; }

.wizard-step {
    flex: 1;
    min-width: 90px;
    text-align: center;
    position: relative;
    padding-bottom: .5rem;
}
.wizard-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 18px;
    left: calc(50% + 16px);
    width: calc(100% - 32px);
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
    transition: background .4s ease;
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
    transition: all .35s cubic-bezier(.4,0,.2,1);
}

.wizard-step.completed .wizard-circle {
    background: #22c55e;
    color: #fff;
    border: 3px solid #dcfce7;
    box-shadow: 0 2px 8px rgba(34,197,94,.25);
}
body.dark-mode .wizard-step.completed .wizard-circle {
    border-color: rgba(34,197,94,.3);
}

.wizard-step.current .wizard-circle {
    background: var(--accent, #8b5cf6);
    color: #fff;
    border: 3px solid rgba(139,92,246,.2);
    box-shadow: 0 0 0 4px rgba(139,92,246,.15);
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
    font-size: .65rem;
    display: block;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
    margin-top: 2px;
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
    0% { box-shadow: 0 0 0 0 rgba(139,92,246,.35); }
    70% { box-shadow: 0 0 0 8px rgba(139,92,246,0); }
    100% { box-shadow: 0 0 0 0 rgba(139,92,246,0); }
}

/* ============================================================
   FINAL CHECKLIST
   ============================================================ */
.wizard-checklist .check-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .7rem 1rem;
    border-radius: var(--radius);
    margin-bottom: .4rem;
    background: #f8fafc;
    transition: all .2s ease;
}
.wizard-checklist .check-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}
body.dark-mode .wizard-checklist .check-item {
    background: #1e293b;
}
body.dark-mode .wizard-checklist .check-item:hover {
    background: #334155;
}
.wizard-checklist .check-item.done {
    background: #f0fdf4;
}
body.dark-mode .wizard-checklist .check-item.done {
    background: rgba(34,197,94,.08);
}
.wizard-checklist .check-item i {
    font-size: 1.15rem;
    flex-shrink: 0;
}

/* ============================================================
   PREMIUM ALERTS
   ============================================================ */
.wizard-alert {
    border-radius: var(--radius-lg);
    border: none;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    font-weight: 500;
    animation: uiSlideUp .3s ease;
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
.wizard-form .ui-card-title {
    padding-top: 1.25rem;
}
.wizard-form .mt-4 {
    margin-top: 1.5rem !important;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 575.98px) {
    .wizard-step { min-width: 70px; }
    .wizard-circle { width: 30px; height: 30px; font-size: .7rem; }
    .wizard-label { font-size: .55rem; }
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
                        <div class="wizard-checklist text-start mb-4 mx-auto" style="max-width:450px;">
                            @foreach($steps as $step)
                                @if($step['completed'])
                                    <div class="check-item done">
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
        <div class="ui-card mb-4" style="--delay:0s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body p-3">
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

@push('scripts')
<script>
function confirmAction({title, text, icon, color, confirmText, onSubmit}) {
    if(confirm(title + '\n\n' + text)) {
        onSubmit();
    }
}
</script>
@endpush
