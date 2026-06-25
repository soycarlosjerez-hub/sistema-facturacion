{{-- Premium UI - Partial compartido para todas las vistas premium --}}
{{-- Incluir con: @include('partials.premium-ui') --}}
{{-- Las clases usan prefijo genérico 'premium-' para reutilización en cualquier módulo --}}

<style>
/* ============================================================
   PREMIUM UI - Keyframes
   ============================================================ */
@keyframes premiumGradientShift {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes premiumFloat {
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-12px)}
}
@keyframes premiumSlideUp {
    from{opacity:0;transform:translateY(24px)}
    to{opacity:1;transform:translateY(0)}
}

/* ============================================================
   PREMIUM UI - Page wrapper
   ============================================================ */
.premium-page {
    animation: premiumSlideUp .5s ease;
}

/* ============================================================
   PREMIUM UI - Header (animated gradient + bubbles)
   ============================================================ */
.premium-header {
    background: linear-gradient(135deg, #059669, #10b981, #06b6d4, #059669);
    background-size: 300% 300%;
    animation: premiumGradientShift 6s ease infinite;
    border-radius: 1.2rem;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 8px 32px rgba(5,150,105,.25);
}
.premium-header::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
    pointer-events: none;
}
.premium-header .bubble {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    pointer-events: none;
}
.premium-header .bubble:nth-child(1) {
    width: 80px; height: 80px; top: -20px; right: 10%;
    animation: premiumFloat 4s ease-in-out infinite;
}
.premium-header .bubble:nth-child(2) {
    width: 50px; height: 50px; bottom: 10px; right: 28%;
    animation: premiumFloat 5s ease-in-out infinite 1s;
}
.premium-header .bubble:nth-child(3) {
    width: 100px; height: 100px; bottom: -30px; right: 5%;
    animation: premiumFloat 6s ease-in-out infinite .5s;
}

/* ============================================================
   PREMIUM UI - Avatar circle (inside header)
   ============================================================ */
.premium-avatar-circle {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    border: 2px solid rgba(255,255,255,.35);
    flex-shrink: 0;
}

/* ============================================================
   PREMIUM UI - Cards (glassmorphism)
   ============================================================ */
.premium-card {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: 1.2rem;
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: 0 8px 32px rgba(0,0,0,.06);
    overflow: hidden;
    transition: all .3s ease;
    animation: premiumSlideUp .5s ease both;
}
.premium-card:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

/* ============================================================
   PREMIUM UI - Card accent strips
   ============================================================ */
