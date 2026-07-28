@extends('layouts.app')

@section('title', 'Configuración SMTP')

@push('styles')
@include('partials.premium-ui')
<style>
.smtp-form label { font-weight: 600; font-size: .85rem; color: #475569; margin-bottom: .35rem; }
.smtp-form .form-control, .smtp-form .form-select { border-radius: .65rem; border: 1.5px solid #e2e8f0; padding: .6rem 1rem; font-size: .9rem; transition: all .2s; }
.smtp-form .form-control:focus, .smtp-form .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
.smtp-card { border-radius: 1rem; border: none; background: rgba(255,255,255,.85); backdrop-filter: blur(12px); box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 32px rgba(0,0,0,.06); overflow: hidden; }
.smtp-card .card-header { background: linear-gradient(135deg,#6366f1,#8b5cf6); padding: 1rem 1.5rem; border: none; }
.smtp-card .card-header h5 { color: #fff; font-weight: 600; margin: 0; font-size: 1rem; }
body.dark-mode .smtp-form label { color: #94a3b8; }
body.dark-mode .smtp-form .form-control, body.dark-mode .smtp-form .form-select { background: #1e293b; border-color: #334155; color: #e2e8f0; }
body.dark-mode .smtp-card { background: rgba(15,23,42,.85); box-shadow: 0 1px 3px rgba(0,0,0,.2), 0 8px 32px rgba(0,0,0,.3); }
body.dark-mode .smtp-card .card-body { background: transparent; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5">
<div class="container-fluid px-4 py-3">

<div class="ui-header mb-4" style="--delay:.1s">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="ui-title fw-bold" style="font-size:1.65rem">Configuraci&oacute;n SMTP</h1>
            <p class="ui-subtitle text-muted mb-0" style="font-size:.9rem">Servidor de correo global del sistema</p>
        </div>
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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="smtp-card card">
            <div class="card-header">
                <h5><i class="bi bi-envelope-at me-2"></i>Servidor SMTP</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('owner.smtp-settings.update') }}" class="smtp-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mailer</label>
                            <select name="mail_mailer" class="form-select">
                                <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="log" {{ $settings['mail_mailer'] == 'log' ? 'selected' : '' }}>Log (solo pruebas)</option>
                                <option value="mail" {{ $settings['mail_mailer'] == 'mail' ? 'selected' : '' }}>PHP Mail</option>
                                <option value="sendmail" {{ $settings['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Host</label>
                            <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host'] }}" placeholder="smtp.ejemplo.com">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Puerto</label>
                            <input type="text" name="mail_port" class="form-control" value="{{ $settings['mail_port'] }}" placeholder="465">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Encriptaci&oacute;n</label>
                            <select name="mail_encryption" class="form-select">
                                <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="null" {{ $settings['mail_encryption'] == 'null' ? 'selected' : '' }}>Sin encriptaci&oacute;n</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username'] }}" placeholder="correo@ejemplo.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contrase&ntilde;a</label>
                            <input type="password" name="mail_password" class="form-control" placeholder="•••••••• (dejar vac&iacute;o para mantener)">
                            <small class="text-muted">Dejar en blanco para mantener la actual.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Direcci&oacute;n Desde (From)</label>
                            <input type="email" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] }}" placeholder="no-reply@ejemplo.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre Desde (From Name)</label>
                            <input type="text" name="mail_from_name" class="form-control" value="{{ $settings['mail_from_name'] }}" placeholder="Sistema">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn text-white px-4 py-2 rounded-4 fw-semibold" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                            <i class="bi bi-check-lg me-1"></i> Guardar Configuraci&oacute;n
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="smtp-card card">
            <div class="card-header">
                <h5><i class="bi bi-send-check me-2"></i>Probar Conexi&oacute;n</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Env&iacute;a un correo de prueba para verificar la configuraci&oacute;n SMTP.</p>
                <form method="POST" action="{{ route('owner.smtp-settings.test') }}" class="smtp-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correo de prueba</label>
                        <input type="email" name="test_email" class="form-control" placeholder="tucorreo@ejemplo.com" required>
                    </div>
                    <button type="submit" class="btn text-white px-4 py-2 rounded-4 fw-semibold w-100" style="background:linear-gradient(135deg,#059669,#10b981)">
                        <i class="bi bi-send me-1"></i> Enviar Prueba
                    </button>
                </form>

                <hr class="my-4">

                <div class="small text-muted">
                    <p class="mb-1"><i class="bi bi-info-circle me-1"></i> Esta configuraci&oacute;n es <strong>global</strong> y aplica a todas las instancias.</p>
                    <p class="mb-0"><i class="bi bi-shield-lock me-1"></i> Solo el usuario Owner puede modificarla.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
@endsection