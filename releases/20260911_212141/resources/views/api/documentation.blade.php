@extends('layouts.app')
@section('title', 'API Reference')
@section('topbar_class', 'px-4')
@section('topbar_extra')
    <span class="text-muted fw-normal" style="font-size: 0.8rem;">Documentación interactiva</span>
@endsection

@push('styles')
<style>
/* ============================================
   API Documentation — Professional Reference UI
   ============================================ */

:root {
    --api-sidebar-width: 280px;
    --api-method-get: #22c55e;
    --api-method-post: #3b82f6;
    --api-method-put: #f59e0b;
    --api-method-patch: #06b6d4;
    --api-method-delete: #ef4444;
    --api-card-bg: #ffffff;
    --api-card-border: #e2e8f0;
    --api-heading: #0f172a;
    --api-text: #475569;
    --api-muted: #94a3b8;
    --api-code-bg: #f8fafc;
    --api-code-border: #e2e8f0;
    --api-hover: #f8fafc;
    --api-active-bg: #eff6ff;
    --api-sidebar-bg: #f8fafc;
    --api-sidebar-border: #e2e8f0;
}

body.dark-mode {
    --api-card-bg: #0f172a;
    --api-card-border: #1e293b;
    --api-heading: #f1f5f9;
    --api-text: #cbd5e1;
    --api-muted: #64748b;
    --api-code-bg: #020617;
    --api-code-border: #1e293b;
    --api-hover: rgba(255,255,255,0.03);
    --api-active-bg: rgba(59,130,246,0.1);
    --api-sidebar-bg: #0f172a;
    --api-sidebar-border: #1e293b;
}

.api-docs-wrapper {
    display: flex;
    min-height: calc(100vh - 60px);
    position: relative;
}

/* ---- Sidebar ---- */
.api-sidebar {
    width: var(--api-sidebar-width);
    min-width: var(--api-sidebar-width);
    background: var(--api-sidebar-bg);
    border-right: 1px solid var(--api-sidebar-border);
    position: sticky;
    top: 0;
    height: calc(100vh - 60px);
    overflow-y: auto;
    z-index: 10;
    transition: transform 0.3s ease;
}

.api-sidebar-header {
    padding: 1.25rem 1rem 0.75rem;
    border-bottom: 1px solid var(--api-sidebar-border);
    position: sticky;
    top: 0;
    background: var(--api-sidebar-bg);
    z-index: 2;
}

.api-sidebar-header h5 {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--api-heading);
    margin-bottom: 0.75rem;
    letter-spacing: -0.01em;
}

.api-search {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--api-card-border);
    border-radius: 8px;
    font-size: 0.8rem;
    background: var(--api-card-bg);
    color: var(--api-text);
    outline: none;
    transition: border-color 0.15s;
}

.api-search:focus {
    border-color: var(--api-method-post);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.api-search::placeholder {
    color: var(--api-muted);
}

.api-sidebar-nav {
    padding: 0.75rem 0;
}

.api-nav-module {
    margin-bottom: 0.25rem;
}

.api-nav-module-header {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--api-muted);
    cursor: pointer;
    transition: color 0.15s;
    text-decoration: none;
}

.api-nav-module-header:hover {
    color: var(--api-heading);
}

.api-nav-module-header .collapse-icon {
    margin-left: auto;
    font-size: 0.65rem;
    transition: transform 0.2s;
}

.api-nav-module-header .collapse-icon.collapsed {
    transform: rotate(-90deg);
}

.api-nav-endpoints {
    overflow: hidden;
    transition: max-height 0.25s ease;
}

.api-nav-endpoint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 1rem 0.35rem 1.5rem;
    font-size: 0.78rem;
    color: var(--api-text);
    text-decoration: none;
    transition: all 0.12s;
    border-left: 2px solid transparent;
}

.api-nav-endpoint:hover {
    background: var(--api-hover);
    color: var(--api-heading);
}

.api-nav-endpoint.active {
    background: var(--api-active-bg);
    border-left-color: var(--api-method-post);
    color: var(--api-heading);
    font-weight: 600;
}

.nav-method-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.35rem;
    border-radius: 4px;
    min-width: 36px;
    text-align: center;
    letter-spacing: 0.02em;
    flex-shrink: 0;
}

