<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($systemName ?? 'Sistema'); ?></title>

     <!-- Fonts -->
     <link rel="preconnect" href="https://fonts.bunny.net">
     <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

     <!-- SweetAlert2 -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <!-- Icons -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
     <link href="<?php echo e(asset('css/a11y.css')); ?>" rel="stylesheet">

     <!-- DataTables -->
     <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
     <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>

    <?php
    $darkMode = session('dark_mode', false);
    ?>

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-width-mobile: 260px;
            --primary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --accent-color: #38bdf8;
            --glass-bg: rgba(255, 255, 255, 0.05);
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        body.dark-mode { background-color: #020617; color: #f1f5f9; }

/* Sidebar */
.sidebar {
    width: var(--sidebar-width);
    min-width: var(--sidebar-width);
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    height: 100vh;
    position: sticky;
    top: 0;
    box-shadow: 4px 0 24px rgba(0,0,0,0.15);
    z-index: 1000;
    border-right: 1px solid rgba(255,255,255,0.06);
}
body.dark-mode .sidebar { 
    background: linear-gradient(180deg, #0f172a 0%, #020617 100%); 
    border-right: 1px solid rgba(255,255,255,0.08);
}

/* Sidebar responsive: slide-in on mobile */
.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 998;
}

@media (max-width: 1199.98px) {
    .sidebar {
        position: fixed !important;
        left: calc(-1 * var(--sidebar-width-mobile));
        width: var(--sidebar-width-mobile);
        min-width: unset;
        transition: left 0.3s ease;
        z-index: 999;
        height: 100vh;
    }
    .sidebar-open .sidebar { left: 0; }
    .sidebar-open .sidebar-overlay { display: block; }
    body.sidebar-open { overflow: hidden; }
}

.sidebar-header {
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: rgba(0,0,0,0.1);
}

.brand-logo {
    width: 48px;
    height: 48px;
    background: var(--accent-color);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(56, 189, 248, 0.35);
    flex-shrink: 0;
}

/* Professional Accordion Styles */
.accordion-button {
    background: transparent !important;
    color: rgba(255,255,255,0.7) !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    padding: 0.875rem 1rem !important;
    margin: 0.25rem 0.75rem !important;
    border-radius: 10px !important;
    transition: all 0.2s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border: none !important;
    box-shadow: none !important;
    font-family: 'Figtree', sans-serif !important;
}

.accordion-button:not(.collapsed) {
    color: #fff !important;
    background: rgba(255,255,255,0.08) !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12) !important;
}

.accordion-button:not(.collapsed) .accordion-text {
    color: #fff !important;
}

.accordion-button:not(.collapsed) .accordion-icon {
    color: #fff !important;
    transform: rotate(180deg);
    transition: transform 0.2s ease;
}

.accordion-button:hover:not(.collapsed) {
    background: rgba(255,255,255,0.12) !important;
}

.accordion-button:focus {
    border-color: none !important;
    box-shadow: none !important;
}

.accordion-body {
    padding: 0 !important;
    background-color: transparent !important;
}

/* Fix accordion item borders for flush look */
.accordion-item {
    background-color: transparent !important;
    border: none !important;
}

.accordion-item:first-of-type .accordion-button {
    border-top-left-radius: 10px !important;
    border-top-right-radius: 10px !important;
}

.accordion-item:last-of-type .accordion-button.collapsed {
    border-bottom-left-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
}

.nav-section-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(255,255,255,0.5);
    margin: 1.5rem 1rem 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.6);
    border-radius: 8px;
    margin: 0.25rem 0.75rem;
    padding: 0.625rem 0.875rem;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    font-weight: 500;
    font-size: 0.875rem;
}

.sidebar .nav-link i {
    font-size: 1.1rem;
    margin-right: 0.75rem;
    width: 22px;
    text-align: center;
    flex-shrink: 0;
}

.sidebar .nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,0.08);
    transform: translateX(2px);
}

.sidebar .nav-link.active {
    background: rgba(255,255,255,0.12);
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2);
    font-weight: 600;
}

.sidebar .nav-link.active i { 
    color: #fff; 
}

.sidebar .nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 24px;
    background: var(--accent-color);
    border-radius: 0 2px 2px 0;
}

.nav-badge {
    margin-left: auto;
    background: rgba(255,255,255,0.15);
    color: #e2e8f0;
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    border-radius: 999px;
    font-weight: 600;
}

/* Dark mode specific adjustments */
body.dark-mode .sidebar .nav-link {
    color: rgba(255,255,255,0.5);
}

body.dark-mode .sidebar .nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,0.08);
}

body.dark-mode .sidebar .nav-link.active {
    background: rgba(255,255,255,0.12);
    color: #fff;
}

body.dark-mode .accordion-button {
    color: rgba(255,255,255,0.6) !important;
}

body.dark-mode .accordion-button:not(.collapsed) {
    color: #fff !important;
    background: rgba(255,255,255,0.08) !important;
}

