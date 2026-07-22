{{-- UI System v2 — Partial compartido para todas las vistas --}}
{{-- Incluir con: @include('partials.premium-ui') --}}
{{-- Cada módulo define su acento en el wrapper principal:
     <div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
--}}

<style>
/* ============================================================
   DESIGN TOKENS (valores por defecto — el módulo los anula)
   ============================================================ */
:root {
    --accent: #10b981;
    --accent-rgb: 16, 185, 129;
    --accent-hover: #059669;
    --accent-light: #d1fae5;
    --radius-sm: .375rem;
    --radius: .65rem;
    --radius-lg: 1rem;
    --radius-xl: 1.25rem;
    --radius-2xl: 1.5rem;
    --radius-pill: 9999px;
    --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
    --shadow: 0 4px 6px -1px rgba(0,0,0,.08);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl: 0 20px 30px -5px rgba(0,0,0,.1);
    --page-py: 1.5rem;
    --page-px: 2rem;
}
@media (max-width: 575.98px) {
    :root { --page-py: .75rem; --page-px: .75rem; }
}

/* ============================================================
   KEYFRAMES
   ============================================================ */
@keyframes uiShift {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes uiFloat {
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-12px)}
}
@keyframes uiSlideUp {
    from{opacity:0;transform:translateY(24px)}
    to{opacity:1;transform:translateY(0)}
}
@keyframes uiFadeIn {
    from{opacity:0}
    to{opacity:1}
}

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.ui-page {
    padding: var(--page-py) var(--page-px);
    animation: uiSlideUp .5s ease;
    max-width: 100%;
}

/* ============================================================
   HEADER (animated gradient + burbujas)
   ============================================================ */
.ui-header {
    background:
        linear-gradient(135deg, rgba(0,0,0,.22), rgba(0,0,0,.08), rgba(255,255,255,.06), rgba(0,0,0,.22)),
        var(--accent, #10b981);
    background-size: 300% 300%;
    animation: uiShift 6s ease infinite;
    border-radius: var(--radius-2xl);
    padding: 1.75rem 2.25rem;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 8px 32px rgba(0,0,0,.1);
    margin-bottom: 1.25rem;
    animation-delay: var(--delay, 0s);
}
.ui-header::before {
    content: '';
    position: absolute;
    inset: -50%;
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    pointer-events: none;
}
.ui-header .bubble {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    pointer-events: none;
}
.ui-header .bubble:nth-child(1) {
    width: 80px; height: 80px; top: -20px; right: 10%;
    animation: uiFloat 4s ease-in-out infinite;
}
.ui-header .bubble:nth-child(2) {
    width: 50px; height: 50px; bottom: 10px; right: 28%;
    animation: uiFloat 5s ease-in-out infinite 1s;
}
.ui-header .bubble:nth-child(3) {
    width: 100px; height: 100px; bottom: -30px; right: 5%;
    animation: uiFloat 6s ease-in-out infinite .5s;
}

.ui-header-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
    gap: 1rem;
    flex-wrap: wrap;
}
.ui-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
}
.ui-header-title {
    font-weight: 700;
    font-size: 1.35rem;
    margin: 0;
    color: #fff;
    line-height: 1.3;
}
.ui-header-meta {
    font-size: .82rem;
    color: rgba(255,255,255,.75);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .25rem;
    margin-top: .15rem;
}
.ui-header-meta .divider {
    opacity: .5;
    margin: 0 .4rem;
}
.ui-header-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
}

/* Avatar dentro del header */
.ui-avatar-circle {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    border: 2px solid rgba(255,255,255,.35);
    flex-shrink: 0;
}

/* ============================================================
   CARDS (glassmorphism)
   ============================================================ */