.nav-path {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ---- Main Content ---- */
.api-main {
    flex: 1;
    min-width: 0;
    padding: 2rem 2.5rem;
    max-width: 1200px;
}

.api-header {
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--api-card-border);
}

.api-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--api-heading);
    letter-spacing: -0.02em;
    margin-bottom: 0.5rem;
}

.api-header p {
    color: var(--api-text);
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.api-base-url {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    background: var(--api-code-bg);
    border: 1px solid var(--api-card-border);
    border-radius: 8px;
    font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
    font-size: 0.82rem;
    color: var(--api-muted);
}

/* ---- Module Section ---- */
.api-module {
    margin-bottom: 3rem;
    scroll-margin-top: 1rem;
}

.api-module-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.api-module-header h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--api-heading);
    margin: 0;
}

.api-module-badge {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    background: var(--api-code-bg);
    border: 1px solid var(--api-card-border);
    color: var(--api-muted);
    white-space: nowrap;
}

.api-module-desc {
    color: var(--api-text);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

/* ---- Endpoint Card ---- */
.endpoint-card {
    background: var(--api-card-bg);
    border: 1px solid var(--api-card-border);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow 0.2s;
    scroll-margin-top: 1rem;
}

.endpoint-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

body.dark-mode .endpoint-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.endpoint-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    cursor: pointer;
    user-select: none;
    transition: background 0.12s;
}

.endpoint-card-header:hover {
    background: var(--api-hover);
}

.method-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.25rem 0.55rem;
    border-radius: 6px;
    min-width: 52px;
    text-align: center;
    letter-spacing: 0.03em;
    color: #fff;
    flex-shrink: 0;
}

.method-GET { background: var(--api-method-get); }
.method-POST { background: var(--api-method-post); }
.method-PUT { background: var(--api-method-put); }
.method-PATCH { background: var(--api-method-patch); }
.method-DELETE { background: var(--api-method-delete); }
.method-REST { background: #64748b; }
.method-OPTIONS { background: #64748b; }
.method-HEAD { background: #64748b; }

.endpoint-path {
    font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--api-heading);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.endpoint-path:hover {
    color: var(--api-method-post);
}

.endpoint-path .bi-files {
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.15s;
}

.endpoint-card-header:hover .endpoint-path .bi-files {
    opacity: 0.5;
}

.endpoint-card-header:hover .endpoint-path .bi-files:hover {
    opacity: 1;
}

.endpoint-action-title {
    font-size: 0.82rem;
    color: var(--api-text);
    margin-left: auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.endpoint-expand-icon {
    color: var(--api-muted);
    font-size: 0.7rem;
    flex-shrink: 0;
    transition: transform 0.2s;
}

.endpoint-card.expanded .endpoint-expand-icon {
    transform: rotate(180deg);
}

.endpoint-card-body {
    display: none;
    padding: 0 1.25rem 1.25rem;
    border-top: 1px solid var(--api-card-border);
}

.endpoint-card.expanded .endpoint-card-body {
    display: block;
}

.endpoint-summary {
    font-size: 0.88rem;
    color: var(--api-text);
    line-height: 1.6;
    margin-bottom: 1rem;
    padding-top: 1rem;
}

.endpoint-permissions {
    font-size: 0.8rem;
    color: var(--api-muted);
    margin-bottom: 1rem;
    padding: 0.5rem 0.75rem;
    background: var(--api-code-bg);
    border-radius: 8px;
    border: 1px solid var(--api-card-border);
    font-family: 'SF Mono', monospace;
}

/* ---- Detail Sections ---- */
.detail-section {
    margin-bottom: 1.25rem;
}

.detail-section:last-child {
    margin-bottom: 0;
}

.detail-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--api-muted);
    margin-bottom: 0.5rem;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}

.detail-table th,
.detail-table td {
    padding: 0.5rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid var(--api-card-border);
}

.detail-table th {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--api-muted);
    background: var(--api-code-bg);
}

.detail-table td {
    color: var(--api-text);
    vertical-align: top;
}

.detail-table tr:last-child td {
    border-bottom: none;
}

.detail-table .field-name {
    font-family: 'SF Mono', monospace;
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--api-heading);
    white-space: nowrap;
}

.detail-table .field-type {
    font-family: 'SF Mono', monospace;
    font-size: 0.75rem;
    color: var(--api-method-post);
    white-space: nowrap;
}

