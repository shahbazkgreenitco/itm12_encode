{{-- @page-meta { "page_no": "STD-01", "version": "4.0" } --}}
@extends('layouts.layout1')
@section('title', 'Service Ticket Dashboard')
@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   STM DASHBOARD v4 — std-*
   Layout: LEFT (wide) | RIGHT (aside ~280px) — sticky
   Light: --app-bg:#f8f9fa  --app-surface:#fff  --app-border:#dee2e6
   Dark:  --app-bg:#141414  --dark-primary:#191919
          --dark-secondary:#2a2a2a  --dark-border:#2a2a2d
          --dark-hover:#262626  --text-primary:#fff
          --text-secondary:#e5e7eb  --text-muted:#757575
   ═══════════════════════════════════════════════════════ */

/* ── HEADER ─────────────────────────────────────────── */
.std-hdr {
    background:var(--app-surface,#fff);
    border-bottom:1px solid var(--app-border,#dee2e6);
    min-height:57px;
    display:flex !important; align-items:center !important;
    justify-content:space-between !important;
    flex-wrap:nowrap !important; gap:12px;
    padding-right:1.25rem !important;
}
[data-bs-theme="dark"] .std-hdr {
    background:var(--dark-primary,#191919) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
}
.std-hdr-t { display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:var(--app-text,#212529); }
.std-hdr-t a { color:inherit;text-decoration:none;display:inline-flex;align-items:center; }
.std-hdr-t a svg { width:16px;height:16px; }
[data-bs-theme="dark"] .std-hdr-t { color:var(--text-primary,#fff) !important; }
.std-drop {
    display:inline-flex;align-items:center;gap:6px;
    border:1px solid var(--app-border,#dee2e6);border-radius:8px;
    padding:6px 12px;font-size:12.5px;font-family:inherit;
    color:var(--app-text,#212529) !important;background:var(--app-surface,#fff);
    cursor:pointer;white-space:nowrap;
}
.std-drop svg { width:12px;height:12px;color:#9ca3af; }
[data-bs-theme="dark"] .std-drop {
    background:var(--dark-primary,#191919) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
    color:var(--text-secondary,#e5e7eb) !important;
}

/* ── OUTER WRAPPER ───────────────────────────────────── */
.std-wrap {
    display:grid;
    grid-template-columns:1fr 280px;
    gap:16px;
    padding:14px 16px calc(var(--footer-height,30px)+20px);
    background:var(--app-bg,#f8f9fa);
    box-sizing:border-box;
    min-width:0; overflow-x:hidden;
    align-items:start;
}
[data-bs-theme="dark"] .std-wrap { background:var(--app-bg,#141414) !important; }

/* Left & right columns */
.std-left  { min-width:0;display:flex;flex-direction:column;gap:14px; }
.std-right { min-width:0;display:flex;flex-direction:column;gap:14px; }

/* ── CARD BASE ───────────────────────────────────────── */
.sc {
    background:var(--app-surface,#fff);
    border:1px solid var(--app-border,#dee2e6);
    border-radius:12px; overflow:hidden;
    min-width:0;
}
[data-bs-theme="dark"] .sc {
    background:var(--dark-secondary,#2a2a2a) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
}
.sp { padding:14px 16px; }
.sh { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px; }
.st { font-size:13.5px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:center;gap:6px; }
[data-bs-theme="dark"] .st { color:var(--text-primary,#fff) !important; }
.sm { background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;line-height:1;padding:2px 4px; }
.sb { border-bottom:1px solid var(--app-border,#dee2e6); }
[data-bs-theme="dark"] .sb { border-color:var(--dark-border,#2a2a2d) !important; }

/* Legend row */
.lr { display:flex;align-items:center;justify-content:space-between;font-size:12px;padding:4px 0;color:var(--app-text,#212529); }
[data-bs-theme="dark"] .lr { color:var(--text-secondary,#e5e7eb) !important; }
.ll { display:flex;align-items:center;gap:7px; }
.ld { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
.ln { font-weight:600; }

/* Donut inner fill */
.di { fill:var(--app-surface,#fff); }
[data-bs-theme="dark"] .di { fill:var(--dark-secondary,#2a2a2a) !important; }
.di2 { fill:var(--app-surface,#fff); }
[data-bs-theme="dark"] .di2 { fill:var(--dark-primary,#191919) !important; }

/* ═══════════════════════════════════════════════════════
   AI BANNER
   ═══════════════════════════════════════════════════════ */
.ai-banner {
    background:linear-gradient(135deg,#ede9fe 0%,#ddd6fe 55%,#c4b5fd 100%);
    border:1px solid #c4b5fd; border-radius:12px; padding:16px;
}
[data-bs-theme="dark"] .ai-banner {
    background:linear-gradient(135deg,#1e1b4b 0%,#2d2060 100%) !important;
    border-color:#4c1d95 !important;
}
.ai-top { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px; }
.ai-lbl { display:flex;align-items:center;gap:8px;font-size:14px;font-weight:700;color:#3b0764; }
[data-bs-theme="dark"] .ai-lbl { color:#c4b5fd !important; }
.ai-dot {
    width:20px;height:20px;border-radius:50%;
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 0 8px rgba(99,102,241,.5); flex-shrink:0;
}
.ai-dot svg { width:10px;height:10px;fill:#fff; }
.ai-nav button {
    width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,.5);
    border:none;cursor:pointer;font-size:16px;color:#4c1d95;
    display:inline-flex;align-items:center;justify-content:center;
}
[data-bs-theme="dark"] .ai-nav button { background:rgba(255,255,255,.1) !important;color:#c4b5fd !important; }
.ai-tiles { display:grid;grid-template-columns:repeat(3,1fr);gap:10px; }
.ai-tile {
    background:#fff;border-radius:10px;padding:12px 14px;
    position:relative;min-width:0;overflow:hidden;
    box-shadow:0 1px 4px rgba(99,102,241,.12);
}
[data-bs-theme="dark"] .ai-tile { background:rgba(255,255,255,.08) !important;box-shadow:none !important; }
.ai-badge {
    position:absolute;top:10px;right:10px;
    font-size:10px;font-weight:600;padding:2px 7px;border-radius:10px;white-space:nowrap;
}
.ab-g { background:#d1fae5;color:#065f46; }
.ab-b { background:#dbeafe;color:#1e40af; }
.ab-r { background:#fee2e2;color:#991b1b; }
[data-bs-theme="dark"] .ab-g { background:#052e16 !important;color:#4ade80 !important; }
[data-bs-theme="dark"] .ab-b { background:#1e2d4a !important;color:#93c5fd !important; }
[data-bs-theme="dark"] .ab-r { background:#2d0f0e !important;color:#f87171 !important; }
.ai-n { font-size:24px;font-weight:800;color:#1e1b4b;display:flex;align-items:center;gap:5px;margin-bottom:6px; }
[data-bs-theme="dark"] .ai-n { color:#e0d9ff !important; }
.ai-d { font-size:12px;color:#5b21b6;line-height:1.45;margin:0; }
[data-bs-theme="dark"] .ai-d { color:#a78bfa !important; }
.au { color:#22c55e; } .ad { color:#ef4444; }

/* ═══════════════════════════════════════════════════════
   2-COL CHART ROW
   ═══════════════════════════════════════════════════════ */
.chart-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }

/* Donut */
.dw { position:relative;flex-shrink:0; }
.dc { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center; }
.dv { font-size:22px;font-weight:800;color:var(--app-text,#212529);line-height:1; }
.dl { font-size:10px;color:#9ca3af;margin-top:2px; }
[data-bs-theme="dark"] .dv { color:var(--text-primary,#fff) !important; }

/* KPI row */
.kpi-row {
    display:flex;align-items:center;gap:24px;
    padding:10px 0;border-bottom:1px solid var(--app-border,#dee2e6);margin-bottom:10px;
}
[data-bs-theme="dark"] .kpi-row { border-color:var(--dark-border,#2a2a2d) !important; }
.kpi { font-size:14px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:baseline;gap:4px; }
.kl  { font-size:11px;color:#9ca3af;font-weight:400; }
[data-bs-theme="dark"] .kpi { color:var(--text-primary,#fff) !important; }

/* Pill chart */
.pill-chart { display:flex;align-items:flex-end;gap:6px;margin-bottom:8px; }
.pc { display:flex;flex-direction:column;align-items:center;flex:1; }
.pb-wrap { display:flex;flex-direction:column-reverse;gap:3px;align-items:center;width:100%; }
.pb { width:14px;border-radius:7px;min-height:8px;margin:0 auto; }
.plbl { font-size:9px;color:#9ca3af;margin-top:5px;white-space:nowrap; }

/* Bar chart */
.bar-chart { display:flex;align-items:flex-end;gap:6px;height:90px;margin-bottom:8px; }
.bg { flex:1;display:flex;flex-direction:column;align-items:center;gap:3px; }
.bv { font-size:9px;color:#6b7280;font-weight:600; }
.bb { width:100%;border-radius:4px 4px 0 0; }

/* ═══════════════════════════════════════════════════════
   FEEDBACK  — 2-col, plain list (no sub-cards)
   ═══════════════════════════════════════════════════════ */
.fb-2col { display:grid;grid-template-columns:1fr 1fr; }
.fb-col  { padding:14px 16px; }
.fb-col:first-child { border-right:1px solid var(--app-border,#dee2e6); }
[data-bs-theme="dark"] .fb-col:first-child { border-color:var(--dark-border,#2a2a2d) !important; }
.fb-title { display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--app-text,#212529);margin-bottom:12px; }
[data-bs-theme="dark"] .fb-title { color:var(--text-primary,#fff) !important; }
.fb-icon { width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0; }
.fbi-p { background:#d1fae5; } .fbi-n { background:#fee2e2; }
.fb-item { display:flex;align-items:flex-start;gap:10px;margin-bottom:14px; }
.fb-item:last-child { margin-bottom:0; }
.fb-av {
    width:36px;height:36px;border-radius:50%;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:11px;font-weight:700;color:#fff;
}
.fb-stars { color:#f59e0b;font-size:12px;letter-spacing:1px;margin-bottom:2px; }
.fb-grey  { color:#e5e7eb; }
.fb-q  { font-size:12px;color:#374151;font-style:italic;line-height:1.4;margin-bottom:2px; }
.fb-by { font-size:11px;color:#9ca3af; }
[data-bs-theme="dark"] .fb-q  { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .fb-by { color:var(--text-muted,#757575) !important; }

/* ═══════════════════════════════════════════════════════
   LEADERBOARD — inside one card, 4 cols no border
   ═══════════════════════════════════════════════════════ */
.lb-row { display:flex;padding:14px;gap:0; }
.lb-col { flex:1;text-align:center;padding:8px; }
.lb-av {
    width:52px;height:52px;border-radius:50%;
    margin:0 auto 8px;display:flex;align-items:center;
    justify-content:center;font-size:15px;font-weight:700;color:#fff;
}
.lb-n  { font-size:13px;font-weight:600;color:var(--app-text,#212529);margin-bottom:2px; }
.lb-p  { font-size:11px;color:#9ca3af;margin-bottom:4px; }
.lb-c  { font-size:11px;color:#6b7280;margin-bottom:8px; }
[data-bs-theme="dark"] .lb-n { color:var(--text-primary,#fff) !important; }
[data-bs-theme="dark"] .lb-c { color:var(--text-muted,#757575) !important; }
.sla-p { font-size:11px;font-weight:600;padding:3px 12px;border-radius:20px;display:inline-block; }
.slp-g { background:#d1fae5;color:#065f46; }
.slp-y { background:#fef3c7;color:#92400e; }
[data-bs-theme="dark"] .slp-g { background:#052e16 !important;color:#4ade80 !important; }
[data-bs-theme="dark"] .slp-y { background:#3a2a0a !important;color:#fbbf24 !important; }

/* ═══════════════════════════════════════════════════════
   RIGHT ASIDE — AI Rec + SLA + Ticket Stats
   ═══════════════════════════════════════════════════════ */
/* AI Rec */
.rec-i {
    display:flex;align-items:flex-start;gap:8px;
    font-size:12px;color:#6b7280;padding:6px 0;
    border-bottom:1px solid var(--app-border,#dee2e6);
}
.rec-i:last-of-type { border-bottom:none;margin-bottom:10px; }
.rec-i svg { width:14px;height:14px;flex-shrink:0;margin-top:1px;color:#9ca3af; }
[data-bs-theme="dark"] .rec-i { color:var(--text-muted,#757575) !important;border-color:var(--dark-border,#2a2a2d) !important; }
.rec-btns { display:flex;gap:8px; }
.rec-apply {
    flex:1;display:flex;align-items:center;justify-content:center;gap:4px;
    background:#065f46;color:#fff !important;border:none;border-radius:7px;
    padding:8px;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;
}
.rec-dismiss {
    flex:1;display:flex;align-items:center;justify-content:center;gap:4px;
    background:#ef4444;color:#fff !important;border:none;border-radius:7px;
    padding:8px;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;
}

/* SLA */
.sla-n { font-size:18px;font-weight:700;color:var(--app-text,#212529);display:flex;align-items:center;gap:6px;margin-bottom:2px; }
[data-bs-theme="dark"] .sla-n { color:var(--text-primary,#fff) !important; }
.sla-s { display:flex;justify-content:space-between;font-size:11px;color:#9ca3af;margin-bottom:14px; }
.prow { margin-bottom:10px; }
.plbl { display:flex;justify-content:space-between;font-size:12px;color:var(--app-text,#212529);margin-bottom:5px; }
[data-bs-theme="dark"] .plbl { color:var(--text-secondary,#e5e7eb) !important; }
.prog { height:7px;border-radius:4px;background:#f3f4f6;overflow:hidden; }
[data-bs-theme="dark"] .prog { background:var(--dark-hover,#262626) !important; }
.progb { height:100%;border-radius:4px; }

/* Ticket Stats — 3 sub-cards */
.ts-sub {
    background:var(--app-surface,#fff);
    border:1px solid var(--app-border,#dee2e6);
    border-radius:10px;padding:12px 14px;margin-bottom:10px;
}
.ts-sub:last-child { margin-bottom:0; }
[data-bs-theme="dark"] .ts-sub {
    background:var(--dark-primary,#191919) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
}
.ts-t { font-size:13px;font-weight:700;color:var(--app-text,#212529);margin-bottom:8px; }
[data-bs-theme="dark"] .ts-t { color:var(--text-primary,#fff) !important; }

/* MATI glowing badge */
.mati-glow {
    width:46px;height:46px;border-radius:50%;
    background:linear-gradient(135deg,#1a1060,#3730a3,#6366f1);
    display:flex;align-items:center;justify-content:center;
    font-size:9px;font-weight:800;color:#fff;letter-spacing:.5px;flex-shrink:0;
    box-shadow:0 0 16px rgba(99,102,241,.75), 0 0 32px rgba(99,102,241,.3);
}

.perf-r { font-size:12px;color:#6b7280;padding:2px 0; }
[data-bs-theme="dark"] .perf-r { color:var(--text-muted,#757575) !important; }

.src-r { display:flex;align-items:center;font-size:12px;padding:3px 0; }
.src-l { display:flex;align-items:center;gap:7px;color:var(--app-text,#212529); }
[data-bs-theme="dark"] .src-l { color:var(--text-secondary,#e5e7eb) !important; }
.src-d { width:8px;height:8px;border-radius:2px;flex-shrink:0; }
.src-n { font-weight:600;min-width:24px; }
.src-lab { color:#9ca3af;font-size:11.5px; }

/* Top Issues tags — pink/rose */
.itag {
    display:inline-flex;align-items:center;
    background:#fce7f3;color:#be185d !important;
    border-radius:20px;font-size:11.5px;font-weight:500;
    padding:4px 12px;margin:3px 3px 0 0;white-space:nowrap;
}
[data-bs-theme="dark"] .itag { background:#4a0d2e !important;color:#f9a8d4 !important; }

/* Sdot for source donut */
.sdot { fill:var(--app-surface,#fff); }
[data-bs-theme="dark"] .sdot { fill:var(--dark-primary,#191919) !important; }

/* Map */
.map-wrap { border-radius:8px;overflow:hidden; }

/* ── RESPONSIVE ─────────────────────────────────────── */
@media (max-width:1199px) { .std-wrap { grid-template-columns:1fr 250px; } }
@media (max-width:991px) {
    .std-wrap { grid-template-columns:1fr;padding:10px; }
    .chart-row { grid-template-columns:1fr 1fr; }
}
@media (max-width:767px) {
    .std-hdr { padding-right:.75rem !important;min-height:52px; }
    .std-hdr-t { font-size:13px; }
    .chart-row { grid-template-columns:1fr; }
    .ai-tiles { grid-template-columns:1fr; }
    .fb-2col  { grid-template-columns:1fr; }
    .fb-col:first-child { border-right:none;border-bottom:1px solid var(--app-border,#dee2e6); }
    .lb-row { flex-wrap:wrap; }
    .lb-col { min-width:45%; }
    .std-wrap { padding:8px 8px 80px; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper std-hdr">
    <div class="std-hdr-t">
        <a href="{{ url()->previous() }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        Service Ticket Dashboard
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <button class="std-drop">IT Support Department <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
        <button class="std-drop">Select Date Range <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
    </div>
</div>

{{-- ② MAIN —— LEFT + RIGHT grid --}}
<main class="main-content">
<div class="std-wrap">

{{-- ═══════════════════════════ LEFT COLUMN ═══════════════════════════ --}}
<div class="std-left">

    {{-- AI Banner --}}
    <div class="ai-banner">
        <div class="ai-top">
            <div class="ai-lbl">
                <div class="ai-dot"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" fill="white"/></svg></div>
                AI Ticket status summary
            </div>
            <div class="ai-nav" style="display:flex;gap:6px;"><button>‹</button><button>›</button></div>
        </div>
        <div class="ai-tiles">
            <div class="ai-tile">
                <span class="ai-badge ab-g">100 Tickets</span>
                <div class="ai-n">18% <span class="au">↑</span></div>
                <p class="ai-d">Ticket volume increased by 18% today</p>
            </div>
            <div class="ai-tile">
                <span class="ai-badge ab-b">20 Tickets</span>
                <div class="ai-n">40% <span class="au">↑</span></div>
                <p class="ai-d">Tickets resolved within 2 hours</p>
            </div>
            <div class="ai-tile">
                <span class="ai-badge ab-r">15 Tickets</span>
                <div class="ai-n">03 <span class="ad">↓</span></div>
                <p class="ai-d">Technicians are overloaded in 10 technicians</p>
            </div>
        </div>
    </div>

    {{-- Charts Row 1: Daily Overview + Status Trends --}}
    <div class="chart-row">

        {{-- Daily Ticket Overview --}}
        <div class="sc">
            <div class="sp">
                <div class="sh"><span class="st">Daily Ticket Overview</span><button class="sm">⋯</button></div>
                <div class="dw" style="width:160px;height:160px;margin:0 auto 10px;">
                    <svg width="160" height="160" viewBox="0 0 160 160">
                        <g transform="rotate(-90 80 80)">
                            <circle cx="80" cy="80" r="58" fill="none" stroke="#050b3c" stroke-width="28" stroke-dasharray="172 194"/>
                            <circle cx="80" cy="80" r="58" fill="none" stroke="#14b8a6" stroke-width="28" stroke-dasharray="73 293" stroke-dashoffset="-173"/>
                            <circle cx="80" cy="80" r="58" fill="none" stroke="#f59e0b" stroke-width="28" stroke-dasharray="73 293" stroke-dashoffset="-247"/>
                            <circle cx="80" cy="80" r="58" fill="none" stroke="#6366f1" stroke-width="28" stroke-dasharray="48 318" stroke-dashoffset="-321"/>
                        </g>
                        <circle cx="80" cy="80" r="43" class="di"/>
                    </svg>
                    <div class="dc"><div class="dv">500</div><div class="dl">Total</div></div>
                </div>
                <div class="kpi-row">
                    <div class="kpi">18% <span class="au">↑</span><span class="kl"> SLA</span></div>
                    <div class="kpi"><span class="ad">↓</span> 32 min<span class="kl"> Avg. Response</span></div>
                </div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#050b3c;"></span>Open Tickets</div><span class="ln">358</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#14b8a6;"></span>Resolved Tickets</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#f59e0b;"></span>SLA About to Breach</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#6366f1;"></span>SLA Breached</div><span class="ln">15</span></div>
            </div>
        </div>

        {{-- Ticket Status Trends --}}
        <div class="sc">
            <div class="sp">
                <div class="sh"><span class="st">Ticket Status Trends</span><button class="sm">⋯</button></div>
                <div class="pill-chart">
                    @foreach([
                        ['Jun 1',[52,14,10,4]],['Jun 2',[45,18,12,7]],
                        ['Jun 3',[58,12,8,5]], ['Jun 3',[38,20,14,9]],
                        ['Jun 5',[52,16,10,6]],['Jun 6',[55,14,9,5]],
                        ['Jun 7',[48,18,11,7]],
                    ] as [$lbl,$s])
                    <div class="pc">
                        <div class="pb-wrap">
                            @foreach([['#050b3c',$s[0]],['#14b8a6',$s[1]],['#6366f1',$s[2]],['#f59e0b',$s[3]]] as [$c,$h])
                            <div class="pb" style="background:{{$c}};height:{{max(8,round($h*1.3))}}px;"></div>
                            @endforeach
                        </div>
                        <div class="plbl">{{$lbl}}</div>
                    </div>
                    @endforeach
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-bottom:10px;">Last 7 Days (June)</div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#050b3c;"></span>Open Tickets</div><span class="ln">358</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#14b8a6;"></span>Inprogress</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#6366f1;"></span>Resolved</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#f59e0b;"></span>On Hold</div><span class="ln">15</span></div>
            </div>
        </div>

    </div>{{-- /chart-row 1 --}}

    {{-- Charts Row 2: Trend Analytics + Issue Type --}}
    <div class="chart-row">

        {{-- Trend Analytics --}}
        <div class="sc">
            <div class="sp">
                <div class="sh"><span class="st">Trend Analytics</span><button class="sm">⋯</button></div>
                <div class="bar-chart">
                    @foreach([[358,85,'#6b9dd4'],[245,62,'#6b9dd4'],[87,25,'#3b5998'],[50,15,'#2d4a8a'],[86,24,'#2d4a8a'],[40,12,'#1a2f6e']] as [$v,$h,$c])
                    <div class="bg">
                        <div class="bv">{{$v}}</div>
                        <div class="bb" style="background:{{$c}};height:{{$h}}px;"></div>
                    </div>
                    @endforeach
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-bottom:10px;">Last 7 Days</div>
                <div class="lr"><div class="ll">Daily Response Time</div><span class="ln">358</span></div>
                <div class="lr"><div class="ll">Daily Resolution Time</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll">Daily Ticket Volume</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll">Daily SLA</div><span class="ln">15</span></div>
            </div>
        </div>

        {{-- Issue Type --}}
        <div class="sc">
            <div class="sp">
                <div class="sh"><span class="st">Issue Type</span><button class="sm">⋯</button></div>
                <div style="display:flex;justify-content:center;margin-bottom:12px;">
                    <div class="dw" style="width:130px;height:130px;">
                        <svg width="130" height="130" viewBox="0 0 130 130">
                            <g transform="rotate(-90 65 65)">
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#050b3c" stroke-width="24" stroke-dasharray="188 126"/>
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#14b8a6" stroke-width="24" stroke-dasharray="48 266" stroke-dashoffset="-189"/>
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#f59e0b" stroke-width="24" stroke-dasharray="38 276" stroke-dashoffset="-238"/>
                                <circle cx="65" cy="65" r="50" fill="none" stroke="#6366f1" stroke-width="24" stroke-dasharray="40 274" stroke-dashoffset="-277"/>
                            </g>
                            <circle cx="65" cy="65" r="35" class="di"/>
                        </svg>
                        <div class="dc"><div class="dv" style="font-size:17px;">50</div><div class="dl">Total</div></div>
                    </div>
                </div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#050b3c;"></span>VPN issues</div><span class="ln">358</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#14b8a6;"></span>Development</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#f59e0b;"></span>Network down</div><span class="ln">32</span></div>
                <div class="lr"><div class="ll"><span class="ld" style="background:#6366f1;"></span>OS Crash</div><span class="ln">15</span></div>
            </div>
        </div>

    </div>{{-- /chart-row 2 --}}

    {{-- Feedback --}}
    <div class="sc">
        <div class="fb-2col">
            <div class="fb-col">
                <div class="fb-title">
                    <span class="fb-icon fbi-p">😊</span>
                    Top 05 Positive Feedback
                </div>
                @foreach([
                    ['JD','linear-gradient(135deg,#6366f1,#4338ca)','★★★★★'],
                    ['SK','linear-gradient(135deg,#14b8a6,#0891b2)','★★★★★'],
                ] as [$i,$g,$s])
                <div class="fb-item">
                    <div class="fb-av" style="background:{{$g}};">{{$i}}</div>
                    <div>
                        <div class="fb-stars">{{$s}}</div>
                        <div class="fb-q">"Extremely fast resolution for the VPN issue!"</div>
                        <div class="fb-by">John Doe · Technician</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="fb-col">
                <div class="fb-title">
                    <span class="fb-icon fbi-n">😞</span>
                    Top 05 Lowest Feedback
                </div>
                @foreach([
                    ['JD','linear-gradient(135deg,#ef4444,#dc2626)','★'],
                    ['MK','linear-gradient(135deg,#f97316,#ea580c)','★★'],
                ] as [$i,$g,$s])
                <div class="fb-item">
                    <div class="fb-av" style="background:{{$g}};">{{$i}}</div>
                    <div>
                        <div class="fb-stars">{{$s}}<span class="fb-grey">★★★★</span></div>
                        <div class="fb-q">"Extremely fast resolution for the VPN issue!"</div>
                        <div class="fb-by">John Doe · Technician</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Leaderboard --}}
    <div class="sc">
        <div class="sp sb" style="padding-bottom:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="st">Technician leader board</span>
                <a href="#" style="font-size:12px;color:#ef4444;background:#fee2e2;border-radius:20px;padding:4px 14px;text-decoration:none;font-weight:500;">View All</a>
            </div>
        </div>
        <div class="lb-row">
            @foreach([
                ['Ronaldo','01 Place','134 Tickets Resolved','SLA 98.93%','g','RO','linear-gradient(135deg,#f59e0b,#d97706)'],
                ['Jodan',  '02 Place','123 Tickets Resolved','SLA 78.93%','y','JO','linear-gradient(135deg,#6366f1,#4338ca)'],
                ['Kanna',  '03 Place','89 Tickets Resolved', 'SLA 98.93%','g','KA','linear-gradient(135deg,#14b8a6,#0891b2)'],
                ['Abrutis','04 Place','56 Tickets Resolved', 'SLA 88.93%','g','AB','linear-gradient(135deg,#ec4899,#db2777)'],
            ] as [$n,$pl,$cnt,$sla,$sc,$ini,$gr])
            <div class="lb-col">
                <div class="lb-av" style="background:{{$gr}};">{{$ini}}</div>
                <div class="lb-n">{{$n}}</div>
                <div class="lb-p">{{$pl}}</div>
                <div class="lb-c">{{$cnt}}</div>
                <span class="sla-p {{$sc==='g'?'slp-g':'slp-y'}}">{{$sla}}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Map --}}
    <div class="sc">
        <div class="sp sb" style="padding-bottom:12px;">
            <span class="st">Ticket by places</span>
        </div>
        <div style="padding:12px 16px;">
            <div class="map-wrap">
                <svg viewBox="0 0 900 360" width="100%" style="display:block;" xmlns="http://www.w3.org/2000/svg">
                    <rect width="900" height="360" fill="#c8dff5" rx="8"/>
                    <ellipse cx="168" cy="155" rx="112" ry="70" fill="#5b9bd5" opacity=".65"/>
                    <ellipse cx="262" cy="232" rx="70" ry="53" fill="#5b9bd5" opacity=".5"/>
                    <ellipse cx="448" cy="143" rx="155" ry="80" fill="#5b9bd5" opacity=".7"/>
                    <ellipse cx="638" cy="168" rx="96" ry="70" fill="#5b9bd5" opacity=".62"/>
                    <ellipse cx="752" cy="192" rx="74" ry="58" fill="#5b9bd5" opacity=".55"/>
                    <ellipse cx="523" cy="265" rx="70" ry="44" fill="#5b9bd5" opacity=".5"/>
                    <circle cx="370" cy="112" r="17" fill="#ef4444"/>
                    <text x="370" y="117" text-anchor="middle" fill="white" font-size="9" font-weight="bold">11</text>
                    <text x="390" y="103" fill="#1e3a5f" font-size="9" font-weight="600">United Kingdom</text>
                    <circle cx="592" cy="145" r="17" fill="#ef4444"/>
                    <text x="592" y="150" text-anchor="middle" fill="white" font-size="9" font-weight="bold">20</text>
                    <text x="612" y="136" fill="#1e3a5f" font-size="9" font-weight="600">China</text>
                    <circle cx="542" cy="194" r="21" fill="#ef4444"/>
                    <text x="542" y="200" text-anchor="middle" fill="white" font-size="11" font-weight="bold">96</text>
                    <text x="566" y="185" fill="#1e3a5f" font-size="9" font-weight="600">India</text>
                    <circle cx="222" cy="280" r="17" fill="#ef4444"/>
                    <text x="222" y="285" text-anchor="middle" fill="white" font-size="9" font-weight="bold">39</text>
                    <text x="242" y="271" fill="#1e3a5f" font-size="9" font-weight="600">Brazil</text>
                </svg>
            </div>
        </div>
    </div>

</div>{{-- /std-left --}}

{{-- ═══════════════════════════ RIGHT ASIDE ═══════════════════════════ --}}
<div class="std-right">

    {{-- AI Recommended Actions --}}
    <div class="sc">
        <div class="sp">
            <div class="sh">
                <div class="st">
                    <div class="ai-dot"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" fill="white"/></svg></div>
                    AI Recommended actions
                </div>
                <button class="sm">⋯</button>
            </div>
            @foreach([
                ['user',  'Assign 12 unassigned tickets immediately'],
                ['alert', 'Prioritize high priority network tickets'],
                ['clock', 'Escalate SLA risk tickets within 2 hours'],
            ] as [$ic,$txt])
            <div class="rec-i">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    @if($ic==='user')   <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    @elseif($ic==='alert') <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    @else <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    @endif
                </svg>
                {{$txt}}
            </div>
            @endforeach
            <div class="rec-btns">
                <button class="rec-apply">✓ Apply Recommendation</button>
                <button class="rec-dismiss">✕ Dismiss</button>
            </div>
        </div>
    </div>

    {{-- SLA & Ticket Priority --}}
    <div class="sc">
        <div class="sp">
            <div class="sh"><span class="st">SLA & Ticket Priority</span><button class="sm">⋯</button></div>
            <svg viewBox="0 0 240 46" width="100%" height="46" style="display:block;margin-bottom:10px;" preserveAspectRatio="none">
                <polyline points="0,38 40,26 80,32 104,12 145,19 185,9 240,21" fill="none" stroke="#ef4444" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                <circle cx="104" cy="12" r="5" fill="#ef4444"/>
                <circle cx="185" cy="9"  r="5" fill="#ef4444"/>
            </svg>
            <div class="sla-n">24 Tickets <svg viewBox="0 0 24 24" width="14" height="14" fill="#ef4444"><path d="M12 2L2 22h20L12 2z"/></svg></div>
            <div class="sla-s"><span>Breaching in next 2 hours</span><span>16 Tickets Breached</span></div>
            @foreach([['Critical','#ef4444',100,24],['High','#f59e0b',18,4],['Medium','#14b8a6',50,12]] as [$l,$c,$p,$n])
            <div class="prow">
                <div class="plbl"><span>{{$l}}</span><span>{{$n}}</span></div>
                <div class="prog"><div class="progb" style="background:{{$c}};width:{{$p}}%;"></div></div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Ticket Stats --}}
    <div class="sc">
        <div class="sp">
            <div class="sh"><span class="st">Ticket Stats</span><button class="sm">⋯</button></div>

            {{-- Sub 1: AI Performance --}}
            <div class="ts-sub">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div>
                        <div class="ts-t">AI Performance</div>
                        <div class="perf-r">120 :  Tickets Resolved by AI</div>
                        <div class="perf-r">12%  :  AI Contribution</div>
                    </div>
                    <div class="mati-glow">MATI</div>
                </div>
            </div>

            {{-- Sub 2: Source Trend --}}
            <div class="ts-sub">
                <div class="ts-t">Ticket Source Trend</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;">
                        @foreach([
                            ['#050b3c','120','Ticket via Mobile'],
                            ['#6366f1','12', 'Teams'],
                            ['#f59e0b','56', 'Whatsapp'],
                            ['#f97316','12', 'Email'],
                            ['#9ca3af','00', 'Oncall'],
                        ] as [$c,$n,$l])
                        <div class="src-r">
                            <div class="src-l"><span class="src-d" style="background:{{$c}};"></span><span class="src-n">{{$n}}</span></div>
                            <span class="src-lab">:  {{$l}}</span>
                        </div>
                        @endforeach
                    </div>
                    <svg width="56" height="56" viewBox="0 0 56 56" style="flex-shrink:0;">
                        <g transform="rotate(-90 28 28)">
                            <circle cx="28" cy="28" r="20" fill="none" stroke="#050b3c" stroke-width="10" stroke-dasharray="79 46"/>
                            <circle cx="28" cy="28" r="20" fill="none" stroke="#6366f1" stroke-width="10" stroke-dasharray="20 105" stroke-dashoffset="-80"/>
                            <circle cx="28" cy="28" r="20" fill="none" stroke="#f59e0b" stroke-width="10" stroke-dasharray="17 108" stroke-dashoffset="-101"/>
                            <circle cx="28" cy="28" r="20" fill="none" stroke="#f97316" stroke-width="10" stroke-dasharray="8  117" stroke-dashoffset="-119"/>
                            <circle cx="28" cy="28" r="20" fill="none" stroke="#9ca3af" stroke-width="10" stroke-dasharray="1  124" stroke-dashoffset="-128"/>
                        </g>
                        <circle cx="28" cy="28" r="14" class="sdot"/>
                    </svg>
                </div>
            </div>

            {{-- Sub 3: Top Issues --}}
            <div class="ts-sub">
                <div class="ts-t">Top Issues</div>
                <div>
                    @foreach(['VPN Access','Server Issue','OS Crash','OS Crash','Network Issue'] as $t)
                    <span class="itag">{{$t}}</span>
                    @endforeach
                </div>
            </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button class="header-icon-btn">IT Support Department </button>
            <button class="header-icon-btn">Select Date Range</button>
        </div>
    </div>

</div>{{-- /std-right --}}
</div>{{-- /std-wrap --}}
</main>
@endsection