.card-accent {
    height: 4px;
}
.card-accent.green { background: linear-gradient(90deg, #10b981, #06b6d4); }
.card-accent.amber { background: linear-gradient(90deg, #f59e0b, #f97316); }
.card-accent.red   { background: linear-gradient(90deg, #ef4444, #f97316); }
.card-accent.blue  { background: linear-gradient(90deg, #3b82f6, #6366f1); }
.card-accent.purple { background: linear-gradient(90deg, #8b5cf6, #a855f7); }

/* ============================================================
   PREMIUM UI - Card body padding
   ============================================================ */
.premium-card .card-body {
    padding: 1.5rem 1.75rem 1.75rem;
}

/* ============================================================
   PREMIUM UI - Card title & subtitle
   ============================================================ */
.premium-card-title {
    font-weight: 700; font-size: 1.05rem;
    display: flex; align-items: center; gap: .75rem;
    padding: 1.25rem 1.75rem .25rem;
    margin: 0; color: #1e293b;
}
.premium-card-title i { font-size: 1.25rem; }
.premium-card-title i.icon-green  { color: #10b981; }
.premium-card-title i.icon-amber  { color: #f59e0b; }
.premium-card-title i.icon-red    { color: #ef4444; }
.premium-card-title i.icon-blue   { color: #3b82f6; }
.premium-card-title i.icon-purple { color: #8b5cf6; }

.premium-card-subtitle {
    color: #64748b; font-size: .85rem;
    padding: 0 1.75rem; margin-bottom: .6rem;
}

/* ============================================================
   PREMIUM UI - Form elements
   ============================================================ */
.premium-card .form-label {
    font-weight: 600; font-size: .85rem;
    color: #334155; margin-bottom: .35rem;
}
.premium-card .form-control,
.premium-card .form-select {
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem;
    padding: .6rem 1rem;
    font-size: .95rem;
    transition: all .2s ease;
    background: #fff;
}
.premium-card .form-control:focus,
.premium-card .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,.15);
    background: #fff;
}
.premium-card .input-group-text {
    border: 1.5px solid #e2e8f0;
    border-right: 0;
    border-radius: .65rem 0 0 .65rem;
    background: #f8fafc;
    color: #64748b;
    font-size: .9rem;
}
.premium-card .input-group .form-control {
    border-left: 0;
    border-radius: 0 .65rem .65rem 0;
}

/* ============================================================
   PREMIUM UI - Buttons
   ============================================================ */
.premium-card .btn-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none; border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600; text-transform: none;
    box-shadow: 0 4px 14px rgba(16,185,129,.3);
    transition: all .25s ease;
    color: #fff;
}
.premium-card .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16,185,129,.45);
    color: #fff;
}
.premium-card .btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none; border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(239,68,68,.3);
    transition: all .25s ease;
    color: #fff;
}
.premium-card .btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(239,68,68,.45);
    color: #fff;
}
.premium-card .btn-outline-secondary {
    background: rgba(255,255,255,.8);
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    color: #475569;
    transition: all .2s ease;
}
.premium-card .btn-outline-secondary:hover {
    background: #fff;
    border-color: #cbd5e1;
    color: #1e293b;
}

/* ============================================================
   PREMIUM UI - Sticky save bar (optional, for forms)
   ============================================================ */
.premium-sticky-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(20px);
    border-top: 2px solid #10b981;
    padding: .75rem 1.5rem;
    z-index: 1050;
    box-shadow: 0 -4px 20px rgba(0,0,0,.08);
    animation: premiumSlideUp .3s ease;
}
.premium-sticky-bar .btn-save {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none; border-radius: .65rem !important;
    padding: .6rem 2rem;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(16,185,129,.3);
    transition: all .25s ease;
    color: #fff;
}
.premium-sticky-bar .btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16,185,129,.45);
    color: #fff;
}
.premium-sticky-bar .btn-cancel {
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem !important;
    padding: .6rem 1.5rem;
    font-weight: 600;
    color: #475569;
    transition: all .2s ease;
}
.premium-sticky-bar .btn-cancel:hover {
    background: #e2e8f0;
    color: #1e293b;
}
@media (max-width: 991.98px) {
    .premium-sticky-bar { left: 0; }
}

/* ============================================================
   PREMIUM UI - Detail rows (for show views)
   ============================================================ */
.premium-detail-row {
    display: flex;
    padding: .85rem 0;
    border-bottom: 1px solid #f1f5f9;
    align-items: baseline;
}
.premium-detail-row:last-child { border-bottom: none; }
.premium-detail-label {
    width: 180px;
    flex-shrink: 0;
    font-weight: 600;
    font-size: .85rem;
    color: #64748b;
}
.premium-detail-value {
    flex: 1;
    font-size: .95rem;
    color: #1e293b;
}
@media (max-width: 767.98px) {
    .premium-detail-row { flex-direction: column; gap: .25rem; }
    .premium-detail-label { width: auto; }
}

/* ============================================================
   PREMIUM UI - Badges
   ============================================================ */
.premium-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .4rem .75rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s ease;
    border: 1.5px solid transparent;
}
.premium-badge:hover { transform: translateY(-1px); }
.premium-badge.active {
    background: rgba(16,185,129,.1);
    border-color: rgba(16,185,129,.3);
    color: #059669;
}
.premium-badge:not(.active) {
    background: #f1f5f9;
    color: #475569;
    border-color: #e2e8f0;
}

/* ============================================================
   PREMIUM UI - Action buttons (edit, delete, etc.)
   ============================================================ */
.premium-btn-edit {
    background: rgba(245,158,11,.1);
    color: #d97706;
    border: 1.5px solid rgba(245,158,11,.2);
    border-radius: .5rem;
    padding: .35rem .65rem;
    font-size: .8rem;
    transition: all .2s ease;
}
.premium-btn-edit:hover {
    background: #f59e0b;
    color: #fff;
    border-color: #f59e0b;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245,158,11,.3);
}
.premium-btn-delete {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border: 1.5px solid rgba(239,68,68,.2);
    border-radius: .5rem;
    padding: .35rem .65rem;
    font-size: .8rem;
    transition: all .2s ease;
}
.premium-btn-delete:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,.3);
}

/* ============================================================
   PREMIUM UI - Stat cards
   ============================================================ */
.premium-stat-card {
    background: rgba(255,255,255,.7);
    backdrop-filter: blur(20px);
    border-radius: 1.2rem;
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: 0 8px 32px rgba(0,0,0,.06);
    overflow: hidden;
    transition: all .3s ease;
    animation: premiumSlideUp .5s ease both;
}
.premium-stat-card:hover {
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.premium-stat-card .stat-value {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1.2;
}
.premium-stat-card .stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
    font-weight: 600;
}

/* ============================================================
   PREMIUM UI - User avatar (for show/profile views)
   ============================================================ */
.premium-user-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid;
    flex-shrink: 0;
}
.premium-user-avatar.avatar-amber {
    background: rgba(245,158,11,.1);
    border-color: rgba(245,158,11,.25);
}
.premium-user-avatar.avatar-green {
    background: rgba(16,185,129,.1);
    border-color: rgba(16,185,129,.25);
}
.premium-user-avatar.avatar-blue {
    background: rgba(59,130,246,.1);
    border-color: rgba(59,130,246,.25);
}

/* ============================================================
   PREMIUM UI - Dark Mode
   ============================================================ */
body.dark-mode .premium-card,
body.dark-mode .premium-stat-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color: #f8fafc; }
body.dark-mode .premium-stat-card .stat-label { color: #94a3b8; }

body.dark-mode .premium-card .form-control,
body.dark-mode .premium-card .form-select {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus {
    background: rgba(15,23,42,.8);
    border-color: #10b981;
}
body.dark-mode .premium-card .input-group-text {
    background: rgba(30,41,59,.8);
    border-color: #334155;
    color: #94a3b8;
}

body.dark-mode .premium-card .btn-outline-secondary {
    background: rgba(255,255,255,.05);
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode .premium-card .btn-outline-secondary:hover {
    background: rgba(255,255,255,.1);
    border-color: #475569;
    color: #f1f5f9;
}

body.dark-mode .premium-detail-label { color: #94a3b8; }
body.dark-mode .premium-detail-value { color: #cbd5e1; }
body.dark-mode .premium-detail-row { border-bottom-color: #1e293b; }

body.dark-mode .premium-badge:not(.active) {
    background: rgba(30,41,59,.8);
    border-color: #334155;
    color: #94a3b8;
}

body.dark-mode .premium-btn-edit {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.3);
    color: #fbbf24;
}
body.dark-mode .premium-btn-delete {
    background: rgba(239,68,68,.15);
    border-color: rgba(239,68,68,.3);
    color: #f87171;
}

body.dark-mode .premium-sticky-bar {
    background: rgba(15,23,42,.9);
    border-top-color: #34d399;
}
body.dark-mode .premium-sticky-bar .btn-cancel {
    background: rgba(30,41,59,.8);
    border-color: #334155;
    color: #94a3b8;
}

body.dark-mode .premium-user-avatar.avatar-amber {
    background: rgba(245,158,11,.15);
    border-color: rgba(245,158,11,.3);
}
body.dark-mode .premium-user-avatar.avatar-green {
    background: rgba(16,185,129,.15);
    border-color: rgba(16,185,129,.3);
}
body.dark-mode .premium-user-avatar.avatar-blue {
    background: rgba(59,130,246,.15);
    border-color: rgba(59,130,246,.3);
}
</style>