.detail-table .field-required {
    font-size: 0.7rem;
    font-weight: 700;
}

.required-yes { color: var(--api-method-delete); }
.required-no { color: var(--api-muted); }

/* ---- Code Blocks ---- */
.code-block-wrapper {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--api-card-border);
}

.code-block-wrapper pre {
    margin: 0;
    padding: 1rem;
    background: var(--api-code-bg);
    overflow-x: auto;
    font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
    font-size: 0.8rem;
    line-height: 1.5;
    color: var(--api-heading);
    max-height: 400px;
}

.copy-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.3rem 0.6rem;
    font-size: 0.7rem;
    border: 1px solid var(--api-card-border);
    border-radius: 6px;
    background: var(--api-card-bg);
    color: var(--api-muted);
    cursor: pointer;
    opacity: 0;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.code-block-wrapper:hover .copy-btn {
    opacity: 1;
}

.copy-btn:hover {
    background: var(--api-hover);
    color: var(--api-heading);
}

.copy-btn.copied {
    background: var(--api-method-get);
    color: #fff;
    border-color: var(--api-method-get);
}

/* ---- Field References ---- */
.field-ref-section {
    background: var(--api-card-bg);
    border: 1px solid var(--api-card-border);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
}

.field-ref-section h4 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--api-heading);
    margin-bottom: 0.75rem;
}

/* ---- Notas ---- */
.api-notas {
    background: var(--api-card-bg);
    border: 1px solid var(--api-card-border);
    border-radius: 12px;
    padding: 1.25rem;
    margin-top: 2rem;
}

.api-notas h3 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--api-heading);
    margin-bottom: 0.75rem;
}

.api-notas ul {
    margin: 0;
    padding-left: 1.25rem;
}

.api-notas li {
    font-size: 0.85rem;
    color: var(--api-text);
    line-height: 1.6;
    margin-bottom: 0.35rem;
}

/* ---- Mobile Overlay ---- */
.api-sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 9;
}

/* ---- Responsive ---- */
@media (max-width: 991.98px) {
    .api-sidebar {
        position: fixed;
        left: calc(-1 * var(--api-sidebar-width) - 20px);
        top: 0;
        height: 100vh;
        z-index: 100;
        box-shadow: 4px 0 24px rgba(0,0,0,0.15);
    }

    .api-sidebar.open {
        left: 0;
    }

    .api-sidebar-overlay.open {
        display: block;
    }

    .api-main {
        padding: 1.5rem 1rem;
    }

    .endpoint-card-header {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .endpoint-action-title {
        width: 100%;
        margin-left: 0;
        padding-left: 3.75rem;
    }

    .detail-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}

@media (max-width: 575.98px) {
    .api-main {
        padding: 1rem 0.75rem;
    }

    .api-header h1 {
        font-size: 1.35rem;
    }

    .nav-method-badge {
        min-width: 30px;
        font-size: 0.55rem;
    }
}

/* ---- Scrollbar styling ---- */
.api-sidebar::-webkit-scrollbar {
    width: 5px;
}
.api-sidebar::-webkit-scrollbar-thumb {
    background: var(--api-card-border);
    border-radius: 3px;
}
.api-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

/* ---- Search highlight ---- */
.api-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: none;
    color: var(--api-muted);
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.25rem;
    display: none;
}

.api-search-wrap {
    position: relative;
}

.api-search-wrap .api-search {
    padding-right: 2rem;
}

.api-search-wrap .api-search-clear.visible {
    display: block;
}

/* ---- Empty State ---- */
.api-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.api-empty i {
    font-size: 3rem;
    color: var(--api-muted);
    margin-bottom: 1rem;
}

.api-empty h3 {
    font-size: 1.25rem;
    color: var(--api-heading);
    margin-bottom: 0.5rem;
}

.api-empty p {
    color: var(--api-text);
    font-size: 0.9rem;
}

/* ---- Permission badge ---- */
.permission-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.15);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--api-method-delete);
    font-family: 'SF Mono', monospace;
}
</style>
@endpush