body.dark-mode .accordion-button:hover:not(.collapsed) {
    background: rgba(255,255,255,0.12) !important;
}

        /* User Profile */
        .user-profile-card {
            background: var(--glass-bg);
            border-radius: 16px;
            margin: 1rem;
            padding: 1rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Content */
        .content-wrapper { background-color: #f1f5f9; min-height: 100vh; width: 100%; }
        body.dark-mode .content-wrapper { background: #020617; }

        /* Topbar */
        .topbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        body.dark-mode .topbar { background: rgba(2, 6, 23, 0.8); border-bottom: 1px solid #1e293b; }
        @media (min-width: 992px) {
            .topbar { padding: 0.75rem 1.5rem; }
        }
        @media (min-width: 1200px) {
            .topbar { padding: 1rem 2rem; }
        }

        /* Content responsive */
        @media (max-width: 1199.98px) {
            main#main-content { width: 100%; overflow-x: hidden; }
        }
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .p-4.p-lg-5 { padding: 1.5rem !important; }
            .container-fluid.px-4 { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
        }
        @media (max-width: 575.98px) {
            .p-4.p-lg-5 { padding: 1rem !important; }
            .container-fluid.px-4 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        }

        /* Dark mode overrides */
        body.dark-mode .card { background: #0f172a; border-color: #1e293b; color: #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
        body.dark-mode .card-header { border-color: #1e293b; color: #f1f5f9; background: #0f172a; }
        body.dark-mode .card-footer { border-color: #1e293b; background: #0f172a; color: #cbd5e1; }
        body.dark-mode .card.bg-white, body.dark-mode .card.bg-light,
        body.dark-mode .card-header.bg-white, body.dark-mode .card-header.bg-light,
        body.dark-mode .card-footer.bg-white, body.dark-mode .card-footer.bg-light { background: #0f172a !important; }

        body.dark-mode .form-control { background: #0f172a; border-color: #334155; color: #f1f5f9; }
        body.dark-mode .form-control:focus { background: #0f172a; border-color: #38bdf8; color: #f1f5f9; box-shadow: 0 0 0 0.2rem rgba(56,189,248,0.15); }
        body.dark-mode .form-select { background-color: #0f172a !important; border-color: #334155; color: #f1f5f9; }
        body.dark-mode .form-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 0.2rem rgba(56,189,248,0.15); }
        body.dark-mode .form-label { color: #cbd5e1; }
        body.dark-mode .input-group-text { background: #1e293b; border-color: #334155; color: #cbd5e1; }
        body.dark-mode .input-group-text.bg-white,
        body.dark-mode .input-group-text.bg-light { background: #1e293b !important; }

        body.dark-mode .btn-light { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
        body.dark-mode .btn-light:hover { background-color: #334155; color: #fff; }
        body.dark-mode .btn-outline-light { border-color: #334155; color: #cbd5e1; }
        body.dark-mode .btn-outline-light:hover { background: #1e293b; color: #fff; }

        body.dark-mode .table {
            --bs-table-bg: transparent;
            --bs-table-color: #f1f5f9;
            --bs-table-border-color: #1e293b;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
            --bs-table-hover-color: #fff;
            color: #f1f5f9;
            border-color: #1e293b;
        }
        body.dark-mode .table thead th {
            background-color: rgba(15, 23, 42, 0.5);
            color: #94a3b8;
            border-color: #1e293b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        body.dark-mode .table-light,
        body.dark-mode .table > thead > tr > .table-light,
        body.dark-mode .table > tbody > tr > .table-light,
        body.dark-mode .table > tfoot > tr > .table-light { background-color: #1e293b !important; color: #f1f5f9 !important; }
        body.dark-mode .table td { border-color: #1e293b; }

        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 { color: #f8fafc; }
        body.dark-mode p, body.dark-mode span, body.dark-mode small { color: #cbd5e1; }
        body.dark-mode .text-muted { color: #94a3b8 !important; }
        body.dark-mode .text-dark { color: #e2e8f0 !important; }
        body.dark-mode .text-body { color: #cbd5e1 !important; }

        body.dark-mode .topbar h5 { color: #f8fafc; }
        body.dark-mode .breadcrumb-item.active { color: #94a3b8; }

        body.dark-mode .pagination .page-link { background-color: #0f172a; border-color: #1e293b; color: #94a3b8; }
        body.dark-mode .pagination .page-item.active .page-link { background-color: var(--accent-color); border-color: var(--accent-color); color: #0f172a; }
        body.dark-mode .pagination .page-item.disabled .page-link { background-color: #020617; border-color: #1e293b; color: #334155; }

        body.dark-mode .alert { background-color: #0f172a; border-color: #1e293b; color: #f1f5f9; }
        body.dark-mode .alert-success { background-color: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.2); color: #4ade80; }
        body.dark-mode .alert-danger { background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #f87171; }
        body.dark-mode .alert-warning { background-color: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.2); color: #fbbf24; }
        body.dark-mode .alert-info { background-color: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.2); color: #38bdf8; }

        body.dark-mode .badge.bg-light { background: #1e293b !important; color: #e2e8f0 !important; }
        body.dark-mode .badge.bg-light.text-dark { color: #e2e8f0 !important; }
        body.dark-mode .badge.bg-white { background: #1e293b !important; color: #e2e8f0 !important; }
        body.dark-mode .bg-light { background-color: #1e293b !important; }
        body.dark-mode .bg-white { background-color: #0f172a !important; }
        body.dark-mode .bg-dark { background-color: #020617 !important; }

        body.dark-mode .modal-content { background: #0f172a; border-color: #1e293b; }
        body.dark-mode .modal-header { border-color: #1e293b; color: #f1f5f9; }
        body.dark-mode .modal-header.bg-white,
        body.dark-mode .modal-header.bg-light { background: #0f172a !important; }
        body.dark-mode .modal-body { color: #cbd5e1; }
        body.dark-mode .modal-footer { border-color: #1e293b; }
        body.dark-mode .btn-close { filter: invert(0.8) brightness(1.5); }
        body.dark-mode .btn-close-white { filter: none; }

        body.dark-mode .dropdown-menu { background: #0f172a; border-color: #1e293b; }
        body.dark-mode .dropdown-item { color: #cbd5e1; }
        body.dark-mode .dropdown-item:hover { background: #1e293b; color: #fff; }
        body.dark-mode .dropdown-item.active { background: #38bdf8; color: #0f172a; }
        body.dark-mode .dropdown-divider { border-color: #1e293b; }

        /* Notification Dropdown */
        .notif-dropdown .notif-item {
            display: flex; align-items: flex-start; gap: 10px; padding: 10px 16px;
            cursor: pointer; transition: background 0.15s; text-decoration: none; color: inherit;
            border-bottom: 1px solid #f1f5f9;
        }
        .notif-dropdown .notif-item:hover { background: #f8fafc; }
        .notif-dropdown .notif-item.unread { background: #eff6ff; }
        .notif-dropdown .notif-item.unread:hover { background: #dbeafe; }
        .notif-dropdown .notif-icon, .notif-dropdown .notif-avatar {
            width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0; font-size: 0.85rem;
        }
        .notif-dropdown .notif-body { flex: 1; min-width: 0; }
        .notif-dropdown .notif-title { font-size: 0.8rem; font-weight: 600; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-dropdown .notif-title strong { font-weight: 700; }
        .notif-dropdown .notif-message { font-size: 0.75rem; color: #64748b; margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-dropdown .notif-time { font-size: 0.65rem; color: #94a3b8; margin-top: 2px; }
        .spin-loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        body.dark-mode .notif-dropdown .notif-item { border-bottom-color: #1e293b; }
        body.dark-mode .notif-dropdown .notif-item:hover { background: #1e293b; }
        body.dark-mode .notif-dropdown .notif-item.unread { background: #0c1529; }
        body.dark-mode .notif-dropdown .notif-item.unread:hover { background: #0f1d32; }
        body.dark-mode .notif-dropdown .notif-message { color: #94a3b8; }

        /* Activity Feed */
        .feed-day-divider small { font-size: 0.7rem; }
        .feed-avatar {
            width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0; font-weight: 600;
        }
        .feed-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .notification-item.unread { background: #eff6ff !important; }

        body.dark-mode .list-group-item { background: #0f172a; border-color: #1e293b; color: #cbd5e1; }
        body.dark-mode .list-group-item.active { background: #38bdf8; border-color: #38bdf8; color: #0f172a; }

        body.dark-mode .progress { background: #1e293b; }
        body.dark-mode .progress-bar { background: #38bdf8; }

        body.dark-mode .nav-link { color: #94a3b8; }
        body.dark-mode .nav-link:hover { color: #f1f5f9; }
        body.dark-mode .nav-link.active { color: #38bdf8; }

        body.dark-mode .border-light { border-color: #1e293b !important; }
        body.dark-mode .border { border-color: #1e293b !important; }

        body.dark-mode section.text-muted,
        body.dark-mode .text-muted small,
        body.dark-mode .text-muted span { color: #94a3b8 !important; }

        /* Force horizontal scroll on tables in small screens */
        @media (max-width: 1199.98px) {
            .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }
        @media (max-width: 991.98px) {
            .card .table { min-width: 600px; }
        }

        /* Scrollbar */
        .sidebar .overflow-y-auto::-webkit-scrollbar { width: 6px; }
        .sidebar .overflow-y-auto::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        .sidebar .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }

        /* Global Search */
        .global-search-results .gs-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px;
            text-decoration: none; color: inherit; transition: background 0.15s;
            border-bottom: 1px solid #f1f5f9;
        }
        .global-search-results .gs-item:last-child { border-bottom: none; }
        .global-search-results .gs-item:hover { background: #f8fafc; }
        .global-search-results .gs-item .gs-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1rem;
        }
        .global-search-results .gs-item .gs-label { font-weight: 600; font-size: 0.85rem; }
        .global-search-results .gs-item .gs-sub { font-size: 0.75rem; color: #64748b; }
        .global-search-results .gs-item .gs-badge { font-size: 0.7rem; font-weight: 700; margin-left: auto; white-space: nowrap; }
        body.dark-mode .global-search-results { background: #0f172a; border-color: #1e293b; color: #f1f5f9; }
        body.dark-mode .global-search-results .gs-item { border-bottom-color: #1e293b; }
        body.dark-mode .global-search-results .gs-item:hover { background: #1e293b; }
        body.dark-mode .global-search-results .gs-item .gs-sub { color: #94a3b8; }
        body.dark-mode #globalSearchInput { background: rgba(255,255,255,.06) !important; border-color: rgba(255,255,255,.1) !important; color: #f1f5f9 !important; }
        body.dark-mode #globalSearchInput::placeholder { color: #64748b; }
        .global-search-nores { padding: 24px; text-align: center; color: #94a3b8; font-size: 0.85rem; }
        .global-search-loading { padding: 20px; text-align: center; color: #94a3b8; }

        /* Action Icon Buttons — override Bootstrap */
        a.ui-action, button.ui-action, input.ui-action { display:inline-flex!important;align-items:center;justify-content:center;width:34px!important;height:34px!important;border-radius:.5rem!important;border:1.5px solid transparent!important;text-decoration:none!important;font-size:.85rem;transition:all .2s ease;cursor:pointer;padding:0;line-height:1; }
        a.ui-action:focus-visible, button.ui-action:focus-visible, input.ui-action:focus-visible { outline:2px solid var(--accent,#3b82f6);outline-offset:2px; }
        a.ui-action-view, button.ui-action-view, input.ui-action-view { background-color:rgba(59,130,246,.1)!important;color:#3b82f6!important;border-color:rgba(59,130,246,.2)!important; }
        a.ui-action-view:hover, button.ui-action-view:hover, input.ui-action-view:hover { background-color:#3b82f6!important;color:#fff!important;border-color:#3b82f6!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(59,130,246,.3)!important; }
        a.ui-action-edit, button.ui-action-edit, input.ui-action-edit { background-color:rgba(245,158,11,.1)!important;color:#d97706!important;border-color:rgba(245,158,11,.2)!important; }
        a.ui-action-edit:hover, button.ui-action-edit:hover, input.ui-action-edit:hover { background-color:#f59e0b!important;color:#fff!important;border-color:#f59e0b!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(245,158,11,.3)!important; }
        a.ui-action-delete, button.ui-action-delete, input.ui-action-delete { background-color:rgba(239,68,68,.1)!important;color:#dc2626!important;border-color:rgba(239,68,68,.2)!important; }
        a.ui-action-delete:hover, button.ui-action-delete:hover, input.ui-action-delete:hover { background-color:#ef4444!important;color:#fff!important;border-color:#ef4444!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(239,68,68,.3)!important; }
        a.ui-action-print, button.ui-action-print, input.ui-action-print { background-color:rgba(100,116,139,.1)!important;color:#64748b!important;border-color:rgba(100,116,139,.2)!important; }
        a.ui-action-print:hover, button.ui-action-print:hover, input.ui-action-print:hover { background-color:#64748b!important;color:#fff!important;border-color:#64748b!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(100,116,139,.3)!important; }
        body.dark-mode a.ui-action-view, body.dark-mode button.ui-action-view, body.dark-mode input.ui-action-view { background-color:rgba(59,130,246,.15)!important;border-color:rgba(59,130,246,.3)!important;color:#60a5fa!important; }
        body.dark-mode a.ui-action-edit, body.dark-mode button.ui-action-edit, body.dark-mode input.ui-action-edit { background-color:rgba(245,158,11,.15)!important;border-color:rgba(245,158,11,.3)!important;color:#fbbf24!important; }
        body.dark-mode a.ui-action-delete, body.dark-mode button.ui-action-delete, body.dark-mode input.ui-action-delete { background-color:rgba(239,68,68,.15)!important;border-color:rgba(239,68,68,.3)!important;color:#f87171!important; }
        body.dark-mode a.ui-action-print, body.dark-mode button.ui-action-print, body.dark-mode input.ui-action-print { background-color:rgba(100,116,139,.15)!important;border-color:rgba(100,116,139,.3)!important;color:#94a3b8!important; }

    </style>
</head>

<body class="<?php echo e($darkMode ? 'dark-mode' : ''); ?>">
    <a href="#main-content" class="skip-to-main" role="link" aria-label="Saltar al contenido principal">
        <i class="bi bi-skip-forward-fill me-1" aria-hidden="true"></i>Saltar al contenido principal
    </a>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="d-flex">

        <!-- Sidebar -->
        <aside class="sidebar d-flex flex-column" id="mainSidebar">
            <div class="sidebar-header d-flex align-items-center">
                <div class="brand-logo me-3" style="overflow: hidden;">
                    <?php if($systemLogo): ?>
                        <img src="<?php echo e($systemLogo); ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
                    <?php else: ?>
                        <i class="bi bi-shop fs-4"></i>
                    <?php endif; ?>
                </div>
                <div class="overflow-hidden">
                    <h5 class="fw-bold mb-0 text-truncate" title="<?php echo e($systemName); ?>"><?php echo e($systemName); ?></h5>
                    <small class="text-muted text-truncate d-block" style="font-size: 0.7rem;"><?php echo e($systemSlogan); ?></small>
                </div>
            </div>

            <div class="flex-grow-1 overflow-y-auto">
                <div class="accordion accordion-flush" id="sidebarAccordion">
                    <?php
                        $sidebarItems = \App\Support\Sidebar::menu();
                        $currentSection = null;
                        $currentItemRoute = null;
                        foreach ($sidebarItems as $item) {
                            if (isset($item['section'])) continue;
                            $rname = $item['route'] ?? '';
                            if (! $rname) continue;
                            $rnameMatch = $item['is_route'] ?? $rname . '.*';
                            if (request()->routeIs($rnameMatch)) {
                                $currentItemRoute = $rnameMatch;
                                break;
                            }
                        }
                        
                        // Group items by section and track active section
                        $groupedItems = [];
                        $currentSection = null;
                        $activeSectionId = null;
                        foreach ($sidebarItems as $item) {
                            if (isset($item['section'])) {
                                $currentSection = $item['section'];
                                $groupedItems[$currentSection] = [];
                            } elseif ($currentSection !== null) {
                                $groupedItems[$currentSection][] = $item;
                                // Check if this item is active
                                $rname = $item['route'] ?? '';
                                if ($rname) {
                                    $rnameMatch = $item['is_route'] ?? $rname . '.*';
                                    if (request()->routeIs($rnameMatch)) {
                                        $activeSectionId = str_replace(' ', '-', strtolower($currentSection));
                                    }
                                }
                            }
                        }
                    ?>
                    
                    <?php $__currentLoopData = $groupedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionTitle => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $sectionId = str_replace(' ', '-', strtolower($sectionTitle)); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?php echo e($sectionId); ?>">
                                <button class="accordion-button <?php echo e($sectionId === $activeSectionId ? '' : 'collapsed'); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo e($sectionId); ?>" data-bs-parent="#sidebarAccordion" aria-expanded="<?php echo e($sectionId === $activeSectionId ? 'true' : 'false'); ?>" aria-controls="collapse-<?php echo e($sectionId); ?>">
                                    <span class="accordion-text"><?php echo e($sectionTitle); ?></span>
                                    <i class="bi bi-chevron-down accordion-icon"></i>
                                </button>
                            </h2>
                            <div id="collapse-<?php echo e($sectionId); ?>" class="accordion-collapse collapse <?php echo e($sectionId === $activeSectionId ? 'show' : ''); ?>" aria-labelledby="heading-<?php echo e($sectionId); ?>">
                                <div class="accordion-body">
                                    <nav class="nav flex-column">
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e($item['url'] ?? route($item['route'])); ?>" class="nav-link <?php echo e(request()->routeIs($item['exact_route'] ?? $item['route']) ? 'active' : ''); ?> py-2">
                                                <i class="bi <?php echo e($item['icon']); ?> me-2"></i> <?php echo e($item['label']); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Perfil / Acciones -->
            <div class="user-profile-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-person-fill text-white"></i>
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="fw-bold text-truncate small text-white"><?php echo e(Auth::user()?->name ?? 'Invitado'); ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.65rem;"><?php echo e(ucfirst(Auth::user()?->roles?->first()?->name ?? Auth::user()?->role ?? 'Sin rol')); ?></div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-sm btn-outline-light flex-grow-1 rounded-3" title="Mi Perfil">
                        <i class="bi bi-person-gear"></i>
                    </a>
                    <form method="POST" action="<?php echo e(route('toggleDarkMode')); ?>" class="flex-grow-1">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-outline-light w-100 rounded-3" title="<?php echo e($darkMode ? 'Modo claro' : 'Modo oscuro'); ?>">
                            <i class="bi <?php echo e($darkMode ? 'bi-sun-fill' : 'bi-moon-fill'); ?>"></i>
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-grow-1">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline-danger w-100 rounded-3" title="Cerrar sesión">
                            <i class="bi bi-power"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Content -->
        <main id="main-content" class="flex-grow-1 content-wrapper" role="main" aria-label="Contenido principal" tabindex="-1">
            <header class="topbar d-flex justify-content-between align-items-center <?php echo $__env->yieldContent('topbar_class'); ?>">
                <div class="d-flex align-items-center gap-2 flex-shrink-1 overflow-hidden">
                    <button class="btn btn-sm btn-light rounded-pill d-xl-none shadow-sm me-1" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-truncate"><?php echo $__env->yieldContent('title'); ?></h5>
                    <?php if (! empty(trim($__env->yieldContent('topbar_extra')))): ?>
                        <span class="text-muted d-none d-sm-inline">·</span>
                        <div class="d-none d-sm-flex align-items-center">
                            <?php echo $__env->yieldContent('topbar_extra'); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <!-- Global Search -->
                    <div class="global-search-wrap d-none d-xl-flex align-items-center" style="position:relative;">
                        <i class="bi bi-search text-muted position-absolute" style="left:12px;z-index:2;font-size:.8rem;"></i>
                        <input type="search" id="globalSearchInput" class="form-control form-control-sm rounded-pill pe-2" 
                               style="padding-left:34px;width:180px;background:rgba(255,255,255,.05);border:1px solid rgba(0,0,0,.08);"
                               placeholder="Buscar... (Ctrl+K)" autocomplete="off">
                        <div id="globalSearchResults" class="global-search-results" style="display:none;position:absolute;top:100%;right:0;width:340px;max-height:450px;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,.15);z-index:99999;margin-top:6px;"></div>
                    </div>

                    <?php if($sesionesCajaGlobales->isNotEmpty()): ?>
                        <div class="dropdown d-none d-md-inline-block">
                            <button class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 px-lg-3 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                <i class="bi bi-cash-stack"></i>
                                <span class="fw-bold d-none d-sm-inline"><?php echo e($sesionesCajaGlobales->first()?->caja?->codigo ?? $sesionesCajaGlobales->first()?->caja?->nombre); ?></span>
                                <span class="badge bg-success text-white ms-1" style="font-size:0.6rem;"><?php echo e($sesionesCajaGlobales->count()); ?></span>
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0" style="min-width: 250px;">
                                <?php $__currentLoopData = $sesionesCajaGlobales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a class="dropdown-item small" href="<?php echo e(route('ventas.create')); ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-cash-register me-1"></i>
                                                <strong><?php echo e($sesion->caja?->nombre); ?></strong>
                                                <?php if($sesion->caja?->codigo): ?>
                                                    <span class="badge bg-dark ms-1" style="font-size:0.6rem;"><?php echo e($sesion->caja->codigo); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-muted" style="font-size:0.7rem;"><?php echo e($sesion->fecha_apertura->format('h:i A')); ?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <div class="px-3 py-2" style="background:#f8fafc;border-radius:0 0 8px 8px;">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Haz clic en una caja para ir al POS
                                        </small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if($sucursales->count() > 0): ?>
                    <div class="dropdown d-none d-xl-inline-block">
                        <button class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                            <i class="bi bi-building me-1"></i>
                            <?php echo e($sucursalActiva?->nombre ?? 'Todas'); ?>

                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0" style="min-width: 200px;">
                            <li><a class="dropdown-item small <?php echo e(!session('sucursal_id') ? 'active' : ''); ?>" href="#" onclick="setSucursal(null)"><i class="bi bi-globe me-2"></i>Todas las sucursales</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a class="dropdown-item small <?php echo e(session('sucursal_id') == $s->id ? 'active' : ''); ?>" href="#" onclick="setSucursal(<?php echo e($s->id); ?>)"><i class="bi bi-building me-2"></i><?php echo e($s->nombre); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    
                    <?php
                        $tipoNegocio = null;
                        $tipoNegocioNombre = null;
                        $user = auth()->user();

                        if ($user) {
                            if ($user->businessInstance && $user->businessInstance->businessType) {
                                $tipoNegocio = $user->businessInstance->businessType->slug;
                                $tipoNegocioNombre = $user->businessInstance->businessType->nombre;
                            } elseif ($user->businessType) {
                                $tipoNegocio = $user->businessType->slug;
                                $tipoNegocioNombre = $user->businessType->nombre;
                            }
                        }

                        $colores = [
                            'restaurante' => 'info',
                            'retail' => 'success',
                            'mayorista' => 'warning',
                            'servicios' => 'primary',
                            'lavadero' => 'primary',
                            'mixto' => 'secondary',
                            'tattoo' => 'dark',
                            'climatizacion' => 'secondary',
                            'tecnologia' => 'danger',
                            'arte_escultura' => 'purple',
                        ];
                        $iconos = [
                            'restaurante' => 'cup-straw',
                            'retail' => 'cart-plus',
                            'mayorista' => 'truck',
                            'servicios' => 'briefcase',
                            'lavadero' => 'droplet',
                            'mixto' => 'grid',
                            'tattoo' => 'brush',
                            'climatizacion' => 'wind',
                            'tecnologia' => 'phone',
                            'arte_escultura' => 'palette',
                        ];
                        $color = $colores[$tipoNegocio] ?? 'secondary';
                        $icono = $iconos[$tipoNegocio] ?? 'grid';
                    ?>
                    <?php if($tipoNegocio): ?>
                    <span class="badge bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> rounded-pill px-3 py-2 d-none d-xl-inline-flex align-items-center gap-1" style="cursor:help;" title="Tipo de negocio: <?php echo e(ucfirst($tipoNegocioNombre ?? $tipoNegocio)); ?>">
                        <i class="bi bi-<?php echo e($icono); ?> me-1"></i>
                        <?php echo e(ucfirst($tipoNegocioNombre ?? $tipoNegocio)); ?>

                    </span>
                    <?php endif; ?>

                    <span class="badge bg-light text-dark border d-none d-xl-inline-block">
                        <i class="bi bi-calendar3 me-1"></i><?php echo e(date('d M, Y')); ?>

                    </span>

                    
                    <div class="dropdown d-none d-sm-inline-block">
                        <button class="btn btn-link text-decoration-none p-1 position-relative dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdownBtn">
                            <i class="bi bi-bell fs-5" id="bellIcon"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size: 0.6rem;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 notif-dropdown" style="min-width: 350px; max-width: 400px;" id="notifDropdownMenu">
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><i class="bi bi-bell me-2"></i>Notificaciones</strong>
                                    <small class="text-muted" id="notifCount">0 nuevas</small>
                                </div>
                            </li>
                            <li style="max-height: 300px; overflow-y: auto;" id="notifList">
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-arrow-repeat spin-loading"></i>
                                    <small>Cargando...</small>
                                </div>
                            </li>
                            <li class="border-top">
                                <a class="dropdown-item text-center small text-primary" href="<?php echo e(route('notifications.index')); ?>">
                                    <i class="bi bi-eye me-1"></i>Ver todas las notificaciones
                                </a>
                            </li>
                        </ul>
                    </div>

                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 px-lg-3 d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-shield-check"></i>
                        <span class="d-none d-md-inline"><?php echo e(ucfirst(Auth::user()?->roles?->first()?->name ?? Auth::user()?->role ?? 'Sin rol')); ?></span>
                    </span>
                </div>
            </header>

            <?php
                $subBannerUser = auth()->user();
                $subBannerInstance = ($subBannerUser && $subBannerUser->business_instance_id && ! $subBannerUser->hasRole('owner') && ! $subBannerUser->hasRole('root'))
                    ? $subBannerUser->businessInstance
                    : null;
                $subBannerEstado = $subBannerInstance ? $subBannerInstance->estadoSuscripcion() : null;
            ?>
            <?php if($subBannerInstance && $subBannerEstado !== 'activa'): ?>
                <?php
                    $bannerCfg = [
                        'prueba' => ['icon' => 'bi-rocket-takeoff', 'color' => 'info',
                            'titulo' => 'Estás en tu periodo de prueba gratuita',
                            'texto' => 'Tu prueba termina el ' . optional($subBannerInstance->trial_ends_at)->format('d/m/Y') . '. Quedan ' . $subBannerInstance->diasPruebaRestantes() . ' día(s). Paga tu suscripción para no interrumpir el servicio.',
                            'btn' => 'Ver Suscripción'],
                        'atrasada' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'warning',
                            'titulo' => 'Tu suscripción está vencida',
                            'texto' => 'Debes pagar RD$ ' . number_format($subBannerInstance->deudaEstimada(), 2) . ' para continuar usando el sistema.',
                            'btn' => 'Pagar Ahora'],
                        'suspendida' => ['icon' => 'bi-lock-fill', 'color' => 'danger',
                            'titulo' => 'Suscripción suspendida',
                            'texto' => 'Tu acceso ha sido restringido por falta de pago. Regulariza tu mensualidad para desbloquear el sistema.',
                            'btn' => 'Regularizar Pago'],
                    ];
                    $cfg = $bannerCfg[$subBannerEstado] ?? null;
                ?>
                <?php if($cfg): ?>
                <div class="px-4 px-lg-5 pt-3">
                    <div class="alert alert-<?php echo e($cfg['color']); ?> d-flex flex-wrap align-items-center gap-3 rounded-4 shadow-sm mb-0">
                        <i class="bi <?php echo e($cfg['icon']); ?> fs-3"></i>
                        <div class="flex-grow-1">
                            <strong><?php echo e($cfg['titulo']); ?>.</strong>
                            <span class="d-block small"><?php echo e($cfg['texto']); ?></span>
                        </div>
                        <a href="<?php echo e(route('suscripcion.index')); ?>" class="btn btn-<?php echo e($cfg['color']); ?> btn-sm rounded-pill px-3 fw-bold">
                            <i class="bi bi-credit-card me-1"></i><?php echo e($cfg['btn']); ?>

                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (! empty(trim($__env->yieldContent('fullbleed')))): ?>
                <?php echo $__env->yieldContent('fullbleed'); ?>
            <?php else: ?>
                <div class="<?php echo $__env->yieldContent('content_class', 'p-4 p-lg-5'); ?>">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <?php if(session('success')): ?>
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-2">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <div class="fw-bold">¡Operación Exitosa!</div>
                        <div class="small opacity-75"><?php echo e(session('success')); ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
        <div id="warningToast" class="toast align-items-center text-white bg-warning border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-2">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <div class="fw-bold">Aviso</div>
                        <div class="small opacity-75"><?php echo e(session('warning')); ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-2">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <div class="fw-bold">Error</div>
                        <div class="small opacity-75"><?php echo e(session('error')); ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>

        <?php if($errors->any() && !session('success') && !session('error')): ?>
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-2">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <div class="fw-bold">Ocurrió un Error</div>
                        <div class="small opacity-75"><?php echo e($errors->first()); ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function setSucursal(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("sucursal.set-activa")); ?>';
            form.innerHTML = '<?php echo csrf_field(); ?><input type="hidden" name="sucursal_id" value="' + (id || '') + '">';
            document.body.appendChild(form);
            form.submit();
        }

        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        document.addEventListener('DOMContentLoaded', function () {});

        /* ============================================================
           UI SYSTEM — Utilidades globales unificadas
           ============================================================ */
        window.UI = {
            confirm: {
                delete: function(url, label) {
                    UI._fire({
                        title: '\u00bfEliminar registro?',
                        text: label ? 'Se eliminar\u00e1: "' + label + '"' : null,
                        icon: 'error',
                        color: '#dc2626',
                        confirmText: 'S\u00ed, eliminar',
                        url: url
                    });
                },
                deleteWithForm: function(formId, label) {
                    var form = document.getElementById(formId);
                    if (!form) return;
                    UI._fire({
                        title: '\u00bfEliminar registro?',
                        text: label ? 'Se eliminar\u00e1: "' + label + '"' : null,
                        icon: 'error',
                        color: '#dc2626',
                        confirmText: 'S\u00ED, eliminar',
                        form: form
                    });
                },
                submit: function(formSelector, opts) {
                    UI._fire(Object.assign({}, opts || {}, {
                        form: document.querySelector(formSelector)
                    }));
                },
                action: function(opts) {
                    UI._fire(opts);
                }
            },
            toast: {
                success: function(msg) { UI._toast('success', msg); },
                error: function(msg) { UI._toast('danger', msg); },
                warning: function(msg) { UI._toast('warning', msg); },
                info: function(msg) { UI._toast('info', msg); }
            },
            _fire: function(opts) {
                var o = Object.assign({
                    title: '\u00bfEst\u00e1 seguro?',
                    text: '',
                    icon: 'warning',
                    color: '#dc2626',
                    confirmText: 'S\u00ed, continuar',
                    cancelText: 'Cancelar',
                    form: null,
                    url: null,
                    onSubmit: null,
                    callback: null
                }, opts);
                if (typeof Swal === 'undefined') {
                    return confirm(o.text || o.title);
                }
                Swal.fire({
                    title: o.title,
                    text: o.text,
                    icon: o.icon,
                    showCancelButton: true,
                    confirmButtonColor: o.color,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: o.confirmText,
                    cancelButtonText: o.cancelText,
                    reverseButtons: true,
                    allowOutsideClick: function() { return !Swal.isLoading(); }
                }).then(function(r) {
                    if (r.isConfirmed) {
                        if (o.callback) o.callback();
                        else if (o.onSubmit) o.onSubmit();
                        else if (o.form) o.form.submit();
                        else if (o.url) window.location.href = o.url;
                    }
                });
            },
            _toast: function(type, msg) {
                if (typeof Swal === 'undefined') return;
                var map = {
                    success: { icon: 'success', title: 'Listo', timer: 2000 },
                    danger: { icon: 'error', title: 'Error', timer: 3000 },
                    warning: { icon: 'warning', title: 'Aviso', timer: 3000 },
                    info: { icon: 'info', title: 'Informaci\u00f3n', timer: 2500 }
                };
                var cfg = map[type] || map.info;
                Swal.fire({ icon: cfg.icon, title: cfg.title, text: msg, timer: cfg.timer, showConfirmButton: false });
            }
        };

        /* Backward compatibility — delegates to UI.confirm */
        function confirmAction(opts) { return UI._fire(opts); }
        function confirmDelete(url, label) { UI.confirm.delete(url, label); }
        function confirmSubmit(formSelector, opts) { UI.confirm.submit(formSelector, opts); }
    </script>
    <script src="<?php echo e(asset('js/a11y.js')); ?>"></script>
    <script>
    (function() {
        'use strict';
        const input = document.getElementById('globalSearchInput');
        if (!input) return;
        const results = document.getElementById('globalSearchResults');
        let timer = null;
        let selectedIdx = -1;

        // Ctrl+K / Cmd+K to focus
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                input.focus();
                input.select();
            }
            if (e.key === 'Escape' && results.style.display !== 'none') {
                results.style.display = 'none';
                input.blur();
            }
        });

        input.addEventListener('input', function() {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) {
                results.style.display = 'none';
                results.innerHTML = '';
                return;
            }
            results.innerHTML = '<div class="global-search-loading"><i class="bi bi-arrow-repeat spinning me-1"></i> Buscando...</div>';
            results.style.display = 'block';
            selectedIdx = -1;
            timer = setTimeout(function() {
                fetch('/search?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            results.innerHTML = '<div class="global-search-nores">Sin resultados para "<strong>' + escapeHtml(q) + '</strong>"</div>';
                            return;
                        }
                        let html = '';
                        data.forEach(function(item, i) {
                            const badge = item.badge ? '<span class="gs-badge">' + escapeHtml(item.badge) + '</span>' : '';
                            html += '<a href="' + item.url + '" class="gs-item" data-idx="' + i + '">';
                            html += '<div class="gs-icon" style="background:' + iconBg(item.type) + ';color:' + iconColor(item.type) + ';"><i class="bi ' + item.icon + '"></i></div>';
                            html += '<div class="flex-grow-1 overflow-hidden"><div class="gs-label text-truncate">' + escapeHtml(item.label) + '</div><div class="gs-sub text-truncate">' + escapeHtml(item.sub) + '</div></div>';
                            html += badge;
                            html += '</a>';
                        });
                        results.innerHTML = html;
                    })
                    .catch(function() {
                        results.innerHTML = '<div class="global-search-nores">Error al buscar</div>';
                    });
            }, 250);
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const items = results.querySelectorAll('.gs-item');
                if (items.length === 0) return;
                selectedIdx = Math.min(selectedIdx + 1, items.length - 1);
                highlightItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const items = results.querySelectorAll('.gs-item');
                if (items.length === 0) return;
                selectedIdx = Math.max(selectedIdx - 1, -1);
                highlightItem(items);
            } else if (e.key === 'Enter' && selectedIdx >= 0) {
                e.preventDefault();
                const items = results.querySelectorAll('.gs-item');
                if (items[selectedIdx]) items[selectedIdx].click();
            }
        });

        function highlightItem(items) {
            items.forEach(function(el, i) {
                el.style.background = i === selectedIdx ? '#e2e8f0' : '';
                if (i === selectedIdx) el.scrollIntoView({ block: 'nearest' });
            });
        }

        function escapeHtml(s) {
            return String(s || '').replace(/[&<>"']/g, function(c) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
            });
        }

        function iconBg(type) {
            const map = { producto: 'rgba(56,189,248,.12)', cliente: 'rgba(34,197,94,.12)', venta: 'rgba(99,102,241,.12)', compra: 'rgba(245,158,11,.12)', proveedor: 'rgba(239,68,68,.12)' };
            return map[type] || 'rgba(100,116,139,.12)';
        }
        function iconColor(type) {
            const map = { producto: '#0284c7', cliente: '#16a34a', venta: '#4f46e5', compra: '#d97706', proveedor: '#dc2626' };
            return map[type] || '#64748b';
        }

        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.global-search-wrap') && results.style.display !== 'none') {
                results.style.display = 'none';
            }
        });

        // Close on blur with delay
        input.addEventListener('blur', function() {
            setTimeout(function() { results.style.display = 'none'; }, 200);
        });
    })();
    </script>

    <!-- AI Chat Assistant -->
    <?php if (isset($component)) { $__componentOriginal03177c265acb18dcea31d30d120be27c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal03177c265acb18dcea31d30d120be27c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ai-chat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ai-chat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal03177c265acb18dcea31d30d120be27c)): ?>
<?php $attributes = $__attributesOriginal03177c265acb18dcea31d30d120be27c; ?>
<?php unset($__attributesOriginal03177c265acb18dcea31d30d120be27c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal03177c265acb18dcea31d30d120be27c)): ?>
<?php $component = $__componentOriginal03177c265acb18dcea31d30d120be27c; ?>
<?php unset($__componentOriginal03177c265acb18dcea31d30d120be27c); ?>
<?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Notification / Activity Feed Widget -->
    <script src="<?php echo e(asset('js/notifications.js')); ?>"></script>

    <script>
    (function() {
        'use strict';
        const input = document.getElementById('globalSearchInput');
        if (!input) return;
        const results = document.getElementById('globalSearchResults');
        let timer = null;
        let selectedIdx = -1;

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && results.style.display !== 'none') {
                results.style.display = 'none';
            }
        });

        if (!results) return;
    })();
    </script>

</body>

</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/layouts/app.blade.php ENDPATH**/ ?>