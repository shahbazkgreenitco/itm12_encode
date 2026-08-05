@extends('layouts.layout1')
@section('title', trans('content.download_format.purchase_import'))

@section('content')
<style>

    .user-list-action-btn svg {
  width: 16px;
  height: 16px;
}

.user-list-action-btn:hover svg {
  color: #ff4d4f; /* hover effect */
}
    .b1-text span:first-child {
        min-width: 150px;
        display: inline-block;
    }

    #attachment-dropper {
        display: none !important;
    }

    :root {
        --primary: #1a2e5a;
        --primary-hover: #243d7a;
        --border-dash: #b0bec5;
        --bg-upload: #f8fafc;
        --bg-page: #eef2f7;
        --text-muted: #607d8b;
        --accent: #1565c0;
        --radius: 12px;
    }

    .import-wrapper {
        /* background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08); */
        padding: 2rem;
        width: 100%;
        /* max-width: 780px; */
    }

    .import-wrapper h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1.5rem;
        letter-spacing: 0.02em;
    }

    .import-wrapper #btnRemove {
        display: block;
        width: 100%;
        color: #00000070;
        font-size: 16px;
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.10px;
        transition: opacity 0.3s ease;
    }

    .import-wrapper #btnRemove:hover {
        color: #00000090;
    }

    /* Drop Zone */
    .drop-zone {
        border: 2px dashed var(--border-dash);
        border-radius: var(--radius);
        background: var(--bg-upload);
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
        border-color: var(--accent);
        background: #e8f0fe;
    }

    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .excel-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 1rem;
        display: block;
    }

    .drop-zone .drag-text {
        /* color: var(--text-muted); */
        margin-bottom: 0.85rem;
        font-weight: 400;
    }

    .btn-upload {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        pointer-events: none;
        /* clicks handled by file input */
        transition: background 0.2s;
    }

    .drop-zone:hover .btn-upload {
        background: var(--primary-hover);
    }

    .file-label {
        display: block;
        color: #000000;
        opacity: .60;
        margin-top: 0.75rem;
    }

    /* Divider */
    .divider {
        display: none;
    }


    /* Right panel */
    .right-panel {
        display: flex;
        align-items: end;
        justify-content: start;
        height: 100%;
        /* border-left: 1px solid #e0e7ef; */
    }


    @media (max-width: 575.98px) {
        .right-panel {
            border-left: none;
            border-top: 1px solid #e0e7ef;
            padding-top: 1.25rem;
            margin-top: 0.5rem;
        }

        .import-wrapper {
            padding: 1.5rem 1rem;
        }
    }


    /* Selected file state */
    .drop-zone.file-selected {
        border-color: #c8d6e5;
    }



    .drop-zone.file-selected:hover {
        border-color: #F12F35;
        background: #f12f3520;
    }

    .file-selected-name {
        font-weight: 400;
        color: #000000;
    }

    /* ── Typography ── */
    .label-required {
        color: var(--red);
    }

    /* ── Card ── */
    .instructions-card {
        background: #fff;
        border: 1px solid #DFDFE1;
        border-radius: var(--radius);
        padding: 1.5rem 1.75rem;
        width: 100%;
        font-size: 0.84rem;
        color: #374151;
        line-height: 1.75;
    }

    .instructions-card #readMoreBtn {
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.10px;
        transition: opacity 0.3s ease;
    }

    .instructions-card #readMoreBtn:hover {
        opacity: .8;
    }

    .instructions-card .header-text {
        font-size: 0.84rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }

    .instructions-card .notice {
        font-style: italic;
        font-weight: 600;
        color: #1a2e5a;
        margin-bottom: 0.75rem;
    }

    .instructions-card .section-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.6rem;
        font-size: 0.9rem;
    }

    .instructions-card ul {
        padding-left: 1.25rem;
        margin-bottom: 0.75rem;
        list-style: disc;
    }

    .instructions-card ul li {
        margin-bottom: 0.25rem;
        color: #4b5563;
    }

    .instructions-card ul li strong {
        color: #111827;
        font-weight: 600;
    }

    /* ── Scrollable body ── */
    .card-scroll-body {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    }

    .card-scroll-body.expanded {
        max-height: none;
        overflow-y: visible;
    }

    /* Custom scrollbar */
    .card-scroll-body::-webkit-scrollbar {
        width: 4px;
    }

    .card-scroll-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .card-scroll-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .card-scroll-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ── Read More ── */
    .read-more-link {
        color: var(--red);
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: color 0.2s;
    }

    .read-more-link:hover {
        color: #c62828;
        text-decoration: underline;
    }

    /* ── Footer ── */
    .page-footer {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #90a4ae;
        padding: 0.6rem 0.25rem 0;
        margin-top: 0.75rem;
        border-top: 1px solid #e0e7ef;
    }

    .select2-container--custom {
        min-width: 130px;
    }

    .select2-container--custom .select2-selection--single {
        height: 36px;
        width: 158px;
        border: 1.5px solid #00000024;
        border-radius: 8px;
        background: #ffffff;
        padding: 0 36px 0 14px;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
        cursor: pointer;
        position: relative;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.06);

    }

    .select2-container--custom .select2-selection--single:hover {
        border-color: #b0b0b0;
    }

    .select2-container--custom.select2-container--open .select2-selection--single {
        border-color: #b0b0b0;
    }

    .select2-container--custom .select2-selection--single .select2-selection__rendered {
        font-size: 16px;
        font-weight: 400;
        color: #00000070;
        line-height: 33px;
        padding: 0;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow {
        position: absolute;
        top: 0;
        right: 10px;
        height: 36px;
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow b {
        display: none;
    }

    .select2-container--custom .select2-selection--single .select2-selection__arrow::after {
        content: '';
        display: block;
        width: 12px;
        height: 12px;
        border-right: 1.7px solid #00000050;
        border-bottom: 1.7px solid #00000050;
        transform: rotate(45deg) translate(-1px, -3px);
        transition: transform 0.2s ease;
    }

    .select2-container--custom.select2-container--open .select2-selection--single .select2-selection__arrow::after {
        transform: rotate(225deg) translate(-1px, -3px);
    }

    .select2-container--custom .select2-dropdown {
        border: 1.5px solid #e0e0e0;
        border-radius: 12px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
        overflow: hidden;
        margin-top: 5px;
    }

    .select2-container--custom.select2-container--below .select2-dropdown {
        border-top: 1.5px solid #e0e0e0;
        border-radius: 12px;
    }

    .select2-container--custom .select2-search--dropdown {
        display: none !important;
        padding: 0;
    }

    .select2-container--custom .select2-results {
        border-radius: 12px;
    }

    .select2-container--custom .select2-results__options {
        padding: 5px 0;
        max-height: 220px;
        overflow-y: auto;
    }

    .select2-container--custom .select2-results__option {
        font-size: 0.875rem;
        color: #1a1a1a;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.12s ease;
        cursor: pointer;
        background: #fff;
    }

    .select2-container--custom .select2-results__option--highlighted[aria-selected] {
        background: #f5f5f5 !important;
        color: #1a1a1a !important;
    }

    .select2-container--custom .select2-results__option[aria-selected="true"] {
        background: #ffffff !important;
        font-weight: 500;
        color: #1a1a1a !important;
    }

    .select2-container--custom .select2-results__option[aria-selected="true"]::after {
        content: '';
        display: inline-block;
        flex-shrink: 0;
        width: 14px;
        height: 14px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14' fill='none'%3E%3Cpath d='M2 7L5.5 10.5L12 3.5' stroke='%231a1a1a' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-size: contain;
    }

    .select2-container--custom .select2-results__options::-webkit-scrollbar {
        width: 4px;
    }

    .select2-container--custom .select2-results__options::-webkit-scrollbar-thumb {
        background: #d0d0d0;
        border-radius: 10px;
    }
</style>
<!-- CONTENT -->
<section class="content" id="main-user-list-wrapper">
    <div
        class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="py-3">
            <div class="container-fluid">
                <button onclick="location.href='{{ url('purchases') }}'"
                    class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
                    <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                            fill="currentColor" />
                    </svg>
                    <h2 class="h2-text mb-0">Purchase Info</h2>
                </button>
            </div>
        </div>
    </div>

    <main class="main-content" id="content-container">
        <div class="tab-bar">
            <ul class="nav nav-underline w-100" id="myTab" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#info-tab">
                        <span>Basic Information</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#document-tab">
                        <span>Documents</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#item-tab">
                        <span>Items</span>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#delete-tab">
                        <span>Delete</span>
                    </a>
                </li> --}}
                {{-- <li class="nav-item ms-auto">
                    <button class="btn btn-danger btn-sm dtActDel" data-id="{{ $purchase->id }}">
                        🗑 Delete
                    </button>
                </li> --}}


            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active container-fluid pt-3 pb-5" id="info-tab">
                @if(session('msg'))
                <div class="alert customize-alert alert-dismissible alert-light-danger bg-danger-subtle text-danger fade show remove-close-icon"
                    role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div class="d-flex align-items-center me-3 gap-3 me-md-0">
                        <svg width="18px" height="18px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                            fill="none">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z" />
                        </svg>
                        {{-- {{ session('msg')['msg'] }} --}}
                    </div>
                </div>
                @endif
            </div>
            <div class="mt-3 tab-pane fade table-responsive border-top-0" id="document-tab">
                <div class="card">
                    <div class="card-body">
                        @can('PurchaseDocumentAdd')
                        <div class="mb-4">

                            <div class="instructions-card">
                                <!-- Upload Area -->
                                <div id="attachment-dropper-cover">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="s1-text text-muted">
                                            Note:
                                            To upload attachments, Drag & Drop the file here (or)
                                        </span>
                                        <button id="manual_file_trigger" class="amg-btn amg-btn-primary">
                                            Add Attachment
                                        </button>
                                    </div>
                                    <!-- Drop Zone -->
                                    <div id="attachment-dropper" class="drop-zone">
                                        <p class="drag-text">Drag & Drop file here or click button</p>
                                    </div>
                                </div>
                                <!-- Attachments List -->
                                <div id="attachments" class="mt-3"></div>
                                <!-- Hidden Inputs -->
                                <input type="file" id="attachment" name="attachment" hidden />
                                <a href="#" id="attach_download" hidden></a>

                            </div>

                        </div>
                        @endcan
                        <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                            <!-- show select -->
                            {{-- <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                <select id="showSelect" class="showSelect">
                                    <option value="10" selected>Show (10)</option>
                                    <option value="25">Show (25)</option>
                                    <option value="50">Show (50)</option>
                                    <option value="100">Show (100)</option>
                                </select>
                            </div> --}}
                             <select id="showSelect" class="form-select" style="width:140px;">
                                <option value="10">Show (10)</option>
                                <option value="25">Show (25)</option>
                                <option value="50">Show (50)</option>
                                <option value="100">Show (100)</option>
                            </select>

                            <!-- spacer -->
                            <div class="flex-grow-1"></div>

                            <!-- searchbar -->
                            <div style="width: 280px;">
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input" id="documentSearch"
                                        placeholder="Search...">
                                </div>
                            </div>

                            <!-- refresh button -->
                            <button class="amg-refresh-btn btn-reload-list" id="documentRefresh">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>Refresh</span>
                            </button>
                        </div>

                        <table id="mytabledocument" class="table display">
                            <thead>
                                <tr>
                                    <th>
                                        <h4>ID</h4>
                                    </th>
                                    <th>
                                        <h4>Attachment Name</h4>
                                    </th>
                                    <th>
                                        <h4>Updated At</h4>
                                    </th>
                                    <th>
                                        <h4>Actions</h4>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade container-fluid pt-3 pb-5" id="item-tab">

                <div class="card">
                    <div class="card-body">

                        <!-- HEADER -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            {{-- <h4 class="h4-text" style="color:#7F7F7F">Purchase Items</h4> --}}

                            {{-- @can('PurchaseItemAdd') --}}
                            {{-- <button class="amg-btn amg-btn-primary open-add-modal">
                                + Add Item
                            </button> --}}
                            {{-- @endcan --}}
                        </div>

                        <!-- FILTERS -->
                        <div class="d-flex align-items-center gap-2 mb-1">

                            <!-- Show -->
                            <select id="itemLength" class="form-select" style="width:140px;">
                                <option value="10">Show (10)</option>
                                <option value="25">Show (25)</option>
                                <option value="50">Show (50)</option>
                                <option value="100">Show (100)</option>
                            </select>

                            <div class="flex-grow-1"></div>

                            <!-- spacer -->
                            <div class="flex-grow-1"></div>

                            <!-- searchbar -->
                            <div style="width: 280px;">
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input" id="tableSearch"
                                        placeholder="Search...">
                                </div>
                            </div>

                            <!-- refresh button -->
                            <button class="amg-refresh-btn btn-reload-list" id="itemRefresh">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>Refresh</span>
                            </button>
                            <button class="amg-btn amg-btn-primary open-item-modal">
                                 Add Item
                            </button>
                        </div>

                        {{--
                    </div> --}}

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table id="mytableitem" class="table display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

        </div>
    </main>
{{-- @include('purchases.purchase-info-modal') --}}
	@include('purchases.history-modal')
    @include('purchases.item_modal_html')
