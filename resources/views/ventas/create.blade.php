@extends('layouts.app')

@section('title', 'Terminal de Ventas (POS)')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --accent: #3b82f6;
    --accent-rgb: 59,130,246;
    --accent-hover: #2563eb;
}
</style>
@endpush

@section('fullbleed')
@php
    $dgiiAmbiente = config('dgii.ambiente_actual', 'sandbox');
    $dgiiSandbox = config('dgii.simular_dgii', true);
@endphp

<style>
    /* ============ Base POS Layout ============ */
:root {
    --pos-accent: #3b82f6;
    --pos-accent-2: #2563eb;
    --pos-accent-3: #1d4ed8;
    --pos-success: #10b981;
    --pos-warning: #f59e0b;
    --pos-danger: #ef4444;
    --pos-bg-light: #f8fafc;
    --pos-bg-dark: #020617;
    --pos-card-light: rgba(255, 255, 255, 0.03);
    --pos-card-dark: rgba(255, 255, 255, 0.08);
    --pos-card-border: rgba(255, 255, 255, 0.1);
    --pos-topbar-light: rgba(255, 255, 255, 0.92);
    --pos-topbar-dark: rgba(15, 23, 42, 0.85);
    --pos-search-light: rgba(255, 255, 255, 0.06);
    --pos-search-dark: rgba(255, 255, 255, 0.12);
    --pos-search-focus-light: rgba(59, 130, 246, 0.08);
    --pos-search-focus-dark: rgba(59, 130, 246, 0.12);
    --pos-dropdown-light: rgba(255, 255, 255, 0.9);
    --pos-dropdown-dark: rgba(30, 41, 59, 0.9);
    --pos-accent-soft: rgba(var(--pos-accent-rgb), 0.1);
    --pos-accent2-soft: rgba(var(--pos-accent2-rgb), 0.1);
    --pos-accent3-soft: rgba(var(--pos-accent3-rgb), 0.1);
    --pos-success-soft: rgba(var(--pos-success-rgb), 0.1);
    --pos-warning-soft: rgba(var(--pos-warning-rgb), 0.1);
    --pos-danger-soft: rgba(var(--pos-danger-rgb), 0.1);
    --pos-text-light: #1e293b;
    --pos-text-dark: #f1f5f9;
    --pos-text-muted-light: #64748b;
    --pos-text-muted-dark: #94a3b8;
    --pos-border-light: rgba(0, 0, 0, 0.1);
    --pos-border-dark: rgba(255, 255, 255, 0.15);
    --pos-shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --pos-shadow-dark: 0 4px 6px -1px rgba(255, 255, 255, 0.1), 0 2px 4px -1px rgba(255, 255, 255, 0.06);
    --pos-accent-rgb: 59, 130, 246;
    --pos-accent2-rgb: 37, 99, 235;
    --pos-success-rgb: 16, 185, 129;
    --pos-warning-rgb: 245, 158, 11;
    --pos-danger-rgb: 239, 68, 68;
    --pos-text-rgb: 30, 41, 59;
}

body.dark-mode {
    --pos-bg: var(--pos-bg-dark);
    --pos-card: var(--pos-card-dark);
    --pos-topbar: var(--pos-topbar-dark);
    --pos-search: var(--pos-search-dark);
    --pos-search-focus: var(--pos-search-focus-dark);
    --pos-dropdown: var(--pos-dropdown-dark);
    --pos-card-border: var(--pos-border-dark);
    --pos-text: var(--pos-text-dark);
    --pos-text-muted: var(--pos-text-muted-dark);
    --pos-border: var(--pos-border-dark);
    --pos-shadow: var(--pos-shadow-dark);
}

body:not(.dark-mode) {
    --pos-bg: var(--pos-bg-light);
    --pos-card: var(--pos-card-light);
    --pos-topbar: var(--pos-topbar-light);
    --pos-search: var(--pos-search-light);
    --pos-search-focus: var(--pos-search-focus-light);
    --pos-dropdown: var(--pos-dropdown-light);
    --pos-card-border: var(--pos-border-light);
    --pos-text: var(--pos-text-light);
    --pos-text-muted: var(--pos-text-muted-light);
    --pos-border: var(--pos-border-light);
    --pos-shadow: var(--pos-shadow-light);
}
    
    /* Apply the variables */
    .pos-app {
        background: var(--pos-bg);
        color: var(--pos-text);
    }
    
    .pos-topbar {
        background: var(--pos-topbar);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--pos-border);
    }
    
    .pos-topbar .caja-tag {
        background: rgba(var(--pos-success-rgb), 0.15);
        color: var(--pos-success);
        border-color: rgba(var(--pos-success-rgb), 0.3);
    }
    
    .pos-stat .label {
        color: var(--pos-text-muted);
    }
    
    .pos-stat .value {
        color: var(--pos-text);
    }
    
    .pos-stat .value.success {
        color: var(--pos-success);
    }
    
    .pos-keyhint {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
    }
    
    .pos-search {
        background: rgba(255,255,255,0.06);
        border: 2px solid var(--pos-border);
        color: var(--pos-text);
    }
    
    .pos-search::placeholder {
        color: var(--pos-text-muted);
    }
    
.pos-search:focus {
    border-color: var(--pos-accent);
    background: var(--pos-search-focus);
    box-shadow: 0 0 0 4px rgba(var(--pos-accent-rgb), 0.15);
}
    
    .pos-search.scanner-flash {
        animation: scanFlash 0.5s ease;
    }
    
    @keyframes scanFlash {
        0% { background: rgba(var(--pos-accent-rgb), 0.3); border-color: var(--pos-accent); }
        100% { background: rgba(var(--pos-accent-rgb), 0.05); border-color: var(--pos-accent); }
    }
    
    .pos-search-icon,
    .pos-search-clear {
        color: var(--pos-text-muted);
    }
    
    .pos-search-clear:hover {
        background: rgba(var(--pos-danger-rgb), 0.2);
        color: #fca5a5;
    }
    
    .search-mode-toggle {
        background: rgba(255,255,255,0.04);
        border-radius: 12px;
        padding: 4px;
        gap: 2px;
    }
    
    .search-mode-toggle button {
        color: var(--pos-text-muted);
    }
    
    .search-mode-toggle button.active {
        background: var(--pos-accent);
        color: white;
    }
    
    .search-results-dropdown {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    }
    
    .search-results-dropdown .res-item {
        border-bottom: 1px solid var(--pos-border);
    }
    
    .search-results-dropdown .res-item:hover,
    .search-results-dropdown .res-item.active {
        background: var(--pos-accent-soft);
    }
    
    .search-results-dropdown .res-item .res-meta,
    .search-results-dropdown .res-item .res-empty {
        color: var(--pos-text-muted);
    }
    
    .pos-tabs {
        gap: 6px;
    }
    
    .pos-tab {
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--pos-border);
        color: var(--pos-text-muted);
    }
    
    .pos-tab:hover {
        background: rgba(255,255,255,0.08);
        color: var(--pos-text);
    }
    
    .pos-tab.active {
        background: var(--pos-accent);
        border-color: var(--pos-accent);
        color: white;
    }
    
    .pos-tab .badge-count {
        background: rgba(0,0,0,0.25);
        color: inherit;
    }
    
    .pos-products {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
    }
    
    .pos-product-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        border-radius: 14px;
        color: var(--pos-text);
    }
    
    .pos-product-card:hover {
        transform: translateY(-2px);
        border-color: var(--pos-accent);
        background: rgba(var(--pos-accent-rgb), 0.05);
        box-shadow: 0 8px 24px rgba(var(--pos-accent-rgb), 0.15);
    }
    
    .pos-product-card:active {
        transform: scale(0.97);
    }
    
        .pos-product-card .ppc-img {
            background: var(--pos-card);
        }
    
    .pos-product-card .ppc-price {
        color: var(--pos-accent);
    }
    
    .pos-product-card .ppc-stock {
        background: rgba(0,0,0,0.6);
    }
    
    .pos-product-card .ppc-stock.ok { color: var(--pos-success); }
    .pos-product-card .ppc-stock.low { color: var(--pos-warning); }
    .pos-product-card .ppc-stock.crit { color: var(--pos-danger); }
    .pos-product-card .ppc-stock.out { color: var(--pos-text-muted); }
    
    .pos-product-card.out-of-stock {
        opacity: 0.45;
        cursor: not-allowed;
    }
    
    .pos-product-card.out-of-stock:hover {
        transform: none;
    }
    
    .pos-cart {
        padding: 4px;
    }
    
    .pos-cart-empty {
        color: var(--pos-text-muted);
    }
    
    .cart-item {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        margin-bottom: 6px;
    }
    
    .cart-item:hover {
        background: rgba(255,255,255,0.05);
    }
    
    .cart-item.removing {
        animation: cartOut 0.3s ease forwards;
    }
    
    .cart-item.adding {
        animation: cartIn 0.3s ease;
    }
    
    @keyframes cartOut {
        to { opacity: 0; transform: translateX(40px); height: 0; padding: 0; margin: 0; border: 0; }
    }
    
    @keyframes cartIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .cart-item .ci-img {
        background: #0f172a;
    }
    
    .cart-item .ci-name {
        font-weight: 700;
    }
    
    .cart-item .ci-meta {
        color: var(--pos-text-muted);
    }
    
    .cart-item .ci-qty button {
        color: var(--pos-text);
    }

    .cart-item .ci-discount {
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .cart-item .discount-input-group {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--pos-border);
        border-radius: 4px;
        overflow: hidden;
        background: var(--pos-surface-2);
    }
    .cart-item .discount-toggle {
        background: transparent;
        border: 0;
        color: var(--pos-text-muted);
        padding: 2px 6px;
        font-size: 0.7rem;
        font-weight: 700;
        cursor: pointer;
        border-right: 1px solid var(--pos-border);
        transition: all 0.15s;
        min-width: 22px;
    }
    .cart-item .discount-toggle:hover { background: var(--pos-hover); }
    .cart-item .discount-toggle.active {
        background: var(--pos-accent);
        color: white;
    }
    .cart-item .discount-input {
        width: 60px;
        border: 0;
        background: transparent;
        color: var(--pos-text);
        font-size: 0.78rem;
        padding: 2px 4px;
        text-align: right;
    }
    .cart-item .discount-input:focus { outline: 1px solid var(--pos-accent); outline-offset: -1px; }
    .cart-item .discount-applied {
        color: var(--pos-danger, #dc3545);
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .cart-item .ci-qty button:hover:not(:disabled) {
        background: var(--pos-accent);
    }
    
    .cart-item .ci-qty button:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    .cart-item .ci-qty .qty-val {
        font-weight: 700;
    }
    
    .cart-item .ci-right {
        text-align: right;
    }
    
    .cart-item .ci-subtotal {
        color: var(--pos-accent);
    }
    
    .cart-item .ci-itbis {
        color: var(--pos-text-muted);
    }
    
    .cart-item .ci-remove {
        color: var(--pos-text-muted);
    }
    
    .cart-item .ci-remove:hover {
        background: rgba(var(--pos-danger-rgb), 0.2);
    }
    
    .pos-right .pr-section {
        border-bottom: 1px solid var(--pos-border);
    }
    
    .pos-right .pr-section-title {
        color: var(--pos-text-muted);
    }
    
    .pos-right .pr-section-title i {
        color: var(--pos-accent);
    }
    
    .cliente-select {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        color: var(--pos-text);
    }
    
    .cliente-select:focus {
        outline: none;
        border-color: var(--pos-accent);
    }
    
    .cliente-pill {
        background: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
    }
    
    .cliente-pill.warn {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }
    
    .cliente-pill.danger {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
    }
    
    .comprobante-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }
    
    .comprobante-card {
        background: rgba(255,255,255,0.04);
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 12px 6px;
        text-align: center;
        transition: all 0.15s;
    }
    
    .comprobante-card:hover {
        background: rgba(255,255,255,0.08);
    }
    
    .comprobante-card.active {
        border-color: var(--pos-accent);
        background: var(--pos-accent-soft);
    }
    
    .comprobante-card i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 4px;
        color: var(--pos-accent);
    }
    
    .comprobante-card .ct-name {
        font-weight: 700;
        font-size: 0.78rem;
    }
    
    .comprobante-card .ct-sub {
        font-size: 0.65rem;
        color: var(--pos-text-muted);
    }
    
    .ncf-select {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        color: var(--pos-text);
        padding: 8px 10px;
        font-size: 0.85rem;
        margin-top: 6px;
    }
    
    .ecf-hint {
        margin-top: 6px;
        padding: 8px 10px;
        background: rgba(59, 130, 246, 0.1);
        border-left: 3px solid var(--pos-accent);
        border-radius: 8px;
        font-size: 0.75rem;
        color: #93c5fd;
    }
    
    .totals-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 0.85rem;
    }
    
    .totals-row .label {
        color: var(--pos-text-muted);
    }
    
    .totals-row .val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    
    .descuento-input {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        border-radius: 8px;
        color: var(--pos-text);
        padding: 4px 8px;
        font-size: 0.8rem;
        width: 100px;
        text-align: right;
    }
    
    .descuento-input:focus {
        outline: none;
        border-color: var(--pos-accent);
    }
    
    .total-display {
        text-align: center;
        padding: 16px 12px;
        background: linear-gradient(135deg, rgba(var(--pos-accent-rgb), 0.15) 0%, rgba(var(--pos-accent2-rgb), 0.1) 100%);
        border-radius: 14px;
        margin-top: 10px;
        border: 1px solid rgba(var(--pos-accent-rgb), 0.3);
    }
    
    .total-display .td-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #93c5fd;
        font-weight: 700;
    }
    
    .total-display .td-amount {
        font-size: 2.4rem;
        font-weight: 900;
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
        margin-top: 4px;
    }

    .pos-app {
        background: var(--pos-bg);
        color: var(--pos-text);
        height: calc(100vh - 70px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

/* ============ Top Bar ============ */
.pos-topbar {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 20px;
    background:
        linear-gradient(135deg, rgba(var(--pos-accent-rgb),.12), rgba(var(--pos-accent-rgb),.04)),
        var(--pos-topbar);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--pos-border);
    flex-shrink: 0;
    position: relative;
    overflow: visible;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, 0s);
}
.pos-topbar .caja-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: rgba(var(--pos-success-rgb), 0.15);
    color: var(--pos-success);
    border-radius: 999px;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1px solid rgba(var(--pos-success-rgb), 0.3);
}
.pos-topbar .caja-tag .pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--pos-success);
    animation: pulse-dot 1.5s ease-in-out infinite;
}
    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        50% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    }

/* ============ Topbar Buttons Visibility Fix ============ */
.pos-topbar .btn {
    --tb-font-size: 0.82rem;
    --tb-padding-x: 10px;
    --tb-padding-y: 5px;
    --tb-border-width: 1.5px;
    font-size: var(--tb-font-size);
    padding: var(--tb-padding-y) var(--tb-padding-x);
    border-width: var(--tb-border-width);
    transition: all 0.15s ease;
}
.pos-topbar .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.pos-topbar .btn:active {
    transform: translateY(0) scale(0.97);
}

/* Light mode topbar buttons */
body:not(.dark-mode) .pos-topbar .btn-outline-light {
    background: rgba(59,130,246,0.08);
    border-color: rgba(59,130,246,0.35);
    color: #3b82f6;
}
body:not(.dark-mode) .pos-topbar .btn-outline-light:hover {
    background: rgba(59,130,246,0.18);
    border-color: #3b82f6;
    color: #2563eb;
}
body:not(.dark-mode) .pos-topbar .btn-outline-secondary {
    background: rgba(100,116,139,0.08);
    border-color: rgba(100,116,139,0.4);
    color: #475569;
}
body:not(.dark-mode) .pos-topbar .btn-outline-secondary:hover {
    background: rgba(100,116,139,0.18);
    border-color: #64748b;
    color: #334155;
}
body:not(.dark-mode) .pos-topbar .btn-outline-danger {
    background: rgba(239,68,68,0.08);
    border-color: rgba(239,68,68,0.4);
    color: #dc2626;
}
body:not(.dark-mode) .pos-topbar .btn-outline-danger:hover {
    background: rgba(239,68,68,0.18);
    border-color: #ef4444;
    color: #b91c1c;
}

/* Dark mode topbar buttons */
body.dark-mode .pos-topbar .btn-outline-light {
    background: rgba(59,130,246,0.15);
    border-color: rgba(59,130,246,0.4);
    color: #93c5fd;
}
body.dark-mode .pos-topbar .btn-outline-light:hover {
    background: rgba(59,130,246,0.3);
    border-color: #3b82f6;
    color: #bfdbfe;
}
body.dark-mode .pos-topbar .btn-outline-secondary {
    background: rgba(148,163,184,0.1);
    border-color: rgba(148,163,184,0.35);
    color: #cbd5e1;
}
body.dark-mode .pos-topbar .btn-outline-secondary:hover {
    background: rgba(148,163,184,0.2);
    border-color: #94a3b8;
    color: #e2e8f0;
}
body.dark-mode .pos-topbar .btn-outline-danger {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.4);
    color: #fca5a5;
}
body.dark-mode .pos-topbar .btn-outline-danger:hover {
    background: rgba(239,68,68,0.3);
    border-color: #ef4444;
    color: #fecaca;
}

    .pos-stat {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1.1;
    }
.pos-stat .label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--pos-text-muted);
    font-weight: 600;
}
.pos-stat .value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--pos-text);
    font-variant-numeric: tabular-nums;
}
.pos-stat .value.success { color: var(--pos-success); }

    .pos-topbar .spacer { flex: 1; }

.pos-keyhint {
    font-size: 0.7rem;
    color: var(--pos-text-muted);
    background: var(--pos-card);
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid var(--pos-border);
    transition: all 0.15s;
}
.pos-keyhint:hover {
    background: var(--pos-accent-soft);
    border-color: var(--pos-accent);
    color: var(--pos-text);
}
.pos-keyhint kbd {
    background: rgba(var(--pos-text-rgb), 0.1);
    padding: 1px 5px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 0.7rem;
    color: var(--pos-text);
}

/* Topbar select styling */
.pos-topbar select.form-select-sm {
    background: var(--pos-card) !important;
    border-color: var(--pos-border) !important;
    color: var(--pos-text) !important;
    font-size: 0.78rem;
    padding: 4px 10px;
    border-radius: 8px;
    max-width: 160px;
    transition: all 0.15s;
}
.pos-topbar select.form-select-sm:hover {
    border-color: var(--pos-accent);
}
.pos-topbar select.form-select-sm:focus {
    border-color: var(--pos-accent) !important;
    box-shadow: 0 0 0 3px rgba(var(--pos-accent-rgb), 0.15) !important;
}