@section('content')
<div class="api-docs-wrapper">
    <div class="api-sidebar-overlay" id="apiSidebarOverlay" onclick="toggleApiSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="api-sidebar" id="apiSidebar">
        <div class="api-sidebar-header">
            <h5>API Reference</h5>
            <div class="api-search-wrap">
                <input type="text" class="api-search" id="apiSearchInput"
                       placeholder="Buscar endpoints..." autocomplete="off">
                <button class="api-search-clear" id="apiSearchClear" onclick="clearApiSearch()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <nav class="api-sidebar-nav" id="apiSidebarNav">
            @foreach($modules as $module)
            <div class="api-nav-module" data-module="{{ strtolower($module['name']) }}">
                <a href="#module-{{ preg_replace('/[^a-z0-9]+/', '-', strtolower($module['name'])) }}"
                   class="api-nav-module-header"
                   onclick="toggleModule(this); return false;">
                    {{ $module['name'] }}
                    <span class="endpoint-count" style="font-size:0.65rem;color:var(--api-muted);margin-left:0.5rem;">
                        ({{ count($module['endpoints']) }})
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="api-nav-endpoints" style="max-height: none;">
                    @foreach($module['endpoints'] as $endpoint)
                    @php
                        $moduleId = preg_replace('/[^a-z0-9]+/', '-', strtolower($module['name']));
                        $endpointId = $moduleId . '-endpoint-' . $endpoint['slug'];
                    @endphp
                    <a href="#{{ $endpointId }}"
                       class="api-nav-endpoint"
                       data-endpoint="{{ $endpointId }}"
                       data-method="{{ $endpoint['method'] }}">
                        <span class="nav-method-badge method-{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                        <span class="nav-path">{{ $endpoint['path'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="api-main" id="apiMain">
        <div class="api-header">
            <div class="d-flex align-items-center gap-2 mb-2">
                <button class="btn btn-sm btn-outline-secondary rounded-pill d-lg-none" onclick="toggleApiSidebar()">
                    <i class="bi bi-list me-1"></i> Módulos
                </button>
                <span class="api-base-url">
                    <i class="bi bi-link-45deg"></i> {{ request()->getScheme() }}://{{ request()->getHost() }}/api
                </span>
                <a href="{{ route('api.documentation.export') }}" class="btn btn-sm btn-outline-success rounded-pill ms-auto" title="Exportar documentación para IA">
                    <i class="bi bi-download me-1"></i> Exportar
                </a>
            </div>
            <h1>Documentación de la API</h1>
            <p>Referencia completa de todos los endpoints REST disponibles. Cada módulo incluye parámetros, ejemplos de solicitud y respuesta.</p>
        </div>

        @forelse($modules as $module)
        @php
            $moduleId = preg_replace('/[^a-z0-9]+/', '-', strtolower($module['name']));
        @endphp
        <section class="api-module" id="module-{{ $moduleId }}" data-module="{{ strtolower($module['name']) }}">
            <div class="api-module-header">
                <h2>{{ $module['name'] }}</h2>
                <span class="api-module-badge">{{ count($module['endpoints']) }} {{ Str::plural('endpoint', count($module['endpoints'])) }}</span>
            </div>
            @if($module['description'])
                <p class="api-module-desc">{{ $module['description'] }}</p>
            @endif

            @foreach($module['endpoints'] as $endpoint)
            @php
                $endpointId = $moduleId . '-endpoint-' . $endpoint['slug'];
            @endphp
            <div class="endpoint-card" id="{{ $endpointId }}">
                <div class="endpoint-card-header" onclick="toggleEndpoint(this)">
                    <span class="method-badge method-{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                    <span class="endpoint-path" title="Copiar endpoint" onclick="event.stopPropagation(); copyPath(this)" style="cursor:pointer;" data-path="{{ $endpoint['path'] }}">{{ $endpoint['path'] }} <i class="bi bi-files"></i></span>
                    <span class="endpoint-action-title">{{ $endpoint['action_title'] ?: $endpoint['name'] }}</span>
                    <i class="bi bi-chevron-down endpoint-expand-icon"></i>
                </div>
                <div class="endpoint-card-body">
                    @if($endpoint['summary'])
                        <div class="endpoint-summary">{{ $endpoint['summary'] }}</div>
                    @endif

                    @if($endpoint['permissions'])
                        <div class="endpoint-permissions">
                            <i class="bi bi-shield-lock me-1"></i> {{ $endpoint['permissions'] }}
                        </div>
                    @endif

                    {{-- Headers --}}
                    @if($endpoint['headers'])
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-headers me-1"></i> Headers
                        </div>
                        <div class="code-block-wrapper">
                            <button class="copy-btn" onclick="copyCode(this)"><i class="bi bi-clipboard"></i> Copy</button>
                            <pre>{{ $endpoint['headers'] }}</pre>
                        </div>
                    </div>
                    @endif

                    {{-- Query Parameters --}}
                    @if(!empty($endpoint['query_params']))
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-sliders me-1"></i> Query Parameters
                        </div>
                        <div class="table-responsive">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Parámetro</th>
                                        <th>Tipo</th>
                                        <th>Requerido</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($endpoint['query_params'] as $param)
                                    <tr>
                                        <td><code class="field-name">{{ $param['field'] ?? '' }}</code></td>
                                        <td><span class="field-type">{{ $param['type'] ?? '-' }}</span></td>
                                        <td>
                                            @php
                                                $req = $param['required'] ?? '';
                                                $isReq = str_contains($req, 'Sí') || str_contains($req, 'Yes') || str_contains($req, '*');
                                            @endphp
                                            <span class="field-required {{ $isReq ? 'required-yes' : 'required-no' }}">
                                                {{ $isReq ? 'Requerido' : 'Opcional' }}
                                            </span>
                                        </td>
                                        <td>{{ $param['description'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Path Parameters --}}
                    @if(!empty($endpoint['path_params']))
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-link me-1"></i> Path Parameters
                        </div>
                        <div class="table-responsive">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Parámetro</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($endpoint['path_params'] as $param)
                                    <tr>
                                        <td><code class="field-name">{{ $param['field'] ?? '' }}</code></td>
                                        <td><span class="field-type">{{ $param['type'] ?? '-' }}</span></td>
                                        <td>{{ $param['description'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Campos --}}
                    @if(!empty($endpoint['campos']))
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-list-check me-1"></i> Campos
                        </div>
                        <div class="table-responsive">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Tipo</th>
                                        <th>Requerido</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($endpoint['campos'] as $campo)
                                    <tr>
                                        <td><code class="field-name">{{ $campo['field'] ?? '' }}</code></td>
                                        <td><span class="field-type">{{ $campo['type'] ?? '-' }}</span></td>
                                        <td>
                                            @php
                                                $req = $campo['required'] ?? '';
                                                $isReq = str_contains($req, 'Sí') || str_contains($req, 'Yes') || str_contains($req, '*');
                                            @endphp
                                            <span class="field-required {{ $isReq ? 'required-yes' : 'required-no' }}">
                                                {{ $isReq ? 'Requerido' : 'Opcional' }}
                                            </span>
                                        </td>
                                        <td>{{ $campo['description'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Request Body JSON --}}
                    @if($endpoint['request_body_json'])
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Request Body
                        </div>
                        <div class="code-block-wrapper">
                            <button class="copy-btn" onclick="copyCode(this)"><i class="bi bi-clipboard"></i> Copy</button>
                            <pre>{{ $endpoint['request_body_json'] }}</pre>
                        </div>
                    </div>
                    @endif

                    {{-- Validations --}}
                    @if($endpoint['validations'])
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-check-circle me-1"></i> Validations
                        </div>
                        <div class="code-block-wrapper">
                            <button class="copy-btn" onclick="copyCode(this)"><i class="bi bi-clipboard"></i> Copy</button>
                            <pre>{{ $endpoint['validations'] }}</pre>
                        </div>
                    </div>
                    @endif

                    {{-- Responses --}}
                    @if(!empty($endpoint['responses']))
                        @foreach($endpoint['responses'] as $response)
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="bi bi-arrow-return-right me-1"></i>
                                Response
                                <span style="margin-left:0.5rem;font-weight:700;color:var(--api-method-get);">
                                    {{ $response['status_code'] }} {{ $response['status_text'] }}
                                </span>
                            </div>
                            @if($response['json'])
                            <div class="code-block-wrapper">
                                <button class="copy-btn" onclick="copyCode(this)"><i class="bi bi-clipboard"></i> Copy</button>
                                <pre>{{ $response['json'] }}</pre>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @endif

                    {{-- Example Request --}}
                    @if($endpoint['example_request'])
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="bi bi-code me-1"></i> Example Request
                        </div>
                        <div class="code-block-wrapper">
                            <button class="copy-btn" onclick="copyCode(this)"><i class="bi bi-clipboard"></i> Copy</button>
                            <pre>{{ $endpoint['example_request'] }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </section>
        @empty
        <div class="api-empty">
            <i class="bi bi-file-earmark-code"></i>
            <h3>No hay documentación disponible</h3>
            <p>Los módulos de API no se han encontrado. Verifica que los archivos markdown estén en <code>docs/api/modules/</code>.</p>
        </div>
        @endforelse

        {{-- Field References --}}
        @foreach($modules as $module)
            @if(!empty($module['field_references']))
            @php
                $moduleId = preg_replace('/[^a-z0-9]+/', '-', strtolower($module['name']));
            @endphp
            <section class="api-module" id="module-{{ $moduleId }}-fields">
                <div class="api-module-header">
                    <h2 style="font-size:1.15rem;">{{ $module['name'] }} — Field Reference</h2>
                </div>
                @foreach($module['field_references'] as $ref)
                <div class="field-ref-section">
                    <h4>{{ $ref['title'] }}</h4>
                    <div class="table-responsive">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ref['fields'] as $field)
                                <tr>
                                    <td><code class="field-name">{{ $field['name'] ?? $field['field'] ?? '' }}</code></td>
                                    <td><span class="field-type">{{ $field['type'] ?? '-' }}</span></td>
                                    <td>{{ $field['description'] ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </section>
            @endif
        @endforeach

        {{-- Notas --}}
        @foreach($modules as $module)
            @if($module['notas'])
            @php
                $moduleId = preg_replace('/[^a-z0-9]+/', '-', strtolower($module['name']));
            @endphp
            <div class="api-notas" id="module-{{ $moduleId }}-notas">
                <h3>{{ $module['name'] }} — Notas</h3>
                {!! Illuminate\Support\Str::markdown($module['notas']) !!}
            </div>
            @endif
        @endforeach

        <div style="height:4rem;"></div>
    </main>
</div>
@endsection

@push('scripts')
<script>
// ---- Toggle endpoint card ----
function toggleEndpoint(header) {
    const card = header.closest('.endpoint-card');
    const body = card.querySelector('.endpoint-card-body');
    const isExpanded = card.classList.toggle('expanded');
    if (isExpanded) {
        body.style.display = 'block';
    } else {
        body.style.display = 'none';
    }
}

// ---- Toggle sidebar on mobile ----
function toggleApiSidebar() {
    document.getElementById('apiSidebar').classList.toggle('open');
    document.getElementById('apiSidebarOverlay').classList.toggle('open');
}

// ---- Copy endpoint path ----
function copyPath(el) {
    const path = el.getAttribute('data-path') || el.textContent.trim();
    copyToClipboard(path, el);
}

// ---- General copy helper with fallback ----
function copyToClipboard(text, el) {
    const done = () => {
        const orig = el.innerHTML;
        el.innerHTML = '<i class="bi bi-check-lg me-1"></i>' + text;
        el.style.color = 'var(--api-method-get)';
        setTimeout(() => {
            el.innerHTML = orig;
            el.style.color = '';
        }, 1500);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(() => fallbackCopy(text, done));
    } else {
        fallbackCopy(text, done);
    }
}

function fallbackCopy(text, cb) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    ta.style.top = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        if (cb) cb();
    } catch (e) {}
    document.body.removeChild(ta);
}

// ---- Copy code ----
function copyCode(btn) {
    const pre = btn.closest('.code-block-wrapper').querySelector('pre');
    const text = pre.textContent;
    const done = () => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copiado';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = orig;
            btn.classList.remove('copied');
        }, 2000);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(() => fallbackCopy(text, done));
    } else {
        fallbackCopy(text, done);
    }
}

