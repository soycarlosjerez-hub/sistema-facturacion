<style>
    .role-picker {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
    }
    .role-card {
        position: relative;
        padding: 16px 14px;
        border-radius: 16px;
        border: 2px solid rgba(15,23,42,0.08);
        background: var(--card-bg, white);
        cursor: pointer;
        transition: all 0.25s;
        overflow: hidden;
    }
    .role-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--role-gradient, #38bdf8);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s;
    }
    .role-card:hover { transform: translateY(-2px); border-color: var(--role-color, #38bdf8); }
    .role-card:hover::before { transform: scaleX(1); }
    .role-card.active {
        border-color: var(--role-color, #38bdf8);
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.95)), var(--role-gradient, transparent);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .role-card.active::before { transform: scaleX(1); }
    .role-card .role-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    .role-card .role-name {
        font-weight: 800;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .role-card .role-desc {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.3;
    }
    .role-card .role-perms {
        font-size: 0.7rem;
        color: var(--role-color, #38bdf8);
        font-weight: 700;
        margin-top: 6px;
    }
    .role-card input[type="radio"] { display: none; }
    .role-card.active::after {
        content: "\F26B";
        font-family: "bootstrap-icons";
        position: absolute;
        top: 10px; right: 10px;
        color: var(--role-color, #38bdf8);
        font-size: 1.1rem;
    }
    body.dark-mode .role-card { background: rgba(30,41,59,0.95); border-color: rgba(255,255,255,0.05); }
    body.dark-mode .role-card .role-desc { color: #94a3b8; }

    .form-floating-modern {
        position: relative;
        margin-bottom: 1rem;
    }
    .form-floating-modern > .form-control,
    .form-floating-modern > .form-control:focus {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1rem 1rem 3rem;
        height: calc(3.5rem + 2px);
        font-size: 0.95rem;
        background: var(--input-bg, white);
        transition: all 0.2s;
    }
    .form-floating-modern > .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 4px rgba(56,189,248,0.1);
    }
    .form-floating-modern > .form-icon {
        position: absolute;
        left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.1rem;
        z-index: 4;
    }
    .form-floating-modern > .form-label-float {
        position: absolute;
        left: 3rem; top: 1.05rem;
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
        transition: all 0.2s;
    }
    .form-floating-modern > .form-control:focus + .form-label-float,
    .form-floating-modern > .form-control:not(:placeholder-shown) + .form-label-float {
        top: -0.5rem; left: 1rem;
        font-size: 0.7rem;
        background: var(--card-bg, white);
        padding: 0 6px;
        color: #38bdf8;
        font-weight: 700;
    }
    body.dark-mode .form-floating-modern > .form-control { background: rgba(15,23,42,0.5); color: #f1f5f9; }
    body.dark-mode .form-floating-modern > .form-control:focus + .form-label-float { background: rgba(30,41,59,0.95); }

    .permission-preview {
        background: rgba(15,23,42,0.03);
        border-radius: 16px;
        padding: 16px;
        border: 1px dashed rgba(15,23,42,0.1);
    }
    body.dark-mode .permission-preview { background: rgba(15,23,42,0.3); border-color: rgba(255,255,255,0.05); }
    .perm-group {
        margin-bottom: 10px;
    }
    .perm-group-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
    }
    .perm-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(56,189,248,0.1);
        color: #0284c7;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        margin: 2px;
    }
    .perm-tag .bi { font-size: 0.7rem; }

    .password-strength {
        height: 4px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 8px;
    }
    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: width 0.3s, background 0.3s;
    }

    .page-header-gradient {
        background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(56,189,248,0.25);
        position: relative;
        overflow: hidden;
    }
    .page-header-gradient::after {
        content: "";
        position: absolute;
        right: -50px; top: -50px;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    }
</style>