.ui-card {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all .3s ease;
    animation: uiSlideUp .5s ease both;
    margin-bottom: 1rem;
    animation-delay: var(--delay, 0s);
}
.ui-card:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.ui-card-accent {
    height: 4px;
    background: linear-gradient(90deg, var(--accent, #10b981), rgba(255,255,255,.3));
}
.ui-card-body {
    padding: 1.5rem 1.75rem;
}

.ui-card-title {
    font-weight: 700;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: 1.25rem 1.75rem .15rem;
    margin: 0;
    color: #1e293b;
}
.ui-card-title i {
    font-size: 1.2rem;
    color: var(--accent, #10b981);
}
.ui-card-subtitle {
    color: #64748b;
    font-size: .85rem;
    padding: 0 1.75rem;
    margin-bottom: .5rem;
}

/* ============================================================
   STAT CARDS
   ============================================================ */
.ui-stat {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-2xl);
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all .3s ease;
    animation: uiSlideUp .5s ease both;
    margin-bottom: 1rem;
    animation-delay: var(--delay, 0s);
}
.ui-stat:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.ui-stat-body {
    padding: 1.25rem 1.5rem;
}
.ui-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1.2;
    color: var(--accent, #10b981);
}
.ui-stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: .35rem;
}
.ui-stat-sub {
    font-size: .75rem;
    color: #94a3b8;
    margin-top: .25rem;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.ui-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s ease;
    border-radius: var(--radius);
    font-size: .9rem;
    padding: .55rem 1.25rem;
    line-height: 1.4;
}
.ui-btn:focus-visible {
    outline: 2px solid var(--accent, #10b981);
    outline-offset: 2px;
}
.ui-btn:disabled, .ui-btn.disabled {
    opacity: .55;
    pointer-events: none;
}
.ui-btn-sm  { font-size: .8rem; padding: .4rem 1rem; }
.ui-btn-lg  { font-size: 1rem; padding: .7rem 1.75rem; }
.ui-btn-pill { border-radius: var(--radius-pill); }

/* Primary — usa el acento del módulo */
.ui-btn-primary {
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255,255,255,.35);
    color: #fff;
}
.ui-btn-primary:hover {
    background: rgba(255,255,255,.3);
    border-color: rgba(255,255,255,.5);
    color: #fff;
    transform: translateY(-1px);
}

/* Solid — botón relleno con color acento */
.ui-btn-solid {
    background: linear-gradient(135deg, var(--accent, #10b981), var(--accent-hover, #059669));
    color: #fff;
    box-shadow: 0 4px 14px rgba(var(--accent-rgb, 16,185,129), .3);
}
.ui-btn-solid:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(var(--accent-rgb, 16,185,129), .45);
    color: #fff;
}

/* Danger */
.ui-btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    box-shadow: 0 4px 14px rgba(239,68,68,.3);
}
.ui-btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239,68,68,.45);
    color: #fff;
}

/* Ghost / Outline */
.ui-btn-ghost {
    background: rgba(255,255,255,.8);
    border: 1.5px solid #e2e8f0;
    color: #475569;
}
.ui-btn-ghost:hover {
    background: #fff;
    border-color: #cbd5e1;
    color: #1e293b;
}

/* Link-style */
.ui-btn-link {
    background: transparent;
    border: none;
    color: var(--accent, #10b981);
    padding: 0;
    text-decoration: none;
}
.ui-btn-link:hover {
    color: var(--accent-hover, #059669);
    text-decoration: underline;
}

/* ============================================================
   ACTION ICON BUTTONS (view, edit, delete, print)
   ============================================================ */
.ui-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: .5rem;
    border: 1.5px solid transparent;
    text-decoration: none;
    font-size: .85rem;
    transition: all .2s ease;
    cursor: pointer;
}
.ui-action:focus-visible {
    outline: 2px solid var(--accent, #10b981);
    outline-offset: 2px;
}
.ui-action-view {
    background: rgba(59,130,246,.1);
    color: #3b82f6;
    border-color: rgba(59,130,246,.2);
}
.ui-action-view:hover {
    background: #3b82f6;
    color: #fff;
    border-color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,130,246,.3);
}
.ui-action-edit {
    background: rgba(245,158,11,.1);
    color: #d97706;
    border-color: rgba(245,158,11,.2);
}
.ui-action-edit:hover {
    background: #f59e0b;
    color: #fff;
    border-color: #f59e0b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245,158,11,.3);
}
.ui-action-delete {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border-color: rgba(239,68,68,.2);
}
.ui-action-delete:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,.3);
}
.ui-action-print {
    background: rgba(100,116,139,.1);
    color: #64748b;
    border-color: rgba(100,116,139,.2);
}
.ui-action-print:hover {
    background: #64748b;
    color: #fff;
    border-color: #64748b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(100,116,139,.3);
}

/* ============================================================
   FORM ELEMENTS
   ============================================================ */
