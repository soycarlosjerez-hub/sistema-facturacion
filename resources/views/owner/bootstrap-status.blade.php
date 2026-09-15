@extends('layouts.app')
@section('title', 'Estado Owner Bootstrap')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Estado Owner Bootstrap</h2>
                    <p class="text-muted mb-0">
                        Verificar y reconstruir el propietario del sistema.
                        @if($status['is_bootstrap'])
                            <span class="badge bg-warning text-dark ms-2">
                                <i class="fas fa-cloud me-1"></i>Modo Bootstrap
                            </span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('owner.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> Estado</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th class="text-muted">Autenticado como Owner:</th>
                                    <td>
                                        @if($status['authenticated'])
                                            <i class="fas fa-check-circle text-success"></i> Sí
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> No
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">En Base de Datos:</th>
                                    <td>
                                        @if($status['is_in_database'])
                                            <i class="fas fa-check-circle text-success"></i> Sí
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> No
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Modo Bootstrap:</th>
                                    <td>
                                        @if($status['is_bootstrap'])
                                            <span class="badge bg-warning text-dark">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Modo actual:</th>
                                    <td><code>{{ $status['mode'] }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-tools me-1 text-warning"></i> Acciones</h5>

                            @if($status['is_bootstrap'] && !$status['is_in_database'])
                                <p class="text-muted small">
                                    El Owner no tiene registro en la BD. Reconstrúyalo con las credenciales de <code>.env</code>.
                                </p>

                                <form action="{{ route('owner.bootstrap.recover') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" name="password" id="password" class="form-control" required minlength="8">
                                        <div class="form-text">Mínimo 8 caracteres.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-database me-1"></i>Reconstruir Owner en BD
                                    </button>
                                </form>

                            @elseif($status['is_in_database'])
                                <p class="text-muted small mb-3">
                                    El Owner está registrado en BD. Cambie la contraseña si es necesario.
                                </p>

                                <form action="{{ route('owner.bootstrap.change-password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Contraseña Actual</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                                        <input type="password" name="password" id="new_password" class="form-control" required minlength="8">
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Confirmar Nueva</label>
                                        <input type="password" name="password_confirmation" id="new_password_confirmation" class="form-control" required minlength="8">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-1"></i>Cambiar Contraseña
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-shield-alt me-1 text-danger"></i> Seguridad</h6>
                    <ul class="text-muted small mb-0">
                        <li>El Owner Bootstrap solo acepta el email de <code>OWNER_EMAIL</code>.</li>
                        <li>Las credenciales no pueden modificarse desde el frontend.</li>
                        <li>La contraseña debe ser un hash bcrypt en <code>OWNER_PASSWORD_HASH</code>.</li>
                        <li>La reconstrucción tiene rate limiting (5 intentos por minuto).</li>
                        <li>Después de reconstruir, el owner se autentica normalmente desde BD.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
