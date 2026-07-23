@extends('layouts.app')
@section('title', 'Configuración Inicial')

@push('styles')
<style>
.wizard-stepper {
    display: flex;
    gap: 0;
    overflow-x: auto;
    padding-bottom: 2px;
}
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
    background: #dee2e6;
    z-index: 0;
}
.wizard-step.completed:not(:last-child)::after {
    background: #28a745;
}
.wizard-step.current:not(:last-child)::after {
    background: #007bff;
}
.wizard-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-size: .75rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
}
.wizard-step.completed .wizard-circle {
    background: #28a745;
    color: #fff;
    border: 2px solid #d4edda;
}
.wizard-step.current .wizard-circle {
    background: #007bff;
    color: #fff;
    border: 2px solid #b8daff;
}
.wizard-step.pending .wizard-circle {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #dee2e6;
}
.wizard-label {
    font-size: .65rem;
    display: block;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
}
.wizard-step.current .wizard-label {
    color: #007bff;
    font-weight: 700;
}
.wizard-step.completed .wizard-label {
    color: #28a745;
    font-weight: 600;
}
.success-icon-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #d4edda;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}
.success-icon-container i {
    font-size: 2.5rem;
    color: #28a745;
}
.check-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .5rem .75rem;
    border-radius: .375rem;
    margin-bottom: .25rem;
    background: #f8f9fa;
}
.check-item i {
    font-size: 1.1rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if(session('setup_completed') || (Auth::user()->businessInstance->setup_completed ?? false))
        {{-- COMPLETADO --}}
        <div class="card mb-4">
            <div class="card-body text-center">
                <h4 class="mb-1">¡Todo listo para facturar!</h4>
                <p class="text-muted mb-0">La instancia ha sido configurada correctamente</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <div class="success-icon-container">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Configuración completada</h5>
                        <p class="text-muted mb-4">Tu sistema está preparado para comenzar a operar</p>

                        <div class="text-start mb-4 mx-auto" style="max-width:450px;">
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
                            <a href="{{ route('setup.abrir-caja') }}" class="btn btn-success">
                                <i class="bi bi-cash-stack me-1"></i> Abrir Caja y Vender
                            </a>
                            <a href="{{ route('ncf.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-receipt-cutoff me-1"></i> Configurar NCF
                            </a>
                        </div>

                        <hr class="my-4">
                        <a href="#" class="text-decoration-none small"
                           onclick="event.preventDefault(); confirmAction({title:'Reiniciar Asistente', text:'¿Reiniciar la configuración inicial? Se marcarán como pendientes los pasos incompletos.', icon:'warning', color:'#f59e0b', confirmText:'Reiniciar', onSubmit:function(){ var f=document.createElement('form');f.method='POST';f.action='{{ route('setup.restart') }}';f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @elseif(!$current)
        {{-- TODOS COMPLETADOS --}}
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-1">Todos los pasos completados</h4>
                <p class="text-muted mb-0">Todos los datos requeridos ya existen</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <div class="success-icon-container">
                            <i class="bi bi-check2-all"></i>
                        </div>
                        <p class="text-muted mb-4">Marca la configuración como completada y empieza a trabajar.</p>
                        <form action="{{ route('setup.complete') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-lg me-1"></i> Marcar como Completado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- WIZARD EN PROGRESO --}}
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stepper --}}
        <div class="card mb-4">
            <div class="card-body py-3">
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

        {{-- Current step --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        @include("setup._step-{$current['key']}", ['step' => $current])
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection