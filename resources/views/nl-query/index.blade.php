@extends('layouts.layout1')
@section('title', 'AI Query Assistant')
@section('content')

<style>
    /* DataTables override to match AMG excel style */
    #excel-dt thead tr th {
        background-color: #1e3a5f !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        white-space: nowrap;
        border: 1px solid #c5d0de !important;
        padding: 8px 12px !important;
    }
    #excel-dt tbody tr:nth-child(even) td { background-color: #EFF6FF !important; }
    #excel-dt tbody tr:nth-child(odd) td  { background-color: #ffffff !important; }
    #excel-dt tbody tr:hover td           { background-color: #dbeafe !important; }
    #excel-dt tbody td {
        font-size: 12px !important;
        font-family: 'Calibri', 'Inter', sans-serif !important;
        border: 1px solid #d0d7de !important;
        padding: 5px 12px !important;
        white-space: nowrap;
    }
    /* Row number column */
    #excel-dt tbody td:first-child,
    #excel-dt thead th:first-child {
        background-color: #f1f5f9 !important;
        color: #6c757d !important;
        text-align: center !important;
        min-width: 40px;
        border-right: 2px solid #c5d0de !important;
        font-weight: 500 !important;
    }
    /* DataTables sorting icons color fix */
    #excel-dt thead .sorting:after,
    #excel-dt thead .sorting_asc:after,
    #excel-dt thead .sorting_desc:after { color: #ffffff !important; opacity: 0.8; }
</style>

