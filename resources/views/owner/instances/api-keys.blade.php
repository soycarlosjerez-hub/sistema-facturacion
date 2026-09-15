@extends('layouts.app')
@section('title', "API Keys — {$instance->nombre}")

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    @if(session('new_api_key'))
    <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 mb-4 shadow" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                <i class="bi bi-key-fill text-dark fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="d-block mb-1">API Key generada exitosamente</strong>
                <p class="mb-2 small">Esta key solo se muestra <strong>una vez</strong>. C&oacute;piala ahora y gu&aacute;rdala en un lugar seguro.</p>
                <div class="ui-input-group input-group-sm mb-1">
                    <input type="text" class="ui-input font-monospace bg-white" value="{{ session('new_api_key') }}" readonly id="newApiKeyInput">
                    <button class="ui-btn ui-btn-sm btn-sm" type="button" onclick="copyNewKey()" style="background:#1e293b;border-color:#1e293b;color:#fff">Copiar</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
    function copyNewKey() {
        var input = document.getElementById('newApiKeyInput');
        input.select(); input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        var btn = input.nextElementSibling; btn.textContent = 'Copiado!';
        setTimeout(function(){ btn.textContent = 'Copiar'; }, 2000);
    }
    </script>
    @endif

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">API Keys</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; {{ $instance->slug }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <button type="button" class="ui-btn ui-btn-sm btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#000" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                    <i class="bi bi-plus-lg me-2"></i>Nueva API Key
                </button>
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-sm">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-key text-warning me-2"></i>Claves de API</h5>
            <div class="d-flex flex-wrap gap-2 align-items-center w-100 w-md-auto">
                <span class="text-muted small">{{ $apiKeys->total() }} clave(s)</span>
                <form method="GET" action="{{ route('owner.instances.api-keys', $instance) }}" class="d-flex gap-2 flex-grow-1">
                    <input type="search" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre o creador..." value="{{ request('search') }}" aria-label="Buscar API Key">
                    <select name="status" class="form-select form-select-sm" style="max-width:140px">
                        <option value="">Estado</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                    <select name="trashed" class="form-select form-select-sm" style="max-width:130px">
                        <option value="">Recurso</option>
                        <option value="with" {{ request('trashed') === 'with' ? 'selected' : '' }}>Con eliminadas</option>
                        <option value="only" {{ request('trashed') === 'only' ? 'selected' : '' }}>Solo eliminadas</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Filtrar"><i class="bi bi-funnel"></i></button>
                    @if(request('search') || request('status') || request('trashed'))
                    <a href="{{ route('owner.instances.api-keys', $instance) }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-4 pt-0">
            @if($apiKeys->isNotEmpty() || $apiKeys->total() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:20%">Nombre</th>
                            <th style="width:25%">Clave</th>
                            <th style="width:10%">Estado</th>
                            <th style="width:15%">&Uacute;ltimo uso</th>
                            <th style="width:12%">Creado</th>
                            <th style="width:13%">Creado por</th>
                            <th class="text-end" style="width:10%">Acci&oacute;n</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apiKeys as $key)
                        <tr class="{{ $key->trashed() ? 'table-warning' : '' }}">
                            <td>
                                <span class="fw-bold">{{ $key->name }}</span>
                                @if($key->trashed())
                                <span class="badge bg-secondary ms-1 small">Eliminada</span>
                                @endif
                            </td>
                            <td>
                                <code class="user-select-all small">{{ $key->key_raw ?? $key->mask() }}</code>
                            </td>
                            <td>
                                @if($key->trashed())
                                <span class="badge bg-danger rounded-pill text-uppercase" style="font-size:.65rem;">Eliminada</span>
                                @else
                                <span class="ui-badge ui-badge-{{ $key->is_active ? 'success' : 'neutral' }} rounded-pill text-uppercase" style="font-size:.65rem;">
                                    {{ $key->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $key->last_used_at?->diffForHumans() ?? 'Nunca' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $key->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <small>{{ $key->creator?->name ?? '—' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if(!$key->trashed())
                                    <button type="button" class="ui-action ui-action-view" title="Ver clave completa" onclick="revealApiKey('{{ route('owner.instances.api-keys.reveal', [$instance, $key]) }}', '{{ $key->id }}')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <form method="POST" action="{{ route('owner.instances.api-keys.regenerate', [$instance, $key]) }}" onsubmit="return UI.confirm.delete(this, '¿Regenerar la clave \"{{ $key->name }}\"? La clave actual dejará de funcionar inmediatamente.')" class="d-inline">
                                        @csrf
                                        <button type="submit" class="ui-action ui-action-edit" title="Regenerar">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('owner.instances.api-keys.toggle', [$instance, $key]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="ui-action ui-action-{{ $key->is_active ? 'view' : 'edit' }}" title="{{ $key->is_active ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi bi-{{ $key->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('owner.instances.api-keys.destroy', [$instance, $key]) }}" onsubmit="return false;" data-delete-url="{{ route('owner.instances.api-keys.destroy', [$instance, $key]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ui-action ui-action-delete" title="Eliminar" onclick="event.preventDefault(); event.stopPropagation(); confirmDelete(this.closest('form'));">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('owner.instances.api-keys.destroy', [$instance, $key]) }}" onsubmit="return false;" data-delete-url="{{ route('owner.instances.api-keys.destroy', [$instance, $key]) }}" data-bs-toggle="tooltip" title="Restaurar/Recuperar" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ui-action ui-action-delete" style="background:#198754;border-color:#198754" title="Restaurar" onclick="event.preventDefault(); event.stopPropagation(); confirmDelete(this.closest('form'));">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('owner.instances.api-keys.force', $instance) }}" onsubmit="return false;" data-delete-url="{{ route('owner.instances.api-keys.force', $instance) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="apiKeyId" value="{{ $key->id }}">
                                        <button type="submit" class="ui-action ui-action-delete" style="background:#dc3545;border-color:#dc3545" title="Eliminar para siempre" onclick="event.preventDefault(); event.stopPropagation(); confirmForceDelete(this.closest('form'));">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $apiKeys->links() }}
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-1">No hay claves de API para esta instancia.</p>
                <p class="small text-muted mb-3">Las claves de API se usan para autenticar servicios externos de forma segura.</p>
                <button type="button" class="ui-btn ui-btn-sm btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#000" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                    <i class="bi bi-plus-lg me-1"></i>Crear Primera Clave
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="createKeyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('owner.instances.api-keys.generate', $instance) }}">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-key text-warning me-2"></i>Nueva API Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold">Nombre descriptivo <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm rounded-4" placeholder="Ej: integracion-shopify, webhook-externo" required maxlength="255">
                        <div class="text-muted small mt-1">Usa un nombre que identifique para qu&eacute; se usar&aacute; esta clave.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="ui-btn ui-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-sm btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#000">
                        <i class="bi bi-plus-lg me-1"></i>Generar Clave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reveal Key Modal -->
<div class="modal fade" id="revealKeyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-key-fill text-warning me-2"></i>Clave Completa de API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="fw-bold text-muted small text-uppercase">Nombre</label>
                    <div class="fw-bold" id="revealKeyName">—</div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted small text-uppercase">Clave API <span class="text-danger">*</span></label>
                    <div class="ui-input-group input-group-sm">
                        <input type="text" class="form-control font-monospace bg-white" id="revealKeyValue" readonly>
                        <button class="ui-btn ui-btn-sm btn-sm" type="button" onclick="copyRevealedKey()" style="background:#1e293b;border-color:#1e293b;color:#fff">
                            <i class="bi bi-clipboard me-1"></i>Copiar
                        </button>
                    </div>
                    <div class="text-danger small mt-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Esta clave es sensible. No la compartas ni la almacenes en código fuente.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="ui-btn ui-btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function revealApiKey(url, keyId) {
    var modal = new bootstrap.Modal(document.getElementById('revealKeyModal'));
    var nameEl = document.getElementById('revealKeyName');
    var keyEl = document.getElementById('revealKeyValue');

    nameEl.textContent = 'Cargando...';
    keyEl.value = '';

    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        nameEl.textContent = data.name || '—';
        keyEl.value = data.key || '';
        modal.show();
    })
    .catch(function() {
        nameEl.textContent = 'Error al cargar';
        modal.show();
    });
}

function copyRevealedKey() {
    var input = document.getElementById('revealKeyValue');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);
    var btn = input.nextElementSibling;
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check me-1"></i>Copiado';
    btn.style.background = '#198754';
    btn.style.borderColor = '#198754';
    setTimeout(function() {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
        btn.style.borderColor = '';
    }, 2000);
}
</script>
@endsection
