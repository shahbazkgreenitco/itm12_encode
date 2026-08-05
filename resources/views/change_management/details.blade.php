{{-- @page-meta { "page_no": "CD-01", "version": "1.0", "description": "Change Request Detail - Change Info" } --}}
@extends('layouts.layout1')
@section('title', 'Change Info')
@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   CHANGE DETAIL  –  cd-*
   Training: header-actions-wrapper + main.main-content
   Light: --app-bg:#f8f9fa  --app-surface:#fff  --app-border:#dee2e6
   Dark:  --app-bg:#141414  --dark-primary:#191919
          --dark-secondary:#2a2a2a  --dark-border:#2a2a2d
          --text-primary:#fff  --text-secondary:#e5e7eb  --text-muted:#757575
   ═══════════════════════════════════════════════════════ */

/* ── 1. HEADER ──────────────────────────────────────── */
.cd-header {
    background     : linear-gradient(90deg,#fde8e0 0%,#fef3ee 45%,#fdf8f6 72%,#f8fafd 100%);
    border-bottom  : 1px solid #f5ddd5;
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: flex-start !important;
    flex-wrap      : nowrap !important;
    gap            : 10px;
    padding-right  : 1.25rem !important;
    overflow       : hidden;
}
[data-bs-theme="dark"] .cd-header {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.cd-back {
    display:inline-flex; align-items:center; justify-content:center;
    background:none; border:none; cursor:pointer;
    color:var(--app-text,#212529) !important; padding:4px;
}
.cd-back svg { width:18px; height:18px; }
.cd-page-title {
    font-size:15px; font-weight:700;
    color:var(--app-text,#212529); white-space:nowrap;
}
[data-bs-theme="dark"] .cd-page-title { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .cd-back { color:var(--text-primary,#fff) !important; }

/* ── 2. TABS ─────────────────────────────────────────── */
.cd-tabs {
    display      : flex;
    padding      : 0 16px;
    border-bottom: 1px solid var(--app-border,#dee2e6);
    background   : var(--app-surface,#fff);
    overflow-x   : auto;
    scrollbar-width:none;
}
.cd-tabs::-webkit-scrollbar { display:none; }
[data-bs-theme="dark"] .cd-tabs {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.cd-tab {
    padding      : 12px 18px;
    font-size    : 13px; font-weight:500;
    color        : #6b7280 !important;
    border       : none; background:none;
    border-bottom: 2.5px solid transparent;
    cursor       : pointer; font-family:inherit;
    white-space  : nowrap; margin-bottom:-1px;
    transition   : color .15s, border-color .15s;
}
.cd-tab.active { color:#ef4444 !important; border-bottom-color:#ef4444; font-weight:600; }
.cd-tab:hover:not(.active) { color:#374151 !important; }
[data-bs-theme="dark"] .cd-tab        { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .cd-tab.active { color:#ef4444 !important; }

/* ── 3. BODY LAYOUT — 70/30 ─────────────────────────── */
.cd-body {
    display              : grid;
    grid-template-columns: 1fr 0.43fr;
    gap                  : 14px;
    padding              : 14px 16px calc(var(--footer-height,30px)+20px);
    background           : var(--app-bg,#f8f9fa);
    box-sizing           : border-box;
    min-width            : 0;
    align-items          : start;
}
[data-bs-theme="dark"] .cd-body { background:var(--app-bg,#141414) !important; }

.cd-left  { display:flex; flex-direction:column; gap:14px; min-width:0; }
.cd-right { min-width:0; position:sticky; top:14px; }

/* Tab panels */
.cd-panel        { display:none; }
.cd-panel.active { display:block; }

/* ── 4. CARD ─────────────────────────────────────────── */
.cd-card {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 12px;
    overflow     : hidden;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .cd-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.cd-card-pad { padding:16px; }
.cd-divider  { border-top:1px solid var(--app-border,#dee2e6); }
[data-bs-theme="dark"] .cd-divider { border-color:var(--dark-border,#2a2a2d) !important; }

/* ── 5. INFO CARD ───────────────────────────────────── */
.cd-info-title {
    font-size:15px; font-weight:700;
    color:var(--app-text,#212529); margin:0 0 8px;
    display:flex; align-items:center; justify-content:space-between;
}
[data-bs-theme="dark"] .cd-info-title { color:var(--text-primary,#fff) !important; }
.cd-expand-btn {
    background:none; border:none; cursor:pointer;
    color:#9ca3af; display:inline-flex; padding:3px;
}
.cd-expand-btn svg { width:16px; height:16px; }
.cd-info-desc { font-size:12.5px; color:#6b7280; line-height:1.6; margin-bottom:12px; }
[data-bs-theme="dark"] .cd-info-desc { color:var(--text-muted,#757575) !important; }

/* Attachment file chip */
.cd-attach-label { font-size:12px; color:#6b7280; margin-bottom:6px; font-weight:500; }
[data-bs-theme="dark"] .cd-attach-label { color:var(--text-muted,#757575) !important; }
.cd-file-chip {
    display:inline-flex; align-items:center; gap:6px;
    background:var(--app-bg,#f3f4f6);
    border:1px solid var(--app-border,#dee2e6);
    border-radius:6px; padding:6px 12px;
    font-size:12.5px; color:var(--app-text,#374151);
    text-decoration:none; cursor:pointer;
    transition:background .15s; margin-bottom:12px;
    display:inline-flex;
}
.cd-file-chip svg { width:14px; height:14px; color:#ef4444; flex-shrink:0; }
[data-bs-theme="dark"] .cd-file-chip {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-secondary,#e5e7eb) !important;
}

/* Status tags row */
.cd-tags-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:14px; }
.cd-tag {
    display:inline-flex; align-items:center; gap:5px;
    font-size:12px; font-weight:500;
    padding:4px 12px; border-radius:20px;
    border:1px solid;
}
.cd-tag-status  { background:#f0fdf4; color:#16a34a !important; border-color:#bbf7d0; }
.cd-tag-wait    { background:#fff3cd; color:#92400e !important; border-color:#fde68a; }
.cd-tag-high    { background:#fff1f2; color:#be123c !important; border-color:#fecdd3; }
.cd-tag-risk    { background:#fff1f2; color:#be123c !important; border-color:#fecdd3; }
.cd-tag-impact  { background:#f0fdf4; color:#16a34a !important; border-color:#bbf7d0; }
[data-bs-theme="dark"] .cd-tag-wait   { background:#3a2a0a !important; color:#fbbf24 !important; border-color:#78350f !important; }
[data-bs-theme="dark"] .cd-tag-high   { background:#2d0f0e !important; color:#f87171 !important; border-color:#7f1d1d !important; }
[data-bs-theme="dark"] .cd-tag-impact { background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important; }

/* Created by / Manager / Reviewer row */
.cd-people-row {
    display:flex; align-items:center; gap:20px;
    flex-wrap:wrap; padding:12px 0;
    border-top:1px solid var(--app-border,#dee2e6);
    border-bottom:1px solid var(--app-border,#dee2e6);
    margin-bottom:14px;
}
[data-bs-theme="dark"] .cd-people-row { border-color:var(--dark-border,#2a2a2d) !important; }
.cd-person-item { display:flex; align-items:center; gap:7px; }
.cd-person-label { font-size:12px; color:#6b7280; margin-right:4px; }
[data-bs-theme="dark"] .cd-person-label { color:var(--text-muted,#757575) !important; }
.cd-person-name { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
[data-bs-theme="dark"] .cd-person-name { color:var(--text-secondary,#e5e7eb) !important; }
.cd-av {
    width:24px; height:24px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:8px; font-weight:700; color:#fff;
    background:linear-gradient(135deg,#6366f1,#4338ca);
    overflow:hidden;
}
.cd-av img { width:100%; height:100%; object-fit:cover; }

/* Meta table */
.cd-meta-grid {
    display:grid; grid-template-columns:1fr 1fr; gap:0;
}
.cd-meta-row {
    display:flex; align-items:center; gap:10px;
    padding:9px 0; border-bottom:1px solid var(--app-border,#dee2e6);
}
.cd-meta-row:nth-child(odd)  { padding-right:20px; }
.cd-meta-row:nth-child(even) { padding-left:20px; border-left:1px solid var(--app-border,#dee2e6); }
.cd-meta-row:nth-last-child(-n+2) { border-bottom:none; }
[data-bs-theme="dark"] .cd-meta-row { border-color:var(--dark-border,#2a2a2d) !important; }
.cd-meta-icon { width:28px; height:28px; border-radius:6px; background:var(--app-bg,#f3f4f6); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cd-meta-icon svg { width:14px; height:14px; color:#6b7280; }
[data-bs-theme="dark"] .cd-meta-icon { background:var(--dark-primary,#191919) !important; }
.cd-meta-key { font-size:12px; color:#6b7280; margin-bottom:1px; }
.cd-meta-val { font-size:12.5px; font-weight:500; color:var(--app-text,#212529); }
[data-bs-theme="dark"] .cd-meta-key { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .cd-meta-val { color:var(--text-secondary,#e5e7eb) !important; }

/* ── 6. PLANNING CARD ───────────────────────────────── */
.cd-planning-title {
    font-size:15px; font-weight:700;
    color:var(--app-text,#212529); margin-bottom:12px;
}
[data-bs-theme="dark"] .cd-planning-title { color:var(--text-primary,#fff) !important; }

/* Accordion */
.cd-accordion { display:flex; flex-direction:column; gap:0; }
.cd-acc-item {
    border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; overflow:hidden;
    margin-bottom:8px;
}
.cd-acc-item:last-child { margin-bottom:0; }
[data-bs-theme="dark"] .cd-acc-item { border-color:var(--dark-border,#2a2a2d) !important; }

.cd-acc-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 14px; cursor:pointer;
    background:var(--app-surface,#fff);
    transition:background .15s; user-select:none;
}
.cd-acc-header:hover { background:var(--app-bg,#f8f9fa); }
[data-bs-theme="dark"] .cd-acc-header {
    background:var(--dark-secondary,#2a2a2a) !important;
}
[data-bs-theme="dark"] .cd-acc-header:hover { background:var(--dark-hover,#262626) !important; }

.cd-acc-left { display:flex; align-items:center; gap:10px; }
.cd-acc-icon {
    width:30px; height:30px; border-radius:8px;
    background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.cd-acc-icon svg { width:15px; height:15px; color:#6366f1; }
[data-bs-theme="dark"] .cd-acc-icon { background:var(--dark-primary,#191919) !important; }
.cd-acc-label { font-size:13px; font-weight:600; color:var(--app-text,#212529); }
[data-bs-theme="dark"] .cd-acc-label { color:var(--text-secondary,#e5e7eb) !important; }

.cd-acc-chevron { color:#9ca3af; transition:transform .22s ease; }
.cd-acc-chevron svg { width:16px; height:16px; }
.cd-acc-item.open .cd-acc-chevron { transform:rotate(180deg); }

.cd-acc-body {
    padding:14px; font-size:12.5px; color:#6b7280; line-height:1.7;
    background:var(--app-bg,#f8f9fa);
    border-top:1px solid var(--app-border,#dee2e6);
    display:none;
}
.cd-acc-item.open .cd-acc-body { display:block; }
[data-bs-theme="dark"] .cd-acc-body {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}

/* ── 7. CONVERSATION CARD ───────────────────────────── */
.cd-conv-title {
    font-size:15px; font-weight:700; color:var(--app-text,#212529);
    margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;
}
[data-bs-theme="dark"] .cd-conv-title { color:var(--text-primary,#fff) !important; }

.cd-conv-item {
    display:flex; gap:10px;
    padding:12px 0;
    border-bottom:1px solid var(--app-border,#dee2e6);
}
.cd-conv-item:last-of-type { border-bottom:none; }
[data-bs-theme="dark"] .cd-conv-item { border-color:var(--dark-border,#2a2a2d) !important; }

.cd-conv-av {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:700; color:#fff;
    background:linear-gradient(135deg,#6366f1,#4338ca);
    overflow:hidden;
}
.cd-conv-av img { width:100%; height:100%; object-fit:cover; }
.cd-conv-name { font-size:13px; font-weight:600; color:var(--app-text,#212529); }
.cd-conv-date { font-size:11.5px; color:#9ca3af; margin-left:8px; }
.cd-conv-text { font-size:12.5px; color:#6b7280; line-height:1.6; margin-top:4px; }
/* Conversation expand/collapse */
.cd-conv-text-wrap { position:relative; }
.cd-conv-text {
    font-size:12.5px; color:#6b7280; line-height:1.6;
    margin-top:4px;
    overflow:hidden;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
}
.cd-conv-text.expanded {
    -webkit-line-clamp:unset;
    display:block;
}
.cd-expand-more {
    font-size:12px; color:#ef4444; font-weight:500;
    cursor:pointer; display:inline-block; margin-top:3px;
    background:none; border:none; padding:0; font-family:inherit;
}
[data-bs-theme="dark"] .cd-conv-name { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .cd-conv-text { color:var(--text-muted,#757575) !important; }

/* Reply / nested */
.cd-conv-reply {
    margin-top:10px; padding:10px 12px;
    background:var(--app-bg,#f8f9fa);
    border-radius:8px;
    border:1px solid var(--app-border,#dee2e6);
}
[data-bs-theme="dark"] .cd-conv-reply {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.cd-conv-reply-header { display:flex; align-items:center; gap:8px; margin-bottom:5px; }
.cd-conv-reply-av {
    width:24px; height:24px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:8px; font-weight:700; color:#fff;
    background:linear-gradient(135deg,#14b8a6,#0891b2);
}

/* ── 8. MESSAGE FORM ────────────────────────────────── */
.cd-msg-label { font-size:13px; font-weight:600; color:var(--app-text,#212529); margin-bottom:6px; display:flex; align-items:center; gap:4px; }
.cd-req-mark { color:#ef4444; }
[data-bs-theme="dark"] .cd-msg-label { color:var(--text-primary,#fff) !important; }

.cd-rte-toolbar {
    display:flex; align-items:center; gap:4px; flex-wrap:wrap;
    padding:5px 10px;
    border:1px solid var(--app-border,#dee2e6);
    border-bottom:none; border-radius:8px 8px 0 0;
    background:var(--app-bg,#f8f9fa);
}
[data-bs-theme="dark"] .cd-rte-toolbar {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.cd-rte-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; border-radius:5px;
    border:none; background:none; cursor:pointer;
    font-size:12.5px; font-weight:600;
    color:var(--app-text,#374151) !important; font-family:inherit;
}
.cd-rte-btn:hover { background:rgba(0,0,0,.07); }
.cd-rte-btn svg { width:13px; height:13px; }
.cd-rte-sep { width:1px; height:18px; background:var(--app-border,#dee2e6); margin:0 3px; flex-shrink:0; }
[data-bs-theme="dark"] .cd-rte-sep { background:var(--dark-border,#2a2a2d) !important; }
.cd-rte-sel {
    font-size:11px; border:1px solid var(--app-border,#dee2e6); border-radius:4px;
    padding:2px 4px; background:var(--app-surface,#fff); color:var(--app-text,#374151); cursor:pointer; outline:none;
}
[data-bs-theme="dark"] .cd-rte-sel {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-primary,#fff) !important;
}
.cd-rte-body {
    border:1px solid var(--app-border,#dee2e6); border-radius:0 0 8px 8px;
    min-height:90px; padding:10px 12px; font-size:12.5px;
    color:#9ca3af; background:var(--app-surface,#fff);
    cursor:text; outline:none; margin-bottom:12px;
}
[data-bs-theme="dark"] .cd-rte-body {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}

/* Attachment zone */
.cd-att-zone {
    border:2px dashed var(--app-border,#dee2e6); border-radius:8px;
    padding:14px; text-align:center; cursor:pointer;
    transition:border-color .15s; margin-bottom:10px;
    position:relative;
}
.cd-att-zone input[type="file"] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
.cd-att-zone:hover { border-color:#93c5fd; }
[data-bs-theme="dark"] .cd-att-zone { border-color:var(--dark-border,#2a2a2d) !important; }
.cd-att-zone svg { width:18px; height:18px; color:#9ca3af; margin-bottom:4px; display:block; margin-left:auto; margin-right:auto; }
.cd-att-txt { font-size:12px; color:#6b7280; }
.cd-att-ext { font-size:10.5px; color:#9ca3af; margin-top:3px; }

.cd-check-row { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--app-text,#374151); margin-bottom:8px; }
.cd-check-row input { accent-color:#ef4444; width:13px; height:13px; cursor:pointer; }
[data-bs-theme="dark"] .cd-check-row { color:var(--text-secondary,#e5e7eb) !important; }

.cd-save-btn {
    display:inline-flex; align-items:center; justify-content:center;
    background:#ef4444; color:#fff !important;
    border:none; border-radius:8px; padding:9px 24px;
    font-size:13px; font-weight:600; font-family:inherit;
    cursor:pointer; transition:background .15s;
}
.cd-save-btn:hover { background:#dc2626; }

/* Dropdown below message */
.cd-sel {
    width:100%; border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; padding:8px 28px 8px 12px;
    font-size:12.5px; font-family:inherit;
    color:var(--app-text,#212529); background:var(--app-surface,#fff);
    outline:none; appearance:none; cursor:pointer; margin-top:8px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 10px center;
}
[data-bs-theme="dark"] .cd-sel {
    background-color: var(--dark-primary,#191919) !important;
    border-color    : var(--dark-border,#2a2a2d) !important;
    color           : var(--text-primary,#fff) !important;
}

/* ── 9. RIGHT SIDEBAR — Update Ticket ───────────────── */
.cd-update-head {
    background:#050b3c; padding:14px 16px; text-align:center;
}
.cd-update-head h6 { color:#fff !important; margin:0; font-size:13px; font-weight:600; }
.cd-update-body { padding:14px 16px; display:flex; flex-direction:column; gap:12px; }

/* 2-col selects */
.cd-sel-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.cd-sel-label { font-size:11.5px; color:#6b7280; margin-bottom:4px; font-weight:500; display:block; }
[data-bs-theme="dark"] .cd-sel-label { color:var(--text-muted,#757575) !important; }
.cd-sel-group { }

.cd-rsel {
    width:100%; border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; padding:7px 26px 7px 10px;
    font-size:12px; font-family:inherit;
    color:var(--app-text,#212529); background:var(--app-surface,#fff);
    outline:none; appearance:none; cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 8px center;
}
[data-bs-theme="dark"] .cd-rsel {
    background-color: var(--dark-primary,#191919) !important;
    border-color    : var(--dark-border,#2a2a2d) !important;
    color           : var(--text-primary,#fff) !important;
}

/* Date input */
.cd-date-wrap {
    position:relative;
    border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; overflow:hidden;
    display:flex; align-items:center;
    background:var(--app-surface,#fff);
}
.cd-date-wrap svg { position:absolute;left:8px;width:14px;height:14px;color:#9ca3af;pointer-events:none; }
.cd-date-wrap input[type="datetime-local"] {
    width:100%; border:none; outline:none;
    padding:7px 8px 7px 28px; font-size:12px;
    font-family:inherit; color:var(--app-text,#212529);
    background:transparent; cursor:pointer;
}
[data-bs-theme="dark"] .cd-date-wrap { background:var(--dark-primary,#191919) !important; border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .cd-date-wrap input { color:var(--text-primary,#fff) !important; }

/* Priority badge in select */
.cd-prio-sel-wrap { position:relative; }
.cd-prio-icon { position:absolute;left:8px;top:50%;transform:translateY(-50%);z-index:1;font-size:12px; }

/* Comment textarea */
.cd-comment {
    width:100%; border:1px solid var(--app-border,#dee2e6);
    border-radius:8px; padding:8px 10px; font-size:12px;
    font-family:inherit; color:var(--app-text,#212529);
    background:var(--app-surface,#fff); resize:none;
    outline:none; min-height:64px;
}
[data-bs-theme="dark"] .cd-comment {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-primary,#fff) !important;
}
.cd-comment-icons { display:flex; align-items:center; gap:8px; padding:6px 0; }
.cd-comment-icons svg { width:16px; height:16px; color:#9ca3af; cursor:pointer; }

.cd-update-btn {
    width:100%; background:#ef4444; color:#fff !important;
    border:none; border-radius:8px; padding:10px;
    font-size:13px; font-weight:600; font-family:inherit;
    cursor:pointer; transition:background .15s;
}
.cd-update-btn:hover { background:#dc2626; }

/* ── 10. RESPONSIVE ─────────────────────────────────── */
@media (max-width:1199px) {
    .cd-body { grid-template-columns:1fr 0.5fr; }
}
@media (max-width:991px) {
    .cd-body  { grid-template-columns:1fr; }
    .cd-right { position:static; }
    .cd-meta-grid { grid-template-columns:1fr; }
    .cd-meta-row:nth-child(even) { border-left:none; padding-left:0; }
    .cd-sel-grid { grid-template-columns:1fr 1fr; }
}
@media (max-width:767px) {
    .cd-header     { padding-right:.75rem !important; min-height:52px; }
    .cd-page-title { font-size:13px; }
    .cd-body       { padding:10px 10px 80px; }
    .cd-sel-grid   { grid-template-columns:1fr; }
    .cd-people-row { gap:12px; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper cd-header">
    <a href="{{ url()->previous() }}" class="cd-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    <span class="cd-page-title">Change Info - {{ $change->number ?? '#CR0000063' }}</span>
</div>

{{-- ② TABS --}}
<div class="cd-tabs">
    @foreach([
        ['info',            'Info',            true],
        ['approvals',       'Approvals',       false],
        ['attachment',      'Attachment',      false],
        ['history',         'History',         false],
        ['relevant_ticket', 'Relevant Ticket', false],
        ['impacted_device', 'Impacted Device', false],
        ['relevant_task',   'Relevant Task',   false],
    ] as [$id,$label,$active])
    <button class="cd-tab {{ $active?'active':'' }}"
            data-panel="{{ $id }}"
            onclick="cdTab('{{ $id }}',this)">{{ $label }}</button>
    @endforeach
</div>

{{-- ③ MAIN --}}
<main class="main-content">
<div class="cd-body">

{{-- ═══ LEFT ═══ --}}
<div class="cd-left">

{{-- ── Info Panel ── --}}
<div class="cd-panel active" id="cd-panel-info">
    <div class="cd-card">
    <div class="cd-card-pad">

        {{-- Title --}}
        <h3 class="cd-info-title">
            {{ $change->title ?? 'Faridabad office location change' }}
            <button class="cd-expand-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                    <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                </svg>
            </button>
        </h3>
        <p class="cd-info-desc">{{ $change->description ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut.' }}</p>

        {{-- Attachment file --}}
        <div class="cd-attach-label">Attachment:</div>
        <a href="#" class="cd-file-chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            Greenitco ITM.Pdf
        </a>

        {{-- Status tags --}}
        <div class="cd-tags-row">
            <span style="font-size:12px;color:#6b7280;font-weight:500;">Status</span>
            <span class="cd-tag cd-tag-wait">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><polyline points="18 15 12 9 6 15"/></svg>
                Waiting for Approval
            </span>
            <span class="cd-tag cd-tag-high">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px;color:#ef4444;"><polyline points="18 15 12 9 6 15"/></svg>
                High
            </span>
            <span class="cd-tag cd-tag-risk">Risk: High</span>
            <span class="cd-tag cd-tag-impact">Impact: Low</span>
        </div>

        {{-- People row --}}
        <div class="cd-people-row">
            <div class="cd-person-item">
                <span class="cd-person-label">Created by</span>
                <div class="cd-av">SK</div>
                <span class="cd-person-name">{{ $change->created_by ?? 'Shivam Kumar' }}</span>
            </div>
            <div class="cd-person-item">
                <span class="cd-person-label">Manager</span>
                <div class="cd-av" style="background:linear-gradient(135deg,#14b8a6,#0891b2);">SA</div>
                <span class="cd-person-name">{{ $change->manager ?? 'Santhosh' }}</span>
            </div>
            <div class="cd-person-item">
                <span class="cd-person-label">Reviewer</span>
                <div class="cd-av" style="background:linear-gradient(135deg,#f97316,#ea580c);">SA</div>
                <span class="cd-person-name">{{ $change->reviewer ?? 'Santhosh' }}</span>
            </div>
        </div>

        {{-- Meta grid --}}
        <div class="cd-meta-grid">
            @foreach([
                ['user',      'Category',             $change->category        ?? 'Infrastructure',              false],
                ['tool',      'Change Implementer',   $change->implementer     ?? 'Shivam Kumar',                true],
                ['refresh',   'Change Type',          $change->type            ?? 'Mumbai',                      false],
                ['clock',     'Downtime',             $change->downtime        ?? 'No',                          false],
                ['calendar',  'Scheduled Start Date', $change->scheduled_start ?? '12 Deceber 2025, 10.28 AM',  false],
                ['calendar',  'Scheduled End Date',   $change->scheduled_end   ?? '12 Deceber 2025, 10.28 AM',  false],
            ] as [$icon,$key,$val,$showAvatar])
            <div class="cd-meta-row">
                <div class="cd-meta-icon">
                    @if($icon==='user')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    @elseif($icon==='tool')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    @elseif($icon==='refresh')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    @elseif($icon==='clock')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @elseif($icon==='calendar')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    @endif
                </div>
                <div>
                    <div class="cd-meta-key">{{ $key }}</div>
                    <div class="cd-meta-val" style="display:flex;align-items:center;gap:6px;">
                        @if($showAvatar)
                        <div class="cd-av" style="width:20px;height:20px;font-size:7px;">SK</div>
                        @endif
                        {{ $val }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
    </div>

    {{-- Planning Card --}}
    <div class="cd-card">
    <div class="cd-card-pad">
        <h3 class="cd-planning-title">Planning</h3>
        <div class="cd-accordion">

            @php
            $accItems = [
                ['reason',   '1. Reason for change',     true,  'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'],
                ['impact',   '2. Impact Discription',    false, ''],
                ['risk',     '3. Risk Discription',      false, ''],
                ['rollout',  '4. Rollout/Rollback Plan', false, ''],
                ['fallback', '5. Fallback Plan',         false, ''],
            ];
            $accIcons = [
                'reason'   => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                'impact'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                'risk'     => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
                'rollout'  => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>',
                'fallback' => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>',
            ];
            @endphp

            @foreach($accItems as [$id,$label,$open,$body])
            <div class="cd-acc-item {{ $open ? 'open' : '' }}" id="acc-{{ $id }}">
                <div class="cd-acc-header" onclick="cdAccToggle('{{ $id }}')">
                    <div class="cd-acc-left">
                        <div class="cd-acc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $accIcons[$id] !!}</svg>
                        </div>
                        <span class="cd-acc-label">{{ $label }}</span>
                    </div>
                    <span class="cd-acc-chevron">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </span>
                </div>
                @if($body)
                <div class="cd-acc-body">{{ $body }}</div>
                @else
                <div class="cd-acc-body">No content added yet.</div>
                @endif
            </div>
            @endforeach

        </div>
    </div>
    </div>

    {{-- Conversation Card --}}
    <div class="cd-card">
    <div class="cd-card-pad">
        <div class="cd-conv-title">
            Conversation
            <button class="cd-expand-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                    <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                </svg>
            </button>
        </div>

        {{-- Conversation items --}}
        @foreach([
            ['AS','linear-gradient(135deg,#6366f1,#4338ca)','Ananth S','December 29, 2025, 10:08 AM','I think before jumping into solutions, we should really understand the user\'s pain points. What problem are they actually facing? Understanding this deeply helps us design better systems.',1,null],
            ['BG','linear-gradient(135deg,#f97316,#ea580c)','Bharat Gupta','January 02, 2026, 12:08 PM','Exactly. Instead of assuming, we need to observe, ask questions, and empathize with users. Once we clearly define the problem, the solutions will be more meaningful and effective for everyone involved.',2,['SH','linear-gradient(135deg,#14b8a6,#0891b2)','Shivam','January 02, 2026, 12:08 PM','Exactly. Instead of assuming, we need to observe, ask questions, and empathize with users. Once we clearly define the problem, the solutions will be more meaningful and effective.']],
            ['KA','linear-gradient(135deg,#ec4899,#db2777)','Kanna','December 29, 2025, 10:08 AM','I think before jumping into solutions, we should really understand the user\'s pain points. What problem are they actually facing?',3,null],
        ] as [$ini,$grad,$name,$date,$text,$cid,$reply])
        <div class="cd-conv-item">
            <div class="cd-conv-av" style="background:{{$grad}};">{{$ini}}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:0;">
                    <span class="cd-conv-name">{{$name}}</span>
                    <span class="cd-conv-date">{{$date}}</span>
                </div>
                <div class="cd-conv-text-wrap">
                    <div class="cd-conv-text" id="cdConvText{{$cid}}">{{$text}}</div>
                    <button class="cd-expand-more" id="cdConvBtn{{$cid}}" onclick="cdConvToggle({{$cid}})">Expand More</button>
                </div>
                @if($reply)
                <div class="cd-conv-reply">
                    <div class="cd-conv-reply-header">
                        <div class="cd-conv-reply-av">{{$reply[0]}}</div>
                        <span style="font-size:12.5px;font-weight:600;color:var(--app-text,#212529);">{{$reply[2]}}</span>
                        <span style="font-size:11px;color:#9ca3af;margin-left:6px;">{{$reply[3]}}</span>
                    </div>
                    <div style="font-size:12px;color:#6b7280;line-height:1.6;">{{$reply[4]}}</div>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Message form --}}
        <div style="margin-top:16px;">
            <div class="cd-msg-label">Message <span class="cd-req-mark">*</span></div>
            <div class="cd-rte-toolbar">
                <select class="cd-rte-sel"><option>Tt</option><option>H1</option><option>H2</option></select>
                <div class="cd-rte-sep"></div>
                <button class="cd-rte-btn"><strong>B</strong></button>
                <button class="cd-rte-btn"><u>U</u></button>
                <button class="cd-rte-btn"><em>I</em></button>
                <button class="cd-rte-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><line x1="18" y1="9" x2="12" y2="15"/><line x1="12" y1="9" x2="18" y2="15"/></svg></button>
                <div class="cd-rte-sep"></div>
                <button class="cd-rte-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4M4 10h2"/></svg></button>
                <button class="cd-rte-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
            </div>
            <div class="cd-rte-body" contenteditable="true">Enter Your Message</div>

            {{-- Attachment --}}
            <div style="font-size:13px;font-weight:600;color:var(--app-text,#212529);margin-bottom:6px;">Attachment</div>
            <div class="cd-att-zone">
                <input type="file" multiple accept=".jpg,.jpeg,.png,.gif,.xls,.doc,.docx,.pdf,.txt,.zip,.psd,.csx">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                <div class="cd-att-txt">Drag & drop or Upload files here</div>
                <div class="cd-att-ext">jpg, jpeg, png, gif, xls, doc, docx, pdf, txt, zip, psd, csx</div>
            </div>

            <div class="cd-check-row">
                <input type="checkbox" id="cdNote" checked>
                <label for="cdNote" style="cursor:pointer;">Make it note for internal purpose.</label>
            </div>

            <button class="cd-save-btn">Save Comment</button>

            <div class="cd-check-row" style="margin-top:10px;">
                <input type="checkbox" id="cdBackTrail" checked>
                <label for="cdBackTrail" style="cursor:pointer;">Add back-trail conversation on e-mail notification</label>
            </div>
            <div class="cd-check-row">
                <input type="checkbox" id="cdCCEmail">
                <label for="cdCCEmail" style="cursor:pointer;">CC Emails</label>
            </div>

            <select class="cd-sel"><option>Critical</option><option>High</option><option>Medium</option><option>Low</option></select>
        </div>
    </div>
    </div>

    </div>{{-- /cd-panel-info --}}

    {{-- ── Approvals Panel ── --}}
    <div class="cd-panel" id="cd-panel-approvals">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">Approvals</h4>
            <p style="font-size:13px;color:#6b7280;">No approval records found.</p>
        </div></div>
    </div>

    {{-- ── Attachment Panel ── --}}
    <div class="cd-panel" id="cd-panel-attachment">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">Attachments</h4>
            <a href="#" class="cd-file-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:14px;height:14px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Greenitco ITM.Pdf
            </a>
        </div></div>
    </div>

    {{-- ── History Panel ── --}}
    <div class="cd-panel" id="cd-panel-history">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">History</h4>
            <p style="font-size:13px;color:#6b7280;">No history records found.</p>
        </div></div>
    </div>

    {{-- ── Relevant Ticket Panel ── --}}
    <div class="cd-panel" id="cd-panel-relevant_ticket">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">Relevant Ticket</h4>
            <p style="font-size:13px;color:#6b7280;">No relevant tickets found.</p>
        </div></div>
    </div>

    {{-- ── Impacted Device Panel ── --}}
    <div class="cd-panel" id="cd-panel-impacted_device">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">Impacted Device</h4>
            <p style="font-size:13px;color:#6b7280;">No impacted devices found.</p>
        </div></div>
    </div>

    {{-- ── Relevant Task Panel ── --}}
    <div class="cd-panel" id="cd-panel-relevant_task">
        <div class="cd-card"><div class="cd-card-pad">
            <h4 style="font-size:14px;font-weight:700;color:var(--app-text,#212529);margin-bottom:10px;">Relevant Task</h4>
            <p style="font-size:13px;color:#6b7280;">No relevant tasks found.</p>
        </div></div>
    </div>

    </div>{{-- /cd-left --}}

{{-- ═══ RIGHT SIDEBAR ═══ --}}
<div class="cd-right">
<div class="cd-card">
    <div class="cd-update-head"><h6>Update Ticket</h6></div>
    <div class="cd-update-body">

        {{-- Change Status + Change Priority --}}
        <div class="cd-sel-grid">
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Status</span>
                <select class="cd-rsel"><option>In Progress</option><option>Pending</option><option>Resolved</option><option>Closed</option></select>
            </div>
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Priority</span>
                <select class="cd-rsel"><option>🔴 High</option><option>🟡 Medium</option><option>🟢 Low</option></select>
            </div>
        </div>

        {{-- Change Impact + Change Risk --}}
        <div class="cd-sel-grid">
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Impact</span>
                <select class="cd-rsel"><option>🟢 Low</option><option>🟡 Medium</option><option>🔴 High</option></select>
            </div>
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Risk</span>
                <select class="cd-rsel"><option>🔴 High</option><option>🟡 Medium</option><option>🟢 Low</option></select>
            </div>
        </div>

        {{-- Change Type + Change Category --}}
        <div class="cd-sel-grid">
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Type</span>
                <select class="cd-rsel"><option>Minor</option><option>Major</option><option>Emergency</option><option>Standard</option></select>
            </div>
            <div class="cd-sel-group">
                <span class="cd-sel-label">Change Category</span>
                <select class="cd-rsel"><option>Hardware</option><option>Software</option><option>Network</option></select>
            </div>
        </div>

        {{-- Downtime Start --}}
        <div>
            <span class="cd-sel-label">Downtime Start</span>
            <div class="cd-date-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="datetime-local" value="2026-07-14T11:45">
            </div>
        </div>

        {{-- Downtime End --}}
        <div>
            <span class="cd-sel-label">Downtime End</span>
            <div class="cd-date-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="datetime-local" value="2026-07-14T11:45">
            </div>
        </div>

        {{-- Schedule Start Date --}}
        <div>
            <span class="cd-sel-label">Schedule Start Date</span>
            <div class="cd-date-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="datetime-local" value="2026-07-14T11:45">
            </div>
        </div>

        {{-- Schedule End Date --}}
        <div>
            <span class="cd-sel-label">Schedule End Date</span>
            <div class="cd-date-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="datetime-local" value="2026-07-14T11:45">
            </div>
        </div>

        {{-- Cost --}}
        <div>
            <span class="cd-sel-label">Cost</span>
            <select class="cd-rsel" style="width:100%;">
                <option>20,00,00</option>
                <option>10,00,00</option>
                <option>50,00,00</option>
            </select>
        </div>

        {{-- Close State --}}
        <div>
            <span class="cd-sel-label">Close State</span>
            <select class="cd-rsel" style="width:100%;"><option>Choose State</option><option>Completed</option><option>Failed</option><option>Cancelled</option></select>
        </div>

        {{-- Comment --}}
        <div>
            <span class="cd-sel-label">Comment</span>
            <div style="border:1px solid var(--app-border,#dee2e6);border-radius:8px;overflow:hidden;">
                <textarea class="cd-comment" placeholder="Add a comment" style="border:none;border-radius:0;"></textarea>
                <div class="cd-comment-icons" style="padding:6px 10px;border-top:1px solid var(--app-border,#dee2e6);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
                </div>
            </div>
        </div>

        <button class="cd-update-btn">Update</button>

    </div>
</div>
</div>{{-- /cd-right --}}

</div>{{-- /cd-body --}}
</main>

<script>
/* ── Tab switch with panels ───────────────────────── */
function cdTab(id, btn) {
    document.querySelectorAll('.cd-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.cd-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    var panel = document.getElementById('cd-panel-' + id);
    if (panel) panel.classList.add('active');
}

/* ── Conversation expand/collapse ─────────────────── */
function cdConvToggle(id) {
    var text = document.getElementById('cdConvText' + id);
    var btn  = document.getElementById('cdConvBtn'  + id);
    var isExpanded = text.classList.contains('expanded');
    if (isExpanded) {
        text.classList.remove('expanded');
        btn.textContent = 'Expand More';
    } else {
        text.classList.add('expanded');
        btn.textContent = 'Collapse';
    }
}

/* ── Accordion toggle ─────────────────────────────── */
function cdAccToggle(id) {
    var item = document.getElementById('acc-' + id);
    item.classList.toggle('open');
}

/* ── RTE placeholder ──────────────────────────────── */
document.querySelectorAll('.cd-rte-body').forEach(function(el) {
    el.addEventListener('focus', function() {
        if (this.textContent.trim() === 'Enter Your Message') {
            this.textContent = '';
            this.style.color = 'var(--app-text,#212529)';
        }
    });
    el.addEventListener('blur', function() {
        if (!this.textContent.trim()) {
            this.textContent = 'Enter Your Message';
            this.style.color = '';
        }
    });
});
</script>
@endsection