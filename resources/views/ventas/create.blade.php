@extends('layouts.app')

@section('title', 'Terminal de Ventas (POS)')

@section('fullbleed')
@php
    $dgiiAmbiente = config('dgii.ambiente_actual', 'sandbox');
    $dgiiSandbox = config('dgii.simular_dgii', true);
@endphp

<style>
    /* ============ Base POS Layout ============ */
:root {
    --pos-accent: #0ea5e9;
    --pos-accent-2: #06b6d4;
    --pos-accent-3: #0284c7;
    --pos-success: #10b981;
    --pos-warning: #f59e0b;
    --pos-danger: #ef4444;
    --pos-bg-light: #f8fafc;
    --pos-bg-dark: #020617;
    --pos-card-light: rgba(255, 255, 255, 0.03);
    --pos-card-dark: rgba(255, 255, 255, 0.08);
    --pos-card-border: rgba(255, 255, 255, 0.1);
    --pos-topbar-light: rgba(255, 255, 255, 0.08);
    --pos-topbar-dark: rgba(255, 255, 255, 0.12);
    --pos-search-light: rgba(255, 255, 255, 0.06);
    --pos-search-dark: rgba(255, 255, 255, 0.12);
    --pos-search-focus-light: rgba(14, 165, 233, 0.08);
    --pos-search-focus-dark: rgba(14, 165, 233, 0.12);
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
    --pos-accent-rgb: 14, 165, 233;
    --pos-accent2-rgb: 6, 182, 212;
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
        background: rgba(255, 255, 255, 0.08);
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
        background: rgba(255,255,255,0.04);
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
        background: rgba(14, 165, 233, 0.1);
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
        background: linear-gradient(135deg, #38bdf8, #06b6d4);
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
    background: var(--pos-topbar);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--pos-border);
    flex-shrink: 0;
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
}
.pos-keyhint kbd {
    background: rgba(var(--pos-text-rgb), 0.1);
    padding: 1px 5px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 0.7rem;
    color: var(--pos-text);
}