<section class="content">
    <div id="main-user-list-wrapper">

        {{-- Header --}}
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path d="M5.84365 2.87333C6.34198 1.415 8.35698 1.37083 8.94782 2.74083L8.99782 2.87417L9.67032 4.84083C9.82444 5.29186 10.0735 5.70459 10.4007 6.05119C10.7279 6.39778 11.1256 6.67018 11.567 6.85L11.7478 6.9175L13.7145 7.58917C15.1728 8.0875 15.217 10.1025 13.8478 10.6933L13.7145 10.7433L11.7478 11.4158C11.2966 11.5699 10.8837 11.8189 10.537 12.146C10.1903 12.4732 9.91773 12.871 9.73782 13.3125L9.67032 13.4925L8.99865 15.46C8.50032 16.9183 6.48531 16.9625 5.89531 15.5933L5.84365 15.46L5.17198 13.4933C5.01796 13.0422 4.76896 12.6293 4.44177 12.2825C4.11457 11.9358 3.71681 11.6632 3.27532 11.4833L3.09531 11.4158L1.12865 10.7442C-0.330519 10.2458 -0.374685 8.23083 0.995315 7.64083L1.12865 7.58917L3.09531 6.9175C3.54634 6.76338 3.95907 6.51433 4.30567 6.18714C4.65226 5.85995 4.92466 5.46224 5.10448 5.02083L5.17198 4.84083L5.84365 2.87333ZM14.0878 1.50573e-07C14.2437 -1.96643e-07 14.3965 0.0437319 14.5288 0.126226C14.6611 0.208721 14.7676 0.326669 14.8362 0.466667L14.8761 0.564167L15.1678 1.41917L16.0236 1.71083C16.1799 1.76391 16.3168 1.86218 16.4172 1.99318C16.5175 2.12418 16.5767 2.28202 16.5872 2.44668C16.5977 2.61135 16.5592 2.77544 16.4763 2.91816C16.3935 3.06087 16.2702 3.17578 16.122 3.24833L16.0236 3.28833L15.1686 3.58L14.877 4.43583C14.8238 4.59202 14.7255 4.72892 14.5944 4.82916C14.4634 4.92941 14.3055 4.98849 14.1409 4.99894C13.9762 5.00938 13.8121 4.9707 13.6695 4.88782C13.5268 4.80493 13.412 4.68156 13.3395 4.53333L13.2995 4.43583L13.0078 3.58083L12.152 3.28917C11.9957 3.23609 11.8588 3.13782 11.7585 3.00682C11.6581 2.87582 11.599 2.71799 11.5884 2.55332C11.5779 2.38865 11.6165 2.22456 11.6993 2.08184C11.7821 1.93913 11.9054 1.82422 12.0536 1.75167L12.152 1.71167L13.007 1.42L13.2986 0.564167C13.3548 0.399521 13.4612 0.256591 13.6027 0.155416C13.7442 0.0542408 13.9138 -0.000104401 14.0878 1.50573e-07Z" fill="url(#paint0_linear_812_204)" />
                    <defs>
                        <linearGradient id="paint0_linear_812_204" x1="1.26315" y1="-2.54709e-07" x2="13.7556" y2="17.6965" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FA080C" />
                            <stop offset="1" stop-color="#1083FF" />
                        </linearGradient>
                    </defs>
                </svg>
                Ask {{ __('header.header.mati_ai') }}
            </h3>
        </div>

        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body pt-3">

                        {{-- Search Box --}}
                        <div class="mb-3">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input
                                    type="text"
                                    id="nl-input"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="e.g. Show all open tickets assigned to John..."
                                    autocomplete="off"
                                />
                                <button class="amg-btn amg-btn-primary" id="nl-submit">
                                    <span id="btn-text">
                                        <i class="fas fa-paper-plane me-1"></i> Ask
                                    </span>
                                    <span id="btn-loader" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Thinking...
                                    </span>
                                </button>
                            </div>

                            {{-- Suggestion chips --}}
                            <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                                <small class="text-muted">Try:</small>
                                <span class="badge rounded-pill bg-light text-dark border suggestion-chip px-3 py-2" role="button" style="cursor:pointer">Show all open tickets</span>
                                <span class="badge rounded-pill bg-light text-dark border suggestion-chip px-3 py-2" role="button" style="cursor:pointer">List all assets assigned to users</span>
                                <span class="badge rounded-pill bg-light text-dark border suggestion-chip px-3 py-2" role="button" style="cursor:pointer">Show pending procurement requests</span>
                                <span class="badge rounded-pill bg-light text-dark border suggestion-chip px-3 py-2" role="button" style="cursor:pointer">List all active projects</span>
                                <span class="badge rounded-pill bg-light text-dark border suggestion-chip px-3 py-2" role="button" style="cursor:pointer">Which user has the most tickets</span>
                            </div>
                        </div>

                        {{-- Error State --}}
                        <div id="error-area" class="d-none mb-3">
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                                <i class="fas fa-exclamation-circle fa-lg"></i>
                                <div>
                                    <strong>Couldn't process your query.</strong>
                                    <div id="error-message" class="small mt-1"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Initial placeholder --}}
                        <div id="initial-area" class="text-center py-5">
                            <i class="fas fa-robot fa-3x text-primary opacity-25 mb-3 d-block"></i>
                            <h6 class="text-muted fw-semibold">Your results will appear here</h6>
                            <p class="text-muted small mb-0">Type a question above or click a suggestion to get started.</p>
                        </div>

                        {{-- Empty State --}}
                        <div id="empty-area" class="d-none text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                            <h6 class="text-muted fw-semibold">No results found</h6>
                            <p class="text-muted small mb-0">Try rephrasing your query or use a suggestion above.</p>
                        </div>

                        {{-- Results --}}
                        <div id="results-area" class="d-none">

                            {{-- SQL + count bar --}}
                            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-3 p-2 bg-light rounded">
                                <div class="d-flex align-items-start gap-2 flex-grow-1 overflow-hidden">
                                    <small class="text-muted text-nowrap mt-1">Generated SQL:</small>
                                    <code id="generated-sql" class="text-dark small text-break"></code>
                                </div>
                                <span id="result-count" class="badge bg-primary text-nowrap align-self-center"></span>
                            </div>

                            {{-- Export Buttons --}}
                            <div class="d-flex gap-2 mb-3">
                                <button class="amg-btn amg-btn-primary amg-btn-sm" id="export-pdf">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1" style="vertical-align:middle">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Download PDF
                                </button>
                                <button class="amg-btn amg-btn-sm" id="export-excel" style="background:#1D6F42;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-size:13px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1" style="vertical-align:middle">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="12" y1="18" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <polyline points="9 15 12 18 15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Download Excel
                                </button>
                            </div>

                            {{-- Excel-style DataTable preview --}}
                            <div class="js-user-list-view-panel">
                                <div class="table-responsive">
                                    <table id="excel-dt" class="table display app-data-table" style="width:100%">
                                        <thead id="excel-dt-thead"></thead>
                                        <tbody id="excel-dt-tbody"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </main>

    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
