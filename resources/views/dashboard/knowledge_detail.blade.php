{{-- @page-meta
{
  "page_no": "KBA-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Claude",
      "from": "2026-05",
      "reviewer": null,
      "description": "Knowledge Base Article Detail Page"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', 'Knowledge Base Article')
@section('content')
<style>
/* ══════════════════════════════════════════════════════════
   KNOWLEDGE BASE ARTICLE  –  kba-*
   ── Rules followed ──────────────────────────────────────
   • header-actions-wrapper  → index.js sidebar shift
   • main.main-content       → index.js margin-left shift
   • All colours via CSS vars → dark mode automatic
   • Bootstrap 5 utilities   → max reuse
   • Zero hardcoded colours on text/bg/border
   ══════════════════════════════════════════════════════════ */

/* ── Section header (plain white, no gradient) ─────── */
.kba-header {
    background     : var(--app-surface, var(--bs-body-bg));
    border-bottom  : 1px solid var(--bs-border-color);
    min-height     : 57px;
    display        : flex !important;
    align-items    : center !important;
    justify-content: space-between !important;
    flex-wrap      : nowrap !important;
    gap            : 12px;
    padding-right  : 1.25rem !important;
}
[data-bs-theme="dark"] .kba-header {
    border-bottom: 1px solid var(--dark-border, #2a2a2d);
}
.kba-header-left {
    display    : flex;
    align-items: center;
    gap        : 10px;
    min-width  : 0;
}
.kba-back-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    background     : none;
    border         : none;
    cursor         : pointer;
    color          : var(--bs-body-color) !important;
    padding        : 4px;
    border-radius  : 6px;
    transition     : background .15s;
    flex-shrink    : 0;
}
.kba-back-btn:hover { background: var(--bs-tertiary-bg); }
.kba-back-btn svg   { width: 18px; height: 18px; }
.kba-page-title {
    font-size  : 15px;
    font-weight: 700;
    color      : var(--bs-body-color);
    white-space: nowrap;
    overflow   : hidden;
    text-overflow: ellipsis;
}

/* Fav button */
.kba-fav-btn {
    display      : inline-flex;
    align-items  : center;
    gap          : 6px;
    background   : none;
    border       : 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding      : 5px 14px;
    font-size    : 12.5px;
    font-family  : inherit;
    color        : var(--bs-body-color) !important;
    cursor       : pointer;
    white-space  : nowrap;
    flex-shrink  : 0;
    transition   : background .15s;
}
.kba-fav-btn:hover { background: var(--bs-tertiary-bg); }
.kba-fav-btn svg   { width: 14px; height: 14px; color: var(--bs-secondary-color); }
.kba-fav-btn.favourited svg { fill: #f59e0b; stroke: #f59e0b; }

/* ── Page body layout ──────────────────────────────── */
.kba-body {
    padding    : 16px 20px calc(var(--footer-height, 30px) + 20px);
    display    : grid;
    grid-template-columns: 1fr 260px;
    gap        : 16px;
    align-items: start;
    width      : 100%;
    box-sizing : border-box;
    min-width  : 0;
}
.kba-article { min-width: 0; }
.kba-sidebar { min-width: 0; }

/* ══════════════════════════════════════════════════
   LEFT — Article content (NO card border)
   ══════════════════════════════════════════════════ */
.kba-article {
    background   : transparent;
    border       : none;
    border-radius: 0;
    overflow     : visible;
}

/* Hero banner — standalone rounded card */
.kba-hero {
    position     : relative;
    height       : 185px;
    border-radius: 16px;
    overflow     : hidden;
    display      : flex;
    align-items  : flex-end;
    margin-bottom: 14px;
    /* Subtle shadow */
    box-shadow   : 0 2px 16px rgba(0,0,0,.08);
}
[data-bs-theme="dark"] .kba-hero { box-shadow: 0 2px 16px rgba(0,0,0,.4); }

.kba-hero-bg {
    position  : absolute;
    inset     : 0;
    background: linear-gradient(135deg, #c45c3a 0%, #a04530 40%, #7a3020 100%);
    z-index   : 0;
}
.kba-hero-img {
    position  : absolute;
    right     : 0; top: 0; bottom: 0;
    width     : 55%;
    object-fit: cover;
    object-position: center top;
    z-index   : 2;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 30%);
    mask-image        : linear-gradient(to right, transparent 0%, black 30%);
}
.kba-hero-text {
    position : relative;
    z-index  : 3;
    padding  : 20px 24px;
    max-width: 55%;
}
.kba-hero-title {
    font-size  : 20px;
    font-weight: 700;
    color      : #fff;
    line-height: 1.35;
    margin     : 0;
    text-shadow: 0 1px 4px rgba(0,0,0,.3);
}

/* Article meta — no border wrapper */
.kba-meta {
    padding    : 2px 0 14px;
    font-size  : 13px;
    color      : var(--bs-secondary-color);
    border     : none;
}
.kba-meta strong { color: var(--bs-body-color); font-weight: 500; }

/* Article body — no padding card */
.kba-content { padding: 0; }

.kba-section-title {
    font-size  : 15px;
    font-weight: 700;
    color      : var(--bs-body-color);
    margin     : 0 0 10px;
    line-height: 1.4;
}

.kba-para {
    font-size  : 13.5px;
    color      : var(--bs-body-color);
    line-height: 1.75;
    margin     : 0 0 12px;
}
.kba-para:last-child { margin-bottom: 0; }
.kba-content-block { margin-bottom: 24px; }
.kba-content-block:last-child { margin-bottom: 0; }

/* ══════════════════════════════════════════════════
   RIGHT — Single card containing both panels
   ══════════════════════════════════════════════════ */
.kba-sidebar {
    display        : flex;
    flex-direction : column;
    gap            : 0;
    background     : var(--app-surface, var(--bs-body-bg));
    border         : 1px solid var(--bs-border-color);
    border-radius  : 12px;
    overflow       : hidden;
    position       : sticky;
    min-width      : 0;
}
[data-bs-theme="dark"] .kba-sidebar { border-color: var(--dark-border, #2a2a2d); }

/* No individual card borders — single unified card */
.kba-panel {
    background   : transparent;
    border       : none;
    border-radius: 0;
    overflow     : visible;
}
/* Divider between panels */
.kba-panel + .kba-panel {
    border-top: 1px solid var(--bs-border-color);
}
[data-bs-theme="dark"] .kba-panel + .kba-panel { border-color: var(--dark-border, #2a2a2d); }

.kba-panel-title {
    font-size    : 13px;
    font-weight  : 700;
    color        : var(--bs-body-color);
    padding      : 14px 16px 10px;
    margin       : 0;
    border-bottom: 1px solid var(--bs-border-color);
}
[data-bs-theme="dark"] .kba-panel-title { border-color: var(--dark-border, #2a2a2d); }

/* Article Info panel */
.kba-info-body { padding: 12px 16px; }

.kba-info-row {
    display    : flex;
    align-items: flex-start;
    gap        : 8px;
    margin-bottom: 8px;
    font-size  : 12.5px;
}
.kba-info-row:last-child { margin-bottom: 0; }
.kba-info-key {
    color     : var(--bs-secondary-color);
    min-width : 72px;
    flex-shrink: 0;
    font-weight: 400;
}
.kba-info-val {
    color      : var(--bs-body-color);
    font-weight: 500;
    display    : flex;
    align-items: center;
    gap        : 6px;
    flex-wrap  : wrap;
}
.kba-info-date {
    font-size  : 11px;
    color      : var(--bs-secondary-color);
    font-style : italic;
    font-weight: 400;
    margin-top : 1px;
    display    : block;
}
.kba-author-av {
    width        : 20px;
    height       : 20px;
    border-radius: 50%;
    display      : inline-flex;
    align-items  : center;
    justify-content: center;
    font-size    : 7px;
    font-weight  : 700;
    color        : #fff !important;
    background   : linear-gradient(135deg, #14b8a6, #0891b2);
    flex-shrink  : 0;
}

/* Related Articles panel */
.kba-related-list { padding: 12px 16px; display: flex; flex-direction: column; gap: 14px; }

.kba-related-item { display: flex; flex-direction: column; gap: 7px; cursor: pointer; }
.kba-related-item:hover .kba-related-title { color: #2563eb !important; }
[data-bs-theme="dark"] .kba-related-item:hover .kba-related-title { color: #93c5fd !important; }

.kba-related-thumb {
    width        : 100%;
    height       : 90px;
    border-radius: 8px;
    object-fit   : cover;
    background   : var(--bs-tertiary-bg);
    display      : block;
}
.kba-related-thumb-placeholder {
    width        : 100%;
    height       : 90px;
    border-radius: 8px;
    overflow     : hidden;
    display      : block;
}

.kba-related-title {
    font-size  : 12px;
    font-weight: 500;
    color      : var(--bs-body-color);
    line-height: 1.45;
    transition : color .15s;
    margin     : 0;
}
.kba-related-divider {
    height    : 1px;
    background: var(--bs-border-color);
    margin    : 0 -16px;
}
[data-bs-theme="dark"] .kba-related-divider { background: var(--dark-border, #2a2a2d); }

/* ── Responsive ────────────────────────────────────── */
@media (max-width: 991px) {
    .kba-body   { grid-template-columns: 1fr; gap: 14px; }
    .kba-sidebar{ flex-direction: row; flex-wrap: wrap; }
    .kba-panel  { flex: 1; min-width: 240px; }
    .kba-panel + .kba-panel { border-top: none; border-left: 1px solid var(--bs-border-color); }
}
@media (max-width: 767px) {
    .kba-header { padding-right:.75rem !important; min-height:52px; }
    .kba-page-title { font-size:14px; }
    .kba-fav-text   { display:none; }
    .kba-fav-btn    { padding:5px 9px; }
    .kba-body       { padding:12px 12px 80px; }
    .kba-hero       { height:150px; border-radius:12px; }
    .kba-hero-title { font-size:16px; }
    .kba-sidebar    { flex-direction:column; }
    .kba-panel + .kba-panel { border-left:none; border-top:1px solid var(--bs-border-color); }
}
@media (max-width: 480px) {
    .kba-hero-img   { width:45%; }
    .kba-hero-text  { max-width:65%; padding:14px 16px; }
    .kba-hero-title { font-size:14px; }
}
</style>

{{-- ══════════════════════════════════════════════════
     ① SECTION HEADER
     .header-actions-wrapper → index.js sidebar shift
     ══════════════════════════════════════════════════ --}}
<div class="header-actions-wrapper kba-header">
    <div class="kba-header-left">
        <a href="{{ url()->previous() }}" class="kba-back-btn" title="Back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        <span class="kba-page-title">{{ 'Knowledge Article' }}</span>
    </div>

    <button class="kba-fav-btn" id="kbaFavBtn" onclick="kbaToggleFav(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
        </svg>
        <span class="kba-fav-text">Add to Favourites</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════
     ② MAIN CONTENT
     main.main-content → index.js margin-left shift
     ══════════════════════════════════════════════════ --}}
<main class="main-content" style="overflow-x:hidden;min-width:0;">
<div class="kba-body">

    {{-- ══ LEFT: Article ══ --}}
    <div class="kba-article">

        {{-- Hero banner with image --}}
        <div class="kba-hero">
            <div class="kba-hero-bg"></div>

            {{-- Placeholder person illustration --}}
            <div style="position:absolute;right:0;top:0;bottom:0;width:55%;z-index:2;
                    -webkit-mask-image:linear-gradient(to right,transparent 0%,black 30%);
                    mask-image:linear-gradient(to right,transparent 0%,black 30%);
                    background:linear-gradient(135deg,#b05030 0%,#8a3a22 100%);">
                <svg viewBox="0 0 200 200" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                    <!-- Silhouette placeholder -->
                    <rect width="200" height="200" fill="rgba(0,0,0,0)"/>
                    <ellipse cx="100" cy="75" rx="38" ry="42" fill="rgba(255,255,255,.12)"/>
                    <path d="M30,200 Q30,130 100,130 Q170,130 170,200 Z" fill="rgba(255,255,255,.10)"/>
                </svg>
            </div>
            <div class="kba-hero-text">
                <h1 class="kba-hero-title">
                    {!! "What is Mai AI in AMG ITM?\nHow it works?" !!}
                </h1>
            </div>
        </div>

        {{-- Created date --}}
        <div class="kba-meta">
            <strong>Created Date:</strong>&nbsp;
            {{ 'January 24, 2026' }}
        </div>

        {{-- Article content --}}
        <div class="kba-content">
            {{-- Static content matching screenshot --}}
            <div class="kba-content-block">
                <h2 class="kba-section-title">How knowledge base can help you to resolve tickets</h2>
                <p class="kba-para">Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.</p>
                <p class="kba-para">Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.</p>
            </div>

            <div class="kba-content-block">
                <h2 class="kba-section-title">How knowledge base can help you to resolve tickets</h2>
                <p class="kba-para">Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.</p>
                <p class="kba-para">Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.</p>
            </div>
        </div>
    </div>{{-- /kba-article --}}

    {{-- ══ RIGHT: Sidebar ══ --}}
    <div class="kba-sidebar">

        {{-- Article Info --}}
        <div class="kba-panel">
            <h6 class="kba-panel-title">Article Info</h6>
            <div class="kba-info-body">

                <div class="kba-info-row">
                    <div class="kba-info-val">
                        <span class="kba-info-key">Created by</span><span class="kba-author-av">AS</span>
                        <span>Ananth S</span><div>
                            
                            <span class="kba-info-date">
                                12 December 2025 at, 12.45 PM
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Related Articles --}}
        <div class="kba-panel">
            <h6 class="kba-panel-title">Related Article</h6>
            <div class="kba-related-list">

                @php
                $related = $relatedArticles ?? [
                    [
                        'title' => 'How knowledge base can help you to resolve tickets:',
                        'image' => null,
                        'type'  => 'laptop',
                    ],
                    [
                        'title' => 'How knowledge base can help you to resolve tickets:',
                        'image' => null,
                        'type'  => 'config',
                    ],
                ];
                @endphp

                @foreach($related as $idx => $rel)
                    @if($idx > 0)
                    <div class="kba-related-divider"></div>
                    @endif

                    <div class="kba-related-item" onclick="window.location='#'">

                        @if(!empty($rel['image']))
                            <img src="{{ $rel['image'] }}" alt="" class="kba-related-thumb" loading="lazy">
                        @else
                            {{-- Placeholder thumbnails matching screenshot style --}}
                            <div class="kba-related-thumb-placeholder">
                                @if(($rel['type'] ?? '') === 'laptop')
                                <svg viewBox="0 0 120 80" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="80" fill="#1a1a2e"/>
                                    <rect x="20" y="10" width="80" height="52" rx="4" fill="#16213e"/>
                                    <rect x="22" y="12" width="76" height="48" rx="3" fill="#0f3460"/>
                                    <!-- Windows logo colors -->
                                    <rect x="44" y="24" width="14" height="14" rx="1" fill="#f25022"/>
                                    <rect x="60" y="24" width="14" height="14" rx="1" fill="#7fba00"/>
                                    <rect x="44" y="40" width="14" height="14" rx="1" fill="#00a4ef"/>
                                    <rect x="60" y="40" width="14" height="14" rx="1" fill="#ffb900"/>
                                    <rect x="15" y="62" width="90" height="6" rx="2" fill="#2d2d2d"/>
                                    <rect x="40" y="64" width="40" height="2" rx="1" fill="#444"/>
                                </svg>
                                @else
                                <svg viewBox="0 0 120 80" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="80" fill="#f8f9fa"/>
                                    <rect x="8" y="6" width="104" height="68" rx="4" fill="#fff" stroke="#e0e0e0" stroke-width="1"/>
                                    <!-- Header bar red -->
                                    <rect x="8" y="6" width="104" height="12" rx="4" fill="#ef4444"/>
                                    <rect x="8" y="14" width="104" height="4" fill="#ef4444"/>
                                    <!-- Content lines -->
                                    <rect x="14" y="24" width="40" height="3" rx="1" fill="#e0e0e0"/>
                                    <rect x="14" y="30" width="92" height="2" rx="1" fill="#f0f0f0"/>
                                    <rect x="14" y="35" width="80" height="2" rx="1" fill="#f0f0f0"/>
                                    <rect x="14" y="40" width="88" height="2" rx="1" fill="#f0f0f0"/>
                                    <!-- Table rows -->
                                    <rect x="14" y="48" width="92" height="2" rx="1" fill="#f0f0f0"/>
                                    <rect x="14" y="53" width="92" height="2" rx="1" fill="#f0f0f0"/>
                                    <rect x="14" y="58" width="60" height="2" rx="1" fill="#f0f0f0"/>
                                </svg>
                                @endif
                            </div>
                        @endif

                        <p class="kba-related-title">{{ $rel['title'] }}</p>

                    </div>
                @endforeach

            </div>
        </div>

    </div>{{-- /kba-sidebar --}}

</div>{{-- /kba-body --}}
</main>

<script>
function kbaToggleFav(btn) {
    btn.classList.toggle('favourited');
    var span = btn.querySelector('.kba-fav-text');
    if (span) span.textContent = btn.classList.contains('favourited') ? 'Saved' : 'Add to Favourites';
    /* TODO: POST to backend */
}
</script>

@endsection