/* ============ Body grid ============ */
.pos-body {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 0;
    overflow: hidden;
}
.pos-left {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 16px;
    gap: 12px;
    min-width: 0;
}
.pos-right {
    background: var(--pos-topbar);
    backdrop-filter: blur(20px);
    border-left: 1px solid var(--pos-border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

    /* ============ Search Section ============ */
    .pos-search-wrap {
        position: relative;
        flex-shrink: 0;
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
        background: rgba(14, 165, 233, 0.05);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }
    .pos-search.scanner-flash {
        animation: scanFlash 0.5s ease;
    }
    @keyframes scanFlash {
        0% { background: rgba(14, 165, 233, 0.3); border-color: var(--pos-accent); }
        100% { background: rgba(14, 165, 233, 0.05); border-color: var(--pos-accent); }
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
        background: rgba(14, 165, 233, 0.05);
        box-shadow: 0 8px 24px rgba(14, 165, 233, 0.15);
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
        background: rgba(14, 165, 233, 0.1);
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
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(6, 182, 212, 0.1) 100%);
        border-radius: 14px;
        margin-top: 10px;
        border: 1px solid rgba(14, 165, 233, 0.3);
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
        background: linear-gradient(135deg, #38bdf8, #06b6d4);
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
    .btn-pay.tarjeta { background: #0ea5e9; }
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
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(6, 182, 212, 0.05) 100%);
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
    .cash-recibido-input:focus { outline: none; border-color: var(--pos-accent); background: rgba(14, 165, 233, 0.05); }

    .cambio-display {
        text-align: center;
        padding: 14px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(14, 165, 233, 0.05));
        border-radius: 12px;
    }
    .cambio-display.negativo { background: rgba(239, 68, 68, 0.1); }
    .cambio-display .cd-label { font-size: 0.7rem; text-transform: uppercase; color: #6ee7b7; font-weight: 700; }
    .cambio-display.negativo .cd-label { color: #fca5a5; }
    .cambio-display .cd-amount { font-size: 1.8rem; font-weight: 900; color: #6ee7b7; font-variant-numeric: tabular-nums; }
    .cambio-display.negativo .cd-amount { color: #fca5a5; }

    .quick-amount-btn {
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(14, 165, 233, 0.3);
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
    }

    /* ============ Modal Productos — Virtual Keyboard ============ */
    #productosModal .modal-content { background: var(--pos-bg); color: var(--pos-text); }
    #productosModal .modal-header { background: linear-gradient(135deg, var(--pos-accent), #0284c7); }
    #productosModal .form-control { background: var(--pos-card); border-color: var(--pos-border); color: var(--pos-text); }
    #productosModal .form-control::placeholder { color: var(--pos-text-muted); }
    #productosModal .form-control:focus { border-color: var(--pos-accent); box-shadow: 0 0 0 3px rgba(14,165,233,0.15); color: var(--pos-text); }

    .tecla {
        flex: 1; height: 52px; border-radius: 10px;
        border: 1px solid var(--pos-border);
        background: var(--pos-card); color: var(--pos-text);
        font-size: 1.15rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        touch-action: manipulation; user-select: none; -webkit-user-select: none;
        transition: background .08s, transform .08s; padding: 0 4px; min-width: 0;
    }
    .tecla:active { background: rgba(14,165,233,0.2); transform: scale(0.93); box-shadow: 0 0 0 2px rgba(14,165,233,0.2); }
    .tecla-func { background: rgba(255,255,255,0.06); font-size: 1rem; }
    .tecla-shift { flex: 1.6; }
    .tecla-shift.active { background: rgba(14,165,233,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
    .tecla-backspace { flex: 1.3; }
    .tecla-space { flex: 4; }
    .tecla-enter { flex: 1.3; background: var(--pos-accent); color: #fff; border-color: var(--pos-accent); }
    .tecla-punct { flex: 1; }
    .tecla-func:active { background: rgba(14,165,233,0.2); }
    .tecla-func.active { background: rgba(14,165,233,0.25); box-shadow: inset 0 2px 4px rgba(0,0,0,.3); border-color: var(--pos-accent); }
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
    .modal-prod-qty button:hover { background: rgba(14,165,233,0.15); border-color: var(--pos-accent); }
    .modal-prod-qty span { font-weight: 800; font-size: 1rem; min-width: 24px; text-align: center; color: var(--pos-text); }

    /* ============ Premium Payment Modal ============ */
    @keyframes cobrarGradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .cobrar-premium .modal-content { border-radius: 20px; overflow: hidden; border: 0; box-shadow: 0 25px 60px rgba(0,0,0,0.5); }
    .cobrar-premium .cobrar-header { background: linear-gradient(135deg, #059669, #10b981, #06b6d4, #059669); background-size: 300% 300%; animation: cobrarGradientShift 6s ease infinite; padding: 20px 24px 16px; color: #fff; }
    .cobrar-premium .cobrar-header .icon-circle { width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; backdrop-filter: blur(8px); }
    .cobrar-total-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 16px; padding: 16px 20px; text-align: center; border: 1px solid rgba(255,255,255,0.15); }
    .cobrar-total-card h2 { font-size: 3rem; font-weight: 900; color: var(--pos-text); font-variant-numeric: tabular-nums; }
    .metodo-btn { border: 2px solid var(--pos-border); border-radius: 14px; padding: 14px 6px; background: rgba(255,255,255,0.03); color: var(--pos-text); font-weight: 700; font-size: 0.9rem; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer; min-height: 68px; }
    .metodo-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
    .metodo-btn.active-metodo.efectivo { border-color: #10b981; background: rgba(16,185,129,0.12); color: #6ee7b7; }
    .metodo-btn.active-metodo.tarjeta { border-color: #0ea5e9; background: rgba(14,165,233,0.12); color: #38bdf8; }
    .metodo-btn.active-metodo.transferencia { border-color: #6366f1; background: rgba(99,102,241,0.12); color: #a5b4fc; }
    .metodo-btn.active-metodo.mixto { border-color: #f59e0b; background: rgba(245,158,11,0.12); color: #fbbf24; }
    .metodo-btn i { font-size: 1.5rem; }
    .input-premium { width: 100%; background: rgba(255,255,255,0.1); border: 2px solid var(--pos-border); border-radius: 12px; color: var(--pos-text); padding: 12px 16px; font-size: 1.25rem; font-weight: 800; text-align: center; font-variant-numeric: tabular-nums; }
    .input-premium:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
    .input-premium::placeholder { font-weight: 400; font-size: 1rem; color: var(--pos-text-muted); opacity: 0.5; }
    .pago-detalle { margin-top: 12px; }
    .pago-detalle label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--pos-text-muted); margin-bottom: 4px; display: block; }
    .cambio-display { text-align: center; padding: 12px 16px; border-radius: 12px; font-size: 1.5rem; font-weight: 800; }
    .cambio-display.positivo { background: rgba(16,185,129,0.15); color: #34d399; }
    .cambio-display.negativo { background: rgba(239,68,68,0.15); color: #f87171; }
    .propina-btn { border-radius: 50px; border: 2px solid rgba(16,185,129,0.3); background: transparent; color: #6ee7b7; font-weight: 700; padding: 10px 20px; font-size: 0.9rem; transition: all 0.15s; cursor: pointer; min-height: 44px; }
    .propina-btn:hover { background: rgba(16,185,129,0.1); border-color: #10b981; transform: scale(1.05); }
    .propina-btn.active { background: #10b981; border-color: #10b981; color: #fff; }
    #propina-input { height: 44px; text-align: center; background: rgba(255,255,255,0.06); border: 2px solid var(--pos-border); border-radius: 12px; color: var(--pos-text); font-weight: 700; font-size: 1.1rem; width: 100px; }
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
    #clienteModal .cliente-search-input:focus { outline: none; border-color: var(--pos-accent); box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
    .cliente-result-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; cursor: pointer; transition: background 0.15s; border: 1px solid transparent; margin-bottom: 4px; }
    .cliente-result-item:hover { background: rgba(14,165,233,0.05); border-color: var(--pos-border); }
    .cliente-result-item .cr-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .cliente-result-item .cr-info { flex: 1; min-width: 0; }
    .cliente-result-item .cr-name { font-weight: 700; color: var(--pos-text); }
    .cliente-result-item .cr-meta { font-size: 0.75rem; color: var(--pos-text-muted); }
</style>

<form id="pos-form" action="{{ route('ventas.store') }}" method="POST" autocomplete="off">
    @csrf

    <div class="pos-app">
        <!-- ============ TOP BAR ============ -->
        <div class="pos-topbar">
            <div class="caja-tag">
                <span class="pulse-dot"></span>
                <span>{{ $sesion->caja->nombre }}</span>
                @if($sesion->caja->codigo)
                    <span style="opacity: 0.7; font-size: 0.75rem;">{{ $sesion->caja->codigo }}</span>
                @endif
            </div>

            <select id="almacen-select" class="form-select form-select-sm d-inline-block w-auto" style="background:rgba(255,255,255,0.06);border-color:var(--pos-border);color:var(--pos-text);font-size:0.78rem;padding:4px 10px;border-radius:8px;max-width:160px;" title="Almacén de despacho">
                @foreach($almacenes as $alm)
                    <option value="{{ $alm->id }}" @if($loop->first) selected @endif>{{ $alm->nombre }}</option>
                @endforeach
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

            <a href="{{ route('cajas.cierre', $sesion->caja->id) }}" class="btn btn-sm btn-outline-danger rounded-pill" title="Cerrar caja y turno">
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
                    <select name="cliente_id" id="cliente_id" style="display:none;">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                    data-es-final="{{ $cliente->id == $clienteConsumidorFinal->id ? '1' : '0' }}"
                                    data-tipo="{{ $cliente->tipo_cliente ?? 'consumo' }}"
                                    data-deuda="{{ $cliente->balance_pendiente ?? 0 }}"
                                    {{ $cliente->id == $clienteConsumidorFinal->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}{{ $cliente->balance_pendiente > 0 ? ' (Debe: RD$'.number_format($cliente->balance_pendiente,0).')' : '' }}
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
                        <span class="label">ITBIS (18%)</span>
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

<!-- ============ Premium Payment Modal ============ -->
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
                        <h2 class="fw-bold mb-0" id="pago-total">RD$ 0.00</h2>
                    </div>
                </div>

                <!-- Fila 2: Métodos de pago grandes -->
                <div class="cobrar-section">
                    <div class="row g-2" id="pago-metodos">
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
                <div id="pago-efectivo" class="cobrar-section">
                    <div class="pago-detalle">
                        <label>Monto Recibido</label>
                        <input type="number" id="monto-recibido" class="input-premium" step="0.01" min="0" placeholder="0.00" value="" inputmode="decimal">
                        <div id="cambio-info" class="mt-2 cambio-display positivo d-none">
                            Cambio: <span class="fw-bold" id="cambio-monto">RD$ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Fila 4: Mixto (tres campos) -->
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

                <!-- Fila 5: Propina -->
                <div class="cobrar-section">
                    <div class="pago-detalle">
                        <label>Propina</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" id="propina-input" step="0.01" min="0" value="0" inputmode="decimal" oninput="actualizarTotalPago()">
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
                <button type="button" class="btn btn-success rounded-pill px-4" data-bs-dismiss="modal" onclick="POS.vaciarCarrito()">
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
    let isSubmitting = false;
    let lastRemovedItem = null;
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
                procesarPagoDirecto(metodo);
                return;
            }
            metodoPagoPendiente = metodo;
            mostrarPago(metodo);
        },

        toggleShortcutsHelp() {
            const overlay = $('shortcutsHelp');
            const isOpen = overlay.classList.contains('show');
            overlay.classList.toggle('show');
            if (!isOpen) {
                // trap focus inside panel
                setTimeout(() => {
                    const closeBtn = overlay.querySelector('.close-shortcuts');
                    if (closeBtn) closeBtn.focus();
                }, 100);
            }
        }
    };
    window.POS = POS;

    // ============ Payment Modal Functions ============
    let metodoPagoActual = 'efectivo';
    let ultimaVentaId = null;

    function mostrarPago(metodo) {
        const total = parseFloat($('hidden-total').value) || 0;
        if (total <= 0) { showToast('Total inválido', 'danger'); return; }
        $('pago-total').innerText = fmt(total);
        // Reset
        $('propina-input').value = '0';
        $('monto-recibido').value = total.toFixed(2);
        document.getElementById('cambio-info').classList.add('d-none');
        $('mixto-efectivo').value = '';
        $('mixto-tarjeta').value = '';
        $('mixto-transferencia').value = '';
        // Select method
        seleccionarMetodoPago(metodo);
        new bootstrap.Modal($('pagoModal')).show();
        setTimeout(() => $('monto-recibido')?.focus(), 400);
    }

    function seleccionarMetodoPago(metodo) {
        metodoPagoActual = metodo;
        document.querySelectorAll('#pago-metodos .metodo-btn').forEach(b => b.classList.remove('active-metodo'));
        document.querySelector(`#pago-metodos .metodo-btn[data-metodo="${metodo}"]`)?.classList.add('active-metodo');
        document.getElementById('pago-efectivo').style.display = metodo === 'efectivo' ? 'block' : 'none';
        document.getElementById('pago-mixto').style.display = metodo === 'mixto' ? 'block' : 'none';
        if (metodo === 'efectivo') {
            const total = parseFloat($('hidden-total').value) || 0;
            $('monto-recibido').value = total.toFixed(2);
            document.getElementById('cambio-info').classList.add('d-none');
            setTimeout(() => $('monto-recibido')?.focus(), 200);
        }
        actualizarTotalPago();
    }

    function actualizarTotalPago() {
        const totalBase = parseFloat($('hidden-total').value) || 0;
        const propina = parseFloat($('propina-input').value) || 0;
        const totalFinal = totalBase + propina;
        $('pago-total').innerText = fmt(totalFinal);

        if (metodoPagoActual === 'efectivo') {
            const recibido = parseFloat($('monto-recibido').value) || 0;
            const cambio = recibido - totalFinal;
            const cambioInfo = document.getElementById('cambio-info');
            const cambioMonto = document.getElementById('cambio-monto');
            if (recibido > 0 && cambio >= 0) {
                cambioInfo.classList.remove('d-none');
                cambioMonto.textContent = fmt(cambio);
            } else {
                cambioInfo.classList.add('d-none');
            }
        } else if (metodoPagoActual === 'mixto') {
            const eff = parseFloat($('mixto-efectivo').value) || 0;
            const card = parseFloat($('mixto-tarjeta').value) || 0;
            const trans = parseFloat($('mixto-transferencia').value) || 0;
            const suma = eff + card + trans;
            const restante = totalFinal - suma;
            const label = document.getElementById('mixto-restante');
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
        $('propina-input').value = (total * porcentaje / 100).toFixed(2);
        actualizarTotalPago();
        document.querySelectorAll('.propina-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function procesarPago() {
        if (isSubmitting) return;
        const total = parseFloat($('hidden-total').value) || 0;
        const propina = parseFloat($('propina-input').value) || 0;

        if (metodoPagoActual === 'efectivo') {
            const recibido = parseFloat($('monto-recibido').value) || 0;
            if (recibido < total + propina) {
                showToast('Monto recibido es menor al total', 'danger');
                return;
            }
        } else if (metodoPagoActual === 'mixto') {
            const eff = parseFloat($('mixto-efectivo').value) || 0;
            const card = parseFloat($('mixto-tarjeta').value) || 0;
            const trans = parseFloat($('mixto-transferencia').value) || 0;
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

        // Inject almacen_id for each cart item when stock validation is active
        if (validaStock) {
            cart.forEach(() => formData.append('almacen_id', almacenId));
        }

        // Add mixto amounts if applicable
        if (metodoPagoActual === 'mixto') {
            formData.set('mixto_efectivo', (parseFloat($('mixto-efectivo').value) || 0).toFixed(2));
            formData.set('mixto_tarjeta', (parseFloat($('mixto-tarjeta').value) || 0).toFixed(2));
            formData.set('mixto_transferencia', (parseFloat($('mixto-transferencia').value) || 0).toFixed(2));
        }
        bootstrap.Modal.getInstance($('pagoModal'))?.hide();

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
        .then(data => {
            playBeep('success');
            ultimaVentaId = data.venta_id;
            resetearCliente();
            mostrarPostPago(data);
        })
        .catch(err => {
            playBeep('error');
            showToast(err?.message || err?.error || 'Error al procesar venta', 'danger');
        })
        .finally(() => {
            isSubmitting = false;
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
            playBeep('success');
            ultimaVentaId = data.venta_id;
            resetearCliente();
            mostrarPostPago(data);
        })
        .catch(err => {
            playBeep('error');
            showToast(err?.message || err?.error || 'Error al procesar venta', 'danger');
        })
        .finally(() => {
            isSubmitting = false;
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
        fetch(`/ventas/facturar/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ _token: document.querySelector('input[name="_token"]')?.value })
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(res => {
            $('factura-status').innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i> ${res.message || 'Facturado exitosamente'}</span>`;
        })
        .catch(() => {
            $('factura-status').innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> Error al facturar</span>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Facturar (e-CF)';
        });
    }

    function imprimirTicket() {
        if (!ultimaVentaId) return;
        fetch(`/ventas/imprimir/${ultimaVentaId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ _token: document.querySelector('input[name="_token"]')?.value })
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(res => showToast('Impresión enviada', 'success'))
        .catch(() => showToast('Error al imprimir', 'danger'));
    }

    // Event listeners for premium payment modal
    document.addEventListener('input', function(e) {
        if (e.target.id === 'monto-recibido') actualizarTotalPago();
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
        const old = bootstrap.Modal.getInstance(modalEl);
        if (old) old.dispose();
        const modal = new bootstrap.Modal(modalEl, { keyboard: false });
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
        modal.show();
        setTimeout(() => $('modal-buscar-producto').focus(), 300);
    }

    function cerrarModalProductos() {
        const el = $('productosModal');
        const m = bootstrap.Modal.getInstance(el);
        if (m) m.hide();
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
            if (validaStock && p.stock <= 0) return false;
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
        let subtotal = 0, itbis = 0, totalDescuentos = 0;
        cart.forEach(item => {
            const lineSub = item.precio * item.qty;
            const descuentoItem = parseFloat(item.descuento) || 0;
            const descuentoAplicado = item.descuento_tipo === 'porcentaje' 
                ? (lineSub * descuentoItem / 100) 
                : descuentoItem;
            const subtotalConDesc = Math.max(0, lineSub - descuentoAplicado);
            subtotal += lineSub;
            totalDescuentos += descuentoAplicado;
            itbis += subtotalConDesc * (item.itbis_p / 100);
        });
        const descuentoGeneral = parseFloat($('input-general-descuento').value) || 0;
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

        if (query.length < 1) {
            dropdown.classList.remove('show');
            $('products-viewport').style.display = 'none';
            $('cart-viewport').style.display = 'block';
            return;
        }

        // Mostrar dropdown de búsqueda
        const filtered = filterProductos(productosPre.filter(p =>
            p.nl.includes(query) ||
            (p.cl && p.cl.includes(query))
        )).slice(0, 12);

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
            if (parseInt(opt.value) === id) {
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
                const nombreSeguro = JSON.stringify(c.nombre);
                return `<div class="cliente-result-item" data-cliente-id="${c.id}" data-cliente-nombre='${nombreSeguro}'>
                    <div class="cr-icon" style="background:rgba(14,165,233,0.1);color:#38bdf8;">${initial}</div>
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
        setInterval(updateTimer, 60000);
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
                e.preventDefault();
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
                    A11y && A11y.announce(`Descuento actualizado: ${item.descuento}`);
                }
                break;
            case 'toggle-discount-type':
                e.preventDefault();
                if (!isNaN(index)) {
                    cart[index].descuento_tipo = cart[index].descuento_tipo === 'porcentaje' ? 'monto' : 'porcentaje';
                    renderCart();
                    A11y && A11y.announce(`Tipo de descuento: ${cart[index].descuento_tipo}`);
                }
                break;
            case 'submit':
                e.preventDefault();
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
        renderCart();
        onClienteChange();
        loadDayStats();
        loadTurnoHistory();
        startTurnoTimer();

        // Refrescar estadísticas cada minuto
        setInterval(loadDayStats, 60000);

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

    // Expose functions for inline onclick handlers
    window.seleccionarMetodoPago = seleccionarMetodoPago;
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

    // Init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endsection