// ---- Search / Filter ----
const searchInput = document.getElementById('apiSearchInput');
const searchClear = document.getElementById('apiSearchClear');

searchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    searchClear.classList.toggle('visible', q.length > 0);

    document.querySelectorAll('.api-nav-module').forEach(mod => {
        const moduleName = mod.dataset.module;
        const endpoints = mod.querySelectorAll('.api-nav-endpoint');
        let moduleHasMatch = false;

        endpoints.forEach(ep => {
            const text = ep.textContent.toLowerCase();
            if (q === '' || text.includes(q)) {
                ep.style.display = 'flex';
                moduleHasMatch = true;
            } else {
                ep.style.display = 'none';
            }
        });

        // Show module if any endpoint matches, or module name matches
        const modHeader = mod.querySelector('.api-nav-module-header');
        const modText = modHeader.textContent.toLowerCase();
        if (q === '' || moduleHasMatch || modText.includes(q)) {
            mod.style.display = 'block';
            // Expand if searching
            if (q !== '') {
                const endpointsContainer = mod.querySelector('.api-nav-endpoints');
                endpointsContainer.style.maxHeight = endpointsContainer.scrollHeight + 'px';
            } else {
                resetModuleCollapse(mod);
            }
        } else {
            mod.style.display = 'none';
        }
    });

    // Also highlight/hide main content
    document.querySelectorAll('.api-module').forEach(mod => {
        const modName = mod.dataset.module;
        const cards = mod.querySelectorAll('.endpoint-card');
        let modMatch = false;

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (q === '' || text.includes(q)) {
                card.style.display = '';
                modMatch = true;
            } else {
                card.style.display = 'none';
            }
        });

        const headerText = mod.querySelector('.api-module-header h2')?.textContent.toLowerCase() || '';
        if (q === '' || modMatch || headerText.includes(q)) {
            mod.style.display = '';
        } else {
            mod.style.display = 'none';
        }
    });
});