/* ============ Body grid ============ */
.pos-body {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 0;
    overflow: hidden;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, .05s);
}
.pos-left {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 16px;
    gap: 12px;
    min-width: 0;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, .1s);
}
.pos-right {
    background: var(--pos-topbar);
    backdrop-filter: blur(20px);
    border-left: 1px solid var(--pos-border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: uiSlideUp .5s ease both;
    animation-delay: var(--delay, .15s);
}

    /* ============ Search Section ============ */
    .pos-search-wrap {
        position: relative;
        flex-shrink: 0;
        animation: uiSlideUp .5s ease both;
        animation-delay: var(--delay, .2s);
    }
.pos-search {
    width: 100%;
    padding: 16px 56px 16px 56px;
    font-size: 1.3rem;
    font-weight: 600;
    background: var(--pos-search);
    border: 2px solid var(--pos-border);
    border-radius: 16px;
    color: var(--pos-text);
    outline: none;
    transition: all 0.2s;
    font-family: 'Inter', -apple-system, sans-serif;
}
    .pos-search::placeholder { color: var(--pos-text-muted); font-weight: 400; }
    .pos-search:focus {
        border-color: var(--pos-accent);
        background: rgba(59, 130, 246, 0.05);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }
    .pos-search.scanner-flash {
        animation: scanFlash 0.5s ease;
    }
    @keyframes scanFlash {
        0% { background: rgba(59, 130, 246, 0.3); border-color: var(--pos-accent); }
        100% { background: rgba(59, 130, 246, 0.05); border-color: var(--pos-accent); }
    }
    .pos-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pos-text-muted);
        font-size: 1.4rem;
    }
    .pos-search-clear {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.08);
        border: none;
        color: var(--pos-text-muted);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .pos-search-clear:hover { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

    .search-mode-toggle {
        display: inline-flex;
        background: rgba(255,255,255,0.04);
        border-radius: 12px;
        padding: 4px;
        gap: 2px;
    }
    .search-mode-toggle button {
        background: transparent;
        border: none;
        color: var(--pos-text-muted);
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }
    .search-mode-toggle button.active {
        background: var(--pos-accent);
        color: white;
    }

    .search-results-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        max-height: 60vh;
        overflow-y: auto;
        background: var(--pos-dropdown);
        border: 1px solid var(--pos-border);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        z-index: 100;
        display: none;
    }
    .search-results-dropdown.show { display: block; }
    .search-results-dropdown .res-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid var(--pos-border);
        transition: background 0.15s;
    }
    .search-results-dropdown .res-item:hover,
    .search-results-dropdown .res-item.active {
        background: var(--pos-accent-soft);
    }
    .search-results-dropdown .res-item:last-child { border-bottom: none; }
    .search-results-dropdown .res-item .res-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        background: var(--pos-card);
        flex-shrink: 0;
    }
    .search-results-dropdown .res-item .res-info { flex: 1; min-width: 0; }
    .search-results-dropdown .res-item .res-name {
        font-weight: 700;
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .search-results-dropdown .res-item .res-meta {
        font-size: 0.75rem;
        color: var(--pos-text-muted);
        margin-top: 2px;
    }
    .search-results-dropdown .res-item .res-right {
        text-align: right;
        flex-shrink: 0;
    }
    .search-results-dropdown .res-item .res-price {
        font-weight: 800;
        color: var(--pos-accent);
        font-size: 1rem;
        font-variant-numeric: tabular-nums;
    }
    .search-results-dropdown .res-empty {
        padding: 40px 20px;
        text-align: center;
        color: var(--pos-text-muted);
    }
    .search-results-dropdown .res-empty i { font-size: 2.5rem; opacity: 0.5; display: block; margin-bottom: 8px; }

    /* ============ Filter tabs ============ */
    .pos-tabs {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
        flex-wrap: wrap;
        animation: uiSlideUp .5s ease both;
        animation-delay: var(--delay, .25s);
    }
    .pos-tab {
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--pos-border);
        color: var(--pos-text-muted);
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .pos-tab:hover { color: var(--pos-text); background: rgba(255,255,255,0.08); }
    .pos-tab.active {
        background: var(--pos-accent);
        border-color: var(--pos-accent);
        color: white;
    }
    .pos-tab .badge-count {
        background: rgba(0,0,0,0.25);
        color: inherit;
        padding: 1px 6px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* ============ Products Grid ============ */
    .pos-products {
        flex: 1;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
        align-content: start;
        padding: 4px;
    }
    .pos-products::-webkit-scrollbar { width: 6px; }
    .pos-products::-webkit-scrollbar-thumb { background: var(--pos-border); border-radius: 3px; }

    .pos-product-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--pos-border);
        border-radius: 14px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.18s;
        position: relative;
        display: flex;
        flex-direction: column;
        text-align: left;
        color: var(--pos-text);
    }
    .pos-product-card:hover {
        transform: translateY(-2px);
        border-color: var(--pos-accent);
        background: rgba(59, 130, 246, 0.05);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
    }
    .pos-product-card:active { transform: scale(0.97); }
    .pos-product-card .ppc-img {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 10px;
        object-fit: cover;
        background: #0f172a;
        margin-bottom: 8px;
    }
    .pos-product-card .ppc-name {
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
    }
    .pos-product-card .ppc-price {
        font-size: 1rem;
        font-weight: 800;
        color: var(--pos-accent);
        margin-top: 6px;
        font-variant-numeric: tabular-nums;
    }
    .pos-product-card .ppc-stock {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 0.65rem;
        padding: 2px 7px;
        border-radius: 6px;
        font-weight: 700;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
    }
    .pos-product-card .ppc-stock.ok { color: #6ee7b7; }
    .pos-product-card .ppc-stock.low { color: #fbbf24; }
    .pos-product-card .ppc-stock.crit { color: #fca5a5; }
    .pos-product-card .ppc-stock.out { color: #94a3b8; }
    .pos-product-card.out-of-stock { opacity: 0.45; cursor: not-allowed; }
    .pos-product-card.out-of-stock:hover { transform: none; }

    /* ============ Cart ============ */
    .pos-cart {
        flex: 1;
        overflow-y: auto;
        padding: 4px;
    }
    .pos-cart::-webkit-scrollbar { width: 6px; }
    .pos-cart::-webkit-scrollbar-thumb { background: var(--pos-border); border-radius: 3px; }

    .pos-cart-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--pos-text-muted);
        text-align: center;
        padding: 40px;
    }
    .pos-cart-empty i { font-size: 4rem; opacity: 0.3; margin-bottom: 16px; }
    .pos-cart-empty h5 { font-weight: 700; }
    .pos-cart-empty p { font-size: 0.85rem; max-width: 280px; }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--pos-border);
        border-radius: 12px;
        margin-bottom: 6px;
        transition: all 0.2s;
    }
    .cart-item:hover { background: rgba(255,255,255,0.05); }
    .cart-item.removing { animation: cartOut 0.3s ease forwards; }
    .cart-item.adding { animation: cartIn 0.3s ease; }
    @keyframes cartOut {
        to { opacity: 0; transform: translateX(40px); height: 0; padding: 0; margin: 0; border: 0; }
    }
    @keyframes cartIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .cart-item .ci-img {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        object-fit: cover;
        background: #0f172a;
        flex-shrink: 0;
    }
    .cart-item .ci-info { flex: 1; min-width: 0; }
    .cart-item .ci-name {
        font-size: 0.85rem;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cart-item .ci-meta {
        font-size: 0.7rem;
        color: var(--pos-text-muted);
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cart-item .ci-qty {
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,0.06);
        border-radius: 8px;
        overflow: hidden;
    }
    .cart-item .ci-qty button {
        background: transparent;
        border: none;
        color: var(--pos-text);
        width: 26px;
        height: 26px;
        font-weight: 700;
        cursor: pointer;
    }
    .cart-item .ci-qty button:hover:not(:disabled) { background: var(--pos-accent); }
    .cart-item .ci-qty button:disabled { opacity: 0.3; cursor: not-allowed; }
    .cart-item .ci-qty .qty-val {
        min-width: 30px;
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .cart-item .ci-right { text-align: right; flex-shrink: 0; }
    .cart-item .ci-subtotal {
        font-weight: 800;
        color: var(--pos-accent);
        font-size: 0.95rem;
        font-variant-numeric: tabular-nums;
    }
    .cart-item .ci-itbis {
        font-size: 0.65rem;
        color: var(--pos-text-muted);
    }
    .cart-item .ci-remove {
        background: transparent;
        border: none;
        color: var(--pos-text-muted);
        padding: 4px;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.15s;
    }
    .cart-item .ci-remove:hover { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

    /* ============ Right column ============ */
    .pos-right .pr-section {
        padding: 14px 18px;
        border-bottom: 1px solid var(--pos-border);
    }
    .pos-right .pr-section:last-child { border-bottom: none; }
    .pos-right .pr-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pos-text-muted);
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pos-right .pr-section-title i { color: var(--pos-accent); }

    /* Cliente */
    .cliente-select {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        border-radius: 10px;
        color: var(--pos-text);
        padding: 10px 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
    }
    .cliente-select:focus { outline: none; border-color: var(--pos-accent); }
    .cliente-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        background: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
    }
    .cliente-pill.warn { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
    .cliente-pill.danger { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    /* Comprobante cards */
    .comprobante-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }
    .comprobante-card {
        background: rgba(255,255,255,0.04);
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 12px 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .comprobante-card:hover { background: rgba(255,255,255,0.08); }
    .comprobante-card.active {
        border-color: var(--pos-accent);
        background: var(--pos-accent-soft);
    }
    .comprobante-card i { font-size: 1.5rem; display: block; margin-bottom: 4px; color: var(--pos-accent); }
    .comprobante-card .ct-name { font-weight: 700; font-size: 0.78rem; }
    .comprobante-card .ct-sub { font-size: 0.65rem; color: var(--pos-text-muted); }

    .ncf-select {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        border-radius: 10px;
        color: var(--pos-text);
        padding: 8px 10px;
        font-size: 0.85rem;
        margin-top: 6px;
    }
    .ecf-hint {
        margin-top: 6px;
        padding: 8px 10px;
        background: rgba(59, 130, 246, 0.1);
        border-left: 3px solid var(--pos-accent);
        border-radius: 8px;
        font-size: 0.75rem;
        color: #93c5fd;
    }

    /* Totals */
    .totals-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 0.85rem;
    }
    .totals-row .label { color: var(--pos-text-muted); }
    .totals-row .val { font-weight: 700; font-variant-numeric: tabular-nums; }
    .descuento-input {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        border-radius: 8px;
        color: var(--pos-text);
        padding: 4px 8px;
        font-size: 0.8rem;
        width: 100px;
        text-align: right;
    }
    .descuento-input:focus { outline: none; border-color: var(--pos-accent); }

    .total-display {
        text-align: center;
        padding: 16px 12px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
        border-radius: 14px;
        margin-top: 10px;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .total-display .td-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #93c5fd;
        font-weight: 700;
    }
    .total-display .td-amount {
        font-size: 2.4rem;
        font-weight: 900;
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
        margin-top: 4px;
    }

    /* Payment buttons */
    .payment-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .btn-pay {
        background: var(--pos-success);
        border: none;
        color: white;
        border-radius: 14px;
        padding: 14px 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        position: relative;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-pay:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
    .btn-pay:active:not(:disabled) { transform: scale(0.97); }
    .btn-pay:disabled { opacity: 0.4; cursor: not-allowed; }
    .btn-pay i { font-size: 1.6rem; }
    .btn-pay .pay-shortcut {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(0,0,0,0.3);
        font-size: 0.6rem;
        padding: 1px 5px;
        border-radius: 4px;
        font-weight: 700;
    }
    .btn-pay.tarjeta { background: #3b82f6; }
    .btn-pay.transferencia { background: #6366f1; }
    .btn-pay.fiado { background: #f59e0b; color: #1f2937; }
    .btn-pay.cuenta_abierta { background: #8b5cf6; }
    .btn-pay.mixto { background: #64748b; }
    .btn-pay.full { grid-column: span 2; }

    /* DGII badge */
.dgii-badge {
    font-size: 0.65rem;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.dgii-badge.sandbox { background: rgba(var(--pos-warning-rgb), 0.15); color: var(--pos-warning); border: 1px solid rgba(var(--pos-warning-rgb), 0.3); }
.dgii-badge.produccion { background: rgba(var(--pos-danger-rgb), 0.15); color: var(--pos-danger); border: 1px solid rgba(var(--pos-danger-rgb), 0.3); }
.dgii-badge.qa { background: rgba(var(--pos-accent-rgb), 0.15); color: var(--pos-accent); border: 1px solid rgba(var(--pos-accent-rgb), 0.3); }

    /* Cart count badge */
    .cart-count-badge {
        background: var(--pos-accent);
        color: white;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 700;
        min-width: 22px;
        text-align: center;
    }
    .cart-count-badge.pulse { animation: badgePulse 0.4s ease; }
    @keyframes badgePulse { 50% { transform: scale(1.3); } }

    /* Mini history */
    .mini-history-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 8px;
        background: rgba(255,255,255,0.03);
        border-radius: 6px;
        margin-bottom: 3px;
        font-size: 0.7rem;
    }
    .mini-history-item .mh-id { color: var(--pos-text-muted); font-weight: 600; }
    .mini-history-item .mh-total { color: #6ee7b7; font-weight: 700; }

    /* Modal polish */
    .modal-content { border-radius: 18px; }
    .modal-pos {
        background: #1e293b;
        color: var(--pos-text);
        border: 1px solid var(--pos-border);
    }
    .modal-pos .modal-header { border-bottom: 1px solid var(--pos-border); }
    .modal-pos .modal-footer { border-top: 1px solid var(--pos-border); }

    .cash-modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .cash-total-display {
        text-align: center;
        padding: 16px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
        border-radius: 14px;
        margin-bottom: 16px;
    }
    .cash-total-display .ctd-label { font-size: 0.7rem; text-transform: uppercase; color: #93c5fd; font-weight: 700; }
    .cash-total-display .ctd-amount { font-size: 2.2rem; font-weight: 900; color: var(--pos-accent); font-variant-numeric: tabular-nums; }

    .cash-recibido-input {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 2px solid var(--pos-border);
        border-radius: 12px;
        color: var(--pos-text);
        padding: 14px 18px;
        font-size: 1.6rem;
        font-weight: 800;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .cash-recibido-input:focus { outline: none; border-color: var(--pos-accent); background: rgba(59, 130, 246, 0.05); }

    .cambio-display {
        text-align: center;
        padding: 14px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.05));
        border-radius: 12px;
    }
    .cambio-display.negativo { background: rgba(239, 68, 68, 0.1); }
    .cambio-display .cd-label { font-size: 0.7rem; text-transform: uppercase; color: #6ee7b7; font-weight: 700; }
    .cambio-display.negativo .cd-label { color: #fca5a5; }
    .cambio-display .cd-amount { font-size: 1.8rem; font-weight: 900; color: #6ee7b7; font-variant-numeric: tabular-nums; }
    .cambio-display.negativo .cd-amount { color: #fca5a5; }

    .quick-amount-btn {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: var(--pos-accent);
        border-radius: 10px;
        padding: 10px 6px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .quick-amount-btn:hover { background: var(--pos-accent); color: white; }
    .quick-amount-btn.exacto { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #6ee7b7; grid-column: span 3; }
    .quick-amount-btn.exacto:hover { background: #10b981; color: white; }

    .keypad-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .keypad-btn {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--pos-border);
        color: var(--pos-text);
        border-radius: 12px;
        padding: 16px;
        font-size: 1.4rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.15s;
    }
    .keypad-btn:hover { background: var(--pos-accent); color: white; transform: scale(1.02); }
    .keypad-btn:active { transform: scale(0.95); }
    .keypad-btn.fn {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #fca5a5;
        font-size: 0.9rem;
    }
    .keypad-btn.fn:hover { background: #ef4444; color: white; }

    /* Responsive */
    /* Shortcuts help modal */
    .shortcuts-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s;
    }
    .shortcuts-overlay.show {
        opacity: 1;
        pointer-events: all;
    }
    .shortcuts-panel {
        background: #0f172a;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 20px;
        padding: 32px;
        max-width: 580px;
        width: 90%;
        max-height: 85vh;
        overflow-y: auto;
        color: #f1f5f9;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    }
    .shortcuts-panel h4 {
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .shortcuts-panel h4 .close-shortcuts {
        margin-left: auto;
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.3rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 8px;
        transition: all 0.15s;
    }
    .shortcuts-panel h4 .close-shortcuts:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .shortcut-group { margin-bottom: 20px; }
    .shortcut-group:last-child { margin-bottom: 0; }
    .shortcut-group-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        padding-bottom: 6px;
    }
    .shortcut-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 0.85rem;
    }
    .shortcut-row .keys {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .shortcut-row .keys kbd {
        display: inline-block;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.75rem;
        font-family: inherit;
        font-weight: 700;
        color: #e2e8f0;
        min-width: 24px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    .shortcut-row .keys kbd.key-combo {
        background: rgba(99,102,241,0.15);
        border-color: rgba(99,102,241,0.3);
        color: #a5b4fc;
    }
    .shortcut-row .desc {
        color: #94a3b8;
        font-size: 0.8rem;
    }

    @media (max-width: 1200px) {
        .pos-body { grid-template-columns: 1fr 380px; }
    }
    @media (max-width: 992px) {
        .pos-body { grid-template-columns: 1fr; grid-template-rows: 1fr auto; }
        .pos-right { border-left: none; border-top: 1px solid var(--pos-border); }
        .pos-right { order: 2; }
        .pos-left { order: 1; }
    }

    /* ============ TABLET (≤768px) ============ */
    @media (max-width: 768px) {
        .pos-topbar {
            padding: 10px 12px;
            gap: 8px;
        }
        .pos-topbar .caja-tag span:not(.pulse-dot) { display: none; }
        .pos-topbar .caja-tag { padding: 4px 10px; }
        .pos-stat .label { display: none; }
        .pos-stat .value { font-size: 0.9rem; }
        #almacen-select { max-width: 120px; font-size: 0.72rem; }

        .pos-body {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr auto;
            padding: 8px;
            gap: 8px;
        }
        .pos-left { padding: 8px; gap: 8px; }
        .pos-right {
            border-left: none;
            border-top: 1px solid var(--pos-border);
            min-height: 400px;
        }
        .pos-search {
            padding: 14px 48px 14px 48px;
            font-size: 1.1rem;
            border-radius: 12px;
        }
        .pos-search-icon { left: 14px; font-size: 1.2rem; }
        .pos-search-clear { right: 10px; width: 28px; height: 28px; }
        .pos-tabs { gap: 4px; }
        .pos-tab { padding: 6px 10px; font-size: 0.72rem; }
        .pos-products { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; padding: 2px; }
        .pos-product-card { padding: 8px; border-radius: 10px; }
        .pos-product-card .ppc-name { font-size: 0.75rem; min-height: 2.2em; }
        .pos-product-card .ppc-price { font-size: 0.9rem; }
        .pos-product-card .ppc-stock { font-size: 0.6rem; padding: 1px 5px; }

        .pos-cart { padding: 2px; }
        .cart-item { padding: 8px; gap: 8px; border-radius: 10px; }
        .cart-item .ci-img { width: 44px; height: 44px; }
        .cart-item .ci-name { font-size: 0.8rem; }
        .cart-item .ci-qty button { width: 24px; height: 24px; font-size: 0.85rem; }
        .cart-item .ci-qty .qty-val { min-width: 26px; font-size: 0.8rem; }
        .cart-item .discount-input { width: 52px; font-size: 0.72rem; }
        .cart-item .discount-toggle { padding: 1px 4px; font-size: 0.65rem; }

        .pos-right { padding: 0; min-height: 350px; }
        .pos-right .pr-section { padding: 10px 12px; }
        .pos-right .pr-section-title { font-size: 0.62rem; margin-bottom: 6px; }
        .cliente-select { padding: 8px 10px; font-size: 0.85rem; }
        .comprobante-grid { gap: 4px; }
        .comprobante-card { padding: 10px 4px; border-radius: 10px; }
        .comprobante-card i { font-size: 1.25rem; }
        .comprobante-card .ct-name { font-size: 0.7rem; }
        .comprobante-card .ct-sub { font-size: 0.58rem; }
        .ncf-select { padding: 6px 8px; font-size: 0.78rem; }
        .ecf-hint { padding: 6px 8px; font-size: 0.68rem; border-radius: 6px; }

        .totals-row { font-size: 0.78rem; padding: 4px 0; }
        .descuento-input { width: 85px; font-size: 0.75rem; padding: 3px 6px; }
        .input-group-sm .input-group-text { font-size: 0.65rem; }
        .total-display { padding: 12px 8px; border-radius: 10px; }
        .total-display .td-label { font-size: 0.62rem; }
        .total-display .td-amount { font-size: 2rem; }

        .payment-buttons { grid-template-columns: 1fr 1fr; gap: 6px; }
        .btn-pay { padding: 12px 6px; border-radius: 10px; font-size: 0.78rem; }
        .btn-pay i { font-size: 1.3rem; }
        .btn-pay .pay-shortcut { display: none; }
        .btn-pay.full { grid-column: span 2; }

        .cobrar-section { margin-bottom: 10px; }
        .cobrar-total-card { padding: 12px 16px; border-radius: 12px; }
        .cobrar-total-card h2 { font-size: 2.2rem; }
        .metodo-btn { border-radius: 10px; padding: 12px 4px; min-height: 56px; font-size: 0.82rem; }
        .metodo-btn i { font-size: 1.3rem; }
        .input-premium { padding: 10px 12px; font-size: 1.1rem; border-radius: 10px; }
        .pago-detalle label { font-size: 0.62rem; margin-bottom: 3px; }
        .cambio-display { padding: 10px 16px; border-radius: 10px; font-size: 1.4rem; }
        .propina-btn { padding: 8px 16px; font-size: 0.82rem; min-height: 40px; }
        #propina-input { height: 40px; font-size: 1rem; border-radius: 10px; width: 85px; }
        .btn-cobrar-touch { padding: 14px 16px; border-radius: 12px; font-size: 1.1rem; min-height: 50px; }
        .quick-amount-btn { padding: 8px 4px; font-size: 0.78rem; border-radius: 8px; }
        .keypad-btn { padding: 12px; font-size: 1.2rem; border-radius: 10px; }

        .modal-prod-card { padding: 10px 8px; border-radius: 12px; }
        .modal-prod-img { width: 70px; height: 70px; border-radius: 10px; }
        .modal-prod-name { font-size: 0.82rem; }
        .modal-prod-price { font-size: 0.9rem; }
        .modal-prod-qty button { width: 32px; height: 32px; font-size: 1rem; }
        .modal-prod-qty span { font-size: 0.9rem; min-width: 22px; }

        .cash-modal-grid { grid-template-columns: 1fr; gap: 16px; }
        .cash-total-display .ctd-amount { font-size: 1.8rem; }
        .cash-recibido-input { padding: 12px 14px; font-size: 1.3rem; border-radius: 10px; }
        .cambio-display { padding: 10px; border-radius: 10px; font-size: 1.5rem; }
        .quick-amount-btn { padding: 8px 4px; font-size: 0.75rem; }
        .keypad-btn { padding: 10px; font-size: 1.15rem; border-radius: 8px; }

        .cobrar-premium .cobrar-header { padding: 16px 20px 12px; }
        .cobrar-premium .icon-circle { width: 40px; height: 40px; font-size: 1.2rem; }
        .cobrar-total-card { padding: 12px 16px; border-radius: 12px; }
        .cobrar-total-card h2 { font-size: 2.2rem; }
        .metodo-btn { min-height: 60px; }
        .input-premium { padding: 10px 12px; font-size: 1.1rem; }
        .btn-cobrar-touch { min-height: 52px; }

        .shortcuts-panel { max-width: 95%; padding: 24px; border-radius: 16px; }
        .shortcut-row .keys kbd { padding: 2px 6px; font-size: 0.7rem; }
    }

    /* ============ MÓVIL (≤576px) ============ */
    @media (max-width: 576px) {
        .pos-app { height: 100vh; }
        .pos-topbar { padding: 8px 10px; gap: 6px; }
        .pos-topbar .caja-tag { padding: 3px 8px; }
        .pos-topbar .caja-tag .pulse-dot { width: 6px; height: 6px; }
        .pos-stat { display: none; }
        #almacen-select { max-width: 100px; }
        .pos-keyhint { display: none; }

        .pos-body { padding: 6px; gap: 6px; }
        .pos-left { padding: 6px; gap: 6px; }
        .pos-search { padding: 12px 40px 12px 40px; font-size: 1rem; border-radius: 10px; }
        .pos-search-icon { left: 12px; font-size: 1.1rem; }
        .pos-search-clear { right: 8px; width: 26px; height: 26px; }
        .search-mode-toggle button { padding: 5px 8px; font-size: 0.7rem; }
        .pos-tabs { gap: 3px; }
        .pos-tab { padding: 5px 8px; font-size: 0.68rem; }
        .pos-products { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 6px; padding: 2px; }
        .pos-product-card { padding: 6px; border-radius: 8px; }
        .pos-product-card .ppc-img { border-radius: 8px; margin-bottom: 6px; }
        .pos-product-card .ppc-name { font-size: 0.7rem; min-height: 2em; }
        .pos-product-card .ppc-price { font-size: 0.82rem; }
        .pos-product-card .ppc-stock { font-size: 0.55rem; padding: 1px 4px; }
        .pos-product-card .ppc-stock.ok { color: #86efac; }
        .pos-product-card .ppc-stock.low { color: #fde047; }
        .pos-product-card .ppc-stock.crit { color: #fca5a5; }

        .cart-item { padding: 6px; gap: 6px; border-radius: 8px; }
        .cart-item .ci-img { width: 38px; height: 38px; border-radius: 8px; }
        .cart-item .ci-name { font-size: 0.75rem; }
        .cart-item .ci-qty button { width: 22px; height: 22px; font-size: 0.8rem; }
        .cart-item .ci-qty .qty-val { min-width: 24px; font-size: 0.75rem; }
        .cart-item .ci-subtotal { font-size: 0.85rem; }
        .cart-item .discount-input-group { display: none; }

        .pos-right { min-height: 300px; }
        .pos-right .pr-section { padding: 8px 10px; }
        .cliente-select { padding: 8px 8px; font-size: 0.8rem; }
        .comprobante-grid { grid-template-columns: 1fr; gap: 4px; }
        .comprobante-card { padding: 10px; text-align: left; display: flex; align-items: center; gap: 10px; }
        .comprobante-card i { font-size: 1.5rem; margin-bottom: 0; }
        .comprobante-card .ct-name { font-size: 0.8rem; }
        .comprobante-card .ct-sub { font-size: 0.65rem; }
        .ncf-select { width: 100%; margin-top: 4px; }
        .ecf-hint { font-size: 0.65rem; }

        .totals-row { font-size: 0.72rem; }
        .descuento-input { width: 70px; font-size: 0.7rem; }
        .total-display { padding: 10px 6px; border-radius: 8px; }
        .total-display .td-label { font-size: 0.58rem; }
        .total-display .td-amount { font-size: 1.8rem; }

        .payment-buttons { grid-template-columns: 1fr; gap: 5px; }
        .btn-pay { padding: 14px 10px; font-size: 0.85rem; border-radius: 12px; min-height: 48px; }
        .btn-pay i { font-size: 1.5rem; }
        .btn-pay.full { grid-column: auto; }

        .pos-search { padding: 12px 40px; font-size: 1rem; }
        .pos-search-icon { left: 12px; font-size: 1.1rem; }
        .pos-search-clear { width: 26px; height: 26px; }
        .search-mode-toggle { padding: 3px; gap: 1px; }
        .search-mode-toggle button { padding: 4px 6px; font-size: 0.65rem; }

        .cobrar-section { margin-bottom: 8px; }
        .cobrar-total-card h2 { font-size: 2rem; }
        .metodo-btn { border-radius: 10px; padding: 10px 4px; min-height: 52px; font-size: 0.8rem; }
        .metodo-btn i { font-size: 1.2rem; }
        .input-premium { padding: 10px 10px; font-size: 1.05rem; font-weight: 700; }
        .pago-detalle label { font-size: 0.58rem; }
        .cambio-display { font-size: 1.3rem; padding: 8px 12px; }
        .propina-btn { padding: 8px 14px; font-size: 0.78rem; min-height: 38px; border-radius: 40px; }
        #propina-input { height: 38px; width: 75px; font-size: 0.95rem; }
        .btn-cobrar-touch { padding: 14px 12px; font-size: 1.05rem; border-radius: 12px; }
        .quick-amount-btn { padding: 6px 3px; font-size: 0.72rem; border-radius: 8px; }
        .keypad-btn { padding: 8px; font-size: 1.05rem; border-radius: 8px; }

        .modal-prod-card { padding: 8px 6px; border-radius: 10px; }
        .modal-prod-img { width: 60px; height: 60px; border-radius: 8px; }
        .modal-prod-name { font-size: 0.78rem; }
        .modal-prod-price { font-size: 0.85rem; }
        .modal-prod-qty button { width: 28px; height: 28px; font-size: 0.95rem; }
        .modal-prod-qty span { font-size: 0.85rem; min-width: 20px; }

        .cash-modal-grid { grid-template-columns: 1fr; gap: 12px; }
        .cash-total-display { padding: 12px; border-radius: 10px; }
        .cash-total-display .ctd-amount { font-size: 1.6rem; }
        .cash-recibido-input { padding: 10px 12px; font-size: 1.2rem; border-radius: 8px; }
        .cambio-display { padding: 8px; font-size: 1.2rem; border-radius: 8px; }
        .quick-amount-btn { padding: 6px 2px; font-size: 0.7rem; border-radius: 6px; }
        .keypad-btn { padding: 8px; font-size: 1rem; border-radius: 8px; }

        .cobrar-premium .cobrar-header { padding: 12px 16px 10px; }
        .cobrar-premium .icon-circle { width: 36px; height: 36px; font-size: 1.1rem; }
        .cobrar-total-card { padding: 10px 14px; border-radius: 10px; }
        .cobrar-total-card h2 { font-size: 2rem; }
        .metodo-btn { min-height: 52px; padding: 10px 4px; font-size: 0.78rem; border-radius: 8px; }
        .metodo-btn i { font-size: 1.1rem; }
        .input-premium { padding: 8px 10px; font-size: 1rem; border-radius: 8px; }
        .pago-detalle label { font-size: 0.55rem; margin-bottom: 2px; }
        .cambio-display { padding: 8px 10px; font-size: 1.2rem; border-radius: 8px; }
        .propina-btn { padding: 6px 12px; font-size: 0.7rem; min-height: 34px; }
        #propina-input { height: auto; font-size: 0.9rem; width: 65px; }
        .btn-cobrar-touch { padding: 12px 10px; font-size: 1rem; border-radius: 10px; min-height: 46px; }
        .quick-amount-btn { padding: 6px 2px; font-size: 0.65rem; border-radius: 6px; }
        .keypad-btn { padding: 6px; font-size: 0.9rem; border-radius: 6px; }

        .tecla { height: 44px; font-size: 1rem; border-radius: 8px; }
        .tecla-row { gap: 4px; margin-bottom: 4px; }
        .tecla-shift { flex: 1.5; }
        .tecla-backspace { flex: 1.2; }
        .tecla-space { flex: 3.5; }
        .tecla-enter { flex: 1.2; }
        .tecla-shift.active { transform: scale(0.98); }

        .cobrar-premium .cobrar-header { padding: 10px 14px 8px; }
        .cobrar-premium .icon-circle { width: 32px; height: 32px; font-size: 1rem; }
        .cobrar-total-card { padding: 8px 12px; border-radius: 8px; }
        .cobrar-total-card h2 { font-size: 1.8rem; }
        .metodo-btn { min-height: 48px; padding: 8px 3px; font-size: 0.72rem; border-radius: 8px; }
        .input-premium { padding: 8px 8px; font-size: 0.95rem; border-radius: 8px; }
        .btn-cobrar-touch { min-height: 44px; }

        .shortcuts-panel { max-width: 100%; padding: 18px; border-radius: 14px; }
        .shortcut-row .keys kbd { padding: 2px 5px; font-size: 0.62rem; min-width: 20px; }
        .shortcut-row .desc { font-size: 0.72rem; }
        .shortcut-group-title { font-size: 0.6rem; margin-bottom: 6px; }
    }

    /* ============ SAFE AREA INSETS (notched phones) ============ */
    @supports (padding: max(0px)) {
        .pos-topbar { padding-left: max(12px, env(safe-area-inset-left)); padding-right: max(12px, env(safe-area-inset-right)); }
        @media (max-width: 576px) {
            .pos-topbar { padding-left: max(8px, env(safe-area-inset-left)); padding-right: max(8px, env(safe-area-inset-right)); }
        }
        .pos-app { padding-bottom: env(safe-area-inset-bottom); }
        .pos-right { padding-bottom: max(0px, env(safe-area-inset-bottom)); }
        .pos-search-wrap { padding-bottom: env(safe-area-inset-bottom); }
    }

    /* ============ TOUCH-FRIENDLY GLOBAL ============ */
    .pos-app * {
        -webkit-tap-highlight-color: transparent;
    }
    button, .btn, .btn-pay, .btn-pay *, .metodo-btn, .tecla, .pos-tab, .comprobante-card,
    .modal-prod-card, .cart-item .ci-qty button, .cart-item .ci-remove,
    .pos-search-clear, .pos-tab, .tecla, .keypad-btn, .quick-amount-btn,
    .method-btn, .cobrar-section button, .cash-recibido-input,
    .cobrar-premium .cobrar-header button, .modal-prod-qty button,
    .discount-toggle, .tecla, .cash-recibido-input {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    /* Touch target minimum 44x44 */
    @media (max-width: 767.98px) {
        button, .btn, .btn-pay, .metodo-btn, .tecla, .pos-tab,
        .comprobante-card, .cart-item .ci-qty button, .cart-item .ci-remove,
        .pos-search-clear, .modal-prod-qty button, .keypad-btn,
        .quick-amount-btn, .method-btn, .tecla, .discount-toggle {
            min-height: 44px;
        }
        .pos-tab { padding: 10px 12px; }
        .tecla { min-height: 48px; }
        .keypad-btn { min-height: 52px; }
        .btn-pay { min-height: 52px; }
        .metodo-btn { min-height: 56px; }
        .comprobante-card { min-height: 56px; }
    }

    /* ============ INPUT MODE FOR NUMERIC ============ */
    input[type="number"][inputmode="numeric"],
    input[type="text"][inputmode="numeric"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* ============ SCROLL SMOOTHNESS ============ */
    .pos-products, .pos-cart, .search-results-dropdown,
    .modal-productos-grid, .categorias-list, .clientes-resultados {
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    /* ============ BOTTOM SHEET ANIMATION ============ */
    .cobrar-sheet {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--pos-bg);
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        box-shadow: 0 -8px 40px rgba(0,0,0,0.3);
        z-index: 1060;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        overflow: hidden;
    }
    .cobrar-sheet.open {
        transform: translateY(0);
    }
    .cobrar-sheet-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
        z-index: 1055;
    }
    .cobrar-sheet-overlay.visible {
        opacity: 1;
        visibility: visible;
    }

    .cobrar-sheet-handle {
        width: 40px;
        height: 5px;
        background: var(--pos-border);
        border-radius: 3px;
        margin: 10px auto 4px;
        cursor: grab;
    }
    .cobrar-sheet-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--pos-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cobrar-sheet-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--pos-text);
    }
    .cobrar-sheet-close {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        color: var(--pos-text);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .cobrar-sheet-close:active { background: var(--pos-accent-soft); color: var(--pos-accent); }

    .cobrar-sheet-body {
        flex: 1;
        overflow-y: auto;
        padding: 8px 16px 16px;
        -webkit-overflow-scrolling: touch;
    }

    .cobrar-sheet-footer {
        padding: 12px 16px;
        border-top: 1px solid var(--pos-border);
        background: var(--pos-card);
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .cobrar-sheet.open {
        animation: sheetSlideUp 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    @keyframes sheetSlideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }

    /* Swipe down to dismiss indicator */
    .cobrar-sheet.swipe-down { transition: transform 0.15s ease-out; }

    /* ============ KEYBOARD VIRTUAL RESPONSIVE ============ */
    @media (max-width: 576px) {
        .tecla { height: 44px; font-size: 1rem; border-radius: 8px; }
        .tecla-row { gap: 4px; margin-bottom: 4px; }
        .tecla-shift { flex: 1.5; }
        .tecla-backspace { flex: 1.2; }
        .tecla-space { flex: 3.5; }
        .tecla-enter { flex: 1.2; }
    }
    #productosModal .modal-content { background: var(--pos-bg); color: var(--pos-text); }
    #productosModal .modal-header { background: linear-gradient(135deg, var(--pos-accent), #1d4ed8); }
    #productosModal .form-control { background: var(--pos-card); border-color: var(--pos-border); color: var(--pos-text); }
    #productosModal .form-control::placeholder { color: var(--pos-text-muted); }
    #productosModal .form-control:focus { border-color: var(--pos-accent); box-shadow: 0 0 0 3px rgba(59,130,246,0.15); color: var(--pos-text); }

    .tecla {
        flex: 1; height: 52px; border-radius: 10px;
        border: 1px solid var(--pos-border);
        background: var(--pos-card); color: var(--pos-text);
        font-size: 1.15rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        touch-action: manipulation; user-select: none; -webkit-user-select: none;
        transition: background .08s, transform .08s; padding: 0 4px; min-width: 0;
    }
    .tecla:active { background: rgba(59,130,246,0.2); transform: scale(0.93); box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
    .tecla-func { background: rgba(255,255,255,0.06); font-size: 1rem; }
    .tecla-shift { flex: 1.6; }
    .tecla-shift.active { background: rgba(59,130,246,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
    .tecla-backspace { flex: 1.3; }
    .tecla-space { flex: 4; }
    .tecla-enter { flex: 1.3; background: var(--pos-accent); color: #fff; border-color: var(--pos-accent); }
    .tecla-punct { flex: 1; }
    .tecla-func:active { background: rgba(59,130,246,0.2); }
    .tecla-func.active { background: rgba(59,130,246,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
    .tecla-row { display: flex; gap: 6px; justify-content: center; margin-bottom: 6px; }
    #teclado-rows { max-width: 100%; }
    #teclado-rows::-webkit-scrollbar { height: 0; }

    /* ============ Modal Productos — Product Cards ============ */
    .modal-prod-card {
        background: var(--pos-card); border: 1px solid var(--pos-border); border-radius: 14px;
        padding: 12px 10px; cursor: pointer; text-align: center; position: relative;
        transition: transform .15s, box-shadow .15s; height: 100%; display: flex; flex-direction: column; align-items: center;
    }
    .modal-prod-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); border-color: var(--pos-accent); }
    .modal-prod-card.out-of-stock { opacity: 0.4; cursor: not-allowed; }
    .modal-prod-card.out-of-stock:hover { transform: none; box-shadow: none; }
    .modal-prod-img { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; background: rgba(255,255,255,0.05); margin-bottom: 8px; }
    .modal-prod-img-placeholder {
        width: 80px; height: 80px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;
    }
    .modal-prod-name { font-size: .9rem; font-weight: 600; color: var(--pos-text); line-height: 1.2; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; }
    .modal-prod-price { font-size: 1rem; font-weight: 800; color: var(--pos-accent); font-variant-numeric: tabular-nums; }
    .modal-prod-stock-badge { font-size: .7rem; padding: 2px 8px; border-radius: 6px; font-weight: 700; position: absolute; top: 8px; right: 8px; }
    .modal-prod-qty { display: flex; align-items: center; gap: 8px; margin-top: 6px; }
    .modal-prod-qty button {
        width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--pos-border);
        background: rgba(255,255,255,0.06); color: var(--pos-text); font-weight: 700; font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .15s;
    }
    .modal-prod-qty button:hover { background: rgba(59,130,246,0.15); border-color: var(--pos-accent); }
    .modal-prod-qty span { font-weight: 800; font-size: 1rem; min-width: 24px; text-align: center; color: var(--pos-text); }

    /* ============ Premium Payment Modal ============ */
    @keyframes cobrarGradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .cobrar-premium .modal-content { border-radius: 20px; overflow: hidden; border: 0; box-shadow: 0 25px 60px rgba(0,0,0,0.5); }
    .cobrar-premium .cobrar-header { background: linear-gradient(135deg, #059669, #10b981, #3b82f6, #059669); background-size: 300% 300%; animation: cobrarGradientShift 6s ease infinite; padding: 20px 24px 16px; color: #fff; }
    .cobrar-premium .cobrar-header .icon-circle { width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; backdrop-filter: blur(8px); }
    .cobrar-total-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 16px; padding: 16px 20px; text-align: center; border: 1px solid rgba(255,255,255,0.15); }
    .cobrar-total-card h2 { font-size: 3rem; font-weight: 900; color: var(--pos-text); font-variant-numeric: tabular-nums; }
    .metodo-btn { border: 2px solid var(--pos-border); border-radius: 14px; padding: 14px 6px; background: rgba(255,255,255,0.03); color: var(--pos-text); font-weight: 700; font-size: 0.9rem; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer; min-height: 68px; }
    .metodo-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
    .metodo-btn.active-metodo.efectivo { border-color: #10b981; background: rgba(16,185,129,0.12); color: #6ee7b7; }
    .metodo-btn.active-metodo.tarjeta { border-color: #3b82f6; background: rgba(59,130,246,0.12); color: #60a5fa; }
    .metodo-btn.active-metodo.transferencia { border-color: #6366f1; background: rgba(99,102,241,0.12); color: #a5b4fc; }
    .metodo-btn.active-metodo.mixto { border-color: #f59e0b; background: rgba(245,158,11,0.12); color: #fbbf24; }
    .metodo-btn i { font-size: 1.5rem; }
    .input-premium { width: 100%; background: rgba(255,255,255,0.1); border: 2px solid var(--pos-border); border-radius: 12px; color: var(--pos-text); padding: 12px 16px; font-size: 1.25rem; font-weight: 800; text-align: center; font-variant-numeric: tabular-nums; }
    .input-premium:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
    .input-premium::placeholder { font-weight: 400; font-size: 1rem; color: var(--pos-text-muted); opacity: 0.5; }
    .pago-detalle { margin-top: 12px; }
    .pago-detalle label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--pos-text-muted); margin-bottom: 4px; display: block; }
    .cambio-display { text-align: center; padding: 14px 20px; border-radius: 12px; font-size: 1.75rem; font-weight: 800; }
    .cambio-display.positivo { background: #dcfce7; color: #166534; }
    .cambio-display.negativo { background: #fee2e2; color: #991b1b; }
    .propina-btn { border-radius: 50px; border: 2px solid #059669; background: transparent; color: #059669; font-weight: 700; padding: 10px 20px; font-size: 0.9rem; transition: all 0.15s; cursor: pointer; min-height: 44px; }
    .propina-btn:hover { background: rgba(5,150,105,0.1); border-color: #047857; color: #047857; transform: scale(1.05); }
    .propina-btn.active { background: #059669; border-color: #059669; color: #fff; }
    #propina-input { height: 44px; text-align: center; background: rgba(255,255,255,0.12); border: 2px solid var(--pos-border); border-radius: 12px; color: var(--pos-text); font-weight: 700; font-size: 1.1rem; width: 100px; }
    #propina-input:focus { outline: none; border-color: #10b981; }
    .btn-cobrar-touch { background: linear-gradient(135deg, #059669, #10b981); border: none; border-radius: 16px; padding: 16px 24px; font-weight: 800; font-size: 1.2rem; color: #fff; transition: all 0.3s; position: relative; overflow: hidden; min-height: 56px; }
    .btn-cobrar-touch:hover { box-shadow: 0 8px 30px rgba(16,185,129,0.4); transform: translateY(-1px); color: #fff; }
    .btn-cobrar-touch .shine { position: absolute; top: 0; left: -100%; width: 60%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent); animation: cobrarShine 3s ease-in-out infinite; }
    @keyframes cobrarShine { 0% { left: -60%; } 100% { left: 160%; } }
    .btn-cobrar-touch:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
    .btn-cobrar-touch:disabled .shine { display: none; }
    .cobrar-section { margin-bottom: 14px; }
    .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }
    
    /* ============ Post-Pago Modal ============ */
    #postPagoModal .modal-content { border-radius: 20px; overflow: hidden; border: 0; }
    #postPagoModal .modal-header.bg-success { background: linear-gradient(135deg, #059669, #10b981) !important; }
    
    /* ============ Cliente Modal ============ */
    #clienteModal .modal-content { border-radius: 16px; background: var(--pos-bg); color: var(--pos-text); border: 1px solid var(--pos-border); }
    #clienteModal .modal-header { border-bottom: 1px solid var(--pos-border); }
    #clienteModal .cliente-search-input { background: rgba(255,255,255,0.06); border: 1px solid var(--pos-border); border-radius: 12px; color: var(--pos-text); padding: 12px 16px; font-size: 1rem; width: 100%; }
    #clienteModal .cliente-search-input:focus { outline: none; border-color: var(--pos-accent); box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    .cliente-result-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; cursor: pointer; transition: background 0.15s; border: 1px solid transparent; margin-bottom: 4px; }
    .cliente-result-item:hover { background: rgba(59,130,246,0.05); border-color: var(--pos-border); }
    .cliente-result-item .cr-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .cliente-result-item .cr-info { flex: 1; min-width: 0; }
    .cliente-result-item .cr-name { font-weight: 700; color: var(--pos-text); }
    .cliente-result-item .cr-meta { font-size: 0.75rem; color: var(--pos-text-muted); }
</style>

<form id="pos-form" action="{{ route('ventas.store') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="sesion_caja_id" id="selected-sesion-id" value="{{ $sesion->id ?? '' }}">

    <div class="pos-app" style="--delay:0s">
        <!-- ============ TOP BAR ============ -->
        <div class="pos-topbar" style="--delay:0s">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light rounded-pill d-lg-none" onclick="POS.toggleSidebar()" aria-label="Menú lateral" aria-expanded="false" aria-controls="mainSidebar" style="width: 36px; height: 36px; padding: 0;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="caja-tag">
                    <span class="pulse-dot"></span>
                    <span>{{ $sesion->caja->nombre }}</span>
                    @if($sesion->caja->codigo)
                        <span style="opacity: 0.7; font-size: 0.75rem;">{{ $sesion->caja->codigo }}</span>
                    @endif
                </div>
            </div>

            <select id="almacen-select" class="form-select form-select-sm d-inline-block w-auto" style="background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);font-size:0.78rem;padding:4px 10px;border-radius:8px;max-width:160px;" title="Almacén de despacho">
                @forelse($almacenes as $alm)
                    <option value="{{ $alm->id }}" @if($loop->first) selected @endif>{{ $alm->nombre }}</option>
                @empty
                    <option value="" disabled>Sin almacenes disponibles</option>
                @endforelse
            </select>

            <div class="pos-stat">
                <span class="label">Vendido Hoy</span>
                <span class="value success" id="day-total-display">RD$0.00</span>
            </div>
            <div class="pos-stat">
                <span class="label">Ventas</span>
                <span class="value" id="day-count-display">0</span>
            </div>
            <div class="pos-stat">
                <span class="label">Turno</span>
                <span class="value" id="turno-timer">00:00</span>
            </div>

            <div class="spacer"></div>

            <span class="pos-keyhint"><kbd>F2</kbd> Buscar</span>
            <span class="pos-keyhint"><kbd>F4</kbd> Cobrar</span>
            @if($dgiiSandbox)
                <span class="dgii-badge sandbox" title="Modo simulación DGII - no se envían e-CF reales">
                    <i class="bi bi-cpu"></i> DGII {{ strtoupper($dgiiAmbiente) }}
                </span>
            @else
                <span class="dgii-badge {{ $dgiiAmbiente }}">
                    <i class="bi bi-broadcast"></i> DGII {{ strtoupper($dgiiAmbiente) }}
                </span>
            @endif

            <button type="button" class="btn btn-sm btn-outline-light rounded-pill" id="btn-mute-audio" title="Sonido" aria-label="Activar/desactivar sonido">
                <i class="bi bi-volume-up"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill" onclick="POS.toggleShortcutsHelp()" title="Atajos de teclado (F1)" aria-label="Atajos de teclado">
                <i class="bi bi-question-lg"></i> <kbd style="font-size:.6rem;background:rgba(255,255,255,.15);border:none;padding:1px 4px;border-radius:3px;">F1</kbd>
            </button>

            @if(count($cajas) > 1)
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCambiarCaja" title="Cambiar de caja">
                    <i class="bi bi-arrow-left-right"></i>
                </button>
            @endif

            <a href="{{ route('cajas.cierre', ['caja' => $sesion->caja_id, 'sesion' => $sesion->id]) }}" class="btn btn-sm btn-outline-danger rounded-pill" title="Cerrar caja y turno">
                <i class="bi bi-power"></i>
            </a>
        </div>

        <!-- ============ BODY ============ -->
        <div class="pos-body">
            <!-- LEFT: search + tabs + products + cart -->
            <div class="pos-left">
                <div class="d-flex gap-2 align-items-center">
                    <div class="search-mode-toggle">
                        <button type="button" class="active" data-mode="barcode" id="mode-barcode">
                            <i class="bi bi-upc-scan"></i> Escáner
                        </button>
                        <button type="button" data-mode="search" id="mode-search">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <small class="text-muted" id="scan-hint">
                        <i class="bi bi-info-circle"></i> Escanea código y presiona Enter
                    </small>
                </div>

                <div class="pos-search-wrap" role="search">
                    <label for="scan-input" class="visually-hidden">Buscar producto o escanear código</label>
                    <i class="bi bi-upc-scan pos-search-icon" aria-hidden="true"></i>
                    <input type="text" 
                           id="scan-input" 
                           class="pos-search" 
                           placeholder="Escanea código o busca por nombre..." 
                           autocomplete="off"
                           aria-label="Buscar producto o escanear código de barras"
                           aria-describedby="scan-help"
                           aria-autocomplete="list"
                           aria-controls="search-results"
                           aria-expanded="false">
                    <small id="scan-help" class="visually-hidden">
                        Presione F2 para enfocar, Enter para agregar primer resultado, Escape para limpiar
                    </small>
                    <button type="button" 
                            class="pos-search-clear" 
                            onclick="POS.clearScan()" 
                            title="Limpiar (ESC)"
                            aria-label="Limpiar búsqueda">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                    <div id="search-results" 
                         class="search-results-dropdown" 
                         role="listbox"
                         aria-label="Resultados de búsqueda"></div>
                </div>

                <div class="pos-tabs" id="pos-tabs">
                    <button type="button" class="pos-tab active" data-filter="all">
                        <i class="bi bi-grid-3x3-gap"></i> Todos <span class="badge-count" id="count-all">0</span>
                    </button>
                    <button type="button" class="pos-tab" data-filter="available">
                        <i class="bi bi-check2-circle"></i> Disponibles <span class="badge-count" id="count-avail">0</span>
                    </button>
                    <button type="button" class="pos-tab" data-filter="low">
                        <i class="bi bi-exclamation-triangle"></i> Stock bajo <span class="badge-count" id="count-low">0</span>
                    </button>
                    <button type="button" class="pos-tab" data-filter="popular">
                        <i class="bi bi-fire"></i> Populares <span class="badge-count" id="count-pop">0</span>
                    </button>
                </div>

                <!-- Category filter (shown when search is active) -->
                <div id="pos-category-filter" style="display:none; padding: 0 8px 6px;">
                    <select id="main-categoria-filtro" class="form-select form-select-sm" style="background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);font-size:0.8rem;" onchange="mainCategoriaFiltroChange()">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>

                <!-- Products grid (empty until search) -->
                <div id="products-viewport" class="pos-products" style="display: none;"></div>

                <!-- Cart (default view) -->
                <div class="pos-cart" id="cart-viewport">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">
                            <i class="bi bi-cart3 me-1"></i> Carrito
                            <span class="cart-count-badge" id="cart-count">0</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="POS.vaciarCarrito()" id="btn-clear-cart" disabled>
                            <i class="bi bi-trash3"></i> Vaciar
                        </button>
                    </div>
                    <div id="cart-list"></div>
                    <div id="empty-cart-msg" class="pos-cart-empty">
                        <i class="bi bi-cart3"></i>
                        <h5>Carrito vacío</h5>
                        <p>Escanea un código o busca un producto para empezar.<br>
                        Atajos: <kbd>F2</kbd> buscar · <kbd>F4</kbd> cobrar efectivo</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: cliente, comprobante, totales, pagos -->
            <div class="pos-right">
                <div class="pr-section">
                    <div class="pr-section-title">
                        <i class="bi bi-person"></i> Cliente
                        <span class="cliente-pill ms-auto" id="cliente-tipo-badge">Consumo</span>
                    </div>
                    <button type="button" class="cliente-select text-start" onclick="mostrarBuscarCliente()" id="btn-select-cliente">
                        <span id="cliente-selected-name">Consumidor Final</span>
                        <small class="text-muted d-block" style="font-size:0.7rem;font-weight:400;">Tocar para cambiar</small>
                    </button>
                    <select name="cliente_id" id="cliente_id" style="display:none;" value="{{ $clienteConsumidorFinal->id }}">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                    data-es-final="{{ $cliente->id == $clienteConsumidorFinal->id ? '1' : '0' }}"
                                    data-tipo="{{ $cliente->tipo_cliente ?? 'consumo' }}"
                                    data-deuda="{{ $cliente->balance_pendiente ?? 0 }}"
                                    data-limite="{{ $cliente->limite_credito ?? 0 }}"
                                    {{ $cliente->id == $clienteConsumidorFinal->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                                @if($cliente->limite_credito > 0)
                                    (Lim: RD$ {{ number_format($cliente->limite_credito, 0) }} · Disp: RD$ {{ number_format(max(0, $cliente->limite_credito - $cliente->balance_pendiente), 0) }})
                                    @if($cliente->balance_pendiente > 0)
                                        · Adeuda: RD$ {{ number_format($cliente->balance_pendiente, 0) }}
                                    @endif
                                @elseif($cliente->balance_pendiente > 0)
                                    (Adeuda: RD$ {{ number_format($cliente->balance_pendiente, 0) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pr-section">
                    <div class="pr-section-title">
                        <i class="bi bi-receipt"></i> Comprobante Fiscal
                    </div>
                    <div class="comprobante-grid">
                        <div class="comprobante-card active" data-comprobante="sin" data-action="select-comprobante">
                            <i class="bi bi-x-circle"></i>
                            <div class="ct-name">Sin Comprob.</div>
                            <div class="ct-sub">B00</div>
                        </div>
                        <div class="comprobante-card" data-comprobante="ncf" data-action="select-comprobante">
                            <i class="bi bi-receipt"></i>
                            <div class="ct-name">NCF</div>
                            <div class="ct-sub">Tradicional</div>
                        </div>
                        <div class="comprobante-card" data-comprobante="ecf" data-action="select-comprobante">
                            <i class="bi bi-shield-check"></i>
                            <div class="ct-name">e-CF</div>
                            <div class="ct-sub">DGII</div>
                        </div>
                    </div>
                    <input type="hidden" name="tipo_comprobante" id="tipo_comprobante" value="sin">
                    <select name="ncf_tipo" id="ncf_tipo" class="ncf-select" disabled>
                        <option value="">Seleccione tipo de NCF...</option>
                        @foreach($ncfSequences as $seq)
                            <option value="{{ $seq->prefijo }}">{{ $seq->nombre }} ({{ $seq->prefijo }})</option>
                        @endforeach
                    </select>
                    <div id="ecf_info" class="ecf-hint" style="display: none;">
                        <i class="bi bi-info-circle me-1"></i>
                        Se generará, firmará y enviará a DGII al confirmar la venta.
                    </div>
                </div>

                <div class="pr-section flex-grow-1 overflow-auto">
                    <div class="pr-section-title">
                        <i class="bi bi-calculator"></i> Totales
                    </div>
                    <div class="totals-row">
                        <span class="label">Subtotal</span>
                        <span class="val" id="display-subtotal">RD$0.00</span>
                    </div>
                    <div class="totals-row">
                        <span class="label">ITBIS</span>
                        <span class="val" id="display-itbis">RD$0.00</span>
                    </div>
                    <div class="totals-row align-items-center">
                        <span class="label">Descuento</span>
                        <div class="input-group input-group-sm" style="width: 130px;">
                            <span class="input-group-text bg-transparent border-end-0 text-muted" style="font-size: 0.75rem;">RD$</span>
                            <input type="number" name="general_descuento" id="input-general-descuento" class="form-control descuento-input" value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="total-display">
                        <div class="td-label">Total a Pagar</div>
                        <div class="td-amount" id="display-total">RD$0.00</div>
                        <input type="hidden" name="total" id="hidden-total">
                        <input type="hidden" name="subtotal_final" id="hidden-subtotal">
                        <input type="hidden" name="impuestos" id="hidden-itbis">
                    </div>

                    <!-- Mini history -->
                    <div id="turno-history-wrap" style="display: none; margin-top: 12px;">
                        <div class="pr-section-title">
                            <i class="bi bi-clock-history"></i> Últimas ventas
                            <a href="{{ route('ventas.index') }}" class="ms-auto text-muted text-decoration-none" style="font-size: 0.7rem;">Ver todas</a>
                        </div>
                        <div id="turno-history"></div>
                    </div>
                </div>

                <div class="pr-section">
                    <input type="hidden" name="tipo_venta_id" id="tipo_venta_id_input" value="{{ $tipoVentaDefault->id ?? 1 }}">

                    <div class="payment-buttons" id="payment-buttons">
                        <button type="button" data-action="submit" data-metodo="efectivo" class="btn-pay">
                            <span class="pay-shortcut">F4</span>
                            <i class="bi bi-cash-stack"></i> Efectivo
                        </button>
                        <button type="button" data-action="submit" data-metodo="tarjeta" class="btn-pay tarjeta">
                            <span class="pay-shortcut">F5</span>
                            <i class="bi bi-credit-card-2-front"></i> Tarjeta
                        </button>
                        <button type="button" data-action="submit" data-metodo="fiado" class="btn-pay fiado">
                            <span class="pay-shortcut">F6</span>
                            <i class="bi bi-journal-bookmark"></i> Fiado
                        </button>
                        <button type="button" data-action="submit" data-metodo="cuenta_abierta" class="btn-pay cuenta_abierta">
                            <span class="pay-shortcut">F7</span>
                            <i class="bi bi-folder-plus"></i> Cta. Abierta
                        </button>
                        <button type="button" data-action="submit" data-metodo="transferencia" class="btn-pay transferencia full" id="btn-transferencia">
                            <i class="bi bi-bank2"></i> Transferencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Cambio de Caja -->
@if(count($cajas) > 1)
<div class="modal fade" id="modalCambiarCaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-pos">
            <div class="modal-header">
                <h5 class="fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Cambiar de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Cierra la caja actual antes de abrir otra.</p>
                <div class="d-grid gap-2">
                    @foreach($cajas as $caja)
                        @if($caja->id != $sesion->caja_id)
                            <a href="{{ route('cajas.cierre', $caja->id) }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-cash-register me-2"></i>
                                <strong>{{ $caja->nombre }}</strong>
                                @if($caja->codigo)<span class="badge bg-dark ms-1">{{ $caja->codigo }}</span>@endif
                                <div class="small text-muted">
                                    @if($caja->estado == 'abierta')
                                        <i class="bi bi-circle-fill text-success"></i> Abierta
                                    @else
                                        <i class="bi bi-circle text-danger"></i> Cerrada
                                    @endif
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ============ Bottom Sheet Payment (Mobile) ============ -->
<div class="cobrar-sheet-overlay" id="cobrarSheetOverlay" aria-hidden="true" onclick="POS.cerrarCobrar()"></div>

<div class="cobrar-sheet" id="cobrarSheet" role="dialog" aria-modal="true" aria-labelledby="cobrarSheetTitle" aria-hidden="true">
    <div class="cobrar-sheet-handle"></div>
    
    <div class="cobrar-sheet-header">
        <h5 class="cobrar-sheet-title" id="cobrarSheetTitle">Cobrar Venta</h5>
        <button type="button" class="cobrar-sheet-close" onclick="POS.cerrarCobrar()" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="cobrar-sheet-body">
        <!-- Fila 1: Total grande -->
        <div class="cobrar-section">
            <div class="cobrar-total-card">
                <h2 class="fw-bold mb-0" id="pago-total">RD$ 0.00</h2>
            </div>
        </div>

        <!-- Fila 2: Métodos de pago -->
        <div class="cobrar-section">
            <div class="row g-2" id="pago-metodos">
                <div class="col-3">
                    <button type="button" class="method-btn efectivo active-metodo w-100" data-metodo="efectivo" onclick="seleccionarMetodoPago('efectivo')">
                        <i class="bi bi-cash-stack"></i> Efectivo
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="method-btn tarjeta w-100" data-metodo="tarjeta" onclick="seleccionarMetodoPago('tarjeta')">
                        <i class="bi bi-credit-card-2-front"></i> Tarjeta
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="method-btn transferencia w-100" data-metodo="transferencia" onclick="seleccionarMetodoPago('transferencia')">
                        <i class="bi bi-bank2"></i> Transf.
                    </button>
                </div>
                <div class="col-3">
                    <button type="button" class="method-btn mixto w-100" data-metodo="mixto" onclick="seleccionarMetodoPago('mixto')">
                        <i class="bi bi-coin"></i> Mixto
                    </button>
                </div>
            </div>
        </div>

        <!-- Fila 3: Efectivo (monto recibido + cambio) -->
        <div id="pago-efectivo" class="cobrar-section">
            <div class="pago-detalle">
                <label>Monto Recibido</label>
                <input type="number" id="monto-recibido" class="input-premium" step="0.01" min="0" placeholder="0.00" value="" inputmode="decimal">
                
                <!-- Botones de Denominaciones RD$ -->
                <div class="row g-2 mt-2 mb-2">
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(50)">RD$50</button></div>
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(100)">RD$100</button></div>
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(200)">RD$200</button></div>
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(500)">RD$500</button></div>
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(1000)">RD$1,000</button></div>
                    <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(2000)">RD$2,000</button></div>
                </div>
                
                <div id="cambio-info" class="mt-2 cambio-display positivo d-none">
                    Cambio: <span class="fw-bold" id="cambio-monto">RD$ 0.00</span>
                </div>
            </div>
        </div>

        <!-- Mixto (tres campos) -->
        <div id="pago-mixto" class="cobrar-section" style="display:none;">
            <div class="pago-detalle">
                <div class="mb-2">
                    <label>Efectivo</label>
                    <input type="number" id="mixto-efectivo" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                </div>
                <div class="mb-2">
                    <label>Tarjeta</label>
                    <input type="number" id="mixto-tarjeta" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                </div>
                <div class="mb-2">
                    <label>Transferencia</label>
                    <input type="number" id="mixto-transferencia" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                </div>
                <small class="text-muted" id="mixto-restante"></small>
            </div>
        </div>

        <!-- Propina -->
        <div class="cobrar-section">
            <div class="pago-detalle">
                <label>Propina</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="propina-input" step="0.01" min="0" value="0" inputmode="decimal" oninput="actualizarTotalPago()">
                    <button type="button" class="propina-btn" onclick="asignarPropina(0, this)">0%</button>
                    <button type="button" class="propina-btn" onclick="asignarPropina(10, this)">10%</button>
                    <button type="button" class="propina-btn" onclick="asignarPropina(15, this)">15%</button>
                    <button type="button" class="propina-btn" onclick="asignarPropina(18, this)">18%</button>
                </div>
            </div>
        </div>

        <!-- Botón cobrar full-width -->
        <div class="cobrar-section mt-1">
            <button type="button" class="btn-cobrar-touch w-100" onclick="procesarPago()">
                <span class="shine"></span>
                <i class="bi bi-check2-circle me-1"></i> Cobrar
            </button>
        </div>
    </div>

    <div class="cobrar-sheet-footer">
        <button type="button" class="btn btn-sm btn-link text-muted w-100" onclick="POS.cerrarCobrar()">
            Cancelar
        </button>
    </div>
</div>

<!-- ============ Premium Payment Modal (Desktop) ============ -->
<div class="modal fade cobrar-premium" id="pagoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="cobrar-header d-flex align-items-center gap-3">
                <div class="icon-circle"><i class="bi bi-cash-stack"></i></div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-0">Cobrar Venta</h5>
                    <small class="text-white-50">Punto de Venta</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">

                <!-- Fila 1: Total grande -->
                <div class="cobrar-section">
                    <div class="cobrar-total-card">
                        <h2 class="fw-bold mb-0" id="md-pago-total">RD$ 0.00</h2>
                    </div>
                </div>

                <!-- Fila 2: Métodos de pago grandes -->
                <div class="cobrar-section">
                    <div class="row g-2" id="md-pago-metodos">
                        <div class="col-3">
                            <button type="button" class="metodo-btn efectivo active-metodo w-100" data-metodo="efectivo" onclick="seleccionarMetodoPago('efectivo')">
                                <i class="bi bi-cash-stack"></i> Efectivo
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="metodo-btn tarjeta w-100" data-metodo="tarjeta" onclick="seleccionarMetodoPago('tarjeta')">
                                <i class="bi bi-credit-card-2-front"></i> Tarjeta
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="metodo-btn transferencia w-100" data-metodo="transferencia" onclick="seleccionarMetodoPago('transferencia')">
                                <i class="bi bi-bank2"></i> Transf.
                            </button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="metodo-btn mixto w-100" data-metodo="mixto" onclick="seleccionarMetodoPago('mixto')">
                                <i class="bi bi-coin"></i> Mixto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Fila 3: Efectivo (monto recibido + cambio) -->
                <div id="md-pago-efectivo" class="cobrar-section">
                    <div class="pago-detalle">
                        <label>Monto Recibido</label>
                        <input type="number" id="md-monto-recibido" class="input-premium" step="0.01" min="0" placeholder="0.00" value="" inputmode="decimal">

                        <!-- Botones de Denominaciones RD$ -->
                        <div class="row g-2 mt-2 mb-2">
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(50)">RD$50</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(100)">RD$100</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(200)">RD$200</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(500)">RD$500</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(1000)">RD$1,000</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold btn-pos-denom" onclick="addRecibido(2000)">RD$2,000</button></div>
                        </div>

                        <div id="md-cambio-info" class="mt-2 cambio-display positivo d-none">
                            Cambio: <span class="fw-bold" id="md-cambio-monto">RD$ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Fila 4: Mixto (tres campos) -->
                <div id="md-pago-mixto" class="cobrar-section" style="display:none;">
                    <div class="pago-detalle">
                        <div class="mb-2">
                            <label>Efectivo</label>
                            <input type="number" id="md-mixto-efectivo" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                        </div>
                        <div class="mb-2">
                            <label>Tarjeta</label>
                            <input type="number" id="md-mixto-tarjeta" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                        </div>
                        <div class="mb-2">
                            <label>Transferencia</label>
                            <input type="number" id="md-mixto-transferencia" class="input-premium" step="0.01" min="0" placeholder="0.00" inputmode="decimal" oninput="actualizarTotalPago()">
                        </div>
                        <small class="text-muted" id="md-mixto-restante"></small>
                    </div>
                </div>

                <!-- Fila 5: Propina -->
                <div class="cobrar-section">
                    <div class="pago-detalle">
                        <label>Propina</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" id="md-propina-input" step="0.01" min="0" value="0" inputmode="decimal" oninput="actualizarTotalPago()">
                            <button type="button" class="propina-btn" onclick="asignarPropina(0, this)">0%</button>
                            <button type="button" class="propina-btn" onclick="asignarPropina(10, this)">10%</button>
                            <button type="button" class="propina-btn" onclick="asignarPropina(15, this)">15%</button>
                            <button type="button" class="propina-btn" onclick="asignarPropina(18, this)">18%</button>
                        </div>
                    </div>
                </div>

                <!-- Fila 6: Botón cobrar full-width -->
                <div class="cobrar-section mt-1">
                    <button type="button" class="btn-cobrar-touch w-100" onclick="procesarPago()">
                        <span class="shine"></span>
                        <i class="bi bi-check2-circle me-1"></i> Cobrar
                    </button>
                </div>

                <!-- Fila 7: Cancelar -->
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-muted" data-bs-dismiss="modal" style="text-decoration:none;">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ Modal Post-Pago ============ -->
<div class="modal fade" id="postPagoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header bg-success text-white border-0" style="background:linear-gradient(135deg,#059669,#10b981)!important;">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle me-2"></i>Pago Exitoso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4" style="background:var(--pos-bg);color:var(--pos-text);">
                <div class="display-4 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                <h5 id="post-cliente" class="fw-bold">Consumidor Final</h5>
                <div class="fs-2 fw-bold text-success mb-3" id="post-total">RD$ 0.00</div>
                <span class="badge bg-secondary rounded-pill px-3 py-2 mb-3" id="post-metodo">Efectivo</span>

                <div class="d-flex gap-2 justify-content-center mt-3">
                    <a href="#" id="btn-ticket" target="_blank" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-receipt me-1"></i> Ticket
                    </a>
                    <button type="button" id="btn-imprimir" class="btn btn-outline-secondary rounded-pill" onclick="imprimirTicket()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button type="button" id="btn-facturar" class="btn btn-outline-warning rounded-pill" onclick="facturarVenta()">
                        <i class="bi bi-shield-check me-1"></i> Facturar (e-CF)
                    </button>
                </div>
                <div id="factura-status" class="mt-2 small"></div>
            </div>
            <div class="modal-footer border-0 justify-content-center" style="background:var(--pos-bg);">
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="POS.nuevaVenta()">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============ Modal Productos con Teclado Virtual ============ -->
<div class="modal fade" id="productosModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-4 border-0 shadow" style="max-height:95vh;">
            <div class="modal-header border-0 rounded-top-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar Producto</h5>
                <button type="button" class="btn-close btn-close-white" style="width:36px;height:36px;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 d-flex flex-column" style="height: calc(95vh - 60px);">
                <div class="input-group shadow-sm rounded-3 mb-2">
                    <span class="input-group-text" style="background: var(--pos-card); border-color: var(--pos-border); color: var(--pos-text-muted); min-height:48px;"><i class="bi bi-search fs-5"></i></span>
                    <input type="text" id="modal-buscar-producto" class="form-control" placeholder="Buscar producto..." autocomplete="off" oninput="modalBuscarProductos()" style="min-height:48px; font-size:1.05rem;">
                    <button class="btn" type="button" id="modal-btn-limpiar" style="display:none; color: var(--pos-text-muted); min-width:48px;" onclick="modalLimpiarBusqueda()"><i class="bi bi-x-lg fs-5"></i></button>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <select id="modal-item-curso" class="form-select form-select-sm rounded-3" style="max-width:120px;background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);">
                        <option value="entrada">Entrada</option>
                        <option value="fuerte" selected>Plato Fuerte</option>
                        <option value="postre">Postre</option>
                        <option value="bebida">Bebida</option>
                    </select>
                    <select id="modal-categoria-filtro" class="form-select form-select-sm rounded-3" onchange="categoriaFiltroChange()" style="background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);">
                        <option value="">Todas</option>
                    </select>
                    <input type="text" id="modal-item-notas" class="form-control form-control-sm rounded-3" placeholder="Notas" maxlength="200" style="background:var(--pos-card);border-color:var(--pos-border);color:var(--pos-text);">
                </div>
                <div id="modal-productos-grid" class="row g-2 overflow-auto mb-2" style="flex:1; min-height:0;"></div>
                <div class="border-top pt-2 mt-2" id="teclado-virtual" style="border-color: var(--pos-border) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold" style="font-size:.8rem; color: var(--pos-text-muted);">Teclado</small>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary rounded-start-pill" style="font-size:.8rem;padding:4px 12px;border-color: var(--pos-border);color: var(--pos-text-muted);" onclick="tecladoIdioma('us')" id="btn-idioma-us">US</button>
                            <button class="btn btn-outline-secondary rounded-end-pill" style="font-size:.8rem;padding:4px 12px;border-color: var(--pos-border);color: var(--pos-text-muted);" onclick="tecladoIdioma('es')" id="btn-idioma-es">ES</button>
                        </div>
                    </div>
                    <div id="teclado-rows"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1200;">
    <div id="scanToast" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="scanToastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- ============ Modal Buscar Cliente ============ -->
<div class="modal fade" id="clienteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold mb-0"><i class="bi bi-person me-2"></i>Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="buscar-cliente-input" class="cliente-search-input" placeholder="Buscar por nombre o RNC..." autocomplete="off">
                <div id="clientes-resultados" class="mt-3" style="max-height:250px;overflow-y:auto;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill w-100" onclick="seleccionarCliente({{ $clienteConsumidorFinal->id }}, '{{ $clienteConsumidorFinal->nombre }}')">
                    <i class="bi bi-person me-1"></i> Consumidor Final
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============ Modal Advertencia de Crédito ============ -->
<div class="modal fade" id="creditoWarningModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content modal-pos">
            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#1f2937;">
                <h5 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Límite de Crédito Excedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">El cliente <strong id="credito-nombre"></strong> superará su límite de crédito con esta venta:</p>
                <div class="p-3 bg-light rounded-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Límite de crédito:</span>
                        <strong id="credito-limite"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Deuda actual:</span>
                        <strong class="text-danger" id="credito-deuda"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Disponible:</span>
                        <strong class="text-success" id="credito-disponible"></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total esta venta:</span>
                        <strong id="credito-total"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nuevo saldo:</span>
                        <strong class="text-danger" id="credito-nuevo-saldo"></strong>
                    </div>
                    <div class="d-flex justify-content-between p-2 bg-danger bg-opacity-10 rounded-3 mt-2">
                        <span class="fw-bold">Exceso:</span>
                        <strong class="text-danger" id="credito-exceso"></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning rounded-pill" id="btn-confirmar-credito">
                    <i class="bi bi-check-circle me-1"></i>Registrar Igual
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Shortcuts Help Overlay -->
<div class="shortcuts-overlay" id="shortcutsHelp">
    <div class="shortcuts-panel" role="dialog" aria-label="Atajos de teclado" aria-modal="true">
        <h4>
            <i class="bi bi-keyboard"></i> Atajos de Teclado
            <button type="button" class="close-shortcuts" onclick="POS.toggleShortcutsHelp()" aria-label="Cerrar">&times;</button>
        </h4>
        <div class="shortcut-group">
            <div class="shortcut-group-title">Búsqueda y Carrito</div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F2</kbd></span>
                <span class="desc">Enfocar búsqueda de productos</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>Enter</kbd></span>
                <span class="desc">Agregar primer resultado / confirmar escáner</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>Esc</kbd></span>
                <span class="desc">Limpiar búsqueda o monto recibido</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd class="key-combo">Ctrl</kbd> + <kbd class="key-combo">⌫</kbd></span>
                <span class="desc">Vaciar carrito completo</span>
            </div>
        </div>
        <div class="shortcut-group">
            <div class="shortcut-group-title">Métodos de Pago</div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F4</kbd></span>
                <span class="desc">Abrir cobro / Efectivo</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F5</kbd></span>
                <span class="desc">Pagar con tarjeta</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F6</kbd></span>
                <span class="desc">Pagar a fiado (crédito)</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F7</kbd></span>
                <span class="desc">Cuenta abierta</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F9</kbd></span>
                <span class="desc">Transferencia bancaria</span>
            </div>
        </div>
        <div class="shortcut-group">
            <div class="shortcut-group-title">Clientes</div>
            <div class="shortcut-row">
                <span class="keys"><kbd class="key-combo">Ctrl</kbd> + <kbd class="key-combo">K</kbd></span>
                <span class="desc">Buscar/Seleccionar cliente</span>
            </div>
        </div>
        <div class="shortcut-group">
            <div class="shortcut-group-title">Acciones</div>
            <div class="shortcut-row">
                <span class="keys"><kbd>F1</kbd></span>
                <span class="desc">Mostrar esta ayuda</span>
            </div>
            <div class="shortcut-row">
                <span class="keys"><kbd class="key-combo">Ctrl</kbd> + <kbd class="key-combo">Enter</kbd></span>
                <span class="desc">Confirmar y guardar venta</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // ============ Datos del servidor ============
    const productos = {!! json_encode($productosJs) !!};
    const productosPre = productos.map(p => ({ ...p, nl: (p.nombre || '').toLowerCase(), cl: (p.codigo_barras || '').toLowerCase() }));
    const codigoBarraMap = new Map(productosPre.filter(p => p.cl).map(p => [p.cl, p]));
    const clientes = {!! json_encode($clientesJs) !!};
    const categorias = {!! json_encode($categoriasJs) !!};
    const almacenes = {!! json_encode($almacenes->map(fn($a) => ['id' => (int)$a->id, 'nombre' => $a->nombre])->values()) !!};
    const sesionId = {{ $sesion->id }};
    const dia = {!! json_encode(\Carbon\Carbon::now()->format('Y-m-d')) !!};
    const placeholder = {!! json_encode(asset('img/producto-placeholder.svg')) !!};
    const urlStatsDia = {!! json_encode(route('ventas.statsDia')) !!};
    const urlTurno = {!! json_encode(url('/ventas/json-turno')) !!};
    const urlCuentaAbierta = {!! json_encode(url('/ventas/cuenta-abierta')) !!};
    const turnoInicio = new Date({!! json_encode($sesion->fecha_apertura->toIso8601String()) !!});
    const validaStock = {!! json_encode($validaStock ?? true) !!};

    // ============ Estado ============
    const cart = [];
    let scanMode = 'barcode';
    let activeFilter = 'all';
    let searchQuery = '';
    let metodoPagoPendiente = null;
    let modalCategoriaFiltro = '';
    let mainCategoriaFiltro = '';
    let isSubmitting = false;
    let lastRemovedItem = null;
    let creditoWarningInstance = null;
    let audioEnabled = localStorage.getItem('pos_audio_enabled') !== 'false';

    function playBeep(type) {
        if (!audioEnabled) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const g = ctx.createGain();
            g.connect(ctx.destination);
            g.gain.value = 0.08;
            const freqs = { success: [880, 1100], error: [440, 330], warning: [660], scan: [1200] };
            const tones = freqs[type] || freqs.success;
            tones.forEach((freq, i) => {
                const o = ctx.createOscillator();
                o.type = 'sine'; o.frequency.value = freq; o.connect(g);
                o.start(ctx.currentTime + i * 0.12);
                o.stop(ctx.currentTime + i * 0.12 + 0.1);
            });
        } catch(e) {}
    }

    // ============ Helpers ============
    const $ = (id) => document.getElementById(id);
    const fmt = (n) => 'RD$' + (parseFloat(n) || 0).toLocaleString('es-DO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const escapeHtml = (s) => String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
    const debounce = (fn, delay) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), delay); }; };

    function getAlmacenId() {
        const el = $('almacen-select');
        if (el && el.value) {
            const id = parseInt(el.value);
            if (!isNaN(id) && id > 0) return id;
        }
        if (almacenes.length > 0) return almacenes[0].id;
        // Last resort — shouldn't happen since server ensures at least one almacen
        return 0;
    }

    function showToast(msg, type = 'success', delay = 2500) {
        const toast = $('scanToast');
        const body = $('scanToastBody');
        toast.className = 'toast align-items-center text-white border-0 bg-' + type;
        body.textContent = msg;
        new bootstrap.Toast(toast, { delay }).show();
    }

    // ============ POS namespace (expuesto a window) ============
    const POS = {
        // Estado expuesto para debugging
        cart, scanMode, productos, validaStock,

        clearScan() {
            $('scan-input').value = '';
            $('scan-input').focus();
            hideSearchResults();
        },

        vaciarCarrito() {
            if (cart.length === 0) return;
            if (confirm('¿Vaciar el carrito completo?')) {
                cart.length = 0;
                renderCart();
                showToast('Carrito vaciado', 'info');
                $('scan-input').focus();
            }
        },

        removeFromCart(index) {
            const item = cart[index];
            if (!item) return;
            lastRemovedItem = item;

            const el = document.querySelector(`.cart-item[data-index="${index}"]`);
            if (el) {
                el.classList.add('removing');
                setTimeout(() => {
                    cart.splice(index, 1);
                    renderCart();
                    mostrarUndoRemoval(item);
                }, 250);
            } else {
                cart.splice(index, 1);
                renderCart();
                mostrarUndoRemoval(item);
            }
        },

        deshacerRemocion() {
            if (!lastRemovedItem) return;
            cart.push(lastRemovedItem);
            lastRemovedItem = null;
            renderCart('add');
            showToast('Producto restaurado', 'success', 1500);
            const toast = bootstrap.Toast.getInstance($('scanToast'));
            if (toast) toast.hide();
        },

        updateQty(index, val) {
            const v = parseInt(val) || 1;
            if (v < 1) return;
            if (!validaStock) {
                cart[index].qty = v;
            } else if (v > cart[index].stock) {
                showToast(`Stock máximo: ${cart[index].stock}`, 'warning');
                cart[index].qty = cart[index].stock;
            } else {
                cart[index].qty = v;
            }
            renderCart();
        },

        selectComprobante(tipo) {
            document.querySelectorAll('.comprobante-card').forEach(c => c.classList.remove('active'));
            document.querySelector(`.comprobante-card[data-comprobante="${tipo}"]`)?.classList.add('active');
            $('tipo_comprobante').value = tipo;
            const ncfSelect = $('ncf_tipo');
            const ecfInfo = $('ecf_info');
            if (tipo === 'sin') {
                ncfSelect.value = '';
                ncfSelect.disabled = true;
                ecfInfo.style.display = 'none';
            } else if (tipo === 'ncf') {
                ncfSelect.disabled = false;
                ecfInfo.style.display = 'none';
            } else if (tipo === 'ecf') {
                ncfSelect.value = '';
                ncfSelect.disabled = true;
                ecfInfo.style.display = 'block';
            }
        },

        submitForm(metodo) {
            if (cart.length === 0) {
                showToast('Agrega al menos un producto al carrito', 'warning');
                return;
            }
            if (isSubmitting) {
                showToast('Ya hay un pago en proceso', 'warning');
                return;
            }
            if (validaStock && !getAlmacenId()) {
                showToast('Selecciona un almacén válido', 'danger');
                return;
            }
            if (metodo === 'fiado' || metodo === 'cuenta_abierta') {
                if (!validarCreditoFiado()) {
                    return;
                }
                procesarPagoDirecto(metodo);
                return;
            }
            metodoPagoPendiente = metodo;
            mostrarPago(metodo);
        },

        cerrarCobrar() {
            const sheet = document.getElementById('cobrarSheet');
            if (sheet) sheet.classList.remove('open');
            const overlay = document.getElementById('cobrarSheetOverlay');
            if (overlay) overlay.classList.remove('visible');
        },

        toggleShortcutsHelp() {
            const overlay = $('shortcutsHelp');
            const isOpen = overlay.classList.contains('show');
            overlay.classList.toggle('show');
            if (!isOpen) {
                setTimeout(() => {
                    const closeBtn = overlay.querySelector('.close-shortcuts');
                    if (closeBtn) closeBtn.focus();
                }, 100);
            }
        },

        nuevaVenta() {
            isSubmitting = false;
            const modal = bootstrap.Modal.getInstance($('postPagoModal'));
            if (modal) modal.hide();
            $('scan-input').focus();
        }
    };
    
    window.POS = POS;

    // ============ Payment Modal Functions ============
    let metodoPagoActual = 'efectivo';
    let ultimaVentaId = null;

    function $p(id) {
        const prefix = getModalPrefix();
        const el = document.getElementById(prefix + id);
        return el || document.getElementById(id);
    }

    function getModalPrefix() {
        const bsSheet = document.getElementById('cobrarSheet');
        return (bsSheet && bsSheet.classList.contains('open')) ? '' : 'md-';
    }

    function mostrarPago(metodo) {
        const total = parseFloat($('hidden-total').value) || 0;
        if (total <= 0) { showToast('Total inválido', 'danger'); return; }
        const isMobile = window.innerWidth < 992;
        if (isMobile) {
            const sheet = document.getElementById('cobrarSheet');
            sheet.classList.add('open');
            document.getElementById('cobrarSheetOverlay').classList.add('visible');
        } else {
            new bootstrap.Modal(document.getElementById('pagoModal')).show();
        }
        $p('pago-total').innerText = fmt(total);
        $p('propina-input').value = '0';
        $p('monto-recibido').value = '';
        $p('cambio-info').classList.add('d-none');
        $p('mixto-efectivo').value = '';
        $p('mixto-tarjeta').value = '';
        $p('mixto-transferencia').value = '';
        seleccionarMetodoPago(metodo);
        setTimeout(() => $p('monto-recibido')?.focus(), 400);
    }

    function seleccionarMetodoPago(metodo) {
        metodoPagoActual = metodo;
        const prefix = getModalPrefix();
        document.querySelectorAll('#' + prefix + 'pago-metodos .metodo-btn').forEach(b => b.classList.remove('active-metodo'));
        document.querySelector('#' + prefix + 'pago-metodos .metodo-btn[data-metodo="' + metodo + '"]')?.classList.add('active-metodo');
        $p('pago-efectivo').style.display = metodo === 'efectivo' ? 'block' : 'none';
        $p('pago-mixto').style.display = metodo === 'mixto' ? 'block' : 'none';
        if (metodo === 'efectivo') {
            $p('cambio-info').classList.add('d-none');
            setTimeout(() => $p('monto-recibido')?.focus(), 200);
        }
        actualizarTotalPago();
    }

    function addRecibido(monto) {
        const input = $p('monto-recibido');
        const actual = parseFloat(input.value) || 0;
        input.value = (actual + monto).toFixed(2);
        actualizarTotalPago();
    }

    function actualizarTotalPago() {
        const totalBase = parseFloat($('hidden-total').value) || 0;
        const propina = parseFloat($p('propina-input').value) || 0;
        const totalFinal = totalBase + propina;
        $p('pago-total').innerText = fmt(totalFinal);

        if (metodoPagoActual === 'efectivo') {
            const recibido = parseFloat($p('monto-recibido').value) || 0;
            const cambio = recibido - totalFinal;
            const cambioInfo = $p('cambio-info');
            const cambioMonto = $p('cambio-monto');
            if (recibido > 0 && cambio >= 0) {
                cambioInfo.classList.remove('d-none');
                cambioMonto.textContent = fmt(cambio);
            } else {
                cambioInfo.classList.add('d-none');
            }
        } else if (metodoPagoActual === 'mixto') {
            const eff = parseFloat($p('mixto-efectivo').value) || 0;
            const card = parseFloat($p('mixto-tarjeta').value) || 0;
            const trans = parseFloat($p('mixto-transferencia').value) || 0;
            const suma = eff + card + trans;
            const restante = totalFinal - suma;
            const label = $p('mixto-restante');
            if (restante > 0.01) {
                label.innerHTML = `<span class="text-warning fw-bold">Faltan ${fmt(restante)}</span>`;
            } else if (restante < -0.01) {
                label.innerHTML = `<span class="text-danger fw-bold">Sobran ${fmt(Math.abs(restante))}</span>`;
            } else {
                label.textContent = '✓ Montos correctos';
                label.className = 'text-success fw-bold';
            }
        }
    }

    function asignarPropina(porcentaje, btn) {
        const total = parseFloat($('hidden-total').value) || 0;
        $p('propina-input').value = (total * porcentaje / 100).toFixed(2);
        actualizarTotalPago();
        document.querySelectorAll('.propina-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function procesarPago() {
        if (isSubmitting) return;
        const total = parseFloat($('hidden-total').value) || 0;
        const propina = parseFloat($p('propina-input').value) || 0;

        if (metodoPagoActual === 'efectivo') {
            const recibido = parseFloat($p('monto-recibido').value) || 0;
            if (recibido < total + propina) {
                showToast('Monto recibido es menor al total', 'danger');
                return;
            }
        } else if (metodoPagoActual === 'mixto') {
            const eff = parseFloat($p('mixto-efectivo').value) || 0;
            const card = parseFloat($p('mixto-tarjeta').value) || 0;
            const trans = parseFloat($p('mixto-transferencia').value) || 0;
            const suma = eff + card + trans;
            if (Math.abs(suma - (total + propina)) > 0.01) {
                showToast('Los montos mixtos no cubren el total', 'warning');
                return;
            }
        }

        // Validate almacen before proceeding
        const almacenId = getAlmacenId();
        if (validaStock && !almacenId) { showToast('Selecciona un almacén válido', 'danger'); return; }

        isSubmitting = true;
        const btn = document.querySelector('.btn-cobrar-touch');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

        // Prepare form submission
        const form = $('pos-form');
        const formData = new FormData(form);
        formData.set('metodo_pago', metodoPagoActual);
        formData.set('propina', propina.toFixed(2));
        formData.set('general_descuento', (parseFloat(document.querySelector('input[name=\"general_descuento\"]')?.value) || 0).toFixed(2));

        // Inject almacen_id for each cart item when stock validation is active
        if (validaStock) {
            cart.forEach(() => formData.append('almacen_id', almacenId));
        }

        // Add mixto amounts if applicable
        if (metodoPagoActual === 'mixto') {
            formData.set('mixto_efectivo', (parseFloat($p('mixto-efectivo').value) || 0).toFixed(2));
            formData.set('mixto_tarjeta', (parseFloat($p('mixto-tarjeta').value) || 0).toFixed(2));
            formData.set('mixto_transferencia', (parseFloat($p('mixto-transferencia').value) || 0).toFixed(2));
        }
        const pagoModal = document.getElementById('pagoModal');
        const bsSheet = document.getElementById('cobrarSheet');
        if (pagoModal && pagoModal.classList.contains('show')) {
            bootstrap.Modal.getInstance(pagoModal)?.hide();
        }
        if (bsSheet && bsSheet.classList.contains('open')) {
            bsSheet.classList.remove('open');
            document.getElementById('cobrarSheetOverlay')?.classList.remove('visible');
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
        .then(data => {
            cart.length = 0;
            renderCart();
            playBeep('success');
            ultimaVentaId = data.venta_id;
            resetearCliente();
            mostrarPostPago(data);
        })
        .catch(err => {
            isSubmitting = false;
            playBeep('error');
            showToast(err?.message || err?.error || 'Error al procesar venta', 'danger');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="shine"></span><i class="bi bi-check2-circle me-1"></i> Cobrar';
        });
    }

    function mostrarUndoRemoval(item) {
        const nombre = escapeHtml(item.nombre);
        const toastEl = $('scanToast');
        const body = $('scanToastBody');
        toastEl.className = 'toast align-items-center text-white border-0 bg-warning';
        body.innerHTML = `"${nombre}" eliminado. <button type="button" class="btn btn-sm btn-link text-white p-0 ms-2 fw-bold" onclick="POS.deshacerRemocion()">Deshacer</button>`;
        new bootstrap.Toast(toastEl, { delay: 5000 }).show();
        setTimeout(() => { lastRemovedItem = null; }, 5000);
    }

    // ============ Crédito/Fiado Validation ============
    function validarCreditoFiado() {
        const select = $('cliente_id');
        if (!select || !select.value) {
            showToast('Selecciona un cliente antes de marcar como Fiado', 'warning');
            return false;
        }
        const opt = select.options[select.selectedIndex];
        if (!opt || opt.dataset.esFinal === '1') {
            showToast('Selecciona un cliente antes de marcar como Fiado', 'warning');
            return false;
        }
        const limite = parseFloat(opt.dataset.limite) || 0;
        const deuda = parseFloat(opt.dataset.deuda) || 0;
        const total = parseFloat($('hidden-total').value) || 0;
        // Si no tiene límite configurado, permitir
        if (limite <= 0) return true;
        const nuevoTotal = deuda + total;
        const disponible = limite - deuda;
        // Verificar si excede el límite
        if (nuevoTotal > limite) {
            const exceso = nuevoTotal - limite;
            $('credito-nombre').textContent = opt.textContent.trim();
            $('credito-limite').textContent = fmt(limite);
            $('credito-deuda').textContent = fmt(deuda);
            $('credito-disponible').textContent = fmt(Math.max(0, disponible));
            $('credito-total').textContent = fmt(total);
            $('credito-nuevo-saldo').textContent = fmt(nuevoTotal);
            $('credito-exceso').textContent = fmt(exceso);
            if (!creditoWarningInstance) {
                creditoWarningInstance = new bootstrap.Modal(document.getElementById('creditoWarningModal'));
            }
            creditoWarningInstance.show();
            return false;
        }
        return true;
    }

    function procesarPagoDirecto(metodo) {
        if (isSubmitting) return;
        const total = parseFloat($('hidden-total').value) || 0;
        if (total <= 0) { showToast('Total inválido', 'danger'); return; }

        // Validate almacen before proceeding
        const almacenId = getAlmacenId();
        if (validaStock && !almacenId) { showToast('Selecciona un almacén válido', 'danger'); return; }

        isSubmitting = true;
        const btn = document.querySelector(`.btn-pay[data-metodo="${metodo}"]`);
        const btnOrigHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
        }

        const form = $('pos-form');
        const formData = new FormData(form);
        formData.set('metodo_pago', metodo);
        formData.set('propina', '0');
        formData.set('general_descuento', (parseFloat(document.querySelector('input[name="general_descuento"]')?.value) || 0).toFixed(2));
        // Inject almacen_id for each cart item when stock validation is active
        if (validaStock) {
            cart.forEach(() => formData.append('almacen_id', almacenId));
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
        .then(data => {
            cart.length = 0;
            renderCart();
            playBeep('success');
            ultimaVentaId = data.venta_id;
            resetearCliente();
            mostrarPostPago(data);
        })
        .catch(err => {
            isSubmitting = false;
            playBeep('error');
            showToast(err?.message || err?.error || 'Error al procesar venta', 'danger');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btnOrigHtml;
            }
        });
    }

    function mostrarPostPago(data) {
        $('post-cliente').textContent = data.cliente || 'Consumidor Final';
        $('post-total').textContent = fmt(data.total);
        const metodoMap = { efectivo: 'Efectivo', tarjeta: 'Tarjeta', transferencia: 'Transferencia', fiado: 'Fiado', cuenta_abierta: 'Cuenta Abierta', mixto: 'Mixto' };
        $('post-metodo').textContent = metodoMap[data.metodo_pago] || data.metodo_pago;
        const ticketUrl = `/ventas/pdf/${data.venta_id}`;
        $('btn-ticket').href = ticketUrl;
        // Enable/disable facturar based on comprobante
        const facturarBtn = $('btn-facturar');
        if (data.tipo_comprobante === 'ecf') {
            facturarBtn.style.display = 'inline-flex';
            facturarBtn.onclick = () => facturarVenta(data.venta_id);
            // Auto-enviar e-CF después de mostrar el modal
            $('factura-status').innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split me-1"></i> Enviando e-CF...</span>';
            setTimeout(() => facturarVenta(data.venta_id), 800);
        } else {
            facturarBtn.style.display = 'none';
        }
        loadDayStats();
        loadTurnoHistory();
        new bootstrap.Modal($('postPagoModal')).show();
    }

    function facturarVenta(ventaId) {
        const id = ventaId || ultimaVentaId;
        if (!id) return;
        const btn = $('btn-facturar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Facturando...';
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
        fetch(`/ventas/facturar/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: '{}'
        })
        .then(r => { if (!r.ok) return r.json().then(e => Promise.reject(e.error || 'Error')); return r.json(); })
        .then(res => {
            $('factura-status').innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i> ${res.message || 'Facturado exitosamente'}</span>`;
        })
        .catch(err => {
            $('factura-status').innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> ${err}</span>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Facturar (e-CF)';
        });
    }

    function imprimirTicket() {
        if (!ultimaVentaId) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
        fetch(`/ventas/imprimir/${ultimaVentaId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: '{}'
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(res => showToast('Impresión enviada', 'success'))
        .catch(() => showToast('Error al imprimir', 'danger'));
    }

    // Event listener for payment monto-recibido (works for both modals via delegation)
    document.addEventListener('input', function(e) {
        if (e.target.closest('#cobrarSheet, #pagoModal')) {
            if (e.target.matches('#monto-recibido, #md-monto-recibido')) actualizarTotalPago();
        }
    });

    function renderizarFiltroCategoriasModal() {
        const sel = $('modal-categoria-filtro');
        if (!sel) return;
        let html = '<option value="">Todas</option>';
        categorias.forEach(c => {
            html += `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`;
        });
        sel.innerHTML = html;
    }

    function categoriaFiltroChange() {
        modalCategoriaFiltro = $('modal-categoria-filtro').value;
        modalBuscarProductos();
    }

    // ============ Main grid category filter ============
    function renderizarFiltroCategoriasMain() {
        const sel = $('main-categoria-filtro');
        if (!sel) return;
        let html = '<option value="">Todas las categorías</option>';
        categorias.forEach(c => {
            html += `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`;
        });
        sel.innerHTML = html;
    }

    function mainCategoriaFiltroChange() {
        mainCategoriaFiltro = $('main-categoria-filtro').value;
        if (searchQuery) triggerSearch();
    }

    // ============ Modal Productos + Teclado Virtual ============
    const PALETA_COLORES_MODAL = [
        { bg: '#fee2e2', fg: '#dc2626' }, { bg: '#ffedd5', fg: '#ea580c' },
        { bg: '#fef9c3', fg: '#ca8a04' }, { bg: '#dcfce7', fg: '#16a34a' },
        { bg: '#cffafe', fg: '#0891b2' }, { bg: '#dbeafe', fg: '#2563eb' },
        { bg: '#ede9fe', fg: '#7c3aed' }, { bg: '#fce7f3', fg: '#db2777' },
        { bg: '#ccfbf1', fg: '#0d9488' }, { bg: '#faf5ff', fg: '#a21caf' },
    ];
    const TECLADO_LAYOUTS = {
        us: [['q','w','e','r','t','y','u','i','o','p'],['a','s','d','f','g','h','j','k','l'],['z','x','c','v','b','n','m']],
        es: [['q','w','e','r','t','y','u','i','o','p'],['a','s','d','f','g','h','j','k','l','ñ'],['z','x','c','v','b','n','m']]
    };
    let tecladoIdiomaActual = 'es';
    let teclaShiftActivo = false;
    let cantidadesModal = {};

    function colorProductoModal(nombre) {
        let h = 0;
        for (let i = 0; i < nombre.length; i++) h = nombre.charCodeAt(i) + ((h << 5) - h);
        return PALETA_COLORES_MODAL[Math.abs(h) % PALETA_COLORES_MODAL.length];
    }

    function abrirModalProductos() {
        const modalEl = $('productosModal');
        if (window._productosModalInstance) {
            window._productosModalInstance.hide();
        }
        window._productosModalInstance = new bootstrap.Modal(modalEl, { keyboard: false });
        $('modal-buscar-producto').value = '';
        $('modal-btn-limpiar').style.display = 'none';
        $('modal-item-notas').value = '';
        $('modal-item-curso').value = 'fuerte';
        $('modal-categoria-filtro').value = '';
        modalCategoriaFiltro = '';
        cantidadesModal = {};
        teclaShiftActivo = false;
        renderizarFiltroCategoriasModal();
        renderizarTecladoModal();
        tecladoIdioma('es');
        renderizarProductosModal('');
        window._productosModalInstance.show();
        setTimeout(() => $('modal-buscar-producto').focus(), 300);
    }

    function cerrarModalProductos() {
        if (window._productosModalInstance) {
            window._productosModalInstance.hide();
        }
    }

    function modalBuscarProductos() {
        const q = $('modal-buscar-producto').value.trim();
        $('modal-btn-limpiar').style.display = q.length > 0 ? 'inline-block' : 'none';
        renderizarProductosModal(q);
    }

    function modalLimpiarBusqueda() {
        $('modal-buscar-producto').value = '';
        $('modal-btn-limpiar').style.display = 'none';
        modalBuscarProductos();
        $('modal-buscar-producto').focus();
    }

    function renderizarProductosModal(filtro) {
        const container = $('modal-productos-grid');
        const q = (filtro || '').toLowerCase();
        const results = productos.filter(p => {
            const matchNombre = (p.nombre || '').toLowerCase().includes(q);
            const matchCodigo = (p.codigo_barras || '').toLowerCase().includes(q);
            const matchCategoria = !modalCategoriaFiltro || String(p.categoria_id) === modalCategoriaFiltro;
            return (matchNombre || matchCodigo) && matchCategoria;
        });
        if (results.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-4" style="color:var(--pos-text-muted);"><i class="bi bi-search" style="font-size:2.5rem;opacity:.4;display:block;margin-bottom:8px;"></i>Sin resultados</div>';
            return;
        }
        let html = '';
        results.forEach(p => {
            const id = p.id;
            if (cantidadesModal[id] === undefined) cantidadesModal[id] = 1;
            const qty = cantidadesModal[id];
            const c = colorProductoModal(p.nombre);
            const initial = (p.nombre || '?').charAt(0).toUpperCase();
            const stockCls = !validaStock ? 'bg-warning text-dark' : (p.stock <= 0 ? 'bg-secondary' : p.stock <= 5 ? 'bg-danger' : 'bg-warning text-dark');
            const stockTxt = p.stock <= 0 ? 'Sin stock' : p.stock + ' uds';
            const outCls = (validaStock && p.stock <= 0) ? ' out-of-stock' : '';
            let imgHtml;
            if (p.imagen_url) {
                imgHtml = `<img class="modal-prod-img" src="${p.imagen_url}" alt="" onerror="this.onerror=null;this.remove();this.nextElementSibling.style.display='flex';">`;
                imgHtml += `<div class="modal-prod-img-placeholder" style="background:${c.bg};color:${c.fg};display:none;">${initial}</div>`;
            } else {
                imgHtml = `<div class="modal-prod-img-placeholder" style="background:${c.bg};color:${c.fg};">${initial}</div>`;
            }
            html += `
            <div class="col-4 col-md-3 col-lg-2">
                <div class="modal-prod-card${outCls}" onclick="agregarProductoDesdeModal(${id})">
                    <span class="modal-prod-stock-badge badge ${stockCls}">${stockTxt}</span>
                    ${imgHtml}
                    <div class="modal-prod-name">${escapeHtml(p.nombre)}</div>
                    <div class="modal-prod-price">${fmt(p.precio)}</div>
                    <div class="modal-prod-qty" onclick="event.stopPropagation()">
                        <button type="button" onpointerdown="cambiarQtyModal(${id}, -1)">&#8722;</button>
                        <span id="mqty-${id}">${qty}</span>
                        <button type="button" onpointerdown="cambiarQtyModal(${id}, 1)">+</button>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
    }

    function cambiarQtyModal(productoId, delta) {
        if (cantidadesModal[productoId] === undefined) cantidadesModal[productoId] = 1;
        let nueva = cantidadesModal[productoId] + delta;
        if (nueva < 1) nueva = 1;
        if (nueva > 99) nueva = 99;
        cantidadesModal[productoId] = nueva;
        const span = $('mqty-' + productoId);
        if (span) span.textContent = nueva;
    }

    function agregarProductoDesdeModal(id) {
        const p = productos.find(x => x.id === id);
        if (!p) { showToast('Producto no encontrado', 'danger'); return; }
        if (validaStock && p.stock <= 0) { showToast('Producto sin stock', 'warning'); return; }
        const qty = cantidadesModal[id] || 1;
        const existing = cart.find(x => x.id === id);
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ id: p.id, nombre: p.nombre, precio: p.precio, itbis_p: p.itbis_p, qty: qty, stock: p.stock, imagen_url: p.imagen_url, descuento: 0, descuento_tipo: 'monto' });
        }
        renderCart('add');
        showToast(`+ ${qty}× ${p.nombre}`, 'success', 1200);
        cerrarModalProductos();
    }

    // Teclado virtual
    function renderizarTecladoModal() {
        const container = $('teclado-rows');
        if (!container) return;
        const layout = TECLADO_LAYOUTS[tecladoIdiomaActual] || TECLADO_LAYOUTS.es;
        let html = '<div class="tecla-row">';
        ['1','2','3','4','5','6','7','8','9','0'].forEach(n => {
            html += `<button class="tecla" onpointerdown="teclaPulsar('${n}')" type="button">${n}</button>`;
        });
        html += '</div>';
        layout.slice(0, -1).forEach(fila => {
            html += '<div class="tecla-row">';
            fila.forEach(letra => {
                const display = teclaShiftActivo ? letra.toUpperCase() : letra;
                html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
            });
            html += '</div>';
        });
        html += '<div class="tecla-row">';
        const shiftCls = teclaShiftActivo ? ' active' : '';
        html += `<button class="tecla tecla-func tecla-shift${shiftCls}" onpointerdown="teclaMayusculas()" type="button"><i class="bi bi-arrow-up-short fs-4"></i></button>`;
        layout[layout.length - 1].forEach(letra => {
            const display = teclaShiftActivo ? letra.toUpperCase() : letra;
            html += `<button class="tecla" onpointerdown="teclaPulsar('${letra}')" type="button">${display}</button>`;
        });
        html += `<button class="tecla tecla-func tecla-backspace" onpointerdown="teclaBorrar()" type="button"><i class="bi bi-backspace fs-4"></i></button>`;
        html += '</div>';
        html += '<div class="tecla-row">';
        html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar(',')" type="button">,</button>`;
        html += `<button class="tecla tecla-func tecla-space" onpointerdown="teclaPulsar(' ')" type="button"><span class="fw-normal" style="font-size:1rem;">Espacio</span></button>`;
        html += `<button class="tecla tecla-punct" onpointerdown="teclaPulsar('.')" type="button">.</button>`;
        html += `<button class="tecla tecla-enter" onpointerdown="teclaEnter()" type="button"><i class="bi bi-arrow-return-left fs-4"></i></button>`;
        html += '</div>';
        container.innerHTML = html;
    }

    function tecladoIdioma(idioma) {
        tecladoIdiomaActual = idioma;
        const usBtn = $('btn-idioma-us');
        const esBtn = $('btn-idioma-es');
        if (usBtn) usBtn.classList.toggle('active', idioma === 'us');
        if (esBtn) esBtn.classList.toggle('active', idioma === 'es');
        renderizarTecladoModal();
    }

    function teclaPulsar(caracter) {
        const input = $('modal-buscar-producto');
        const start = input.selectionStart || input.value.length;
        const end = input.selectionEnd || input.value.length;
        const val = input.value;
        const letra = teclaShiftActivo ? caracter.toUpperCase() : caracter;
        input.value = val.substring(0, start) + letra + val.substring(end);
        const newPos = start + letra.length;
        input.setSelectionRange(newPos, newPos);
        input.focus();
        if (teclaShiftActivo) { teclaShiftActivo = false; renderizarTecladoModal(); }
        modalBuscarProductos();
    }

    function teclaMayusculas() { teclaShiftActivo = !teclaShiftActivo; renderizarTecladoModal(); }

    function teclaBorrar() {
        const input = $('modal-buscar-producto');
        const start = input.selectionStart || input.value.length;
        const end = input.selectionEnd || input.value.length;
        if (start === 0 && end === 0) return;
        if (start !== end) {
            input.value = input.value.substring(0, start) + input.value.substring(end);
            input.setSelectionRange(start, start);
        } else {
            input.value = input.value.substring(0, start - 1) + input.value.substring(start);
            input.setSelectionRange(start - 1, start - 1);
        }
        input.focus();
        modalBuscarProductos();
    }

    function teclaEnter() { cerrarModalProductos(); }

    // ============ Carrito ============
    function addToCart(id, fromScanner = false) {
        const p = productos.find(x => x.id === id);
        if (!p) {
            showToast(`Producto #${id} no encontrado`, 'danger');
            return;
        }
        const existing = cart.find(x => x.id === id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                id: p.id, nombre: p.nombre, precio: p.precio,
                itbis_p: p.itbis_p, qty: 1, stock: p.stock, imagen_url: p.imagen_url,
                descuento: 0, descuento_tipo: 'monto'
            });
        }
        if (fromScanner) {
            $('scan-input').classList.add('scanner-flash');
            setTimeout(() => $('scan-input').classList.remove('scanner-flash'), 400);
            playBeep('scan');
        }
        // Limpiar input y mostrar carrito
        $('scan-input').value = '';
        searchQuery = '';
        hideSearchResults();
        $('products-viewport').style.display = 'none';
        $('cart-viewport').style.display = 'block';
        renderCart('add');
        $('scan-input').focus();
        if (fromScanner) showToast(`+ ${p.nombre}`, 'success', 1200);
    }

    function renderCart(anim = null) {
        const list = $('cart-list');
        const empty = $('empty-cart-msg');
        const countBadge = $('cart-count');
        const clearBtn = $('btn-clear-cart');
        if (cart.length === 0) {
            list.innerHTML = '';
            empty.style.display = 'flex';
            countBadge.textContent = '0';
            clearBtn.disabled = true;
        } else {
            empty.style.display = 'none';
            countBadge.textContent = cart.length;
            countBadge.classList.remove('pulse');
            void countBadge.offsetWidth;
            countBadge.classList.add('pulse');
            clearBtn.disabled = false;
            list.innerHTML = cart.map((item, index) => {
                const subtotal = item.precio * item.qty;
                const descuentoItem = parseFloat(item.descuento) || 0;
                const descuentoAplicado = item.descuento_tipo === 'porcentaje' 
                    ? (subtotal * descuentoItem / 100) 
                    : descuentoItem;
                const subtotalConDesc = Math.max(0, subtotal - descuentoAplicado);
                const itbis = subtotalConDesc * (item.itbis_p / 100);
                return `
                <div class="cart-item ${anim === 'add' && index === cart.length-1 ? 'adding' : ''}" data-index="${index}">
                    <img src="${item.imagen_url}" class="ci-img" alt="" onerror="this.onerror=null;this.src='${placeholder}'">
                    <div class="ci-info">
                        <div class="ci-name">${escapeHtml(item.nombre)}</div>
                        <div class="ci-meta">
                            <span class="ci-qty">
                                <button type="button" data-action="dec" data-index="${index}" aria-label="Disminuir cantidad">−</button>
                                <span class="qty-val" aria-label="Cantidad">${item.qty}</span>
                                <button type="button" data-action="inc" data-index="${index}" aria-label="Aumentar cantidad">+</button>
                            </span>
                            <span>× ${fmt(item.precio)}</span>
                        </div>
                        <div class="ci-discount">
                            <label for="desc-${index}" class="visually-hidden">Descuento línea ${index + 1}</label>
                            <div class="discount-input-group">
                                <button type="button" 
                                        class="discount-toggle ${item.descuento_tipo === 'porcentaje' ? 'active' : ''}" 
                                        data-action="toggle-discount-type" 
                                        data-index="${index}"
                                        title="Cambiar entre monto/porcentaje"
                                        aria-label="Cambiar tipo de descuento">${item.descuento_tipo === 'porcentaje' ? '%' : '$'}</button>
                                <input type="number" 
                                       id="desc-${index}"
                                       class="discount-input" 
                                       data-action="set-discount" 
                                       data-index="${index}"
                                       value="${item.descuento || ''}" 
                                       min="0" 
                                       step="0.01"
                                       placeholder="Desc"
                                       aria-label="Descuento de la línea ${index + 1}">
                            </div>
                            ${descuentoAplicado > 0 ? `<small class="discount-applied">-${fmt(descuentoAplicado)}</small>` : ''}
                        </div>
                    </div>
                    <div class="ci-right">
                        <div class="ci-subtotal">${fmt(subtotalConDesc)}</div>
                        <div class="ci-itbis">+ ITBIS ${fmt(itbis)}</div>
                    </div>
                    <button type="button" class="ci-remove" data-action="remove" data-index="${index}" title="Eliminar" aria-label="Eliminar producto del carrito">
                        <i class="bi bi-x-circle" aria-hidden="true"></i>
                    </button>
                    <input type="hidden" name="producto_id[]" value="${item.id}">
                    <input type="hidden" name="precio[]" value="${item.precio.toFixed(2)}">
                    <input type="hidden" name="cantidad[]" value="${item.qty}">
                    <input type="hidden" name="subtotal[]" value="${subtotalConDesc.toFixed(2)}">
                    <input type="hidden" name="descuento[]" value="${descuentoAplicado.toFixed(2)}">
                    <input type="hidden" name="descuento_tipo[]" value="${item.descuento_tipo}">
                </div>`;
            }).join('');
        }
        calculateTotals();
    }

    function calculateTotals() {
        const descuentoGeneral = parseFloat($('input-general-descuento').value) || 0;
        let subtotal = 0, itbis = 0, totalDescuentos = 0;
        const lineData = [];
        cart.forEach(item => {
            const lineSub = item.precio * item.qty;
            const descuentoItem = parseFloat(item.descuento) || 0;
            const descuentoAplicado = item.descuento_tipo === 'porcentaje' 
                ? (lineSub * descuentoItem / 100) 
                : descuentoItem;
            const subtotalConDesc = Math.max(0, lineSub - descuentoAplicado);
            subtotal += lineSub;
            totalDescuentos += descuentoAplicado;
            lineData.push({ subtotalConDesc, itbis_p: item.itbis_p });
        });
        // Recalcular ITBIS proporcionalmente aplicando descuento general
        const baseImponibleTotal = subtotal - totalDescuentos;
        if (baseImponibleTotal > 0 && descuentoGeneral > 0) {
            itbis = 0;
            lineData.forEach(ld => {
                const proporcion = Math.min(1, ld.subtotalConDesc / baseImponibleTotal);
                const descuentoProporcional = descuentoGeneral * proporcion;
                const baseFinal = Math.max(0, ld.subtotalConDesc - descuentoProporcional);
                itbis += baseFinal * (ld.itbis_p / 100);
            });
        } else {
            lineData.forEach(ld => {
                itbis += ld.subtotalConDesc * (ld.itbis_p / 100);
            });
        }
        const descuentoTotal = totalDescuentos + descuentoGeneral;
        const total = Math.max(0, subtotal - descuentoTotal + itbis);
        $('display-subtotal').innerText = fmt(subtotal);
        $('display-itbis').innerText = fmt(itbis);
        $('display-total').innerText = fmt(total);
        $('hidden-total').value = total.toFixed(2);
        $('hidden-subtotal').value = subtotal.toFixed(2);
        $('hidden-itbis').value = itbis.toFixed(2);
    }

    // ============ Tabs / Filtros ============
    function renderTabCounts() {
        const available = validaStock ? productos.filter(p => p.stock > 0).length : productos.length;
        $('count-all').textContent = productos.length;
        $('count-low').textContent = validaStock ? productos.filter(p => p.stock > 0 && p.stock <= 15).length : 0;
        $('count-avail').textContent = available;
        $('count-pop').textContent = productos.filter(p => p.ventas_count > 0).length;
    }

    function filterProductos(list) {
        if (!validaStock) {
            if (activeFilter === 'popular') return list.sort((a,b) => b.ventas_count - a.ventas_count);
            if (activeFilter === 'low') return [];
            return list;
        }
        switch (activeFilter) {
            case 'low': return list.filter(p => p.stock > 0 && p.stock <= 15);
            case 'available': return list.filter(p => p.stock > 0);
            case 'popular': return list.filter(p => p.ventas_count > 0).sort((a,b) => b.ventas_count - a.ventas_count);
            default: return list;
        }
    }

    // ============ Search (mostrar productos en grid) ============
    function triggerSearch() {
        const query = $('scan-input').value.toLowerCase().trim();
        searchQuery = query;
        const dropdown = $('search-results');

        // Show/hide category filter
        const catFilterDiv = $('pos-category-filter');
        if (query.length < 1) {
            dropdown.classList.remove('show');
            $('products-viewport').style.display = 'none';
            $('cart-viewport').style.display = 'block';
            if (catFilterDiv) catFilterDiv.style.display = 'none';
            return;
        }
        if (catFilterDiv) catFilterDiv.style.display = 'block';

        // Apply category filter
        let filteredProducts = productosPre.filter(p =>
            p.nl.includes(query) ||
            (p.cl && p.cl.includes(query))
        );
        if (mainCategoriaFiltro) {
            filteredProducts = filteredProducts.filter(p => String(p.categoria_id) === mainCategoriaFiltro);
        }
        const filtered = filterProductos(filteredProducts).slice(0, 12);

        if (filtered.length > 0) {
            dropdown.innerHTML = filtered.map(p => `
                <div class="res-item" data-action="add" data-id="${p.id}">
                    <img src="${p.imagen_url}" class="res-img" alt="" onerror="this.onerror=null;this.src='${placeholder}'">
                    <div class="res-info">
                        <div class="res-name">${escapeHtml(p.nombre)}</div>
                        <div class="res-meta">${escapeHtml(p.codigo_barras || 'Sin código')} · ${escapeHtml(p.unidad_medida)}</div>
                    </div>
                    <div class="res-right">
                        <div class="res-price">${fmt(p.precio)}</div>
                        <div class="res-meta">${p.stock > 0 ? p.stock + ' disp.' : 'Sin stock'}</div>
                    </div>
                </div>
            `).join('');
        } else {
            dropdown.innerHTML = `<div class="res-empty"><i class="bi bi-search"></i><div>Sin resultados para "<strong>${escapeHtml(query)}</strong>"</div></div>`;
        }
        dropdown.classList.add('show');

        // Mostrar grid inferior con resultados
        renderProductsGrid(filtered);
    }

    function renderProductsGrid(items) {
        const viewport = $('products-viewport');
        const cartViewport = $('cart-viewport');
        if (items.length === 0) {
            viewport.style.display = 'none';
            cartViewport.style.display = 'block';
            return;
        }
        viewport.style.display = 'grid';
        cartViewport.style.display = 'none';
        viewport.innerHTML = items.map(p => {
            const stockCls = p.stock === 0 ? 'out' : p.stock <= 5 ? 'crit' : p.stock <= 15 ? 'low' : 'ok';
            const stockLbl = p.stock === 0 ? 'Agotado' : p.stock + ' disp.';
            return `
            <button type="button" class="pos-product-card ${p.stock === 0 ? 'out-of-stock' : ''}" data-action="add" data-id="${p.id}">
                <img src="${p.imagen_url}" class="ppc-img" alt="" onerror="this.onerror=null;this.src='${placeholder}'">
                <div class="ppc-name">${escapeHtml(p.nombre)}</div>
                <div class="ppc-price">${fmt(p.precio)}</div>
                <span class="ppc-stock ${stockCls}">${stockLbl}</span>
            </button>`;
        }).join('');
    }

    function hideSearchResults() {
        $('search-results').classList.remove('show');
    }

    // ============ Procesar código (escáner) ============
    function procesarCodigo(code) {
        const codeLower = code.toLowerCase().trim();
        const p = codigoBarraMap.get(codeLower)
               || productosPre.find(x => x.cl && x.cl.includes(codeLower));
        if (p) {
            addToCart(p.id, true);
        } else {
            showToast(`No se encontró producto con código "${code}"`, 'danger');
            $('scan-input').classList.add('scanner-flash');
            setTimeout(() => $('scan-input').classList.remove('scanner-flash'), 500);
            $('scan-input').value = '';
        }
    }

    // ============ Cliente ============
    function onClienteChange() {
        const select = $('cliente_id');
        if (!select || !select.options.length) return;
        const opt = select.options[select.selectedIndex];
        if (!opt) return;
        const esFinal = opt.dataset.esFinal === '1';
        const tipo = opt.dataset.tipo || 'consumo';
        const deuda = parseFloat(opt.dataset.deuda) || 0;

        const badge = $('cliente-tipo-badge');
        const tiposMap = {
            'consumo': { text: 'Consumo', cls: '' },
            'credito_fiscal': { text: 'Crédito Fiscal', cls: 'warn' },
            'gubernamental': { text: 'Gubernamental', cls: 'warn' },
            'especial': { text: 'Especial', cls: 'warn' },
        };
        const t = tiposMap[tipo] || tiposMap['consumo'];
        badge.textContent = t.text;
        badge.className = 'cliente-pill ms-auto' + (deuda > 0 ? ' danger' : ' ' + t.cls);

        if (!esFinal) {
            fetchExistingItems($('cliente_id').value);
        }
    }

    function fetchExistingItems(clienteId) {
        fetch(`${urlCuentaAbierta}/${clienteId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }})
            .catch(() => {});
    }

    // ============ Buscar Cliente Modal ============
    function mostrarBuscarCliente() {
        $('buscar-cliente-input').value = '';
        $('clientes-resultados').innerHTML = '';
        new bootstrap.Modal($('clienteModal')).show();
        setTimeout(() => $('buscar-cliente-input')?.focus(), 300);
    }

    function seleccionarCliente(id, nombre) {
        const select = $('cliente_id');
        for (let opt of select.options) {
            if (parseInt(opt.value) == id) {
                select.value = id;
                break;
            }
        }
        $('cliente-selected-name').textContent = nombre;
        bootstrap.Modal.getInstance($('clienteModal'))?.hide();
        onClienteChange();
    }

    function resetearCliente() {
        const select = $('cliente_id');
        if (!select) return;
        const finalOpt = Array.from(select.options).find(o => o.dataset.esFinal === '1');
        if (finalOpt) {
            select.value = finalOpt.value;
            $('cliente-selected-name').textContent = finalOpt.textContent.trim();
            onClienteChange();
        }
    }

    // Client search as you type
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.cliente-result-item');
        if (item) {
            const id = parseInt(item.dataset.clienteId);
            const nombre = item.dataset.clienteNombre;
            if (id && nombre) seleccionarCliente(id, nombre);
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'buscar-cliente-input') {
            const q = e.target.value.trim();
            const container = $('clientes-resultados');
            if (q.length < 2) {
                container.innerHTML = '<div class="text-muted text-center py-3" style="font-size:0.85rem;">Escribe al menos 2 caracteres</div>';
                return;
            }
            // Filter from the existing clientes list
            const query = q.toLowerCase();
            const results = clientes.filter(c =>
                (c.nombre || '').toLowerCase().includes(query) ||
                (c.rnc || c.rnc_cedula || '').toLowerCase().includes(query)
            );
            if (results.length === 0) {
                container.innerHTML = '<div class="text-muted text-center py-3" style="font-size:0.85rem;">Sin resultados</div>';
                return;
            }
            container.innerHTML = results.map(c => {
                const initial = (c.nombre || '?').charAt(0).toUpperCase();
                const tipo = c.tipo_cliente === 'credito_fiscal' ? 'Crédito Fiscal' :
                            c.tipo_cliente === 'gubernamental' ? 'Gubernamental' :
                            c.tipo_cliente === 'especial' ? 'Especial' : 'Consumo';
                const nombreSeguro = escapeHtml(c.nombre);
                return `<div class="cliente-result-item" data-cliente-id="${c.id}" data-cliente-nombre='${nombreSeguro}'>
                    <div class="cr-icon" style="background:rgba(59,130,246,0.1);color:#60a5fa;">${initial}</div>
                    <div class="cr-info">
                        <div class="cr-name">${escapeHtml(c.nombre)}</div>
                        <div class="cr-meta">${tipo} ${c.rnc || c.rnc_cedula ? '· ' + escapeHtml(c.rnc || c.rnc_cedula) : ''}</div>
                    </div>
                </div>`;
            }).join('');
        }
    });

    // ============ Stats & history ============
    function loadDayStats() {
        fetch(`${urlStatsDia}?fecha=${dia}&sesion_id=${sesionId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }})
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(d => {
                $('day-total-display').textContent = fmt(d.total);
                $('day-count-display').textContent = d.count;
            })
            .catch(() => {});
    }

    function loadTurnoHistory() {
        fetch(`${urlTurno}/${sesionId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }})
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(d => {
                if (d.ventas && d.ventas.length > 0) {
                    $('turno-history-wrap').style.display = 'block';
                    $('turno-history').innerHTML = d.ventas.slice(0, 5).map(v => `
                        <div class="mini-history-item">
                            <span class="mh-id">#${String(v.id).padStart(4, '0')} · ${escapeHtml(v.cliente_nombre || '')}</span>
                            <span class="mh-total">${fmt(v.total)}</span>
                        </div>
                    `).join('');
                }
            })
            .catch(() => {});
    }

    function startTurnoTimer() {
        const updateTimer = () => {
            const now = new Date();
            const diff = Math.floor((now - turnoInicio) / 1000);
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            $('turno-timer').textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
        };
        updateTimer();
        window._turnoInterval = setInterval(updateTimer, 60000);
    }

    // ============ Event delegation (FIX BUGS) ============
    function handleClick(e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;
        const action = target.dataset.action;
        const id = parseInt(target.dataset.id);
        const index = parseInt(target.dataset.index);

        switch (action) {
            case 'add':
                e.preventDefault();
                if (id) addToCart(id);
                break;
            case 'remove':
                e.preventDefault();
                if (!isNaN(index)) POS.removeFromCart(index);
                break;
            case 'inc':
                e.preventDefault();
                if (!isNaN(index)) POS.updateQty(index, cart[index].qty + 1);
                break;
            case 'dec':
                e.preventDefault();
                if (!isNaN(index)) POS.updateQty(index, cart[index].qty - 1);
                break;
            case 'set-discount':
                return;
            case 'toggle-discount-type':
                e.preventDefault();
                if (!isNaN(index)) {
                    cart[index].descuento_tipo = cart[index].descuento_tipo === 'porcentaje' ? 'monto' : 'porcentaje';
                    renderCart();
                    console.log(`Tipo de descuento: ${cart[index].descuento_tipo}`);
                }
                break;
            case 'submit':
                e.preventDefault();
                if (isSubmitting) return;
                POS.submitForm(target.dataset.metodo);
                break;
            case 'select-comprobante':
                e.preventDefault();
                POS.selectComprobante(target.dataset.comprobante);
                break;
        }
    }

    // ============ Atajos teclado ============
    function handleGlobalKeys(e) {
        if (isSubmitting) return;
        const target = e.target;
        const inSearch = target.id === 'scan-input';

        if (e.key === 'F1') { e.preventDefault(); POS.toggleShortcutsHelp(); return; }
        if (e.key === 'F2') { e.preventDefault(); if (scanMode === 'search') { abrirModalProductos(); } else { $('scan-input').focus(); $('scan-input').select(); } return; }
        if (e.key === 'F4' && !['monto-recibido','propina-input','mixto-efectivo','mixto-tarjeta','mixto-transferencia'].includes(target.id)) { e.preventDefault(); if (cart.length > 0) POS.submitForm('efectivo'); return; }
        if (e.key === 'F5') { e.preventDefault(); if (cart.length > 0) POS.submitForm('tarjeta'); return; }
        if (e.key === 'F6') { e.preventDefault(); if (cart.length > 0) POS.submitForm('fiado'); return; }
        if (e.key === 'F7') { e.preventDefault(); if (cart.length > 0) POS.submitForm('cuenta_abierta'); return; }
        if (e.key === 'F9') { e.preventDefault(); if (cart.length > 0) POS.submitForm('transferencia'); return; }
        if (e.ctrlKey && e.key === 'k') { e.preventDefault(); mostrarBuscarCliente(); return; }
        if (e.ctrlKey && e.key === 'Backspace') { e.preventDefault(); POS.vaciarCarrito(); return; }
        if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); if (cart.length > 0) POS.submitForm('efectivo'); return; }
        if (e.key === 'Escape' && $('shortcutsHelp').classList.contains('show')) { e.preventDefault(); POS.toggleShortcutsHelp(); return; }
        if (e.key === 'Escape' && inSearch) { e.preventDefault(); POS.clearScan(); return; }
    }

    // ============ Init ============
    function init() {
        renderTabCounts();
        renderizarFiltroCategoriasMain();
        renderCart();
        onClienteChange();
        loadDayStats();
        loadTurnoHistory();
        startTurnoTimer();

        // Refrescar estadísticas cada minuto
        window._statsInterval = setInterval(loadDayStats, 60000);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            clearInterval(window._turnoInterval);
            clearInterval(window._statsInterval);
            if (window._productosModalInstance) {
                window._productosModalInstance.dispose();
                window._productosModalInstance = null;
            }
        });

        // Mute audio toggle
        const muteBtn = $('btn-mute-audio');
        if (muteBtn) {
            muteBtn.addEventListener('click', () => {
                audioEnabled = !audioEnabled;
                localStorage.setItem('pos_audio_enabled', audioEnabled);
                muteBtn.innerHTML = `<i class="bi bi-${audioEnabled ? 'volume-up' : 'volume-mute'}"></i>`;
            });
        }

        // Modo Escáner/Buscar
        $('mode-barcode').addEventListener('click', () => setScanMode('barcode'));
        $('mode-search').addEventListener('click', () => setScanMode('search'));

        // Click en scan-input abre modal si modo búsqueda
        $('scan-input').addEventListener('click', () => {
            if (scanMode === 'search') abrirModalProductos();
        });

        // Dispose modal on close
        const prodModalEl = $('productosModal');
        if (prodModalEl) {
            prodModalEl.addEventListener('hidden.bs.modal', function () {
                const inst = bootstrap.Modal.getInstance(this);
                if (inst) inst.dispose();
            });
        }

        // Dispose postPagoModal on hide to prevent memory leaks
        const postModalEl = $('postPagoModal');
        if (postModalEl) {
            postModalEl.addEventListener('hidden.bs.modal', function () {
                const inst = bootstrap.Modal.getInstance(this);
                if (inst) inst.dispose();
            });
        }

        // Tabs
        document.querySelectorAll('.pos-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.pos-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                activeFilter = tab.dataset.filter;
                if (searchQuery) triggerSearch();
            });
        });

        // Búsqueda en vivo (con debounce)
        $('scan-input').addEventListener('input', debounce(triggerSearch, 200));

        // Enter en input
        $('scan-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (!code) return;
                if (scanMode === 'barcode') {
                    procesarCodigo(code);
                } else {
                    const first = $('search-results').querySelector('.res-item');
                    if (first) {
                        addToCart(parseInt(first.dataset.id));
                    } else {
                        procesarCodigo(code);
                    }
                }
            }
        });

        // Descuento
        $('input-general-descuento').addEventListener('input', calculateTotals);

        // Cliente - cambiar a botón que abre modal
        const clienteSelect = $('cliente_id');
        if (clienteSelect) {
            clienteSelect.addEventListener('change', onClienteChange);
            // FORZAR cliente por defecto a "Consumidor Final" (evita que aparezca otro)
            const defaultClientId = {{ $clienteConsumidorFinal->id }};
            clienteSelect.value = String(defaultClientId);
            onClienteChange();
        }

        // Click-outside to close search
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.pos-search-wrap') && !e.target.closest('.pos-products')) {
                hideSearchResults();
            }
        });

        // Close shortcuts overlay on backdrop click
        $('shortcutsHelp').addEventListener('click', (e) => {
            if (e.target === $('shortcutsHelp')) POS.toggleShortcutsHelp();
        });

        // Event delegation (CRITICAL FIX)
        document.addEventListener('click', handleClick);

        // Discount input change
        document.addEventListener('change', function(e) {
            const target = e.target.closest('[data-action="set-discount"]');
            if (!target) return;
            const index = parseInt(target.dataset.index);
            if (!isNaN(index)) {
                const value = parseFloat(target.value) || 0;
                const item = cart[index];
                const lineTotal = item.precio * item.qty;
                if (lineTotal > 0) {
                    const descuentoAplicado = item.descuento_tipo === 'porcentaje' ? value : (value / lineTotal * 100);
                    if (descuentoAplicado > 50) {
                        if (!confirm('Descuento superior al 50%. ¿Confirmar?')) {
                            target.value = item.descuento || 0;
                            return;
                        }
                    }
                }
                item.descuento = Math.max(0, value);
                renderCart();
                console.log(`Descuento actualizado: ${item.descuento}`);
            }
        });

        // Global keyboard
        document.addEventListener('keydown', handleGlobalKeys);

        // Initial focus
        $('scan-input').focus();
    }

    function setScanMode(mode) {
        scanMode = mode;
        document.querySelectorAll('.search-mode-toggle button').forEach(b => b.classList.remove('active'));
        document.querySelector(`.search-mode-toggle button[data-mode="${mode}"]`).classList.add('active');
        const hint = $('scan-hint');
        if (mode === 'barcode') {
            hint.innerHTML = '<i class="bi bi-info-circle"></i> Escanea código y presiona Enter';
            $('scan-input').placeholder = 'Escanea código de barras...';
            $('scan-input').focus();
        } else {
            hint.innerHTML = '<i class="bi bi-info-circle"></i> Buscar productos por nombre o código';
            $('scan-input').placeholder = 'Buscar por nombre o código...';
            abrirModalProductos();
        }
    }

        // Confirmar venta con crédito excedido
        $('btn-confirmar-credito')?.addEventListener('click', function() {
            if (creditoWarningInstance) {
                creditoWarningInstance.hide();
            }
            isSubmitting = false;
            procesarPagoDirecto('fiado');
        });

    // Expose functions for inline onclick handlers
    window.seleccionarMetodoPago = seleccionarMetodoPago;
    window.addRecibido = addRecibido;
    window.actualizarTotalPago = actualizarTotalPago;
    window.asignarPropina = asignarPropina;
    window.procesarPago = procesarPago;
    window.mostrarPostPago = mostrarPostPago;
    window.facturarVenta = facturarVenta;
    window.mostrarUndoRemoval = mostrarUndoRemoval;
    window.imprimirTicket = imprimirTicket;
    window.mostrarBuscarCliente = mostrarBuscarCliente;
    window.seleccionarCliente = seleccionarCliente;
    window.cerrarModalProductos = cerrarModalProductos;
    window.agregarProductoDesdeModal = agregarProductoDesdeModal;
    window.cambiarQtyModal = cambiarQtyModal;
    window.modalBuscarProductos = modalBuscarProductos;
    window.modalLimpiarBusqueda = modalLimpiarBusqueda;
    window.tecladoIdioma = tecladoIdioma;
    window.categoriaFiltroChange = categoriaFiltroChange;
    window.mainCategoriaFiltroChange = mainCategoriaFiltroChange;

    // Init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endsection
