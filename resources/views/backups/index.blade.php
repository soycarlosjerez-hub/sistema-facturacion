@extends('layouts.app')

@section('title', 'Backups')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    border-radius: 1rem; padding: 2rem; color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(5,150,105,0.4);
    position: relative; overflow: hidden;
}
.premium-header::after {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.filter-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}
.btn-icon-hover {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50% !important;
    padding: 0;
    transition: all 0.2s;
}
.btn-icon-hover:hover { transform: scale(1.15); }
.avatar-circle {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 1.2rem;
}
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cloud-arrow-down text-success me-2"></i>Respaldo de Base de Datos</h2>
            <p class="text-muted mb-0">Gestión de copias de seguridad</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('backups.config') }}" class="btn btn-outline-light rounded-pill">
                <i class="bi bi-gear me-1"></i> Configuración
            </a>
            <form method="POST" action="{{ route('backups.store') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-white text-success rounded-pill px-4 fw-bold shadow-sm" onclick="return confirm('¿Iniciar backup ahora?')">
                    <i class="bi bi-shield-check me-1"></i> Crear Backup
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="avatar-circle bg-soft-success mx-auto mb-2">
                        <i class="bi bi-hdd-stack"></i>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Total Backups</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ $backups->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="avatar-circle bg-soft-info mx-auto mb-2">
                        <i class="bi bi-hard-drive"></i>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Espacio Total</small>
                    <h4 class="fw-bold mb-0 mt-1">
                        @php
                            $bytes = $totalSize;
                            $units = ['B','KB','MB','GB'];
                            $i = 0;
                            while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                        @endphp
                        {{ round($bytes, 2) }} {{ $units[$i] }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="avatar-circle bg-soft-warning mx-auto mb-2">
                        <i class="bi bi-person"></i>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Manuales</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ $countManual }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="avatar-circle bg-soft-dark mx-auto mb-2" style="background:rgba(15,23,42,0.08);color:#0f172a;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:.5px;">Automáticos</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ $countAuto }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Historial de Backups</h5>
            <small class="text-muted">Los backups se almacenan en storage/app/backups/</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Archivo</th>
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
                                    <span class="badge bg-dark bg-opacity-10 text-dark rounded-pill px-2 py-1"><i class="bi bi-clock-history me-1"></i>Automático</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1"><i class="bi bi-person me-1"></i>Manual</span>
                                @endif
                            </td>
                            <td>
                                @if($backup->status === 'completado')
                                    <span class="status-badge bg-success bg-opacity-10 text-success">Completado</span>
                                @else
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger">Fallido</span>
                                @endif
                            </td>
                            <td><span class="fw-semibold">{{ $backup->sizeForHumans() }}</span></td>
                            <td><small class="text-muted">{{ $backup->user?->name ?? 'Sistema' }}</small></td>
                            <td><small>{{ $backup->created_at->format('d/m/Y h:i A') }}</small></td>
                            <td class="text-end pe-4">
                                @if($backup->status === 'completado')
                                <a href="{{ route('backups.download', $backup) }}" class="btn btn-sm btn-outline-success rounded-pill btn-icon-hover" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-icon-hover" 
                                        onclick="confirmDelete('{{ route('backups.destroy', $backup) }}', '{{ $backup->filename }}')"
                                        title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">No hay backups aún</p>
                                <form method="POST" action="{{ route('backups.store') }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill mt-2">
                                        <i class="bi bi-shield-check me-1"></i> Crear primer backup
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $backups->links() }}
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-circle bg-soft-info flex-shrink-0" style="width:52px;height:52px;font-size:1.3rem;">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Acerca de los Backups</h6>
                    <p class="text-muted small mb-0">
                        Los backups se realizan con <code>mysqldump</code> e incluyen: estructura de tablas, datos, procedimientos almacenados y eventos.
                        Se conservan automáticamente los últimos 30 días. Los archivos se almacenan en <code>storage/app/backups/</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.btn-white {
    background: white; color: #059669; border: none;
}
.btn-white:hover {
    background: #f0fdf4; color: #047857;
}
</style>
<script>
function confirmDelete(url, filename) {
    Swal.fire({
        title: '¿Eliminar backup?',
        text: `Se eliminará: "${filename}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
