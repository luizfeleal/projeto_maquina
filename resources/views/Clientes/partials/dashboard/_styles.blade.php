<style>
    :root {
        --sp-navy: #1E2E5E;
        --sp-teal: #2C9BA5;
        --sp-teal-light: #d5ebed;
        --sp-border: #e8ecf0;
        --sp-muted: #6b7280;
        --sp-bg: #f8fafc;
    }

    .dash-page {
        padding: 20px 24px 32px;
    }
    .dash-page .page-heading {
        padding: 0 0 20px;
        margin: 0;
    }
    .dash-page .page-heading h1 {
        margin-bottom: 6px;
    }

    .dash-section { margin-bottom: 28px; scroll-margin-top: 80px; }
    .dash-section:last-child { margin-bottom: 0; }
    .dash-section-header { margin-bottom: 16px; }
    .dash-section-header h2 {
        margin: 0 0 4px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dash-section-header p { margin: 0; font-size: .82rem; color: var(--sp-muted); }

    .dash-card {
        background: #fff;
        border: 1px solid var(--sp-border);
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .dash-card-header {
        padding: 16px 20px 12px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dash-card-body { padding: 16px 20px; flex: 1; }
    .dash-card-footer {
        padding: 12px 20px;
        border-top: 1px solid #f3f4f6;
    }

    .dash-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .dash-icon--teal   { background: var(--sp-teal-light); color: var(--sp-teal); }
    .dash-icon--navy   { background: #e8ecf8; color: var(--sp-navy); }
    .dash-icon--warn   { background: #fef3c7; color: #ca8a04; }
    .dash-icon--danger { background: #fee2e2; color: #dc2626; }
    .dash-icon--ok     { background: #dcfce7; color: #16a34a; }

    .dash-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .dash-kpi {
        background: #fff;
        border: 1px solid var(--sp-border);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .dash-kpi-label {
        font-size: .68rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: #9ca3af; margin-bottom: 8px;
        display: flex; align-items: center; gap: 6px;
    }
    .dash-kpi-value { font-size: 1.35rem; font-weight: 800; color: #111827; line-height: 1.1; }
    .dash-kpi-sub { margin-top: 6px; font-size: .72rem; color: var(--sp-muted); }

    .dash-btn-secondary {
        width: 100%; padding: 9px;
        background: var(--sp-bg); border: 1px solid var(--sp-border);
        border-radius: 10px; font-size: .8rem; font-weight: 600;
        color: #374151; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        text-decoration: none; transition: background .15s;
    }
    .dash-btn-secondary:hover { background: #f1f5f9; color: #374151; }

    .dash-btn-primary {
        padding: 10px 24px;
        background: var(--sp-teal); border: none;
        border-radius: 10px; font-size: .85rem; font-weight: 700;
        color: #fff; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        text-decoration: none;
    }
    .dash-btn-primary:hover { background: #248a93; color: #fff; }

    .dash-nav {
        position: sticky; top: 0; z-index: 10;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--sp-border);
        border-radius: 14px;
        padding: 8px 10px;
        margin-bottom: 24px;
        display: flex; flex-wrap: wrap; gap: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .dash-nav a, .dash-nav-link {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: .78rem; font-weight: 600;
        color: var(--sp-muted);
        text-decoration: none;
        display: flex; align-items: center; gap: 5px;
        transition: all .15s;
    }
    .dash-nav a:hover, .dash-nav-link:hover,
    .dash-nav a.active, .dash-nav-link.active {
        background: var(--sp-teal-light);
        color: var(--sp-navy);
    }

    .dash-row-line {
        display: flex; align-items: center; justify-content: space-between;
        padding: 9px 0; border-bottom: 1px solid #f9fafb;
    }
    .dash-row-line:last-child { border-bottom: none; }

    .dash-action-btn {
        flex: 1; min-width: 90px;
        text-align: center; text-decoration: none;
        background: var(--sp-bg); border: 1px solid var(--sp-border);
        border-radius: 8px; padding: 7px 8px;
        font-size: .75rem; font-weight: 600; color: #374151;
        display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .dash-action-btn:hover { background: #f1f5f9; color: #374151; }

    .rel-tab-btn.active {
        background: #fff !important;
        color: var(--sp-teal) !important;
        border-bottom-color: var(--sp-teal) !important;
    }

    .dash-tx-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .dash-tx-table th {
        text-align: left; padding: 10px 12px;
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
        color: #9ca3af; border-bottom: 1px solid #f3f4f6;
    }
    .dash-tx-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f9fafb;
        color: #374151;
    }
    .dash-tx-table tr:last-child td { border-bottom: none; }

    .dash-filter-select {
        border: 1px solid var(--sp-border); border-radius: 8px;
        padding: 8px 14px; font-size: .85rem;
        color: #374151; background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
        min-width: 240px; cursor: pointer;
    }

    .maq-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .maq-search-wrap {
        flex: 1;
        min-width: 220px;
        position: relative;
    }
    .maq-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1rem;
        pointer-events: none;
    }
    .maq-search-input {
        width: 100%;
        border: 1px solid var(--sp-border);
        border-radius: 10px;
        padding: 10px 14px 10px 38px;
        font-size: .85rem;
        color: #374151;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    .maq-search-input:focus {
        outline: none;
        border-color: var(--sp-teal);
        box-shadow: 0 0 0 3px rgba(44, 155, 165, 0.15);
    }
    .maq-count-badge {
        font-size: .78rem;
        font-weight: 600;
        color: var(--sp-muted);
        white-space: nowrap;
    }
    .maq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }
    .maq-pagination {
        margin-top: 16px;
        padding-top: 4px;
    }
    .maq-pagination .page-link {
        font-size: .8rem;
        min-width: 36px;
        text-align: center;
    }
    .maq-empty-filter {
        padding: 40px 24px;
        text-align: center;
        margin-top: 4px;
    }

    @media (max-width: 991px) {
        .dash-page { padding: 16px 16px 24px; }
    }
    @media (max-width: 576px) {
        .dash-page { padding: 12px 12px 20px; }
        .dash-section { margin-bottom: 22px; }
        .dash-nav { margin-bottom: 18px; }
    }
</style>