function clearApiSearch() {
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
    searchInput.focus();
}

// ---- Reset module collapse state ----
function resetModuleCollapse(mod) {
    const container = mod.querySelector('.api-nav-endpoints');
    const icon = mod.querySelector('.collapse-icon');
    container.style.maxHeight = container.scrollHeight + 'px';
    if (icon) icon.classList.remove('collapsed');
}

// ---- Toggle module collapse ----
function toggleModule(header) {
    const mod = header.closest('.api-nav-module');
    const container = mod.querySelector('.api-nav-endpoints');
    const icon = header.querySelector('.collapse-icon');

    if (container.style.maxHeight !== '0px' && container.style.maxHeight !== '0') {
        container.style.maxHeight = '0px';
        if (icon) icon.classList.add('collapsed');
    } else {
        container.style.maxHeight = container.scrollHeight + 'px';
        if (icon) icon.classList.remove('collapsed');
    }
}

// ---- IntersectionObserver for active sidebar link ----
(function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                document.querySelectorAll('.api-nav-endpoint').forEach(link => {
                    const isActive = link.getAttribute('href') === '#' + id;
                    link.classList.toggle('active', isActive);
                });
            }
        });
    }, {
        rootMargin: '-80px 0px -60% 0px',
        threshold: 0
    });

    document.querySelectorAll('.endpoint-card').forEach(card => {
        observer.observe(card);
    });
})();

