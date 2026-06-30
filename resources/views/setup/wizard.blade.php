@extends('layouts.app')
@section('title', 'Configuración Inicial')

@push('styles')
@include('partials.premium-ui')
<style>
.steps-bar { display: flex; gap: 0; overflow-x: auto; padding: 0.5rem 0; }
.step-item { flex: 1; min-width: 80px; text-align: center; position: relative; }
.step-item:not(:last-child)::after { content: ''; position: absolute; top: 20px; left: 50%; width: 100%; height: 2px; background: #e2e8f0; z-index: 0; }
.step-item.completed:not(:last-child)::after { background: #22c55e; }
.step-item.current:not(:last-child)::after { background: linear-gradient(90deg, #8b5cf6, #e2e8f0); }
.step-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 4px; font-size: 0.85rem; font-weight: 700; position: relative; z-index: 1; transition: all .3s; }
.step-item.completed .step-circle { background: #22c55e; color: #fff; }
.step-item.current .step-circle { background: #8b5cf6; color: #fff; box-shadow: 0 0 0 4px rgba(139,92,246,.25); animation: pulse 2s infinite; }
.step-item.pending .step-circle { background: #e2e8f0; color: #94a3b8; }
.step-label { font-size: 0.6rem; display: block; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.step-item.current .step-label { color: #8b5cf6; font-weight: 700; }
.step-item.completed .step-label { color: #22c55e; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(139,92,246,.4); } 70% { box-shadow: 0 0 0 10px rgba(139,92,246,0); } 100% { box-shadow: 0 0 0 0 rgba(139,92,246,0); } }
.form-wizard-card { border: none; border-radius: 16px; overflow: hidden; }
.form-wizard-card .card-accent { height: 4px; }
.wizard-form .form-control, .wizard-form .form-select { border-radius: 10px; padding: 0.65rem 1rem; }
.wizard-form .form-control:focus, .wizard-form .form-select:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.15); }
.btn-wizard-next { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; border-radius: 50px; padding: 0.6rem 2rem; font-weight: 700; color: #fff; transition: all .3s; }
.btn-wizard-next:hover { box-shadow: 0 6px 20px rgba(139,92,246,.45); transform: translateY(-1px); color: #fff; }
.final-checklist .check-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; border-radius: 10px; margin-bottom: 0.5rem; background: #f8fafc; }
.final-checklist .check-item.done { background: #f0fdf4; }
body.dark-mode .step-item:not(:last-child)::after { background: #334155; }
body.dark-mode .step-item.completed:not(:last-child)::after { background: #22c55e; }
body.dark-mode .step-item.pending .step-circle { background: #334155; color: #64748b; }
body.dark-mode .step-label { color: #94a3b8; }
body.dark-mode .final-checklist .check-item { background: #1e293b; }
body.dark-mode .final-checklist .check-item.done { background: #052e16; }

</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page" x-data="wizardApp()">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6, #a855f7, #7c3aed); background-size: 300% 300%;">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-magic"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Configuración Inicial</h2>
                <p class="text-white-50 mb-0">Completa los pasos para tener todo listo y empezar a facturar</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if(session('setup_completed') || (Auth::user()->businessInstance->setup_completed ?? false))
        {{-- Pantalla final --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card text-center py-5">
                    <div class="card-accent green"></div>
                    <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                    <h3 class="fw-bold mb-2">¡Todo listo para facturar!</h3>
                    <p class="text-muted mb-4">La instancia ha sido configurada correctamente.</p>

                    <div class="final-checklist text-start mb-4 mx-auto" style="max-width: 450px;">
                        @foreach($steps as $step)
                            @if($step['completed'])
                                <div class="check-item done">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>{{ $step['label'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="{{ route('setup.abrir-caja') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow">
                            <i class="bi bi-cash-stack me-1"></i> Abrir Caja y Vender
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                        <a href="{{ route('ncf.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-receipt-cutoff me-1"></i> Configurar NCF
                        </a>
                    </div>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="{{ route('setup.restart') }}" class="text-muted small"
                           onclick="return confirm('¿Reiniciar la configuración inicial? Se marcarán como pendientes los pasos incompletos.')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reinizar configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(!$current)
        {{-- Todo completado pero no marcado --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card text-center py-5">
                    <div class="card-accent green"></div>
                    <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                    <h3 class="fw-bold mb-2">Todos los pasos completados</h3>
                    <p class="text-muted mb-4">Todos los datos requeridos ya existen. Marca la configuración como completada.</p>
                    <form action="{{ route('setup.complete') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow">
                            <i class="bi bi-check-lg me-1"></i> Marcar como Completado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- Steps bar --}}
        <div class="premium-card mb-4 p-3">
            <div class="card-accent purple"></div>
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

        {{-- Current step form --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card form-wizard-card">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4 wizard-form">
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
function wizardApp() {
    return {};
}
</script>
@endpush