</section>

@endsection

@push('scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.filedrop/0.1.0/jquery.filedrop.min.js"></script> --}}
<script type="text/javascript" src="{!! CommonHelper::asset('js/purchase/index.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/purchase/info.js') !!}"></script>
<script type="text/javascript">
    var config = {};
	var dTbl = '';
    config.url = new Object;
    config.url.purchase             = "{{ url('purchases') }}";
    config.url.add 					= "{{ url('purchase/add') }}";
    config.url.edit 				= "{{ url('purchase/edit') }}";
    config.url.update 				= "{{ url('purchase/edit') }}";
    config.url.purchase_delete 		= "{{ url('purchase/delete') }}";
    config.url.list 				= "{{ url('jx-purchases') }}";
	config.url.purchase_info 		= "{{ url('purchase/info') }}";
    config.purchase_id              = "{{ $purchase->id }}";
    config.url.purchase_info_tab 	= "{{ url('purchase/info_tab') }}";
    config.url.purchaseFileDelete 	= "{{ url('purchase-file-delete') }}";
    config.url.historyModalUrl 		= "{{ url('purchase-history') }}";
    config.url.attachment_add       = "{{ url('purchase-attachment/add') }}";
    config.url.attachment_remove    = "{{ url('purchase-attachment/remove') }}";
    config.url.attachment_list      = "{{ url('purchase-attachment/list') }}";
    config.url.attachment_download  = "{{ url('purchase-attachment/download') }}";
    config.url.attachment_view      = "{{ url('purchase-attachment/view') }}";
    config.url.save                 = "{{ url('purchase-item/save') }}";
    config.url.purchaseItemList     = "{{ url('purchase-item/list') }}";
    config.url.editPurchaseItem     = "{{ url('purchase-item/edit') }}";
    config.url.getPurchaseItem      = "{{ url('purchase-item/get') }}";
    config.url.delete               = "{{ url('purchase-item/delete') }}";
    config.getUnitsByAjax           = "{{ url('getUnitsByQuery') }}";
	config.token 					= "{{ csrf_token() }}";
    config.permissions = {!! json_encode($permissionArray, true) !!};
    config.translations = {
		add_item: '{{ trans('config.purchase_fields.add_item') }}',
		press_enter_with_Search: '{{ trans('config.purchase_fields.press_enter_with_Search') }}',
		Search:'{{ trans('config.purchase_fields.Search') }}',
		Refresh_List:'{{ trans('config.purchase_fields.Refresh_List') }}',
		save: '{{ trans('config.purchase_fields.save') }}',
        something_went_wrong: '{{ trans('config.purchase_fields.something_went_wrong') }}',
        are_you_delete_item: '{{ trans('config.purchase_fields.are_you_delete_item') }}',
        are_you_delete: '{{ trans('config.purchase_fields.are_you_delete') }}',
        edit: '{{ trans('config.purchase_fields.edit') }}',
        delete: '{{ trans('config.purchase_fields.delete') }}',
        view: '{{ trans('config.purchase_fields.view') }}',
        edit_itme: '{{ trans('config.purchase_fields.edit_itme') }}',
        select_unit: '{{ trans('config.purchase_fields.select_unit') }}',
        enter_item: '{{ trans('config.purchase_fields.enter_item') }}',
        download_attachment: '{{ trans('config.purchase_fields.download_attachment') }}',
        uploading: '{{ trans('config.purchase_fields.uploading') }}',
	};

	new Purchase(config);
    new MyApp(config)

	$(document).on('click', '.dtActDel', function(e) {
		e.preventDefault();
		url = window.config.url.purchase_delete + "/" + $(this).attr('data-id');
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete,'warning', url, config.url.purchase, data,'url_yes');
		// vex.dialog.confirm({
		// 	message: 'Are you sure to delete this purchase?',
		// 	callback: function (value) {
		// 		if (value) {
		// 			$.getJSON(url, function(data) {
		// 				if (data.status == 'error')
		// 				   {
		// 					vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
		// 					return ;
		// 				   }

		// 				if (data.status == 'success')
		// 					vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
		// 					window.location = config.url.purchase;
		// 				$.get(config.url.infocontent, function(data) {
		// 					$('#info-tab').html(data);
		// 				})
		// 			})
		// 		} else {
		// 			//console.log('Chicken.')
		// 		}
		// 	}
		// })
	})

	$(document).on('click', '.del-link', function(e) {
		e.preventDefault();
		var attachId = $(this).attr('data-id');
		url = window.config.url.purchaseFileDelete + "/" + $(this).attr('data-id');

		vex.dialog.confirm({
			message: 'Are you sure to delete this file?',
			callback: function (value) {
				if (value) {
					$.getJSON(url, function(data) {
						if (data.status == 'error')
						   {
							vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
							return;
						   }

						if (data.status == 'success')
							{
								vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
								dTbl.ajax.reload()
								$("#purchasemodal").modal('hide');
							}
						$.get(config.url.infocontent, function(data) {
							$('#info-tab').html(data);
						})
					})
				} else {
					//console.log('Chicken.')
				}
			}
		})



	})

    $(document).on("click", "#manual_file_trigger", function (e) {
    e.preventDefault();
    $("#attachment").click();
});

$(document).on("change", "#attachment", function () {
    let fileName = this.files[0]?.name || "File selected";
    $("#attachment-dropper").show();
});
$(document).on("click", ".drop-zone", function () {
    $("#attachment").click();
});

    
	
</script>

@endpush