.ui-label {
    display: block;
    font-weight: 600;
    font-size: .85rem;
    color: #334155;
    margin-bottom: .35rem;
}
.ui-input,
.ui-select,
.ui-textarea {
    display: block;
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: var(--radius);
    padding: .55rem 1rem;
    font-size: .9rem;
    transition: all .2s ease;
    background: #fff;
    color: #1e293b;
}
.ui-input:focus,
.ui-select:focus,
.ui-textarea:focus {
    border-color: var(--accent, #10b981);
    outline: 2px solid rgba(var(--accent-rgb, 16,185,129), .15);
    outline-offset: -1px;
    background: #fff;
}
.ui-input::placeholder { color: #94a3b8; }
.ui-textarea { min-height: 100px; resize: vertical; }

/* Input group */
.ui-input-group {
    display: flex;
    align-items: center;
}
.ui-input-group .ui-input-group-text {
    display: flex;
    align-items: center;
    padding: .55rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-right: 0;
    border-radius: var(--radius) 0 0 var(--radius);
    background: #f8fafc;
    color: #64748b;
    font-size: .9rem;
    white-space: nowrap;
}
.ui-input-group .ui-input {
    border-left: 0;
    border-radius: 0 var(--radius) var(--radius) 0;
}

/* ============================================================
   TABLE
   ============================================================ */
.ui-table {
    --bs-table-bg: transparent;
    --bs-table-color: #1e293b;
    --bs-table-border-color: #f1f5f9;
    --bs-table-hover-bg: rgba(var(--accent-rgb, 16,185,129), .03);
    width: 100%;
    margin: 0;
    border-collapse: collapse;
}
.ui-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.ui-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
    transition: background .15s;
}
.ui-table tbody tr:last-child td { border-bottom: none; }
.ui-table tbody tr:hover { background: rgba(var(--accent-rgb, 16,185,129), .03); }

/* ============================================================
   BADGES (semánticos)
   ============================================================ */
.ui-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .35rem .7rem;
    border-radius: var(--radius-pill);
    font-size: .75rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: all .2s ease;
}
.ui-badge-success {
    background: rgba(34,197,94,.1);
    color: #16a34a;
    border: 1px solid rgba(34,197,94,.2);
}
.ui-badge-warning {
    background: rgba(245,158,11,.1);
    color: #d97706;
    border: 1px solid rgba(245,158,11,.2);
}
.ui-badge-danger {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border: 1px solid rgba(239,68,68,.2);
}
.ui-badge-info {
    background: rgba(59,130,246,.1);
    color: #2563eb;
    border: 1px solid rgba(59,130,246,.2);
}
.ui-badge-neutral {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.ui-badge-primary {
    background: rgba(var(--accent-rgb, 16,185,129), .1);
    color: var(--accent, #10b981);
    border: 1px solid rgba(var(--accent-rgb, 16,185,129), .2);
}

/* ============================================================
   STICKY BAR (fixed inferior para forms)
   ============================================================ */
.ui-sticky-bar {
    position: fixed;
    bottom: 0;
    left: 280px;
    right: 0;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(20px);
    border-top: 2px solid var(--accent, #10b981);
    padding: .7rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,.08);
    animation: uiSlideUp .3s ease;
}
.ui-sticky-bar-inner {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: .75rem;
    max-width: 100%;
}
@media (max-width: 991.98px) {
    .ui-sticky-bar { left: 0; }
}
@media (max-width: 575.98px) {
    .ui-sticky-bar { padding: .6rem .75rem; }
    .ui-sticky-bar-inner { gap: .4rem; }
    .ui-sticky-bar .ui-btn { font-size: .78rem; padding: .4rem .75rem; }
}

/* ============================================================
   DETAIL ROWS (para show views)
   ============================================================ */
.ui-detail-row {
    display: flex;
    padding: .85rem 0;
    border-bottom: 1px solid #f1f5f9;
    align-items: baseline;
}
.ui-detail-row:last-child { border-bottom: none; }
.ui-detail-label {
    width: 180px;
    flex-shrink: 0;
    font-weight: 600;
    font-size: .85rem;
    color: #64748b;
}
.ui-detail-value {
    flex: 1;
    font-size: .95rem;
    color: #1e293b;
}
@media (max-width: 767.98px) {
    .ui-detail-row { flex-direction: column; gap: .25rem; }
    .ui-detail-label { width: auto; }
}

/* ============================================================
   USER AVATARS (show/profile)
   ============================================================ */
.ui-user-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid;
    flex-shrink: 0;
}
.ui-user-avatar-sm  { width: 48px; height: 48px; font-size: 1rem; }
.ui-user-avatar-lg  { width: 96px; height: 96px; font-size: 2rem; }
.ui-user-avatar-amber { background: rgba(245,158,11,.1); border-color: rgba(245,158,11,.25); }
.ui-user-avatar-green { background: rgba(var(--accent-rgb, 16,185,129), .1); border-color: rgba(var(--accent-rgb, 16,185,129), .25); }
.ui-user-avatar-blue  { background: rgba(59,130,246,.1); border-color: rgba(59,130,246,.25); }

/* ============================================================
   EMPTY STATE
   ============================================================ */
.ui-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
}
.ui-empty-state i {
    font-size: 2.5rem;
    color: #cbd5e1;
    display: block;
    margin-bottom: .75rem;
}
.ui-empty-state p {
    font-weight: 600;
    color: #64748b;
    margin-bottom: .25rem;
}

