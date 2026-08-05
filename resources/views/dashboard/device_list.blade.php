{{-- @page-meta { "page_no": "DL-01", "version": "1.0", "description": "Devices Listing - Asset Management" } --}}
@extends('layouts.layout1')
@section('title', 'Devices')
@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   DEVICES LISTING  –  dvl-*
   Light: --app-bg:#f8f9fa  --app-surface:#fff  --app-border:#dee2e6
   Dark:  --app-bg:#141414  --dark-primary:#191919
          --dark-secondary:#2a2a2a  --dark-border:#2a2a2d
          --dark-hover:#262626  --text-primary:#fff
          --text-secondary:#e5e7eb  --text-muted:#757575
   ═══════════════════════════════════════════════════════ */

/* ── 1. HEADER ─────────────────────────────────────── */
.dvl-header {
    background     : linear-gradient(90deg,#fde8e0 0%,#fef3ee 45%,#fdf8f6 72%,#f8fafd 100%);
    border-bottom  : 1px solid #f5ddd5;
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 12px;
    padding-right  : 1.25rem !important;
    overflow       : hidden;
}
[data-bs-theme="dark"] .dvl-header {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.dvl-page-title {
    font-size:16px; font-weight:700;
    color:var(--app-text,#212529); white-space:nowrap;
}
[data-bs-theme="dark"] .dvl-page-title { color:var(--text-primary,#fff) !important; }

/* Header action icon buttons */
.dvl-hdr-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
.dvl-icon-btn {
    width:34px; height:34px; border-radius:8px;
    border:1px solid var(--app-border,#dee2e6);
    background:var(--app-surface,#fff);
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; color:var(--app-text,#6b7280) !important;
    transition:background .15s; flex-shrink:0;
}
.dvl-icon-btn:hover { background:var(--app-bg,#f8f9fa); }
.dvl-icon-btn svg { width:15px; height:15px; }
[data-bs-theme="dark"] .dvl-icon-btn {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}
[data-bs-theme="dark"] .dvl-icon-btn:hover { background:var(--dark-hover,#262626) !important; }

/* Filter button */
.dvl-filter-btn {
    display:inline-flex; align-items:center; gap:6px;
    border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; padding:6px 14px;
    font-size:12.5px; font-family:inherit;
    color:var(--app-text,#212529) !important;
    background:var(--app-surface,#fff);
    cursor:pointer; white-space:nowrap;
}
.dvl-filter-btn svg { width:14px; height:14px; color:#9ca3af; }
[data-bs-theme="dark"] .dvl-filter-btn {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-secondary,#e5e7eb) !important;
}

/* Add Device button */
.dvl-add-btn {
    display:inline-flex; align-items:center; gap:6px;
    background:#ef4444; color:#fff !important;
    border:none; border-radius:8px;
    padding:8px 16px; font-size:13px; font-weight:600;
    font-family:inherit; cursor:pointer;
    white-space:nowrap; transition:background .15s;
}
.dvl-add-btn:hover { background:#dc2626; }
.dvl-add-btn svg { width:14px; height:14px; }

/* ── 2. BODY ───────────────────────────────────────── */
.dvl-body {
    padding   : 14px 16px calc(var(--footer-height,30px)+20px);
    background: var(--app-bg,#f8f9fa);
    box-sizing: border-box;
}
[data-bs-theme="dark"] .dvl-body { background:var(--app-bg,#141414) !important; }

/* Table card */
.dvl-card {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 12px;
    overflow     : hidden;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .dvl-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* ── 3. TOOLBAR (show + search + refresh + expand) ── */
.dvl-toolbar {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    padding        : 10px 16px;
    border-bottom  : 1px solid var(--app-border,#dee2e6);
    gap            : 10px;
    flex-wrap      : wrap;
}
[data-bs-theme="dark"] .dvl-toolbar { border-color:var(--dark-border,#2a2a2d) !important; }

.dvl-show-sel {
    display:inline-flex; align-items:center; gap:6px;
    border:1px solid var(--app-border,#dee2e6); border-radius:8px;
    padding:6px 10px; font-size:12.5px; font-family:inherit;
    color:var(--app-text,#212529) !important;
    background:var(--app-surface,#fff); cursor:pointer;
}
.dvl-show-sel svg { width:13px; height:13px; color:#9ca3af; }
[data-bs-theme="dark"] .dvl-show-sel {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-secondary,#e5e7eb) !important;
}

.dvl-toolbar-right { display:flex; align-items:center; gap:8px; }

.dvl-search {
    display:inline-flex; align-items:center; gap:6px;
    border:1px solid var(--app-border,#dee2e6); border-radius:8px;
    padding:6px 12px; background:var(--app-surface,#fff);
}
.dvl-search input {
    border:none; outline:none; font-size:12.5px;
    color:var(--app-text,#212529); background:transparent;
    font-family:inherit; width:160px;
}
.dvl-search input::placeholder { color:#9ca3af; }
.dvl-search svg { width:14px; height:14px; color:#9ca3af; flex-shrink:0; }
[data-bs-theme="dark"] .dvl-search {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .dvl-search input { color:var(--text-secondary,#e5e7eb) !important; }

.dvl-refresh-btn {
    display:inline-flex; align-items:center; gap:5px;
    border:none; background:none; cursor:pointer;
    font-size:12.5px; font-family:inherit;
    color:var(--app-text,#6b7280) !important; padding:6px 4px;
}
.dvl-refresh-btn svg { width:14px; height:14px; }
[data-bs-theme="dark"] .dvl-refresh-btn { color:var(--text-muted,#757575) !important; }

/* Expand/collapse toggle btn */
.dvl-expand-btn {
    width:32px; height:32px; border-radius:8px;
    border:1px solid var(--app-border,#dee2e6);
    background:var(--app-surface,#fff);
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; transition:background .15s;
    color:var(--app-text,#6b7280) !important;
}
.dvl-expand-btn:hover { background:var(--app-bg,#f8f9fa); }
.dvl-expand-btn svg { width:15px; height:15px; }
[data-bs-theme="dark"] .dvl-expand-btn {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}

/* ── 4. TABLE ──────────────────────────────────────── */
/* ── 4. TABLE ──────────────────────────────────────── */
.dvl-table {
    width          : 100%;
    border-collapse: collapse;
    min-width      : 900px;
}

/* Table header */
.dvl-thead th {
    padding      : 12px 16px;
    font-size    : 12.5px; font-weight:600;
    color        : #374151;
    text-align   : left;
    border-top   : 1px solid var(--app-border,#dee2e6);
    border-bottom: 1px solid var(--app-border,#dee2e6);
    white-space  : nowrap;
    background   : var(--app-surface,#fff);
    user-select  : none;
}
[data-bs-theme="dark"] .dvl-thead th {
    color        : var(--text-secondary,#e5e7eb) !important;
    border-color : var(--dark-border,#2a2a2d) !important;
    background   : var(--dark-secondary,#2a2a2a) !important;
}
.dvl-sort-icon { display:inline-flex; align-items:center; gap:3px; cursor:pointer; }
.dvl-sort-icon svg { width:11px; height:11px; color:#9ca3af; }

/* Rows — flat, alternating bg */
.dvl-row { cursor:pointer; transition:background .12s; }
.dvl-row:nth-child(odd) td { background:#ffffff; }
.dvl-row.row-odd td { background:#ffffff; }
.dvl-row.row-even td { background:#eef1ff; }
.dvl-row:nth-child(even) td { background:#eef1ff; }
.dvl-row:hover td { background:#e4e8ff !important; }
[data-bs-theme="dark"] .dvl-row.row-odd  td { background:var(--dark-secondary,#2a2a2a) !important; }
[data-bs-theme="dark"] .dvl-row.row-even td { background:var(--dark-primary,#191919) !important; }
[data-bs-theme="dark"] .dvl-row:hover    td { background:var(--dark-hover,#262626) !important; }

/* Cells */
.dvl-row td {
    padding        : 16px;
    font-size      : 12.5px;
    color          : var(--app-text,#212529);
    vertical-align : middle;
    border-bottom  : 1px solid var(--app-border,#dee2e6);
}
.dvl-row td:first-child { padding-left:16px; }
.dvl-row td:last-child  { padding-right:16px; }
[data-bs-theme="dark"] .dvl-row td {
    color        : var(--text-secondary,#e5e7eb) !important;
    border-color : var(--dark-border,#2a2a2d) !important;
}
.dvl-spacer { display:none; }

/* Checkbox */
.dvl-cb { width:15px; height:15px; accent-color:#ef4444; cursor:pointer; }

/* ── DEVICE CELL ────────────────────────────────────── */
.dvl-dev-cell { display:flex; align-items:center; gap:10px; }
.dvl-dev-thumb {
    width:52px; height:42px; border-radius:6px;
    object-fit:cover; flex-shrink:0;
    border:1px solid var(--app-border,#dee2e6);
    background:#f1f5f9;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
}
.dvl-dev-thumb img { width:100%; height:100%; object-fit:cover; }
.dvl-dev-thumb svg { width:22px; height:22px; color:#cbd5e1; }
[data-bs-theme="dark"] .dvl-dev-thumb { border-color:var(--dark-border,#2a2a2d) !important; background:var(--dark-primary,#191919) !important; }

.dvl-dev-name { font-size:13px; font-weight:600; color:var(--app-text,#212529); line-height:1.3; }
.dvl-dev-brand { font-size:11.5px; color:#6b7280; margin-top:1px; display:flex; align-items:center; gap:4px; }
.dvl-dev-ip    { font-size:11.5px; color:#6b7280; margin-top:1px; }
.dvl-dev-type  { font-size:11.5px; color:#6b7280; margin-top:1px; }
[data-bs-theme="dark"] .dvl-dev-name  { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .dvl-dev-brand,
[data-bs-theme="dark"] .dvl-dev-ip,
[data-bs-theme="dark"] .dvl-dev-type  { color:var(--text-muted,#757575) !important; }

/* Brand icons (unicode) */
.dvl-brand-icons { font-size:11px; letter-spacing:2px; }

/* ── MODEL CELL ─────────────────────────────────────── */
.dvl-model-name { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); line-height:1.3; }
.dvl-model-sub  { font-size:11.5px; color:#6b7280; margin-top:1px; }
.dvl-model-conn { font-size:12px; font-weight:600; color:var(--app-text,#212529); margin-top:2px; }
[data-bs-theme="dark"] .dvl-model-name { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .dvl-model-sub  { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .dvl-model-conn { color:var(--text-secondary,#e5e7eb) !important; }

/* ── STATUS BADGES ──────────────────────────────────── */
.dvl-badge {
    display:inline-flex; align-items:center;
    font-size:11.5px; font-weight:500;
    padding:4px 12px; border-radius:20px; white-space:nowrap;
}
.dvl-badge-deploy {
    background:#f3e8ff; color:#7c3aed !important;
    border:1px solid #e9d5ff;
}
.dvl-badge-deployed {
    background:#d1fae5; color:#065f46 !important;
    border:1px solid #a7f3d0;
}
[data-bs-theme="dark"] .dvl-badge-deploy   { background:#2e1065 !important; color:#c4b5fd !important; border-color:#4c1d95 !important; }
[data-bs-theme="dark"] .dvl-badge-deployed { background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important; }

/* ── WARRANTY CELL ──────────────────────────────────── */
.dvl-warranty-date { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
.dvl-warranty-val  { font-size:12px; color:#22c55e; font-weight:500; margin-top:2px; }
[data-bs-theme="dark"] .dvl-warranty-date { color:var(--text-secondary,#e5e7eb) !important; }

/* ── ASSIGNED TO CELL ───────────────────────────────── */
.dvl-user-row { display:flex; align-items:center; gap:7px; margin-bottom:4px; }
.dvl-user-av {
    width:26px; height:26px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#6366f1,#4338ca);
    display:flex; align-items:center; justify-content:center;
    font-size:8px; font-weight:700; color:#fff; overflow:hidden;
}
.dvl-user-av img { width:100%; height:100%; object-fit:cover; }
.dvl-user-name { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
.dvl-user-email-icon {
    width:18px; height:18px; border-radius:4px;
    background:#e0e7ff; display:inline-flex;
    align-items:center; justify-content:center;
}
.dvl-user-email-icon svg { width:10px; height:10px; color:#6366f1; }
[data-bs-theme="dark"] .dvl-user-name { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .dvl-user-email-icon { background:var(--dark-border,#2a2a2d) !important; }

.dvl-pending-badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; color:#6b7280;
}
.dvl-pending-dot { width:14px; height:14px; border-radius:50%; background:#ef4444; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.dvl-pending-dot svg { width:8px; height:8px; color:#fff; }
[data-bs-theme="dark"] .dvl-pending-badge { color:var(--text-muted,#757575) !important; }

/* ── LOCATION CELL ──────────────────────────────────── */
.dvl-loc-name  { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
.dvl-loc-place { font-size:11.5px; color:#6b7280; margin-top:2px; }
[data-bs-theme="dark"] .dvl-loc-name  { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .dvl-loc-place { color:var(--text-muted,#757575) !important; }

/* ── UPDATED ON CELL ────────────────────────────────── */
.dvl-upd-date { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
.dvl-upd-time { font-size:11.5px; color:#6b7280; margin-top:2px; }
[data-bs-theme="dark"] .dvl-upd-date { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .dvl-upd-time { color:var(--text-muted,#757575) !important; }

/* ── ACTIONS CELL ───────────────────────────────────── */
.dvl-actions { display:flex; align-items:center; justify-content:center; gap:8px; }
.dvl-act-btn {
    width:28px; height:28px; border-radius:6px;
    border:none; background:none;
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; color:#9ca3af !important;
    transition:color .15s, background .15s;
    padding:0;
}
.dvl-act-btn:hover { color:#374151 !important; background:var(--app-bg,#f3f4f6); }
.dvl-act-btn svg { width:15px; height:15px; }
[data-bs-theme="dark"] .dvl-act-btn { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .dvl-act-btn:hover { color:var(--text-primary,#fff) !important; background:var(--dark-hover,#262626) !important; }

/* ── COLLAPSE MODE ──────────────────────────────────── */
.dvl-table.collapsed .dvl-row td { padding:10px 14px; }
.dvl-table.collapsed .dvl-row td:first-child { padding-left:12px; }
.dvl-table.collapsed .dvl-dev-brand,
.dvl-table.collapsed .dvl-dev-ip,
.dvl-table.collapsed .dvl-dev-type,
.dvl-table.collapsed .dvl-model-sub,
.dvl-table.collapsed .dvl-model-conn,
.dvl-table.collapsed .dvl-warranty-val,
.dvl-table.collapsed .dvl-upd-time,
.dvl-table.collapsed .dvl-loc-place,
.dvl-table.collapsed .dvl-pending-badge { display:none !important; }
.dvl-table.collapsed .dvl-dev-thumb { width:34px; height:28px; }
.dvl-table.collapsed .dvl-user-row   { margin-bottom:0; }

/* ── 5. PAGINATION ──────────────────────────────────── */
.dvl-pagination {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    padding        : 12px 16px;
    border-top     : 1px solid var(--app-border,#dee2e6);
    font-size      : 12.5px;
    color          : #6b7280;
}
[data-bs-theme="dark"] .dvl-pagination {
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}
.dvl-pag-btns { display:flex; align-items:center; gap:4px; }
.dvl-pag-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:6px 12px; border-radius:7px; font-size:12.5px;
    border:1px solid var(--app-border,#dee2e6);
    background:var(--app-surface,#fff); cursor:pointer;
    color:var(--app-text,#212529) !important; font-family:inherit;
    transition:background .15s;
}
.dvl-pag-btn:hover { background:var(--app-bg,#f8f9fa); }
.dvl-pag-btn.active {
    background:#ef4444 !important; border-color:#ef4444 !important;
    color:#fff !important; font-weight:600;
}
.dvl-pag-btn:disabled { opacity:.4; cursor:not-allowed; }
.dvl-pag-btn svg { width:13px; height:13px; }
[data-bs-theme="dark"] .dvl-pag-btn {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-secondary,#e5e7eb) !important;
}

/* ── 6. RESPONSIVE ──────────────────────────────────── */
.dvl-table-scroll { overflow-x:auto; }
@media (max-width:1199px) { .dvl-search input { width:120px; } }
@media (max-width:991px)  { .dvl-body { padding:10px; } }
@media (max-width:767px)  {
    .dvl-header { padding-right:.75rem !important; min-height:52px; }
    .dvl-page-title { font-size:14px; }
    .dvl-hdr-actions .dvl-icon-btn:nth-child(n+4) { display:none; }
    .dvl-body { padding:8px 8px 80px; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper dvl-header">
    <span class="dvl-page-title">Devices - {{ $total ?? '1456' }}</span>

    <div class="dvl-hdr-actions">
        {{-- Filter --}}
        <button class="dvl-filter-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filter
        </button>
        {{-- Refresh --}}
        <button class="dvl-icon-btn" title="Refresh">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
        </button>
        {{-- Merge --}}
        <button class="dvl-icon-btn" title="Merge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M13 6h3a2 2 0 0 1 2 2v7"/><line x1="6" y1="9" x2="6" y2="21"/></svg>
        </button>
        {{-- Download --}}
        <button class="dvl-icon-btn" title="Download">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </button>
        {{-- List view --}}
        <button class="dvl-icon-btn" title="List View">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </button>
        {{-- Export --}}
        <button class="dvl-icon-btn" title="Export">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </button>
        {{-- Send --}}
        <button class="dvl-icon-btn" title="Send">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
        {{-- Folder --}}
        <button class="dvl-icon-btn" title="Archive">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        </button>
        {{-- Bell --}}
        <button class="dvl-icon-btn" title="Alerts">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        {{-- Print --}}
        <button class="dvl-icon-btn" title="Print">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        </button>
        {{-- Divider --}}
        <div style="width:1px;height:24px;background:var(--app-border,#dee2e6);margin:0 2px;"></div>
        {{-- Add Device --}}
        <button class="dvl-add-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Device
        </button>
    </div>
</div>

{{-- ② BODY --}}
<main class="main-content">
<div class="dvl-body">
<div class="dvl-card">

    {{-- Toolbar --}}
    <div class="dvl-toolbar">
        <button class="dvl-show-sel">
            Show (10)
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="dvl-toolbar-right">
            <div class="dvl-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="Search..." id="dvlSearch" oninput="dvlFilter(this.value)">
            </div>
            <button class="dvl-refresh-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                Refresh
            </button>
            {{-- Expand / Collapse toggle --}}
            <button class="dvl-expand-btn" id="dvlExpandBtn" onclick="dvlToggleExpand()" title="Toggle expand/collapse">
                <svg id="dvlExpandIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                    <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="dvl-table-scroll">
    <table class="dvl-table" id="dvlTable">
        <thead class="dvl-thead">
            <tr>
                <th style="width:36px;"><input type="checkbox" class="dvl-cb" id="dvlCheckAll" onclick="dvlCheckAll(this)"></th>
                <th>
                    <span class="dvl-sort-icon">
                        Device
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Model
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Status
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Warranty
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Assigned To
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Location
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>
                    <span class="dvl-sort-icon">
                        Updated On
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="12 2 4 9 12 16"/><polyline points="12 8 20 15 12 22" opacity=".5"/></svg>
                    </span>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="dvlTbody">
            @php
            $devices = [
                ['671959/07/2025','Lenovo','모모모','192.168.29.201','Laptop','Latitude 7420','2VH67M398890','5CE42A15BF67','Via Network','Ready to Deploy','17 Mar 2025','Valid','Bharat Gupta','07 Oct 2025','Bengaluru','Stock Place','21 Jan 2026','10.34 AM'],
                ['671959/07/2025','Lenovo','모모모','192.168.29.201','Laptop','Latitude 7420','2VH67M398890','5CE42A15BF67','Via Network','Deployed',      '17 Mar 2025','Valid','Bharat Gupta','07 Oct 2025','Bengaluru','Stock Place','21 Jan 2026','10.34 AM'],
                ['671959/07/2025','Lenovo','모모모','192.168.29.201','Laptop','Latitude 7420','2VH67M398890','5CE42A15BF67','Via Network','Ready to Deploy','17 Mar 2025','Valid','Bharat Gupta','07 Oct 2025','Bengaluru','Stock Place','21 Jan 2026','10.34 AM'],
                ['671959/07/2025','Lenovo','모모모','192.168.29.201','Laptop','Latitude 7420','2VH67M398890','5CE42A15BF67','Via Network','Deployed',      '17 Mar 2025','Valid','Bharat Gupta','07 Oct 2025','Bengaluru','Stock Place','21 Jan 2026','10.34 AM'],
            ];
            @endphp
            @foreach($devices as $i => [$devId,$brand,$brandIcons,$ip,$type,$model,$serial,$mac,$conn,$status,$warDate,$warVal,$user,$assignDate,$city,$place,$updDate,$updTime])
            <tr class="dvl-row {{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}" data-search="{{ strtolower($devId.' '.$model.' '.$status.' '.$user) }}">
                <td><input type="checkbox" class="dvl-cb dvl-row-cb"></td>
                {{-- Device --}}
                <td>
                    <div class="dvl-dev-cell">
                        <div class="dvl-dev-thumb" style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.2"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M22 17H2"/><rect x="6" y="20" width="12" height="1" rx=".5"/><line x1="1" y1="20" x2="23" y2="20"/></svg>
                        </div>
                        <div>
                            <div class="dvl-dev-name">{{ $devId }}</div>
                            <div class="dvl-dev-brand">{{ $brand }} <span class="dvl-brand-icons">{{ $brandIcons }}</span></div>
                            <div class="dvl-dev-ip">{{ $ip }}</div>
                            <div class="dvl-dev-type">{{ $type }}</div>
                        </div>
                    </div>
                </td>
                {{-- Model --}}
                <td>
                    <div class="dvl-model-name">{{ $model }}</div>
                    <div class="dvl-model-sub">{{ $serial }}</div>
                    <div class="dvl-model-sub">{{ $mac }}</div>
                    <div class="dvl-model-conn">{{ $conn }}</div>
                </td>
                {{-- Status --}}
                <td>
                    <span class="dvl-badge {{ $status === 'Deployed' ? 'dvl-badge-deployed' : 'dvl-badge-deploy' }}">
                        {{ $status }}
                    </span>
                </td>
                {{-- Warranty --}}
                <td>
                    <div class="dvl-warranty-date">{{ $warDate }}</div>
                    <div class="dvl-warranty-val">{{ $warVal }}</div>
                </td>
                {{-- Assigned To --}}
                <td>
                    <div class="dvl-user-row">
                        <div class="dvl-user-av">{{ strtoupper(substr($user,0,2)) }}</div>
                        <span class="dvl-user-name">{{ $user }}</span>
                        <div class="dvl-user-email-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                    </div>
                    <div class="dvl-pending-badge">
                        <span>{{ $assignDate }}</span>
                        <span class="dvl-pending-dot">
                            <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>
                        </span>
                        <span>Pending</span>
                    </div>
                </td>
                {{-- Location --}}
                <td>
                    <div class="dvl-loc-name">{{ $city }}</div>
                    <div class="dvl-loc-place">{{ $place }}</div>
                </td>
                {{-- Updated On --}}
                <td>
                    <div class="dvl-upd-date">{{ $updDate }}</div>
                    <div class="dvl-upd-time">{{ $updTime }}</div>
                </td>
                {{-- Actions --}}
                <td>
                    <div class="dvl-actions">
                        <button class="dvl-act-btn" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="dvl-act-btn" title="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                        <button class="dvl-act-btn" title="More">
                            <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    {{-- Pagination --}}
    <div class="dvl-pagination">
        <span>Page 1 of 10</span>
        <div class="dvl-pag-btns">
            <button class="dvl-pag-btn" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Previous
            </button>
            <button class="dvl-pag-btn active">1</button>
            <button class="dvl-pag-btn">2</button>
            <button class="dvl-pag-btn">
                Next
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>

</div>{{-- /dvl-card --}}
</div>{{-- /dvl-body --}}
</main>

<script>
/* ── Expand / Collapse toggle ─────────────────────── */
var dvlExpanded = true;
function dvlToggleExpand() {
    dvlExpanded = !dvlExpanded;
    var tbl  = document.getElementById('dvlTable');
    var icon = document.getElementById('dvlExpandIcon');
    if (dvlExpanded) {
        tbl.classList.remove('collapsed');
        icon.innerHTML = '<polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>';
    } else {
        tbl.classList.add('collapsed');
        icon.innerHTML = '<polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="10" y1="14" x2="3" y2="21"/><line x1="21" y1="3" x2="14" y2="10"/>';
    }
}

/* ── Search filter ────────────────────────────────── */
function dvlFilter(q) {
    var rows = document.querySelectorAll('#dvlTbody .dvl-row');
    var lq   = q.toLowerCase();
    rows.forEach(function(r) {
        r.style.display = r.dataset.search.includes(lq) ? '' : 'none';
    });
}

/* ── Check all ────────────────────────────────────── */
function dvlCheckAll(cb) {
    document.querySelectorAll('.dvl-row-cb').forEach(function(c) { c.checked = cb.checked; });
}
</script>
@endsection