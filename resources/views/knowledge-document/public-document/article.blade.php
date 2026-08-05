{{-- * ------------------------------------------------------------
* File: article.blade.php  (Public Article)
* Module: Knowledge Document
* KNGD/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #007
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
@extends('layouts.articals')
@section('title', trans('tkt_knowledge_document.articles'))
@section('content')
    @php
        $kdTitle = trim(strip_tags(html_entity_decode($kd->kd_title ?? ($kd->title ?? ''))));
        $articleImage = !empty($kd->card_img) ? asset('uploads/article/' . $kd->card_img) : asset('imgs/kd_image.png');
        $relatedArticles = collect($kd_article ?? []);
        $tagColors = ['red', 'blue', 'green', 'orange', 'purple', 'teal'];
        $statusText = '';
        if (($kd->status ?? null) == 1) {
            $statusText = trans('tkt_knowledge_document.Public');
        } elseif (($kd->status ?? null) == 2) {
            $statusText = trans('tkt_knowledge_document.Private');
        } elseif (($kd->status ?? null) == 3) {
            $statusText = trans('tkt_knowledge_document.only_allowed_user');
        }
        elseif (($kd->status ?? null) == 4) {
            $statusText = trans('content.knowledge_document.non_login_public') ;
        }
    @endphp

    <nav class="top-navbar app-header position-relative">    
        <div class="d-flex justify-content-center align-items-center w-100 h-100">
            <div class="brand_logo">
                <a href="{{ url('') }}" class="navbar-brand">
                    <img src="{{ asset('main_asset/assets/images/amg-light.png') }}"
                        class="light-logo"
                        alt="{{ __('header.header.brand_alt') }}">
                </a>

                <a href="{{ url('') }}" class="navbar-brand">
                    <img src="{{ asset('main_asset/assets/images/amg-dark.png') }}"
                        class="dark-logo"
                        alt="{{ __('header.header.brand_alt') }}">
                </a>
            </div>
        </div>

        <div class="position-absolute top-50 end-0 translate-middle-y me-4">
            <button class="top-icon-btn" data-theme-toggle  data-bs-placement="bottom"  data-bs-toggle="tooltip"  title="{{ __('header.header.dark_mode') }}">
                <svg viewBox="0 0 22 22" fill="none">
                    <path d="M10.655 20.8101C16.2635 20.8101 20.8101 16.2635 20.8101 10.655C20.8101 5.04657 16.2635 0.5 10.655 0.5C5.04657 0.5 0.5 5.04657 0.5 10.655C0.5 16.2635 5.04657 20.8101 10.655 20.8101Z" stroke="currentColor"/>
                    <path d="M15.7229 5.58653C14.3785 4.24211 12.555 3.48682 10.6537 3.48682C8.75244 3.48682 6.929 4.24211 5.58458 5.58653C4.24015 6.93096 3.48486 8.75439 3.48486 10.6557C3.48486 12.557 4.24015 14.3804 5.58458 15.7249L10.6537 10.6557L15.7229 5.58653Z" fill="currentColor"/>
                </svg>
            </button>
        </div>

    </nav>

    <div id="page_boxed">
        <div id="main-knowledge-document-wrapper" class="kd-article-detail-shell">
            <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
                <symbol id="kd-arrow-right" viewBox="0 0 64 64">
                    <path d="M61.69 28c1.43 2.48 1.43 5.52 0 8L50.31 55.71a8 8 0 0 1-6.93 4H20.62a8 8 0 0 1-6.93-4L2.31 36a8 8 0 0 1 0-8L13.69 8.29a8 8 0 0 1 6.93-4h22.76a8 8 0 0 1 6.93 4L61.69 28Z" fill="#DA1A1A" />
                    <path d="M45.9 33.16 35.47 43.58a1.64 1.64 0 0 1-2.32-2.32l7.46-7.45H19.26a1.64 1.64 0 0 1 0-3.27h21.35l-7.45-7.46a1.64 1.64 0 0 1 2.32-2.32l10.42 10.42a1.64 1.64 0 0 1 0 1.98Z" fill="#fff" />
                </symbol>
                <symbol id="kd-arrow-left" viewBox="0 0 64 56">
                    <path d="M2.31 23.71a8 8 0 0 0 0 8l11.38 19.72a8 8 0 0 0 6.93 4h22.76a8 8 0 0 0 6.93-4l11.38-19.72a8 8 0 0 0 0-8L50.31 4a8 8 0 0 0-6.93-4H20.62a8 8 0 0 0-6.93 4L2.31 23.71Z" fill="#DA1A1A" />
                    <path d="M18.1 28.87 28.53 39.29a1.64 1.64 0 0 0 2.32-2.32l-7.46-7.45h21.35a1.64 1.64 0 0 0 0-3.28H23.39l7.45-7.46a1.64 1.64 0 0 0-2.32-2.32L18.1 26.55a1.64 1.64 0 0 0 0 2.32Z" fill="#fff" />
                </symbol>
            </svg>

            <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
                <h3 class="h3-text mb-0">{{trans("content.knowledge_document.Knowledge_Document") }}</h3>
                <div class="d-flex gap-8 kd-header-actions">
                    <div class="kd-toolbar-search search-container">
                        <div class="amg-list-searchbar">
                            <button class="btn-searchbox" type="button" data-bs-toggle="tooltip" title="{{ trans('tkt_knowledge_document.search') }}">
                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" id="search-box" class="amg-list-searchbar__input search-box searchbox plain-search" placeholder="{{ trans('tkt_knowledge_document.search_article') }}" autocomplete="off">
                            <div class="input-loader" id="input-loader"></div>
                        </div>
                        <div id="suggestions" class="suggestions-box"></div>
                    </div>
                </div>
            </div>

            <main class="main-content" id="mainContent">
                <div class="container-fluid px-0">
                    <div class="card rounded-0 kd-shell-card">
                        <div class="card-body pt-3">
                            <div class="kd-page" id="article-info-page">
                                <section class="kd-detail-hero">
                                    <h1 class="h1-text">{{ $kdTitle }}</h1>
                                    <div class="kd-hero-word">{{ trans('tkt_knowledge_document.articles') }}</div>
                                    <div class="kd-hero-crumb">
                                        <span>{{trans("content.knowledge_document.Knowledge_Document") }}</span>
                                        <strong>/ {{ trans('tkt_knowledge_document.article_detail') }}</strong>
                                    </div>
                                </section>

                                <div class="kd-detail-grid">
                                    <article class="kd-main-article">
                                        <div class="kd-main-image" style="background-image:url('{{ $articleImage }}')">
                                            <div class="kd-date-tab">
                                                <i class="fa fa-clock-o"></i>
                                                <span>{{ $kd->kd_created_at ?? CommonHelper::getDateAs($kd->created_at, 'd M Y h:i A', 'Y-m-d H:i:s') }}</span>
                                            </div>
                                            <a href="{{ url('/public-articles') }}" class="kd-back-tab js-act-go-back" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('button.back') }}">
                                                <svg><use href="#kd-arrow-left"></use></svg>
                                            </a>
                                        </div>

                                        

                                        <div class="kd-article-content target word-wrap">
                                            {!! $kd->content !!}
                                        </div>

                                        <div class="row attach-doc d-none">
                                            <div class="col-sm-12">
                                                <p class="attachment-title">{{ trans('tkt_knowledge_document.attachments') }}</p>
                                                <div id="tkt-content">
                                                    <div class="main_attachments attachments mar-top"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ticket_timeline" class="timeline no-bg d-none"></div>
                                    </article>

                                    <aside class="kd-detail-sidebar">
                                        

                                        <div class="kd-sidebar-panel">
                                            @if ($creator_by)
                                                <div class="kd-sidebar-card">
                                                    <h3 class="h3-text">{{ trans('tkt_knowledge_document.article_info') }}</h3>
                                                    <div class="article-info">
                                                        <div class="user-info">
                                                            <img src="{{ $creator_by && $creator_by->getProfileImg() ? $creator_by->getProfileImg() : asset('imgs/profile-75.jpg') }}" alt="{{ trans('tkt_knowledge_document.user') }}" class="avatar">
                                                            <div class="user-details">
                                                                <b>{{ $creator_by->fullName() }}</b>
                                                            </div>
                                                        </div>
                                                        <div class="kd-status-badge">
                                                            <p>{{ trans('tkt_knowledge_document.article_status_label') }}</p>
                                                            @if($statusText)
                                                                <span>{{ $statusText }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- @if((Auth::user()->hasAnyRole(['SuperAdmin', 'Admin']) || Auth::user()->hasPermission('service_tickets'))) --}}
                                                <div class="kd-sidebar-card mb-0">
                                                    <h3 class="h3-text">{{ trans('tkt_knowledge_document.tags') }}</h3>
                                                    <div class="kd-tags">
                                                        @if (isset($tags) && !empty($tags))
                                                            @foreach ($tags as $tag)
                                                                @if ($tag->tags != '')
                                                                    <span class="kd-tag">{{ $tag->tags }}</span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            {{-- @endif --}}
                                        </div>

                                        @if ($relatedArticles->count() > 0 && config('services.knowledge_document.enabled'))
                                            @can('KnowledgeDocumentRead')
                                                <div class="kd-related-panel">
                                                    <h2>{{ trans('tkt_knowledge_document.related_most') }}</h2>
                                                    @foreach ($relatedArticles as $kda)
                                                        <article class="kd-related-card">
                                                            <a href="{{ url('knowledge_document/article/view/' . $kda->id) }}" target="_blank">
                                                                <div class="kd-related-image" style="background-image:url('{{ !empty($kda->card_img) ? asset('/uploads/article/' . $kda->card_img) : asset('imgs/kd_image.png') }}')">
                                                                    <span class="kd-arrow"><svg><use href="#kd-arrow-right"></use></svg></span>
                                                                </div>
                                                                <div class="kd-related-copy">
                                                                    <h4>{{ $kda->title }}</h4>
                                                                    @if (isset($kda->dep_name))
                                                                        <span>{{ $kda->dep_name }}</span>
                                                                    @endif
                                                                    <small><i class="bi bi-stopwatch"></i> {{ $kda->kd_created_at }}</small>
                                                                </div>
                                                            </a>
                                                        </article>
                                                    @endforeach
                                                </div>
                                            @endcan
                                        @endif
                                    </aside>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="kd-image-preview-modal" class="amg-modal modal fade kd-image-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title kd-image-preview-title"></h5>
                    <button type="button" class="modal-close kd-image-preview-close" data-bs-dismiss="modal" aria-label="{{ trans('tkt_knowledge_document.close') }}" title="{{ trans('tkt_knowledge_document.close') }}">
                         <svg viewBox="0 0 31 31" fill="currentColor">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body kd-image-preview-body">
                    <div class="kd-image-preview-message">{{ trans('tkt_knowledge_document.loading_preview') }}</div>
                    <img src="" alt="">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/pnotify/pnotify.custom.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link rel="stylesheet" href="{!! CommonHelper::asset('newcss/fonts/font-awesome-animation.min.css') !!}">
    <style>
        [data-bs-theme="dark"] #main-knowledge-document-wrapper {
            --page-bg: var(--app-bg);
            --arrow-surface: var(--app-bg);
            --text: var(--text-primary);
            --muted: var(--text-muted);
            --line: var(--dark-border);
            --chip-bg: var(--dark-secondary);
            --chip-text: var(--text-secondary);
            --chip-border: var(--dark-tertiary);
            --field-bg: var(--dark-secondary);
            --field-border: var(--dark-border);
            --panel-bg: var(--dark-secondary);
            --card-bg: var(--dark-tertiary);
        }
        .amg-list-searchbar{
            border: none
        }

        #main-knowledge-document-wrapper {
            --page-bg: #fff;
            --arrow-surface: #fff;
            --text: #102033;
            --muted: #6d7a8b;
            --line: #e8edf3;
            --red: #da1a1a;
            --panel-bg: #f7f7f7;
            --card-bg: #fff;
            --soft-red: #fbe8e8;
            color: var(--text);
        }

        #main-knowledge-document-wrapper * {
            box-sizing: border-box;
        }

        #main-knowledge-document-wrapper a {
            color: inherit;
            text-decoration: none;
        }

        #main-knowledge-document-wrapper .kd-shell-card {
            border: 0;
            background: var(--page-bg);
            box-shadow: none;
        }

        #main-knowledge-document-wrapper .header-actions-wrapper {
            margin-bottom: 0;
        }

        #main-knowledge-document-wrapper .kd-header-actions {
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        #main-knowledge-document-wrapper .kd-toolbar-search {
            position: relative;
            /* width: min(30rem, 100vw - 2rem); */
            /* max-width: 100%; */
        }

        #main-knowledge-document-wrapper .amg-list-searchbar {
            position: relative;
            height: unset !important;
            width: 18.75rem;
        }

        #main-knowledge-document-wrapper .amg-list-searchbar > .btn-searchbox {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: inherit;
        }

        #main-knowledge-document-wrapper .search-box {
            padding-right: 3rem;
        }

        #main-knowledge-document-wrapper .input-loader {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            display: none;
            width: 1rem;
            height: 1rem;
            transform: translateY(-50%);
            z-index: 3;
        }

        .amg-list-searchbar:focus-within {
            box-shadow: none !important;
        }

        #main-knowledge-document-wrapper .input-loader::before {
            position: absolute;
            content: "";
            width: 1rem;
            height: 1rem;
            border: 0.125rem solid var(--line);
            border-top-color: var(--red);
            border-radius: 50%;
            animation: kd-spin 0.8s linear infinite;
        }

        @keyframes kd-spin {
            to { transform: rotate(360deg); }
        }

        #main-knowledge-document-wrapper .suggestions-box {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            right: 0;
            display: none;
            max-height: 16rem;
            overflow-y: auto;
            z-index: 1000;
            border: 0.0625rem solid var(--line);
            border-radius: 0.5rem;
            background: var(--card-bg);
            box-shadow: 0 0.75rem 1.875rem rgba(15, 23, 42, 0.14);
        }

        #main-knowledge-document-wrapper .suggestion-item,
        #main-knowledge-document-wrapper .no-results {
            padding: 0.75rem 1rem;
            color: var(--text);
        }

        #main-knowledge-document-wrapper .suggestion-item {
            cursor: pointer;
        }

        #main-knowledge-document-wrapper .suggestion-item:hover {
            background: var(--panel-bg);
        }

        #main-knowledge-document-wrapper .no-results {
            color: var(--muted);
        }

        .kd-page {
            width: 100%;
            max-width: 90rem;
            margin: 0 auto;
            padding: 0 1.125rem;
        }

        #main-knowledge-document-wrapper .kd-page h2,
        #main-knowledge-document-wrapper .kd-page h3,
        #main-knowledge-document-wrapper .kd-page h4,
        #main-knowledge-document-wrapper .kd-page p {
            margin-top: 0;
        }

        /* .kd-detail-hero {
            position: relative;
            min-height: 11rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0.5rem;
            background-color: #36053e;
            background-position: center;
            background-size: cover;
            isolation: isolate;
        }

        .kd-detail-hero::before {
            position: absolute;
            content: "";
            inset: 0;
            z-index: -1;
            background: linear-gradient(90deg, rgba(24, 0, 31, 0.65), rgba(42, 41, 194, 0.26));
        } */

        .kd-detail-hero {
            position: relative;
            min-height: 11rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0.5rem;
            background-image:  url('{{ asset('uploads/article/article-datail.jpg') }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            isolation: isolate;
        }


        /* Overlay */
        .kd-detail-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;            
        }

        .kd-detail-hero h1 {
            max-width: min(42rem, 70%);
            margin: 0;
            color: #fff;
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            word-break: break-word;
        }

        .kd-hero-word {
            position: absolute;
            right: 1.125rem;
            top: 0.75rem;
            color: rgba(255, 255, 255, 0.92);
            font-size: 2rem;
            font-weight: 600;
            line-height: 1;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            letter-spacing: 0;
        }

        .kd-hero-crumb {
            position: absolute;
            right: 10%;
            bottom: -0.0625rem;
            /* min-width: 18.75rem;s */
            padding: .85rem .75rem 0.75rem;
            border-radius: 8px;
            background: var(--page-bg);
            color: var(--red);
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
        }

        .kd-hero-crumb::before,
        .kd-hero-crumb::after {
            position: absolute;
            bottom: 0;
            content: "";
            width: 2.5rem;
            height: 2.5rem;
            background: transparent;
        }

        .kd-hero-crumb::before {
            left: -2.5rem;
            border-bottom-right-radius: 1rem;
            box-shadow: 0.75rem 0.75rem var(--page-bg);
        }

        .kd-hero-crumb::after {
            right: -2.5rem;
            border-bottom-left-radius: 1rem;
            box-shadow: -0.75rem 0.75rem var(--page-bg);
        }

        .kd-detail-hero .kd-hero-crumb::before,
        .kd-detail-hero .kd-hero-crumb::after {
            border-radius: 8px;
        }

        .kd-hero-crumb strong {
            color: var(--text);
        }

        .kd-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(17rem, 1.3fr);
            gap: 1.125rem;
            align-items: start;
        }

        .kd-main-article {
            min-width: 0;
        }

        /* .kd-main-image {
            position: relative;
            min-height: 22.5rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            background-position: center;
            background-size: cover;
            overflow: visible;
        } */

        .kd-main-image {
            position: relative;
            min-height: 22.5rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;

            background-position: center center;
            background-repeat: no-repeat;

            /* portrait + landscape both support */
            background-size: contain;

            background-color: #111;
        }

        .kd-date-tab {
            position: absolute;
            left: 1.5rem;
            bottom: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            min-height: 2.875rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px 8px 0 0;
            background: var(--page-bg);
            color: var(--text);
            font-size: 0.8125rem;
        }

        .kd-date-tab i {
            color: var(--red);
        }

        .kd-back-tab {
            position: absolute;
            right: -0.875rem;
            bottom: -0.625rem;
            z-index: 5;
            width: 4.75rem;
            height: 4.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 1.125rem;
            background: var(--arrow-surface, #fff);
        }

        .kd-back-tab::before,
        .kd-back-tab::after {
            position: absolute;
            content: "";
            width: 1.375rem;
            height: 1.375rem;
            background: transparent;
            border-bottom-right-radius: 1.125rem;
            box-shadow: 0.4375rem 0.4375rem var(--arrow-surface, #fff);
        }

        .kd-back-tab::before {
            left: -1.375rem;
            bottom: 0.625rem;
        }

        .kd-back-tab::after {
            right: 0.875rem;
            top: -1.375rem;
        }

        .kd-back-tab svg {
            position: relative;
            z-index: 1;
            width: 2.75rem;
            height: 2.5rem;
        }

        .kd-article-content {
            color: var(--text);
            font-size: 1rem;
            line-height: 1.55;
        }

        .kd-article-content h1,
        .kd-article-content h2,
        .kd-article-content h3 {
            color: var(--text);
        }

        .kd-article-content img {
            max-width: 100%;
            height: auto;
        }

        .attachment-title {
            margin: 1rem 0 0.75rem;
            color: var(--red);
            font-weight: 600;
            font-size: 0.9375rem;
        }

        #article-info-page .main_attachments .kd-attachment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: flex-start;
        }

        #article-info-page .main_attachments .kd-attachment-card {
            width: 9.375rem;
            min-height: 5.625rem;
            display: flex;
            align-items: stretch;
            justify-content: center;
            padding: 0.625rem 0.5rem 0.5rem;
            border: 0.0625rem solid #c7c7c7;
            border-radius: 0.125rem;
            background: #f5f5f5;
            box-shadow: none;
        }

        #article-info-page .main_attachments .attach-item-cntnt {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        #article-info-page .main_attachments .attach-name {
            width: 100%;
            min-height: 2.375rem;
            display: -webkit-box;
            overflow: hidden;
            color: #000;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1.45;
            text-align: center;
            text-overflow: ellipsis;
            word-break: break-word;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        #article-info-page .main_attachments .icons {
            display: flex;
            justify-content: center;
            gap: 1.125rem;
        }

        #article-info-page .main_attachments .kd-attach-action {
            width: 2.375rem;
            height: 2.1875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0.1875rem;
            background: #000;
            color: #fff;
            cursor: pointer;
            font-size: 0.9375rem;
            line-height: 1;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        #article-info-page .main_attachments .kd-attach-action:hover {
            background: var(--red);
            color: #fff;
            transform: translateY(-0.0625rem);
        }

        [data-bs-theme="dark"] #article-info-page .main_attachments .kd-attachment-card {
            border-color: var(--line);
            background: var(--card-bg);
        }

        [data-bs-theme="dark"] #article-info-page .main_attachments .attach-name {
            color: var(--text);
        }

        /* #kd-image-preview-modal .modal-dialog {
            max-width: min(92vw, 68rem);
        }

        #kd-image-preview-modal .modal-content {
            border: 0;
            background: #101010;
            overflow: hidden;
            box-shadow: 0 1.25rem 3.5rem rgba(0, 0, 0, 0.4);
        }

        #kd-image-preview-modal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background: #171717;
            color: #fff;
        }

        #kd-image-preview-modal .kd-image-preview-close {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 50%;
            background: #fff;
            color: #111;
            cursor: pointer;
        }

        #kd-image-preview-modal .kd-image-preview-title {
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            word-break: break-word;
        }

        #kd-image-preview-modal .kd-image-preview-body {
            position: relative;
            min-height: 18rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: #080808;
        }

        #kd-image-preview-modal .kd-image-preview-message {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.95rem;
            font-weight: 500;
        } */

        #kd-image-preview-modal .kd-image-preview-message.error {
            color: #ffb4b4;
        }

        #kd-image-preview-modal .kd-image-preview-body img {
            display: block;
            max-width: 100%;
            max-height: 78vh;
            border-radius: 0.25rem;
            object-fit: contain;
        }

        .kd-ticket-card,
        .kd-action-panel,
        .kd-sidebar-panel,
        .kd-related-panel {
            border-radius:1rem;
            background: var(--panel-bg);
        }

        .kd-ticket-card {
            margin-bottom: 1.25rem;
            overflow: hidden;
            box-shadow: 0 0.25rem 1.25rem rgba(15, 23, 42, 0.08);
        }

        .kd-ticket-header {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.875rem;
            padding: 1rem;
            background: var(--soft-red);
        }

        .kd-ticket-header span,
        .kd-ticket-details span {
            display: block;
            color: var(--muted);
            font-size: 0.75rem;
            margin-bottom: 0.3125rem;
        }

        .kd-ticket-header strong,
        .kd-ticket-details strong {
            display: block;
            color: var(--text);
            font-size: 0.875rem;
        }

        .kd-success-pill {
            width: fit-content;
            padding: 0.25rem 0.625rem;
            border-radius: 999px;
            background: #26a653;
            color: #fff !important;
        }

        .kd-ticket-user {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .kd-ticket-user img {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .kd-ticket-details {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.875rem;
            padding: 1rem;
            background: var(--card-bg);
        }

        .kd-detail-sidebar {
            display: grid;
            gap: 1rem;
        }

        .kd-action-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
            padding: .5rem;
            background: var(--soft-red);
            border-radius: 8px;
        }

        .kd-action-panel .button-round {
            /* min-height: 2.75rem; */
            padding: .5rem;
            border-radius: 8px;
            font-size: .75rem;
        }

        .kd-sidebar-panel,
        .kd-related-panel {
            padding: 1.125rem;
        }

        .kd-sidebar-card {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background: var(--card-bg);
            box-shadow: 0 0.25rem 1.125rem rgba(15, 23, 42, 0.06);
        }

        .kd-sidebar-card h3,
        .kd-related-panel h2 {
            margin: 0 0 1rem;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
        }

        .kd-related-panel h2 {
            font-size: 1.6875rem;
        }

        .article-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details {
            min-width: 0;
            margin-left: 0.625rem;
        }

        .user-details b,
        .user-details span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .75rem;
        }

        .user-details span,
        .kd-status-badge p {
            color: var(--muted);
        }

        .kd-status-badge {
            text-align: center;
        }

        .kd-status-badge p {
            margin: 0 0 0.25rem;
            font-size: 0.75rem;
        }

        .kd-status-badge span {
            display: inline-block;
            padding: .4rem;
            border-radius: 4px;
            background: #ef4444;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .kd-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .kd-tag {
            display: inline-flex;
            padding: 0.375rem 0.875rem;
            border-radius: 4px;
            color: #7e8793;
            background-color: #eeeeed;
            font-size: 0.6875rem;
            font-weight: 700;
        }
        [data-bs-theme="dark"] .kd-tag{
            background-color: #2a2a2a !important;
        }


        .kd-related-card + .kd-related-card {
            margin-top: 1rem;
        }

        .kd-related-card a {
            display: grid;
            grid-template-columns: minmax(7rem, 45%) minmax(0, 1fr);
            gap: 0.875rem;
            align-items: center;
        }

        .kd-related-image {
            position: relative;
            min-height: 7.625rem;
            border-radius: 0.5rem;
            background-color: #820707;
            background-image: url('{{ asset('imgs/kd_image.png') }}');
            background-position: center;
            background-size: cover;
            overflow: visible;
        }

       .kd-related-card + .kd-related-card{
            margin-bottom: 0.9375rem;
         }

       .kd-related-card + .kd-related-carda {
            display: grid;
            grid-template-columns: minmax(9.375rem, 58%) 1fr;
            gap: 0.875rem;
            align-items: start;
        }

        .kd-related-image {
            min-height: 6.375rem;
            border-radius: 0.375rem;
        }

        .kd-arrow {
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: 5;
            width: 2.625rem;
            height: 2.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 0.75rem;
            background: var(--panel-bg, #fff);
        }

        .kd-arrow::before,
        .kd-arrow::after {
            position: absolute;
            content: "";
            /* width: 0.9375rem;
            height: 0.9375rem; */
            background: transparent;
            border-bottom-right-radius: 0.6875rem;
            box-shadow: 0.3125rem 0.3125rem var(--arrow-surface, #fff);
        }

        .kd-arrow::before {
            left: -0.9375rem;
            bottom: 0.5625rem;
        }

        .kd-arrow::after {
            right: 0.75rem;
            top: -0.9375rem;
        }

        .kd-arrow svg {
            position: relative;
            z-index: 1;
            width: 1.875rem;
            height: 1.75rem;
        }

        .kd-related-copy h4 {
            margin: 0 0 0.5rem;
            color: var(--text);
            font-size: 0.9375rem;
            font-weight: 600;
        }

        .kd-related-copy span {
            display: inline-flex;
            margin-bottom: 0.75rem;
            padding: 0.1875rem 0.75rem;
            border: 0.0625rem solid var(--text);
            border-radius: 4px;
            color: var(--text);
            font-size: 0.6875rem;
        }

        .kd-related-copy small {
            display: block;
            color: var(--muted);
        }

        #ticket_timeline {
            margin-top: 1rem;
        }

        #articleupdateModal {
            overflow-y: auto !important;
        }

        [data-bs-theme="dark"] #main-knowledge-document-wrapper .kd-hero-crumb,
        [data-bs-theme="dark"] #main-knowledge-document-wrapper .kd-date-tab {
            background: var(--page-bg);
        }

        [data-bs-theme="dark"] #main-knowledge-document-wrapper .kd-action-panel,
        [data-bs-theme="dark"] #main-knowledge-document-wrapper .kd-ticket-header {
            background: rgba(218, 26, 26, 0.16);
        }

        @media (max-width: 74.9375rem) {
            .kd-detail-grid {
                grid-template-columns: 1fr;
            }

            .kd-detail-sidebar {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .kd-related-panel {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 47.9375rem) {
            #main-knowledge-document-wrapper .header-actions-wrapper {
                align-items: flex-start !important;
                gap: 0.75rem;
                flex-wrap: wrap;
                padding-right: 0.75rem !important;
            }

            #main-knowledge-document-wrapper .kd-toolbar-search {
                width: 100%;
            }

            .kd-page {
                padding: 0.625rem 0.75rem 0;
            }

            .kd-detail-hero {
                min-height: 8rem;
                margin-bottom: 2rem;
            }

           

            .kd-hero-word {
                font-size: 1.75rem;
            }

            .kd-hero-crumb {
                right: 1rem;
                min-width: 12rem;
                padding: 0.875rem 1rem 0.625rem;
                font-size: 0.8125rem;
            }

            .kd-main-image {
                min-height: 15rem;
            }

            .kd-ticket-header,
            .kd-ticket-details,
            .kd-detail-sidebar,
            .kd-related-card a {
                grid-template-columns: 1fr;
            }

            .article-info {
                align-items: flex-start;
                flex-direction: column;
            }

            .kd-article-content {
                font-size: 1rem;
            }

            
        }

         .main-content {
            margin-left: 0 !important;
        }

        .header-actions-wrapper{
            margin-top:0 !important;
        }
    </style>
