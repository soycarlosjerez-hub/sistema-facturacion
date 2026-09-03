@extends('layouts.app')

@section('title', 'Configuración de Backups')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Configuración de Backups</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-lock me-1"></i>
                        <span>Información del sistema de respaldo</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.backups.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm ui-btn-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Backups
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-card" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center p-4">
                    <i class="bi bi-folder2-open fs-1 mb-3" style="color:#f59e0b;"></i>
                    <h6 class="fw-bold">Directorio de Backups</h6>
                    <p class="text-muted small mb-0 font-monospace bg-light p-2 rounded d-inline-block">{{ $backupDir }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center p-4">
                    <i class="bi bi-file-earmark-check fs-1 mb-3" style="color:#f59e0b;"></i>
                    <h6 class="fw-bold">Total Backups</h6>
                    <p class="display-6 fw-bold mb-0">{{ $backupCount }}</p>
                    <p class="text-muted small mb-0">respaldos almacenados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center p-4">
                    <i class="bi bi-hdd-stack fs-1 mb-3" style="color:#f59e0b;"></i>
                    <h6 class="fw-bold">Espacio Usado</h6>
                    <p class="display-6 fw-bold mb-0">
                        @php
                            $bytes = $totalSize;
                            $units = ['B','KB','MB','GB'];
                            $i = 0;
                            while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                        @endphp
                        {{ round($bytes, 2) }} {{ $units[$i] }}
                    </p>
                    <p class="text-muted small mb-0">almacenamiento total</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock me-2" style="color:#f59e0b;"></i>Último Backup
                    </h6>
                    @if($lastBackup)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Archivo</span>
                                <span class="font-monospace small">{{ $lastBackup->filename }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Tamaño</span>
                                <span>{{ $lastBackup->sizeForHumans() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Tipo</span>
                                <span>
                                    @if($lastBackup->type === 'automatico')
                                        <span class="ui-badge ui-badge-info">Automático</span>
                                    @else
                                        <span class="ui-badge ui-badge-primary">Manual</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Fecha</span>
                                <span>{{ $lastBackup->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Creado por</span>
                                <span>{{ $lastBackup->user?->name ?? 'Sistema' }}</span>
                            </div>
                        </div>
                        @if($lastBackup->status === 'fallido')
                            <div class="alert alert-danger py-2 px-3 mb-0 small">
                                <i class="bi bi-x-circle me-2"></i>
                                Último estado: {{ $lastBackup->notes ?: 'Error desconocido' }}
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No hay backups registrados</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2" style="color:#f59e0b;"></i>Mysqldump
                    </h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Ruta</span>
                            <span class="font-monospace small">{{ $mysqldumpPath }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Estado</span>
                            <span>
                                @if(file_exists($mysqldumpPath) || str_contains($mysqldumpPath, 'mysqldump'))
                                    <span class="ui-badge ui-badge-success">Disponible</span>
                                @else
                                    <span class="ui-badge ui-badge-danger">No encontrado</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-terminal me-2" style="color:#f59e0b;"></i>Comandos Artisan Disponibles
            </h6>
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:backup</code>
                        <small class="text-muted">Crea un backup SQL sin compresión</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:backup --compress</code>
                        <small class="text-muted">Crea un backup comprimido (.sql.gz) - ~75% menos espacio</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:backup --filename mi_backup</code>
                        <small class="text-muted">Backup con nombre personalizado</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:restore archivo.sql</code>
                        <small class="text-muted">Restaura desde un archivo de backup</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:restore archivo.sql.gz</code>
                        <small class="text-muted">Restaura desde archivo comprimido (auto-detected)</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-dark text-light p-3 rounded">
                        <code class="d-block mb-2">php artisan app:list-backups</code>
                        <small class="text-muted">Lista todos los backups registrados</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mt-3" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-calendar-check me-2" style="color:#f59e0b;"></i>Backups Automatizados
            </h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Configuración</th>
                            <th>Horario</th>
                            <th>Tipo</th>
                            <th>Retención</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Laravel Scheduler</strong></td>
                            <td>Cada 1 minuto</td>
                            <td>
                                <span class="ui-badge ui-badge-info">Medianoche</span>
                            </td>
                            <td>30 días</td>
                        </tr>
                        <tr>
                            <td><strong>CRON del Sistema</strong></td>
                            <td>8:00 AM</td>
                            <td>
                                <span class="ui-badge ui-badge-info">Automático</span>
                            </td>
                            <td>7 días</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
