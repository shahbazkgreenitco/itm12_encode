{{-- @page-meta
{
  "page_no": "STM-01",
  "file": "index.blade.php",
  "versions": [{ "version": "1.0", "writer": "Claude", "from": "2026-05", "description": "Service Ticket Management Dashboard" }]
}
--}}
@extends('layouts.layout1')
@section('title', 'Service Ticket Management')
@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   SERVICE TICKET DASHBOARD  –  stm-*
   modetheme.css vars:
     Light: --app-bg:#f8f9fa  --app-surface:#ffffff  --app-border:#dee2e6
     Dark:  --app-bg:#141414  --dark-primary:#191919  --dark-secondary:#2a2a2a
            --dark-border:#2a2a2d  --dark-hover:#262626
            --text-primary:#fff  --text-secondary:#e5e7eb  --text-muted:#757575
   ══════════════════════════════════════════════════════════ */

/* ── Section header ───────────────────────────────────── */
.stm-header {
    background     : var(--app-surface,#fff);
    border-bottom  : 1px solid var(--app-border,#dee2e6);
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 10px;
    padding-right  : 1.25rem !important;
}
[data-bs-theme="dark"] .stm-header {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.stm-title { font-size:16px; font-weight:700; color:var(--bs-body-color); white-space:nowrap; }
[data-bs-theme="dark"] .stm-title { color:var(--text-primary,#fff) !important; }

.stm-header-btns { display:flex; align-items:center; gap:8px; flex-shrink:0; }
.stm-btn-outline {
    display:inline-flex; align-items:center; gap:5px;
    border:1.5px solid #ef4444; border-radius:8px;
    padding:6px 14px; font-size:12.5px; font-weight:500;
    color:#ef4444 !important; background:transparent;
    cursor:pointer; font-family:inherit; transition:background .15s;
}
.stm-btn-outline:hover { background:rgba(239,68,68,.06); }
.stm-btn-primary {
    display:inline-flex; align-items:center; gap:5px;
    border:none; border-radius:8px;
    padding:6px 14px; font-size:12.5px; font-weight:500;
    color:#fff !important; background:#ef4444;
    cursor:pointer; font-family:inherit; transition:background .15s;
}
.stm-btn-primary:hover { background:#dc2626; }
.stm-btn-primary svg, .stm-btn-outline svg { width:13px; height:13px; }

/* ── Announcement bar ─────────────────────────────────── */
.stm-announce-bar {
    background : #050b3c;
    padding    : 9px 16px;
    font-size  : 12.5px;
    color      : #fff !important;
    display    : flex;
    align-items: center;
    gap        : 6px;
}
.stm-announce-bar svg { width:14px; height:14px; color:rgba(255,255,255,.7); flex-shrink:0; }
[data-bs-theme="dark"] .stm-announce-bar { background:#0a0f2e !important; }

/* ── Page body ────────────────────────────────────────── */
.stm-body {
    padding   : 14px 16px calc(var(--footer-height,30px) + 20px);
    background: var(--app-bg,#f8f9fa);
    display   : flex;
    flex-direction: column;
    gap       : 14px;
}
[data-bs-theme="dark"] .stm-body { background:var(--app-bg,#141414) !important; }

/* ── Card base ────────────────────────────────────────── */
.stm-card {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 12px;
    overflow     : hidden;
}
[data-bs-theme="dark"] .stm-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.stm-card-pad { padding:14px 16px; }
.stm-card-title { font-size:14px; font-weight:700; color:var(--bs-body-color); margin:0 0 2px; }
.stm-card-sub   { font-size:12px; color:var(--bs-secondary-color); margin:0; }
[data-bs-theme="dark"] .stm-card-title { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .stm-card-sub   { color:var(--text-muted,#757575) !important; }

/* ══════════════════════════════════════════════════════
   MATI AI BANNER
   ══════════════════════════════════════════════════════ */
.stm-ai-banner {
    background   : linear-gradient(135deg,#c8a8f8 0%,#b088f0 30%,#9060e8 60%,#7040d0 100%);
    border-radius: 12px;
    padding      : 20px 20px 20px 24px;
    display      : flex;
    align-items  : center;
    justify-content: space-between;
    gap          : 16px;
    position     : relative;
    overflow     : hidden;
    min-height   : 90px;
}
.stm-ai-banner::before {
    content   : '';
    position  : absolute;
    right     : 120px; top:-40px;
    width     : 200px; height:200px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.stm-ai-title { font-size:18px; font-weight:700; color:#fff !important; margin:0 0 4px; }
.stm-ai-sub   { font-size:12px; color:rgba(255,255,255,.8) !important; margin:0; }
/* Robot emoji/illustration */
.stm-ai-robot { font-size:42px; flex-shrink:0; line-height:1; }
.stm-ai-assist-btn {
    display        : inline-flex; align-items:center; gap:5px;
    background     : rgba(255,255,255,.18);
    color          : #fff !important;
    border         : 1.5px solid rgba(255,255,255,.4);
    border-radius  : 8px;
    padding        : 7px 16px; font-size:12.5px; font-weight:500;
    font-family    : inherit; cursor:pointer; white-space:nowrap;
    transition     : background .15s;
    flex-shrink    : 0;
    backdrop-filter: blur(4px);
}
.stm-ai-assist-btn:hover { background:rgba(255,255,255,.28); }

/* ══════════════════════════════════════════════════════
   MAIN 2-COL LAYOUT
   ══════════════════════════════════════════════════════ */
.stm-main-grid {
    display              : grid;
    grid-template-columns: 1fr 380px;
    gap                  : 14px;
    align-items          : start;
}

/* ══════════════════════════════════════════════════════
   MY ASSIGNED TICKETS — stat tiles
   ══════════════════════════════════════════════════════ */
.stm-stat-tiles {
    display              : grid;
    grid-template-columns: repeat(3,1fr);
    gap                  : 10px;
    margin-top           : 12px;
}
.stm-stat-tile {
    border-radius: 10px;
    padding      : 14px 14px 10px;
    position     : relative;
    overflow     : hidden;
    min-height   : 90px;
    display      : flex;
    flex-direction: column;
    justify-content: space-between;
}
/* Tile colour variants */
.stm-tile-pink   { background:linear-gradient(135deg,#fde8e0 0%,#fcd5c8 100%); }
.stm-tile-blue   { background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 100%); }
.stm-tile-green  { background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%); }
[data-bs-theme="dark"] .stm-tile-pink  { background:linear-gradient(135deg,#2d1515 0%,#3a1a1a 100%) !important; }
[data-bs-theme="dark"] .stm-tile-blue  { background:linear-gradient(135deg,#0a1a3a 0%,#0f2044 100%) !important; }
[data-bs-theme="dark"] .stm-tile-green { background:linear-gradient(135deg,#052e16 0%,#063820 100%) !important; }

.stm-tile-label { font-size:11.5px; color:#6b7280; font-weight:500; }
.stm-tile-val   { font-size:24px; font-weight:700; color:#111827; line-height:1.1; }
.stm-tile-icon  {
    position:absolute; right:12px; top:12px;
    width:34px; height:34px; border-radius:8px;
    background:rgba(255,255,255,.5);
    display:flex; align-items:center; justify-content:center;
}
.stm-tile-icon svg { width:17px; height:17px; }
.stm-tile-arrow {
    width:28px; height:28px; border-radius:50%;
    background:#111827; color:#fff !important;
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; align-self:flex-end;
    transition:background .15s;
}
.stm-tile-arrow:hover { background:#374151; }
.stm-tile-arrow svg { width:13px; height:13px; }
[data-bs-theme="dark"] .stm-tile-label { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .stm-tile-val   { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .stm-tile-arrow { background:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .stm-tile-icon  { background:rgba(255,255,255,.08) !important; }

/* ══════════════════════════════════════════════════════
   ANNOUNCEMENTS TABLE
   ══════════════════════════════════════════════════════ */
.stm-ann-table { width:100%; border-collapse:collapse; }
.stm-ann-table th {
    font-size:12px; font-weight:600; color:var(--bs-secondary-color);
    padding:8px 12px; text-align:left; border-bottom:1px solid var(--app-border,#dee2e6);
}
.stm-ann-table td {
    font-size:12.5px; color:var(--bs-body-color);
    padding:9px 12px; border-bottom:1px solid #f3f4f6;
    vertical-align:middle;
}
.stm-ann-table tr:last-child td { border-bottom:none; }
.stm-ann-table tbody tr:hover td { background:var(--bs-tertiary-bg); }
[data-bs-theme="dark"] .stm-ann-table th { color:var(--text-muted,#757575) !important; border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .stm-ann-table td { color:var(--text-secondary,#e5e7eb) !important; border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .stm-ann-table tbody tr:hover td { background:var(--dark-hover,#262626) !important; }

.stm-status-badge {
    display:inline-flex; align-items:center;
    background:#ef4444; color:#fff !important;
    border-radius:5px; font-size:11px; font-weight:600;
    padding:2px 10px; white-space:nowrap;
}
.stm-ann-more {
    display:flex; align-items:center; justify-content:flex-end;
    padding:8px 12px; border-top:1px solid var(--app-border,#dee2e6);
}
[data-bs-theme="dark"] .stm-ann-more { border-color:var(--dark-border,#2a2a2d) !important; }
.stm-more-arrow {
    width:28px; height:28px; border-radius:50%;
    background:#111827; color:#fff !important;
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer;
}
[data-bs-theme="dark"] .stm-more-arrow { background:var(--dark-border,#2a2a2d) !important; }
.stm-more-arrow svg { width:13px; height:13px; }

/* ══════════════════════════════════════════════════════
   BOTTOM ROW  3-col grid
   ══════════════════════════════════════════════════════ */
.stm-bottom-grid {
    display              : grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap                  : 14px;
}

/* ── My Tickets donut chart ───────────────────────────── */
.stm-tickets-inner {
    display    : flex;
    align-items: center;
    gap        : 14px;
    padding    : 12px 14px;
}
.stm-donut-wrap { position:relative; width:130px; height:130px; flex-shrink:0; }
.stm-donut-center {
    position       : absolute;
    top:50%; left:50%;
    transform      : translate(-50%,-50%);
    text-align     : center;
    pointer-events : none;
}
.stm-donut-pct  { font-size:18px; font-weight:700; color:var(--bs-body-color); line-height:1; }
.stm-donut-lbl  { font-size:10px; color:var(--bs-secondary-color); }
[data-bs-theme="dark"] .stm-donut-pct { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .stm-donut-lbl { color:var(--text-muted,#757575) !important; }

.stm-legend { display:flex; flex-direction:column; gap:5px; }
.stm-leg-row {
    display:flex; align-items:center; justify-content:space-between;
    gap:6px; font-size:12px; color:var(--bs-body-color);
}
.stm-leg-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.stm-leg-left { display:flex; align-items:center; gap:5px; }
.stm-leg-val { font-weight:600; font-size:12px; }
.stm-leg-total {
    display:flex; justify-content:space-between;
    font-size:12.5px; font-weight:700; color:var(--bs-body-color);
    border-top:1px solid var(--app-border,#dee2e6);
    padding-top:5px; margin-top:3px;
}
[data-bs-theme="dark"] .stm-leg-row   { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .stm-leg-total {
    color:var(--text-primary,#fff) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
}

/* View All btn */
.stm-view-all {
    font-size:12px; color:#ef4444 !important;
    background:#fef2f2; border:1px solid #fecaca;
    border-radius:6px; padding:3px 10px;
    cursor:pointer; font-family:inherit; transition:background .15s;
}
.stm-view-all:hover { background:#fee2e2; }
[data-bs-theme="dark"] .stm-view-all { background:#2d0f0e !important; border-color:#5a1a18 !important; color:#f87171 !important; }

/* ── Latest Ticket Activity ───────────────────────────── */
.stm-ticket-row {
    display      : flex;
    align-items  : flex-start;
    gap          : 10px;
    padding      : 9px 14px;
    border-bottom: 1px solid #f3f4f6;
    cursor       : pointer;
    transition   : background .15s;
}
.stm-ticket-row:last-child { border-bottom:none; }
.stm-ticket-row:hover { background:var(--bs-tertiary-bg); }
[data-bs-theme="dark"] .stm-ticket-row { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .stm-ticket-row:hover { background:var(--dark-hover,#262626) !important; }

.stm-tkt-num   { font-size:12px; font-weight:600; color:var(--bs-secondary-color); min-width:38px; }
.stm-tkt-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:4px; }
.stm-tkt-title { font-size:12.5px; font-weight:500; color:var(--bs-body-color); margin-bottom:2px; }
.stm-tkt-date  { font-size:11px; color:var(--bs-secondary-color); }
[data-bs-theme="dark"] .stm-tkt-num   { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .stm-tkt-title { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .stm-tkt-date  { color:var(--text-muted,#757575) !important; }

.stm-tkt-status {
    margin-left:auto; flex-shrink:0;
    font-size:10.5px; font-weight:500;
    padding:2px 8px; border-radius:4px;
}
.stm-tkt-s-progress { background:#fff3f0; color:#b45309 !important; }
.stm-tkt-s-onhold   { background:#fef3c7; color:#92400e !important; }
.stm-tkt-s-resolved { background:#ecfdf5; color:#065f46 !important; }
[data-bs-theme="dark"] .stm-tkt-s-progress { background:#3a1a0a !important; color:#fb923c !important; }
[data-bs-theme="dark"] .stm-tkt-s-onhold   { background:#3a2a0a !important; color:#fbbf24 !important; }
[data-bs-theme="dark"] .stm-tkt-s-resolved { background:#0a2a1a !important; color:#34d399 !important; }

/* ── Right column: 24hrs + Knowledge ─────────────────── */
.stm-right-col { display:flex; flex-direction:column; gap:14px; }

/* 24 Hours card */
.stm-hours-card {
    background   : #ef4444;
    border-radius: 12px;
    padding      : 16px 18px;
    display      : flex;
    align-items  : center;
    gap          : 10px;
}
.stm-hours-title { font-size:18px; font-weight:700; color:#fff !important; }
.stm-hours-card svg { width:24px; height:24px; color:#fff; flex-shrink:0; }

/* Knowledge card */
.stm-know-item {
    display    : flex;
    align-items: center;
    gap        : 10px;
    padding    : 10px 14px;
    border-bottom: 1px solid #f3f4f6;
    cursor     : pointer;
    transition : background .15s;
}
.stm-know-item:last-of-type { border-bottom:none; }
.stm-know-item:hover { background:var(--bs-tertiary-bg); }
[data-bs-theme="dark"] .stm-know-item { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .stm-know-item:hover { background:var(--dark-hover,#262626) !important; }

.stm-know-thumb {
    width:52px; height:40px; border-radius:6px;
    object-fit:cover; flex-shrink:0;
    overflow:hidden; display:flex; align-items:center; justify-content:center;
}
.stm-know-title { font-size:12.5px; font-weight:500; color:var(--bs-body-color); margin-bottom:2px; }
.stm-know-date  { font-size:11px; color:var(--bs-secondary-color); }
[data-bs-theme="dark"] .stm-know-title { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .stm-know-date  { color:var(--text-muted,#757575) !important; }

.stm-know-more {
    display:flex; justify-content:flex-end;
    padding:8px 12px; border-top:1px solid var(--app-border,#dee2e6);
}
[data-bs-theme="dark"] .stm-know-more { border-color:var(--dark-border,#2a2a2d) !important; }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width:1199px) {
    .stm-main-grid   { grid-template-columns:1fr 340px; }
    .stm-bottom-grid { grid-template-columns:1fr 1fr; }
    .stm-right-col   { flex-direction:row; flex-wrap:wrap; }
    .stm-right-col > * { flex:1; min-width:220px; }
}
@media (max-width:991px) {
    .stm-main-grid   { grid-template-columns:1fr; }
    .stm-bottom-grid { grid-template-columns:1fr 1fr; }
    .stm-stat-tiles  { grid-template-columns:1fr 1fr 1fr; }
}
@media (max-width:767px) {
    .stm-header      { padding-right:.75rem !important; min-height:52px; }
    .stm-title       { font-size:14px; }
    .stm-body        { padding:10px 10px 80px; }
    .stm-stat-tiles  { grid-template-columns:1fr 1fr; }
    .stm-bottom-grid { grid-template-columns:1fr; }
    .stm-right-col   { flex-direction:column; }
    .stm-ai-banner   { flex-wrap:wrap; }
    .stm-ai-robot    { display:none; }
}
@media (max-width:480px) {
    .stm-stat-tiles   { grid-template-columns:1fr; }
    .stm-header-btns .stm-btn-outline span { display:none; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper stm-header">
    <span class="stm-title">Service Ticket Dashboard</span>
    <div class="stm-header-btns">
        <button class="stm-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <span>My Actions</span>
        </button>
        <button class="stm-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create Ticket
        </button>
    </div>
</div>

{{-- ② MAIN CONTENT --}}
<main class="main-content" style="overflow-x:hidden;">
    <div class="stm-body">

        {{-- Main 2-col grid --}}
        <div class="stm-main-grid">

            {{-- Left: My Assigned Tickets --}}
            <div class="stm-card">
                <div class="stm-card-pad">
                    <p class="stm-card-title">My Assigned Tickets</p>
                    <p class="stm-card-sub">All your IT &amp; Non IT Assets, tracked in real time.</p>
                </div>

                <div class="stm-stat-tiles" style="padding:0 14px 14px;">
                    {{-- Total Tickets --}}
                    <div class="stm-stat-tile stm-tile-pink">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Total Tickets</div>
                        <div class="stm-tile-val">{{ number_format($stats['total'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- In Progress --}}
                    <div class="stm-stat-tile stm-tile-blue">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">In Progress</div>
                        <div class="stm-tile-val">{{ number_format($stats['inprogress'] ?? 8907) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Hold --}}
                    <div class="stm-stat-tile stm-tile-green">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Hold</div>
                        <div class="stm-tile-val">{{ number_format($stats['hold'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Total Tickets --}}
                    <div class="stm-stat-tile stm-tile-pink">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Total Tickets</div>
                        <div class="stm-tile-val">{{ number_format($stats['total'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- In Progress --}}
                    <div class="stm-stat-tile stm-tile-blue">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">In Progress</div>
                        <div class="stm-tile-val">{{ number_format($stats['inprogress'] ?? 8907) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Hold --}}
                    <div class="stm-stat-tile stm-tile-green">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Hold</div>
                        <div class="stm-tile-val">{{ number_format($stats['hold'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Total Tickets --}}
                    <div class="stm-stat-tile stm-tile-pink">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Total Tickets</div>
                        <div class="stm-tile-val">{{ number_format($stats['total'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- In Progress --}}
                    <div class="stm-stat-tile stm-tile-blue">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">In Progress</div>
                        <div class="stm-tile-val">{{ number_format($stats['inprogress'] ?? 8907) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Hold --}}
                    <div class="stm-stat-tile stm-tile-green">
                        <div class="stm-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="stm-tile-label">Hold</div>
                        <div class="stm-tile-val">{{ number_format($stats['hold'] ?? 10293) }}</div>
                        <button class="stm-tile-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Announcements --}}
            <div class="stm-card">
                <div class="stm-card-pad" style="border-bottom:1px solid var(--app-border,#dee2e6);">
                    <p class="stm-card-title">Announcements</p>
                    <p class="stm-card-sub">All your IT requests, tracked in real time.</p>
                </div>
                <table class="stm-ann-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['18 Jul 2025','Schedule Maintenance','IT Department'],
                            ['18 Jul 2025','Schedule Maintenance','IT Department'],
                            ['18 Jul 2025','Schedule Maintenance','IT Department'],
                            ['18 Jul 2025','Schedule Maintenance','IT Department'],
                        ] as [$date,$subj,$dept])
                        <tr>
                            <td style="white-space:nowrap;color:var(--bs-secondary-color);font-size:12px;">{{ $date }}</td>
                            <td style="font-weight:500;">{{ $subj }}</td>
                            <td style="color:var(--bs-secondary-color);font-size:12px;">{{ $dept }}</td>
                            <td><span class="stm-status-badge">Status</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="stm-ann-more">
                    <button class="stm-more-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
            </div>

        </div>{{-- /stm-main-grid --}}

        {{-- Bottom 3-col row --}}
        <div class="stm-bottom-grid">

            {{-- My Tickets donut --}}
            <div class="stm-card">
                <div class="stm-card-pad d-flex align-items-start justify-content-between">
                    <div>
                        <p class="stm-card-title">My Tickets</p>
                        <p class="stm-card-sub">All your IT requests, tracked in real time.</p>
                    </div>
                    <button class="stm-view-all">View All</button>
                </div>
                <div class="stm-tickets-inner">

                    {{-- Donut SVG --}}
                    <div class="stm-donut-wrap">
                        <svg width="130" height="130" viewBox="0 0 130 130">
                            <g transform="rotate(-90 65 65)">
                                {{-- r=50, circumference=314.16 --}}
                                {{-- In Progress 47.35% = 148.9 --}}
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#ef4444" stroke-width="28" stroke-dasharray="148.9 165.26" stroke-dashoffset="0"/>
                                {{-- Open 4.23% = 13.3 --}}
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#3b82f6" stroke-width="28" stroke-dasharray="13.3 300.86" stroke-dashoffset="-150.4"/>
                                {{-- Resolved 16.67% = 52.4 --}}
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#14b8a6" stroke-width="28" stroke-dasharray="52.4 261.76" stroke-dashoffset="-165.2"/>
                                {{-- Closed 33.86% = 106.4 --}}
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#f59e0b" stroke-width="28" stroke-dasharray="106.4 207.76" stroke-dashoffset="-219.1"/>
                            </g>
                            <circle cx="65" cy="65" r="36" fill="var(--app-surface,#fff)"/>
                        </svg>
                        <div class="stm-donut-center">
                            <div class="stm-donut-pct">70%</div>
                            <div class="stm-donut-lbl">In Progress</div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="stm-legend" style="flex:1;">
                        @foreach([
                            ['#ef4444','Inprogress',  358],
                            ['#3b82f6','Open',         32],
                            ['#14b8a6','Resolved',    126],
                            ['#f59e0b','Closed',      256],
                            ['#9ca3af','Waiting for User',  0],
                            ['#6b7280','Waiting for Vendor',0],
                            ['#ef4444','Reopen',        0],
                        ] as [$color,$label,$count])
                        <div class="stm-leg-row">
                            <div class="stm-leg-left">
                                <span class="stm-leg-dot" style="background:{{ $color }};"></span>
                                <span>{{ $label }}</span>
                            </div>
                            <span class="stm-leg-val">{{ $count }}</span>
                        </div>
                        @endforeach
                        <div class="stm-leg-total">
                            <span>Total Tickets</span><span>756</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Latest Ticket Activity --}}
            <div class="stm-card">
                <div class="stm-card-pad" style="border-bottom:1px solid var(--app-border,#dee2e6);">
                    <p class="stm-card-title">Latest Ticket Activity</p>
                </div>
                @foreach([
                    ['#4750','Salary Not Credited','18 Jul 2025, 12:08 PM','#ef4444','In Progress','stm-tkt-s-progress'],
                    ['#3658','Laptop not working', '18 Jul 2025, 12:08 PM','#f59e0b','Onhold',     'stm-tkt-s-onhold'],
                    ['#4391','Macbook Broken',     '18 Jul 2025, 12:08 PM','#22c55e','Resolved',   'stm-tkt-s-resolved'],
                    ['#5121','Salary Not Credited','18 Jul 2025, 12:08 PM','#ef4444','In Progress','stm-tkt-s-progress'],
                ] as [$num,$title,$date,$dot,$status,$cls])
                <div class="stm-ticket-row">
                    <span class="stm-tkt-num">{{ $num }}</span>
                    <span class="stm-tkt-dot" style="background:{{ $dot }};"></span>
                    <div style="flex:1;min-width:0;">
                        <div class="stm-tkt-title">{{ $title }}</div>
                        <div class="stm-tkt-date">{{ $date }}</div>
                    </div>
                    <span class="stm-tkt-status {{ $cls }}">{{ $status }}</span>
                </div>
                @endforeach
            </div>

            {{-- Right col: 24hrs + Knowledge --}}
            <div class="stm-right-col">

                {{-- Knowledge Document --}}
                <div class="stm-card">
                    <div class="stm-card-pad" style="border-bottom:1px solid var(--app-border,#dee2e6);">
                        <p class="stm-card-title">Knowledge Document</p>
                    </div>
                    @foreach([
                        ['Salary Not Credited','18 Jul 2025, 12:08 PM','#ef4444'],
                        ['Salary Not Credited','18 Jul 2025, 12:08 PM','#3b82f6'],
                    ] as [$title,$date,$thumb])
                    <div class="stm-know-item">
                        <div class="stm-know-thumb" style="background:{{ $thumb }}20;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $thumb }}" stroke-width="1.5" style="width:20px;height:20px;">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="stm-know-title">{{ $title }}</div>
                            <div class="stm-know-date">{{ $date }}</div>
                        </div>
                    </div>
                    @endforeach
                    <div class="stm-know-more">
                        <button class="stm-more-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

        </div>{{-- /stm-bottom-grid --}}
    </div>{{-- /stm-body --}}
</main>

@endsection