// ---- Auto-expand card on anchor load ----
(function() {
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target && target.classList.contains('endpoint-card')) {
            const body = target.querySelector('.endpoint-card-body');
            target.classList.add('expanded');
            if (body) body.style.display = 'block';
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
        if (target && target.closest('.api-nav-module')) {
            const mod = target.closest('.api-nav-module');
            const container = mod.querySelector('.api-nav-endpoints');
            const icon = mod.querySelector('.collapse-icon');
            container.style.maxHeight = container.scrollHeight + 'px';
            if (icon) icon.classList.remove('collapsed');
        }
    }
})();

// ---- Smooth scroll for sidebar links ----
document.querySelectorAll('.api-nav-endpoint').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                // Expand card if not already
                if (target.classList.contains('endpoint-card') && !target.classList.contains('expanded')) {
                    const body = target.querySelector('.endpoint-card-body');
                    target.classList.add('expanded');
                    if (body) body.style.display = 'block';
                }
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, '', href);

                // Close mobile sidebar
                const sidebar = document.getElementById('apiSidebar');
                if (sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    document.getElementById('apiSidebarOverlay').classList.remove('open');
                }
            }
        }
    });
});

// ---- Module nav header links - smooth scroll + expand ----
document.querySelectorAll('.api-nav-module-header').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, '', href);
            }
        }
    });
});

// ---- Keyboard shortcut: Ctrl+K / Cmd+K for search ----
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }
});
</script>
@endpush