@endpush

@push('scripts')
    {{-- @include('includes.jslib') --}}
    <script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/jquery_filedrop/jquery.filedrop.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/pnotify/pnotify.custom.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/lord-icon/lord-icon-2.0.2.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/drag-drap.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/knowledge-document/attachments.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/knowledge-document/public_article.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.placeholder = {};
            config.title = {};
            config.url.site_url = "{{ url('/') }}";
            config.token = "{{ csrf_token() }}";
            config.main_attachments = {!! json_encode($attachments) !!};
            config.url.get_timeline = "{{ url('kd/get_timeline') }}";
            config.url.attachment_download = "{{ url('public-articles/attachment/download') }}";
            config.url.attachment_view = "{{ url('public-articles/attachment/view') }}";
            config.url.ticket_attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.ticket_attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.get_tag = "{{ url('knowledge_document/get-tag') }}";
            config.url.updateArticleTags = "{{ url('knowledge_document/tags/update') }}";
            config.url.getTagDetails = "{{ url('knowledge_document/document/getTagDetails') }}";
            config.url.staring = "{{ url('knowledge_document/article/staring') }}";
            config.url.document = "{{ url('knowledge_document/document') }}";
            config.id = {!! json_encode($kd->id) !!};
            config.url.delete = "{{ url('knowledge_document/document/delete') }}";
            config.url.edit = "{{ url('knowledge_document/document/edit') }}";
            config.url.kd_attachment_add = "{{ url('knowledge_document/attachment/kd_attachment') }}";
            config.url.kd_attachment_remove = "{{ url('knowledge_document/attachment/kd_attachment_remove') }}";
            config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
            config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage') }}";
            config.url.departments_with_company = "{{ url('getDepartmentsWithCompanyByQuery') }}";
            config.url.problem_categories_by_dept = "{{ url('kd/problem-categories/by-dept') }}";
            config.url.problem_categories = "{{ url('kd/problem-categories') }}";
            config.url.update = "{{ url('knowledge_document/document/update') }}";
            config.url.searchurl = "{{ url('public-document/search') }}";
            config.url.view = "{{ url('public-article/view') }}";
            config.url.fetch_category_by_ajax = "{{ url('knowledge_document/fetch_category_by_ajax') }}";
            config.url.fetch_subcategory_by_ajax = "{{ url('knowledge_document/fetch_subcategory_by_ajax') }}";
            config.translations = {
                are_you_star: '{{ trans('content.service_ticket_fields.are_you_star') }}',
                something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                are_you_want_Delete: '{{ trans('content.knowledge_document.are_you_want_Delete') }}',
                Edit_Article: '{{ trans('content.knowledge_document.Edit_Article') }}',
                No_Filter: '{{ trans('content.knowledge_document.no_filter') }}',
                Parent_category: '{{ trans('content.service_ticket_fields.Select_Parent_Category') }}',
                Sub_Category: '{{ trans('content.service_ticket_fields.Select_Sub_Category') }}',
                filter_by_department: '{{ trans('content.filter_heading.filter_by_department') }}',
                select_status: '{{ trans('content.knowledge_document.select_status') }}',
                Starred: '{{ trans('content.service_ticket_fields.Starred') }}',
                Add_Star: '{{ trans('content.service_ticket_fields.Add_Star') }}',
                select_department: '{{ trans('content.knowledge_document.select_department') }}',
                select_sub_catgeory: '{{ trans('content.knowledge_document.select_sub_catgeory') }}',
                comment_summer: '{{ trans('content.knowledge_document.Enter_content') }}',
                upload_file: '{{ trans('content.service_ticket_fields.upload_file') }}',
            };
            config.url.back_to = "{{ route('kdLists') }}";
          
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': config.token
                }
            });
            new MyApp(config);
            new Article(config);
        });
    </script>
@endpush
