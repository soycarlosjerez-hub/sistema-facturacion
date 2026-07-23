@extends('layouts.app')
@section('title', 'Configuración Inicial')

@push('styles')
@include('partials.premium-ui')
<style>
/* ============================================================
   SETUP WIZARD — Overrides específicos del wizard
   ============================================================ */

/* Steps bar */
.steps-bar {
    display: flex;
    gap: 0;
    overflow-x: auto;
    padding: .5rem 0;
    scrollbar-width: thin;
}
.steps-bar::-webkit-scrollbar { height: 4px; }
.steps-bar::-webkit-scrollbar-thumb { background: rgba(139,92,246,.3); border-radius: 4px; }

.step-item {
    flex: 1;
    min-width: 80px;
    text-align: center;
    position: relative;
}
.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: calc(50% + 14px);
    width: calc(100% - 28px);
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
    transition: background .3s;
}
.step-item.completed:not(:last-child)::after {
    background: linear-gradient(90deg, #22c55e, #e2e8f0);
}
.step-item.current:not(:last-child)::after {
    background: linear-gradient(90deg, var(--accent, #8b5cf6), #e2e8f0);
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 4px;
    font-size: .85rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
    transition: all .3s;
}
.step-item.completed .step-circle {
    background: #22c55e;
    color: #fff;
    border: 3px solid #dcfce7;
}
.step-item.current .step-circle {
    background: var(--accent, #8b5cf6);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(139,92,246,.25);
    animation: pulse 2s infinite;
    border: 3px solid rgba(139,92,246,.15);
}
.step-item.pending .step-circle {
    background: #f1f5f9;
    color: #94a3b8;
    border: 3px solid #e2e8f0;
}
.step-label {
    font-size: .6rem;
    display: block;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
}
.step-item.current .step-label {
    color: var(--accent, #8b5cf6);
    font-weight: 700;
}
.step-item.completed .step-label {
    color: #22c55e;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(139,92,246,.4); }
    70% { box-shadow: 0 0 0 10px rgba(139,92,246,0); }
    100% { box-shadow: 0 0 0 0 rgba(139,92,246,0); }
}

/* Final checklist */
.final-checklist .check-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem 1rem;
    border-radius: var(--radius);
    margin-bottom: .5rem;
    background: #f8fafc;
    transition: all .2s;
}
.final-checklist .check-item.done {
    background: #f0fdf4;
}
.final-checklist .check-item i {
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* Responsive */
@media (max-width: 575.98px) {
    .step-item { min-width: 65px; }
    .step-circle { width: 32px; height: 32px; font-size: .75rem; }
    .step-label { font-size: .55rem; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    @if(session('setup_completed') || (Auth::user()->businessInstance->setup_completed ?? false))
        {{-- Pantalla final --}}
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
                        <div class="final-checklist text-start mb-4 mx-auto" style="max-width:450px;">
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
        {{-- Todo completado pero no marcado --}}
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
        {{-- Wizard en progreso --}}
        @if(session('error'))
            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif

        {{-- Steps bar --}}
        <div class="ui-card mb-4" style="--delay:0s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body p-3">
                <div class="steps-bar">
                    @foreach($steps as $i => $step)
                        <div class="step-item {{ $step['completed'] ? 'completed' : ($current && $step['key'] === $current['key'] ? 'current' : 'pending') }}">
                            <div class="step-circle">
                                @if($step['completed'])
                                    <i class="bi bi-check-lg"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span class="step-label">{{ $step['label'] }}</span>
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
                    <div class="ui-card-body">
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
