<style>
    .role-stat-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(20px);
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        border: 1px solid rgba(15,23,42,0.06);
        box-shadow: 0 4px 12px rgba(15,23,42,0.04);
        transition: all 0.3s;
    }
    .role-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15,23,42,0.10); }
    .role-stat-card .icon-bubble {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }

    .role-big-card {
        background: var(--card-bg, white);
        border-radius: 20px;
        border: 2px solid transparent;
        padding: 1.5rem;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .role-big-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: var(--role-gradient);
    }
    .role-big-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.10); }
    .role-big-card.protected {
        border-color: rgba(245,158,11,0.3);
    }
    .role-big-card .role-icon-lg {
        width: 64px; height: 64px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 1.7rem;
        margin-bottom: 14px;
    }
    .role-big-card .role-name {
        font-size: 1.3rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .role-big-card .perm-bar {
        height: 6px;
        background: rgba(15,23,42,0.06);
        border-radius: 999px;
        overflow: hidden;
        margin: 10px 0;
    }
    .role-big-card .perm-bar-fill {
        height: 100%;
        background: var(--role-gradient);
        border-radius: 999px;
        transition: width 0.5s;
    }

    .perm-module-card {
        background: var(--card-bg, white);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(15,23,42,0.06);
        transition: all 0.2s;
        height: 100%;
    }
    .perm-module-card:hover { border-color: var(--accent-color, #38bdf8); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .perm-module-card .module-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(15,23,42,0.06);
    }
    .perm-module-card .module-title {
        font-weight: 800;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--accent-color, #38bdf8);
    }
    .perm-module-card .module-icon {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--accent-color, #38bdf8);
        color: white;
    }

    .perm-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid transparent;
    }
    .perm-toggle:hover { background: rgba(56,189,248,0.05); }
    .perm-toggle input[type="checkbox"] {
        width: 16px; height: 16px;
        cursor: pointer;
        accent-color: var(--accent-color, #38bdf8);
    }
    .perm-toggle.is-checked {
        background: rgba(34,197,94,0.08);
        border-color: rgba(34,197,94,0.3);
    }
    .perm-toggle.is-checked .perm-name {
        color: #16a34a;
        font-weight: 700;
    }
    .perm-toggle .perm-name {
        font-size: 0.8rem;
        flex-grow: 1;
    }

    .role-action-btn {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15,23,42,0.04);
        color: #64748b;
        border: 0;
        transition: all 0.2s;
    }
    .role-action-btn:hover { transform: translateY(-1px); }
    .role-action-btn.edit:hover { background: rgba(56,189,248,0.15); color: #0284c7; }
    .role-action-btn.view:hover { background: rgba(34,197,94,0.15); color: #16a34a; }
    .role-action-btn.delete:hover { background: rgba(239,68,68,0.15); color: #dc2626; }

    .protected-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(245,158,11,0.15);
        color: #b45309;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .matrix-table {
        font-size: 0.8rem;
    }
    .matrix-table th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 5;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .matrix-table td {
        padding: 6px 8px !important;
        vertical-align: middle;
    }
    .matrix-table .module-row td {
        background: rgba(15,23,42,0.04);
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }
    .matrix-table .perm-cell {
        text-align: center;
    }
    .matrix-table .perm-check {
        display: inline-block;
        width: 22px; height: 22px;
        border-radius: 6px;
        line-height: 22px;
    }
    .matrix-table .perm-check.on {
        background: rgba(34,197,94,0.15);
        color: #16a34a;
    }
    .matrix-table .perm-check.off {
        background: rgba(239,68,68,0.05);
        color: #cbd5e1;
    }
    .matrix-table .perm-name-cell {
        font-size: 0.75rem;
        color: #475569;
    }
</style>