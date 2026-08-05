{{-- @page-meta
{
  "page_no": "KBL-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Claude",
      "from": "2026-05",
      "reviewer": null,
      "description": "Knowledge Base Listing Page with AI Agent"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', 'Knowledge Base')
@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   KNOWLEDGE BASE  –  kb-*
   ══════════════════════════════════════════════════════════ */

/* ── Section header ───────────────────────────────────── */
.kb-header {
    background     : var(--app-surface, var(--bs-body-bg));
    border-bottom  : 1px solid var(--bs-border-color);
    min-height     : 48px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 10px;
    padding-right  : 1rem !important;
}
[data-bs-theme="dark"] .kb-header { border-color: var(--dark-border,#2a2a2d); }

.kb-page-title {
    font-size  : 14px;
    font-weight: 600;
    color      : var(--bs-body-color);
    white-space: nowrap;
}
.kb-toolbar { display:flex; align-items:center; gap:6px; flex-shrink:0; }
.kb-tb-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    gap            : 5px;
    background     : none;
    border         : 1px solid var(--bs-border-color);
    border-radius  : 8px;
    padding        : 5px 10px;
    font-size      : 12px;
    font-family    : inherit;
    color          : var(--bs-body-color) !important;
    cursor         : pointer;
    white-space    : nowrap;
    transition     : background .15s;
}
.kb-tb-btn:hover { background: var(--bs-tertiary-bg); }
.kb-tb-btn svg   { width:13px; height:13px; color:var(--bs-secondary-color); }
.kb-tb-icon {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    background     : none;
    border         : 1px solid var(--bs-border-color);
    border-radius  : 8px;
    padding        : 6px 8px;
    cursor         : pointer;
    transition     : background .15s;
}
.kb-tb-icon:hover { background: var(--bs-tertiary-bg); }
.kb-tb-icon svg   { width:14px; height:14px; color:var(--bs-secondary-color); display:block; }

/* ── Page body ────────────────────────────────────────── */
.kb-body { padding: 16px 16px calc(var(--footer-height,30px) + 20px); }

/* ══════════════════════════════════════════════════════
   AI AGENT SECTION
   ══════════════════════════════════════════════════════ */
.kb-ai-section {
    background   : var(--app-surface, var(--bs-body-bg));
    border       : 1px solid var(--bs-border-color);
    border-radius: 14px;
    padding      : 28px 24px 20px;
    margin-bottom: 20px;
}
[data-bs-theme="dark"] .kb-ai-section {
    border-color: var(--dark-border,#2a2a2d);
    background  : var(--dark-primary,#191919);
}

/* AI Agent heading */
.kb-ai-heading {
    display        : flex;
    align-items    : center;
    justify-content: center;
    gap            : 8px;
    font-size      : 24px;
    font-weight    : 700;
    color          : var(--bs-body-color);
    margin-bottom  : 18px;
    text-align     : center;
}
.kb-ai-slash {
    display    : inline-flex;
    align-items: center;
    gap        : 1px;
    font-weight: 900;
    font-size  : 28px;
    line-height: 1;
}
.kb-ai-slash span:first-child { color: #ef4444; }
.kb-ai-slash span:last-child  { color: #3b82f6; }

/* Chat input box */
.kb-ai-input-wrap {
    background   : var(--app-surface, var(--bs-body-bg));
    border       : 1px solid #c7d2fe;
    border-radius: 12px;
    padding      : 14px 14px 10px;
    margin-bottom: 18px;
    min-height   : 110px;
    position     : relative;
    display      : flex;
    flex-direction: column;
}
[data-bs-theme="dark"] .kb-ai-input-wrap { border-color: #3730a3; background: var(--dark-secondary,#2a2a2a); }
.kb-ai-input-wrap:focus-within { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.15); }

.kb-ai-textarea {
    width     : 100%;
    border    : none;
    outline   : none;
    background: transparent;
    font-size : 13.5px;
    font-family: inherit;
    color     : var(--bs-body-color);
    resize    : none;
    flex      : 1;
    min-height: 60px;
    line-height: 1.6;
}
.kb-ai-textarea::placeholder { color:var(--bs-secondary-color); opacity:.7; }

.kb-ai-input-footer {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    margin-top     : 8px;
}
.kb-ai-add-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    width          : 26px; height:26px;
    background     : none;
    border         : 1.5px solid var(--bs-border-color);
    border-radius  : 6px;
    cursor         : pointer;
    color          : var(--bs-secondary-color) !important;
    transition     : background .15s;
}
.kb-ai-add-btn:hover { background: var(--bs-tertiary-bg); }
.kb-ai-add-btn svg   { width:13px; height:13px; }

.kb-ai-send-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    width          : 30px; height:30px;
    background     : #050b3c;
    border         : none;
    border-radius  : 8px;
    cursor         : pointer;
    transition     : background .15s;
}
.kb-ai-send-btn:hover { background: #0a1263; }
.kb-ai-send-btn svg   { width:14px; height:14px; color:#fff; }

/* Quick action chips — horizontal row, no border, clean layout */
.kb-ai-chips {
    display  : grid;
    grid-template-columns: repeat(4,1fr);
    gap      : 10px;
}
.kb-ai-chip {
    display    : flex;
    align-items: flex-start;
    gap        : 0;
    background : var(--app-surface, var(--bs-body-bg));
    border     : 1px solid var(--bs-border-color);
    border-radius: 10px;
    padding    : 10px 12px;
    cursor     : pointer;
    flex-direction: column;
    transition : background .15s, border-color .15s;
}
.kb-ai-chip:hover { background: var(--bs-tertiary-bg); border-color: #93c5fd; }
[data-bs-theme="dark"] .kb-ai-chip { border-color: var(--dark-border,#2a2a2d); }

/* Chip photo avatar — circular, realistic */
.kb-ai-chip-av {
    width        : 32px; height:32px; border-radius:50%;
    display      : flex; align-items:center; justify-content:center;
    flex-shrink  : 0; font-size:11px; font-weight:700;
    color        : #fff !important;
    margin-bottom: 8px;
    background-size  : cover;
    background-position: center;
    overflow     : hidden;
}
.kb-chip-av-1 { background: radial-gradient(circle at 35% 35%, #c0392b, #8e1010); }
.kb-chip-av-2 { background: radial-gradient(circle at 35% 35%, #8e44ad, #5b2c6f); }
.kb-chip-av-3 { background: radial-gradient(circle at 35% 35%, #2980b9, #1a5276); }
.kb-chip-av-4 { background: radial-gradient(circle at 35% 35%, #d35400, #922b21); }

/* Chip avatar inner face SVG */
.kb-chip-av-face {
    width:100%; height:100%; display:flex; align-items:center; justify-content:center;
}

.kb-ai-chip-title { font-size:12.5px; font-weight:600; color:var(--bs-body-color); line-height:1.3; margin-bottom:2px; }
.kb-ai-chip-sub   { font-size:11px; color:var(--bs-secondary-color); line-height:1.3; }

/* ══════════════════════════════════════════════════════
   KNOWLEDGE BASE DOCUMENTS SECTION
   ══════════════════════════════════════════════════════ */
.kb-section-title {
    font-size    : 16px;
    font-weight  : 700;
    color        : var(--bs-body-color);
    margin-bottom: 14px;
}

.kb-doc-group { margin-bottom: 20px; }

/* 3-col grid — hero spans 2 cols, side card fills same row height */
.kb-group-grid {
    display              : grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap                  : 10px;
    margin-bottom        : 10px;
    align-items          : stretch;
}

/* Hero card — 2 cols wide, stretches to fill grid row */
.kb-hero-card {
    grid-column  : 1 / 3;
    border-radius: 12px;
    overflow     : hidden;
    position     : relative;
    min-height   : 200px;
    cursor       : pointer;
    display      : flex;
    align-items  : flex-end;
}
.kb-hero-card-bg {
    position  : absolute; inset:0; z-index:0;
    background: linear-gradient(135deg,#c45c3a 0%,#a04530 50%,#7a3020 100%);
}
.kb-hero-card.green .kb-hero-card-bg {
    background: linear-gradient(135deg,#2d7a5c 0%,#1f5e44 50%,#144030 100%);
}
.kb-hero-card-body {
    position : relative; z-index:3;
    padding  : 20px 18px;
    width    : 58%;
}
.kb-hero-card-title {
    font-size:15px; font-weight:700; color:#fff;
    line-height:1.35; margin:0 0 8px;
    text-shadow:0 1px 4px rgba(0,0,0,.4);
}
.kb-hero-card-desc {
    font-size:11.5px; color:rgba(255,255,255,.85); line-height:1.55; margin:0;
}

/* Side doc card — fills full height of hero */
.kb-doc-card {
    background   : var(--app-surface, var(--bs-body-bg));
    border       : 1px solid var(--bs-border-color);
    border-radius: 12px;
    padding      : 12px 14px;
    cursor       : pointer;
    transition   : box-shadow .18s, border-color .18s;
    display      : flex;
    flex-direction: column;
    gap          : 6px;
}
[data-bs-theme="dark"] .kb-doc-card { border-color: var(--dark-border,#2a2a2d); }
.kb-doc-card:hover { box-shadow:0 3px 14px rgba(0,0,0,.08); border-color:#93c5fd; }

.kb-doc-card-meta {
    display:flex; align-items:center; justify-content:space-between; gap:8px;
}
.kb-doc-av {
    width:22px; height:22px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:7px; font-weight:700; color:#fff !important; flex-shrink:0;
}
.kb-av-1 { background:linear-gradient(135deg,#6366f1,#4338ca); }
.kb-av-2 { background:linear-gradient(135deg,#14b8a6,#0891b2); }
.kb-av-3 { background:linear-gradient(135deg,#f59e0b,#d97706); }
.kb-av-4 { background:linear-gradient(135deg,#ef4444,#dc2626); }

.kb-doc-date  { font-size:11.5px; color:var(--bs-secondary-color); white-space:nowrap; }
.kb-doc-title { font-size:13px; font-weight:600; color:var(--bs-body-color); line-height:1.4; margin:0; }
.kb-doc-desc  {
    font-size:12px; color:var(--bs-secondary-color); line-height:1.55; margin:0;
    flex:1;
    display:-webkit-box; -webkit-line-clamp:5; -webkit-box-orient:vertical; overflow:hidden;
}

.kb-small-row {
    display:grid; grid-template-columns:repeat(3,1fr); gap:10px;
}

/* ── Hero card (featured, tall, spans 2 columns) ──────── */
.kb-hero-card {
    grid-column  : 1 / 3;
    grid-row     : 1 / 3;
    border-radius: 12px;
    overflow     : hidden;
    position     : relative;
    min-height   : 200px;
    cursor       : pointer;
}
/* Hero background gradient + image */
.kb-hero-card-bg {
    position  : absolute;
    inset     : 0;
    background: linear-gradient(135deg,#c45c3a 0%,#a04530 50%,#7a3020 100%);
    z-index   : 0;
}
.kb-hero-card-img {
    position  : absolute;
    right     : 0; top:0; bottom:0;
    width     : 55%;
    object-fit: cover;
    object-position: center top;
    z-index   : 2;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 25%);
    mask-image        : linear-gradient(to right, transparent 0%, black 25%);
}
.kb-hero-card-body {
    position : relative;
    z-index  : 3;
    padding  : 20px 16px;
    width    : 60%;
}
.kb-hero-card-title {
    font-size  : 16px;
    font-weight: 700;
    color      : #fff;
    line-height: 1.35;
    margin     : 0 0 8px;
    text-shadow: 0 1px 3px rgba(0,0,0,.3);
}
.kb-hero-card-desc {
    font-size  : 12px;
    color      : rgba(255,255,255,.85);
    line-height: 1.55;
    margin     : 0;
}

/* ── Small doc card ───────────────────────────────────── */
.kb-doc-card {
    background   : var(--app-surface, var(--bs-body-bg));
    border       : 1px solid var(--bs-border-color);
    border-radius: 12px;
    padding      : 12px 14px;
    cursor       : pointer;
    transition   : box-shadow .18s, border-color .18s;
    display      : flex;
    flex-direction: column;
    gap          : 6px;
    overflow     : hidden;
}
[data-bs-theme="dark"] .kb-doc-card { border-color: var(--dark-border,#2a2a2d); }
.kb-doc-card:hover { box-shadow:0 3px 14px rgba(0,0,0,.08); border-color:#93c5fd; }
[data-bs-theme="dark"] .kb-doc-card:hover { box-shadow:0 3px 14px rgba(0,0,0,.4); }

.kb-doc-card-meta {
    display    : flex;
    align-items: center;
    justify-content: space-between;
    gap        : 8px;
}
.kb-doc-av {
    width        : 22px; height:22px; border-radius:50%;
    display      : inline-flex; align-items:center; justify-content:center;
    font-size    : 7px; font-weight:700; color:#fff !important;
    flex-shrink  : 0;
}
.kb-av-1 { background:linear-gradient(135deg,#6366f1,#4338ca); }
.kb-av-2 { background:linear-gradient(135deg,#14b8a6,#0891b2); }
.kb-av-3 { background:linear-gradient(135deg,#f59e0b,#d97706); }
.kb-av-4 { background:linear-gradient(135deg,#ef4444,#dc2626); }

.kb-doc-date  { font-size:11.5px; color:var(--bs-secondary-color); white-space:nowrap; }
.kb-doc-title { font-size:13px; font-weight:600; color:var(--bs-body-color); line-height:1.4; margin:0; }
.kb-doc-desc  { font-size:12px; color:var(--bs-secondary-color); line-height:1.55; margin:0;
                display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }

/* ── Bottom 3-col row for small cards ─────────────────── */
.kb-small-row {
    display              : grid;
    grid-template-columns: repeat(3,1fr);
    gap                  : 10px;
}

/* ── Second hero (green/teal variant) ─────────────────── */
.kb-hero-card.green .kb-hero-card-bg {
    background: linear-gradient(135deg,#2d7a5c 0%,#1f5e44 50%,#144030 100%);
}

/* ══════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════ */
@media (max-width:991px) {
    .kb-group-grid { grid-template-columns:1fr 1fr; }
    .kb-hero-card  { grid-column:1/3; }
    .kb-small-row  { grid-template-columns:1fr 1fr; }
    .kb-ai-chips   { grid-template-columns:1fr 1fr; }
}
@media (max-width:767px) {
    .kb-header     { padding-right:.75rem !important; min-height:44px; }
    .kb-page-title { font-size:13px; }
    .kb-body       { padding:12px 12px 80px; }
    .kb-group-grid { grid-template-columns:1fr; }
    .kb-hero-card  { grid-column:1/2; min-height:170px; }
    .kb-small-row  { grid-template-columns:1fr; }
    .kb-ai-chips   { grid-template-columns:1fr 1fr; gap:8px; }
    .kb-ai-heading { font-size:18px; }
}
@media (max-width:480px) {
    .kb-small-row  { grid-template-columns:1fr 1fr; }
    .kb-ai-chips   { grid-template-columns:1fr; }
}
</style>

{{-- ① SECTION HEADER --}}
<div class="header-actions-wrapper kb-header">
    <span class="kb-page-title">Knowledge Base</span>
    <div class="kb-toolbar">
        <button class="kb-tb-icon" title="Search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
        </button>
        <button class="kb-tb-btn" title="Filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filter
        </button>
        <button class="kb-tb-icon" title="Refresh">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
        </button>
    </div>
</div>

{{-- ② MAIN CONTENT --}}
<main class="main-content" style="overflow-x:hidden;">
<div class="kb-body">

    {{-- ══ AI AGENT SECTION ══ --}}
    <div class="kb-ai-section">

        {{-- Heading: // Your AI Agent --}}
        <div class="kb-ai-heading">
            <span class="kb-ai-slash">
                <span>/</span><span>/</span>
            </span>
            Your AI Agent
        </div>

        {{-- Chat input --}}
        <div class="kb-ai-input-wrap">
            <textarea class="kb-ai-textarea"
                      placeholder="Your AI Agent helps you analyze tickets, reduce SLA risk, and take faster actions.."
                      rows="3"></textarea>
            <div class="kb-ai-input-footer">
                <button class="kb-ai-add-btn" title="Attach">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <button class="kb-ai-send-btn" title="Send">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Quick action chips --}}
        <div class="kb-ai-chips">
            @foreach([
                ['Analyze My Tickets',   'Highlights urgent ones', 'kb-chip-av-1', 'AT'],
                ['SLA Risk Prediction',  'SLA Risk Prediction',   'kb-chip-av-2', 'SR'],
                ['Suggest Next Actions', 'Close with reason',     'kb-chip-av-3', 'SN'],
                ['Draft Responses',      'Internal comments',     'kb-chip-av-4', 'DR'],
            ] as [$title, $sub, $avClass, $initials])
            <div class="kb-ai-chip" onclick="kbAiChip('{{ $title }}')">
                <div class="kb-ai-chip-av {{ $avClass }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="rgba(255,255,255,.9)">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                </div>
                <div class="kb-ai-chip-title">{{ $title }}</div>
                <div class="kb-ai-chip-sub">{{ $sub }}</div>
            </div>
            @endforeach
        </div>

    </div>{{-- /kb-ai-section --}}

    {{-- ══ KNOWLEDGE BASE DOCUMENTS ══ --}}
    <h2 class="kb-section-title">Knowledge Base Document</h2>

    @php
    $docGroups = $documentGroups ?? [
        [
            'hero' => [
                'title'   => "What is Mai AI in AMG ITM?\nHow it works?",
                'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their.',
                'bg'      => 'default',
                'image'   => null,
            ],
            'side' => [
                [
                    'title'   => 'Why is IT Asset Management important?',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-1',
                ],
            ],
            'bottom' => [
                [
                    'title'   => 'Why IT Asset Management Matters',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-2',
                ],
                [
                    'title'   => 'AI-Powered IT Asset Management',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-3',
                ],
                [
                    'title'   => 'Getting Started with ITM',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-1',
                ],
            ],
        ],
        [
            'hero' => [
                'title'   => "What is Mai AI in AMG ITM?\nHow it works?",
                'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their.',
                'bg'      => 'green',
                'image'   => null,
            ],
            'side' => [
                [
                    'title'   => 'Why is IT Asset Management important?',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-3',
                ],
            ],
            'bottom' => [
                [
                    'title'   => 'Define asset categories and lifecycle rules',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-2',
                ],
                [
                    'title'   => 'Getting Started with ITM',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-4',
                ],
                [
                    'title'   => 'Continuously monitor and optimize',
                    'desc'    => 'IT Asset Management (ITM) is the practice of tracking and managing an organization\'s IT assets throughout their entire lifecycle—from procurement and deployment to',
                    'date'    => 'January 02, 2025',
                    'av'      => 'kb-av-1',
                ],
            ],
        ],
    ];
    @endphp

    @foreach($docGroups as $group)
    <div class="kb-doc-group">

        {{-- Main grid: hero (2 cols, 2 rows) + side card --}}
        <div class="kb-group-grid">

            {{-- Hero card --}}
            <div class="kb-hero-card {{ $group['hero']['bg'] === 'green' ? 'green' : '' }}"
                 onclick="window.location='#'">
                <div class="kb-hero-card-bg"></div>

                {{-- Placeholder person illustration --}}
                <div style="position:absolute;right:0;top:0;bottom:0;width:55%;z-index:2;
                     -webkit-mask-image:linear-gradient(to right,transparent 0%,black 25%);
                     mask-image:linear-gradient(to right,transparent 0%,black 25%);">
                    <svg viewBox="0 0 200 220" width="100%" height="100%"
                         preserveAspectRatio="xMidYMid slice"
                         xmlns="http://www.w3.org/2000/svg">
                        <!-- Person silhouette -->
                        <ellipse cx="100" cy="70" rx="40" ry="44"
                                 fill="{{ $group['hero']['bg'] === 'green' ? 'rgba(255,255,255,.18)' : 'rgba(255,255,255,.15)' }}"/>
                        <path d="M20,220 Q20,140 100,140 Q180,140 180,220 Z"
                              fill="{{ $group['hero']['bg'] === 'green' ? 'rgba(255,255,255,.12)' : 'rgba(255,255,255,.1)' }}"/>
                        <!-- Face highlights -->
                        <ellipse cx="85" cy="60" rx="8" ry="6" fill="rgba(255,255,255,.06)"/>
                        <ellipse cx="115" cy="60" rx="8" ry="6" fill="rgba(255,255,255,.06)"/>
                    </svg>
                </div>

                <div class="kb-hero-card-body">
                    <h3 class="kb-hero-card-title">
                        {!! nl2br(e($group['hero']['title'])) !!}
                    </h3>
                    <p class="kb-hero-card-desc">{{ $group['hero']['desc'] }}</p>
                </div>
            </div>

            {{-- Side card (top right) --}}
            @foreach($group['side'] as $sideCard)
            <div class="kb-doc-card" onclick="window.location='#'">
                <div class="kb-doc-card-meta">
                    <span class="kb-doc-av {{ $sideCard['av'] }}">
                        {{ strtoupper(substr($sideCard['title'],0,2)) }}
                    </span>
                    <span class="kb-doc-date">{{ $sideCard['date'] }}</span>
                </div>
                <h4 class="kb-doc-title">{{ $sideCard['title'] }}</h4>
                <p class="kb-doc-desc">{{ $sideCard['desc'] }}</p>
            </div>
            @endforeach

        </div>{{-- /kb-group-grid --}}

        {{-- Bottom 3 small cards row --}}
        <div class="kb-small-row">
            @foreach($group['bottom'] as $card)
            <div class="kb-doc-card" onclick="window.location='#'">
                <div class="kb-doc-card-meta">
                    <span class="kb-doc-av {{ $card['av'] }}">
                        {{ strtoupper(substr($card['title'],0,2)) }}
                    </span>
                    <span class="kb-doc-date">{{ $card['date'] }}</span>
                </div>
                <h4 class="kb-doc-title">{{ $card['title'] }}</h4>
                <p class="kb-doc-desc">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>

    </div>{{-- /kb-doc-group --}}
    @endforeach

</div>{{-- /kb-body --}}
</main>

<script>
function kbAiChip(title) {
    var ta = document.querySelector('.kb-ai-textarea');
    if (ta) {
        ta.value = title;
        ta.focus();
    }
}
</script>

@endsection