{{-- @page-meta { "page_no": "DD-01", "version": "1.0", "description": "Device Details - Asset Management" } --}}
@extends('layouts.layout1')
@section('title', 'Device Details')
@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   DEVICE DETAILS  –  dd-*
   Light: --app-bg:#f8f9fa  --app-surface:#fff  --app-border:#dee2e6
   Dark:  --app-bg:#141414  --dark-primary:#191919
          --dark-secondary:#2a2a2a  --dark-border:#2a2a2d
          --text-primary:#fff  --text-secondary:#e5e7eb  --text-muted:#757575
   ═══════════════════════════════════════════════════════ */

/* ── 1. HEADER ─────────────────────────────────────── */
.dd-header {
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
[data-bs-theme="dark"] .dd-header {
    background   : var(--dark-primary,#191919) !important;
    border-color : var(--dark-border,#2a2a2d) !important;
}
.dd-page-title {
    font-size:16px; font-weight:700;
    color:var(--app-text,#212529);
    display:flex; align-items:center; gap:10px;
}
.dd-page-title a {
    color:inherit; text-decoration:none;
    display:inline-flex; align-items:center;
}
.dd-page-title a svg { width:18px; height:18px; }
[data-bs-theme="dark"] .dd-page-title { color:var(--text-primary,#fff) !important; }

/* ── 2. PAGE BG + LAYOUT ───────────────────────────── */
.dd-page-bg {
    background : var(--app-bg,#f8f9fa);
    padding    : 14px 16px calc(var(--footer-height,30px) + 16px);
    box-sizing : border-box;
    min-height : calc(100vh - 57px);
}
[data-bs-theme="dark"] .dd-page-bg { background:var(--app-bg,#141414) !important; }

/* 2-col layout: left card + right card */
.dd-layout {
    display              : grid;
    grid-template-columns: 1fr 260px;
    gap                  : 14px;
    align-items          : start;
    min-width            : 0;
}

/* ── 3. LEFT CARD ──────────────────────────────────── */
.dd-left-card {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 12px;
    overflow     : hidden;
    min-width    : 0;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .dd-left-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* ── 4. RIGHT CARD ─────────────────────────────────── */
.dd-right-card {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 12px;
    overflow     : hidden;
    min-width    : 0;
    box-shadow   : 0 1px 3px rgba(0,0,0,.04);
}
[data-bs-theme="dark"] .dd-right-card {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* ── 5. TABS ───────────────────────────────────────── */
.dd-tabs {
    display      : flex;
    padding      : 0 20px;
    border-bottom: 1px solid var(--app-border,#dee2e6);
    background   : var(--app-surface,#fff);
    overflow-x   : auto;
    scrollbar-width:none;
}
.dd-tabs::-webkit-scrollbar { display:none; }
[data-bs-theme="dark"] .dd-tabs {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.dd-tab {
    padding      : 13px 18px;
    font-size    : 13px; font-weight:500;
    color        : var(--app-text,#6b7280) !important;
    border       : none; background:none;
    border-bottom: 2.5px solid transparent;
    cursor       : pointer; font-family:inherit;
    white-space  : nowrap; margin-bottom:-1px;
    transition   : color .15s, border-color .15s;
}
.dd-tab.active {
    color            : #ef4444 !important;
    border-bottom-color:#ef4444;
    font-weight      : 600;
}
.dd-tab:hover:not(.active) { color:var(--app-text,#374151) !important; }
[data-bs-theme="dark"] .dd-tab        { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .dd-tab.active { color:#ef4444 !important; }

/* Tab panels */
.dd-panel        { display:none; }
.dd-panel.active { display:block; }

/* ── 6. INFO TABLE ─────────────────────────────────── */
/* ── INFO TABLE — alternating FULL ROW bg ─────────── */
.dd-table-wrap { overflow:hidden; }

.dd-row {
    display              : grid;
    grid-template-columns: 200px 1fr;
    border-bottom        : 1px solid var(--app-border,#dee2e6);
}
.dd-row:last-child { border-bottom:none; }

/* Odd rows: full row light grey */
.dd-row:nth-child(odd) > .dd-cell-key,
.dd-row:nth-child(odd) > .dd-cell-val { background:var(--app-bg,#f8f9fa); }

/* Even rows: full row white */
.dd-row:nth-child(even) > .dd-cell-key,
.dd-row:nth-child(even) > .dd-cell-val { background:var(--app-surface,#fff); }

.dd-cell-key {
    padding    : 13px 20px;
    font-size  : 13px;
    color      : #6b7280;
    font-weight: 400;
    display    : flex;
    align-items: center;
    border-right:1px solid var(--app-border,#dee2e6);
}
.dd-cell-val {
    padding    : 13px 20px;
    font-size  : 13px;
    color      : var(--app-text,#212529);
    font-weight: 500;
    display    : flex;
    align-items: center;
    gap        : 8px;
}

[data-bs-theme="dark"] .dd-row { border-color:var(--dark-border,#2a2a2d) !important; }
[data-bs-theme="dark"] .dd-row:nth-child(odd) > .dd-cell-key,
[data-bs-theme="dark"] .dd-row:nth-child(odd) > .dd-cell-val {
    background:var(--dark-primary,#191919) !important;
}
[data-bs-theme="dark"] .dd-row:nth-child(even) > .dd-cell-key,
[data-bs-theme="dark"] .dd-row:nth-child(even) > .dd-cell-val {
    background:var(--dark-secondary,#2a2a2a) !important;
}
[data-bs-theme="dark"] .dd-cell-key {
    color       :var(--text-muted,#757575) !important;
    border-color:var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .dd-cell-val { color:var(--text-secondary,#e5e7eb) !important; }


/* Hidden extra rows */
.dd-extra-rows        { display:none; }
.dd-extra-rows.expanded { display:contents; }

/* View More / Less */
.dd-view-more {
    display    : flex; align-items:center; gap:5px;
    color      : #ef4444 !important;
    font-size  : 13px; font-weight:500;
    padding    : 12px 20px; cursor:pointer;
    background : var(--app-surface,#fff);
    border     : none; border-top:1px solid var(--app-border,#dee2e6);
    font-family: inherit; text-align:left;
    transition : color .15s; width:100%;
}
.dd-view-more:hover  { color:#dc2626 !important; }
.dd-view-more svg    { width:14px; height:14px; transition:transform .2s; }
.dd-view-more.expanded svg { transform:rotate(180deg); }
[data-bs-theme="dark"] .dd-view-more {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.dd-badge-deployed {
    display      : inline-flex; align-items:center; gap:5px;
    background   : #d1fae5; color:#065f46 !important;
    border-radius: 20px; font-size:12px; font-weight:500;
    padding      : 3px 12px; border:1px solid #a7f3d0;
}
.dd-badge-deployed::before {
    content:''; width:7px; height:7px; border-radius:50%;
    background:#22c55e; display:inline-block;
}
[data-bs-theme="dark"] .dd-badge-deployed {
    background  : #052e16 !important;
    color       : #4ade80 !important;
    border-color: #166534 !important;
}

/* Check In button */
.dd-checkin-btn {
    display      : inline-flex; align-items:center; gap:6px;
    background   : #ef4444; color:#fff !important;
    border       : none; border-radius:8px;
    padding      : 7px 18px; font-size:12.5px; font-weight:600;
    font-family  : inherit; cursor:pointer;
    transition   : background .15s; white-space:nowrap;
}
.dd-checkin-btn:hover { background:#dc2626; }
.dd-checkin-btn svg { width:14px; height:14px; }

/* Checkout info */
.dd-checkout-info { display:flex; flex-direction:column; gap:2px; }
.dd-checkout-name {
    display:flex; align-items:center; gap:6px;
    font-size:13.5px; font-weight:600; color:var(--app-text,#212529);
}
[data-bs-theme="dark"] .dd-checkout-name { color:var(--text-primary,#fff) !important; }
.dd-checkout-org  { font-size:12px; color:#6b7280; }
.dd-checkout-status {
    display:inline-flex; align-items:center; gap:4px;
    font-size:12px; color:#22c55e; font-weight:500;
}
.dd-checkout-status svg { width:13px; height:13px; }
.dd-checkout-type { font-size:12px; color:#6b7280; }
[data-bs-theme="dark"] .dd-checkout-org    { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .dd-checkout-type   { color:var(--text-muted,#757575) !important; }

/* User avatar mini */
.dd-user-av {
    width:28px; height:28px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#6366f1,#4338ca);
    display:flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:700; color:#fff; overflow:hidden;
}
.dd-user-av img { width:100%; height:100%; object-fit:cover; }

/* Manufacturer logo */
.dd-mfr {
    display:inline-flex; align-items:center; gap:7px;
    font-size:13px; font-weight:600;
}
.dd-mfr-logo {
    width:24px; height:24px; border-radius:50%;
    background:linear-gradient(135deg,#0077b6,#00b4d8);
    display:flex; align-items:center; justify-content:center;
    font-size:8px; font-weight:800; color:#fff;
}

/* View More Details link */
.dd-view-more {
    display:inline-flex; align-items:center; gap:4px;
    color:#ef4444 !important; font-size:13px; font-weight:500;
    text-decoration:none; padding:14px 20px; cursor:pointer;
    transition:color .15s;
}
.dd-view-more:hover { color:#dc2626 !important; }

/* ── 7. RIGHT SIDEBAR sections ─────────────────────── */
.dd-aside {
    padding       : 0;
    background    : transparent;
    min-width     : 0;
}
.dd-side-section {
    padding      : 16px;
    border-bottom: 1px solid var(--app-border,#dee2e6);
}
.dd-side-section:last-child { border-bottom:none; }
[data-bs-theme="dark"] .dd-side-section {
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* RDP Status */
.dd-rdp-disabled {
    display     : flex; align-items:center; justify-content:center;
    gap         : 7px;
    background  : #e5e7eb; color:#6b7280 !important;
    border-radius:8px; padding:9px;
    font-size   : 13px; font-weight:500;
    margin-bottom:8px; width:100%;
    border      : none; font-family:inherit; cursor:not-allowed;
}
.dd-rdp-disabled svg { width:15px; height:15px; }
[data-bs-theme="dark"] .dd-rdp-disabled {
    background  : var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}
.dd-rdp-cancel {
    display     : flex; align-items:center; justify-content:center;
    background  : #ef4444; color:#fff !important;
    border-radius:8px; padding:9px;
    font-size   : 13px; font-weight:600;
    width       : 100%; border:none;
    font-family : inherit; cursor:pointer;
    transition  : background .15s;
}
.dd-rdp-cancel:hover { background:#dc2626; }

/* Warranty bar */
.dd-warranty-row {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    margin-bottom  : 6px;
}
.dd-warranty-exp {
    font-size:12px; color:#6b7280;
}
[data-bs-theme="dark"] .dd-warranty-exp { color:var(--text-muted,#757575) !important; }
.dd-warranty-bar {
    height      : 8px; border-radius:4px;
    background  : #e5e7eb; overflow:hidden;
}
[data-bs-theme="dark"] .dd-warranty-bar { background:var(--dark-border,#2a2a2d) !important; }
.dd-warranty-fill {
    height      : 100%; border-radius:4px;
    background  : linear-gradient(90deg,#22c55e,#4ade80);
    width       : 70%;
}

/* Device Image */
.dd-device-img {
    width        : 100%; border-radius:10px;
    overflow     : hidden; background:#f1f5f9;
    border       : 1px solid var(--app-border,#dee2e6);
}
.dd-device-img img {
    width:100%; height:150px; object-fit:cover; display:block;
}
.dd-img-placeholder {
    width:100%; height:150px;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,#f1f5f9,#e2e8f0);
    color:#94a3b8;
}
.dd-img-placeholder svg { width:48px; height:48px; }
[data-bs-theme="dark"] .dd-device-img { border-color:var(--dark-border,#2a2a2d) !important; }

/* Barcode */
.dd-barcode {
    width:100%; border-radius:10px;
    border:1px solid var(--app-border,#dee2e6);
    overflow:hidden; background:#fff;
    display:flex; align-items:center; justify-content:center;
    padding:10px;
}
[data-bs-theme="dark"] .dd-barcode {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* QR code SVG */
.dd-qr {
    width:120px; height:120px;
    image-rendering:pixelated;
}

/* ── 7. OTHER TABS (placeholder) ──────────────────── */
.dd-tab-placeholder {
    padding    : 40px 20px;
    text-align : center;
    color      : var(--app-text,#6b7280);
}
.dd-tab-placeholder svg { width:48px; height:48px; color:#d1d5db; margin-bottom:10px; display:block; margin:0 auto 10px; }

/* ── 8. RESPONSIVE ─────────────────────────────────── */
@media (max-width:991px) {
    .dd-layout { grid-template-columns:1fr; }
}
@media (max-width:767px) {
    .dd-header     { padding-right:.75rem !important; min-height:52px; }
    .dd-page-title { font-size:14px; }
    .dd-row        { grid-template-columns:140px 1fr; }
    .dd-cell-key,.dd-cell-val { padding:11px 14px; font-size:12px; }
    .dd-tabs       { padding:0 14px; }
    .dd-tab        { padding:11px 14px; font-size:12px; }
    .dd-page-bg    { padding:10px 10px 80px; }
}
@media (max-width:480px) {
    .dd-row      { grid-template-columns:1fr; }
    .dd-cell-key { border-bottom:none; padding-bottom:2px; font-size:11px; font-weight:600; }
    .dd-cell-val { padding-top:4px; }
    .dd-page-bg  { padding:8px 8px 80px; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper dd-header">
    <div class="dd-page-title">
        <a href="{{ url()->previous() }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        {{ $device->name ?? 'ITM2077 - Dell Lattitute 1240' }}
    </div>
</div>

{{-- ② MAIN --}}
<main class="main-content">
<div class="dd-page-bg">
<div class="dd-layout">

    {{-- ═══ LEFT CARD ═══ --}}
    <div class="dd-left-card">
        <div class="dd-tabs">
            @foreach([
                ['information',  'Information',    true],
                ['configuration','Configuration',  false],
                ['financial',    'Financial',      false],
                ['maintenance',  'Maintenance',    false],
                ['gatepass',     'Gate Pass',      false],
                ['movement',     'Device Movement',false],
            ] as [$id,$label,$active])
            <button class="dd-tab {{ $active ? 'active' : '' }}" onclick="ddTab('{{ $id }}',this)">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── Information Panel ── --}}
        <div class="dd-panel active" id="dd-panel-information">

        <div class="dd-table-wrap">

            {{-- Visible rows --}}
            <div class="dd-row">
                <div class="dd-cell-key">Device Tag</div>
                <div class="dd-cell-val">{{ $device->tag ?? 'ITM2027-0989A' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Device Name</div>
                <div class="dd-cell-val">{{ $device->device_name ?? "Ananth's Mac" }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Device Tag</div>
                <div class="dd-cell-val">{{ $device->tag ?? 'ITM2027-0989A' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Current Status</div>
                <div class="dd-cell-val">
                    <span class="dd-badge-deployed">Deployed</span>
                </div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Check Out To</div>
                <div class="dd-cell-val" style="justify-content:space-between;flex-wrap:wrap;gap:10px;">
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <div class="dd-user-av">
                            @if(isset($device->checkout_user_photo))
                            <img src="{{ $device->checkout_user_photo }}" alt="">
                            @else AB @endif
                        </div>
                        <div class="dd-checkout-info">
                            <div class="dd-checkout-name">{{ $device->checkout_user ?? 'Aditya Bhave' }}</div>
                            <div class="dd-checkout-org">{{ $device->checkout_org ?? 'Greenitco Technologies' }}</div>
                            <div class="dd-checkout-status">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Checkout to User
                            </div>
                            <div class="dd-checkout-type">Allocation Type: Business Travel</div>
                        </div>
                    </div>
                    <button class="dd-checkin-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><circle cx="12" cy="12" r="10"/></svg>
                        Check In
                    </button>
                </div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Checked Out Date</div>
                <div class="dd-cell-val">{{ $device->checkout_date ?? '07 March, 2026' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Expected Checkin Date</div>
                <div class="dd-cell-val">{{ $device->expected_checkin ?? 'ITM2027-0989A' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Category</div>
                <div class="dd-cell-val">{{ $device->category ?? 'Desktop' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Device Type</div>
                <div class="dd-cell-val">{{ $device->device_type ?? 'ITM2027-0989A' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Device From</div>
                <div class="dd-cell-val">{{ $device->device_from ?? 'Desktop' }}</div>
            </div>
            <div class="dd-row">
                <div class="dd-cell-key">Manufacturer</div>
                <div class="dd-cell-val">
                    <div class="dd-mfr">
                        <div class="dd-mfr-logo">D</div>
                        {{ $device->manufacturer ?? 'Dell' }}
                    </div>
                </div>
            </div>

            {{-- ── Hidden extra rows (expand on View More) ── --}}
            <div class="dd-extra-rows" id="ddExtraRows">
                <div class="dd-row">
                    <div class="dd-cell-key">Serial Number</div>
                    <div class="dd-cell-val">{{ $device->serial ?? 'SN-4892-DELL-2024' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Model</div>
                    <div class="dd-cell-val">{{ $device->model ?? 'Latitude 1240' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">RAM</div>
                    <div class="dd-cell-val">{{ $device->ram ?? '16 GB' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Storage</div>
                    <div class="dd-cell-val">{{ $device->storage ?? '512 GB SSD' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Processor</div>
                    <div class="dd-cell-val">{{ $device->processor ?? 'Intel Core i5 12th Gen' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">OS</div>
                    <div class="dd-cell-val">{{ $device->os ?? 'Windows 11 Pro' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Purchase Date</div>
                    <div class="dd-cell-val">{{ $device->purchase_date ?? '12 Jan, 2024' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Purchase Cost</div>
                    <div class="dd-cell-val">{{ $device->purchase_cost ?? '₹ 85,000' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Warranty Expiry</div>
                    <div class="dd-cell-val">{{ $device->warranty_expiry ?? '26 Jan, 2026' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Location</div>
                    <div class="dd-cell-val">{{ $device->location ?? 'Mumbai Office - Floor 3' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Department</div>
                    <div class="dd-cell-val">{{ $device->department ?? 'Product Design' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">IP Address</div>
                    <div class="dd-cell-val">{{ $device->ip ?? '192.168.1.45' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">MAC Address</div>
                    <div class="dd-cell-val">{{ $device->mac ?? '00:1A:2B:3C:4D:5E' }}</div>
                </div>
                <div class="dd-row">
                    <div class="dd-cell-key">Condition</div>
                    <div class="dd-cell-val">{{ $device->condition ?? 'Good' }}</div>
                </div>
            </div>

        </div>{{-- /dd-table-wrap --}}

            {{-- View More / Less button --}}
            <button class="dd-view-more" id="ddViewMoreBtn" onclick="ddToggleMore()">
                View More Details
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

        </div>{{-- /information panel --}}

        {{-- ── Configuration Panel ── --}}
        <div class="dd-panel" id="dd-panel-configuration">
            <div class="dd-tab-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1 0 4.93 19.07 10 10 0 0 0 19.07 4.93z"/>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <p style="margin:0;font-size:13px;">Configuration details will appear here.</p>
            </div>
        </div>

        {{-- ── Financial Panel ── --}}
        <div class="dd-panel" id="dd-panel-financial">
            <div class="dd-tab-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <p style="margin:0;font-size:13px;">Financial details will appear here.</p>
            </div>
        </div>

        {{-- ── Maintenance Panel ── --}}
        <div class="dd-panel" id="dd-panel-maintenance">
            <div class="dd-tab-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                <p style="margin:0;font-size:13px;">Maintenance records will appear here.</p>
            </div>
        </div>

        {{-- ── Gate Pass Panel ── --}}
        <div class="dd-panel" id="dd-panel-gatepass">
            <div class="dd-tab-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                <p style="margin:0;font-size:13px;">Gate pass records will appear here.</p>
            </div>
        </div>

        {{-- ── Device Movement Panel ── --}}
        <div class="dd-panel" id="dd-panel-movement">
            <div class="dd-tab-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <polyline points="5 9 2 12 5 15"/><polyline points="19 9 22 12 19 15"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                </svg>
                <p style="margin:0;font-size:13px;">Device movement history will appear here.</p>
            </div>
        </div>

    </div>{{-- /dd-left-card --}}

    {{-- ═══ RIGHT CARD ═══ --}}
    <div class="dd-right-card">
    <div class="dd-aside">

        {{-- Current RDP Status --}}
        <div class="dd-side-section">
            <h6 class="dd-aside-title">Current RDP Status</h6>
            <button class="dd-rdp-disabled" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
                Disabled
            </button>
            <button class="dd-rdp-cancel" style="margin-top:8px;">Cancel</button>
        </div>

        {{-- Warranty Status --}}
        <div class="dd-side-section">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <h6 class="dd-aside-title" style="margin:0;">Warranty Status</h6>
                <span class="dd-warranty-exp">Ex : 26Jan, 2026</span>
            </div>
            <div class="dd-warranty-bar">
                <div class="dd-warranty-fill"></div>
            </div>
        </div>

        {{-- Device Images --}}
        <div class="dd-side-section" style="padding:0;">
            <div style="padding:14px 16px 10px;">
                <h6 class="dd-aside-title" style="margin:0;">Device Images</h6>
            </div>
            <div class="dd-device-img" style="border-radius:0;border:none;border-top:1px solid var(--app-border,#dee2e6);">
                @if(isset($device->image))
                <img src="{{ $device->image }}" alt="Device Image">
                @else
                <div class="dd-img-placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                @endif
            </div>
        </div>

        {{-- Barcode Image --}}
        <div class="dd-side-section">
            <h6 class="dd-aside-title">Barcode Image</h6>
            <div class="dd-barcode">
                <svg class="dd-qr" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
                    <rect width="21" height="21" fill="white"/>
                    <rect x="0" y="0" width="7" height="7" fill="black"/>
                    <rect x="1" y="1" width="5" height="5" fill="white"/>
                    <rect x="2" y="2" width="3" height="3" fill="black"/>
                    <rect x="14" y="0" width="7" height="7" fill="black"/>
                    <rect x="15" y="1" width="5" height="5" fill="white"/>
                    <rect x="16" y="2" width="3" height="3" fill="black"/>
                    <rect x="0" y="14" width="7" height="7" fill="black"/>
                    <rect x="1" y="15" width="5" height="5" fill="white"/>
                    <rect x="2" y="16" width="3" height="3" fill="black"/>
                    <rect x="8" y="0" width="1" height="1" fill="black"/><rect x="10" y="0" width="1" height="1" fill="black"/>
                    <rect x="12" y="0" width="1" height="1" fill="black"/><rect x="8" y="2" width="2" height="1" fill="black"/>
                    <rect x="11" y="2" width="1" height="1" fill="black"/><rect x="8" y="4" width="1" height="1" fill="black"/>
                    <rect x="10" y="4" width="3" height="1" fill="black"/><rect x="7" y="7" width="1" height="1" fill="black"/>
                    <rect x="9" y="7" width="2" height="1" fill="black"/><rect x="12" y="7" width="2" height="1" fill="black"/>
                    <rect x="7" y="8" width="3" height="1" fill="black"/><rect x="11" y="8" width="1" height="1" fill="black"/>
                    <rect x="0" y="8" width="1" height="1" fill="black"/><rect x="2" y="8" width="1" height="1" fill="black"/>
                    <rect x="4" y="8" width="1" height="1" fill="black"/><rect x="6" y="8" width="1" height="1" fill="black"/>
                    <rect x="13" y="9" width="1" height="1" fill="black"/><rect x="8" y="9" width="1" height="1" fill="black"/>
                    <rect x="10" y="9" width="1" height="1" fill="black"/><rect x="0" y="9" width="1" height="1" fill="black"/>
                    <rect x="3" y="9" width="2" height="1" fill="black"/><rect x="6" y="9" width="1" height="1" fill="black"/>
                    <rect x="7" y="10" width="2" height="1" fill="black"/><rect x="11" y="10" width="3" height="1" fill="black"/>
                    <rect x="1" y="10" width="2" height="1" fill="black"/><rect x="5" y="10" width="1" height="1" fill="black"/>
                    <rect x="8" y="11" width="1" height="1" fill="black"/><rect x="10" y="11" width="2" height="1" fill="black"/>
                    <rect x="13" y="11" width="1" height="1" fill="black"/><rect x="0" y="11" width="1" height="1" fill="black"/>
                    <rect x="2" y="11" width="3" height="1" fill="black"/><rect x="6" y="11" width="1" height="1" fill="black"/>
                    <rect x="14" y="7" width="1" height="1" fill="black"/><rect x="16" y="7" width="1" height="1" fill="black"/>
                    <rect x="18" y="7" width="1" height="1" fill="black"/><rect x="20" y="7" width="1" height="1" fill="black"/>
                    <rect x="14" y="8" width="2" height="1" fill="black"/><rect x="18" y="8" width="2" height="1" fill="black"/>
                    <rect x="15" y="9" width="1" height="1" fill="black"/><rect x="17" y="9" width="1" height="1" fill="black"/>
                    <rect x="20" y="9" width="1" height="1" fill="black"/><rect x="14" y="10" width="3" height="1" fill="black"/>
                    <rect x="19" y="10" width="2" height="1" fill="black"/><rect x="14" y="11" width="1" height="1" fill="black"/>
                    <rect x="16" y="11" width="2" height="1" fill="black"/><rect x="20" y="11" width="1" height="1" fill="black"/>
                    <rect x="15" y="12" width="2" height="1" fill="black"/><rect x="18" y="12" width="1" height="1" fill="black"/>
                    <rect x="14" y="13" width="1" height="1" fill="black"/><rect x="17" y="13" width="3" height="1" fill="black"/>
                </svg>
            </div>
        </div>

    </div>{{-- /dd-aside --}}
                {{-- QR / Barcode SVG representation --}}
                <svg class="dd-qr" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
                    <rect width="21" height="21" fill="white"/>
                    {{-- Finder top-left --}}
                    <rect x="0" y="0" width="7" height="7" fill="black"/>
                    <rect x="1" y="1" width="5" height="5" fill="white"/>
                    <rect x="2" y="2" width="3" height="3" fill="black"/>
                    {{-- Finder top-right --}}
                    <rect x="14" y="0" width="7" height="7" fill="black"/>
                    <rect x="15" y="1" width="5" height="5" fill="white"/>
                    <rect x="16" y="2" width="3" height="3" fill="black"/>
                    {{-- Finder bottom-left --}}
                    <rect x="0" y="14" width="7" height="7" fill="black"/>
                    <rect x="1" y="15" width="5" height="5" fill="white"/>
                    <rect x="2" y="16" width="3" height="3" fill="black"/>
                    {{-- Data modules --}}
                    <rect x="8"  y="0"  width="1" height="1" fill="black"/>
                    <rect x="10" y="0"  width="1" height="1" fill="black"/>
                    <rect x="12" y="0"  width="1" height="1" fill="black"/>
                    <rect x="8"  y="2"  width="2" height="1" fill="black"/>
                    <rect x="11" y="2"  width="1" height="1" fill="black"/>
                    <rect x="8"  y="4"  width="1" height="1" fill="black"/>
                    <rect x="10" y="4"  width="3" height="1" fill="black"/>
                    <rect x="7"  y="7"  width="1" height="1" fill="black"/>
                    <rect x="9"  y="7"  width="2" height="1" fill="black"/>
                    <rect x="12" y="7"  width="2" height="1" fill="black"/>
                    <rect x="7"  y="8"  width="3" height="1" fill="black"/>
                    <rect x="11" y="8"  width="1" height="1" fill="black"/>
                    <rect x="0"  y="8"  width="1" height="1" fill="black"/>
                    <rect x="2"  y="8"  width="1" height="1" fill="black"/>
                    <rect x="4"  y="8"  width="1" height="1" fill="black"/>
                    <rect x="6"  y="8"  width="1" height="1" fill="black"/>
                    <rect x="13" y="9"  width="1" height="1" fill="black"/>
                    <rect x="8"  y="9"  width="1" height="1" fill="black"/>
                    <rect x="10" y="9"  width="1" height="1" fill="black"/>
                    <rect x="0"  y="9"  width="1" height="1" fill="black"/>
                    <rect x="3"  y="9"  width="2" height="1" fill="black"/>
                    <rect x="6"  y="9"  width="1" height="1" fill="black"/>
                    <rect x="7"  y="10" width="2" height="1" fill="black"/>
                    <rect x="11" y="10" width="3" height="1" fill="black"/>
                    <rect x="1"  y="10" width="2" height="1" fill="black"/>
                    <rect x="5"  y="10" width="1" height="1" fill="black"/>
                    <rect x="8"  y="11" width="1" height="1" fill="black"/>
                    <rect x="10" y="11" width="2" height="1" fill="black"/>
                    <rect x="13" y="11" width="1" height="1" fill="black"/>
                    <rect x="0"  y="11" width="1" height="1" fill="black"/>
                    <rect x="2"  y="11" width="3" height="1" fill="black"/>
                    <rect x="6"  y="11" width="1" height="1" fill="black"/>
                    <rect x="7"  y="12" width="3" height="1" fill="black"/>
                    <rect x="11" y="12" width="1" height="1" fill="black"/>
                    <rect x="13" y="12" width="1" height="1" fill="black"/>
                    <rect x="1"  y="12" width="1" height="1" fill="black"/>
                    <rect x="4"  y="12" width="2" height="1" fill="black"/>
                    <rect x="8"  y="13" width="2" height="1" fill="black"/>
                    <rect x="12" y="13" width="2" height="1" fill="black"/>
                    <rect x="0"  y="13" width="2" height="1" fill="black"/>
                    <rect x="3"  y="13" width="1" height="1" fill="black"/>
                    <rect x="6"  y="13" width="1" height="1" fill="black"/>
                    <rect x="14" y="7"  width="1" height="1" fill="black"/>
                    <rect x="16" y="7"  width="1" height="1" fill="black"/>
                    <rect x="18" y="7"  width="1" height="1" fill="black"/>
                    <rect x="20" y="7"  width="1" height="1" fill="black"/>
                    <rect x="14" y="8"  width="2" height="1" fill="black"/>
                    <rect x="18" y="8"  width="2" height="1" fill="black"/>
                    <rect x="15" y="9"  width="1" height="1" fill="black"/>
                    <rect x="17" y="9"  width="1" height="1" fill="black"/>
                    <rect x="20" y="9"  width="1" height="1" fill="black"/>
                    <rect x="14" y="10" width="3" height="1" fill="black"/>
                    <rect x="19" y="10" width="2" height="1" fill="black"/>
                    <rect x="14" y="11" width="1" height="1" fill="black"/>
                    <rect x="16" y="11" width="2" height="1" fill="black"/>
                    <rect x="20" y="11" width="1" height="1" fill="black"/>
                    <rect x="15" y="12" width="2" height="1" fill="black"/>
                    <rect x="18" y="12" width="1" height="1" fill="black"/>
                    <rect x="14" y="13" width="1" height="1" fill="black"/>
                    <rect x="17" y="13" width="3" height="1" fill="black"/>
                    {{-- Bottom rows --}}
                    <rect x="0"  y="14" width="1" height="1" fill="black"/>
                    <rect x="7"  y="14" width="2" height="1" fill="black"/>
                    <rect x="10" y="14" width="2" height="1" fill="black"/>
                    <rect x="14" y="14" width="2" height="1" fill="black"/>
                    <rect x="18" y="14" width="1" height="1" fill="black"/>
                    <rect x="20" y="14" width="1" height="1" fill="black"/>
                    <rect x="8"  y="15" width="1" height="1" fill="black"/>
                    <rect x="11" y="15" width="1" height="1" fill="black"/>
                    <rect x="13" y="15" width="1" height="1" fill="black"/>
                    <rect x="15" y="15" width="2" height="1" fill="black"/>
                    <rect x="19" y="15" width="2" height="1" fill="black"/>
                    <rect x="7"  y="16" width="3" height="1" fill="black"/>
                    <rect x="11" y="16" width="2" height="1" fill="black"/>
                    <rect x="14" y="16" width="1" height="1" fill="black"/>
                    <rect x="16" y="16" width="1" height="1" fill="black"/>
                    <rect x="19" y="16" width="1" height="1" fill="black"/>
                    <rect x="8"  y="17" width="1" height="1" fill="black"/>
                    <rect x="10" y="17" width="1" height="1" fill="black"/>
                    <rect x="12" y="17" width="3" height="1" fill="black"/>
                    <rect x="17" y="17" width="2" height="1" fill="black"/>
                    <rect x="20" y="17" width="1" height="1" fill="black"/>
                    <rect x="7"  y="18" width="2" height="1" fill="black"/>
                    <rect x="11" y="18" width="1" height="1" fill="black"/>
                    <rect x="13" y="18" width="2" height="1" fill="black"/>
                    <rect x="16" y="18" width="1" height="1" fill="black"/>
                    <rect x="18" y="18" width="3" height="1" fill="black"/>
                    <rect x="8"  y="19" width="3" height="1" fill="black"/>
                    <rect x="12" y="19" width="1" height="1" fill="black"/>
                    <rect x="14" y="19" width="3" height="1" fill="black"/>
                    <rect x="19" y="19" width="1" height="1" fill="black"/>
                    <rect x="7"  y="20" width="1" height="1" fill="black"/>
                    <rect x="9"  y="20" width="2" height="1" fill="black"/>
                    <rect x="13" y="20" width="2" height="1" fill="black"/>
                    <rect x="16" y="20" width="2" height="1" fill="black"/>
                </svg>
            </div>
        </div>

    </div>{{-- /dd-aside --}}
    </div>{{-- /dd-right-card --}}
</div>{{-- /dd-layout --}}
</div>{{-- /dd-page-bg --}}
</main>

<script>
/* Tab switching */
function ddTab(id, btn) {
    document.querySelectorAll('.dd-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.dd-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const panel = document.getElementById('dd-panel-' + id);
    if (panel) panel.classList.add('active');
}

/* View More / Less toggle */
function ddToggleMore() {
    const extra = document.getElementById('ddExtraRows');
    const btn   = document.getElementById('ddViewMoreBtn');
    const isExpanded = extra.classList.contains('expanded');
    if (isExpanded) {
        extra.classList.remove('expanded');
        btn.classList.remove('expanded');
        btn.childNodes[0].textContent = 'View More Details';
    } else {
        extra.classList.add('expanded');
        btn.classList.add('expanded');
        btn.childNodes[0].textContent = 'View Less Details';
    }
}
</script>
@endsection