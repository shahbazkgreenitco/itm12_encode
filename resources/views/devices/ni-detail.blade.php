{{-- @page-meta { "page_no": "NID-01", "version": "1.0", "description": "Network Inventory Details - Users Details" } --}}
@extends('layouts.layout1')
@section('title', 'Inventory Details')
@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   NETWORK INVENTORY DETAILS  –  nid-*
   Light: --app-bg:#f8f9fa  --app-surface:#fff  --app-border:#dee2e6
   Dark:  --app-bg:#141414  --dark-primary:#191919
          --dark-secondary:#2a2a2a  --dark-border:#2a2a2d
          --text-primary:#fff  --text-secondary:#e5e7eb  --text-muted:#757575
   ═══════════════════════════════════════════════════════ */

/* ── 1. HEADER ──────────────────────────────────────── */
.nid-header {
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
[data-bs-theme="dark"] .nid-header {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
.nid-page-title {
    font-size  : 16px; font-weight:700;
    color      : var(--app-text,#212529); white-space:nowrap;
}
[data-bs-theme="dark"] .nid-page-title { color:var(--text-primary,#fff) !important; }

/* Header right buttons */
.nid-hdr-right { display:flex; align-items:center; gap:8px; flex-shrink:0; }

.nid-filter-btn {
    display    : inline-flex; align-items:center; gap:6px;
    border     : 1px solid var(--app-border,#dee2e6);
    border-radius:8px; padding:6px 14px;
    font-size  : 13px; font-family:inherit;
    color      : var(--app-text,#212529) !important;
    background : var(--app-surface,#fff); cursor:pointer;
}
.nid-filter-btn svg { width:14px; height:14px; color:#9ca3af; }
[data-bs-theme="dark"] .nid-filter-btn {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-secondary,#e5e7eb) !important;
}

.nid-icon-btn {
    width:36px; height:36px; border-radius:8px;
    border:1px solid var(--app-border,#dee2e6);
    background:var(--app-surface,#fff);
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; color:#6b7280 !important; transition:background .15s;
}
.nid-icon-btn:hover { background:var(--app-bg,#f8f9fa); }
.nid-icon-btn svg { width:15px; height:15px; }
[data-bs-theme="dark"] .nid-icon-btn {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
    color       : var(--text-muted,#757575) !important;
}

.nid-add-btn {
    display    : inline-flex; align-items:center; gap:6px;
    background : #ef4444; color:#fff !important;
    border     : none; border-radius:8px;
    padding    : 8px 16px; font-size:13px; font-weight:600;
    font-family: inherit; cursor:pointer; white-space:nowrap;
    transition : background .15s;
}
.nid-add-btn:hover { background:#dc2626; }
.nid-add-btn svg { width:14px; height:14px; }

/* ── 2. BODY LAYOUT ─────────────────────────────────── */
.nid-body {
    display              : grid;
    grid-template-columns: 220px 1fr;
    gap                  : 0;
    background           : var(--app-bg,#f8f9fa);
    min-height           : calc(100vh - 57px);
    box-sizing           : border-box;
    align-items          : start;
}
[data-bs-theme="dark"] .nid-body { background:var(--app-bg,#141414) !important; }

/* ── 3. LEFT NAV ─────────────────────────────────────── */
.nid-leftnav {
    background   : var(--app-surface,#fff);
    border-right : 1px solid var(--app-border,#dee2e6);
    min-height   : 100%;
    padding      : 14px 0;
    position     : sticky;
    top          : 0;
    overflow-y   : auto;
    align-self   : stretch;
    margin       : 18px 0 0 14px;
}
[data-bs-theme="dark"] .nid-leftnav {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* Search in left nav */
.nid-nav-search {
    display      : flex; align-items:center; gap:7px;
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 8px; padding:7px 12px;
    margin       : 0 12px 12px;
    background   : var(--app-bg,#f8f9fa);
}
.nid-nav-search input {
    border:none; outline:none; font-size:12.5px;
    color:var(--app-text,#212529); background:transparent;
    font-family:inherit; width:100%;
}
.nid-nav-search input::placeholder { color:#9ca3af; }
.nid-nav-search svg { width:14px; height:14px; color:#9ca3af; flex-shrink:0; }
[data-bs-theme="dark"] .nid-nav-search {
    background  : var(--dark-primary,#191919) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .nid-nav-search input { color:var(--text-primary,#fff) !important; }

/* Nav items */
.nid-nav-item {
    display        : flex; align-items:center;
    padding        : 11px 20px;
    font-size      : 13px; font-weight:500;
    color          : var(--app-text,#374151) !important;
    cursor         : pointer; transition:background .12s;
    border         : none;
    width          : 100%; text-align:left; font-family:inherit;
    background     : none;
    border-left    : 3px solid transparent;
}
.nid-nav-item:hover {
    background: var(--app-bg,#f3f4f6);
    color     : var(--app-text,#212529) !important;
}
/* Active — dark navy bg, white text, no left border accent */
.nid-nav-item.active {
    background : #050b3c;
    color      : #fff !important;
    font-weight: 600;
    border-left: 3px solid #050b3c;
}
.nid-nav-item.active:hover { background:#0a1650 !important; }
[data-bs-theme="dark"] .nid-nav-item        { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .nid-nav-item:hover  { background:var(--dark-hover,#262626) !important; }
[data-bs-theme="dark"] .nid-nav-item.active {
    background  : #1a237e !important;
    color       : #fff !important;
    border-color: #3b82f6 !important;
}

/* ── 4. RIGHT CONTENT ───────────────────────────────── */
.nid-content {
    padding        : 16px;
    min-width      : 0;
    display        : flex;
    flex-direction : column;
    gap            : 12px;
    background     : var(--app-bg,#f8f9fa);
}
[data-bs-theme="dark"] .nid-content { background:var(--app-bg,#141414) !important; }

.nid-tab-panel { display:flex; flex-direction:column; gap:12px; }

/* ── 5. SECTION CARDS — with border radius and gap ── */
.nid-section {
    background   : var(--app-surface,#fff);
    border       : 1px solid var(--app-border,#dee2e6);
    border-radius: 10px;
    overflow     : hidden;
}
.nid-section:last-child { }
[data-bs-theme="dark"] .nid-section {
    background  : var(--dark-secondary,#2a2a2a) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}

/* Section header — light blue bg */
.nid-sec-head {
    display        : flex;
    align-items    : center;
    justify-content: space-between;
    padding        : 12px 20px;
    background     : #eff6ff;
    border-bottom  : 1px solid #dbeafe;
    cursor         : pointer;
    user-select    : none;
    transition     : background .15s;
}
.nid-sec-head:hover { background:#dbeafe; }
[data-bs-theme="dark"] .nid-sec-head {
    background  : rgba(59,130,246,.1) !important;
    border-color: var(--dark-border,#2a2a2d) !important;
}
[data-bs-theme="dark"] .nid-sec-head:hover {
    background:rgba(59,130,246,.16) !important;
}
.nid-section.closed .nid-sec-head { border-bottom:none; }

.nid-sec-title {
    display  : flex; align-items:center; gap:8px;
    font-size: 13.5px; font-weight:700;
    color    : var(--app-text,#212529);
}
.nid-sec-title svg { width:16px; height:16px; color:#3b82f6; flex-shrink:0; }
[data-bs-theme="dark"] .nid-sec-title { color:var(--text-primary,#fff) !important; }

.nid-sec-chevron { color:#6b7280; transition:transform .22s ease; line-height:0; }
.nid-sec-chevron svg { width:16px; height:16px; }
.nid-section.closed .nid-sec-chevron { transform:rotate(180deg); }

/* Section body */
.nid-sec-body { padding:16px 20px; display:block; }
.nid-section.closed .nid-sec-body { display:none; }

/* ── 6. DATA GRID (3 col) ───────────────────────────── */
.nid-data-grid {
    display              : grid;
    grid-template-columns: repeat(3,1fr);
    row-gap              : 0;
    column-gap           : 20px;
}

.nid-data-item {
    padding: 10px 0;
    border-bottom:1px solid var(--app-border,#dee2e6);
}
/* Last row items — no bottom border */
.nid-data-item:nth-last-child(-n+3) { border-bottom:none; }
/* If last row has empty items, still remove border */
.nid-data-item:empty { border-bottom:none; }

.nid-data-key {
    font-size  : 12px; color:#6b7280;
    margin-bottom:3px; font-weight:400; line-height:1.3;
}
.nid-data-val {
    font-size : 13px; font-weight:600;
    color     : var(--app-text,#212529);
    word-break: break-word; line-height:1.4;
}
[data-bs-theme="dark"] .nid-data-key { color:var(--text-muted,#757575) !important; }
[data-bs-theme="dark"] .nid-data-val { color:var(--text-secondary,#e5e7eb) !important; }
[data-bs-theme="dark"] .nid-data-item { border-color:var(--dark-border,#2a2a2d) !important; }

/* Remove old grid divider — no longer needed */
.nid-grid-divider { display:none; }

/* ── 7. RESPONSIVE ──────────────────────────────────── */
@media (max-width:1199px) { .nid-data-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:991px)  {
    .nid-body { grid-template-columns:1fr; }
    .nid-leftnav { min-height:auto; border-right:none; border-bottom:1px solid var(--app-border,#dee2e6); position:relative; }
    .nid-data-grid { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:767px)  {
    .nid-header { padding-right:.75rem !important; min-height:52px; }
    .nid-page-title { font-size:14px; }
    .nid-data-grid  { grid-template-columns:1fr; }
    .nid-content    { padding:10px 10px 80px; }
}
</style>

{{-- ① HEADER --}}
<div class="header-actions-wrapper nid-header">
    <span class="nid-page-title">{{ $user->name ?? 'Inventory Details' }}</span>
    <div class="nid-hdr-right">
        <button class="nid-filter-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filter
        </button>
        <button class="nid-icon-btn" title="Refresh">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
        </button>
        <button class="nid-icon-btn" title="Send">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
        <button class="nid-add-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
        </button>
    </div>
</div>

{{-- ② MAIN --}}
<main class="main-content">
<div class="nid-body">

    {{-- LEFT NAV --}}
    <div class="nid-leftnav">
        <div class="nid-nav-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search..." id="nidNavSearch" oninput="nidNavFilter(this.value)">
        </div>

        @php
        $navItems = [
            'information'          => 'Information',
            'program'              => 'Program',
            'memory'               => 'Memory',
            'driver'               => 'Driver',
            'adapters'             => 'Adapters',
            'monitors'             => 'Monitors',
            'accounts'             => 'Accounts',
            'environment_variables'=> 'Environment Variables',
            'outlook_accounts'     => 'Outlook Accounts',
            'usb_ports'            => 'USB Ports',
            'changes'              => 'Changes',
        ];
        @endphp

        @foreach($navItems as $id => $label)
        <button
            class="nid-nav-item {{ $id === 'information' ? 'active' : '' }}"
            data-nav="{{ $id }}"
            onclick="nidNav('{{ $id }}', this)">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- RIGHT CONTENT --}}
    <div class="nid-content" id="nidContent">

        {{-- ── INFORMATION PANEL ── --}}
        <div class="nid-tab-panel" id="nid-panel-information">

            {{-- Device Information --}}
            <div class="nid-section open" id="sec-device">
                <div class="nid-sec-head" onclick="nidToggleSec('device')">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                        Device Information
                    </div>
                    <span class="nid-sec-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></span>
                </div>
                <div class="nid-sec-body">
                    <div class="nid-data-grid">
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer Name</div>
                            <div class="nid-data-val">{{ $device->computer_name ?? 'SHEIKH-JAINAB' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Serial Number</div>
                            <div class="nid-data-val">{{ $device->serial ?? '67JNVH2' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer Model</div>
                            <div class="nid-data-val">{{ $device->model ?? 'Latitude E7470' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer Manufacturer</div>
                            <div class="nid-data-val">{{ $device->manufacturer ?? 'Dell Inc.' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">IPv4 Address</div>
                            <div class="nid-data-val">{{ $device->ipv4 ?? '192.168.29.86' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">MAC Address</div>
                            <div class="nid-data-val">{{ $device->mac ?? 'F4:8C:50:B2:CB:9D' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer Domain</div>
                            <div class="nid-data-val">{{ $device->domain ?? 'WORKGROUP' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">IPv6 Address</div>
                            <div class="nid-data-val">{{ $device->ipv6 ?? 'fe80::d4:c6d6:ce51:b5002' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer System Type</div>
                            <div class="nid-data-val">{{ $device->system_type ?? 'x64-based PC' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Computer Workgroup</div>
                            <div class="nid-data-val">{{ $device->workgroup ?? 'WORKGROUP' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Agent ID</div>
                            <div class="nid-data-val">{{ $device->agent_id ?? 'F9EF-7EB5-681D-C5B9' }}</div>
                        </div>
                        <div class="nid-data-item"></div>
                    </div>
                </div>
            </div>

            {{-- Operating System --}}
            <div class="nid-section open" id="sec-os">
                <div class="nid-sec-head" onclick="nidToggleSec('os')">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Operating System
                    </div>
                    <span class="nid-sec-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></span>
                </div>
                <div class="nid-sec-body">
                    <div class="nid-data-grid">
                        <div class="nid-data-item">
                            <div class="nid-data-key">Operating System</div>
                            <div class="nid-data-val">{{ $os->name ?? 'Microsoft Windows 10 Pro' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Manufacturer</div>
                            <div class="nid-data-val">{{ $os->manufacturer_ip ?? '192.168.29.86' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">License Activated</div>
                            <div class="nid-data-val">{{ $os->license_activated ?? 'Active' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Version</div>
                            <div class="nid-data-val">{{ $os->version ?? '10.0.19045' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Microsoft Corporation</div>
                            <div class="nid-data-val">{{ $os->corp ?? 'fe80::d4:c6d6:ce51:b5002' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Serial Number</div>
                            <div class="nid-data-val">{{ $os->serial ?? '00330-50000-00000-AAOEM' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">System Drive</div>
                            <div class="nid-data-val">{{ $os->system_drive ?? 'C:' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Service Pack</div>
                            <div class="nid-data-val">{{ $os->service_pack ?? 'NA' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Architecture</div>
                            <div class="nid-data-val">{{ $os->architecture ?? '64-bit' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">License Status</div>
                            <div class="nid-data-val">{{ $os->license_status ?? 'Licensed' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">OS Installed Directory</div>
                            <div class="nid-data-val">{{ $os->install_dir ?? 'C:\Windows\system32' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">License Product Key ID</div>
                            <div class="nid-data-val" style="font-size:11.5px;">{{ $os->product_key_id ?? '03612-03305-000-000000-02-1033-19045.0000-1192026' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Windows Version</div>
                            <div class="nid-data-val">{{ $os->windows_version ?? '22H2' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">License Product Key</div>
                            <div class="nid-data-val">{{ $os->product_key ?? 'WFG6P' }}</div>
                        </div>
                        <div class="nid-data-item"></div>
                    </div>
                </div>
            </div>

            {{-- Processor --}}
            <div class="nid-section open" id="sec-processor">
                <div class="nid-sec-head" onclick="nidToggleSec('processor')">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                            <rect x="9" y="9" width="6" height="6"/>
                            <line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/>
                            <line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/>
                            <line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/>
                            <line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>
                        </svg>
                        Processor
                    </div>
                    <span class="nid-sec-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></span>
                </div>
                <div class="nid-sec-body">
                    <div class="nid-data-grid">
                        <div class="nid-data-item">
                            <div class="nid-data-key">Name</div>
                            <div class="nid-data-val">{{ $cpu->name ?? 'Intel® Core™ i7-6650U CPU @ 2.20GHz' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Architecture</div>
                            <div class="nid-data-val">{{ $cpu->architecture ?? 'x64' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Frequency</div>
                            <div class="nid-data-val">{{ $cpu->frequency ?? '2201 MHz' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Manufacturer</div>
                            <div class="nid-data-val">{{ $cpu->manufacturer ?? 'GenuineIntel' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Thread Count</div>
                            <div class="nid-data-val">{{ $cpu->thread_count ?? '4' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Serial</div>
                            <div class="nid-data-val">{{ $cpu->serial ?? 'BFEBFBFF000406E3' }}</div>
                        </div>
                        <div class="nid-grid-divider"></div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Family</div>
                            <div class="nid-data-val">{{ $cpu->family ?? '198' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Number of Cores</div>
                            <div class="nid-data-val">{{ $cpu->cores ?? '2' }}</div>
                        </div>
                        <div class="nid-data-item"></div>
                    </div>
                </div>
            </div>

            {{-- BIOS --}}
            <div class="nid-section open" id="sec-bios">
                <div class="nid-sec-head" onclick="nidToggleSec('bios')">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="2" y="6" width="20" height="12" rx="2"/>
                            <path d="M6 12h.01M10 12h.01M14 12h.01M18 12h.01"/>
                        </svg>
                        BIOS
                    </div>
                    <span class="nid-sec-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></span>
                </div>
                <div class="nid-sec-body">
                    <div class="nid-data-grid">
                        <div class="nid-data-item">
                            <div class="nid-data-key">Name</div>
                            <div class="nid-data-val">{{ $bios->name ?? '1.20.3' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Manufacturer</div>
                            <div class="nid-data-val">{{ $bios->manufacturer ?? 'Dell Inc.' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Release Date</div>
                            <div class="nid-data-val">{{ $bios->release_date ?? '2018-08-20' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agent Details --}}
            <div class="nid-section open" id="sec-agent">
                <div class="nid-sec-head" onclick="nidToggleSec('agent')">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        Agent Details
                    </div>
                    <span class="nid-sec-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></span>
                </div>
                <div class="nid-sec-body">
                    <div class="nid-data-grid">
                        <div class="nid-data-item">
                            <div class="nid-data-key">Agent Type</div>
                            <div class="nid-data-val">{{ $agent->type ?? 'Non AD' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Agent Version</div>
                            <div class="nid-data-val">{{ $agent->version ?? '2.6.7.10' }}</div>
                        </div>
                        <div class="nid-data-item">
                            <div class="nid-data-key">Last Update</div>
                            <div class="nid-data-val">{{ $agent->last_update ?? '2018-08-20' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /nid-panel-information --}}

        {{-- ── OTHER PANELS (placeholder) ── --}}
        @foreach([
            ['program',               'Program',               'rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"'],
            ['memory',                'Memory',                'path d="M6 19v-3"/><path d="M10 19v-3"/><path d="M14 19v-3"/><path d="M18 19v-3"/><rect x="2" y="4" width="20" height="12" rx="2"'],
            ['driver',                'Driver',                'circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"'],
            ['adapters',              'Adapters',              'path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"'],
            ['monitors',              'Monitors',              'rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"'],
            ['accounts',              'Accounts',              'path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"'],
            ['environment_variables', 'Environment Variables', 'polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"'],
            ['outlook_accounts',      'Outlook Accounts',      'path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"'],
            ['usb_ports',             'USB Ports',             'path d="M12 2v10M8 6l4-4 4 4"/><path d="M8 14H6a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2m8-6h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2"/><rect x="8" y="14" width="8" height="6" rx="1"'],
            ['changes',               'Changes',               'polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"'],
        ] as [$pid,$plabel,$picon])
        <div class="nid-tab-panel" id="nid-panel-{{ $pid }}" style="display:none;">
            <div class="nid-section open">
                <div class="nid-sec-head">
                    <div class="nid-sec-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! '<'.$picon.'/>' !!}</svg>
                        {{ $plabel }}
                    </div>
                </div>
                <div class="nid-sec-body" style="text-align:center;padding:32px 16px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto 10px;">{!! '<'.$picon.'/>' !!}</svg>
                    <p style="font-size:13px;color:#9ca3af;margin:0;">No {{ strtolower($plabel) }} data available.</p>
                </div>
            </div>
        </div>
        @endforeach

    </div>{{-- /nid-content --}}
</div>{{-- /nid-body --}}
</main>

<script>
/* ── Nav switch ───────────────────────────────────── */
function nidNav(id, btn) {
    document.querySelectorAll('.nid-nav-item').forEach(function(n) { n.classList.remove('active'); });
    document.querySelectorAll('.nid-tab-panel').forEach(function(p) { p.style.display = 'none'; });
    btn.classList.add('active');
    var panel = document.getElementById('nid-panel-' + id);
    if (panel) panel.style.display = 'flex';
    // information panel uses flex-col
    if (id === 'information') { panel.style.display = 'block'; }
}

/* ── Section toggle (collapse/expand) ────────────── */
function nidToggleSec(id) {
    var sec = document.getElementById('sec-' + id);
    sec.classList.toggle('closed');
}

/* ── Nav search filter ────────────────────────────── */
function nidNavFilter(q) {
    var lq = q.toLowerCase();
    document.querySelectorAll('.nid-nav-item').forEach(function(btn) {
        btn.style.display = btn.textContent.trim().toLowerCase().includes(lq) ? '' : 'none';
    });
}
</script>
@endsection