/* ============================================================
   DARK MODE
   ============================================================ */
body.dark-mode .ui-card,
body.dark-mode .ui-stat {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .ui-card-title { color: #f1f5f9; }
body.dark-mode .ui-card-subtitle { color: #94a3b8; }
body.dark-mode .ui-stat-value { color: #f8fafc; }
body.dark-mode .ui-stat-label { color: #94a3b8; }
body.dark-mode .ui-stat-sub { color: #64748b; }

body.dark-mode .ui-input,
body.dark-mode .ui-select,
body.dark-mode .ui-textarea {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
body.dark-mode .ui-input:focus,
body.dark-mode .ui-select:focus,
body.dark-mode .ui-textarea:focus {
    background: rgba(15,23,42,.8);
}
body.dark-mode .ui-input-group .ui-input-group-text {
    background: rgba(30,41,59,.8);
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode .ui-label { color: #cbd5e1; }

body.dark-mode .ui-btn-ghost {
    background: rgba(255,255,255,.05);
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode .ui-btn-ghost:hover {
    background: rgba(255,255,255,.1);
    border-color: #475569;
    color: #f1f5f9;
}

body.dark-mode .ui-table {
    --bs-table-color: #f1f5f9;
    --bs-table-border-color: #1e293b;
}
body.dark-mode .ui-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-bottom-color: #1e293b;
}
body.dark-mode .ui-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}

body.dark-mode .ui-detail-label { color: #94a3b8; }
body.dark-mode .ui-detail-value { color: #cbd5e1; }
body.dark-mode .ui-detail-row { border-bottom-color: #1e293b; }

body.dark-mode .ui-badge-neutral {
    background: rgba(30,41,59,.8);
    border-color: #334155;
    color: #94a3b8;
}

body.dark-mode .ui-action-view {
    background: rgba(59,130,246,.15);
    border-color: rgba(59,130,246,.3);
    color: #60a5fa;
}
body.dark-mode .ui-action-edit {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.3);
    color: #fbbf24;
}
body.dark-mode .ui-action-delete {
    background: rgba(239,68,68,.15);
    border-color: rgba(239,68,68,.3);
    color: #f87171;
}
body.dark-mode .ui-action-print {
    background: rgba(100,116,139,.15);
    border-color: rgba(100,116,139,.3);
    color: #94a3b8;
}

body.dark-mode .ui-user-avatar-amber {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.3);
}
body.dark-mode .ui-user-avatar-green {
    background: rgba(var(--accent-rgb, 16,185,129), .15);
    border-color: rgba(var(--accent-rgb, 16,185,129), .3);
}
body.dark-mode .ui-user-avatar-blue {
    background: rgba(59,130,246,.15);
    border-color: rgba(59,130,246,.3);
}

body.dark-mode .ui-sticky-bar {
    background: rgba(15,23,42,.9);
}
body.dark-mode .ui-empty-state i { color: #334155; }
body.dark-mode .ui-empty-state p { color: #64748b; }

body.dark-mode .ui-badge-success {
    background: rgba(34,197,94,.15);
    border-color: rgba(34,197,94,.3);
    color: #4ade80;
}
body.dark-mode .ui-badge-warning {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.3);
    color: #fbbf24;
}
body.dark-mode .ui-badge-danger {
    background: rgba(239,68,68,.15);
    border-color: rgba(239,68,68,.3);
    color: #f87171;
}
body.dark-mode .ui-badge-info {
    background: rgba(59,130,246,.15);
    border-color: rgba(59,130,246,.3);
    color: #60a5fa;
}
body.dark-mode .ui-badge-primary {
    background: rgba(var(--accent-rgb, 16,185,129), .15);
    border-color: rgba(var(--accent-rgb, 16,185,129), .3);
    color: #34d399;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 767.98px) {
    .ui-header { padding: 1.25rem 1.25rem; }
    .ui-header-body { flex-direction: column; align-items: flex-start; }
    .ui-header-actions { width: 100%; }
    .ui-header-actions .ui-btn { flex: 1; }
    .ui-card-body { padding: 1.1rem; }
    .ui-table { min-width: 600px; }
    .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
@media (max-width: 575.98px) {
    .ui-header-title { font-size: 1.1rem; }
    .ui-avatar-circle { width: 44px; height: 44px; font-size: 1.25rem; }
    .ui-stat-value { font-size: 1.25rem; }
}
</style>
