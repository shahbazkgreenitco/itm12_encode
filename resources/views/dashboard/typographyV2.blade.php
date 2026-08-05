@extends('layouts.layout1')
@section('title', 'Design System')

@section('content')

    <!-- CONTENT -->
    <main class="main-content" id="mainContent">

        <div class="container-fluid">
            <div class="card bg-transparent bg-none shadow-none">

                <div class="doc-section mt-5">
                    <h2>Typography System — CSS Tokens</h2>

                    <p class="text-muted">
                        AMG typography is powered by CSS variables using the Inter font family.
                        Copy this block into your project to enable the full typography system.
                    </p>

                    <pre><code>
                /* Typography System */
                @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

        :root {

            --font-primary: 'Inter', system-ui, -apple-system, sans-serif;

            --font-regular: 400;
            --font-medium: 500;
            --font-semi-bold: 600;
            --font-bold: 700;

            /* H1 / H2 — Title */
            --font-size-h1: 24px;
            --font-size-h2: 20px;

            /* H3 - H5 — Headline */
            --font-size-h3: 16px;
            --font-size-h4: 14px;
            --font-size-h5: 14px;

            /* H6 - H9 — Body */
            --font-size-b1: 13px;
            --font-size-b2: 13px;
            --font-size-b3: 12px;
            --font-size-b4: 12px;

            /* H10 — Caption */
            --font-size-caption: 10px;

            --line-height-h1: 120%;
            --line-height-h2: 120%;
            --line-height-h3: 120%;
            --line-height-h4: 120%;
            --line-height-h5: 120%;
            --line-height-b1: 140%;
            --line-height-b2: 120%;
            --line-height-b3: 140%;
            --line-height-b4: 120%;
            --line-height-caption: 100%;

            --weight-h1: var(--font-semi-bold);
            --weight-h2: var(--font-semi-bold);
            --weight-h3: var(--font-semi-bold);
            --weight-h4: var(--font-semi-bold);
            --weight-h5: var(--font-medium);
            --weight-b1: var(--font-regular);
            --weight-b2: var(--font-medium);
            --weight-b3: var(--font-regular);
            --weight-b4: var(--font-medium);
            --weight-b4-italic: var(--font-regular);
            --weight-caption: var(--font-semi-bold);
        }
        </code></pre>

                    <!-- <button class="copy-btn">Copy</button> -->
                </div>

                <div class="doc-section">
                    <h3>Typography Usage Examples</h3>

                    <div class="preview">

                        <p style="font-size:var(--font-size-h1);line-height:var(--line-height-h1);font-weight:var(--weight-h1);margin-bottom:8px;">H1. Title 1</p>
                        <p style="font-size:var(--font-size-h2);line-height:var(--line-height-h2);font-weight:var(--weight-h2);margin-bottom:8px;">H2. Title 2</p>
                        <p style="font-size:var(--font-size-h3);line-height:var(--line-height-h3);font-weight:var(--weight-h3);margin-bottom:8px;">H3. Headline 1</p>
                        <p style="font-size:var(--font-size-h4);line-height:var(--line-height-h4);font-weight:var(--weight-h4);margin-bottom:8px;">H4. Headline 2</p>
                        <p style="font-size:var(--font-size-h5);line-height:var(--line-height-h5);font-weight:var(--weight-h5);margin-bottom:8px;">H5. Headline 3</p>

                        <p style="font-size:var(--font-size-b1);line-height:var(--line-height-b1);font-weight:var(--weight-b1);margin-bottom:8px;">H6. Body (Primary)</p>
                        <p style="font-size:var(--font-size-b2);line-height:var(--line-height-b2);font-weight:var(--weight-b2);margin-bottom:8px;">H7. Body</p>
                        <p style="font-size:var(--font-size-b3);line-height:var(--line-height-b3);font-weight:var(--weight-b3);margin-bottom:8px;">H8. Body</p>
                        <p style="font-size:var(--font-size-b4);line-height:var(--line-height-b4);font-weight:var(--weight-b4);margin-bottom:8px;">H9. Body</p>
                        <p style="font-size:var(--font-size-b4);line-height:var(--line-height-b4);font-weight:var(--weight-b4-italic);font-style:italic;margin-bottom:8px;">H9. Body Regular Italic</p>
                        <p style="font-size:var(--font-size-caption);line-height:var(--line-height-caption);font-weight:var(--weight-caption);margin-bottom:0;">H10. Caption</p>

                    </div>

                    <pre><code>