$(document).ready(function () {

    let lastQueryData = null;
    let dataTable     = null;

    // Suggestion chips
    $('.suggestion-chip').on('click', function () {
        $('#nl-input').val($(this).text().trim());
        submitQuery();
    });

    $('#nl-submit').on('click', function () { submitQuery(); });
    $('#nl-input').on('keypress', function (e) { if (e.which === 13) submitQuery(); });

    function submitQuery() {
        const query = $('#nl-input').val().trim();
        if (!query) { $('#nl-input').focus(); return; }

        // Reset states
        $('#results-area, #empty-area, #error-area, #initial-area').addClass('d-none');
        $('#btn-text').addClass('d-none');
        $('#btn-loader').removeClass('d-none');
        $('#nl-submit').prop('disabled', true);

        // Destroy existing DataTable
        if (dataTable !== null) {
            dataTable.destroy();
            dataTable = null;
            $('#excel-dt-thead').empty();
            $('#excel-dt-tbody').empty();
        }

        $.ajax({
            url: '{{ route("nl-query.query") }}',
            method: 'POST',
            data: { query: query, _token: '{{ csrf_token() }}' },
            success: function (res) {
                resetButton();

                if (res.empty || res.count === 0) {
                    $('#empty-area').removeClass('d-none');
                    return;
                }

                // Store for export (uses original data for Paperdoc)
                lastQueryData = {
                    query:   query,
                    sql:     res.query,
                    columns: res.columns,
                    results: res.results
                };

                // SQL bar
                $('#generated-sql').text(res.query);
                $('#result-count').text(res.count + ' record' + (res.count !== 1 ? 's' : ''));

                // ── Build thead ──────────────────────────────────────────
                let thead = '<tr><th>#</th>';
                res.columns.forEach(col => {
                    const label = col.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    thead += `<th>${label}</th>`;
                });
                thead += '</tr>';
                $('#excel-dt-thead').html(thead);

                // ── Build tbody ──────────────────────────────────────────
                let tbody = '';
                res.results.forEach((row, i) => {
                    tbody += `<tr><td>${i + 1}</td>`;
                    res.columns.forEach(col => {
                        const val = row[col];
                        if (val === null || val === undefined || val === '') {
                            tbody += `<td><span class="text-muted">—</span></td>`;
                        } else if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2}/)) {
                            tbody += `<td>${val.substring(0, 16).replace('T', ' ')}</td>`;
                        } else {
                            tbody += `<td>${val}</td>`;
                        }
                    });
                    tbody += '</tr>';
                });
                $('#excel-dt-tbody').html(tbody);

                // ── Init DataTables ──────────────────────────────────────
                dataTable = $('#excel-dt').DataTable({
                    pageLength:  25,
                    lengthMenu:  [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    ordering:    true,
                    searching:   true,
                    responsive:  false,
                    scrollX:     true,
                    columnDefs:  [{ orderable: false, targets: 0 }], // disable sort on # column
                    language: {
                        search:            '',
                        searchPlaceholder: 'Search results...',
                        lengthMenu:        'Show _MENU_ entries',
                        info:              'Showing _START_ to _END_ of _TOTAL_ records',
                        paginate: {
                            previous: '<i class="fas fa-chevron-left"></i>',
                            next:     '<i class="fas fa-chevron-right"></i>'
                        }
                    },
                    dom: '<"d-flex align-items-center justify-content-between mb-2"lf>rtip',
                    initComplete: function () {
                        $('#excel-dt_filter input')
                            .addClass('amg-list-searchbar__input')
                            .css('margin-left', '8px');
                        $('#excel-dt_length select')
                            .addClass('amg-table-pagination-dropdown');
                    }
                });

                $('#results-area').removeClass('d-none');

                $('html, body').animate({
                    scrollTop: $('#results-area').offset().top - 80
                }, 400);
            },
            error: function (xhr) {
                resetButton();
                const msg = xhr.responseJSON?.error || 'Something went wrong. Please try again.';
                $('#error-message').text(msg);
                $('#error-area').removeClass('d-none');
            }
        });
    }

    function resetButton() {
        $('#btn-text').removeClass('d-none');
        $('#btn-loader').addClass('d-none');
        $('#nl-submit').prop('disabled', false);
    }

    // ── PDF Export ────────────────────────────────────────────────────────
    $('#export-pdf').on('click', function () {
        if (!lastQueryData) return;
        const $btn = $(this);
        $btn.prop('disabled', true).text('Generating...');

        $.ajax({
            url: '{{ route("nl-query.export-pdf") }}',
            method: 'POST',
            data: { ...lastQueryData, _token: '{{ csrf_token() }}' },
            xhrFields: { responseType: 'blob' },
            success: function (blob) {
                triggerDownload(blob, 'ai-query-report.pdf');
                $btn.prop('disabled', false).html(`
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1" style="vertical-align:middle">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg> Download PDF`);
            },
            error: function () {
                $btn.prop('disabled', false).text('Download PDF');
                alert('PDF generation failed. Please try again.');
            }
        });
    });

    // ── Excel Export ──────────────────────────────────────────────────────
    $('#export-excel').on('click', function () {
        if (!lastQueryData) return;
        const $btn = $(this);
        $btn.prop('disabled', true).text('Generating...');

        $.ajax({
            url: '{{ route("nl-query.export-excel") }}',
            method: 'POST',
            data: { ...lastQueryData, _token: '{{ csrf_token() }}' },
            xhrFields: { responseType: 'blob' },
            success: function (blob) {
                triggerDownload(blob, 'ai-query-report.xlsx');
                $btn.prop('disabled', false).html(`
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="me-1" style="vertical-align:middle">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="18" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <polyline points="9 15 12 18 15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg> Download Excel`);
            },
            error: function () {
                $btn.prop('disabled', false).text('Download Excel');
                alert('Excel generation failed. Please try again.');
            }
        });
    });

    function triggerDownload(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    }

});
</script>
@endpush