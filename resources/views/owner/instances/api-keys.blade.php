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
                    <button class="ui-btn ui-btn-solid btn-sm" type="button" onclick="copyNewKey()" style="background:#1e293b;border-color:#1e293b">Copiar</button>
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
                <button type="button" class="ui-btn ui-btn-solid" style="background:#f59e0b;border-color:#f59e0b;color:#000" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                    <i class="bi bi-plus-lg me-2"></i>Nueva API Key
                </button>
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-key text-warning me-2"></i>Claves de API</h5>
            <small class="text-muted">{{ $apiKeys->count() }} clave(s)</small>
        </div>
        <div class="card-body p-4 pt-0">
            @if($apiKeys->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Clave</th>
                            <th>Estado</th>
                            <th>&Uacute;ltimo uso</th>
                            <th>Creado</th>
                            <th>Creado por</th>
                            <th class="text-end">Acci&oacute;n</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apiKeys as $key)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $key->name }}</span>
                            </td>
                            <td>
                                <code class="user-select-all small">{{ $key->mask() }}</code>
                            </td>
                            <td>
                                <span class="ui-badge ui-badge-{{ $key->is_active ? 'success' : 'neutral' }} rounded-pill text-uppercase" style="font-size:.65rem;">
                                    {{ $key->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
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
                                    <form method="POST" action="{{ route('owner.instances.api-keys.regenerate', [$instance, $key]) }}" onsubmit="return UI.confirm.delete('&iquest;Regenerar la clave &quot;{{ $key->name }}&quot;? La clave actual dejar&aacute; de funcionar inmediatamente.')" class="d-inline">
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
                                    <form method="POST" action="{{ route('owner.instances.api-keys.destroy', [$instance, $key]) }}" onsubmit="return UI.confirm.delete('&iquest;Eliminar permanentemente la clave &quot;{{ $key->name }}&quot;? Esta acci&oacute;n no se puede deshacer.')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-0">No hay claves de API para esta instancia.</p>
                <button type="button" class="ui-btn ui-btn-solid btn-sm mt-2" style="background:#f59e0b;border-color:#f59e0b;color:#000" data-bs-toggle="modal" data-bs-target="#createKeyModal">
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
                        <label class="ui-label fw-bold">Nombre descriptivo <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="ui-input rounded-4" placeholder="Ej: integracion-shopify, webhook-externo" required maxlength="255">
                        <div class="form-text">Usa un nombre que identifique para qu&eacute; se usar&aacute; esta clave.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="ui-btn ui-btn-primary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-solid" style="background:#f59e0b;border-color:#f59e0b;color:#000">
                        <i class="bi bi-plus-lg me-1"></i>Generar Clave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
