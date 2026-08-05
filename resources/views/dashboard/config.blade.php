{{-- @page-meta
{
  "page_no": "TCF-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Claude",
      "from": "2026-05",
      "reviewer": null,
      "description": "Ticket Configuration Page"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', 'Ticket Configuration')
@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   TICKET CONFIGURATION  –  tcf-*
   Bootstrap 5 + CSS vars. Zero hardcoded text/bg colours.
   ══════════════════════════════════════════════════════════ */

/* ── Section header ──────────────────────────────────── */
.tcf-header {
    background     : var(--app-surface, var(--bs-body-bg));
    border-bottom  : 1px solid var(--bs-border-color);
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 10px;
    padding-right  : 1.25rem !important;
}
.tcf-header-left { display:flex; align-items:center; gap:10px; }
.tcf-back-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:8px;
    background:none; border:1px solid var(--bs-border-color);
    cursor:pointer; transition:background .15s; flex-shrink:0;
    color:var(--bs-body-color) !important;
}
.tcf-back-btn:hover { background:var(--bs-tertiary-bg); }
.tcf-back-btn svg   { width:14px; height:14px; }
.tcf-page-title     { font-size:15px; font-weight:700; color:var(--bs-body-color); }

.tcf-print-btn {
    display:inline-flex; align-items:center; gap:5px;
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:5px 12px; font-size:12px; font-family:inherit;
    color:var(--bs-body-color) !important; cursor:pointer;
    transition:background .15s; white-space:nowrap; flex-shrink:0;
}
.tcf-print-btn:hover { background:var(--bs-tertiary-bg); }
.tcf-print-btn svg   { width:13px; height:13px; color:var(--bs-secondary-color); }

/* ── Page body ───────────────────────────────────────── */
.tcf-body { padding:0 0 calc(var(--footer-height,30px) + 20px); }

/* ── Tab navigation ──────────────────────────────────── */
.tcf-tabs {
    display        : flex;
    align-items    : center;
    gap            : 0;
    padding        : 0 16px;
    border-bottom  : 1px solid var(--bs-border-color);
    background     : var(--app-surface,var(--bs-body-bg));
    overflow-x     : auto;
    scrollbar-width: none;
}
.tcf-tabs::-webkit-scrollbar { display:none; }

.tcf-tab {
    display      : inline-flex;
    align-items  : center;
    gap          : 6px;
    padding      : 12px 16px;
    font-size    : 13px;
    font-weight  : 400;
    color        : var(--bs-secondary-color) !important;
    border        : none;
    border-bottom : 2px solid transparent;
    background    : none;
    cursor        : pointer;
    white-space   : nowrap;
    font-family   : inherit;
    transition    : color .18s, border-color .18s;
    margin-bottom : -1px;
}
.tcf-tab:hover { color:var(--bs-body-color) !important; }
.tcf-tab.active {
    color        : #ef4444 !important;
    border-bottom: 2px solid #ef4444;
    font-weight  : 500;
}

/* ── Tab panels ──────────────────────────────────────── */
.tcf-panel { display:none; padding:20px 16px; }
.tcf-panel.active { display:block; }

/* ── Section card (accordion wrapper) ───────────────── */
.tcf-section {
    background    : var(--app-surface,var(--bs-body-bg));
    border        : 1px solid var(--bs-border-color);
    border-radius : 10px;
    margin-bottom : 12px;
    overflow      : hidden;
}

/* Section title row (non-accordion) */
.tcf-section-title {
    font-size  : 14px;
    font-weight: 600;
    color      : var(--bs-body-color);
    padding    : 14px 16px 0;
    margin     : 0 0 4px;
}
.tcf-section-subtitle {
    font-size  : 12px;
    color      : var(--bs-secondary-color);
    padding    : 0 16px 14px;
    margin     : 0;
    border-bottom: 1px solid var(--bs-border-color);
}

/* ── Accordion ───────────────────────────────────────── */
/*
   Screenshot pattern:
   ┌──────────────────────────────────────────────────────┐
   │  Departments  ▶                                      │
   └──────────────────────────────────────────────────────┘
   - Title + right-pointing filled triangle (▶) when closed
   - Title + down-pointing filled triangle (▼) when open
   - No border, no chevron on right side
   - Padding matches card body left alignment
*/
.tcf-accordion-head {
    display    : flex;
    align-items: center;
    gap        : 8px;
    padding    : 14px 16px;
    cursor     : pointer;
    user-select: none;
    transition : background .15s;
    border-bottom: 1px solid transparent;
}
.tcf-accordion-head:hover { background:var(--bs-tertiary-bg); }
.tcf-accordion-head.open  { border-bottom-color:var(--bs-border-color); }

.tcf-accordion-title {
    font-size  : 14px;
    font-weight: 600;
    color      : var(--bs-body-color);
    display    : flex;
    align-items: center;
    gap        : 0;
    flex       : 1;
}

/* Arrow indicator — filled triangle, switches direction */
.tcf-acc-arrow {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    margin-left    : 8px;
    transition     : transform .2s ease;
    flex-shrink    : 0;
}
.tcf-acc-arrow svg { width:10px; height:10px; }
/* Closed = right-pointing ▶ */
.tcf-accordion-head:not(.open) .tcf-acc-arrow svg { fill:var(--bs-secondary-color); }
/* Open = rotated 90deg = down-pointing ▼ */
.tcf-accordion-head.open .tcf-acc-arrow { transform:rotate(90deg); }

.tcf-accordion-body { padding:0; }

/* Status badge — text only (no box), matching screenshot */
.tcf-badge {
    display    : inline-flex;
    align-items: center;
    font-size  : 12.5px;
    font-weight: 500;
    padding    : 0;
    background : none !important;
    border     : none !important;
}
.tcf-badge-green  { color:#16a34a !important; }
.tcf-badge-purple { color:#7c3aed !important; }
.tcf-badge-red    { color:#dc2626 !important; }
[data-bs-theme="dark"] .tcf-badge-green  { color:#4ade80 !important; }
[data-bs-theme="dark"] .tcf-badge-purple { color:#a78bfa !important; }
[data-bs-theme="dark"] .tcf-badge-red    { color:#f87171 !important; }

/* Table header — light purple/pink tint matching screenshot */
.tcf-table thead tr { background:rgba(139,92,246,.06); }
[data-bs-theme="dark"] .tcf-table thead tr { background:rgba(139,92,246,.12); }
.tcf-table th {
    font-size  : 12.5px;
    font-weight: 600;
    color      : var(--bs-body-color);
    padding    : 11px 16px;
    text-align : left;
    border-bottom: 1px solid var(--bs-border-color);
    white-space: nowrap;
}
/* Sort icon ↑↓ */
.tcf-sort-icon {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    margin-left    : 4px;
    vertical-align : middle;
    color          : var(--bs-secondary-color);
    opacity        : .7;
    cursor         : pointer;
    font-size      : 11px;
    line-height    : 1;
}

/* Table rows */
.tcf-table td {
    font-size    : 13px;
    color        : var(--bs-body-color);
    padding      : 12px 16px;
    border-bottom: 1px solid var(--bs-border-color);
    vertical-align: middle;
}
.tcf-table tr:last-child td { border-bottom:none; }
.tcf-table tbody tr:hover td { background:var(--bs-tertiary-bg); }

/* Table bar — Show(10) dropdown pill */
.tcf-show-pill {
    display    : inline-flex;
    align-items: center;
    gap        : 4px;
    border     : 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding    : 5px 10px;
    font-size  : 12.5px;
    color      : var(--bs-body-color);
    background : var(--app-surface,var(--bs-body-bg));
    cursor     : pointer;
    white-space: nowrap;
}
.tcf-show-pill select {
    border:none; outline:none; background:transparent;
    font-size:12.5px; font-family:inherit; color:var(--bs-body-color); cursor:pointer;
    appearance:none; -webkit-appearance:none;
}
.tcf-show-pill svg { width:11px; height:11px; color:var(--bs-secondary-color); }

/* ── Form section body ───────────────────────────────── */
.tcf-form-body { padding:16px; }

/* Form rows */
.tcf-form-row {
    display              : grid;
    grid-template-columns: repeat(3, 1fr);
    gap                  : 14px;
    margin-bottom        : 14px;
}
.tcf-form-row-2 { grid-template-columns: repeat(2, 1fr); }
.tcf-form-row-1 { grid-template-columns: 1fr; }

.tcf-form-group { display:flex; flex-direction:column; gap:5px; }
.tcf-label { font-size:12px; font-weight:500; color:var(--bs-secondary-color); }

.tcf-select, .tcf-input {
    width          : 100%;
    border         : 1px solid var(--bs-border-color);
    border-radius  : 6px;
    padding        : 7px 10px;
    font-family    : inherit;
    font-size      : 12.5px;
    color          : var(--bs-body-color);
    background     : var(--app-surface,var(--bs-body-bg));
    outline        : none;
    transition     : border-color .18s;
    box-sizing     : border-box;
}
.tcf-select {
    appearance         : none; -webkit-appearance:none; cursor:pointer;
    padding-right      : 28px;
    background-image   : url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat  : no-repeat;
    background-position: right 9px center;
}
.tcf-select:focus, .tcf-input:focus {
    border-color:var(--bs-primary,#0d6efd);
    box-shadow  :0 0 0 2px rgba(13,110,253,.12);
}

/* ── Toggle switch ───────────────────────────────────── */
.tcf-toggle-wrap { display:flex; align-items:center; gap:10px; }
.tcf-toggle {
    position:relative; width:40px; height:22px;
    display:inline-block; flex-shrink:0;
}
.tcf-toggle input { display:none; }
.tcf-toggle-slider {
    position:absolute; inset:0; border-radius:11px;
    background:#d1d5db; cursor:pointer;
    transition:background .2s;
}
[data-bs-theme="dark"] .tcf-toggle-slider { background:#4b5563; }
.tcf-toggle-slider::before {
    content:''; position:absolute;
    width:16px; height:16px; border-radius:50%;
    background:#fff; top:3px; left:3px;
    transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.3);
}
.tcf-toggle input:checked + .tcf-toggle-slider { background:#22c55e; }
.tcf-toggle input:checked + .tcf-toggle-slider::before { transform:translateX(18px); }
.tcf-toggle-label { font-size:12.5px; color:var(--bs-body-color); }

/* ── Checkbox group ──────────────────────────────────── */
.tcf-checkbox-row {
    display    : flex;
    align-items: center;
    gap        : 20px;
    flex-wrap  : wrap;
    margin-bottom: 10px;
}
.tcf-check-item {
    display    : flex;
    align-items: center;
    gap        : 6px;
    font-size  : 12.5px;
    color      : var(--bs-body-color);
    cursor     : pointer;
    white-space: nowrap;
}
.tcf-check-item input[type="checkbox"] {
    width:14px; height:14px; accent-color:#ef4444; cursor:pointer;
    flex-shrink:0;
}
.tcf-check-item input[type="checkbox"].blue { accent-color:#3b82f6; }

/* ── Work hours box ──────────────────────────────────── */
.tcf-workhours {
    border       : 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding      : 14px;
    display      : flex;
    gap          : 20px;
    align-items  : flex-start;
    flex-wrap    : wrap;
}
.tcf-workhours-left  { flex:1; min-width:200px; }
.tcf-workhours-right { flex:2; min-width:280px; }
.tcf-time-row { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
.tcf-time-group { display:flex; flex-direction:column; gap:4px; }
.tcf-time-label { font-size:12px; color:var(--bs-secondary-color); }
.tcf-time-input {
    width:100px; border:1px solid var(--bs-border-color); border-radius:6px;
    padding:6px 10px; font-size:12.5px; font-family:inherit;
    color:var(--bs-body-color); background:var(--app-surface,var(--bs-body-bg));
    outline:none; transition:border-color .18s;
}
.tcf-time-input:focus { border-color:var(--bs-primary,#0d6efd); }
.tcf-days-label { font-size:12px; font-weight:500; color:var(--bs-secondary-color); margin-bottom:8px; display:flex; align-items:center; gap:6px; }

/* ── Report fields grid ──────────────────────────────── */
.tcf-fields-grid {
    display              : grid;
    grid-template-columns: repeat(4, 1fr);
    gap                  : 8px 14px;
    padding              : 14px 16px;
}

/* ── Departments table ───────────────────────────────── */
.tcf-table-bar {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    padding        : 10px 16px;
    gap            : 10px;
    flex-wrap      : wrap;
    border-bottom  : 1px solid var(--bs-border-color);
}
.tcf-table-bar-left  { display:flex; align-items:center; gap:10px; flex-wrap:nowrap; }
.tcf-table-bar-right { display:flex; align-items:center; gap:8px; flex-wrap:nowrap; }

/* Select All pill — inline in toolbar, same height as show-pill */
.tcf-select-all-wrap {
    display      : inline-flex;
    align-items  : center;
    gap          : 7px;
    height       : 32px;
    padding      : 0 12px;
    border        : 1px solid var(--bs-border-color);
    border-radius : 8px;
    background    : var(--app-surface, var(--bs-body-bg));
    cursor        : pointer;
    user-select   : none;
    white-space   : nowrap;
    transition    : background .15s, border-color .15s;
}
.tcf-select-all-wrap:hover { background: var(--bs-tertiary-bg); }
.tcf-select-all-wrap span  { font-size:12.5px; color:var(--bs-body-color); }
.tcf-select-all-wrap.all-selected { border-color:#3b82f6; background:rgba(59,130,246,.07); }
.tcf-select-all-wrap.all-selected span { color:#2563eb !important; font-weight:500; }

/* Row selected highlight */
.dept-row.row-selected > td { background:rgba(59,130,246,.06) !important; }
[data-bs-theme="dark"] .dept-row.row-selected > td { background:rgba(59,130,246,.15) !important; }

.tcf-show-select {
    display:inline-flex; align-items:center; gap:6px;
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:4px 10px; font-size:12px; font-family:inherit;
    color:var(--bs-body-color); background:var(--app-surface,var(--bs-body-bg));
    cursor:pointer;
}
.tcf-show-select select {
    border:none; outline:none; background:transparent;
    font-size:12px; font-family:inherit; color:var(--bs-body-color); cursor:pointer;
}

.tcf-table-search {
    display:flex; align-items:center; gap:6px;
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:4px 10px; background:var(--app-surface,var(--bs-body-bg));
}
.tcf-table-search svg   { width:13px; height:13px; color:var(--bs-secondary-color); flex-shrink:0; }
.tcf-table-search input {
    border:none; outline:none; background:transparent;
    font-size:12px; font-family:inherit; color:var(--bs-body-color); width:150px;
}
.tcf-table-search input::placeholder { color:var(--bs-secondary-color); opacity:.6; }

.tcf-icon-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:8px;
    border:1px solid var(--bs-border-color); background:var(--app-surface,var(--bs-body-bg));
    cursor:pointer; transition:background .15s; color:var(--bs-secondary-color) !important;
}
.tcf-icon-btn:hover { background:var(--bs-tertiary-bg); }
.tcf-icon-btn svg { width:13px; height:13px; }

.tcf-add-btn {
    display:inline-flex; align-items:center; gap:5px;
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:4px 12px; font-size:12px; font-family:inherit;
    color:var(--bs-body-color) !important; background:var(--app-surface,var(--bs-body-bg));
    cursor:pointer; transition:background .15s; white-space:nowrap;
}
.tcf-add-btn:hover { background:var(--bs-tertiary-bg); }
.tcf-add-btn svg { width:12px; height:12px; color:var(--bs-secondary-color); }

.tcf-table { width:100%; border-collapse:collapse; }
.tcf-table th {
    font-size:12px; font-weight:600; color:var(--bs-secondary-color);
    padding:9px 16px; text-align:left; border-bottom:1px solid var(--bs-border-color);
    background:var(--bs-tertiary-bg,var(--app-bg)); white-space:nowrap;
}
.tcf-table th .tcf-sort-icon { width:12px; height:12px; vertical-align:middle; margin-left:3px; opacity:.5; }
.tcf-table td {
    font-size:12.5px; color:var(--bs-body-color); padding:10px 16px;
    border-bottom:1px solid var(--bs-border-color); vertical-align:middle;
}
.tcf-table tr:last-child td { border-bottom:none; }
.tcf-table tbody tr:hover td { background:var(--bs-tertiary-bg); }

/* Status badges */
.tcf-badge {
    display:inline-flex; align-items:center;
    font-size:11px; font-weight:500; padding:2px 10px; border-radius:4px;
}
.tcf-badge-green { background:#f0fdf4; color:#16a34a !important; }
.tcf-badge-red   { background:#fef2f2; color:#dc2626 !important; }
[data-bs-theme="dark"] .tcf-badge-green { background:#052e16; color:#4ade80 !important; }
[data-bs-theme="dark"] .tcf-badge-red   { background:#2d0f0e; color:#f87171 !important; }

/* Table pagination */
.tcf-table-footer {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 16px; border-top:1px solid var(--bs-border-color); flex-wrap:wrap; gap:8px;
}
.tcf-table-info { font-size:12px; color:var(--bs-secondary-color); }
.tcf-pagination { display:flex; align-items:center; gap:4px; }
.tcf-pg-btn {
    display:inline-flex; align-items:center; justify-content:center;
    height:28px; min-width:28px; border:1px solid var(--bs-border-color); border-radius:6px;
    background:var(--app-surface,var(--bs-body-bg)); color:var(--bs-body-color) !important;
    font-size:12px; font-family:inherit; cursor:pointer; padding:0 8px;
    transition:background .15s; gap:3px; white-space:nowrap;
}
.tcf-pg-btn:hover   { background:var(--bs-tertiary-bg); }
.tcf-pg-btn.active  { background:#2563eb; color:#fff !important; border-color:#2563eb; }
.tcf-pg-btn svg     { width:12px; height:12px; }

/* ── User Info split layout ──────────────────────────── */
.tcf-user-split {
    display:grid; grid-template-columns:260px 1fr; min-height:400px;
}
.tcf-user-list {
    border-right:1px solid var(--bs-border-color);
    display:flex; flex-direction:column;
}
.tcf-user-list-head {
    padding:10px 12px; border-bottom:1px solid var(--bs-border-color);
    font-size:12px; font-weight:600; color:var(--bs-body-color);
    display:flex; align-items:center; justify-content:space-between;
}
.tcf-user-list-head .tcf-user-count { font-size:11px; color:var(--bs-secondary-color); font-weight:400; }
.tcf-user-search {
    padding:8px 12px; border-bottom:1px solid var(--bs-border-color);
}
.tcf-user-search-inner {
    display:flex; align-items:center; gap:5px;
    border:1px solid var(--bs-border-color); border-radius:6px;
    padding:4px 8px; background:var(--app-surface,var(--bs-body-bg));
}
.tcf-user-search-inner svg   { width:12px; height:12px; color:var(--bs-secondary-color); flex-shrink:0; }
.tcf-user-search-inner input {
    border:none; outline:none; background:transparent;
    font-size:12px; font-family:inherit; color:var(--bs-body-color); width:100%;
}
.tcf-user-search-inner input::placeholder { color:var(--bs-secondary-color); opacity:.6; }

.tcf-user-items { overflow-y:auto; flex:1; max-height:350px; }
.tcf-user-item {
    display:flex; align-items:center; gap:8px;
    padding:8px 12px; cursor:pointer; transition:background .15s;
    border-bottom:1px solid var(--bs-border-color);
}
.tcf-user-item:hover { background:var(--bs-tertiary-bg); }
.tcf-user-item.active { background:#050b3c; }
.tcf-user-item.active .tcf-user-name,
.tcf-user-item.active .tcf-user-email { color:#fff !important; }
.tcf-user-av {
    width:32px; height:32px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:700; color:#fff !important;
    background:linear-gradient(135deg,#6366f1,#4338ca);
}
.tcf-user-name  { font-size:12.5px; font-weight:500; color:var(--bs-body-color); }
.tcf-user-email { font-size:11px; color:var(--bs-secondary-color); }

/* User detail panel */
.tcf-user-detail { padding:14px 16px; overflow-y:auto; }
.tcf-user-detail-head {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:12px; flex-wrap:wrap; gap:8px;
}
.tcf-selected-user { font-size:13px; font-weight:600; color:var(--bs-body-color); }
.tcf-detail-actions { display:flex; align-items:center; gap:6px; }
.tcf-export-btn {
    display:inline-flex; align-items:center; gap:4px;
    border:1px solid var(--bs-border-color); border-radius:6px;
    padding:4px 10px; font-size:11.5px; font-family:inherit;
    color:var(--bs-body-color) !important; background:var(--app-surface,var(--bs-body-bg));
    cursor:pointer; transition:background .15s;
}
.tcf-export-btn:hover { background:var(--bs-tertiary-bg); }
.tcf-export-btn svg   { width:12px; height:12px; }

.tcf-priv-search {
    display:flex; align-items:center; gap:5px;
    border:1px solid var(--bs-border-color); border-radius:6px;
    padding:4px 8px; background:var(--app-surface,var(--bs-body-bg));
}
.tcf-priv-search svg   { width:12px; height:12px; color:var(--bs-secondary-color); flex-shrink:0; }
.tcf-priv-search input {
    border:none; outline:none; background:transparent;
    font-size:12px; font-family:inherit; color:var(--bs-body-color); width:120px;
}

.tcf-detail-section { margin-bottom:14px; }
.tcf-detail-section-title { font-size:12.5px; font-weight:600; color:var(--bs-body-color); margin-bottom:8px; }
.tcf-perm-grid {
    display:grid; grid-template-columns:repeat(3,1fr); gap:6px 10px;
}
.tcf-perm-item {
    display:flex; align-items:center; gap:6px;
    font-size:12px; color:var(--bs-body-color); cursor:pointer; white-space:nowrap;
}
.tcf-perm-item input { width:13px; height:13px; accent-color:#3b82f6; cursor:pointer; flex-shrink:0; }

/* ══════════════════════════════════════════════════════════
   DARK MODE OVERRIDES
   modetheme.css forces color on all elements — we fight back
   with specific scoped selectors using higher specificity.
   ══════════════════════════════════════════════════════════ */

/* ── Inputs & selects in dark ─────────────────────────── */
[data-bs-theme="dark"] .tcf-select,
[data-bs-theme="dark"] .tcf-input,
[data-bs-theme="dark"] .tcf-time-input,
[data-bs-theme="dark"] .tcf-show-pill select,
[data-bs-theme="dark"] .tcf-table-search input,
[data-bs-theme="dark"] .tcf-user-search-inner input,
[data-bs-theme="dark"] .tcf-priv-search input {
    background : var(--dark-primary, #191919) !important;
    color      : var(--text-primary, #fff) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-select option {
    background : var(--dark-secondary, #2a2a2a);
    color      : var(--text-primary, #fff);
}

/* ── Cards / sections ─────────────────────────────────── */
[data-bs-theme="dark"] .tcf-section {
    background : var(--dark-primary, #191919) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}

/* ── Table ────────────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-table thead tr {
    background: rgba(139,92,246,.15) !important;
}
[data-bs-theme="dark"] .tcf-table th {
    color       : var(--text-primary, #fff) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-table td {
    color       : var(--text-secondary, #e5e7eb) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-table tbody tr:hover td {
    background: var(--dark-hover, #262626) !important;
}

/* ── Toolbar elements ─────────────────────────────────── */
[data-bs-theme="dark"] .tcf-show-pill,
[data-bs-theme="dark"] .tcf-table-search,
[data-bs-theme="dark"] .tcf-icon-btn,
[data-bs-theme="dark"] .tcf-add-btn,
[data-bs-theme="dark"] .tcf-print-btn,
[data-bs-theme="dark"] .tcf-pg-btn,
[data-bs-theme="dark"] .tcf-export-btn {
    background : var(--dark-primary, #191919) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
    color      : var(--text-primary, #fff) !important;
}
[data-bs-theme="dark"] .tcf-icon-btn svg,
[data-bs-theme="dark"] .tcf-add-btn svg,
[data-bs-theme="dark"] .tcf-print-btn svg,
[data-bs-theme="dark"] .tcf-export-btn svg {
    color: var(--text-muted, #757575) !important;
}
[data-bs-theme="dark"] .tcf-icon-btn:hover,
[data-bs-theme="dark"] .tcf-add-btn:hover,
[data-bs-theme="dark"] .tcf-print-btn:hover,
[data-bs-theme="dark"] .tcf-pg-btn:hover {
    background: var(--dark-hover, #262626) !important;
}
[data-bs-theme="dark"] .tcf-pg-btn.active {
    background : #2563eb !important;
    color      : #fff !important;
    border-color: #2563eb !important;
}
[data-bs-theme="dark"] .tcf-show-pill,
[data-bs-theme="dark"] .tcf-show-pill select {
    color: var(--text-primary, #fff) !important;
}

/* ── Tabs ─────────────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-tabs {
    background  : var(--dark-primary, #191919) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-tab {
    color: var(--text-muted, #757575) !important;
}
[data-bs-theme="dark"] .tcf-tab:hover { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .tcf-tab.active { color:#ef4444 !important; }

/* ── Section header ───────────────────────────────────── */
[data-bs-theme="dark"] .tcf-header {
    background  : var(--dark-primary, #191919) !important;
    border-color: var(--dark-border, #2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-back-btn {
    background  : transparent !important;
    border-color: var(--dark-border, #2a2a2d) !important;
    color       : var(--text-primary, #fff) !important;
}
[data-bs-theme="dark"] .tcf-back-btn:hover { background: var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .tcf-page-title { color:var(--text-primary,#fff) !important; }

/* ── Accordion ────────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-accordion-head:hover { background:var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .tcf-accordion-head.open  { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .tcf-accordion-title      { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .tcf-acc-arrow svg        { fill:var(--text-muted,#757575) !important; }

/* ── Section title/subtitle ───────────────────────────── */
[data-bs-theme="dark"] .tcf-section-title   { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .tcf-section-subtitle{
    color       : var(--text-muted,#757575) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* ── Form labels ──────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-label { color:var(--text-muted,#757575) !important; }

/* ── Checkboxes & check items ─────────────────────────── */
[data-bs-theme="dark"] .tcf-check-item { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .tcf-perm-item  { color:var(--text-secondary,#e5e7eb) !important; }

/* ── Work hours box ───────────────────────────────────── */
[data-bs-theme="dark"] .tcf-workhours  { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .tcf-time-label { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .tcf-days-label { color:var(--text-muted,#757575) !important; }

/* ── User split panel ─────────────────────────────────── */
[data-bs-theme="dark"] .tcf-user-list { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .tcf-user-list-head {
    color       : var(--text-primary,#fff) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-user-count  { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .tcf-user-search { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .tcf-user-search-inner {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-user-item {
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-user-item:hover  { background:var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .tcf-user-item.active { background:#050b3c !important; }
[data-bs-theme="dark"] .tcf-user-name        { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .tcf-user-email       { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .tcf-user-item.active .tcf-user-name,
[data-bs-theme="dark"] .tcf-user-item.active .tcf-user-email { color:#fff !important; }
[data-bs-theme="dark"] .tcf-detail-section-title { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .tcf-selected-user        { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .tcf-priv-search {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* ── Table footer & info ──────────────────────────────── */
[data-bs-theme="dark"] .tcf-table-footer {
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-table-info { color:var(--text-muted,#757575) !important; }

/* ── Select All row (header above table bar) ──────────── */
[data-bs-theme="dark"] .tcf-table-bar {
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-table-search input { color:var(--text-primary,#fff) !important; }

/* ── Fields grid ──────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-fields-grid .tcf-check-item { color:var(--text-secondary,#e5e7eb) !important; }

/* ── Sort icon ────────────────────────────────────────── */
[data-bs-theme="dark"] .tcf-sort-icon { color:var(--text-muted,#757575) !important; }

/* ── Dark mode: select-all-wrap ───────────────────────── */
[data-bs-theme="dark"] .tcf-select-all-wrap {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .tcf-select-all-wrap span { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .tcf-select-all-wrap:hover { background:var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .tcf-select-all-wrap.all-selected span { color:#93c5fd !important; }

/* ── Dark mode: show pill ─────────────────────────────── */
[data-bs-theme="dark"] .tcf-show-pill {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-primary,#fff) !important;
}
[data-bs-theme="dark"] .tcf-show-pill select {
    background: transparent !important;
    color     : var(--text-primary,#fff) !important;
}
[data-bs-theme="dark"] .tcf-show-pill svg { color:var(--text-muted,#757575) !important; }

/* ══════════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════════ */
@media (max-width:991px) {
    .tcf-form-row   { grid-template-columns:1fr 1fr; }
    .tcf-fields-grid{ grid-template-columns:repeat(3,1fr); }
    .tcf-user-split { grid-template-columns:220px 1fr; }
}
@media (max-width:767px) {
    .tcf-header { padding-right:.75rem !important; }
    .tcf-page-title { font-size:14px; }
    .tcf-form-row   { grid-template-columns:1fr; gap:10px; }
    .tcf-fields-grid{ grid-template-columns:repeat(2,1fr); }
    .tcf-user-split { grid-template-columns:1fr; }
    .tcf-user-list  { border-right:none; border-bottom:1px solid var(--bs-border-color); }
    .tcf-user-items { max-height:220px; }
    .tcf-panel      { padding:12px; }
    .tcf-accordion-body { padding:12px; }
    .tcf-workhours  { flex-direction:column; gap:12px; }
    .tcf-table-bar  { flex-direction:column; align-items:flex-start; }
    .tcf-perm-grid  { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:480px) {
    .tcf-fields-grid{ grid-template-columns:1fr 1fr; }
    .tcf-checkbox-row{ gap:12px; }
    .tcf-perm-grid  { grid-template-columns:1fr; }
}
</style>

{{-- ══════════════════════════════════════════════════
     ① SECTION HEADER
     ══════════════════════════════════════════════════ --}}
<div class="header-actions-wrapper tcf-header">
    <div class="tcf-header-left">
        <a href="{{ url()->previous() }}" class="tcf-back-btn" title="Back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        <span class="tcf-page-title">Ticket Configuration</span>
    </div>
    <button class="tcf-print-btn" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
        Print Eula
    </button>
</div>

{{-- ══════════════════════════════════════════════════
     ② MAIN CONTENT
     ══════════════════════════════════════════════════ --}}
<main class="main-content">
<div class="tcf-body">

    {{-- ── Tab Navigation ── --}}
    <div class="tcf-tabs" role="tablist">
        @foreach([
            ['general',       'General Settings'],
            ['notification',  'Notification Settings'],
            ['auto-updates',  'Auto Updates'],
            ['sla',           'Custom SLA Configuration'],
            ['email',         'Email to Ticket'],
        ] as [$id, $label])
        <button class="tcf-tab {{ $id === 'general' ? 'active' : '' }}"
                role="tab"
                data-tab="{{ $id }}"
                onclick="tcfSwitchTab('{{ $id }}', this)">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ════════════════════════════════════════════════
         GENERAL SETTINGS TAB
         ════════════════════════════════════════════════ --}}
    <div class="tcf-panel active" id="tab-general">

        {{-- ── Section: General ── --}}
        <div class="tcf-section">
            <h6 class="tcf-section-title">General</h6>
            <p class="tcf-section-subtitle">Configure basic ticketing behavior and system preferences.</p>

            <div class="tcf-form-body">

                {{-- Row: Hide Fields + VIP Toggle --}}
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <div class="tcf-label mb-2">Hide Fields For User View</div>
                        <div class="tcf-checkbox-row">
                            @foreach(['Priority','Assigned To','Expire At','TAT'] as $field)
                            <label class="tcf-check-item">
                                <input type="checkbox" class="blue" {{ in_array($field,['Priority','TAT']) ? 'checked' : '' }}>
                                {{ $field }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div class="tcf-label mb-2">Enable VIP Ticket Listing</div>
                        <div class="tcf-toggle-wrap">
                            <label class="tcf-toggle">
                                <input type="checkbox" checked>
                                <span class="tcf-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Row 1: 3-col selects --}}
                <div class="tcf-form-row mb-3">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Mail Notification</label>
                        <select class="tcf-select">
                            <option selected>Ticket Status Changes</option>
                            <option>All Notifications</option>
                            <option>None</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">TAT By Work Hour</label>
                        <select class="tcf-select">
                            <option selected>Yes</option>
                            <option>No</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Departments Access</label>
                        <select class="tcf-select">
                            <option selected>No</option>
                            <option>Yes</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2: 3-col selects --}}
                <div class="tcf-form-row mb-3">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Knowledge Document Auto Suggestion</label>
                        <select class="tcf-select">
                            <option selected>Disable</option>
                            <option>Enable</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Auto archive ticket after Days</label>
                        <select class="tcf-select">
                            <option selected>2</option>
                            <option>7</option>
                            <option>14</option>
                            <option>30</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">CC Email Reminder</label>
                        <select class="tcf-select">
                            <option selected>Un Checked</option>
                            <option>Checked</option>
                        </select>
                    </div>
                </div>

                {{-- Row 3 --}}
                <div class="tcf-form-row mb-3">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Auto archive user activity after Days:</label>
                        <select class="tcf-select">
                            <option selected>Disable</option>
                            <option>7</option>
                            <option>30</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Ticket ID Initials:</label>
                        <select class="tcf-select">
                            <option selected>2</option>
                            <option>3</option>
                            <option>4</option>
                        </select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Ticket ID Initial Separator:</label>
                        <select class="tcf-select">
                            <option selected>Un Checked</option>
                            <option>Checked</option>
                        </select>
                    </div>
                </div>

                {{-- Work Hours --}}
                <div class="tcf-workhours">
                    <div class="tcf-workhours-left">
                        <div class="tcf-label mb-2">Work Hours</div>
                        <div class="tcf-time-row">
                            <div class="tcf-time-group">
                                <span class="tcf-time-label">Start Time:</span>
                                <input type="text" class="tcf-time-input" value="9:00 AM">
                            </div>
                            <div class="tcf-time-group">
                                <span class="tcf-time-label">End Time:</span>
                                <input type="text" class="tcf-time-input" value="9:00 AM">
                            </div>
                        </div>
                    </div>
                    <div class="tcf-workhours-right">
                        <div class="tcf-days-label">
                            <input type="checkbox" style="accent-color:#3b82f6;width:13px;height:13px;">
                            Work Days
                        </div>
                        <div class="tcf-checkbox-row" style="gap:10px;">
                            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                            <label class="tcf-check-item">
                                <input type="checkbox" class="blue"
                                    {{ in_array($day,['Sunday','Wednesday','Thursday','Friday','Saturday']) ? 'checked' : '' }}>
                                {{ $day }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>{{-- /form-body --}}
        </div>{{-- /general section --}}

        {{-- ── Accordion: Report Fields ── --}}
        <div class="tcf-section">
            <div class="tcf-accordion-head open" onclick="tcfToggleAccordion(this)">
                <span class="tcf-accordion-title">
                    Report Fields
                    <span class="tcf-acc-arrow">
                        <svg viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="2,1 9,5 2,9" fill="currentColor"/>
                        </svg>
                    </span>
                </span>
            </div>
            <div class="tcf-accordion-body" id="body-report">

                <div class="mb-2 ps-1">
                    <label class="tcf-check-item">
                        <input type="checkbox" class="blue" id="selectAllFields" onchange="tcfSelectAll(this,'field-check')">
                        <span style="font-size:12px;font-weight:500;">Select All</span>
                    </label>
                </div>

                <div class="tcf-fields-grid">
                    @foreach([
                        'Ticket ID','Subject','Status','Ticket Resolved At',
                        'Ticket Attender','Ticket Created At','Last Updated At','Tags',
                        'Created Via','Device','Feedback','Priority',
                        'Incomplete Tasks','Percentage of Completed Task','Problem Category','Ticket Creator',
                        'Total Tasks','Department','Sub Category','Completed Tasks',
                    ] as $field)
                    <label class="tcf-check-item field-check">
                        <input type="checkbox" class="blue"
                            {{ in_array($field,['Ticket Created At','Percentage of Completed Task']) ? 'checked' : '' }}>
                        {{ $field }}
                    </label>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ── Accordion: Departments ── --}}
        <div class="tcf-section">
            <div class="tcf-accordion-head open" onclick="tcfToggleAccordion(this)">
                <span class="tcf-accordion-title">
                    Departments
                    <span class="tcf-acc-arrow">
                        <svg viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="2,1 9,5 2,9" fill="currentColor"/>
                        </svg>
                    </span>
                </span>
            </div>
            <div id="body-departments">

                {{-- Table toolbar: Select All + Show(10) left | Search + refresh + toggles + Add right --}}
                <div class="tcf-table-bar">
                    <div class="tcf-table-bar-left">
                        {{-- Select All inline with toolbar --}}
                        <label class="tcf-select-all-wrap" id="deptSelectAllWrap">
                            <input type="checkbox" id="deptSelectAll" style="width:14px;height:14px;accent-color:#3b82f6;cursor:pointer;flex-shrink:0;">
                            <span style="font-size:12.5px;color:var(--bs-body-color);white-space:nowrap;">Select All</span>
                        </label>
                        <div class="tcf-show-pill">
                            Show
                            <select id="deptShowCount">
                                <option>(10)</option>
                                <option>(25)</option>
                                <option>(50)</option>
                            </select>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="tcf-table-bar-right">
                        <div class="tcf-table-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input type="text" placeholder="Search Departments" oninput="deptSearch(this.value)">
                        </div>
                        <button class="tcf-icon-btn" title="Refresh" onclick="location.reload()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                            </svg>
                        </button>
                        <label class="tcf-toggle" style="width:38px;height:22px;" title="Toggle 1">
                            <input type="checkbox" checked>
                            <span class="tcf-toggle-slider"></span>
                        </label>
                        <label class="tcf-toggle" style="width:38px;height:22px;" title="Toggle 2">
                            <input type="checkbox">
                            <span class="tcf-toggle-slider"></span>
                        </label>
                        <button class="tcf-add-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Department
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div style="overflow-x:auto;">
                    <table class="tcf-table" id="deptTable">
                        <thead>
                            <tr>
                                <th style="width:42px;"></th>
                                <th>Departments <span class="tcf-sort-icon">↑↓</span></th>
                                <th>Status <span class="tcf-sort-icon">↑↓</span></th>
                                <th>Email Account <span class="tcf-sort-icon">↑↓</span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="deptTableBody">
                            @foreach([
                                ['UX Design',   'Enabled',  'ananths@greenitco.com',    true],
                                ['Development', 'Disabled', 'jenish@manageengine.com',  false],
                                ['UX Design',   'Enabled',  'ananths@greenitco.com',    true],
                                ['UX Design',   'Enabled',  'ananths@greenitco.com',    true],
                                ['UX Design',   'Enabled',  'ananths@greenitco.com',    true],
                                ['UX Design',   'Enabled',  'ananths@greenitco.com',    true],
                            ] as [$dept, $status, $email, $enabled])
                            <tr class="dept-row">
                                <td>
                                    <input type="checkbox" class="dept-row-check" style="width:14px;height:14px;accent-color:#3b82f6;cursor:pointer;">
                                </td>
                                <td class="fw-medium">{{ $dept }}</td>
                                <td>
                                    <span class="tcf-badge {{ $status === 'Enabled' ? 'tcf-badge-green' : 'tcf-badge-purple' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td style="color:var(--bs-secondary-color);font-size:12.5px;">{{ $email }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="tcf-toggle dept-row-toggle" style="width:38px;height:22px;">
                                            <input type="checkbox" {{ $enabled ? 'checked' : '' }}>
                                            <span class="tcf-toggle-slider"></span>
                                        </label>
                                        <span class="dept-toggle-label" style="font-size:12.5px;color:var(--bs-body-color);">
                                            {{ $enabled ? 'Disable' : 'Enable' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Table footer: Page x of y | < Previous 1 2 Next > --}}
                <div class="tcf-table-footer">
                    <span class="tcf-table-info">Page 1 of 10</span>
                    <div class="tcf-pagination">
                        <button class="tcf-pg-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width:12px;height:12px;">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                            Previous
                        </button>
                        <button class="tcf-pg-btn active">1</button>
                        <button class="tcf-pg-btn">2</button>
                        <button class="tcf-pg-btn">
                            Next
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width:12px;height:12px;">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>{{-- /body-departments --}}
        </div>{{-- /departments accordion --}}

        {{-- ── Accordion: Ticket Handler Limits ── --}}
        <div class="tcf-section">
            <div class="tcf-accordion-head open" onclick="tcfToggleAccordion(this)">
                <span class="tcf-accordion-title">
                    Ticket Handler Limits
                    <span class="tcf-acc-arrow">
                        <svg viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="2,1 9,5 2,9" fill="currentColor"/>
                        </svg>
                    </span>
                </span>
            </div>
            <div id="body-handler">

                <div class="tcf-user-split">

                    {{-- User list --}}
                    <div class="tcf-user-list">
                        <div class="tcf-user-list-head">
                            User Info
                            <span class="tcf-user-count">Showing 1-8 of 355 entries</span>
                        </div>
                        <div class="tcf-user-search">
                            <div class="tcf-user-search-inner">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                </svg>
                                <input type="text" placeholder="Search User">
                            </div>
                        </div>
                        <div class="tcf-user-items">
                            @foreach([
                                ['Virat Kohli',  'virat.kohli',  'VK', 'av-t'],
                                ['Ananth Se',    'ananth.ux',    'AS', 'av-i', true],
                                ['Rohit Sharma', 'rohit.sharma', 'RS', 'av-r'],
                                ['Jenish Roy',   'jenish.roy',   'JR', 'av-o'],
                                ['Jenish Roy',   'jenish.roy',   'JR', 'av-t'],
                                ['Jenish Roy',   'jenish.roy',   'JR', 'av-i'],
                                ['Glen Manwell', 'glen.maz',     'GM', 'av-o'],
                                ['Glen Manwell', 'glen.maz',     'GM', 'av-t'],
                            ] as $u)
                            <div class="tcf-user-item {{ isset($u[4]) && $u[4] ? 'active' : '' }}"
                                 onclick="tcfSelectUser(this,'{{ $u[0] }}')">
                                <div class="tcf-user-av {{ $u[3] }}">{{ $u[2] }}</div>
                                <div>
                                    <div class="tcf-user-name">{{ $u[0] }}</div>
                                    <div class="tcf-user-email">{{ $u[1] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        {{-- List pagination --}}
                        <div style="padding:8px 12px;border-top:1px solid var(--bs-border-color);display:flex;align-items:center;justify-content:space-between;">
                            <div class="tcf-pagination">
                                <button class="tcf-pg-btn" style="height:24px;min-width:24px;font-size:11px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                                    Previous
                                </button>
                                <button class="tcf-pg-btn active" style="height:24px;min-width:24px;font-size:11px;">1</button>
                                <button class="tcf-pg-btn" style="height:24px;min-width:24px;font-size:11px;">2</button>
                                <button class="tcf-pg-btn" style="height:24px;min-width:24px;font-size:11px;">
                                    Next
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- User detail --}}
                    <div class="tcf-user-detail">
                        <div class="tcf-user-detail-head">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <label class="tcf-check-item">
                                    <input type="checkbox" class="blue">
                                    <span style="font-size:12px;font-weight:500;">Select All</span>
                                </label>
                                <div class="tcf-priv-search">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <input type="text" placeholder="Search Privileges">
                                </div>
                            </div>
                            <div class="tcf-detail-actions">
                                <button class="tcf-icon-btn" title="Refresh">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                                    </svg>
                                </button>
                                <button class="tcf-export-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    Export User
                                </button>
                            </div>
                        </div>

                        <div class="tcf-selected-user mb-3">Selected User: anths.selvaraj</div>

                        {{-- Departments permissions --}}
                        <div class="tcf-detail-section">
                            <div class="tcf-detail-section-title">Departments</div>
                            <div class="tcf-perm-grid">
                                @foreach([
                                    ['Checklist',           false],
                                    ['Dev Department',      false],
                                    ['Development',         false],
                                    ['Finance',             false],
                                    ['Product Growth Team', true],
                                    ['IT Support',          false],
                                    ['Product Design - UIUX',false],
                                    ['To be Assigned - Sales',false],
                                    ['QA-SDET',             false],
                                    ['QA-SDET-DELHI',       false],
                                    ['Developement',        true],
                                    ['testing123',          false],
                                    ['To be Assigned',      false],
                                    ['Promp Engineer',      false],
                                    ['Design',              false],
                                ] as [$perm, $checked])
                                <label class="tcf-perm-item">
                                    <input type="checkbox" {{ $checked ? 'checked' : '' }}>
                                    {{ $perm }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="tcf-detail-section">
                            <div class="tcf-detail-section-title">Action Controls</div>
                            <div class="tcf-perm-grid">
                                @foreach([
                                    ['Checklist',              false],
                                    ['Dev Department',         false],
                                    ['Development',            false],
                                    ['Finance',                false],
                                    ['Product Growth Team',    true],
                                    ['IT Support',             false],
                                    ['Product Design - UIUX',  false],
                                    ['To be Assigned - Sales',  false],
                                    ['QA-SDET',                false],
                                    ['QA-SDET-DELHI',          false],
                                    ['Developement',           true],
                                    ['testing123',             false],
                                ] as [$perm, $checked])
                                <label class="tcf-perm-item">
                                    <input type="checkbox" {{ $checked ? 'checked' : '' }}>
                                    {{ $perm }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="tcf-detail-section">
                            <div class="tcf-detail-section-title">Ticket Creation</div>
                            <label class="tcf-perm-item">
                                <input type="checkbox">
                                Allow Ticket Create For Others
                            </label>
                        </div>

                        <div class="tcf-detail-section">
                            <div class="tcf-detail-section-title">Ticket Merge</div>
                            <label class="tcf-perm-item">
                                <input type="checkbox">
                                Allow to merge Tickets
                            </label>
                        </div>

                    </div>{{-- /user-detail --}}
                </div>{{-- /user-split --}}

            </div>{{-- /body-handler --}}
        </div>{{-- /handler accordion --}}

    </div>{{-- /tab-general --}}

    {{-- ════════════════════════════════════════════════
         NOTIFICATION SETTINGS TAB
         ════════════════════════════════════════════════ --}}
    <div class="tcf-panel" id="tab-notification">
        <div class="tcf-section">
            <h6 class="tcf-section-title">Notification Settings</h6>
            <p class="tcf-section-subtitle">Configure email and push notification preferences.</p>
            <div class="tcf-form-body">
                <div class="tcf-form-row">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Notify on Ticket Create</label>
                        <select class="tcf-select"><option>Yes</option><option>No</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Notify on Status Change</label>
                        <select class="tcf-select"><option>Yes</option><option>No</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Notify on Assignment</label>
                        <select class="tcf-select"><option>Yes</option><option>No</option></select>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="tcf-toggle-wrap">
                        <label class="tcf-toggle"><input type="checkbox" checked><span class="tcf-toggle-slider"></span></label>
                        <span class="tcf-toggle-label">Email Notifications</span>
                    </div>
                    <div class="tcf-toggle-wrap">
                        <label class="tcf-toggle"><input type="checkbox"><span class="tcf-toggle-slider"></span></label>
                        <span class="tcf-toggle-label">Push Notifications</span>
                    </div>
                    <div class="tcf-toggle-wrap">
                        <label class="tcf-toggle"><input type="checkbox" checked><span class="tcf-toggle-slider"></span></label>
                        <span class="tcf-toggle-label">SMS Notifications</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AUTO UPDATES TAB --}}
    <div class="tcf-panel" id="tab-auto-updates">
        <div class="tcf-section">
            <h6 class="tcf-section-title">Auto Updates</h6>
            <p class="tcf-section-subtitle">Configure automatic update rules for tickets.</p>
            <div class="tcf-form-body">
                <div class="tcf-form-row">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Auto Close After (Days)</label>
                        <select class="tcf-select"><option>7</option><option>14</option><option>30</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Auto Escalation</label>
                        <select class="tcf-select"><option>Enable</option><option>Disable</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Auto Assign Rule</label>
                        <select class="tcf-select"><option>Round Robin</option><option>Load Balanced</option></select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SLA CONFIG TAB --}}
    <div class="tcf-panel" id="tab-sla">
        <div class="tcf-section">
            <h6 class="tcf-section-title">Custom SLA Configuration</h6>
            <p class="tcf-section-subtitle">Define service level agreements per priority and department.</p>
            <div class="tcf-form-body">
                <div class="tcf-form-row">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Critical SLA (hours)</label>
                        <input type="number" class="tcf-input" value="2">
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">High SLA (hours)</label>
                        <input type="number" class="tcf-input" value="8">
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Medium SLA (hours)</label>
                        <input type="number" class="tcf-input" value="24">
                    </div>
                </div>
                <div class="tcf-form-row">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Low SLA (hours)</label>
                        <input type="number" class="tcf-input" value="72">
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Breach Notification</label>
                        <select class="tcf-select"><option>Enable</option><option>Disable</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">SLA Calculation</label>
                        <select class="tcf-select"><option>Business Hours</option><option>Calendar Hours</option></select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EMAIL TO TICKET TAB --}}
    <div class="tcf-panel" id="tab-email">
        <div class="tcf-section">
            <h6 class="tcf-section-title">Email to Ticket</h6>
            <p class="tcf-section-subtitle">Configure inbound email settings to auto-create tickets.</p>
            <div class="tcf-form-body">
                <div class="tcf-form-row">
                    <div class="tcf-form-group">
                        <label class="tcf-label">Inbound Email</label>
                        <input type="email" class="tcf-input" placeholder="tickets@yourdomain.com">
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Protocol</label>
                        <select class="tcf-select"><option>IMAP</option><option>POP3</option></select>
                    </div>
                    <div class="tcf-form-group">
                        <label class="tcf-label">Port</label>
                        <input type="number" class="tcf-input" value="993">
                    </div>
                </div>
                <div class="tcf-toggle-wrap mt-2">
                    <label class="tcf-toggle"><input type="checkbox" checked><span class="tcf-toggle-slider"></span></label>
                    <span class="tcf-toggle-label">Enable Email to Ticket</span>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /tcf-body --}}
</main>

<script>
/* ── Tab switching ─────────────────────────────────── */
function tcfSwitchTab(id, btn) {
    document.querySelectorAll('.tcf-tab').forEach(function(t)   { t.classList.remove('active'); });
    document.querySelectorAll('.tcf-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var panel = document.getElementById('tab-' + id);
    if (panel) panel.classList.add('active');
}

/* ── Accordion toggle (smooth slide) ──────────────── */
function tcfToggleAccordion(head) {
    head.classList.toggle('open');
    var body = head.nextElementSibling;
    if (!body) return;
    if (head.classList.contains('open')) {
        body.style.display   = '';
        body.style.overflow  = 'hidden';
        body.style.maxHeight = '0';
        body.style.transition= 'max-height .25s ease';
        requestAnimationFrame(function() {
            body.style.maxHeight = body.scrollHeight + 'px';
            body.addEventListener('transitionend', function h() {
                body.style.maxHeight = 'none';
                body.style.overflow  = '';
                body.style.transition= '';
                body.removeEventListener('transitionend', h);
            });
        });
    } else {
        body.style.overflow  = 'hidden';
        body.style.maxHeight = body.scrollHeight + 'px';
        body.style.transition= 'max-height .25s ease';
        requestAnimationFrame(function() {
            body.style.maxHeight = '0';
            body.addEventListener('transitionend', function h() {
                body.style.display   = 'none';
                body.style.transition= '';
                body.removeEventListener('transitionend', h);
            });
        });
    }
}

/* ════════════════════════════════════════════════════
   SELECT ALL — Departments Table
   ════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {

    var masterCb  = document.getElementById('deptSelectAll');
    var masterWrap = document.getElementById('deptSelectAllWrap');

    function getRowChecks() {
        return document.querySelectorAll('#deptTableBody .dept-row-check');
    }

    function syncMaster() {
        var rows    = getRowChecks();
        var checked = Array.from(rows).filter(function(c) { return c.checked; });
        if (!masterCb) return;
        masterCb.checked       = checked.length === rows.length && rows.length > 0;
        masterCb.indeterminate = checked.length > 0 && checked.length < rows.length;
        if (masterWrap) {
            masterWrap.classList.toggle('all-selected', masterCb.checked);
        }
    }

    /* Master → all rows */
    if (masterCb) {
        masterCb.addEventListener('change', function() {
            var rows = getRowChecks();
            rows.forEach(function(cb) {
                cb.checked = masterCb.checked;
                var tr = cb.closest('.dept-row');
                if (tr) tr.classList.toggle('row-selected', masterCb.checked);
            });
            if (masterWrap) {
                masterWrap.classList.toggle('all-selected', masterCb.checked);
            }
        });
    }

    /* Row → sync master */
    document.querySelectorAll('#deptTableBody .dept-row-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var tr = cb.closest('.dept-row');
            if (tr) tr.classList.toggle('row-selected', cb.checked);
            syncMaster();
        });
    });

    /* Toggle action text update */
    document.querySelectorAll('#deptTableBody .dept-row-toggle input').forEach(function(tog) {
        tog.addEventListener('change', function() {
            var label = this.closest('td').querySelector('.dept-toggle-label');
            if (label) label.textContent = this.checked ? 'Disable' : 'Enable';
        });
    });

    /* ════════════════════════════════════════════════════
       SELECT ALL — Report Fields
       ════════════════════════════════════════════════════ */
    var reportMaster = document.getElementById('selectAllFields');
    if (reportMaster) {
        reportMaster.addEventListener('change', function() {
            document.querySelectorAll('.field-check input[type="checkbox"]').forEach(function(cb) {
                cb.checked = reportMaster.checked;
            });
        });
        document.querySelectorAll('.field-check input[type="checkbox"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var all     = document.querySelectorAll('.field-check input[type="checkbox"]');
                var chk     = document.querySelectorAll('.field-check input[type="checkbox"]:checked');
                reportMaster.indeterminate = chk.length > 0 && chk.length < all.length;
                reportMaster.checked       = chk.length === all.length;
            });
        });
    }

    /* ════════════════════════════════════════════════════
       SELECT ALL — Permissions (Handler panel)
       ════════════════════════════════════════════════════ */
    var permMaster = document.querySelector('.tcf-user-detail .tcf-check-item input[type="checkbox"]');
    if (permMaster) {
        permMaster.addEventListener('change', function() {
            document.querySelectorAll('.tcf-perm-item input[type="checkbox"]').forEach(function(cb) {
                cb.checked = permMaster.checked;
            });
        });
        document.querySelectorAll('.tcf-perm-item input[type="checkbox"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var all = document.querySelectorAll('.tcf-perm-item input[type="checkbox"]');
                var chk = document.querySelectorAll('.tcf-perm-item input[type="checkbox"]:checked');
                permMaster.indeterminate = chk.length > 0 && chk.length < all.length;
                permMaster.checked       = chk.length === all.length;
            });
        });
    }

    /* ── Department search ────────────────────────── */
    window.deptSearch = function(q) {
        q = q.toLowerCase();
        document.querySelectorAll('#deptTableBody .dept-row').forEach(function(tr) {
            var name = tr.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
            tr.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    };

    /* ── User selection ───────────────────────────── */
    window.tcfSelectUser = function(el, name) {
        document.querySelectorAll('.tcf-user-item').forEach(function(i) { i.classList.remove('active'); });
        el.classList.add('active');
        var su = document.querySelector('.tcf-selected-user');
        if (su) su.textContent = 'Selected User: ' + name.toLowerCase().replace(/\s+/g, '.');
    };
});
</script>

@endsection