&lt;p class="h1"&gt;Title 1&lt;/p&gt;
&lt;p class="b1"&gt;Body text&lt;/p&gt;
&lt;p class="caption"&gt;Caption text&lt;/p&gt;
</code></pre>

                    <!-- <button class="copy-btn">Copy</button> -->
                </div>

                <div class="doc-section">
                    <h2>Color System — CSS Tokens</h2>
                    <p class="text-muted">Semantic color tokens for background, buttons, text, icons and borders (light theme shown; dark-mode values in comments).</p>

                    {{-- <pre><code>
:root {

    /* Background Colors */
    --bg-surface: #F8FAFD;
    --bg-base-white: #FFFFFF;
    --bg-header-nav: #EFF2FA;
    --bg-primary-nav: #001236;
    --bg-header-gradient-1: #F6ECF8;
    --bg-header-gradient-2: #FFF1E8;

    /* Button Colors */
    --btn-active: #F12F35;
    --btn-deactive: #F12F35;
    --btn-secondary: #001B51;
    --btn-black: #001B51;
    --btn-accept: #186B43;
    --btn-reject: #C73338;

    /* Text Colors */
    --text-primary: #000000;
    --text-secondary: #7F7F7F;
    --text-label: #515151;

    /* Icon Colors */
    --icon-grey: #7F7F7F;
    --icon-black: #000000;
    --icon-white: #FFFFFF;

    /* Border Colors */
    --border-light: #F0F0F0;
    --border-dark: #D9D9D9;

    /* Dark mode equivalents */
    /* --bg-surface: #141414; --bg-header-nav: #111111; --bg-primary-nav: #191919; */
    /* --btn-primary: #C80208; --btn-surface: #191919; --btn-white: #F4F4F4; */
    /* --text-primary: #F7F7F7; --text-secondary: #7F7F7F; --text-label: #7F7F7F; */
}
        </code></pre> --}}

                    <style>
                        .swatch-row{
                            display:flex;
                            flex-wrap:wrap;
                            gap:16px;
                        }
                        .swatch-card{
                            width:170px;
                            background:#fff;
                            border-radius:14px;
                            padding:10px;
                            text-align:center;
                            box-shadow:0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
                        }
                        .swatch-color{
                            height:64px;
                            border-radius:10px;
                            margin-bottom:12px;
                        }
                        .swatch-label{
                            font-size:14px;
                            font-weight:500;
                            color:#374151;
                            margin-bottom:2px;
                        }
                        .swatch-hex{
                            font-size:13px;
                            color:#9CA3AF;
                            margin-bottom:12px;
                        }
                        .swatch-copy-btn{
                            background:#111111;
                            color:#fff;
                            border:none;
                            border-radius:8px;
                            padding:8px 20px;
                            font-size:13px;
                            font-weight:500;
                            cursor:pointer;
                        }
                        .swatch-copy-btn:hover{
                            background:#2a2a2a;
                        }
                    </style>

                    <h3 class="mt-4">Background Colors</h3>
                    <div class="swatch-row mt-2">
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#F8FAFD;border:1px solid #eee"></div>
                            <div class="swatch-label">Surface</div>
                            <div class="swatch-hex">#F8FAFD</div>
                            <button class="swatch-copy-btn" data-copy="#F8FAFD">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#FFFFFF;border:1px solid #eee"></div>
                            <div class="swatch-label">Base White</div>
                            <div class="swatch-hex">#FFFFFF</div>
                            <button class="swatch-copy-btn" data-copy="#FFFFFF">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#EFF2FA"></div>
                            <div class="swatch-label">Header Nav</div>
                            <div class="swatch-hex">#EFF2FA</div>
                            <button class="swatch-copy-btn" data-copy="#EFF2FA">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#001236"></div>
                            <div class="swatch-label">Primary Nav</div>
                            <div class="swatch-hex">#001236</div>
                            <button class="swatch-copy-btn" data-copy="#001236">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#F6ECF8"></div>
                            <div class="swatch-label">Header Gradient 1</div>
                            <div class="swatch-hex">#F6ECF8</div>
                            <button class="swatch-copy-btn" data-copy="#F6ECF8">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#FFF1E8"></div>
                            <div class="swatch-label">Header Gradient 2</div>
                            <div class="swatch-hex">#FFF1E8</div>
                            <button class="swatch-copy-btn" data-copy="#FFF1E8">Copy</button>
                        </div>
                    </div>

                    <h3 class="mt-5">Button Colors</h3>
                    <div class="swatch-row mt-2">
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#F12F35"></div>
                            <div class="swatch-label">Active</div>
                            <div class="swatch-hex">#F12F35</div>
                            <button class="swatch-copy-btn" data-copy="#F12F35">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#F12F35;opacity:0.55"></div>
                            <div class="swatch-label">Deactive</div>
                            <div class="swatch-hex">#F12F35</div>
                            <button class="swatch-copy-btn" data-copy="#F12F35">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#001B51"></div>
                            <div class="swatch-label">Secondary Button</div>
                            <div class="swatch-hex">#001B51</div>
                            <button class="swatch-copy-btn" data-copy="#001B51">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#000000"></div>
                            <div class="swatch-label">Black Button</div>
                            <div class="swatch-hex">#001B51</div>
                            <button class="swatch-copy-btn" data-copy="#001B51">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#186B43"></div>
                            <div class="swatch-label">Accept</div>
                            <div class="swatch-hex">#186B43</div>
                            <button class="swatch-copy-btn" data-copy="#186B43">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#C73338"></div>
                            <div class="swatch-label">Reject</div>
                            <div class="swatch-hex">#C73338</div>
                            <button class="swatch-copy-btn" data-copy="#C73338">Copy</button>
                        </div>
                    </div>

                    <h3 class="mt-5">Text Colors</h3>
                    <div class="swatch-row mt-2">
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#000000"></div>
                            <div class="swatch-label">Primary</div>
                            <div class="swatch-hex">#000000</div>
                            <button class="swatch-copy-btn" data-copy="#000000">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#7F7F7F"></div>
                            <div class="swatch-label">Secondary</div>
                            <div class="swatch-hex">#7F7F7F</div>
                            <button class="swatch-copy-btn" data-copy="#7F7F7F">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#515151"></div>
                            <div class="swatch-label">Label</div>
                            <div class="swatch-hex">#515151</div>
                            <button class="swatch-copy-btn" data-copy="#515151">Copy</button>
                        </div>
                    </div>

                    <h3 class="mt-5">Icon Colors</h3>
                    <div class="swatch-row mt-2">
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#7F7F7F"></div>
                            <div class="swatch-label">Icons Grey</div>
                            <div class="swatch-hex">#7F7F7F</div>
                            <button class="swatch-copy-btn" data-copy="#7F7F7F">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#000000"></div>
                            <div class="swatch-label">Icon Black</div>
                            <div class="swatch-hex">#000000</div>
                            <button class="swatch-copy-btn" data-copy="#000000">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#FFFFFF;border:1px solid #eee"></div>
                            <div class="swatch-label">Icons White</div>
                            <div class="swatch-hex">#FFFFFF</div>
                            <button class="swatch-copy-btn" data-copy="#FFFFFF">Copy</button>
                        </div>
                    </div>

                    <h3 class="mt-5">Border Colors</h3>
                    <div class="swatch-row mt-2">
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#F0F0F0"></div>
                            <div class="swatch-label">Border Light</div>
                            <div class="swatch-hex">#F0F0F0</div>
                            <button class="swatch-copy-btn" data-copy="#F0F0F0">Copy</button>
                        </div>
                        <div class="swatch-card">
                            <div class="swatch-color" style="background:#D9D9D9"></div>
                            <div class="swatch-label">Border Dark</div>
                            <div class="swatch-hex">#D9D9D9</div>
                            <button class="swatch-copy-btn" data-copy="#D9D9D9">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- ================= STATUS BADGES ================= -->
                <div class="doc-section">
                    <h1>Status Badges</h1>
                    <p class="text-muted">Two badge styles: a soft/outline style for lists and secondary context, and a solid style for priority, SLA and high-emphasis status.</p>

                    <style>
                        .badge-group-title{font-weight:600;margin-bottom:12px;}
                        .badge-group{border:1px solid #eee;border-radius:8px;padding:24px;min-height:120px;}
                        .badge-stack{display:flex;flex-direction:column;gap:8px;border:1px dashed #b06de0;border-radius:8px;padding:12px;}
                        .badge-pill{display:inline-block;padding:4px 12px;border-radius:4px;font-size:13px;font-weight:500;text-align:center;}

                        /* Soft / outline variants */
                        .badge-soft-green{background:#EDFFEB;color:#044E00;}
                        .badge-soft-gray{background:#EFEFEF;color:#353535;}
                        .badge-soft-purple{background:#FEECFF;color:#881287;}
                        .badge-soft-blue{background:#DFEBFF;color:#25227B;}
                        .badge-soft-orange{background:#F9EECF;color:#664200;}
                        .badge-soft-red{background:#FFE8E8;color:#AF0000;}
                        .badge-soft-pink{background:#EFE8FF;color:#56126D;}
                        .badge-soft-cyan{background:#E8F9FF;color:#22327B;}
                        .badge-soft-white{background:#FFFFFF;color:#000000;border:1px solid #DFDFE1;}

                        /* Solid variants */
                        .badge-solid-critical{background:#B61F24;color:#fff;}
                        .badge-solid-high{background:#F2671B;color:#fff;}
                        .badge-solid-medium{background:#E28F16;color:#fff;}
                        .badge-solid-low{background:#018E86;color:#fff;}
                        .badge-solid-sla{background:#018E86;color:#fff;}
                        .badge-solid-breach-warn{background:#E28F16;color:#fff;}
                        .badge-solid-breach{background:#B61F24;color:#fff;}
                        .badge-solid-magenta{background:#A01BA2;color:#fff;}
                        .badge-solid-blue{background:#0158BB;color:#fff;}
                        .badge-solid-orange{background:#E28F16;color:#fff;}
                        .badge-solid-gray{background:#B4B9C5;color:#fff;}
                        .badge-solid-teal{background:#018E86;color:#fff;}
                        .badge-solid-vip {
                            background: linear-gradient(
                                90deg,
                                #011345 0%,
                                #0037D8 35%,
                                #04D5B6 70%,
                                #02F0FD 100%
                            );
                            color: #fff;
                        }
                        .badge-solid-purple{background:#A01BA2;color:#fff;}
                        .badge-solid-red{background:#B61F24;color:#fff;}
                    </style>

                    <!-- Soft badges -->
                    <h3 class="mt-4">Soft Badges (lists / secondary status)</h3>
                    <div class="row g-4 mt-1">
                        <div class="col-md-3">
                            <p class="badge-group-title">Device Status</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-soft-green">Ready to Deploy</span>
                                    <span class="badge-pill badge-soft-gray">Deployed</span>
                                    <span class="badge-pill badge-soft-purple">Ready on Stock</span>
                                    <span class="badge-pill badge-soft-blue">Pending</span>
                                    <span class="badge-pill badge-soft-orange">Out for Repair</span>
                                    <span class="badge-pill badge-soft-red">Lost or Stolen</span>
                                    <span class="badge-pill badge-soft-purple">Repair in House</span>
                                    <span class="badge-pill badge-soft-cyan">Scrap</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Service Ticket</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-soft-red">Open</span>
                                    <span class="badge-pill badge-soft-pink">Re Open</span>
                                    <span class="badge-pill badge-soft-orange">In Progress</span>
                                    <span class="badge-pill badge-soft-blue">On Hold</span>
                                    <span class="badge-pill badge-soft-green">Resolved</span>
                                    <span class="badge-pill badge-soft-gray">Closed</span>
                                    <span class="badge-pill badge-soft-purple">Waiting for the User</span>
                                    <span class="badge-pill badge-soft-purple">Waiting for the Vendor</span>
                                    <span class="badge-pill badge-soft-blue">Waiting for Approval</span>
                                    <span class="badge-pill badge-soft-white">Spam</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Change Management</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-soft-red">Open</span>
                                    <span class="badge-pill badge-soft-orange">Planning</span>
                                    <span class="badge-pill badge-soft-green">Approved</span>
                                    <span class="badge-pill badge-soft-blue">Waiting for Approval</span>
                                    <span class="badge-pill badge-soft-purple">Pending Release</span>
                                    <span class="badge-pill badge-soft-purple">Pending Review</span>
                                    <span class="badge-pill badge-soft-orange">In Progress</span>
                                    <span class="badge-pill badge-soft-blue">On Hold</span>
                                    <span class="badge-pill badge-soft-gray">Closed</span>
                                    <span class="badge-pill badge-soft-pink">Rejected</span>
                                    <span class="badge-pill badge-soft-green">Roll Out</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Users</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-soft-purple">Super Admin</span>
                                    <span class="badge-pill badge-soft-orange">Admin</span>
                                    <span class="badge-pill badge-soft-green">Technician</span>
                                    <span class="badge-pill badge-soft-blue">User</span>
                                    <span class="badge-pill badge-soft-pink">Vendor</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <pre><code>&lt;span class="badge-pill badge-soft-green"&gt;Ready to Deploy&lt;/span&gt;
&lt;span class="badge-pill badge-soft-red"&gt;Open&lt;/span&gt;</code></pre>
                    <button class="copy-btn">Copy</button>

                    <!-- Solid badges -->
                    <h3 class="mt-5">Solid Badges (priority / SLA / emphasis)</h3>
                    <div class="row g-4 mt-1">
                        <div class="col-md-3">
                            <p class="badge-group-title">Device Status — Priority</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-solid-critical">Critical</span>
                                    <span class="badge-pill badge-solid-high">High</span>
                                    <span class="badge-pill badge-solid-medium">Medium</span>
                                    <span class="badge-pill badge-solid-low">Low</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Service Ticket — SLA</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-solid-sla">SLA</span>
                                    <span class="badge-pill badge-solid-breach-warn">About to Breach</span>
                                    <span class="badge-pill badge-solid-breach">Breached</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Change Management</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-solid-magenta">New Ticket</span>
                                    <span class="badge-pill badge-solid-blue">Handler Updated</span>
                                    <span class="badge-pill badge-solid-orange">Guest Updated</span>
                                    <span class="badge-pill badge-solid-gray">Spam</span>
                                    <span class="badge-pill badge-solid-teal">Normal</span>
                                    <span class="badge-pill badge-solid-vip">VIP</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <p class="badge-group-title">Users</p>
                            <div class="badge-group">
                                <div class="badge-stack">
                                    <span class="badge-pill badge-solid-purple">Standard</span>
                                    <span class="badge-pill badge-solid-red">Emergency</span>
                                    <span class="badge-pill badge-solid-orange">Major</span>
                                    <span class="badge-pill badge-solid-teal">Minor</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <pre><code>&lt;span class="badge-pill badge-solid-critical"&gt;Critical&lt;/span&gt;
&lt;span class="badge-pill badge-solid-vip"&gt;VIP&lt;/span&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>


                <h1>Buttons</h1>
                <p>Reusable AMG button components with copy-paste-ready markup.</p>

                <!-- Primary -->
                <div class="doc-section">
                    <h3>Primary Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-primary">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-primary"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Secondary -->
                <div class="doc-section">
                    <h3>Secondary Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-secondary">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-secondary"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Outline -->
                <div class="doc-section">
                    <h3>Outline Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-outline">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-outline"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Ghost -->
                <div class="doc-section">
                    <h3>Ghost Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-ghost">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-ghost"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Inactive -->
                <div class="doc-section">
                    <h3>Inactive Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-inactive">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-inactive"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Link -->
                <div class="doc-section">
                    <h3>Link Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-link">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-link"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Link Plain -->
                <div class="doc-section">
                    <h3>Plain Link Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-link amg-btn-link-plain">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-link amg-btn-link-plain"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Disabled -->
                <div class="doc-section">
                    <h3>Disabled Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-primary" disabled>Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-primary" disabled&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Loading -->
                <div class="doc-section">
                    <h3>Loading Button</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-primary amg-btn-loading">Save Changes</button>
                    </div>
                    <pre><code>&lt;button class="amg-btn amg-btn-primary amg-btn-loading"&gt;Save Changes&lt;/button&gt;</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Sizes -->
                <div class="doc-section">
                    <h3>Button Sizes</h3>
                    <div class="preview d-flex gap-2 flex-wrap">
                        <button class="amg-btn amg-btn-primary amg-btn-lg">Large</button>
                        <button class="amg-btn amg-btn-primary amg-btn-md">Medium</button>
                        <button class="amg-btn amg-btn-primary amg-btn-sm">Small</button>
                    </div>
                    <pre><code>
&lt;button class="amg-btn amg-btn-primary amg-btn-lg"&gt;Large&lt;/button&gt;
&lt;button class="amg-btn amg-btn-primary amg-btn-md"&gt;Medium&lt;/button&gt;
&lt;button class="amg-btn amg-btn-primary amg-btn-sm"&gt;Small&lt;/button&gt;
</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>

                <!-- Layout -->
                <div class="doc-section">
                    <h3>Button Layout</h3>
                    <div class="preview">
                        <button class="amg-btn amg-btn-primary amg-btn-block mb-2">Block Button</button>
                        <button class="amg-btn amg-btn-primary amg-btn-auto">Auto Button</button>
                    </div>
                    <pre><code>
&lt;button class="amg-btn amg-btn-primary amg-btn-block"&gt;Block Button&lt;/button&gt;
&lt;button class="amg-btn amg-btn-primary amg-btn-auto"&gt;Auto Button&lt;/button&gt;
</code></pre>
                    <button class="copy-btn">Copy</button>
                </div>


                <div class="color-guide">
                    <div class="doc-section">
                        <h1>Icons</h1>
                        <div class="col-md-8 type-meta">
                            For menu and submenu we are using svg icons and if you want icons in design we have iconify
                            library in
                            our design theme
                        </div>
                        <iframe src="https://icons.getbootstrap.com/" height="315" class="w-100"title="Iconify"
                            allowfullscreen></iframe>
                    </div>

                    <style>
                        /* ===============================
           Form Field Section
        ================================ */

                        .doc-subtitle {
                            color: #666;
                            margin-bottom: 24px;
                        }

                        .form-grid {
                            display: grid;
                            grid-template-columns: repeat(3, 1fr);
                            gap: 32px;
                            background: #fff;
                            border-radius: 16px;
                            padding: 24px;
                        }

                        .form-example {
                            padding-right: 24px;
                        }

                        .example-title {
                            font-size: 13px;
                            color: #666;
                            margin-bottom: 16px;
                        }

                        /* ===============================
           Form Field Core
        ================================ */

                        .form-field {
                            display: flex;
                            flex-direction: column;
                        }

                        .field-label {
                            display: flex;
                            align-items: center;
                            gap: 4px;
                            font-size: 14px;
                            font-weight: 600;
                            margin-bottom: 6px;
                            color: #111;
                        }

                        .required {
                            color: #F12F35;
                        }

                        .info {
                            font-size: 12px;
                            color: #777;
                            cursor: pointer;
                        }

                        /* ===============================
           Controls
        ================================ */

                        .form-control {
                            height: 40px;
                            padding: 0 12px;
                            font-size: 14px;
                            border-radius: 6px;
                            border: 1px solid #d0d0d0;
                            transition: border 0.15s ease;
                        }

                        .form-control:focus {
                            outline: none;
                            border-color: #001B51;
                        }

                        .form-control.textarea {
                            height: auto;
                            min-height: 80px;
                            padding: 8px 12px;
                            resize: vertical;
                        }

                        /* ===============================
                        Error State
                        ================================ */

                        .form-field.error .form-control {
                            border-color: #F12F35;
                        }

                        .field-error {
                            margin-top: 6px;
                            font-size: 12px;
                            color: #F12F35;
                        }

                        /* ===============================
                        Form Layout – Two Column
                        ================================ */

                        .form-layout {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 24px 32px;
                            max-width: 1000px;
                        }

                        /* Responsive */
                        @media (max-width: 768px) {
                            .form-layout {
                                grid-template-columns: 1fr;
                            }
                        }

                        /* ===============================
                            Label with Icon
                        ================================ */

                        .field-label.with-icon {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                        }

                        .field-label .icon {
                            font-size: 16px;
                            color: #666;
                        }

                        /* Inputs (already defined earlier, included for clarity) */

                        .form-field select.form-control {
                            appearance: none;
                            -webkit-appearance: none;
                            -moz-appearance: none;

                            width: 100%;
                            height: 40px;
                            padding: 0 44px 0 12px;
                            font-size: 14px;

                            border-radius: 6px;
                            border: 1px solid #d0d0d0;
                            background-color: #fff;

                            background-image:
                                linear-gradient(45deg, transparent 50%, #666 50%),
                                linear-gradient(135deg, #666 50%, transparent 50%);
                            background-position:
                                calc(100% - 20px) 17px,
                                calc(100% - 14px) 17px;
                            background-size:
                                6px 6px,
                                6px 6px;
                            background-repeat: no-repeat;

                            cursor: pointer;
                        }

                        .form-field select.form-control:focus {
                            outline: none;
                            border-color: #001B51;
                        }

                        .form-field select.form-control:disabled {
                            background-color: #f5f5f5;
                            cursor: not-allowed;
                        }

                        .form-field.error select.form-control {
                            border-color: #F12F35;
                        }
                    </style>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.swatch-copy-btn');
            if (!btn) return;

            const value = btn.getAttribute('data-copy');
            if (!value) return;

            const finish = () => {
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                btn.disabled = true;
                setTimeout(() => {
                    btn.textContent = original;
                    btn.disabled = false;
                }, 1200);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value).then(finish).catch(() => fallbackCopy(value, finish));
            } else {
                fallbackCopy(value, finish);
            }
        });

        function fallbackCopy(text, cb) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try { document.execCommand('copy'); } catch (err) {}
            document.body.removeChild(ta);
            cb();
        }
    </script>
@endsection