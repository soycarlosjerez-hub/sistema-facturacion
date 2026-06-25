{{-- DataTables UI - Partial CSS reutilizable para tablas DataTables estilo premium --}}
{{-- Incluir con: @include('partials.datatable-ui') --}}
{{-- Setear variables CSS antes: --dt-accent, --dt-accent-gradient, --dt-accent-rgb --}}

<style>
/* ============================================================
   DATATABLE UI - Table base
   ============================================================ */
.dt-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(var(--dt-accent-rgb, 99,102,241),.04);
    margin: 0;
    width: 100% !important;
}
.dt-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.dt-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.dt-table tbody tr:last-child td { border-bottom: none; }
.dt-table tbody tr { transition: background .15s; }
.dt-table tbody tr:hover { background: rgba(var(--dt-accent-rgb, 99,102,241),.03); }

/* ============================================================
   DATATABLE UI - Wrapper
   ============================================================ */
[id$="_wrapper"] { padding: 0; }
[id$="_wrapper"] > .row:first-child {
    padding: 0 1rem;
    margin-bottom: 0.5rem;
}
[id$="_wrapper"] > .row:last-child {
    padding: 0 1rem;
    margin-top: 0;
}

/* ============================================================
   DATATABLE UI - Length menu
   ============================================================ */
[id$="_wrapper"] .dataTables_length {
    font-size: .85rem;
    color: #64748b;
}
[id$="_wrapper"] .dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
}
[id$="_wrapper"] .dataTables_length select {
    border-radius: .5rem;
    border: 1.5px solid #e2e8f0;
    padding: 0.35rem 2rem 0.35rem 0.75rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color .2s;
}
[id$="_wrapper"] .dataTables_length select:focus {
    border-color: var(--dt-accent, #3b82f6);
    box-shadow: 0 0 0 3px rgba(var(--dt-accent-rgb, 59,130,246),.1);
    outline: none;
}

/* ============================================================
   DATATABLE UI - Search filter
   ============================================================ */
[id$="_wrapper"] .dataTables_filter { text-align: right; }
[id$="_wrapper"] .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
    font-size: .85rem;
    color: #64748b;
}
[id$="_wrapper"] .dataTables_filter input {
    border-radius: 2rem;
    border: 1.5px solid #e2e8f0;
    padding: 0.45rem 1rem 0.45rem 2.2rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    width: 240px;
    max-width: 100%;
    transition: all .2s;
}
[id$="_wrapper"] .dataTables_filter input:focus {
    border-color: var(--dt-accent, #3b82f6);
    box-shadow: 0 0 0 3px rgba(var(--dt-accent-rgb, 59,130,246),.1);
    outline: none;
    width: 280px;
}

/* ============================================================
   DATATABLE UI - Scroll
   ============================================================ */
[id$="_wrapper"] .dataTables_scroll { overflow: visible; }
[id$="_wrapper"] .dataTables_scrollHead { overflow: visible !important; }
[id$="_wrapper"] .dataTables_scrollHeadInner { width: 100% !important; padding-right: 0 !important; }
[id$="_wrapper"] .dataTables_scrollBody { overflow: visible !important; }

/* ============================================================
   DATATABLE UI - Info
   ============================================================ */
[id$="_wrapper"] .dataTables_info {
    font-size: .8rem;
    color: #64748b;
    padding: 0.75rem 0;
    font-weight: 500;
}

/* ============================================================
   DATATABLE UI - Pagination
   ============================================================ */
[id$="_wrapper"] .dataTables_paginate {
    padding: 0.5rem 0;
    text-align: right !important;
}
[id$="_wrapper"] .dataTables_paginate .paginate_button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 0.6rem;
    margin: 0 2px;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.5rem;
    background: #fff;
    color: #475569;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    line-height: 1;
}
[id$="_wrapper"] .dataTables_paginate .paginate_button:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}
[id$="_wrapper"] .dataTables_paginate .paginate_button.current {
    background: var(--dt-accent-gradient, linear-gradient(135deg, #3b82f6, #6366f1)) !important;
    border-color: var(--dt-accent, #3b82f6) !important;
    color: #fff !important;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(var(--dt-accent-rgb, 59,130,246),.25);
}
[id$="_wrapper"] .dataTables_paginate .paginate_button.current:hover {
    filter: brightness(1.1);
}
[id$="_wrapper"] .dataTables_paginate .paginate_button.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: transparent;
    border-color: #e2e8f0;
    color: #94a3b8;
}

/* ============================================================
   DATATABLE UI - Responsive child rows
   ============================================================ */
table.dataTable > tbody > tr.child { background: #f8fafc; }
table.dataTable > tbody > tr.child ul {
    margin: 0;
    padding: 0.5rem 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1.5rem;
}
table.dataTable > tbody > tr.child ul li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: .85rem;
    color: #475569;
}
table.dataTable > tbody > tr.child ul li .child-label {
    font-weight: 600;
    color: #64748b;
    min-width: 100px;
}
table.dataTable > tbody > tr.child ul li .child-value {
    color: #1e293b;
}

/* ============================================================
   DATATABLE UI - Dtr control arrow
   ============================================================ */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
    position: relative;
    padding-left: 2.5rem;
    cursor: pointer;
}
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
    top: 50%;
    transform: translateY(-50%);
    left: 0.75rem;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid #94a3b8;
    transition: transform .2s, border-color .2s;
}
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control::before {
    border-top: none;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 6px solid var(--dt-accent, #3b82f6);
}

/* ============================================================
   DATATABLE UI - Empty / No results
   ============================================================ */
.dataTables_empty {
    text-align: center !important;
    padding: 3rem 1rem !important;
    color: #94a3b8;
}

/* ============================================================
   DATATABLE UI - Dark Mode
   ============================================================ */
body.dark-mode .dt-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-bottom-color: #1e293b;
}
body.dark-mode .dt-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode .dt-table tbody tr:hover {
    background: rgba(var(--dt-accent-rgb, 99,102,241),.05);
}

