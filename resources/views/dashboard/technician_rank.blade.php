{{-- @page-meta
{
  "page_no": "LBD-01",
  "file": "index.blade.php",
  "versions": [{ "version": "1.0", "writer": "Claude", "from": "2026-05", "description": "Technician Leaderboard" }]
}
--}}
@extends('layouts.layout1')
@section('title', 'Technician Leaderboard')
@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   LEADERBOARD  –  lbd-*
   Dark mode vars from modetheme.css:
     --dark-primary   #191919   deepest bg
     --dark-secondary #2a2a2a   card surface
     --dark-border    #2a2a2d   all borders
     --dark-hover     #262626   hover bg
     --text-primary   #ffffff   main text
     --text-secondary #e5e7eb   body text
     --text-muted     #757575   secondary text
     --app-bg         #141414   page bg
     --app-surface    #111827   card surface light
   ══════════════════════════════════════════════════════════ */

/* ── Header gradient ──────────────────────────────────── */
.lbd-header {
    background     : linear-gradient(90deg,#fde8e0 0%,#fef3ee 45%,#fdf8f6 72%,var(--app-bg,#f8fafd) 100%);
    border-bottom  : 1px solid #f5ddd5;
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 12px;
    overflow       : hidden;
    padding-right  : 1.25rem !important;
}
[data-bs-theme="dark"] .lbd-header {
    border-bottom-color: var(--dark-border,#2a2a2d) !important;
}
.lbd-back-btn {
    display:inline-flex; align-items:center; justify-content:center;
    background:none; border:none; cursor:pointer;
    color:var(--bs-body-color) !important; padding:4px; border-radius:6px; flex-shrink:0;
}
.lbd-back-btn svg { width:18px; height:18px; }
.lbd-page-title { font-size:15px; font-weight:700; color:var(--bs-body-color); }
[data-bs-theme="dark"] .lbd-page-title { color:var(--text-primary,#fff) !important; }

/* ── Sub header ───────────────────────────────────────── */
.lbd-sub-header {
    padding      : 10px 16px;
    font-size    : 13px; font-weight:600;
    color        : var(--bs-body-color);
    display      : flex; align-items:center; gap:6px;
    border-bottom: 1px solid var(--bs-border-color);
    background   : var(--app-surface,var(--bs-body-bg));
}
[data-bs-theme="dark"] .lbd-sub-header {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-primary,#fff) !important;
}
.lbd-date-range { font-size:12px; font-weight:400; color:var(--bs-secondary-color); }
[data-bs-theme="dark"] .lbd-date-range { color:var(--text-muted,#757575) !important; }

/* ── Body layout ──────────────────────────────────────── */
.lbd-body {
    display              : grid;
    grid-template-columns: 310px 1fr;
    gap                  : 16px;
    padding              : 14px 16px calc(var(--footer-height,30px) + 16px);
    background           : var(--app-bg,#f8f9fa);
    align-items          : start;
    width                : 100%;
    box-sizing           : border-box;
    min-width            : 0;
}
[data-bs-theme="dark"] .lbd-body {
    background: var(--app-bg,#141414) !important;
}

/* ══════════════════════════════════════════════════════
   LEFT PANEL  — white card on grey page bg
   ══════════════════════════════════════════════════════ */
.lbd-left {
    background   : #ffffff;
    border       : 1px solid #e8ecf0;
    border-radius: 12px;
    overflow     : hidden;
    box-shadow   : 0 1px 4px rgba(0,0,0,.05);
    display      : flex;
    flex-direction: column;
    min-width    : 0;
}
[data-bs-theme="dark"] .lbd-left {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    box-shadow  : 0 2px 8px rgba(0,0,0,.5) !important;
}

/* Toolbar */
.lbd-toolbar {
    display      : flex;
    align-items  : center;
    gap          : 7px;
    padding      : 10px 12px;
    border-bottom: 1px solid var(--bs-border-color);
}
[data-bs-theme="dark"] .lbd-toolbar { border-color:var(--dark-border,#2a2a2d) !important; }

.lbd-search-box {
    flex         : 1;
    display      : flex;
    align-items  : center;
    gap          : 6px;
    background   : #f3f4f6;
    border       : 1px solid #e5e7eb;
    border-radius: 8px;
    padding      : 5px 10px;
    transition   : border-color .18s;
}
.lbd-search-box:focus-within { border-color:#93c5fd; background:#fff; }
.lbd-search-box svg   { width:13px; height:13px; color:#9ca3af; flex-shrink:0; }
.lbd-search-box input {
    border:none; outline:none; background:transparent;
    font-size:12.5px; color:#374151; width:100%; font-family:inherit;
}
.lbd-search-box input::placeholder { color:#9ca3af; }
[data-bs-theme="dark"] .lbd-search-box {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .lbd-search-box input { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .lbd-search-box input::placeholder { color:var(--text-muted,#757575) !important; }

.lbd-icon-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    width          : 30px; height:30px;
    border         : 1px solid #e5e7eb;
    border-radius  : 7px;
    background     : #fff;
    cursor         : pointer;
    transition     : background .15s;
    flex-shrink    : 0;
}
.lbd-icon-btn svg { width:14px; height:14px; color:#6b7280; }
.lbd-icon-btn:hover { background:#f3f4f6; }
[data-bs-theme="dark"] .lbd-icon-btn {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .lbd-icon-btn svg { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .lbd-icon-btn:hover { background:var(--dark-hover,#262626) !important; }

/* List scroll */
.lbd-list {
    overflow-y  : auto;
    max-height  : calc(100vh - 210px);
    padding     : 10px 10px 0;
    display     : flex;
    flex-direction: column;
    gap         : 6px;
}
.lbd-list::-webkit-scrollbar       { width:3px; }
.lbd-list::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:3px; }
[data-bs-theme="dark"] .lbd-list::-webkit-scrollbar-thumb { background:var(--dark-border,#2a2a2d); }

/* TOP 3 items — white card with shadow */
.lbd-item {
    display      : flex;
    align-items  : center;
    gap          : 10px;
    padding      : 10px 12px;
    cursor       : pointer;
    border-radius: 10px;
    transition   : box-shadow .15s, background .15s;
    position     : relative;
    background   : #fff;
    border       : 1px solid #f0f0f0;
}
.lbd-item.top3 {
    box-shadow: 0 1px 6px rgba(0,0,0,.08);
    margin-bottom: 2px;
}
.lbd-item.top3:hover {
    box-shadow: 0 3px 14px rgba(0,0,0,.10);
}
/* Below top3 — plain, no card style */
.lbd-item.plain {
    background : transparent;
    border     : none;
    border-radius: 8px;
    box-shadow : none;
    margin-bottom: 0;
    padding    : 8px 12px;
}
.lbd-item.plain:hover { background:#f9fafb; }

/* Active state — red left border + slight bg */
.lbd-item.active {
    border-left  : 3px solid #ef4444 !important;
    padding-left : 9px !important;
    background   : #fff !important;
    box-shadow   : 0 2px 10px rgba(239,68,68,.10) !important;
}

/* Separator between top3 and rest */
.lbd-list-divider {
    height    : 1px;
    background: #f0f0f0;
    margin    : 6px 0 8px;
}
[data-bs-theme="dark"] .lbd-list-divider { background:var(--dark-border,#2a2a2d) !important; }

[data-bs-theme="dark"] .lbd-item {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .lbd-item.plain {
    background: transparent !important;
    border    : none !important;
}
[data-bs-theme="dark"] .lbd-item.plain:hover { background:var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .lbd-item.top3:hover  { box-shadow:0 3px 14px rgba(0,0,0,.4) !important; }
[data-bs-theme="dark"] .lbd-item.active {
    background  : var(--dark-primary,#191919) !important;
    border-left : 3px solid #ef4444 !important;
    box-shadow  : 0 2px 10px rgba(239,68,68,.2) !important;
}

/* Rank circle */
.lbd-rank-circle {
    width:26px; height:26px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:10.5px; font-weight:700; color:#fff !important;
    flex-shrink:0;
}
/* Per-rank colours matching screenshot */
.lbd-rc-1 { background:linear-gradient(135deg,#f59e0b,#d97706); } /* gold */
.lbd-rc-2 { background:linear-gradient(135deg,#22c55e,#16a34a); } /* green */
.lbd-rc-3 { background:linear-gradient(135deg,#6366f1,#4338ca); } /* indigo */
.lbd-rc-5 { background:#d1d5db; color:#6b7280 !important; }
.lbd-rc-6 { background:linear-gradient(135deg,#14b8a6,#0891b2); } /* teal */
.lbd-rc-7 { background:#d1d5db; color:#6b7280 !important; }

/* Avatar */
.lbd-av {
    width:42px; height:42px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; color:#fff !important; overflow:hidden;
}

/* Name / id */
.lbd-name { font-size:13px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.lbd-tid  { font-size:11px; color:#9ca3af; }
[data-bs-theme="dark"] .lbd-name { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .lbd-tid  { color:var(--text-muted,#757575) !important; }

/* Score */
.lbd-score-wrap { margin-left:auto; display:flex; flex-direction:column; align-items:flex-end; gap:2px; flex-shrink:0; }
.lbd-score-pill {
    background:#f0fdf4; color:#16a34a !important;
    border:1px solid #bbf7d0; border-radius:5px;
    font-size:11.5px; font-weight:600; padding:2px 8px;
}
[data-bs-theme="dark"] .lbd-score-pill {
    background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important;
}
.lbd-score-chg {
    display:flex; align-items:center; gap:2px;
    font-size:11px; color:#ef4444 !important;
}
.lbd-score-chg svg { width:10px; height:10px; }

/* ══════════════════════════════════════════════════════
   RIGHT PANEL  — detail
   ══════════════════════════════════════════════════════ */
.lbd-right { display:flex; flex-direction:column; gap:14px; min-width:0; }

/* Profile card */
.lbd-profile {
    background   : #ffffff;
    border       : 1px solid #e8ecf0;
    border-radius: 12px;
    padding      : 16px 20px;
    display      : flex;
    align-items  : center;
    justify-content: space-between;
    gap          : 16px;
    flex-wrap    : wrap;
    box-shadow   : 0 1px 4px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .lbd-profile {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    box-shadow  : none !important;
}
.lbd-profile-left { display:flex; align-items:center; gap:14px; }
.lbd-profile-av {
    width:68px; height:68px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#6366f1,#4338ca);
    border:3px solid #e5e7eb;
    display:flex; align-items:center; justify-content:center;
    font-size:22px; font-weight:700; color:#fff !important; overflow:hidden;
}
[data-bs-theme="dark"] .lbd-profile-av { border-color:var(--dark-border,#2a2a2d) !important; }

.lbd-profile-name {
    font-size:17px; font-weight:700; color:#111827;
    display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;
}
[data-bs-theme="dark"] .lbd-profile-name { color:var(--text-primary,#fff) !important; }

.lbd-top-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:#f0fdf4; color:#16a34a !important;
    border:1px solid #bbf7d0; border-radius:20px;
    font-size:11px; font-weight:600; padding:2px 10px;
}
.lbd-top-badge svg { width:11px; height:11px; }
[data-bs-theme="dark"] .lbd-top-badge {
    background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important;
}

.lbd-meta-row {
    display:flex; align-items:center; gap:6px;
    font-size:12.5px; color:#6b7280; margin-bottom:3px;
}
.lbd-meta-row:last-child { margin-bottom:0; }
.lbd-meta-row svg { width:13px; height:13px; flex-shrink:0; }
[data-bs-theme="dark"] .lbd-meta-row { color:var(--text-muted,#757575) !important; }

/* Gold hexagon rank badge — pure SVG */
.lbd-rank-hex {
    display        : flex;
    flex-direction : column;
    align-items    : center;
    gap            : 4px;
    flex-shrink    : 0;
}
.lbd-hex-wrap {
    position : relative;
    width    : 90px;
    height   : 104px;
    display  : flex;
    align-items    : center;
    justify-content: center;
}
.lbd-hex-wrap svg.hex-bg {
    position: absolute;
    inset   : 0;
    width   : 100%;
    height  : 100%;
}
.lbd-hex-content {
    position       : relative;
    z-index        : 2;
    display        : flex;
    flex-direction : column;
    align-items    : center;
    justify-content: center;
    gap            : 1px;
    margin-top     : 6px;
}
.lbd-hex-num { font-size:22px; font-weight:900; color:#5a2d00 !important; line-height:1; }
.lbd-hex-lbl { font-size:10px; font-weight:700; color:#7c3d00 !important; }
.lbd-ribbon  { display:flex; align-items:center; justify-content:center; margin-top:-2px; }

/* Section title */
.lbd-sec-title { font-size:14px; font-weight:700; color:#111827; margin:0 0 12px; }
[data-bs-theme="dark"] .lbd-sec-title { color:var(--text-primary,#fff) !important; }

/* Stats grid */
.lbd-stats-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:10px; }
.lbd-stats-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:14px; }

.lbd-stat-card {
    background   : #ffffff;
    border       : 1px solid #e8ecf0;
    border-radius: 10px;
    padding      : 14px 16px;
    display      : flex;
    align-items  : flex-start;
    justify-content: space-between;
    gap          : 8px;
    transition   : box-shadow .15s;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
.lbd-stat-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.07); }
[data-bs-theme="dark"] .lbd-stat-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    box-shadow  : none !important;
}
[data-bs-theme="dark"] .lbd-stat-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.5) !important; }

.lbd-stat-lbl { font-size:12px; color:#6b7280; margin-bottom:6px; }
.lbd-stat-val { font-size:18px; font-weight:700; color:#111827; line-height:1.2; }
[data-bs-theme="dark"] .lbd-stat-lbl { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .lbd-stat-val { color:var(--text-primary,#fff) !important; }

/* Stat icon */
.lbd-sico {
    width:36px; height:36px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
}
.lbd-sico svg { width:18px; height:18px; fill:none; stroke-width:1.8; }
.si-red    { background:#fef2f2; } .si-red    svg { stroke:#ef4444; }
.si-green  { background:#f0fdf4; } .si-green  svg { stroke:#22c55e; }
.si-blue   { background:#eff6ff; } .si-blue   svg { stroke:#3b82f6; }
.si-orange { background:#fff7ed; } .si-orange svg { stroke:#f97316; }
.si-purple { background:#faf5ff; } .si-purple svg { stroke:#a855f7; }
[data-bs-theme="dark"] .si-red    { background:#2d0f0e !important; }
[data-bs-theme="dark"] .si-green  { background:#052e16 !important; }
[data-bs-theme="dark"] .si-blue   { background:#0a1a3a !important; }
[data-bs-theme="dark"] .si-orange { background:#431407 !important; }
[data-bs-theme="dark"] .si-purple { background:#2d1b69 !important; }

/* AI card */
.lbd-ai-card {
    background   : #ffffff;
    border       : 1px solid #e8ecf0;
    border-radius: 10px;
    padding      : 14px 16px;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .lbd-ai-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    box-shadow  : none !important;
}
.lbd-ai-title { font-size:14px; font-weight:700; color:#111827; margin-bottom:10px; }
.lbd-ai-body  { font-size:13px; color:#4b5563; line-height:1.72; margin:0; }
[data-bs-theme="dark"] .lbd-ai-title { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .lbd-ai-body  { color:var(--text-muted,#757575) !important; }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width:1100px) {
    .lbd-body   { grid-template-columns:260px 1fr; padding:10px 12px; }
    .lbd-stats-3{ grid-template-columns:1fr 1fr; }
}
@media (max-width:991px) {
    .lbd-body   { grid-template-columns:1fr; padding:10px; gap:12px; }
    .lbd-list   { max-height:280px; }
    .lbd-stats-3{ grid-template-columns:1fr 1fr; }
    .lbd-stats-2{ grid-template-columns:1fr 1fr; }
}
@media (max-width:767px) {
    .lbd-header { padding-right:.75rem !important; min-height:52px; }
    .lbd-page-title { font-size:14px; }
    .lbd-profile { position:relative; }
    .lbd-rank-hex { position:absolute; top:14px; right:14px; }
    .lbd-stats-3{ grid-template-columns:1fr 1fr; gap:8px; }
    .lbd-stats-2{ grid-template-columns:1fr 1fr; }
    .lbd-stat-val { font-size:16px; }
}
@media (max-width:480px) {
    .lbd-body   { padding:8px; }
    .lbd-stats-3{ grid-template-columns:1fr; }
    .lbd-stats-2{ grid-template-columns:1fr; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper lbd-header">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ url()->previous() }}" class="lbd-back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        <span class="lbd-page-title">Technician Leaderboard</span>
    </div>
</div>

{{-- ② MAIN --}}
<main class="main-content" style="overflow-x:hidden;background:var(--app-bg,#f8f9fa);min-width:0;">

    <div class="lbd-sub-header">
        Technician Performance Analytics
        <span class="lbd-date-range">({{ $dateRange ?? '06 Jan 2026 – 06 Apr 2026' }})</span>
    </div>

    <div class="lbd-body">

        {{-- ══ LEFT PANEL ══ --}}
        <div class="lbd-left">

            <div class="lbd-toolbar">
                <div class="lbd-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" placeholder="Search Technician" id="lbdSearch">
                </div>
                <button class="lbd-icon-btn" title="Refresh">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                    </svg>
                </button>
                <button class="lbd-icon-btn" title="Filter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                </button>
                <button class="lbd-icon-btn" title="Sort">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="lbd-list" id="lbdList">
                @php
                $technicians = $technicians ?? [
                    ['rank'=>1,'rc'=>'lbd-rc-1','name'=>'Ananth Designer','id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#6366f1,#4338ca)','ini'=>'AD','active'=>true],
                    ['rank'=>2,'rc'=>'lbd-rc-2','name'=>'Kannan Kutta',   'id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#22c55e,#16a34a)', 'ini'=>'KK'],
                    ['rank'=>3,'rc'=>'lbd-rc-3','name'=>'Jenish Roy',     'id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#f59e0b,#d97706)', 'ini'=>'JR'],
                    ['rank'=>5,'rc'=>'lbd-rc-5','name'=>'Mango Kumar',    'id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#ec4899,#db2777)', 'ini'=>'MK'],
                    ['rank'=>6,'rc'=>'lbd-rc-6','name'=>'Purple Pandya',  'id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#14b8a6,#0891b2)', 'ini'=>'PP'],
                    ['rank'=>7,'rc'=>'lbd-rc-7','name'=>'Ux pilot kvs',   'id'=>'1234AS','score'=>'98.45','chg'=>'12.54','av'=>'linear-gradient(135deg,#f97316,#ea580c)', 'ini'=>'UK'],
                ];
                $top3 = array_filter($technicians, fn($t) => $t['rank'] <= 3);
                $rest = array_filter($technicians, fn($t) => $t['rank'] > 3);
                @endphp

                {{-- Top 3 — white cards with shadow --}}
                @foreach($top3 as $t)
                <div class="lbd-item top3 {{ !empty($t['active']) ? 'active' : '' }}"
                     onclick="lbdPick(this,'{{ $t['name'] }}')"
                     data-name="{{ strtolower($t['name']) }}">
                    <div class="lbd-rank-circle {{ $t['rc'] }}">{{ str_pad($t['rank'],2,'0',STR_PAD_LEFT) }}</div>
                    <div class="lbd-av" style="background:{{ $t['av'] }};">{{ $t['ini'] }}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="lbd-name">{{ $t['name'] }}</div>
                        <div class="lbd-tid">Tech ID : {{ $t['id'] }}</div>
                    </div>
                    <div class="lbd-score-wrap">
                        <span class="lbd-score-pill">{{ $t['score'] }}</span>
                        <span class="lbd-score-chg">
                            {{ $t['chg'] }}%
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <polyline points="5 16 12 19 19 16"/>
                            </svg>
                        </span>
                    </div>
                </div>
                @endforeach

                {{-- Divider between top3 and rest --}}
                @if(count($rest) > 0)
                <div class="lbd-list-divider"></div>
                @endif

                {{-- Rest — plain list style --}}
                @foreach($rest as $t)
                <div class="lbd-item plain {{ !empty($t['active']) ? 'active' : '' }}"
                     onclick="lbdPick(this,'{{ $t['name'] }}')"
                     data-name="{{ strtolower($t['name']) }}">
                    <div class="lbd-rank-circle {{ $t['rc'] }}">{{ str_pad($t['rank'],2,'0',STR_PAD_LEFT) }}</div>
                    <div class="lbd-av" style="background:{{ $t['av'] }};">{{ $t['ini'] }}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="lbd-name">{{ $t['name'] }}</div>
                        <div class="lbd-tid">Tech ID : {{ $t['id'] }}</div>
                    </div>
                    <div class="lbd-score-wrap">
                        <span class="lbd-score-pill">{{ $t['score'] }}</span>
                        <span class="lbd-score-chg">
                            {{ $t['chg'] }}%
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <polyline points="5 16 12 19 19 16"/>
                            </svg>
                        </span>
                    </div>
                </div>
                @endforeach

                {{-- Bottom spacer for scroll breathing room --}}
                <div style="height:10px;"></div>
            </div>
        </div>

        {{-- ══ RIGHT PANEL ══ --}}
        <div class="lbd-right">

            {{-- Profile --}}
            <div class="lbd-profile">
                <div class="lbd-profile-left">
                    <div class="lbd-profile-av">AD</div>
                    <div>
                        <div class="lbd-profile-name">
                            {{ $tech->name ?? 'Ananth Designer' }}
                            <span class="lbd-top-badge">
                                <svg viewBox="0 0 24 24" fill="#16a34a"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                Top Performer
                            </span>
                        </div>
                        <div class="lbd-meta-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Tech ID : {{ $tech->tech_id ?? 'ulux124' }}
                        </div>
                        <div class="lbd-meta-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $tech->email ?? 'ananths@greenitco.com' }}
                        </div>
                        <div class="lbd-meta-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $tech->phone ?? '8300254599' }}
                        </div>
                    </div>
                </div>

                {{-- Gold hexagon rank badge — flat-top hexagon SVG --}}
                <div class="lbd-rank-hex">
                    <div class="lbd-hex-wrap">
                        <svg class="hex-bg" viewBox="0 0 100 116" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="hexGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%"   stop-color="#fde68a"/>
                                    <stop offset="30%"  stop-color="#f59e0b"/>
                                    <stop offset="70%"  stop-color="#d97706"/>
                                    <stop offset="100%" stop-color="#92400e"/>
                                </linearGradient>
                                <linearGradient id="hexInner" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%"   stop-color="#fef3c7"/>
                                    <stop offset="100%" stop-color="#d97706"/>
                                </linearGradient>
                                <filter id="hexGlow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="4" stdDeviation="5" flood-color="#d97706" flood-opacity=".6"/>
                                </filter>
                            </defs>
                            {{-- Flat-top hexagon: cx=50 cy=58 r=48
                                 flat-top points: top-left, top-right, right, bottom-right, bottom-left, left
                                 x = cx + r*cos(angle), y = cy + r*sin(angle)
                                 angles: -30,30,90,150,210,270 → flat top --}}
                            {{-- Outer hex --}}
                            <polygon
                                points="50,10 92,34 92,82 50,106 8,82 8,34"
                                fill="url(#hexGold)"
                                filter="url(#hexGlow)"
                            />
                            {{-- Inner lighter hex ring --}}
                            <polygon
                                points="50,16 87,37 87,79 50,100 13,79 13,37"
                                fill="none"
                                stroke="rgba(255,255,255,0.4)"
                                stroke-width="1.5"
                            />
                            {{-- Center lighter circle --}}
                            <circle cx="50" cy="58" r="28" fill="rgba(255,255,255,0.15)"/>
                        </svg>
                        <div class="lbd-hex-content">
                            <span class="lbd-hex-num">#1</span>
                            <span class="lbd-hex-lbl">Rank</span>
                        </div>
                    </div>
                    {{-- Red ribbon --}}
                    <div class="lbd-ribbon">
                        <svg width="60" height="24" viewBox="0 0 60 24" xmlns="http://www.w3.org/2000/svg">
                            {{-- Left ribbon tail --}}
                            <polygon points="0,0 28,0 30,12 28,24 0,24 4,12" fill="#dc2626"/>
                            {{-- Right ribbon tail --}}
                            <polygon points="60,0 32,0 30,12 32,24 60,24 56,12" fill="#dc2626"/>
                            {{-- Highlights --}}
                            <polygon points="0,0 28,0 30,12 28,24 0,24 4,12" fill="none" stroke="#ef4444" stroke-width="0.5"/>
                            <polygon points="60,0 32,0 30,12 32,24 60,24 56,12" fill="none" stroke="#ef4444" stroke-width="0.5"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Performance Summary --}}
            <div>
                <p class="lbd-sec-title">Performance Summary</p>

                <div class="lbd-stats-3">
                    <div class="lbd-stat-card">
                        <div>
                            <div class="lbd-stat-lbl">Feedback Score</div>
                            <div class="lbd-stat-val">0.00 (29 Tickets)</div>
                        </div>
                        <div class="lbd-sico si-red">
                            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                    </div>
                    <div class="lbd-stat-card">
                        <div>
                            <div class="lbd-stat-lbl">Escalations</div>
                            <div class="lbd-stat-val">2 (30%)</div>
                        </div>
                        <div class="lbd-sico si-green">
                            <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        </div>
                    </div>
                    <div class="lbd-stat-card">
                        <div>
                            <div class="lbd-stat-lbl">SLA Breached</div>
                            <div class="lbd-stat-val">29</div>
                        </div>
                        <div class="lbd-sico si-blue">
                            <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        </div>
                    </div>
                </div>

                <div class="lbd-stats-2">
                    <div class="lbd-stat-card">
                        <div>
                            <div class="lbd-stat-lbl">Avg Response</div>
                            <div class="lbd-stat-val">0.30 Hrs</div>
                        </div>
                        <div class="lbd-sico si-orange">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                    </div>
                    <div class="lbd-stat-card">
                        <div>
                            <div class="lbd-stat-lbl">Not Responded Ticket</div>
                            <div class="lbd-stat-val">26</div>
                        </div>
                        <div class="lbd-sico si-purple">
                            <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI Summary --}}
            <div class="lbd-ai-card">
                <div class="lbd-ai-title">AI Performance Summary</div>
                <p class="lbd-ai-body">{{ $aiSummary ?? 'Enhance feedback collection to gather customer satisfaction insights, currently showing no feedback recorded. Review the 2 escalations to understand root causes and prevention strategies. Enhance feedback collection to gather customer satisfaction insights, currently showing no feedback recorded. Review the 2 escalations to understand root causes and prevention strategies. Enhance feedback collection to gather customer satisfaction insights, currently showing no feedback recorded. Review the 2 escalations to understand root causes and prevention strategies.' }}</p>
            </div>

        </div>{{-- /lbd-right --}}
    </div>{{-- /lbd-body --}}
</main>

<script>
function lbdPick(el, name) {
    document.querySelectorAll('.lbd-item').forEach(function(i){ i.classList.remove('active'); });
    el.classList.add('active');
    /* TODO: AJAX load tech detail */
    console.log('Selected:', name);
}
document.getElementById('lbdSearch').addEventListener('input', function(){
    var q = this.value.toLowerCase();
    document.querySelectorAll('.lbd-item').forEach(function(el){
        el.style.display = (!q || el.dataset.name.includes(q)) ? '' : 'none';
    });
});
</script>
@endsection