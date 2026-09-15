@extends('layouts.app')

@section('title', 'Backups del Sistema')

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
                    <i class="bi bi-database-fill-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Backups del Sistema</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-lock me-1"></i>
                        <span>Gestión de copias de seguridad</span>
                        <span class="divider">·</span>
                        <i class="bi bi-hdd-stack me-1"></i>
                        <span>{{ $backups->total() }} respaldo(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.backups.config') }}" class="ui-btn ui-btn-primary ui-btn-sm ui-btn-pill">
                    <i class="bi bi-gear me-1"></i> Configuración
                </a>
                <button type="button" class="ui-btn ui-btn-solid ui-btn-pill" onclick="toggleBackupDropdown(event)" id="newBackupBtn">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Nuevo Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Dropdown de Backup — fuera del header para evitar stacking context -->
    <div id="backupDropdownMenu" class="backup-dropdown-menu" style="display:none; position:fixed; z-index:100000;">
        <div class="backup-dropdown-content">
            <div class="backup-dropdown-item" onclick="createBackup(false)">
                <i class="bi bi-file-earmark-text"></i>
                <span>SQL Completo</span>
            </div>
            <div class="backup-dropdown-item" onclick="createBackup(true)">
                <i class="bi bi-file-earmark-zip"></i>
                <span>Comprimido (~75% menos)</span>
            </div>
            <div class="backup-dropdown-divider"></div>
            <div class="backup-dropdown-item" onclick="showCustomBackupModal()">
                <i class="bi bi-pencil"></i>
                <span>Con nombre personalizado</span>
            </div>
        </div>
    </div>

    <form id="backupForm" method="POST" action="{{ route('owner.backups.store') }}" style="display:none;">
        @csrf
        <input type="hidden" name="compress" id="compressInput" value="0">
    </form>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total Backups</div>
                    <div class="ui-stat-value">{{ $backups->total() }}</div>
                    <div class="ui-stat-sub">respaldos almacenados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Espacio Total</div>
                    <div class="ui-stat-value" style="font-size:1.25rem;">
                        @php
                            $bytes = $totalSize;
                            $units = ['B','KB','MB','GB'];
                            $i = 0;
                            while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                        @endphp
                        {{ round($bytes, 2) }} {{ $units[$i] }}
                    </div>
                    <div class="ui-stat-sub">almacenamiento usado</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Últimos 7 Días</div>
                    <div class="ui-stat-value" style="color:#06b6d4 !important;">{{ $last7Days }}</div>
                    <div class="ui-stat-sub">respaldos recientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Último Backup</div>
                    <div class="ui-stat-value" style="font-size:1rem;">
                        {{ $lastBackup ? $lastBackup->created_at->diffForHumans() : 'N/A' }}
                    </div>
                    <div class="ui-stat-sub">
                        {{ $lastBackup ? date('d/m/Y H:i', strtotime($lastBackup->created_at)) : 'Sin backups' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-list-check"></i> Historial de Backups
        </div>
        <div class="ui-card-subtitle">Los backups se almacenan en <code>storage/app/backups/</code></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="table ui-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Archivo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Tamaño</th>
                            <th>Creado por</th>
                            <th>Fecha</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold small font-monospace">{{ $backup->filename }}</span>
                                    @if($backup->notes)
                                        <br><small class="text-muted">{{ $backup->notes }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->type === 'automatico')
                                        <span class="ui-badge ui-badge-info"><i class="bi bi-clock-history me-1"></i>Automático</span>
                                    @elseif($backup->type === 'restore')
                                        <span class="ui-badge ui-badge-danger"><i class="bi bi-arrow-repeat me-1"></i>Restore</span>
                                    @else
                                        <span class="ui-badge ui-badge-primary"><i class="bi bi-person me-1"></i>Manual</span>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->status === 'completado')
                                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Completado</span>
                                    @else
                                        <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle me-1"></i>Fallido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->size_bytes > 0)
                                        <span class="fw-semibold">{{ $backup->sizeForHumans() }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($backup->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center" style="width:28px;height:28px;border-radius:50%;background:rgba(245,158,11,.15);">
                                                <span class="fw-bold small" style="color:#f59e0b;">{{ strtoupper(substr($backup->user->name, 0, 1)) }}</span>
                                            </div>
                                            <small class="text-muted">{{ $backup->user->name }}</small>
                                        </div>
                                    @else
                                        <small class="text-muted">Sistema</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $backup->created_at ? date('d/m/Y H:i', strtotime($backup->created_at)) : 'N/A' }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($backup->status === 'completado' && $backup->size_bytes > 0 && !str_starts_with($backup->filename, 'RESTORE:'))
                                            <a href="{{ route('owner.backups.download', $backup) }}" class="ui-action ui-action-print" title="Descargar" data-bs-toggle="tooltip">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button type="button" class="ui-action ui-action-delete"
                                                    onclick="UI.confirm.action({title:'Restaurar Backup', text:'Esto reemplazará TODOS los datos actuales. ¿Continuar?', icon:'warning', color:'#ef4444', confirmText:'Sí, Restaurar', onSubmit:function(){ document.getElementById('restoreForm_{{ $backup->id }}').submit(); }})"
                                                    title="Restaurar" data-bs-toggle="tooltip">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <form id="restoreForm_{{ $backup->id }}" action="{{ route('owner.backups.restore', $backup) }}" method="POST" style="display:none;">
                                                @csrf
                                                <input type="hidden" name="confirm" value="1">
                                            </form>
                                        @endif
                                        <button type="button" class="ui-action ui-action-delete"
                                                onclick="UI.confirm.delete('{{ route('owner.backups.destroy', $backup) }}', '{{ $backup->filename }}')"
                                                title="Eliminar" data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ui-empty-state py-5">
                                        <i class="bi bi-inbox"></i>
                                        <p>No hay backups aún</p>
                                        <form method="POST" action="{{ route('owner.backups.store') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill mt-2">
                                                <i class="bi bi-cloud-arrow-up me-1"></i> Crear primer backup
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ui-card-body p-3 border-top">
            {{ $backups->links() }}
        </div>
    </div>

    <div class="ui-card mt-3" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(245,158,11,.1);">
                    <i class="bi bi-info-circle text-warning fs-5" style="color:#f59e0b;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color:#f59e0b;">Acerca de los Backups</h6>
                    <p class="text-muted small mb-2">
                        Los backups se realizan con <code class="text-dark bg-light px-2 py-1 rounded">mysqldump</code> e incluyen: estructura de tablas, datos, procedimientos almacenados, triggers y eventos.
                    </p>
                    <p class="text-muted small mb-2">
                        <strong>Backups automáticos:</strong> Se generan todos los días a medianoche vía Laravel Scheduler y a las 8:00 AM vía CRON del sistema.
                    </p>
                    <p class="text-muted small mb-0">
                        Los archivos se almacenan en <code class="text-dark bg-light px-2 py-1 rounded">storage/app/backups/</code>. Los backups se conservan automáticamente los últimos <strong>30 días</strong>. Los archivos .sql.gz comprimidos ocupan ~75% menos espacio.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mt-3" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(239,68,68,.1);">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-danger">Restauración de Datos</h6>
                    <p class="text-muted small mb-0">
                        Restaurar un backup <strong>reemplazará TODOS los datos actuales</strong> de la base de datos. Esta acción no se puede deshacer.
                        Siempre crea un backup antes de restaurar para mantener un historial.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customNameModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:1px solid rgba(245,158,11,.2);">
            <form method="POST" action="{{ route('owner.backups.store') }}" id="customNameForm">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Nombre Personalizado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del backup</label>
                        <input type="text" name="filename" class="form-control" placeholder="ejemplo: backup_anterior_migration" required>
                        <small class="text-muted">Sin extensión (.sql o .sql.gz), solo letras, números y guiones</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Compresión</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="compress" id="compressNo" value="0" checked>
                            <label class="form-check-label" for="compressNo">Sin compresión (mayor tamaño, más rápido)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="compress" id="compressYes" value="1">
                            <label class="form-check-label" for="compressYes">Comprimir (.sql.gz) - ~75% menos espacio</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="ui-btn ui-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Crear Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Backup dropdown functions
function toggleBackupDropdown(event) {
    event.stopPropagation();
    var menu = document.getElementById('backupDropdownMenu');
    if (menu.style.display === 'none') {
        menu.style.display = 'block';
        var btn = document.getElementById('newBackupBtn');
        var rect = btn.getBoundingClientRect();
        menu.style.top = rect.bottom + window.scrollY + 8 + 'px';
        menu.style.left = rect.left + window.scrollX + 'px';
    } else {
        menu.style.display = 'none';
    }
}

function createBackup(compress) {
    document.getElementById('compressInput').value = compress ? '1' : '0';
    document.getElementById('backupForm').submit();
}

function showCustomBackupModal() {
    new bootstrap.Modal(document.getElementById('customNameModal')).show();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    var menu = document.getElementById('backupDropdownMenu');
    var btn = document.getElementById('newBackupBtn');
    if (menu.style.display === 'block' && !menu.contains(event.target) && !btn.contains(event.target)) {
        menu.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
    document.getElementById('customNameModal').addEventListener('show.bs.modal', function () {
        setTimeout(function() {
            document.querySelector('#customNameModal input[name="filename"]').focus();
        }, 500);
    });
});

UI.confirm = {
    delete: function(url, label) {
        UI.confirm.action({
            title: '¿Eliminar backup?',
            text: 'Se eliminará: "' + label + '"',
            icon: 'warning',
            color: '#ef4444',
            confirmText: 'Sí, eliminar',
            onSubmit: function() {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    },
    action: function(opts) {
        Swal.fire({
            title: opts.title,
            text: opts.text || '',
            icon: opts.icon || 'info',
            showCancelButton: true,
            confirmButtonColor: opts.color || '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: opts.confirmText || 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed && opts.onSubmit) {
                opts.onSubmit();
            }
        });
    }
};
</script>
@endpush

@push('styles')
<style>
.backup-dropdown-menu {
    pointer-events: auto;
}
.backup-dropdown-content {
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(20px);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
    overflow: hidden;
    min-width: 220px;
    padding: 8px 0;
}
.backup-dropdown-item {
    padding: 12px 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: .9rem;
    font-weight: 500;
    color: #1e293b;
    transition: all .15s ease;
}
.backup-dropdown-item:hover {
    background: rgba(245,158,11,.08);
    color: #d97706;
}
.backup-dropdown-item i {
    font-size: 1.1rem;
    color: #64748b;
}
.backup-dropdown-item:hover i {
    color: #d97706;
}
.backup-dropdown-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 4px 0;
}
</style>
@endpush
@endsection