body.dark-mode [id$="_wrapper"] .dataTables_length select {
    background-color: #1e293b;
    color: #f1f5f9;
    border-color: #334155;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
}
body.dark-mode [id$="_wrapper"] .dataTables_filter input {
    background-color: #1e293b;
    color: #f1f5f9;
    border-color: #334155;
}
body.dark-mode [id$="_wrapper"] .dataTables_filter input::placeholder {
    color: #64748b;
}
body.dark-mode [id$="_wrapper"] .dataTables_info {
    color: #64748b;
}

body.dark-mode [id$="_wrapper"] .dataTables_paginate .paginate_button {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode [id$="_wrapper"] .dataTables_paginate .paginate_button:hover {
    background: #334155;
    border-color: #475569;
    color: #f1f5f9;
}
body.dark-mode [id$="_wrapper"] .dataTables_paginate .paginate_button.current {
    background: var(--dt-accent-gradient, linear-gradient(135deg, #3b82f6, #6366f1)) !important;
    border-color: var(--dt-accent, #3b82f6) !important;
    color: #fff !important;
}
body.dark-mode [id$="_wrapper"] .dataTables_paginate .paginate_button.disabled {
    background: transparent;
    border-color: #1e293b;
    color: #475569;
}

body.dark-mode table.dataTable > tbody > tr.child { background: #0f172a; }
body.dark-mode table.dataTable > tbody > tr.child ul li { color: #cbd5e1; }
body.dark-mode table.dataTable > tbody > tr.child ul li .child-label { color: #94a3b8; }
body.dark-mode table.dataTable > tbody > tr.child ul li .child-value { color: #f1f5f9; }

body.dark-mode [id$="_wrapper"] .dataTables_length label { color: #94a3b8; }
body.dark-mode [id$="_wrapper"] .dataTables_filter label { color: #94a3b8; }
</style>