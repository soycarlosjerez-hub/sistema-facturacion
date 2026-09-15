@extends('layouts.app')

@section('title', 'Mi Suscripción')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0"><i class="bi bi-credit-card me-2"></i>Mi Suscripción</h4>
            <a href="{{ route('suscripcion.pagos') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-receipt me-1"></i>Historial de Pagos
            </a>
        </div>
        <p class="text-muted small mb-0">Estado y gestión de tu plan en {{ config('app.name') }}.</p>
    </div>

    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-4">
                <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-4">
                <i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-4">
                <i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}
            </div>
        @endif

        @php
            $estado = $instance->estadoSuscripcion();
            $badge = [
                'prueba' => ['bg-primary', 'Prueba gratuita'],
                'activa' => ['bg-success', 'Activa'],
                'atrasada' => ['bg-warning text-dark', 'Atrasada'],
                'suspendida' => ['bg-danger', 'Suspendida'],
            ][$estado] ?? ['bg-secondary', ucfirst($estado)];
        @endphp

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $instance->nombre }}</h5>
                        <span class="badge bg-light text-dark border rounded-pill">
                            Plan <strong>{{ $instance->plan?->nombre ?? '—' }}</strong>
                        </span>
                    </div>
                    <span class="badge {{ $badge[0] }} rounded-pill px-3 py-2 fs-6">
                        <i class="bi {{ ['prueba'=>'bi-rocket-takeoff','activa'=>'bi-check-circle-fill','atrasada'=>'bi-exclamation-triangle-fill','suspendida'=>'bi-lock-fill'][$estado] ?? 'bi-question-circle' }} me-1"></i>
                        {{ $badge[1] }}
                    </span>
                </div>

                <div class="row g-3 text-center">
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small">Precio Mensual</div>
                            <div class="fw-bold fs-5">RD$ {{ number_format($instance->precioMensual(), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small">Próximo Pago</div>
                            <div class="fw-bold fs-5">{{ optional($instance->proximoPagoEsperado())->format('d/m/Y') ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small">Meses Atrasados</div>
                            <div class="fw-bold fs-5">{{ $instance->mesesAtrasados() }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small">Deuda Estimada</div>
                            <div class="fw-bold fs-5">RD$ {{ number_format($instance->deudaEstimada(), 2) }}</div>
                        </div>
                    </div>
                </div>

                @if($estado === 'prueba')
                    <div class="alert alert-primary d-flex align-items-center gap-2 mt-4 mb-0 rounded-4">
                        <i class="bi bi-hourglass-split"></i>
                        <div>
                            Estás en tu <strong>prueba gratuita de {{ $instance->trialDays() }} días</strong>.
                            Termina el <strong>{{ optional($instance->trial_ends_at)->format('d/m/Y') }}</strong>.
                            @if($instance->diasPruebaRestantes() > 0)
                                Quedan <strong>{{ $instance->diasPruebaRestantes() }} día(s)</strong>.
                            @else
                                Tu prueba ya terminó.
                            @endif
                        </div>
                    </div>
                @endif

                @if($pendiente)
                    <div class="alert alert-warning d-flex align-items-center gap-2 mt-4 mb-0 rounded-4">
                        <i class="bi bi-clock-history"></i>
                        <div>
                            Tienes un pago <strong>pendiente de confirmación</strong> (referencia <code>{{ $pendiente->referencia_externa }}</code>).
                            Nuestro equipo lo validará en breve.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-bank me-2"></i>Pagar por Transferencia</h5>
                <p class="text-muted small mb-3">Realiza la transferencia a una de las cuentas indicadas y reporta tu referencia para confirmar el pago.</p>

                @if($cuentas->isEmpty())
                    <div class="alert alert-info rounded-4">
                        <i class="bi bi-info-circle me-1"></i>
                        No hay cuentas bancarias configuradas aún. Contacta a nuestro equipo para obtener los datos de transferencia.
                    </div>
                @else
                    <div class="row g-2 mb-3">
                        @foreach($cuentas as $cuenta)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-bank text-primary"></i>
                                    <strong>{{ $cuenta->banco }}</strong>
                                </div>
                                <div class="small mb-1"><strong>{{ $cuenta->nombre }}</strong> — {{ $cuenta->tipo_cuenta }}</div>
                                <div class="small text-muted">Nº {{ $cuenta->numero_cuenta }}</div>
                                @if($cuenta->titular)
                                    <div class="small text-muted">Titular: {{ $cuenta->titular }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                @if(! $pendiente)
                <form method="POST" action="{{ route('suscripcion.pagar') }}" class="mt-2">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Monto a pagar (RD$)</label>
                            <input type="number" step="0.01" min="1" name="monto" class="form-control"
                                   value="{{ old('monto', $instance->deudaEstimada() > 0 ? number_format($instance->deudaEstimada(), 2, '.', '') : $instance->precioMensual()) }}" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Referencia de la transferencia</label>
                            <input type="text" name="referencia_externa" class="form-control" maxlength="120"
                                   placeholder="Nº de referencia / comprobante" value="{{ old('referencia_externa') }}" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-send-check me-1"></i>Reportar Transferencia
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3"><i class="bi bi-list-check me-1"></i>Módulos del Plan</h6>
                @php $modulos = $instance->plan?->modulosPermitidos() ?? []; @endphp
                @if($modulos === [])
                    <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg me-1"></i>Todos los módulos disponibles</span>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($modulos as $m)
                            <span class="badge bg-light text-dark border rounded-pill">{{ ucfirst($m) }}</span>
                        @endforeach
                    </div>
                @endif

                <hr>

                <h6 class="fw-bold text-uppercase small text-muted mb-3"><i class="bi bi-info-circle me-1"></i>¿Cómo funciona?</h6>
                <ul class="small text-muted mb-0" style="line-height:1.9;">
                    <li><i class="bi bi-1-circle me-1"></i>Realiza la transferencia a una cuenta indicada.</li>
                    <li><i class="bi bi-2-circle me-1"></i>Reporta el monto y la referencia.</li>
                    <li><i class="bi bi-3-circle me-1"></i>Nuestro equipo confirma el pago y tu suscripción queda activa.</li>
                    <li><i class="bi bi-4-circle me-1"></i>Recibirás notificaciones y correos con los días restantes y vencimientos.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection