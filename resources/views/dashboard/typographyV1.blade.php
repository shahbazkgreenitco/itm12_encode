@extends('layouts.layout1')
@section('title', 'Typography')

@section('content')

    <!-- CONTENT -->
    <main class="main-content" id="mainContent">

        <div class="container-fluid">
            <div class="card bg-transparent">

                <div class="doc-section">
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

            --font-size-h1: 28px;
            --font-size-h2: 24px;
            --font-size-h3: 20px;
            --font-size-s1: 18px;
            --font-size-s2: 16px;
            --font-size-b1: 16px;
            --font-size-b2: 16px;
            --font-size-b3: 14px;
            --font-size-b4: 14px;

            --line-height-h1: 34px;
            --line-height-h2: 28px;
            --line-height-h3: 28px;
            --line-height-s1: 28px;
            --line-height-s2: 24px;
            --line-height-b1: 24px;
            --line-height-b2: 24px;
            --line-height-b3: 20px;
            --line-height-b4: 20px;
        }
        </code></pre>

                    <!-- <button class="copy-btn">Copy</button> -->
                </div>

                <div class="doc-section">
                    <h3>Typography Usage Examples</h3>

                    <div class="preview">

                        <h1>H1 — Headline</h1>
                        <h2>H2 — Page Title</h2>
                        <h3>H3 — Headline</h3>

                        <p class="s1">S1 — Secondary Subtitle</p>
                        <p class="s2">S2 — Primary Subtitle</p>

                        <p class="b1">B1 — Primary body text</p>
                        <p class="b2">B2 — Medium emphasis body</p>
                        <p class="b3">B3 — Helper text</p>
                        <p class="b4">B4 — Metadata text</p>

                    </div>

                    <pre><code>
<h1>Headline</h1>
<p class="b1">Body text</p>
<p class="s2">Subtitle</p>
</code></pre>

                    <!-- <button class="copy-btn">Copy</button> -->
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

                    <h1>Colors</h1>
                    <p>System color palette with semantic grouping and copy-ready values.</p>

                    <!-- ================= Brand Colors ================= -->
                    <div class="doc-section">
                        <h3>Brand Colors</h3>

                        <div class="color-grid">

                            <div class="color-card">
                                <div class="color-swatch" style="background:#F12F35"></div>
                                <div class="color-meta">
                                    <span>Primary</span>
                                    <span>#F12F35</span>
                                </div>
                                <button class="copy-btn" data-copy="#F12F35">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#001B51"></div>
                                <div class="color-meta">
                                    <span>Secondary</span>
                                    <span>#001B51</span>
                                </div>
                                <button class="copy-btn" data-copy="#001B51">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#000000"></div>
                                <div class="color-meta">
                                    <span>Primary Text</span>
                                    <span>#000000</span>
                                </div>
                                <button class="copy-btn" data-copy="#000000">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#E8E8E8"></div>
                                <div class="color-meta">
                                    <span>Outline</span>
                                    <span>#E8E8E8</span>
                                </div>
                                <button class="copy-btn" data-copy="#E8E8E8">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#FFFFFF;border:1px solid #ddd"></div>
                                <div class="color-meta">
                                    <span>White</span>
                                    <span>#FFFFFF</span>
                                </div>
                                <button class="copy-btn" data-copy="#FFFFFF">Copy</button>
                            </div>

                        </div>
                    </div>

                    <!-- ================= Status ================= -->
                    <div class="doc-section">
                        <h3>Status</h3>

                        <div class="color-grid">

                            <div class="color-card">
                                <div class="color-swatch" style="background:#006FFD"></div>
                                <div class="color-meta">
                                    <span>Open</span>
                                    <span>#006FFD</span>
                                </div>
                                <button class="copy-btn" data-copy="#006FFD">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#EF7C27"></div>
                                <div class="color-meta">
                                    <span>In Progress</span>
                                    <span>#EF7C27</span>
                                </div>
                                <button class="copy-btn" data-copy="#EF7C27">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#6E6E6E"></div>
                                <div class="color-meta">
                                    <span>On Hold</span>
                                    <span>#6E6E6E</span>
                                </div>
                                <button class="copy-btn" data-copy="#6E6E6E">Copy</button>
                            </div>

                        </div>
                    </div>

                    <!-- ================= Priority ================= -->
                    <div class="doc-section">
                        <h3>Priority</h3>

                        <div class="color-grid">

                            <div class="color-card">
                                <div class="color-swatch" style="background:#F73019"></div>
                                <div class="color-meta">
                                    <span>Critical</span>
                                    <span>#F73019</span>
                                </div>
                                <button class="copy-btn" data-copy="#F73019">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#F26F1B"></div>
                                <div class="color-meta">
                                    <span>High</span>
                                    <span>#F26F1B</span>
                                </div>
                                <button class="copy-btn" data-copy="#F26F1B">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#F3AE2C"></div>
                                <div class="color-meta">
                                    <span>Medium</span>
                                    <span>#F3AE2C</span>
                                </div>
                                <button class="copy-btn" data-copy="#F3AE2C">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#13AC53"></div>
                                <div class="color-meta">
                                    <span>Low 2</span>
                                    <span>#13AC53</span>
                                </div>
                                <button class="copy-btn" data-copy="#13AC53">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#AEE587"></div>
                                <div class="color-meta">
                                    <span>Low</span>
                                    <span>#AEE587</span>
                                </div>
                                <button class="copy-btn" data-copy="#AEE587">Copy</button>
                            </div>

                        </div>
                    </div>

                    <!-- ================= SLA ================= -->
                    <div class="doc-section">
                        <h3>SLA Timing</h3>

                        <div class="color-grid">

                            <div class="color-card">
                                <div class="color-swatch" style="background:#018E86"></div>
                                <div class="color-meta">
                                    <span>SLA</span>
                                    <span>#018E86</span>
                                </div>
                                <button class="copy-btn" data-copy="#018E86">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#F7BF2A"></div>
                                <div class="color-meta">
                                    <span>About to Breach</span>
                                    <span>#F7BF2A</span>
                                </div>
                                <button class="copy-btn" data-copy="#F7BF2A">Copy</button>
                            </div>

                            <div class="color-card">
                                <div class="color-swatch" style="background:#B51F24"></div>
                                <div class="color-meta">
                                    <span>Breached</span>
                                    <span>#B51F24</span>
                                </div>
                                <button class="copy-btn" data-copy="#B51F24">Copy</button>
                            </div>

                        </div>
                    </div>


                    <section class="doc-section">
                        <h2>Spacing</h2>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Token</th>
                                    <th>Value</th>
                                    <th>Usage</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>XS</td>
                                    <td>4px</td>
                                    <td>Icon padding</td>
                                    <td>
                                        <button class="copy-btn" data-copy="mb-1">Copy</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>SM</td>
                                    <td>8px</td>
                                    <td>Inline spacing</td>
                                    <td>
                                        <button class="copy-btn" data-copy="mb-2">Copy</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>MD</td>
                                    <td>16px</td>
                                    <td>Inline spacing</td>
                                    <td>
                                        <button class="copy-btn" data-copy="mb-3">Copy</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>LG</td>
                                    <td>24px</td>
                                    <td>Field spacing</td>
                                    <td>
                                        <button class="copy-btn" data-copy="mb-4">Copy</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>XL</td>
                                    <td>32px</td>
                                    <td>Section spacing</td>
                                    <td>
                                        <button class="copy-btn" data-copy="mb-5">Copy</button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>


                        <div class="mb-4">
                            <label class="form-label mb-1">Sample label 1</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="mb-4">
                            <label class="form-label mb-1">Spacing between form input</label>
                            <input type="text" class="form-control">
                        </div>

                        <p> Label → Input = mb-1 / Field → Field = mb-4 </p>


                        <pre>
                <code>
                    &lt;div class="mb-4"&gt;
                    &lt;label class="form-label mb-1"&gt;Asset Name&lt;/label&gt;
                    &lt;input type="text" class="form-control"&gt;
                    &lt;/div&gt;

                    &lt;div class="mb-4"&gt;
                    &lt;label class="form-label mb-1"&gt;Spacing between form input&lt;/label&gt;
                    &lt;input type="text" class="form-control"&gt;
                    &lt;/div&gt;
                </code>
            </pre>

                        <button class="copy-btn">Copy</button>


                    </section>


                    <div class="doc-section">
                        <h1>Icons</h1>
                        <div class="col-md-8 type-meta">
                            For menu and submenu we are using svg icons and if you want icons in design we have iconify
                            library in
                            our design theme
                        </div>
                        <iframe src="https://icon-sets.iconify.design/" width="960" height="315" title="Iconify"
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

                        /* ===============================
           Reuse from Form Field
        ================================ */

                        .form-field {
                            display: flex;
                            flex-direction: column;
                        }

                        .field-label {
                            font-size: 14px;
                            font-weight: 600;
                            margin-bottom: 6px;
                            color: #111;
                        }

                        .required {
                            color: #F12F35;
                        }

                        /* Inputs (already defined earlier, included for clarity) */

                        .form-control {
                            height: 40px;
                            padding: 0 12px;
                            font-size: 14px;
                            border-radius: 6px;
                            border: 1px solid #d0d0d0;
                        }

                        .form-control:focus {
                            outline: none;
                            border-color: #001B51;
                        }

                        /* ===============================
           Select – Minimal Chevron
           (works with existing HTML)
        ================================ */

                        .form-field select.form-control {
                            appearance: none;
                            -webkit-appearance: none;
                            -moz-appearance: none;

                            width: 100%;
                            height: 40px;
                            padding: 0 44px 0 12px;
                            /* right space for arrow */
                            font-size: 14px;

                            border-radius: 6px;
                            border: 1px solid #d0d0d0;
                            background-color: #fff;

                            /* Chevron */
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

                        /* Focus */
                        .form-field select.form-control:focus {
                            outline: none;
                            border-color: #001B51;
                        }

                        /* Disabled */
                        .form-field select.form-control:disabled {
                            background-color: #f5f5f5;
                            cursor: not-allowed;
                        }

                        /* Error state (already matches your system) */
                        .form-field.error select.form-control {
                            border-color: #F12F35;
                        }
                    </style>

                    <!-- labels -->
                    <section class="doc-section">
                        <h2>Label</h2>
                        <p class="doc-subtitle">Form field patterns with label, input control, and validation state.</p>

                        <div class="form-grid">

                            <!-- Input Field -->
                            <div class="form-example">
                                <p class="example-title">Field with Input</p>

                                <div class="form-field error">
                                    <label class="field-label">
                                        Label <span class="required">*</span>
                                        <span class="info"><svg width="14" height="14" viewBox="0 0 14 14"
                                                fill="none" >
                                                <path
                                                    d="M7.49902 6.49998C7.49902 6.22384 7.27517 5.99998 6.99902 5.99998C6.72288 5.99998 6.49902 6.22384 6.49902 6.49998V9.49998C6.49902 9.77612 6.72288 9.99998 6.99902 9.99998C7.27517 9.99998 7.49902 9.77612 7.49902 9.49998V6.49998ZM7.74807 4.50001C7.74807 4.91369 7.41271 5.24905 6.99903 5.24905C6.58535 5.24905 6.25 4.91369 6.25 4.50001C6.25 4.08633 6.58535 3.75098 6.99903 3.75098C7.41271 3.75098 7.74807 4.08633 7.74807 4.50001ZM7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0ZM1 7C1 3.68629 3.68629 1 7 1C10.3137 1 13 3.68629 13 7C13 10.3137 10.3137 13 7 13C3.68629 13 1 10.3137 1 7Z"
                                                    fill="#424242" />
                                            </svg></span>
                                    </label>

                                    <input class="form-control" placeholder="Placeholder text" />

                                    <div class="field-error">Error text</div>
                                </div>
                            </div>

                            <!-- Textarea Field -->
                            <div class="form-example">
                                <p class="example-title">Field with TextArea</p>

                                <div class="form-field">
                                    <label class="field-label">
                                        Company description <span class="info"><svg width="14" height="14"
                                                viewBox="0 0 14 14" fill="none" >
                                                <path
                                                    d="M7.49902 6.49998C7.49902 6.22384 7.27517 5.99998 6.99902 5.99998C6.72288 5.99998 6.49902 6.22384 6.49902 6.49998V9.49998C6.49902 9.77612 6.72288 9.99998 6.99902 9.99998C7.27517 9.99998 7.49902 9.77612 7.49902 9.49998V6.49998ZM7.74807 4.50001C7.74807 4.91369 7.41271 5.24905 6.99903 5.24905C6.58535 5.24905 6.25 4.91369 6.25 4.50001C6.25 4.08633 6.58535 3.75098 6.99903 3.75098C7.41271 3.75098 7.74807 4.08633 7.74807 4.50001ZM7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0ZM1 7C1 3.68629 3.68629 1 7 1C10.3137 1 13 3.68629 13 7C13 10.3137 10.3137 13 7 13C3.68629 13 1 10.3137 1 7Z"
                                                    fill="#424242" />
                                            </svg></span>
                                    </label>

                                    <textarea class="form-control textarea" placeholder="Placeholder text"></textarea>
                                </div>
                            </div>

                            <!-- Dropdown Field -->
                            <div class="form-example">
                                <p class="example-title">Field with Dropdown</p>

                                <div class="form-field error">
                                    <label class="field-label">
                                        Label <span class="required">*</span>
                                    </label>

                                    <select class="form-control">
                                        <option value="">Placeholder text</option>
                                        <option>Option one</option>
                                        <option>Option two</option>
                                    </select>

                                    <div class="field-error">Please make selection</div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- forms -->
                    <section class="doc-section">
                        <h2>Form</h2>

                        <div class="form-layout">

                            <!-- Department -->
                            <div class="form-field">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1"><svg style="opacity:60%;"
                                            width="21" height="21" viewBox="0 0 21 21" fill="none"
                                            >
                                            <path
                                                d="M20.25 19.5H18V1.5H18.75C18.9489 1.5 19.1397 1.42098 19.2803 1.28033C19.421 1.13968 19.5 0.948912 19.5 0.75C19.5 0.551088 19.421 0.360322 19.2803 0.21967C19.1397 0.0790176 18.9489 0 18.75 0H2.25C2.05109 0 1.86032 0.0790176 1.71967 0.21967C1.57902 0.360322 1.5 0.551088 1.5 0.75C1.5 0.948912 1.57902 1.13968 1.71967 1.28033C1.86032 1.42098 2.05109 1.5 2.25 1.5H3V19.5H0.75C0.551088 19.5 0.360322 19.579 0.21967 19.7197C0.0790176 19.8603 0 20.0511 0 20.25C0 20.4489 0.0790176 20.6397 0.21967 20.7803C0.360322 20.921 0.551088 21 0.75 21H20.25C20.4489 21 20.6397 20.921 20.7803 20.7803C20.921 20.6397 21 20.4489 21 20.25C21 20.0511 20.921 19.8603 20.7803 19.7197C20.6397 19.579 20.4489 19.5 20.25 19.5ZM4.5 1.5H16.5V19.5H13.5V15.75C13.5 15.5511 13.421 15.3603 13.2803 15.2197C13.1397 15.079 12.9489 15 12.75 15H8.25C8.05109 15 7.86032 15.079 7.71967 15.2197C7.57902 15.3603 7.5 15.5511 7.5 15.75V19.5H4.5V1.5ZM12 19.5H9V16.5H12V19.5ZM6.75 4.5C6.75 4.30109 6.82902 4.11032 6.96967 3.96967C7.11032 3.82902 7.30109 3.75 7.5 3.75H9C9.19891 3.75 9.38968 3.82902 9.53033 3.96967C9.67098 4.11032 9.75 4.30109 9.75 4.5C9.75 4.69891 9.67098 4.88968 9.53033 5.03033C9.38968 5.17098 9.19891 5.25 9 5.25H7.5C7.30109 5.25 7.11032 5.17098 6.96967 5.03033C6.82902 4.88968 6.75 4.69891 6.75 4.5ZM11.25 4.5C11.25 4.30109 11.329 4.11032 11.4697 3.96967C11.6103 3.82902 11.8011 3.75 12 3.75H13.5C13.6989 3.75 13.8897 3.82902 14.0303 3.96967C14.171 4.11032 14.25 4.30109 14.25 4.5C14.25 4.69891 14.171 4.88968 14.0303 5.03033C13.8897 5.17098 13.6989 5.25 13.5 5.25H12C11.8011 5.25 11.6103 5.17098 11.4697 5.03033C11.329 4.88968 11.25 4.69891 11.25 4.5ZM6.75 8.25C6.75 8.05109 6.82902 7.86032 6.96967 7.71967C7.11032 7.57902 7.30109 7.5 7.5 7.5H9C9.19891 7.5 9.38968 7.57902 9.53033 7.71967C9.67098 7.86032 9.75 8.05109 9.75 8.25C9.75 8.44891 9.67098 8.63968 9.53033 8.78033C9.38968 8.92098 9.19891 9 9 9H7.5C7.30109 9 7.11032 8.92098 6.96967 8.78033C6.82902 8.63968 6.75 8.44891 6.75 8.25ZM11.25 8.25C11.25 8.05109 11.329 7.86032 11.4697 7.71967C11.6103 7.57902 11.8011 7.5 12 7.5H13.5C13.6989 7.5 13.8897 7.57902 14.0303 7.71967C14.171 7.86032 14.25 8.05109 14.25 8.25C14.25 8.44891 14.171 8.63968 14.0303 8.78033C13.8897 8.92098 13.6989 9 13.5 9H12C11.8011 9 11.6103 8.92098 11.4697 8.78033C11.329 8.63968 11.25 8.44891 11.25 8.25ZM6.75 12C6.75 11.8011 6.82902 11.6103 6.96967 11.4697C7.11032 11.329 7.30109 11.25 7.5 11.25H9C9.19891 11.25 9.38968 11.329 9.53033 11.4697C9.67098 11.6103 9.75 11.8011 9.75 12C9.75 12.1989 9.67098 12.3897 9.53033 12.5303C9.38968 12.671 9.19891 12.75 9 12.75H7.5C7.30109 12.75 7.11032 12.671 6.96967 12.5303C6.82902 12.3897 6.75 12.1989 6.75 12ZM11.25 12C11.25 11.8011 11.329 11.6103 11.4697 11.4697C11.6103 11.329 11.8011 11.25 12 11.25H13.5C13.6989 11.25 13.8897 11.329 14.0303 11.4697C14.171 11.6103 14.25 11.8011 14.25 12C14.25 12.1989 14.171 12.3897 14.0303 12.5303C13.8897 12.671 13.6989 12.75 13.5 12.75H12C11.8011 12.75 11.6103 12.671 11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12Z"
                                                fill="black" />
                                        </svg></span>
                                    <select class="form-select" aria-label="Username" aria-describedby="basic-addon1">
                                        <option selected disabled>Select Department</option>
                                        <option value="john">john</option>
                                        <option value="jane">jane</option>
                                        <option value="admin">admin</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="form-field">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg width="20" style="opacity: 60%;" height="19" viewBox="0 0 20 19"
                                            fill="none" >
                                            <path
                                                d="M18 4.5H14.25C14.25 3.30653 13.7759 2.16193 12.932 1.31802C12.0881 0.474106 10.9435 0 9.75 0C8.55653 0 7.41193 0.474106 6.56802 1.31802C5.72411 2.16193 5.25 3.30653 5.25 4.5H1.5C1.10218 4.5 0.720644 4.65804 0.43934 4.93934C0.158035 5.22064 0 5.60218 0 6V17.25C0 17.6478 0.158035 18.0294 0.43934 18.3107C0.720644 18.592 1.10218 18.75 1.5 18.75H18C18.3978 18.75 18.7794 18.592 19.0607 18.3107C19.342 18.0294 19.5 17.6478 19.5 17.25V6C19.5 5.60218 19.342 5.22064 19.0607 4.93934C18.7794 4.65804 18.3978 4.5 18 4.5ZM9.75 1.5C10.5456 1.5 11.3087 1.81607 11.8713 2.37868C12.4339 2.94129 12.75 3.70435 12.75 4.5H6.75C6.75 3.70435 7.06607 2.94129 7.62868 2.37868C8.19129 1.81607 8.95435 1.5 9.75 1.5ZM18 17.25H1.5V6H18V17.25Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <select class="form-select" aria-label="departments" aria-describedby="basic-addon1">
                                        <option selected disabled>Select Category</option>
                                        <option value="john">IT</option>
                                        <option value="jane">HR</option>
                                        <option value="admin">Testing</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Subject -->
                            <div class="form-field">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg width="20" style="opacity: 60%;" height="20" viewBox="0 0 20 20"
                                            fill="none" >
                                            <path
                                                d="M9.75 0C7.82164 0 5.93657 0.571828 4.33319 1.64317C2.72982 2.71451 1.48013 4.23726 0.742179 6.01884C0.00422452 7.80042 -0.188858 9.76082 0.187348 11.6521C0.563554 13.5434 1.49215 15.2807 2.85571 16.6443C4.21928 18.0079 5.95656 18.9365 7.84787 19.3127C9.73919 19.6889 11.6996 19.4958 13.4812 18.7578C15.2627 18.0199 16.7855 16.7702 17.8568 15.1668C18.9282 13.5634 19.5 11.6784 19.5 9.75C19.4973 7.16498 18.4692 4.68661 16.6413 2.85872C14.8134 1.03084 12.335 0.00272983 9.75 0ZM16.4878 4.99406L10.5 8.45062V1.53469C11.6889 1.64369 12.8401 2.00945 13.8739 2.60672C14.9077 3.20398 15.7995 4.01851 16.4878 4.99406ZM9 1.53469V9.31594L2.26032 13.2066C1.70704 12.0074 1.44975 10.6928 1.51008 9.37353C1.57041 8.05422 1.94659 6.76868 2.60701 5.62497C3.26742 4.48126 4.19276 3.51281 5.30523 2.80104C6.41771 2.08927 7.68481 1.65498 9 1.53469ZM9.75 18C8.42924 17.9995 7.12792 17.6818 5.95543 17.0738C4.78295 16.4658 3.77358 15.5851 3.01219 14.5059L17.2397 6.2925C17.8199 7.54946 18.0744 8.9322 17.9799 10.3134C17.8854 11.6945 17.4448 13.0297 16.6987 14.1959C15.9527 15.3621 14.9252 16.3217 13.7108 16.9865C12.4965 17.6513 11.1344 17.9999 9.75 18Z"
                                                fill="black" />
                                        </svg>
                                    </span> <input type="text" class="form-control" placeholder="Enter Subject"
                                        aria-label="subject" aria-describedby="basic-addon1">
                                </div>
                            </div>

                            <!-- Ticket Raiser -->
                            <div class="form-field">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg width="20" style="opacity: 60%;" height="19" viewBox="0 0 20 19"
                                            fill="none" >
                                            <path
                                                d="M19.4082 17.6276C17.9803 15.1591 15.78 13.3891 13.2122 12.5501C14.4824 11.7939 15.4692 10.6417 16.0212 9.27048C16.5731 7.89922 16.6597 6.38468 16.2676 4.95945C15.8755 3.53422 15.0264 2.27711 13.8506 1.38117C12.6749 0.485228 11.2376 0 9.75941 0C8.28122 0 6.84391 0.485228 5.66818 1.38117C4.49246 2.27711 3.64334 3.53422 3.25123 4.95945C2.85911 6.38468 2.94569 7.89922 3.49765 9.27048C4.04961 10.6417 5.03644 11.7939 6.3066 12.5501C3.73878 13.3882 1.53847 15.1582 0.110659 17.6276C0.0582987 17.7129 0.0235684 17.8079 0.00851736 17.9069C-0.00653367 18.006 -0.00160057 18.107 0.0230256 18.2041C0.0476518 18.3011 0.0914723 18.3923 0.151901 18.4722C0.212331 18.552 0.288144 18.619 0.37487 18.6691C0.461595 18.7192 0.557476 18.7514 0.656854 18.7638C0.756232 18.7763 0.857095 18.7687 0.953492 18.7415C1.04989 18.7143 1.13987 18.6681 1.21812 18.6056C1.29637 18.5431 1.3613 18.4656 1.4091 18.3776C3.17535 15.3251 6.29722 13.5026 9.75941 13.5026C13.2216 13.5026 16.3435 15.3251 18.1097 18.3776C18.1575 18.4656 18.2225 18.5431 18.3007 18.6056C18.379 18.6681 18.4689 18.7143 18.5653 18.7415C18.6617 18.7687 18.7626 18.7763 18.862 18.7638C18.9613 18.7514 19.0572 18.7192 19.1439 18.6691C19.2307 18.619 19.3065 18.552 19.3669 18.4722C19.4273 18.3923 19.4712 18.3011 19.4958 18.2041C19.5204 18.107 19.5254 18.006 19.5103 17.9069C19.4952 17.8079 19.4605 17.7129 19.4082 17.6276ZM4.50941 6.75255C4.50941 5.7142 4.81732 4.69917 5.39419 3.83581C5.97107 2.97245 6.79101 2.29954 7.75032 1.90218C8.70963 1.50482 9.76523 1.40086 10.7836 1.60343C11.802 1.806 12.7375 2.30601 13.4717 3.04024C14.2059 3.77447 14.706 4.70993 14.9085 5.72833C15.1111 6.74673 15.0071 7.80233 14.6098 8.76164C14.2124 9.72095 13.5395 10.5409 12.6762 11.1178C11.8128 11.6946 10.7978 12.0026 9.75941 12.0026C8.36748 12.0011 7.03299 11.4475 6.04874 10.4632C5.0645 9.47897 4.5109 8.14448 4.50941 6.75255Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <select class="form-select" aria-label="Username" aria-describedby="basic-addon1">
                                        <option selected disabled>Select Ticket Raiser</option>
                                        <option value="john">john</option>
                                        <option value="jane">jane</option>
                                        <option value="admin">admin</option>
                                    </select>
                                </div>

                            </div>

                        </div>
                    </section>

                    <!-- modals -->
                    <div class="doc-section">
                        <h1>Modal</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about modal's by exploring our design theme:
                            <a target="_blank"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-modals.html"
                                class="blue-link">
                                Explore more here
                            </a>
                        </div>
                        <div class="d-md-flex button-group">
                            <div>
                                <button class="btn me-1 mb-1 bg-warning-subtle text-warning px-4 fs-4 "
                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg">
                                    Extra Large Modal
                                </button>
                                <!-- sample modal content -->
                                <div class="modal fade" id="bs-example-modal-xlg" tabindex="-1"
                                    aria-labelledby="bs-example-modal-lg" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title" id="myLargeModalLabel">
                                                    Extra Large modal
                                                </h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4>
                                                    Overflowing text to show scroll behavior
                                                </h4>
                                                <p>
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Vivamus sagittis lacus
                                                    vel augue laoreet rutrum faucibus dolor
                                                    auctor.
                                                </p>
                                                <p>
                                                    Aenean lacinia bibendum nulla sed consectetur.
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Donec sed odio dui. Donec
                                                    ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn bg-danger-subtle text-danger  waves-effect text-start"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                            </div>
                            <div>
                                <!-- ------------------------------------------ -->
                                <!-- Large -->
                                <!-- ------------------------------------------ -->
                                <button class="btn me-1 mb-1 bg-success-subtle text-success px-4 fs-4 "
                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">
                                    Large Modal
                                </button>
                                <div class="modal fade" id="bs-example-modal-lg" tabindex="-1"
                                    aria-labelledby="bs-example-modal-lg" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title" id="myLargeModalLabel">
                                                    Large modal
                                                </h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4>
                                                    Overflowing text to show scroll behavior
                                                </h4>
                                                <p>
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Vivamus sagittis lacus
                                                    vel augue laoreet rutrum faucibus dolor
                                                    auctor.
                                                </p>
                                                <p>
                                                    Aenean lacinia bibendum nulla sed consectetur.
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Donec sed odio dui. Donec
                                                    ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn bg-danger-subtle text-danger  waves-effect text-start"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                            </div>
                            <div>
                                <!-- ------------------------------------------ -->
                                <!-- Medium -->
                                <!-- ------------------------------------------ -->
                                <button class="btn me-1 mb-1 bg-primary-subtle text-primary px-4 fs-4 "
                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-md">
                                    Medium Modal
                                </button>
                                <!-- sample modal content -->
                                <div id="bs-example-modal-md" class="modal fade" tabindex="-1"
                                    aria-labelledby="bs-example-modal-md" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title" id="myModalLabel">
                                                    Medium Modal
                                                </h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4>
                                                    Overflowing text to show scroll behavior
                                                </h4>
                                                <p>
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Vivamus sagittis lacus
                                                    vel augue laoreet rutrum faucibus dolor
                                                    auctor.
                                                </p>
                                                <p>
                                                    Aenean lacinia bibendum nulla sed consectetur.
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Donec sed odio dui. Donec
                                                    ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn bg-danger-subtle text-danger  waves-effect"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                            </div>
                            <div>
                                <!-- ------------------------------------------ -->
                                <!-- Small -->
                                <!-- ------------------------------------------ -->
                                <button class="btn me-1 mb-1 bg-danger-subtle text-danger px-4 fs-4 "
                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm">
                                    Small Modal
                                </button>
                                <!-- sample modal content -->
                                <div class="modal fade" id="bs-example-modal-sm" tabindex="-1"
                                    aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
                                                <h4 class="modal-title" id="myModalLabel">
                                                    Small Modal
                                                </h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4>
                                                    Overflowing text to show scroll behavior
                                                </h4>
                                                <p>
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Vivamus sagittis lacus
                                                    vel augue laoreet rutrum faucibus dolor
                                                    auctor.
                                                </p>
                                                <p>
                                                    Aenean lacinia bibendum nulla sed consectetur.
                                                    Praesent commodo cursus magna, vel scelerisque
                                                    nisl consectetur et. Donec sed odio dui. Donec
                                                    ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                    class="btn bg-danger-subtle text-danger  waves-effect"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                            </div>
                        </div>
                    </div>

                    <!-- accrodian -->
                    <div class="doc-section">
                        <h1>Accordian</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about accordion's by exploring our design theme.
                            <a target="_blank"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-accordian.html"
                                class="blue-link">
                                Explore more here
                            </a>
                        </div>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Accordion Item #1
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the first item's accordion body.</strong>
                                        It is hidden by default, until the collapse plugin adds the appropriate
                                        classes that we use to style each element. These classes control the
                                        overall appearance, as well as the showing and hiding via CSS
                                        transitions. You can modify any of this with custom CSS or overriding
                                        our default variables. It's also worth noting that just about any HTML
                                        can go within the
                                        <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Accordion Item #2
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the second item's accordion body.</strong>
                                        It is hidden by default, until the collapse plugin adds the appropriate
                                        classes that we use to style each element. These classes control the
                                        overall appearance, as well as the showing and hiding via CSS
                                        transitions. You can modify any of this with custom CSS or overriding
                                        our default variables. It's also worth noting that just about any HTML
                                        can go within the
                                        <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Accordion Item #3
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the third item's accordion body.</strong>
                                        It is hidden by default, until the collapse plugin adds the appropriate
                                        classes that we use to style each element. These classes control the
                                        overall appearance, as well as the showing and hiding via CSS
                                        transitions. You can modify any of this with custom CSS or overriding
                                        our default variables. It's also worth noting that just about any HTML
                                        can go within the
                                        <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="doc-section">
                        <h1>Datatable</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Datatable's by exploring our design theme.
                            <a target="_blank"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-accordian.html"
                                class="blue-link">
                                Explore more here
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-ini"
                                class="table w-100 table-striped table-bordered display text-nowrap">
                                <thead>
                                    <!-- start row -->
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start date</th>
                                        <th>Salary</th>
                                    </tr>
                                    <!-- end row -->
                                </thead>
                                <tbody>
                                    <!-- start row -->
                                    <tr>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>Edinburgh</td>
                                        <td>61</td>
                                        <td>2011/04/25</td>
                                        <td>$320,800</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Garrett Winters</td>
                                        <td>Accountant</td>
                                        <td>Tokyo</td>
                                        <td>63</td>
                                        <td>2011/07/25</td>
                                        <td>$170,750</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Ashton Cox</td>
                                        <td>Junior Technical Author</td>
                                        <td>San Francisco</td>
                                        <td>66</td>
                                        <td>2009/01/12</td>
                                        <td>$86,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Cedric Kelly</td>
                                        <td>Senior Javascript Developer</td>
                                        <td>Edinburgh</td>
                                        <td>22</td>
                                        <td>2012/03/29</td>
                                        <td>$433,060</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Airi Satou</td>
                                        <td>Accountant</td>
                                        <td>Tokyo</td>
                                        <td>33</td>
                                        <td>2008/11/28</td>
                                        <td>$162,700</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Brielle Williamson</td>
                                        <td>Integration Specialist</td>
                                        <td>New York</td>
                                        <td>61</td>
                                        <td>2012/12/02</td>
                                        <td>$372,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Herrod Chandler</td>
                                        <td>Sales Assistant</td>
                                        <td>San Francisco</td>
                                        <td>59</td>
                                        <td>2012/08/06</td>
                                        <td>$137,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Rhona Davidson</td>
                                        <td>Integration Specialist</td>
                                        <td>Tokyo</td>
                                        <td>55</td>
                                        <td>2010/10/14</td>
                                        <td>$327,900</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Colleen Hurst</td>
                                        <td>Javascript Developer</td>
                                        <td>San Francisco</td>
                                        <td>39</td>
                                        <td>2009/09/15</td>
                                        <td>$205,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Sonya Frost</td>
                                        <td>Software Engineer</td>
                                        <td>Edinburgh</td>
                                        <td>23</td>
                                        <td>2008/12/13</td>
                                        <td>$103,600</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jena Gaines</td>
                                        <td>Office Manager</td>
                                        <td>London</td>
                                        <td>30</td>
                                        <td>2008/12/19</td>
                                        <td>$90,560</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Quinn Flynn</td>
                                        <td>Support Lead</td>
                                        <td>Edinburgh</td>
                                        <td>22</td>
                                        <td>2013/03/03</td>
                                        <td>$342,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Charde Marshall</td>
                                        <td>Regional Director</td>
                                        <td>San Francisco</td>
                                        <td>36</td>
                                        <td>2008/10/16</td>
                                        <td>$470,600</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Haley Kennedy</td>
                                        <td>Senior Marketing Designer</td>
                                        <td>London</td>
                                        <td>43</td>
                                        <td>2012/12/18</td>
                                        <td>$313,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Tatyana Fitzpatrick</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>19</td>
                                        <td>2010/03/17</td>
                                        <td>$385,750</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Michael Silva</td>
                                        <td>Marketing Designer</td>
                                        <td>London</td>
                                        <td>66</td>
                                        <td>2012/11/27</td>
                                        <td>$198,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Paul Byrd</td>
                                        <td>Chief Financial Officer (CFO)</td>
                                        <td>New York</td>
                                        <td>64</td>
                                        <td>2010/06/09</td>
                                        <td>$725,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Gloria Little</td>
                                        <td>Systems Administrator</td>
                                        <td>New York</td>
                                        <td>59</td>
                                        <td>2009/04/10</td>
                                        <td>$237,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Bradley Greer</td>
                                        <td>Software Engineer</td>
                                        <td>London</td>
                                        <td>41</td>
                                        <td>2012/10/13</td>
                                        <td>$132,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Dai Rios</td>
                                        <td>Personnel Lead</td>
                                        <td>Edinburgh</td>
                                        <td>35</td>
                                        <td>2012/09/26</td>
                                        <td>$217,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jenette Caldwell</td>
                                        <td>Development Lead</td>
                                        <td>New York</td>
                                        <td>30</td>
                                        <td>2011/09/03</td>
                                        <td>$345,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Yuri Berry</td>
                                        <td>Chief Marketing Officer (CMO)</td>
                                        <td>New York</td>
                                        <td>40</td>
                                        <td>2009/06/25</td>
                                        <td>$675,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Caesar Vance</td>
                                        <td>Pre-Sales Support</td>
                                        <td>New York</td>
                                        <td>21</td>
                                        <td>2011/12/12</td>
                                        <td>$106,450</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Doris Wilder</td>
                                        <td>Sales Assistant</td>
                                        <td>Sidney</td>
                                        <td>23</td>
                                        <td>2010/09/20</td>
                                        <td>$85,600</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Angelica Ramos</td>
                                        <td>Chief Executive Officer (CEO)</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2009/10/09</td>
                                        <td>$1,200,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Gavin Joyce</td>
                                        <td>Developer</td>
                                        <td>Edinburgh</td>
                                        <td>42</td>
                                        <td>2010/12/22</td>
                                        <td>$92,575</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jennifer Chang</td>
                                        <td>Regional Director</td>
                                        <td>Singapore</td>
                                        <td>28</td>
                                        <td>2010/11/14</td>
                                        <td>$357,650</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Brenden Wagner</td>
                                        <td>Software Engineer</td>
                                        <td>San Francisco</td>
                                        <td>28</td>
                                        <td>2011/06/07</td>
                                        <td>$206,850</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Fiona Green</td>
                                        <td>Chief Operating Officer (COO)</td>
                                        <td>San Francisco</td>
                                        <td>48</td>
                                        <td>2010/03/11</td>
                                        <td>$850,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Shou Itou</td>
                                        <td>Regional Marketing</td>
                                        <td>Tokyo</td>
                                        <td>20</td>
                                        <td>2011/08/14</td>
                                        <td>$163,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Michelle House</td>
                                        <td>Integration Specialist</td>
                                        <td>Sidney</td>
                                        <td>37</td>
                                        <td>2011/06/02</td>
                                        <td>$95,400</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Suki Burks</td>
                                        <td>Developer</td>
                                        <td>London</td>
                                        <td>53</td>
                                        <td>2009/10/22</td>
                                        <td>$114,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Prescott Bartlett</td>
                                        <td>Technical Author</td>
                                        <td>London</td>
                                        <td>27</td>
                                        <td>2011/05/07</td>
                                        <td>$145,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Gavin Cortez</td>
                                        <td>Team Leader</td>
                                        <td>San Francisco</td>
                                        <td>22</td>
                                        <td>2008/10/26</td>
                                        <td>$235,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Martena Mccray</td>
                                        <td>Post-Sales support</td>
                                        <td>Edinburgh</td>
                                        <td>46</td>
                                        <td>2011/03/09</td>
                                        <td>$324,050</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Unity Butler</td>
                                        <td>Marketing Designer</td>
                                        <td>San Francisco</td>
                                        <td>47</td>
                                        <td>2009/12/09</td>
                                        <td>$85,675</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Howard Hatfield</td>
                                        <td>Office Manager</td>
                                        <td>San Francisco</td>
                                        <td>51</td>
                                        <td>2008/12/16</td>
                                        <td>$164,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Hope Fuentes</td>
                                        <td>Secretary</td>
                                        <td>San Francisco</td>
                                        <td>41</td>
                                        <td>2010/02/12</td>
                                        <td>$109,850</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Vivian Harrell</td>
                                        <td>Financial Controller</td>
                                        <td>San Francisco</td>
                                        <td>62</td>
                                        <td>2009/02/14</td>
                                        <td>$452,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Timothy Mooney</td>
                                        <td>Office Manager</td>
                                        <td>London</td>
                                        <td>37</td>
                                        <td>2008/12/11</td>
                                        <td>$136,200</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jackson Bradshaw</td>
                                        <td>Director</td>
                                        <td>New York</td>
                                        <td>65</td>
                                        <td>2008/09/26</td>
                                        <td>$645,750</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Olivia Liang</td>
                                        <td>Support Engineer</td>
                                        <td>Singapore</td>
                                        <td>64</td>
                                        <td>2011/02/03</td>
                                        <td>$234,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Bruno Nash</td>
                                        <td>Software Engineer</td>
                                        <td>London</td>
                                        <td>38</td>
                                        <td>2011/05/03</td>
                                        <td>$163,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Sakura Yamamoto</td>
                                        <td>Support Engineer</td>
                                        <td>Tokyo</td>
                                        <td>37</td>
                                        <td>2009/08/19</td>
                                        <td>$139,575</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Thor Walton</td>
                                        <td>Developer</td>
                                        <td>New York</td>
                                        <td>61</td>
                                        <td>2013/08/11</td>
                                        <td>$98,540</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Finn Camacho</td>
                                        <td>Support Engineer</td>
                                        <td>San Francisco</td>
                                        <td>47</td>
                                        <td>2009/07/07</td>
                                        <td>$87,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Serge Baldwin</td>
                                        <td>Data Coordinator</td>
                                        <td>Singapore</td>
                                        <td>64</td>
                                        <td>2012/04/09</td>
                                        <td>$138,575</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Zenaida Frank</td>
                                        <td>Software Engineer</td>
                                        <td>New York</td>
                                        <td>63</td>
                                        <td>2010/01/04</td>
                                        <td>$125,250</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Zorita Serrano</td>
                                        <td>Software Engineer</td>
                                        <td>San Francisco</td>
                                        <td>56</td>
                                        <td>2012/06/01</td>
                                        <td>$115,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jennifer Acosta</td>
                                        <td>Junior Javascript Developer</td>
                                        <td>Edinburgh</td>
                                        <td>43</td>
                                        <td>2013/02/01</td>
                                        <td>$75,650</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Cara Stevens</td>
                                        <td>Sales Assistant</td>
                                        <td>New York</td>
                                        <td>46</td>
                                        <td>2011/12/06</td>
                                        <td>$145,600</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Hermione Butler</td>
                                        <td>Regional Director</td>
                                        <td>London</td>
                                        <td>47</td>
                                        <td>2011/03/21</td>
                                        <td>$356,250</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Lael Greer</td>
                                        <td>Systems Administrator</td>
                                        <td>London</td>
                                        <td>21</td>
                                        <td>2009/02/27</td>
                                        <td>$103,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Jonas Alexander</td>
                                        <td>Developer</td>
                                        <td>San Francisco</td>
                                        <td>30</td>
                                        <td>2010/07/14</td>
                                        <td>$86,500</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Shad Decker</td>
                                        <td>Regional Director</td>
                                        <td>Edinburgh</td>
                                        <td>51</td>
                                        <td>2008/11/13</td>
                                        <td>$183,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Michael Bruce</td>
                                        <td>Javascript Developer</td>
                                        <td>Singapore</td>
                                        <td>29</td>
                                        <td>2011/06/27</td>
                                        <td>$183,000</td>
                                    </tr>
                                    <!-- end row -->
                                    <!-- start row -->
                                    <tr>
                                        <td>Donna Snider</td>
                                        <td>Customer Support</td>
                                        <td>New York</td>
                                        <td>27</td>
                                        <td>2011/01/25</td>
                                        <td>$112,000</td>
                                    </tr>
                                    <!-- end row -->
                                </tbody>
                                <tfoot>
                                    <!-- start row -->
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start date</th>
                                        <th>Salary</th>
                                    </tr>
                                    <!-- end row -->
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="doc-section">
                        <h1>Datepicker</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Datepicker's by exploring our design theme.
                            <a class="blue-link"
                                href="https://wrappixel.github.io/premium-documentation-wp/bootstrap/materialM/docs-datepicker.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" class="form-control" name="start" />
                            <span class="input-group-text bg-primary b-0 text-white">TO</span>
                            <input type="text" class="form-control" name="end" />
                        </div>
                    </div>

                    <div class="doc-section">
                        <h1>Charts</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Chart's by exploring our design theme.
                            <a class="blue-link"
                                href="https://wrappixel.github.io/premium-documentation-wp/bootstrap/materialM/docs-charts-apex.html">
                                Explore more here
                            </a>
                        </div>
                        <div id="chart-pie-simple"></div>
                    </div>

                    <div class="doc-section">
                        <h1>Select2</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Select2 by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/form-select2.html">
                                Explore more here
                            </a>
                        </div>

                        <h4 class="card-title">Support Tag</h4>
                        <p class="card-subtitle mb-3">
                            Tagging can also be used in multi-value select boxes. In
                            the example below, we set the multiple="multiple"
                            attribute on a Select2 control that also has
                            <mark>
                                <code> tags: true</code>
                            </mark> enabled.
                        </p>
                        <select class="form-control" multiple="" id="select2-with-tags">
                            <option>orange</option>
                            <option>white</option>
                            <option>purple</option>
                            <option value="red">red</option>
                            <option value="blue" selected>blue</option>
                            <option value="green" selected>green</option>
                        </select>
                    </div>

                    <!-- Tabs -->
                    <div class="doc-section">
                        <h1>Tabs</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Tab's by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-tab.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-2">
                                <!-- Nav tabs -->
                                <div class="nav flex-column nav-pills mb-4 mb-md-0" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill"
                                        href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                                        aria-selected="true">
                                        Home
                                    </a>
                                    <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                        href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                                        aria-selected="false">
                                        Profile
                                    </a>
                                    <a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                        href="#v-pills-messages" role="tab" aria-controls="v-pills-messages"
                                        aria-selected="false">
                                        Messages
                                    </a>
                                    <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                        aria-selected="false">
                                        Settings
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                        aria-labelledby="v-pills-home-tab">
                                        <p>
                                            Raw denim you probably haven't heard of them jean
                                            shorts Austin. Nesciunt tofu stumptown aliqua,
                                            retro synth master cleanse. Mustache cliche
                                            tempor, williamsburg carles vegan helvetica.
                                        </p>
                                        Raw denim you probably haven't heard of them jean
                                        shorts Austin. Nesciunt tofu stumptown aliqua, retro
                                        synth master cleanse. Mustache cliche tempor,
                                        williamsburg carles vegan helvetica.
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab">
                                        <p>
                                            Probably haven't heard of them jean shorts Austin.
                                            Nesciunt tofu stumptown aliqua, retro synth master
                                            cleanse. Mustache cliche tempor, williamsburg
                                            carles vegan helvetica.
                                        </p>
                                        <p>
                                            Probably haven't heard of them jean shorts Austin.
                                            Nesciunt tofu stumptown aliqua, retro synth master
                                            cleanse. Mustache cliche tempor, williamsburg
                                            carles vegan helvetica.
                                        </p>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                        aria-labelledby="v-pills-messages-tab">
                                        <p>
                                            Raw denim you probably haven't heard of them jean
                                            shorts Austin. Nesciunt tofu stumptown aliqua,
                                            retro synth master cleanse. Mustache cliche
                                            tempor, williamsburg carles vegan helvetica.
                                        </p>
                                        Raw denim you probably haven't heard of them jean
                                        shorts Austin. Nesciunt tofu stumptown aliqua, retro
                                        synth master cleanse. Mustache cliche tempor,
                                        williamsburg carles vegan helvetica.
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab">
                                        <p>
                                            Probably haven't heard of them jean shorts Austin.
                                            Nesciunt tofu stumptown aliqua, retro synth master
                                            cleanse. Mustache cliche tempor, williamsburg
                                            carles vegan helvetica.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Tooltips  -->
                    <div class="doc-section">
                        <h1>Tooltips</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Tooltip's by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-tooltip-popover.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="d-md-flex align-items-center button-group mt-4">
                            <button type="button" class="me-2 btn bg-info-subtle text-info " data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Tooltip on top">
                                Tooltip on top
                            </button>
                            <button type="button" class="me-2 btn bg-primary-subtle text-primary "
                                data-bs-toggle="tooltip" data-bs-placement="right" title="Tooltip on right">
                                Tooltip on right
                            </button>
                            <button type="button" class="me-2 btn bg-success-subtle text-success "
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tooltip on bottom">
                                Tooltip on bottom
                            </button>
                            <button type="button" class="me-2 btn bg-danger-subtle text-danger "
                                data-bs-toggle="tooltip" data-bs-placement="left" title="Tooltip on left">
                                Tooltip on left
                            </button>
                        </div>
                    </div>




                    <!-- Popover -->
                    <div class="doc-section">
                        <h1>Popover</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Popover's by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-tooltip-popover.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="d-md-flex align-items-center button-group mt-4">
                            <button type="button" class="me-2 btn bg-info-subtle text-info d-flex align-items-center "
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top"
                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                <i class="ti ti-chevron-up fs-4"></i>
                                Popover on top
                            </button>

                            <button type="button"
                                class="me-2 btn bg-primary-subtle text-primary d-flex align-items-center "
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="right"
                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                <i class="ti ti-chevron-right fs-4"></i>
                                Popover on right
                            </button>

                            <button type="button"
                                class="me-2 btn bg-success-subtle text-success d-flex align-items-center "
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="bottom"
                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                <i class="ti ti-chevron-down fs-4"></i>
                                Popover on bottom
                            </button>

                            <button type="button"
                                class="me-2 btn bg-danger-subtle text-danger d-flex align-items-center "
                                data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left"
                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                <i class="ti ti-chevron-left fs-4"></i>
                                Popover on left
                            </button>
                        </div>
                    </div>

                    <!-- Sweetalert  -->
                    <div class="doc-section">
                        <h1>Sweetalert</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Sweetalert's by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/component-sweetalert.html">
                                Explore more here
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="border-bottom title-part-padding">
                                    <h4 class="card-title">Success Message</h4>
                                    <h6 class="card-subtitle">(Click on image)</h6>
                                </div>
                                <div class="card-body p-3">
                                    <img src="../../assets/images/alert/alert3.png" alt="alert"
                                        class="img-fluid model_img" id="sa-success" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toaster  -->
                    <div class="doc-section">
                        <h1>Toaster</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Toaster's by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/component-toastr.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="card">
                            <div class="border-bottom title-part-padding">
                                <h4 class="card-title mb-0">Basic</h4>
                            </div>
                            <div class="card-body">
                                <div class="button-group">
                                    <button type="button"
                                        class="
                        btn
                        px-4
                        fs-4
                        bg-success-subtle
                        text-success
                        fw-medium
                      "
                                        id="ts-success">
                                        Success
                                    </button>
                                    <button type="button"
                                        class="
                        btn
                        px-4
                        fs-4
                        bg-info-subtle
                        text-info
                        fw-medium
                      "
                                        id="ts-info">
                                        Info
                                    </button>
                                    <button type="button"
                                        class="
                        btn
                        px-4
                        fs-4
                        bg-warning-subtle
                        text-warning
                        fw-medium
                      "
                                        id="ts-warning">
                                        Warning
                                    </button>
                                    <button type="button"
                                        class="
                        btn
                        px-4
                        fs-4
                        bg-danger-subtle
                        text-danger
                        fw-medium
                      "
                                        id="ts-error">
                                        Error
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progressbar  -->
                    <div class="doc-section">
                        <h1>Progressbar</h1>
                        <div class="col-md-8 type-meta mb-3">
                            Learn more about Progressbar by exploring our design theme.
                            <a class="blue-link"
                                href="https://bootstrapdemos.wrappixel.com/materialM/dist/main/ui-progressbar.html">
                                Explore more here
                            </a>
                        </div>
                        <div class="progress" style="height: 15px">
                            <div class="progress-bar text-bg-danger" style="width: 60%" role="progressbar"></div>
                        </div>
                        <div class="progress mt-4" style="height: 15px">
                            <div class="progress-bar text-bg-info" style="width: 40%" role="progressbar"></div>
                        </div>
                        <div class="progress mt-4" style="height: 15px">
                            <div class="progress-bar text-bg-success" style="width: 20%" role="progressbar"></div>
                        </div>
                        <div class="progress mt-4" style="height: 15px">
                            <div class="progress-bar text-bg-primary" style="width: 30%" role="progressbar"></div>
                        </div>
                        <div class="progress mt-4" style="height: 15px">
                            <div class="progress-bar text-bg-warning" style="width: 80%" role="progressbar"></div>
                        </div>
                        <div class="progress mt-4" style="height: 15px">
                            <div class="progress-bar text-bg-inverse" style="width: 40%" role="progressbar"></div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </main>
@endsection
