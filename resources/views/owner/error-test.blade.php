@extends('layouts.app')

@section('title', 'Prueba de Correos de Error')

@push('styles')
<style>
.test-card { border-radius: 1rem; border: none; background: rgba(255,255,255,.85); backdrop-filter: blur(12px); box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 32px rgba(0,0,0,.06); overflow: hidden; height: 100%; }
.test-card .card-header { padding: 1rem 1.5rem; border: none; font-weight: 600; font-size: .95rem; }
.test-card .card-body { padding: 1.5rem; }
.test-card .card-footer { background: transparent; border-top: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: .4rem; }
.status-dot.ok { background: #10b981; }
.status-dot.fail { background: #ef4444; }
.status-dot.warn { background: #f59e0b; }
.log-table { font-size: .85rem; }
.log-table td, .log-table th { padding: .5rem .75rem; vertical-align: middle; }
.log-level { padding: .15rem .5rem; border-radius: .35rem; font-size: .75rem; font-weight: 600; text-transform: uppercase; }
.log-level.error { background: #fee2e2; color: #991b1b; }
.log-level.critical { background: #fecaca; color: #7f1d1d; }
.log-level.warning { background: #fef3c7; color: #92400e; }
body.dark-mode .test-card { background: rgba(15,23,42,.85); box-shadow: 0 1px 3px rgba(0,0,0,.2), 0 8px 32px rgba(0,0,0,.3); }
body.dark-mode .test-card .card-footer { border-top-color: #334155; }
body.dark-mode .log-level.error { background: #7f1d1d; color: #fecaca; }
body.dark-mode .log-level.critical { background: #450a0a; color: #fca5a5; }
body.dark-mode .log-level.warning { background: #78350f; color: #fde68a; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626">
<div class="container-fluid px-4 py-3">

<div class="ui-header mb-4" style="--delay:.1s">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="ui-title fw-bold" style="font-size:1.65rem">Prueba de Correos de Error</h1>
            <p class="ui-subtitle text-muted mb-0" style="font-size:.9rem">Valida que los correos de alerta de error se env&iacute;en correctamente</p>
        </div>
        <a href="{{ route('owner.smtp-settings') }}" class="btn btn-outline-secondary rounded-4 px-3 py-2 fw-semibold">
            <i class="bi bi-gear me-1"></i> Configuraci&oacute;n SMTP
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" style="background: linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" style="background: linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Status Bar --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="test-card card">
            <div class="card-header" style="background: linear-gradient(135deg,#059669,#10b981); color:#fff;">
                <i class="bi bi-envelope me-1"></i> SMTP Server
            </div>
            <div class="card-body">
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
    <div class="col-md-4">
        <div class="test-card card">
            <div class="card-header" style="background: linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff;">
                <i class="bi bi-bell me-1"></i> Alerta de Error
            </div>
            <div class="card-body">
                @if($alertOk)
                    <div><span class="status-dot ok"></span><strong>Correo destino</strong></div>
                    <small class="text-muted">{{ $settings['error_alert_email'] }}</small>
                @else
                    <div><span class="status-dot fail"></span><strong>Sin destinatario</strong></div>
                    <small class="text-muted">Config&uacute;ralo en Owner &gt; SMTP Settings</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="test-card card">
            <div class="card-header" style="background: linear-gradient(135deg,#059669,#10b981); color:#fff;">
                <i class="bi bi-envelope-check me-1"></i> Envío Sincrónico
            </div>
            <div class="card-body">
                <div><span class="status-dot ok"></span><strong>Activado</strong></div>
                <small class="text-muted">Los correos se envían directamente sin colas</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Pruebas --}}
    <div class="col-lg-7">
        <div class="test-card card">
            <div class="card-header" style="background: linear-gradient(135deg,#ef4444,#dc2626); color:#fff;">
                <i class="bi bi-flask me-1"></i> Pruebas de Env&iacute;o
            </div>
            <div class="card-body">

                {{-- 1. SMTP Test --}}
                <div class="mb-4 p-3 rounded-4" style="background: rgba(99,102,241,.05); border: 1px solid rgba(99,102,241,.15);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-send-check me-1 text-primary"></i> Prueba SMTP Directa</h6>
                    <p class="text-muted small mb-3">Env&iacute;a un correo directo para verificar que el servidor SMTP responde.</p>
                    <form method="POST" action="{{ route('owner.error-test.smtp') }}" class="row g-2">
                        @csrf
                        <div class="col-md-8">
                            <input type="email" name="test_email" class="form-control" value="{{ old('test_email', $settings['error_alert_email']) }}" placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn text-white w-100 fw-semibold" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                                <i class="bi bi-send me-1"></i> Enviar Prueba
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. Simular Excepción --}}
                <div class="mb-4 p-3 rounded-4" style="background: rgba(239,68,68,.05); border: 1px solid rgba(239,68,68,.15);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle me-1 text-danger"></i> Simular Excepci&oacute;n</h6>
                    <p class="text-muted small mb-3">Lanza una excepci&oacute;n real para probar el callback <code>reportable</code> de Laravel y verificar que se env&iacute;a el correo.</p>
                    <p class="small text-warning-emphasis mb-2"><i class="bi bi-info-circle me-1"></i> La excepci&oacute;n se cachea 5 min para evitar duplicados.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="division">
                            <button type="submit" class="btn btn-outline-danger fw-semibold" onclick="return confirm('Se lanzar\u00e1 una excepci\u00f3n real. \u00bfContinuar?')">
                                <i class="bi bi-0-circle me-1"></i> Divisi&oacute;n por Cero
                            </button>
                        </form>
                        <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="null">
                            <button type="submit" class="btn btn-outline-warning fw-semibold" onclick="return confirm('Se lanzar\u00e1 una excepci\u00f3n real. \u00bfContinuar?')">
                                <i class="bi bi-slash-circle me-1"></i> Objeto Nulo
                            </button>
                        </form>
                        <form method="POST" action="{{ route('owner.error-test.simulate') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="class">
                            <button type="submit" class="btn btn-outline-secondary fw-semibold" onclick="return confirm('Se lanzar\u00e1 una excepci\u00f3n real. \u00bfContinuar?')">
                                <i class="bi bi-x-circle me-1"></i> M&eacute;todo inexistente
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 3. Log Error --}}
                <div class="mb-4 p-3 rounded-4" style="background: rgba(245,158,11,.05); border: 1px solid rgba(245,158,11,.15);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-journal-text me-1 text-warning"></i> Generar Log de Error</h6>
                    <p class="text-muted small mb-3">Dispara un <code>Log::error()</code> para probar el listener <code>LogErrorToDatabase</code>.</p>
                    <form method="POST" action="{{ route('owner.error-test.log') }}">
                        @csrf
                        <button type="submit" class="btn text-white fw-semibold" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                            <i class="bi bi-journal-plus me-1"></i> Generar Log de Error
                        </button>
                    </form>
                </div>

                {{-- 4. DB Trigger --}}
                <div class="p-3 rounded-4" style="background: rgba(16,185,129,.05); border: 1px solid rgba(16,185,129,.15);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-database-gear me-1 text-success"></i> Trigger Manual desde BD</h6>
                    <p class="text-muted small mb-3">Crea un registro en <code>instance_error_logs</code> y env&iacute;a el correo inmediatamente (por cola).</p>
                    <form method="POST" action="{{ route('owner.error-test.db-trigger') }}" class="row g-2">
                        @csrf
                        <div class="col-md-8">
                            <input type="email" name="email" class="form-control" value="{{ old('email', $settings['error_alert_email']) }}" placeholder="alertas@miempresa.com">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn text-white w-100 fw-semibold" style="background:linear-gradient(135deg,#059669,#10b981)">
                                <i class="bi bi-play me-1"></i> Trigger &amp; Enviar
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Logs Recientes --}}
    <div class="col-lg-5">
        <div class="test-card card">
            <div class="card-header" style="background: linear-gradient(135deg,#475569,#64748b); color:#fff;">
                <i class="bi bi-list-ul me-1"></i> &Uacute;ltimos Errores
            </div>
            <div class="card-body p-0">
                @if(count($recentErrors) > 0)
                <div class="table-responsive">
                    <table class="table table-hover log-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nivel</th>
                                <th>T&iacute;tulo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentErrors as $log)
                            <tr>
                                <td><span class="log-level {{ $log->level }}">{{ $log->level }}</span></td>
                                <td class="text-truncate" style="max-width:200px" title="{{ $log->title }}">{{ $log->title }}</td>
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <span>No hay errores registrados a&uacute;n.</span>
                </div>
                @endif
            </div>
            <div class="card-footer text-end small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Los correos de error se envían de forma sincrónica. No requiere worker de queue.
            </div>
        </div>
    </div>
</div>

</div>
</div>
@endsection
