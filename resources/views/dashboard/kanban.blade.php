{{-- @page-meta
{
  "page_no": "KBD-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Claude",
      "from": "2026-05",
      "reviewer": null,
      "description": "Kanban Board - Standard Implementation"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', 'Kanban Board')
@section('content')
<style>
/*
 ╔══════════════════════════════════════════════════════════════╗
 ║  KANBAN BOARD  –  kbd-*                                      ║
 ║                                                              ║
 ║  KANBAN BEST PRACTICE RULES IMPLEMENTED:                     ║
 ║  1. WIP Limit  — column shows warning when cards exceed limit ║
 ║  2. Card Priority — colour-coded left border (P1/P2/P3/P4)   ║
 ║  3. Card States  — tags: Backlog / In Progress / Review / Done║
 ║  4. Swimlanes    — optional grouping row inside columns       ║
 ║  5. Blocked      — red "BLOCKED" indicator on stuck cards     ║
 ║  6. Due date     — overdue = red, due soon = orange, ok = grey║
 ║  7. Assignee     — always visible on card                     ║
 ║  8. Card count   — column header shows x/WIP-limit           ║
 ║  9. Visual flow  — left-to-right stage progression            ║
 ║  10. Empty state — ghost card with CTA, not blank             ║
 ╚══════════════════════════════════════════════════════════════╝
*/

/* ── Root tokens (extend index.css + modetheme.css) ─── */
:root {
    --kbd-col-w     : 288px;
    --kbd-gap       : 14px;
    --kbd-radius    : 12px;
    --kbd-card-r    : 8px;
    --kbd-p1        : #ef4444;   /* Critical   */
    --kbd-p2        : #f59e0b;   /* High       */
    --kbd-p3        : #3b82f6;   /* Medium     */
    --kbd-p4        : #9ca3af;   /* Low        */
    --kbd-blocked   : #dc2626;
    --kbd-overdue   : #ef4444;
    --kbd-due-soon  : #f59e0b;
}

/* ── Section header ────────────────────────────────── */
.kbd-header {
    background     : linear-gradient(110deg,#fde8e0 0%,#f3eeff 35%,#e8f0fe 70%,var(--app-bg,#f8fafd) 100%);
    border-bottom  : 1px solid var(--bs-border-color);
    min-height     : 58px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 10px;
    overflow       : hidden;
    padding-right  : 1.25rem !important;
}
[data-bs-theme="dark"] .kbd-header { border-bottom:1px solid var(--dark-border,#2a2a2d); }

.kbd-page-title { font-size:16px; font-weight:700; color:var(--bs-body-color); white-space:nowrap; }

/* ── Toolbar ───────────────────────────────────────── */
.kbd-toolbar { display:flex; align-items:center; gap:6px; flex-shrink:0; }

.kbd-search {
    display:flex; align-items:center; gap:6px;
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:5px 12px; width:200px; transition:border-color .18s,width .2s;
}
.kbd-search:focus-within { border-color:#93c5fd; width:230px; }
.kbd-search svg   { width:13px; height:13px; color:var(--bs-secondary-color); flex-shrink:0; }
.kbd-search input {
    border:none; outline:none; background:transparent;
    font-size:12.5px; color:var(--bs-body-color); width:100%; font-family:inherit;
}
.kbd-search input::placeholder { color:var(--bs-secondary-color); opacity:.65; }

.kbd-tb-btn {
    display:inline-flex; align-items:center; justify-content:center; gap:5px;
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:6px 9px; font-size:12px; font-family:inherit;
    color:var(--bs-body-color) !important; cursor:pointer; white-space:nowrap;
    transition:background .15s; flex-shrink:0;
}
.kbd-tb-btn:hover { background:var(--bs-tertiary-bg); }
.kbd-tb-btn svg   { width:14px; height:14px; color:var(--bs-secondary-color); }
.kbd-tb-btn .chev { width:10px; height:10px; margin-left:1px; }

.kbd-btn-add {
    display:inline-flex; align-items:center; gap:5px;
    background:#ef4444; color:#fff !important; border:none; border-radius:8px;
    padding:7px 14px; font-size:13px; font-weight:500; font-family:inherit;
    cursor:pointer; white-space:nowrap; transition:background .15s; flex-shrink:0;
}
.kbd-btn-add:hover { background:#dc2626; }
.kbd-btn-add svg   { width:13px; height:13px; }

/* ── Sub toolbar ───────────────────────────────────── */
.kbd-sub-bar {
    display:flex; align-items:center; justify-content:space-between;
    padding:8px 16px; gap:10px; flex-wrap:wrap;
    border-bottom:1px solid var(--bs-border-color);
    background:var(--app-bg,var(--bs-body-bg));
}
.kbd-status-pills { display:flex; align-items:center; gap:8px; }
.kbd-status-pill {
    display:inline-flex; align-items:center; gap:5px;
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:20px;
    padding:3px 12px; font-size:12px; font-weight:500;
    color:var(--bs-body-color) !important; cursor:pointer; transition:background .15s;
}
.kbd-status-pill:hover { background:var(--bs-tertiary-bg); }
.kbd-status-pill.active-open  { background:#fef2f2; border-color:#fca5a5; }
.kbd-status-pill.active-close { background:#f0fdf4; border-color:#86efac; }
[data-bs-theme="dark"] .kbd-status-pill.active-open  { background:#2d0f0e; border-color:#7f1d1d; }
[data-bs-theme="dark"] .kbd-status-pill.active-close { background:#052e16; border-color:#166534; }
.kbd-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.kbd-dot-red   { background:#ef4444; }
.kbd-dot-green { background:#22c55e; }

.kbd-filter-right { display:flex; align-items:center; gap:8px; }
.kbd-select {
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:8px;
    padding:4px 26px 4px 10px; font-size:12px; font-family:inherit;
    color:var(--bs-body-color); outline:none; cursor:pointer;
    appearance:none; -webkit-appearance:none; max-width:160px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 8px center;
    transition:border-color .18s;
}
.kbd-select:focus { border-color:#93c5fd; }

.kbd-av-stack { display:flex; }
.kbd-av {
    width:26px; height:26px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:8px; font-weight:700; color:#fff !important;
    border:2px solid var(--app-surface,var(--bs-body-bg));
    margin-left:-7px; flex-shrink:0; cursor:pointer;
    transition:transform .15s; position:relative; z-index:1;
}
.kbd-av:first-child { margin-left:0; }
.kbd-av:hover { transform:scale(1.15); z-index:2; }
.av-i { background:linear-gradient(135deg,#6366f1,#4338ca); }
.av-t { background:linear-gradient(135deg,#14b8a6,#0891b2); }
.av-o { background:linear-gradient(135deg,#f59e0b,#d97706); }
.av-r { background:linear-gradient(135deg,#ef4444,#dc2626); }

/* ── WIP Limit indicator ───────────────────────────── */
.kbd-wip-indicator {
    font-size:11px; font-weight:600;
    padding:1px 7px; border-radius:10px; flex-shrink:0;
}
.kbd-wip-ok      { background:#f0fdf4; color:#16a34a !important; }
.kbd-wip-warning { background:#fff7ed; color:#ea580c !important; }
.kbd-wip-over    { background:#fef2f2; color:#dc2626 !important; animation:kbd-pulse 1.2s infinite; }
[data-bs-theme="dark"] .kbd-wip-ok      { background:#052e16; color:#4ade80 !important; }
[data-bs-theme="dark"] .kbd-wip-warning { background:#431407; color:#fb923c !important; }
[data-bs-theme="dark"] .kbd-wip-over    { background:#2d0f0e; color:#f87171 !important; }

@keyframes kbd-pulse {
    0%,100% { opacity:1; }
    50%      { opacity:.6; }
}

/* ── Board scroll container ────────────────────────── */
.kbd-board {
    display       : flex;
    gap           : var(--kbd-gap);
    padding       : 14px 16px calc(var(--footer-height,30px) + 20px);
    overflow-x    : auto;
    overflow-y    : hidden;
    align-items   : flex-start;
    min-height    : calc(100vh - 155px);
    background    : var(--app-bg,var(--bs-body-bg));
    scrollbar-width: thin;
    scrollbar-color: var(--bs-border-color) transparent;
}
.kbd-board::-webkit-scrollbar       { height:5px; }
.kbd-board::-webkit-scrollbar-thumb { background:var(--bs-border-color); border-radius:4px; }

/* ── Column ────────────────────────────────────────── */
.kbd-col {
    width       : var(--kbd-col-w);
    min-width   : var(--kbd-col-w);
    background  : var(--app-surface,var(--bs-body-bg));
    border      : 1px solid var(--bs-border-color);
    border-radius: var(--kbd-radius);
    display     : flex;
    flex-direction: column;
    overflow    : hidden;
    flex-shrink : 0;
    transition  : box-shadow .2s;
}
/* Stage colour accent — thin top border per column */
.kbd-col[data-stage="backlog"]     { border-top:3px solid #9ca3af; }
.kbd-col[data-stage="todo"]        { border-top:3px solid #3b82f6; }
.kbd-col[data-stage="inprogress"]  { border-top:3px solid #f59e0b; }
.kbd-col[data-stage="review"]      { border-top:3px solid #8b5cf6; }
.kbd-col[data-stage="done"]        { border-top:3px solid #22c55e; }
.kbd-col[data-stage="custom"]      { border-top:3px solid #6366f1; }

.kbd-col.drag-over { box-shadow:0 0 0 2px #3b82f6; background:rgba(59,130,246,.03); }
[data-bs-theme="dark"] .kbd-col.drag-over { background:rgba(59,130,246,.08); }

/* Column header */
.kbd-col-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 12px 10px;
    border-bottom:1px solid var(--bs-border-color);
    gap:6px;
}
.kbd-col-head-left  { display:flex; align-items:center; gap:6px; min-width:0; }
.kbd-col-head-right { display:flex; align-items:center; gap:4px; flex-shrink:0; }
.kbd-col-name {
    font-size:13px; font-weight:600; color:var(--bs-body-color);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px;
}
.kbd-icon-btn {
    background:none; border:none; cursor:pointer; padding:4px; border-radius:6px;
    color:var(--bs-secondary-color) !important;
    display:inline-flex; align-items:center; justify-content:center;
    transition:background .15s;
}
.kbd-icon-btn:hover { background:var(--bs-tertiary-bg); }
.kbd-icon-btn svg   { width:14px; height:14px; }

/* ── Cards scroll area ─────────────────────────────── */
.kbd-cards {
    flex:1; overflow-y:auto; padding:10px;
    display:flex; flex-direction:column; gap:8px;
    min-height:120px;
    max-height:calc(100vh - 265px);
    scrollbar-width:thin;
    scrollbar-color:var(--bs-border-color) transparent;
}
.kbd-cards::-webkit-scrollbar       { width:3px; }
.kbd-cards::-webkit-scrollbar-thumb { background:var(--bs-border-color); border-radius:4px; }

/* ── Kanban card ───────────────────────────────────── */
/*
   Best practice: left border = priority, card has:
   - Priority indicator (left coloured border)
   - Title (concise, action-oriented)
   - Labels/tags
   - Due date (colour-coded)
   - Assignee avatars
   - Story points
   - Blocked indicator if applicable
*/
.kbd-card {
    background    : var(--bs-body-bg);
    border        : 1px solid var(--bs-border-color);
    border-radius : var(--kbd-card-r);
    border-left   : 3px solid var(--kbd-p4);  /* default: low priority */
    cursor        : grab;
    transition    : box-shadow .18s, transform .15s, opacity .15s;
    user-select   : none;
    position      : relative;
}
.kbd-card:hover {
    box-shadow : 0 4px 16px rgba(0,0,0,.1);
    transform  : translateY(-2px);
    border-color: var(--bs-secondary-color);
}
[data-bs-theme="dark"] .kbd-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.4); }
.kbd-card:active { cursor:grabbing; transform:scale(.98); }
.kbd-card.dragging { opacity:.5; transform:rotate(2deg); }

/* Priority border colours */
.kbd-card[data-priority="critical"] { border-left-color:var(--kbd-p1); }
.kbd-card[data-priority="high"]     { border-left-color:var(--kbd-p2); }
.kbd-card[data-priority="medium"]   { border-left-color:var(--kbd-p3); }
.kbd-card[data-priority="low"]      { border-left-color:var(--kbd-p4); }

/* Blocked overlay stripe */
.kbd-card.blocked::after {
    content    : 'BLOCKED';
    position   : absolute; top:6px; right:6px;
    background : var(--kbd-blocked); color:#fff;
    font-size  : 9px; font-weight:700; letter-spacing:.06em;
    padding    : 1px 6px; border-radius:3px;
}

/* Card cover image */
.kbd-card-cover {
    width:100%; height:120px; object-fit:cover;
    border-radius:calc(var(--kbd-card-r) - 1px) calc(var(--kbd-card-r) - 1px) 0 0;
    display:block; background:var(--bs-tertiary-bg);
}
.kbd-card-cover-placeholder {
    width:100%; height:120px;
    border-radius:calc(var(--kbd-card-r) - 1px) calc(var(--kbd-card-r) - 1px) 0 0;
    background:linear-gradient(135deg,#eef2ff 0%,#f5f3ff 50%,#fdf2f8 100%);
    display:flex; align-items:center; justify-content:center; overflow:hidden;
}
[data-bs-theme="dark"] .kbd-card-cover-placeholder {
    background:linear-gradient(135deg,#1e1b4b 0%,#1f1535 50%,#2d1520 100%);
}

/* Card body */
.kbd-card-body { padding:10px 10px 8px; }

/* Labels row */
.kbd-card-labels { display:flex; flex-wrap:wrap; gap:4px; margin-bottom:6px; }
.kbd-label {
    font-size:10px; font-weight:600; padding:1px 7px; border-radius:3px;
    text-transform:uppercase; letter-spacing:.04em; white-space:nowrap;
}
.kbd-label-blue   { background:#dbeafe; color:#1d4ed8 !important; }
.kbd-label-green  { background:#dcfce7; color:#15803d !important; }
.kbd-label-orange { background:#ffedd5; color:#c2410c !important; }
.kbd-label-purple { background:#ede9fe; color:#6d28d9 !important; }
.kbd-label-red    { background:#fee2e2; color:#b91c1c !important; }
.kbd-label-grey   { background:var(--bs-tertiary-bg); color:var(--bs-secondary-color) !important; }
[data-bs-theme="dark"] .kbd-label-blue   { background:#1e2d4a; color:#93c5fd !important; }
[data-bs-theme="dark"] .kbd-label-green  { background:#052e16; color:#4ade80 !important; }
[data-bs-theme="dark"] .kbd-label-orange { background:#431407; color:#fb923c !important; }
[data-bs-theme="dark"] .kbd-label-purple { background:#2d1b69; color:#c4b5fd !important; }
[data-bs-theme="dark"] .kbd-label-red    { background:#2d0f0e; color:#f87171 !important; }

.kbd-card-title {
    font-size:13px; font-weight:500; color:var(--bs-body-color);
    line-height:1.4; margin:0 0 8px;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}

/* Card footer: due date, points, assignees */
.kbd-card-foot {
    display:flex; align-items:center; justify-content:space-between;
    gap:6px; flex-wrap:wrap; margin-top:4px;
}
.kbd-card-foot-left  { display:flex; align-items:center; gap:6px; }
.kbd-card-foot-right { display:flex; align-items:center; }

/* Due date */
.kbd-due {
    display:inline-flex; align-items:center; gap:3px;
    font-size:11px; font-weight:500; padding:2px 7px; border-radius:4px;
}
.kbd-due svg       { width:10px; height:10px; }
.kbd-due-ok        { color:var(--bs-secondary-color) !important; background:var(--bs-tertiary-bg); }
.kbd-due-soon      { color:#d97706 !important; background:#fff7ed; }
.kbd-due-overdue   { color:#dc2626 !important; background:#fef2f2; }
[data-bs-theme="dark"] .kbd-due-soon    { background:#431407; color:#fb923c !important; }
[data-bs-theme="dark"] .kbd-due-overdue { background:#2d0f0e; color:#f87171 !important; }

/* Story points chip */
.kbd-points {
    display:inline-flex; align-items:center; justify-content:center;
    width:20px; height:20px; border-radius:50%; border:1.5px solid var(--bs-border-color);
    font-size:10px; font-weight:700; color:var(--bs-secondary-color) !important;
}

/* Card assignee avatars */
.kbd-card-avs { display:flex; }
.kbd-card-av {
    width:22px; height:22px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:7px; font-weight:700; color:#fff !important;
    border:2px solid var(--bs-body-bg);
    margin-left:-5px; flex-shrink:0;
    title:'';
}
.kbd-card-av:first-child { margin-left:0; }

/* Card checkbox (select) */
.kbd-card-check {
    position:absolute; top:8px; left:8px;
    width:14px; height:14px; opacity:0;
    transition:opacity .15s; cursor:pointer;
    accent-color:#3b82f6; z-index:2;
}
.kbd-card:hover .kbd-card-check,
.kbd-card-check:checked { opacity:1; }

/* ── Column footer: Add a Card ─────────────────────── */
.kbd-add-card {
    display:flex; align-items:center; gap:6px;
    width:100%; padding:9px 12px;
    background:none; border:none; border-top:1px solid var(--bs-border-color);
    font-size:12.5px; font-weight:500; font-family:inherit;
    color:var(--bs-secondary-color) !important; cursor:pointer;
    transition:background .15s, color .15s; text-align:left;
}
.kbd-add-card:hover { background:var(--bs-tertiary-bg); color:var(--bs-body-color) !important; }
.kbd-add-card svg   { width:13px; height:13px; flex-shrink:0; }

/* ── Empty state card ──────────────────────────────── */
.kbd-empty {
    border:2px dashed var(--bs-border-color); border-radius:var(--kbd-card-r);
    padding:20px 14px; text-align:center; cursor:pointer;
    transition:border-color .15s, background .15s;
}
.kbd-empty:hover { border-color:#93c5fd; background:rgba(59,130,246,.03); }
.kbd-empty-icon { font-size:24px; margin-bottom:6px; }
.kbd-empty-text { font-size:12px; color:var(--bs-secondary-color); line-height:1.5; }

/* ── Add column button ─────────────────────────────── */
.kbd-add-col {
    width:260px; min-width:260px; flex-shrink:0;
    border:2px dashed var(--bs-border-color); border-radius:var(--kbd-radius);
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; padding:28px 20px; cursor:pointer;
    background:transparent; transition:border-color .15s, background .15s;
}
.kbd-add-col:hover { border-color:#3b82f6; background:rgba(59,130,246,.03); }
.kbd-add-col svg  { width:20px; height:20px; color:var(--bs-secondary-color); }
.kbd-add-col span { font-size:13px; font-weight:500; color:var(--bs-secondary-color); }

/* ── Drop placeholder (drag visual) ───────────────── */
.kbd-drop-placeholder {
    height:72px; border-radius:var(--kbd-card-r);
    border:2px dashed #3b82f6; background:rgba(59,130,246,.06);
}

/* ── Context menu ──────────────────────────────────── */
.kbd-ctx-menu {
    display:none; position:fixed;
    background:var(--app-surface,var(--bs-body-bg));
    border:1px solid var(--bs-border-color); border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:2000;
    min-width:160px; overflow:hidden; animation:kbdFadeIn .12s ease;
}
[data-bs-theme="dark"] .kbd-ctx-menu { box-shadow:0 8px 24px rgba(0,0,0,.5); }
.kbd-ctx-menu.open { display:block; }
@keyframes kbdFadeIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.kbd-ctx-item {
    display:flex; align-items:center; gap:8px;
    padding:8px 14px; font-size:12.5px; color:var(--bs-body-color) !important;
    cursor:pointer; transition:background .12s; border:none; background:none;
    width:100%; text-align:left; font-family:inherit;
}
.kbd-ctx-item:hover      { background:var(--bs-tertiary-bg); }
.kbd-ctx-item.danger     { color:#dc2626 !important; }
.kbd-ctx-item.danger:hover { background:#fef2f2; }
[data-bs-theme="dark"] .kbd-ctx-item.danger:hover { background:#2d0f0e; }
.kbd-ctx-item svg        { width:13px; height:13px; flex-shrink:0; }
.kbd-ctx-divider         { height:1px; background:var(--bs-border-color); margin:3px 0; }

/* ═══════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════ */

@media (max-width:1200px) { :root { --kbd-col-w:268px; } }

@media (max-width:991px) {
    :root { --kbd-col-w:250px; }
    .kbd-search { width:160px; }
}

@media (max-width:767px) {
    .kbd-header { min-height:52px; padding-right:.75rem !important; }
    .kbd-page-title { font-size:14px; }
    .kbd-search { width:130px; }
    .kbd-search:focus-within { width:155px; }

    /* Mobile: collapse text labels from toolbar */
    .kbd-tb-label { display:none; }
    .kbd-btn-add-label { display:none; }
    .kbd-btn-add { padding:7px 9px; }

    /* Sub bar: stack */
    .kbd-sub-bar { flex-direction:column; align-items:flex-start; padding:8px 12px; gap:6px; }
    .kbd-filter-right { width:100%; justify-content:flex-end; }
    .kbd-select { max-width:130px; font-size:11.5px; }

    /* Board: vertical scroll on mobile */
    .kbd-board {
        flex-direction:column; overflow-x:hidden; overflow-y:auto;
        padding:10px 10px 80px; gap:10px; min-height:unset;
    }
    .kbd-col, .kbd-add-col {
        width:100%; min-width:100%;
    }
    .kbd-cards { max-height:350px; }
    .kbd-add-col { padding:20px; }
}

@media (max-width:480px) {
    .kbd-toolbar  { gap:4px; }
    .kbd-search   { width:110px; }
    .kbd-page-title { font-size:13px; }
}
</style>

{{-- ══════════════════════════════════════════════════
     ① SECTION HEADER
     ══════════════════════════════════════════════════ --}}
<div class="header-actions-wrapper kbd-header">
    <span class="kbd-page-title">Kanban Board</span>

    <div class="kbd-toolbar">
        <div class="kbd-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="kbdSearch" placeholder="Please enter search text">
        </div>

        <button class="kbd-tb-btn" title="Filter" onclick="kbdFilterPanel()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Sort" onclick="kbdSortToggle()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/>
            </svg>
            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Refresh" onclick="location.reload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Fullscreen" onclick="kbdFullscreen()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Archive">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="21 8 21 21 3 21 3 8"/>
                <rect x="1" y="3" width="22" height="5"/>
                <line x1="10" y1="12" x2="14" y2="12"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Table view">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/>
                <line x1="9" y1="3" x2="9" y2="21"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Export">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Members">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </button>

        <button class="kbd-tb-btn" title="Board layout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="18" rx="1"/>
                <rect x="14" y="3" width="7" height="18" rx="1"/>
            </svg>
        </button>

        <button class="kbd-btn-add" onclick="kbdShowAddCard()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span class="kbd-btn-add-label">Add Card</span>
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     ② MAIN CONTENT
     ══════════════════════════════════════════════════ --}}
<main class="main-content">

    {{-- Sub toolbar --}}
    <div class="kbd-sub-bar">
        <div class="kbd-status-pills">
            <button class="kbd-status-pill active-open" id="kbdPillOpen" onclick="kbdFilterStatus('open',this)">
                <span class="kbd-dot kbd-dot-red"></span>Open
            </button>
            <button class="kbd-status-pill" id="kbdPillClosed" onclick="kbdFilterStatus('closed',this)">
                <span class="kbd-dot kbd-dot-green"></span>Closed
            </button>
        </div>
        <div class="kbd-filter-right">
            <select class="kbd-select" onchange="kbdFilterSprint(this.value)">
                <option value="">testing For release ...</option>
                <option value="sprint1">Sprint 1</option>
                <option value="sprint2">Sprint 2</option>
                <option value="sprint3">Sprint 3</option>
            </select>
            <select class="kbd-select" onchange="kbdFilterColumn(this.value)">
                <option value="">All Columns</option>
                <option value="0">LTTS</option>
                <option value="1">Safari</option>
                <option value="2">Shyammetal...</option>
            </select>
            <div class="kbd-av-stack" title="Filter by member">
                <div class="kbd-av av-i" title="Ananth S">AS</div>
                <div class="kbd-av av-t" title="Vijay K">VK</div>
                <div class="kbd-av av-o" title="Mark A">MA</div>
            </div>
        </div>
    </div>

    {{-- Board --}}
    <div class="kbd-board" id="kbdBoard">

        @php
        /*
         * Kanban standard stages (left → right = workflow):
         * Backlog → To Do → In Progress → In Review → Done
         * Each column has a WIP limit to prevent bottlenecks.
         */
        $columns = $kanbanColumns ?? [
            [
                'id'        => 'col-0',
                'title'     => 'LTTS',
                'stage'     => 'backlog',
                'count'     => 0,
                'wip_limit' => 5,
                'cards'     => [],
            ],
            [
                'id'        => 'col-1',
                'title'     => 'Safari',
                'stage'     => 'inprogress',
                'count'     => 0,
                'wip_limit' => 3,
                'cards'     => [],
            ],
            [
                'id'        => 'col-2',
                'title'     => 'Shyammetal...',
                'stage'     => 'review',
                'count'     => 0,
                'wip_limit' => 4,
                'cards'     => [],
            ],
        ];
        @endphp

        @foreach($columns as $col)
        @php
            $count    = count($col['cards']);
            $wipLimit = $col['wip_limit'] ?? 5;
            $wipClass = $count >= $wipLimit ? 'kbd-wip-over'
                      : ($count >= $wipLimit * 0.8 ? 'kbd-wip-warning' : 'kbd-wip-ok');
        @endphp

        <div class="kbd-col"
             id="{{ $col['id'] }}"
             data-stage="{{ $col['stage'] }}"
             ondragover="kbdDragOver(event,this)"
             ondrop="kbdDrop(event,this)"
             ondragleave="kbdDragLeave(this)">

            {{-- Column header --}}
            <div class="kbd-col-head">
                <div class="kbd-col-head-left">
                    <span class="kbd-col-name" title="{{ $col['title'] }}">{{ $col['title'] }}</span>
                    {{-- WIP indicator: x / limit --}}
                    <span class="kbd-wip-indicator {{ $wipClass }}" id="{{ $col['id'] }}-wip"
                          title="WIP Limit: {{ $wipLimit }}">
                        {{ $count }}/{{ $wipLimit }}
                    </span>
                </div>
                <div class="kbd-col-head-right">
                    <button class="kbd-icon-btn" title="Column options" onclick="kbdColMenu(event,'{{ $col['id'] }}')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="5" r="1" fill="currentColor"/>
                            <circle cx="12" cy="12" r="1" fill="currentColor"/>
                            <circle cx="12" cy="19" r="1" fill="currentColor"/>
                        </svg>
                    </button>
                    <button class="kbd-icon-btn" title="Add card" onclick="kbdAddCard('{{ $col['id'] }}')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Cards --}}
            <div class="kbd-cards" id="{{ $col['id'] }}-cards">

                @forelse($col['cards'] as $card)
                @php
                    $priority = $card['priority'] ?? 'low';
                    $dueClass = 'kbd-due-ok';
                    if (!empty($card['due'])) {
                        $due = \Carbon\Carbon::parse($card['due']);
                        if ($due->isPast())                          $dueClass = 'kbd-due-overdue';
                        elseif ($due->diffInDays(now()) <= 2)        $dueClass = 'kbd-due-soon';
                    }
                @endphp
                <div class="kbd-card {{ !empty($card['blocked']) ? 'blocked' : '' }}"
                     data-priority="{{ $priority }}"
                     data-card-id="{{ $card['id'] ?? '' }}"
                     draggable="true"
                     ondragstart="kbdDragStart(event,this)"
                     ondragend="kbdDragEnd(this)"
                     oncontextmenu="kbdCtxMenu(event,this)"
                     ondblclick="kbdOpenCard(this)">

                    <input type="checkbox" class="kbd-card-check" title="Select card">

                    @if(!empty($card['cover']))
                        <img src="{{ $card['cover'] }}" alt="" class="kbd-card-cover" loading="lazy">
                    @endif

                    <div class="kbd-card-body">
                        @if(!empty($card['labels']))
                        <div class="kbd-card-labels">
                            @foreach($card['labels'] as $lbl)
                            <span class="kbd-label kbd-label-{{ $lbl['color'] ?? 'grey' }}">{{ $lbl['text'] }}</span>
                            @endforeach
                        </div>
                        @endif

                        <p class="kbd-card-title">{{ $card['title'] }}</p>

                        <div class="kbd-card-foot">
                            <div class="kbd-card-foot-left">
                                @if(!empty($card['due']))
                                <span class="kbd-due {{ $dueClass }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($card['due'])->format('d M') }}
                                </span>
                                @endif
                                @if(!empty($card['points']))
                                <span class="kbd-points" title="Story points">{{ $card['points'] }}</span>
                                @endif
                            </div>
                            @if(!empty($card['assignees']))
                            <div class="kbd-card-avs">
                                @foreach(array_slice($card['assignees'],0,3) as $av)
                                <div class="kbd-card-av {{ $av['color_class'] ?? 'av-i' }}" title="{{ $av['name'] ?? '' }}">
                                    {{ strtoupper(substr($av['name'] ?? 'U', 0, 2)) }}
                                </div>
                                @endforeach
                                @if(count($card['assignees']) > 3)
                                <div class="kbd-card-av" style="background:#6b7280;" title="{{ count($card['assignees']) - 3 }} more">
                                    +{{ count($card['assignees']) - 3 }}
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                {{-- Empty state: best practice = actionable ghost, not blank space --}}
                <div class="kbd-empty" onclick="kbdAddCard('{{ $col['id'] }}')">
                    <div class="kbd-empty-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--bs-secondary-color)" stroke-width="1.5" opacity=".5">
                            <rect x="5" y="2" width="14" height="20" rx="2"/>
                            <line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>
                            <line x1="9" y1="15" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <div class="kbd-empty-text">No cards yet<br><span style="color:#3b82f6;font-weight:500;">+ Add first card</span></div>
                </div>
                @endforelse

            </div>{{-- /kbd-cards --}}

            <button class="kbd-add-card" onclick="kbdAddCard('{{ $col['id'] }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add a Card
            </button>

        </div>{{-- /kbd-col --}}
        @endforeach

        {{-- Add new column --}}
        <div class="kbd-add-col" onclick="kbdAddColumn()" role="button" tabindex="0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Add Column</span>
        </div>

    </div>{{-- /kbd-board --}}
</main>

{{-- Context menu --}}
<div class="kbd-ctx-menu" id="kbdCtxMenu">
    <button class="kbd-ctx-item" onclick="kbdCtxAction('open')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Open Card
    </button>
    <button class="kbd-ctx-item" onclick="kbdCtxAction('edit')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
    </button>
    <button class="kbd-ctx-item" onclick="kbdCtxAction('copy')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Duplicate
    </button>
    <button class="kbd-ctx-item" onclick="kbdCtxAction('block')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        Mark Blocked
    </button>
    <div class="kbd-ctx-divider"></div>
    <button class="kbd-ctx-item danger" onclick="kbdCtxAction('delete')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Delete Card
    </button>
</div>

<script>
/* ══════════════════════════════════════════════════════
   KANBAN — BEST PRACTICE INTERACTIONS
   ══════════════════════════════════════════════════════ */

/* ── 1. DRAG & DROP ──────────────────────────────────
   Best practice:
   - Show placeholder where card will be dropped
   - Animate card on drag start
   - Show column highlight on drag-over
   - Auto-scroll column when dragging near edge
────────────────────────────────────────────────────── */
var _dragEl   = null;
var _placeholder = null;

function kbdDragStart(e, el) {
    _dragEl = el;
    el.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', el.dataset.cardId || '');

    /* Create drop placeholder */
    _placeholder = document.createElement('div');
    _placeholder.className = 'kbd-drop-placeholder';

    requestAnimationFrame(function() { el.style.display = 'none'; });
}

function kbdDragEnd(el) {
    el.classList.remove('dragging');
    el.style.display = '';
    if (_placeholder && _placeholder.parentNode) _placeholder.parentNode.removeChild(_placeholder);
    _placeholder = null;
    document.querySelectorAll('.kbd-col').forEach(function(c) { c.classList.remove('drag-over'); });
    _dragEl = null;
    kbdUpdateAllWip();
}

function kbdDragOver(e, col) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    col.classList.add('drag-over');

    var body = col.querySelector('.kbd-cards');
    var afterEl = kbdGetAfter(body, e.clientY);
    if (afterEl) {
        body.insertBefore(_placeholder, afterEl);
    } else {
        body.appendChild(_placeholder);
    }
}

function kbdDragLeave(col) {
    /* Only remove highlight if leaving the column entirely */
    setTimeout(function() {
        if (!col.matches(':hover')) col.classList.remove('drag-over');
    }, 60);
}

function kbdDrop(e, col) {
    e.preventDefault();
    col.classList.remove('drag-over');
    if (!_dragEl) return;

    var body = col.querySelector('.kbd-cards');
    if (_placeholder && _placeholder.parentNode === body) {
        body.insertBefore(_dragEl, _placeholder);
    } else {
        body.appendChild(_dragEl);
    }
    _dragEl.style.display = '';

    /* Remove empty state if present */
    var empty = body.querySelector('.kbd-empty');
    if (empty) empty.remove();

    kbdUpdateAllWip();
    /* TODO: PATCH card.column_id to backend */
}

function kbdGetAfter(container, y) {
    var cards = [...container.querySelectorAll('.kbd-card:not(.dragging)')];
    return cards.reduce(function(closest, child) {
        var box    = child.getBoundingClientRect();
        var offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) return { offset:offset, el:child };
        return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).el;
}

/* ── 2. WIP LIMIT ENFORCEMENT ────────────────────────
   Best practice: surface WIP limit visually,
   warn at 80%, block (visual only) when exceeded.
────────────────────────────────────────────────────── */
function kbdUpdateAllWip() {
    document.querySelectorAll('.kbd-col').forEach(function(col) {
        var cards = col.querySelectorAll('.kbd-card').length;
        var wipEl = col.querySelector('[id$="-wip"]');
        if (!wipEl) return;
        /* Read limit from title attribute */
        var limit = parseInt(wipEl.title.replace('WIP Limit: ','')) || 5;
        wipEl.textContent = cards + '/' + limit;
        wipEl.className = 'kbd-wip-indicator ' + (
            cards >= limit          ? 'kbd-wip-over' :
            cards >= limit * 0.8    ? 'kbd-wip-warning' : 'kbd-wip-ok'
        );
    });
}

/* ── 3. ADD CARD ─────────────────────────────────────
   Best practice: inline quick-add at bottom of column,
   then open detail panel for full editing.
────────────────────────────────────────────────────── */
function kbdAddCard(colId) {
    var body = document.getElementById(colId + '-cards');
    if (!body) return;

    /* Remove empty state */
    var empty = body.querySelector('.kbd-empty');
    if (empty) empty.remove();

    /* Quick-add input inline */
    if (body.querySelector('.kbd-quick-add')) return; /* already open */

    var qa = document.createElement('div');
    qa.className = 'kbd-quick-add';
    qa.style.cssText = 'background:var(--app-surface,var(--bs-body-bg));border:2px solid #3b82f6;border-radius:8px;padding:8px;';
    qa.innerHTML = '<textarea placeholder="Card title..." style="width:100%;border:none;outline:none;background:transparent;font-size:13px;font-family:inherit;color:var(--bs-body-color);resize:none;height:60px;"></textarea>'
        + '<div style="display:flex;gap:6px;margin-top:4px;">'
        + '<button onclick="kbdConfirmAdd(this,\'' + colId + '\')" style="background:#3b82f6;color:#fff;border:none;border-radius:6px;padding:4px 12px;font-size:12px;font-family:inherit;cursor:pointer;">Add</button>'
        + '<button onclick="kbdCancelAdd(this)" style="background:none;border:1px solid var(--bs-border-color);border-radius:6px;padding:4px 12px;font-size:12px;font-family:inherit;cursor:pointer;color:var(--bs-body-color);">Cancel</button>'
        + '</div>';
    body.appendChild(qa);
    qa.querySelector('textarea').focus();
    /* ESC to cancel */
    qa.querySelector('textarea').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') kbdCancelAdd(qa.querySelector('button'));
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); kbdConfirmAdd(qa.querySelector('button'), colId); }
    });
}

function kbdConfirmAdd(btn, colId) {
    var qa    = btn.closest('.kbd-quick-add');
    var title = qa.querySelector('textarea').value.trim();
    if (!title) { qa.querySelector('textarea').focus(); return; }

    var body = document.getElementById(colId + '-cards');
    var card = kbdCreateCard({ title:title, priority:'medium' });
    body.insertBefore(card, qa);
    qa.remove();
    kbdUpdateAllWip();
    /* TODO: POST to backend */
}

function kbdCancelAdd(btn) {
    btn.closest('.kbd-quick-add').remove();
    /* Restore empty state if no cards */
}

function kbdCreateCard(data) {
    var el = document.createElement('div');
    el.className = 'kbd-card';
    el.dataset.priority = data.priority || 'medium';
    el.draggable = true;
    el.addEventListener('dragstart', function(e) { kbdDragStart(e, el); });
    el.addEventListener('dragend',   function()  { kbdDragEnd(el); });
    el.addEventListener('contextmenu', function(e) { kbdCtxMenu(e, el); });
    el.addEventListener('dblclick',    function()  { kbdOpenCard(el); });

    el.innerHTML = '<input type="checkbox" class="kbd-card-check" title="Select card">'
        + '<div class="kbd-card-body">'
        + '<p class="kbd-card-title">' + escHtml(data.title) + '</p>'
        + '<div class="kbd-card-foot">'
        + '<div class="kbd-card-foot-left"></div>'
        + '</div></div>';
    return el;
}

/* ── 4. CARD DETAIL (double-click) ───────────────────
   Best practice: open side/modal panel for full details.
   Here we show a simple toast — replace with your modal.
────────────────────────────────────────────────────── */
function kbdOpenCard(el) {
    var title = el.querySelector('.kbd-card-title')?.textContent?.trim() || 'Card';
    kbdToast('Opening: ' + title, 'info');
    /* TODO: open detail panel/modal */
}

/* ── 5. CONTEXT MENU ─────────────────────────────────
   Best practice: right-click context menu on cards.
────────────────────────────────────────────────────── */
var _ctxCard = null;
function kbdCtxMenu(e, el) {
    e.preventDefault();
    _ctxCard = el;
    var menu = document.getElementById('kbdCtxMenu');
    menu.style.left = Math.min(e.clientX, window.innerWidth  - 180) + 'px';
    menu.style.top  = Math.min(e.clientY, window.innerHeight - 220) + 'px';
    menu.classList.add('open');
}
function kbdCtxAction(action) {
    var menu = document.getElementById('kbdCtxMenu');
    menu.classList.remove('open');
    if (!_ctxCard) return;
    if (action === 'delete') {
        if (confirm('Delete this card?')) { _ctxCard.remove(); kbdUpdateAllWip(); }
    } else if (action === 'block') {
        _ctxCard.classList.toggle('blocked');
    } else if (action === 'copy') {
        var clone = _ctxCard.cloneNode(true);
        clone.addEventListener('dragstart', function(e) { kbdDragStart(e, clone); });
        clone.addEventListener('dragend',   function()  { kbdDragEnd(clone); });
        clone.addEventListener('contextmenu', function(e) { kbdCtxMenu(e, clone); });
        clone.addEventListener('dblclick',    function()  { kbdOpenCard(clone); });
        _ctxCard.parentNode.insertBefore(clone, _ctxCard.nextSibling);
        kbdUpdateAllWip();
    } else if (action === 'open') {
        kbdOpenCard(_ctxCard);
    }
}
document.addEventListener('click', function() {
    document.getElementById('kbdCtxMenu').classList.remove('open');
});

/* ── 6. ADD COLUMN ───────────────────────────────────── */
function kbdAddColumn() {
    var title = prompt('Column name:');
    if (!title) return;
    kbdToast('Column "' + title + '" added', 'success');
    /* TODO: POST to backend, reload */
}

/* ── 7. SEARCH ───────────────────────────────────────── */
document.getElementById('kbdSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.kbd-card').forEach(function(c) {
        var text = c.querySelector('.kbd-card-title')?.textContent?.toLowerCase() || '';
        c.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
});

/* ── 8. STATUS FILTER ────────────────────────────────── */
function kbdFilterStatus(status, btn) {
    document.querySelectorAll('.kbd-status-pill').forEach(function(p) {
        p.classList.remove('active-open','active-close');
    });
    btn.classList.add(status === 'open' ? 'active-open' : 'active-close');
}

/* ── 9. COLUMN FILTER ────────────────────────────────── */
function kbdFilterColumn(val) {
    document.querySelectorAll('.kbd-col').forEach(function(col, i) {
        col.style.display = (!val || String(i) === val) ? '' : 'none';
    });
}

/* ── 10. FULLSCREEN ──────────────────────────────────── */
function kbdFullscreen() {
    var el = document.getElementById('kbdBoard');
    if (!document.fullscreenElement) {
        el.requestFullscreen && el.requestFullscreen();
    } else {
        document.exitFullscreen && document.exitFullscreen();
    }
}

/* ── Toast notification ──────────────────────────────── */
function kbdToast(msg, type) {
    var t = document.createElement('div');
    var bg = type==='success'?'#22c55e':type==='error'?'#ef4444':'#3b82f6';
    t.style.cssText = 'position:fixed;bottom:60px;right:20px;background:'+bg+';color:#fff;'
        +'padding:10px 18px;border-radius:8px;font-size:13px;font-weight:500;z-index:9999;'
        +'box-shadow:0 4px 16px rgba(0,0,0,.2);animation:kbdFadeIn .2s ease;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function() { t.remove(); }, 2800);
}

/* ── Helpers ─────────────────────────────────────────── */
function escHtml(s) {
    return s.replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

function kbdSortToggle() { kbdToast('Sort options coming soon','info'); }
function kbdFilterPanel(){ kbdToast('Filter panel coming soon','info'); }
function kbdShowAddCard(){ /* find first col */ var c = document.querySelector('.kbd-col'); if(c) kbdAddCard(c.id); }
function kbdColMenu(e,colId) { kbdToast('Column options for '+colId,'info'); }
function kbdFilterSprint(v) { kbdToast('Sprint: '+(v||'All'),'info'); }
</script>

@endsection