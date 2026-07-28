@extends('layouts.app')

@section('title', 'Prueba de Correos de Error')

@push('styles')
@include('partials.premium-ui')
<style>
/* ============================================================
   Error Test — Custom Styles
   ============================================================ */

/* Status dot indicators */
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: .4rem;
    vertical-align: middle;
}
.status-dot.ok   { background: #10b981; }
.status-dot.fail { background: #ef4444; }
.status-dot.warn { background: #f59e0b; }

/* Log level badges */
.log-level {
    padding: .15rem .5rem;
    border-radius: .35rem;
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
}
.log-level.error   { background: #fee2e2; color: #991b1b; }
.log-level.critical { background: #fecaca; color: #7f1d1d; }
.log-level.warning  { background: #fef3c7; color: #92400e; }

/* Section boxes inside the main card */
.test-section {
    border-radius: var(--radius-lg);
    padding: 1.15rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid;
}
.test-section:last-child { margin-bottom: 0; }
.test-section.section-indigo {
    background: rgba(99,102,241,.05);
    border-color: rgba(99,102,241,.15);
}
.test-section.section-red {
    background: rgba(239,68,68,.05);
    border-color: rgba(239,68,68,.15);
}
.test-section.section-amber {
    background: rgba(245,158,11,.05);
    border-color: rgba(245,158,11,.15);
}
.test-section.section-green {
    background: rgba(16,185,129,.05);
    border-color: rgba(16,185,129,.15);
}

/* Stat cards override — remove default card header styling */
.stat-status-card {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all .3s ease;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, 0s);
}
.stat-status-card:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.stat-status-card .stat-header {
    padding: .85rem 1.25rem;
    color: #fff;
    font-weight: 600;
    font-size: .85rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.stat-status-card .stat-body {
    padding: 1.25rem;
}

/* Dark mode overrides */
body.dark-mode .log-level.error   { background: #7f1d1d; color: #fecaca; }
body.dark-mode .log-level.critical { background: #450a0a; color: #fca5a5; }
body.dark-mode .log-level.warning  { background: #78350f; color: #fde68a; }

body.dark-mode .test-section.section-indigo {
    background: rgba(99,102,241,.08);
    border-color: rgba(99,102,241,.2);
}
body.dark-mode .test-section.section-red {
    background: rgba(239,68,68,.08);
    border-color: rgba(239,68,68,.2);
}
body.dark-mode .test-section.section-amber {
    background: rgba(245,158,11,.08);
    border-color: rgba(245,158,11,.2);
}
body.dark-mode .test-section.section-green {
    background: rgba(16,185,129,.08);
    border-color: rgba(16,185,129,.2);
}

body.dark-mode .stat-status-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626">
<div class="container-fluid px-4 py-3">

    {{-- ── Header Premium ── --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bug"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Prueba de Correos de Error</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-flask me-1"></i>
                        Valida que los correos de alerta de error se envíen correctamente
                        <span class="divider">·</span>
                        <i class="bi bi-shield-lock me-1"></i>
                        Solo Owner
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.smtp-settings') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-gear me-1"></i> Configuración SMTP
                </a>
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             style="background: linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46;" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             style="background: linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b;" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- ── Status Stats Row ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4" style="--delay:.1s">
            <div class="stat-status-card">
                <div class="stat-header" style="background: linear-gradient(135deg,#059669,#10b981);">
                    <i class="bi bi-envelope"></i> SMTP Server
                </div>
                <div class="stat-body">
                    @if($smtpOk)
                        <div><span class="status-dot ok"></span><strong>Configurado</strong></div>
                        <small class="text-muted">{{ $settings['mail_host'] }}:{{ $settings['mail_port'] }}</small>
                    @else
                        <div><span class="status-dot fail"></span><strong>No configurado</strong></div>
                        <small class="text-muted">Configura el SMTP en Owner &gt; SMTP Settings</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4" style="--delay:.15s">
            <div class="stat-status-card">
                <div class="stat-header" style="background: linear-gradient(135deg,#6366f1,#8b5cf6);">
                    <i class="bi bi-bell"></i> Alerta de Error
                </div>
                <div class="stat-body">
                    @if($alertOk)
                        <div><span class="status-dot ok"></span><strong>Correo destino</strong></div>
                        <small class="text-muted">{{ $settings['error_alert_email'] }}</small>
                    @else
                        <div><span class="status-dot fail"></span><strong>Sin destinatario</strong></div>
                        <small class="text-muted">Configúralo en Owner &gt; SMTP Settings</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4" style="--delay:.2s">
            <div class="stat-status-card">
                <div class="stat-header" style="background: linear-gradient(135deg,#059669,#10b981);">
                    <i class="bi bi-envelope-check"></i> Envío Sincrónico
                </div>
                <div class="stat-body">
                    <div><span class="status-dot ok"></span><strong>Activado</strong></div>
                    <small class="text-muted">Los correos se envían directamente sin colas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Grid ── --}}
    <div class="row g-4">

        {{-- ════════════════════════════════════════
              LEFT — Test Actions
              ════════════════════════════════════════ --}}
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #ef4444, rgba(255,255,255,.3));"></div>
                <h5 class="ui-card-title">
                    <i class="bi bi-flask"></i> Pruebas de Envío
                </h5>
                <p class="ui-card-subtitle">Realiza pruebas individuales para validar cada componente del sistema de alertas.</p>

                <div class="ui-card-body">

                    {{-- 1. SMTP Test --}}
                    <div class="test-section section-indigo">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-send-check me-1" style="color:#6366f1;"></i> Prueba SMTP Directa
                        </h6>
                        <p class="text-muted small mb-3">Envía un correo directo para verificar que el servidor SMTP responde.</p>
                        <form method="POST" action="{{ route('owner.error-test.smtp') }}" class="row g-2">
                            @csrf
                            <div class="col-md-8">
                                <input type="email" name="test_email" class="ui-input"
                                       value="{{ old('test_email', $settings['error_alert_email']) }}"
                                       placeholder="correo@ejemplo.com" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill"
                                        style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
                                    <i class="bi bi-send me-1"></i> Enviar Prueba
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Simulate Exception --}}
                    <div class="test-section section-red">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-exclamation-triangle me-1" style="color:#ef4444;"></i> Simular Excepción
                        </h6>
                        <p class="text-muted small mb-2">Lanza una excepción real para probar el callback <code>reportable</code> de Laravel y verificar que se envía el correo.</p>
                        <p class="small text-warning-emphasis mb-3"><i class="bi bi-info-circle me-1"></i> La excepción se cachea 5 min para evitar duplicados.</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="division">
                                <button type="submit" class="ui-btn ui-btn-ghost rounded-pill"
                                        style="border-color:rgba(239,68,68,.3);color:#dc2626;"
                                        onclick="return confirm('Se lanzará una excepción real. ¿Continuar?')">
                                    <i class="bi bi-0-circle me-1"></i> División por Cero
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="null">
                                <button type="submit" class="ui-btn ui-btn-ghost rounded-pill"
                                        style="border-color:rgba(245,158,11,.3);color:#d97706;"
                                        onclick="return confirm('Se lanzará una excepción real. ¿Continuar?')">
                                    <i class="bi bi-slash-circle me-1"></i> Objeto Nulo
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="class">
                                <button type="submit" class="ui-btn ui-btn-ghost rounded-pill"
                                        style="border-color:rgba(100,116,139,.3);color:#64748b;"
                                        onclick="return confirm('Se lanzará una excepción real. ¿Continuar?')">
                                    <i class="bi bi-x-circle me-1"></i> Método inexistente
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- 3. Log Error --}}
                    <div class="test-section section-amber">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-journal-text me-1" style="color:#f59e0b;"></i> Generar Log de Error
                        </h6>
                        <p class="text-muted small mb-3">Dispara un <code>Log::error()</code> para probar el listener <code>LogErrorToDatabase</code>.</p>
                        <form method="POST" action="{{ route('owner.error-test.log') }}">
                            @csrf
                            <button type="submit" class="ui-btn ui-btn-solid rounded-pill"
                                    style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
                                <i class="bi bi-journal-plus me-1"></i> Generar Log de Error
                            </button>
                        </form>
                    </div>

                    {{-- 4. DB Trigger --}}
                    <div class="test-section section-green">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-database-gear me-1" style="color:#10b981;"></i> Trigger Manual desde BD
                        </h6>
                        <p class="text-muted small mb-3">Crea un registro en <code>instance_error_logs</code> y envía el correo inmediatamente (por cola).</p>
                        <form method="POST" action="{{ route('owner.error-test.db-trigger') }}" class="row g-2">
                            @csrf
                            <div class="col-md-8">
                                <input type="email" name="email" class="ui-input"
                                       value="{{ old('email', $settings['error_alert_email']) }}"
                                       placeholder="alertas@miempresa.com">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill"
                                        style="--accent:#059669;--accent-rgb:5,150,105;--accent-hover:#047857;">
                                    <i class="bi bi-play me-1"></i> Trigger &amp; Enviar
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
              RIGHT — Recent Errors Log
              ════════════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #475569, rgba(255,255,255,.3));"></div>
                <h5 class="ui-card-title">
                    <i class="bi bi-list-ul" style="color:#475569;"></i> Últimos Errores
                </h5>
                <p class="ui-card-subtitle">Registros recientes de errores capturados por el sistema.</p>

                <div class="ui-card-body p-0">
                    @if(count($recentErrors) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover ui-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nivel</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentErrors as $log)
                                <tr>
                                    <td><span class="log-level {{ $log->level }}">{{ $log->level }}</span></td>
                                    <td class="text-truncate" style="max-width:200px" title="{{ $log->title }}">{{ $log->title }}</td>
                                    <td class="text-nowrap"><small>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="ui-empty-state py-5">
                        <i class="bi bi-inbox d-block mb-2"></i>
                        <p class="mb-0">No hay errores registrados aún.</p>
                    </div>
                    @endif
                </div>
                <div class="px-4 pb-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Los correos de error se envían de forma sincrónica. No requiere worker de queue.
                </div>
            </div>
        </div>

    </div>
    {{-- /row --}}

</div>
</div>
@endsection
