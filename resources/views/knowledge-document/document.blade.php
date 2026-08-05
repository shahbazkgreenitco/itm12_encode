
{{-- * ------------------------------------------------------------
* File: document.blade.php
* Module: Knowledge Document
* KNGD/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Sandeep Verma
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Change the UI Degin And Document Image Fixing (Hrishikesh Pandey)
* [1.0.0] - Initial version (Sandeep Verma)
* ------------------------------------------------------------ --}}
@extends('layouts.layout1')
@section('title', trans('tkt_knowledge_document.articles'))
@section('content')
    @php
        $kdPlainText = function ($value, $limit = null) {
            $text = trim(strip_tags(html_entity_decode($value ?? '')));
            return $limit ? \Illuminate\Support\Str::limit($text, $limit, '...') : $text;
        };

        $kdArticleUrl = function ($article) {
            return !empty($article->id) ? url('knowledge_document/article/view/' . $article->id) : '#';
        };

        $kdImageAttr = function ($article) {
            return !empty($article->card_img)
                ? 'style="background-image:url(\'' . e(url('uploads/article/' . $article->card_img)) . '\')"'
                : '';
        };

        $kdImageClass = function ($article, $classes = '') {
            return trim($classes . (empty($article->card_img) ? ' kd-red-card' : ''));
        };

        $popularSource = $popular_article ?? null;
        $popularItems =
            $popularSource instanceof \Illuminate\Support\Collection
                ? $popularSource
                : collect(!empty($popularSource) ? [$popularSource] : []);
        $mostItems = collect($most_visited ?? []);
        $latestItems = collect($latest_articles ?? []);
        $tagItems = collect($tag ?? []);
    @endphp

    <div id="page_boxed">
        <div id="main-knowledge-document-wrapper">
            <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
                <symbol id="kd-arrow-right" viewBox="0 0 64 64">
                    <path
                        d="M61.69 28c1.43 2.48 1.43 5.52 0 8L50.31 55.71a8 8 0 0 1-6.93 4H20.62a8 8 0 0 1-6.93-4L2.31 36a8 8 0 0 1 0-8L13.69 8.29a8 8 0 0 1 6.93-4h22.76a8 8 0 0 1 6.93 4L61.69 28Z"
                        fill="#DA1A1A" />
                    <path
                        d="M45.9 33.16 35.47 43.58a1.64 1.64 0 0 1-2.32-2.32l7.46-7.45H19.26a1.64 1.64 0 0 1 0-3.27h21.35l-7.45-7.46a1.64 1.64 0 0 1 2.32-2.32l10.42 10.42a1.64 1.64 0 0 1 0 1.98Z"
                        fill="#fff" />
                </symbol>
                <symbol id="kd-arrow-down" viewBox="0 0 64 56">
                    <path
                        d="M61.69 23.71a8 8 0 0 1 0 8L50.31 51.43a8 8 0 0 1-6.93 4H20.62a8 8 0 0 1-6.93-4L2.31 31.71a8 8 0 0 1 0-8L13.69 4a8 8 0 0 1 6.93-4h22.76a8 8 0 0 1 6.93 4l11.38 19.71Z"
                        fill="#DA1A1A" />
                    <path
                        d="m30.84 41.61-10.42-10.42a1.64 1.64 0 0 1 2.32-2.32l7.45 7.45V14.97a1.64 1.64 0 0 1 3.28 0v21.35l7.45-7.45a1.64 1.64 0 0 1 2.32 2.32L33.16 41.61a1.64 1.64 0 0 1-2.32 0Z"
                        fill="#fff" />
                </symbol>
            </svg>

            <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
                <h3 class="h3-text mb-0">{{trans("content.knowledge_document.Knowledge_Document") }}</h3>
                <div class="d-flex gap-8 kd-header-actions">

                    <div class="kd-toolbar-search search-cover-box">
                        <div class="amg-list-searchbar">
                            <button class="btn-searchbox" type="button" data-bs-toggle="tooltip"
                                title="{{ trans('tkt_knowledge_document.search') }}">
                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input search_box searchbox plain-search"
                                placeholder="{{ trans('tkt_knowledge_document.search') }}">
                        </div>
                    </div>
                     <button class="header-icon-btn-only header-icon-btn-only-sm btn-reload" type="button"
                        data-bs-toggle="tooltip" title="{{ trans('tkt_knowledge_document.reload') }}">
                        <svg viewBox="0 0 20 18" fill="none">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                fill="currentColor" />
                        </svg>
                    </button>

                    <button class="header-icon-btn header-icon-btn-sm btn-visible-content" type="button"
                        data-bs-toggle="tooltip" title="{{ trans('tkt_knowledge_document.filter') }}">
                        <svg viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b6-text opacity-50">{{ trans('tkt_knowledge_document.filter') }}</span>
                        <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                    </button>
                   
                    @can('KnowledgeDocumentArticleAdd')
                        <button class="amg-btn amg-btn-primary amg-btn-sm js-act-add-document" type="button"
                            data-bs-toggle="tooltip" title="{{ trans('tkt_knowledge_document.add_article') }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor" />
                            </svg>
                            <span>{{ trans('tkt_knowledge_document.add_article') }}</span>
                        </button>
                    @endcan
                </div>
            </div>

            <main class="main-content" id="mainContent">
                <div class="container-fluid px-0">
                    <div class="card rounded-0 kd-shell-card">
                        <div class="card-body pt-3">
                            <div id="api_loader" class="hide"></div>
                            <button type="button" id="loadMoreBtn" class="d-none hide" aria-hidden="true"></button>
                            <div class="kd-list-toolbar d-flex align-items-center gap-2 mb-3">
                                <div class="kd-chip-row department"></div>
                                <div class="flex-grow-1"></div>
                            </div>
                            @include('knowledge-document.filter')
                            <div class="kd-page kd-api-pending" id="kd-article-page">
                                <section class="kd-top-grid">
                                    <article class="kd-feature">
                                        <h3 class="h3-text mb-0">{{ trans('tkt_knowledge_document.popular_articles') }}</h3>
                                        @foreach ($popularItems->take(1) as $article)
                                            <a href="{{ $kdArticleUrl($article) }}" target="_blank" class="text-decoration-none">
                                                <div class="{{ $kdImageClass($article, 'kd-card-image kd-hero') }}"
                                                    {!! $kdImageAttr($article) !!}>
                                                    @if (empty($article->card_img))
                                                        <span class="kd-watermark large">KD</span>
                                                    @endif
                                                    <span class="kd-arrow">
                                                        <svg>
                                                            <use href="#kd-arrow-right"></use>
                                                        </svg></span>
                                                </div>
                                                <div class="kd-meta">
                                                    <span class="kd-pill">{{ $article->dep_name ?? ($article->category_name ?? '') }}</span>
                                                    <span>{{ $article->updated_at_format ?? '' }}</span>
                                                </div>
                                                <h4 class="h4-text">{{ $kdPlainText($article->title ?? '', 52) }}</h4>
                                                <p>{{ $kdPlainText($article->content ?? '', 56) }}</p>
                                            </a>
                                        @endforeach
                                    </article>

                                    <aside class="kd-most">
                                        <h3 class="h3-text">{{ trans('tkt_knowledge_document.most_viewed') }}</h3>
                                        @foreach ($mostItems->take(3) as $article)
                                            <article class="kd-side-card">
                                                <a href="{{ $kdArticleUrl($article) }}" target="_blank">
                                                    <div class="{{ $kdImageClass($article, 'kd-side-image kd-mini-red') }}"
                                                        {!! $kdImageAttr($article) !!}>
                                                        <span class="kd-arrow"><svg>
                                                                <use href="#kd-arrow-right"></use>
                                                            </svg></span>
                                                    </div>
                                                    <div class="kd-side-copy">
                                                        <h4>{{ $kdPlainText($article->title ?? '', 40) }}</h4>
                                                        @if (!empty($article->dep_name))
                                                            <span class="kd-pill">{{ $article->dep_name }}</span>
                                                        @endif
                                                        <small>{{ $article->updated_at_format ?? '' }}</small>
                                                    </div>
                                                </a>
                                            </article>
                                        @endforeach
                                    </aside>
                                </section>

                                <div class="kd-red-rule"></div>
                                <section class="kd-section">
                                    <h2>{{ trans('tkt_knowledge_document.latest_articles') }}</h2>
                                    <hr>
                                    <div class="kd-latest-grid">
                                        @foreach ($latestItems->take(4) as $article)
                                            <article class="kd-stack">
                                                <a href="{{ $kdArticleUrl($article) }}" target="_blank">
                                                    <div class="{{ $kdImageClass($article, 'kd-card-image') }}"
                                                        {!! $kdImageAttr($article) !!}>
                                                        @if (empty($article->card_img))
                                                            <span class="kd-watermark">KD</span>
                                                        @endif
                                                        <div class="kd-overlay">
                                                            <h4 class="h4-text">{{ $kdPlainText($article->title ?? '', 34) }}</h4>
                                                            @if (!empty($article->dep_name))
                                                                <span
                                                                    class="kd-pill light">{{ $article->dep_name }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="kd-arrow"><svg>
                                                                <use href="#kd-arrow-right"></use>
                                                            </svg></span>
                                                    </div>
                                                </a>
                                            </article>
                                        @endforeach
                                    </div>

                                    <div class="kd-latest-mosaic">
                                        @foreach ($latestItems->slice(4, 5)->values() as $index => $article)
                                            <article class="{{ $index === 0 ? 'kd-wide' : 'kd-small' }}">
                                                <a href="{{ $kdArticleUrl($article) }}" target="_blank">
                                                    <div class="{{ $kdImageClass($article, 'kd-card-image') }}"
                                                        {!! $kdImageAttr($article) !!}>
                                                        @if (empty($article->card_img) && $index === 0)
                                                            <span class="kd-watermark large">KD</span>
                                                        @endif
                                                        <span class="kd-arrow"><svg>
                                                                <use href="#kd-arrow-right"></use>
                                                            </svg></span>
                                                    </div>
                                                    <div class="kd-meta kd-under-meta">
                                                        @if (!empty($article->dep_name))
                                                            <span class="kd-pill">{{ $article->dep_name }}</span>
                                                        @endif
                                                        <span>{{ $article->updated_at_format ?? '' }}</span>
                                                    </div>
                                                    <h4 class="h4-text">{{ $kdPlainText($article->title ?? '', 34) }}</h4>
                                                </a>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="kd-section kd-tags-section">
                                    <h2>{{ trans('tkt_knowledge_document.tags') }}</h2>
                                    <div class="kd-tags" id="kd-tags-list">
                                        @foreach ($tagItems->take(21) as $item)
                                            <button type="button" class="kd-chip tag-btn" data-id="{{ $item->id }}"
                                                id="{{ $item->id }}">{{ $item->tags }}</button>
                                        @endforeach
                                        @if ($tagItems->count() > 21)
                                            <div class="kd-more-tags">
                                                <button type="button" class="kd-chip more kd-more-tags-toggle" aria-expanded="false">
                                                    + {{ $tagItems->count() - 21 }} more
                                                </button>
                                                <div class="kd-tag-popover" role="menu">
                                                    @foreach ($tagItems->skip(21) as $item)
                                                        <button type="button" class="kd-chip tag-btn" data-id="{{ $item->id }}"
                                                            id="more-tag-{{ $item->id }}">{{ $item->tags }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="kd-tag-card-grid" id="kd-list-grid">
                                        @foreach ($latestItems->take(4) as $article)
                                            <article class="kd-stack">
                                                <a href="{{ $kdArticleUrl($article) }}" target="_blank">
                                                    <div class="{{ $kdImageClass($article, 'kd-card-image') }}"
                                                        {!! $kdImageAttr($article) !!}>
                                                        @if (empty($article->card_img))
                                                            <span class="kd-watermark">KD</span>
                                                        @endif
                                                        <div class="kd-overlay">
                                                            <h4>{{ $kdPlainText($article->title ?? '', 34) }}</h4>
                                                            @if (!empty($article->dep_name))
                                                                <span
                                                                    class="kd-pill light">{{ $article->dep_name }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="kd-arrow"><svg>
                                                                <use href="#kd-arrow-right"></use>
                                                            </svg></span>
                                                    </div>
                                                </a>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>

                                <button type="button" class="kd-load-more" id="kdLoadMoreBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Load More">
                                    <svg>
                                        <use href="#kd-arrow-down"></use>
                                    </svg>
                                </button>
                                <div class="kd-red-rule"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            @include('knowledge-document.create_modal')
        </div>
    </div>
@endsection
@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/flatpickr/flatpickr.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
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
            --chip-more-text: var(--text-icon);
            --field-bg: var(--dark-secondary);
            --field-border: var(--dark-border);
            --icon-muted: var(--text-icon);
            --hero-fallback: var(--dark-secondary);
        }
        #main-knowledge-document-wrapper {
            --page-bg: #fff;
            --arrow-surface: #fff;
        }

        #main-knowledge-document-wrapper * {
            box-sizing: border-box;
        }

        #main-knowledge-document-wrapper button,
        #main-knowledge-document-wrapper input {
            font: inherit;
        }

        #main-knowledge-document-wrapper a {
            color: inherit;
            text-decoration: none;
        }

        #main-knowledge-document-wrapper .header-actions-wrapper {
            margin-bottom: 0;
            /* background: var(--page-bg); */
        }

        #main-knowledge-document-wrapper .kd-header-actions {
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        #main-knowledge-document-wrapper .kd-shell-card {
            border: 0;
            background: var(--page-bg);
            box-shadow: none;
        }

        #main-knowledge-document-wrapper #kd-article-page.kd-api-pending {
            display: none;
        }

        #main-knowledge-document-wrapper .kd-list-toolbar {
            flex-wrap: wrap;
        }

        #main-knowledge-document-wrapper #api_loader:not(.hide) {
            min-height: 2.5rem;
        }

        #main-knowledge-document-wrapper #api_loader:not(.hide)::after {
            content: "";
            display: block;
            width: 1.75rem;
            height: 1.75rem;
            margin: 0.375rem auto 1rem;
            border: 0.1875rem solid var(--line);
            border-top-color: var(--red);
            border-radius: 50%;
            animation: kd-spin 0.8s linear infinite;
        }

        @keyframes kd-spin {
            to {
                transform: rotate(360deg);
            }
        }

        #main-knowledge-document-wrapper .kd-toolbar-search {
            /* width: 20rem; */
            max-width: 100%;
        }

        #main-knowledge-document-wrapper .amg-list-searchbar>.btn-searchbox {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: inherit;
        }

        #main-knowledge-document-wrapper .kd-filter-panel {
            margin: 0 1.125rem 1rem;
            padding: 1rem;
            border: 0.0625rem solid var(--line);
            border-radius: 0.5rem;
            background: var(--field-bg);
        }

        #main-knowledge-document-wrapper .kd-filter-panel label {
            display: block;
            margin-bottom: 0.375rem;
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 600;
        }

        #advanceFilterModal .modal-body {
            overflow: visible;
        }


        .daterangepicker {
            z-index: 1085;
        }

        [data-bs-theme="dark"] .daterangepicker select.monthselect,
        [data-bs-theme="dark"] .daterangepicker select.yearselect {
            background-color: var(--dark-tertiary);
            border-color: var(--dark-border);
            color: var(--text-primary);
        }

        .kd-page {
            width: 100%;
            max-width: 90rem;
            margin: 0 auto;
            padding: 0 1.125rem;
            overflow: hidden;
        }

        .kd-toolbar {
            display: grid;
            grid-template-columns: minmax(20rem, 1fr) minmax(26.25rem, 0.95fr);
            align-items: start;
            gap: 1.125rem;
            margin-bottom: 0.875rem;
        }

        .kd-chip-row,
        .kd-tags {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.375rem;
        }

        .kd-chip {
            min-height: 1.5625rem;
            padding: 0.1875rem 0.8125rem;
            border: 0.0625rem solid var(--chip-border);
            border-radius: 1.125rem;
            background: var(--chip-bg);
            color: var(--chip-text);
            font-size: 0.75rem;
            line-height: 1.25;
            cursor: default;
        }

        .kd-chip.more {
            border-color: transparent;
            background: transparent;
            color: var(--chip-more-text);
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            cursor: pointer;
        }

        .kd-more-tags {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .kd-tag-popover {
            position: absolute;
            left: 0;
            top: calc(100% + 0.5rem);
            z-index: 30;
            display: none;
            width: min(22rem, calc(100vw - 2rem));
            max-height: 15rem;
            overflow-y: auto;
            padding: 0.75rem;
            border: 0.0625rem solid var(--line);
            border-radius: 0.5rem;
            background: white;
            border: 1px solid #ddd;
            gap: 0.375rem;
            flex-wrap: wrap;
        }

        [data-bs-theme="dark"] .kd-tag-popover {
            background: #171616;
            border: 1px solid #141414;
            border-radius: 8px;
            padding: 8px;
            margin-top: 5px;
            min-width: 180px;
            max-width: 260px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .kd-more-tags.is-open .kd-tag-popover {
            display: flex;
        }

        .kd-tag-popover::before {
            position: absolute;
            content: "";
            top: -0.375rem;
            left: 1.25rem;
            width: 0.75rem;
            height: 0.75rem;
            border-left: 0.0625rem solid var(--line);
            border-top: 0.0625rem solid var(--line);
            background: var(--panel-bg);
            transform: rotate(45deg);
        }

      .department .btn,
            .tag-btn {
                min-height: 1.5625rem;
                padding: 0.1875rem 0.8125rem;
                border: 0.0625rem solid var(--chip-border);
                border-radius: 8px;
                color: var(--chip-text);
                font-size: 0.75rem;
                line-height: 1.25;
                background-color: #eeeeed;
            }

            [data-bs-theme="dark"] .department .btn,
            [data-bs-theme="dark"] .tag-btn {
                background-color: var(--chip-bg) !important;
            }
        .department .active_filter_btn,
        .kd-tags .active_filter_btn {
           border: 0.0625rem solid black !important;
        }
        [data-bs-theme="dark"] .department .active_filter_btn,
        [data-bs-theme="dark"] .kd-tags .active_filter_btn {
           border: 0.0625rem solid rgb(248, 246, 246) !important;
        }

        .kd-search {
            display: flex;
            justify-content: flex-end;
            min-width: 0;
        }

        .kd-search-group {
            display: grid;
            grid-template-columns: minmax(16.25rem, 1fr) 2.5rem 2.5rem 2.5rem 2.5rem;
            width: 100%;
            max-width: 43.75rem;
            height: 2.375rem;
            border: 0.0625rem solid var(--field-border);
            background: var(--field-bg);
        }

        .kd-search-group input {
            min-width: 0;
            border: 0;
            border-right: 0.0625rem solid var(--field-border);
            padding: 0 0.75rem;
            background: var(--field-bg);
            color: var(--muted);
            outline: none;
            font-size: 0.75rem;
        }

        .kd-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-right: 0.0625rem solid var(--field-border);
            background: var(--field-bg);
            color: var(--icon-muted);
        }

        .kd-icon-btn:last-child {
            border-right: 0;
        }

        .kd-icon {
            width: 1.125rem;
            height: 1.125rem;
            display: block;
            stroke: currentColor;
            stroke-width: 2.4;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        #main-knowledge-document-wrapper .kd-page h2,
        #main-knowledge-document-wrapper .kd-page h3,
        #main-knowledge-document-wrapper .kd-page h4,
        #main-knowledge-document-wrapper .kd-page p {
            margin-top: 0;
        }

        #main-knowledge-document-wrapper .kd-page h2 {
            margin-bottom: 0.6875rem;
            font-size: 1.6875rem;
            line-height: 1.1;
            font-weight: 800;
        }

        /* #main-knowledge-document-wrapper .kd-page h3 {
            font-size: 1.4375rem;
            line-height: 1.15;
            font-weight: 800;
        }

        #main-knowledge-document-wrapper .kd-page h4 {
            font-size: 1rem;
            line-height: 1.15;
            font-weight: 800;
        } */

        .kd-top-grid {
            display: grid;
            grid-template-columns: minmax(0, 2.08fr) minmax(20rem, 1fr);
            gap: 1.125rem;
        }

        .kd-card-image,
        .kd-side-image {
            position: relative;
            overflow: visible;
            border-radius: 1.125rem;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .kd-hero {
            min-height: 20.5rem;
            background-color: var(--hero-fallback);
        }

        .kd-hero::before {
            position: absolute;
            content: "";
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.22), rgba(255, 255, 255, 0.08));
            pointer-events: none;
        }

        .kd-meta {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 0.8125rem 0 0.625rem;
            color: var(--muted);
            font-size: 0.6875rem;
            line-height: 1.25;
        }

        .kd-feature h3 {
            margin-bottom: 0.4375rem;
        }

        .kd-feature p {
            color: var(--muted);
            font-size: 0.75rem;
            line-height: 1.3;
        }

        .kd-most {
            padding-top: 2.3125rem;
        }

        .kd-most h3 {
            margin-bottom: 0.5625rem;
        }

        .kd-side-card {
            margin-bottom: 0.9375rem;
        }

        .kd-side-card a {
            display: grid;
            grid-template-columns: minmax(9.375rem, 58%) 1fr;
            gap: 0.875rem;
            align-items: start;
        }

        .kd-side-image {
            min-height: 6.375rem;
            border-radius: 0.375rem;
        }

        .kd-side-copy {
            min-width: 0;
        }

        .kd-side-copy h4 {
            margin-bottom: 0.4375rem;
        }

        .kd-side-copy small {
            display: block;
            color: var(--muted);
            font-size: 0.6875rem;
        }

        .kd-pill {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            max-width: 100%;
            min-height: 1.0625rem;
            padding: 0.0625rem 0.5625rem;
            /* border: 0.0625rem solid var(--chip-border); */
            border: 0.0625rem solid black;
            border-radius: 4px;
            background: var(--chip-bg);
            color: var(--chip-text);
            font-size: 0.625rem;
            line-height: 1.3;
            white-space: nowrap;
        }

        .kd-pill.light {
            border-color: #fff;
            background: transparent;
            color: #fff;
            text-shadow: 0 0.0625rem 0.25rem rgba(0, 0, 0, 0.45);
            font-size: 0.625rem;
        }

        .kd-arrow {
            position: absolute;
            right: -0.875rem;
            bottom: -0.625rem;
            z-index: 5;
            width: 3.75rem;
            height: 3.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 1.125rem;
            background: var(--arrow-surface, #fff);
        }

        .kd-arrow::before,
        .kd-arrow::after {
            position: absolute;
            content: "";
            width: 1.375rem;
            height: 1.375rem;
            background: transparent;
            border-bottom-right-radius: 1.125rem;
            box-shadow: 0.4375rem 0.4375rem var(--arrow-surface, #fff);
        }

        .kd-arrow::before {
            left: -1.375rem;
            bottom: 0.625rem;
        }

        .kd-arrow::after {
            right: 0.875rem;
            top: -1.375rem;
        }

        .kd-arrow svg {
            position: relative;
            z-index: 1;
            width: 2.75rem;
            height: 2.5rem;
        }

        .kd-side-image .kd-arrow {
            right: -0.75rem;
            bottom: -0.5625rem;
            width: 2.625rem;
            height: 2.625rem;
            border-top-left-radius: 0.75rem;
        }

        .kd-side-image .kd-arrow::before,
        .kd-side-image .kd-arrow::after {
            width: 0.9375rem;
            height: 0.9375rem;
            border-bottom-right-radius: 0.6875rem;
            box-shadow: 0.3125rem 0.3125rem var(--arrow-surface, #fff);
        }

        .kd-side-image .kd-arrow::before {
            left: -0.9375rem;
            bottom: 0.5625rem;
        }

        .kd-side-image .kd-arrow::after {
            right: 0.75rem;
            top: -0.9375rem;
        }

        .kd-side-image .kd-arrow svg {
            width: 1.875rem;
            height: 1.75rem;
        }

        .kd-red-card {
            /* background:
                    radial-gradient(circle at 17% 0%, rgba(235, 42, 42, 0.42), transparent 30%),
                    linear-gradient(136deg, #b70000 0%, #720000 48%, #080101 100%); */
            background-image: url('{{ asset('imgs/kd_image.png') }}');
        }

        .kd-red-card::before {
            position: absolute;
            content: "";
            inset: 0;
            border-radius: inherit;
            opacity: 0.45;
            background:
                repeating-radial-gradient(ellipse at 5% 100%, transparent 0 0.9375rem, rgba(255, 255, 255, 0.34) 1rem 1.0625rem, transparent 1.125rem 1.875rem),
                linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.04));
            pointer-events: none;
        }

        .kd-mini-red::after {
            position: absolute;
            top: 0.25rem;
            right: 0.625rem;
            content: "KD";
            color: rgba(255, 255, 255, 0.35);
            font-size: 3.125rem;
            font-weight: 800;
            line-height: 1;
            writing-mode: vertical-rl;
        }

        .kd-watermark {
            position: absolute;
            top: 4.75rem;
            left: 0.3125rem;
            z-index: 1;
            color: rgba(255, 255, 255, 0.72);
            font-size: 6rem;
            font-weight: 800;
            line-height: 0.8;
            transform: rotate(270deg);
            pointer-events: none;
        }

        .kd-watermark.large {
            top: 6rem;
            /* left: 34%; */
            font-size: 8.375rem;
        }

        .kd-overlay {
            position: absolute;
            left: 1.25rem;
            right: 3.375rem;
            bottom: 1.375rem;
            z-index: 2;
            color: #fff;
        }

        .kd-overlay h4 {
            margin-bottom: 0.5rem;
            color: #fff;
            font-size: 1.125rem;
            text-shadow: 0 0.0625rem 0.25rem rgba(0, 0, 0, 0.45);
        }

        .kd-red-rule {
            position: relative;
            height: 0.375rem;
            margin: 1.125rem -1.125rem 1.1875rem;
            border-radius: 0 0.375rem 0.375rem 0;
            background: var(--red);
        }

        .kd-red-rule::after {
            position: absolute;
            top: 0;
            right: -0.875rem;
            content: "";
            border-top: 0.1875rem solid transparent;
            border-bottom: 0.1875rem solid transparent;
            border-left: 0.5625rem solid var(--red);
        }

        .kd-section h2 {
            margin-left: 0.375rem;
        }

        .kd-section hr {
            margin: 0 0.375rem 1.125rem;
            border: 0;
            border-top: 0.0625rem solid var(--line);
        }

        .kd-latest-grid,
        .kd-tag-card-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.125rem;
            padding: 0 0.375rem;
        }

        .kd-stack .kd-card-image {
            min-height: 24.5625rem;
        }

        .kd-latest-mosaic {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 0.47fr) minmax(0, 0.47fr);
            gap: 1.125rem;
            margin-top: 1.125rem;
            padding: 0 0 0 0;
            align-items: start;
        }

        .kd-wide {
            grid-row: span 2;
        }

        .kd-wide .kd-card-image {
            min-height: 20.8125rem;
        }

        .kd-small .kd-card-image {
            min-height: 8.625rem;
            border-radius: 0.5rem;
        }

        .kd-wide h4,
        .kd-small h4 {
            font-size: 1rem;
            margin: 0;
        }

        .kd-under-meta {
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            margin: 0.625rem 0 0.75rem;
        }

        .kd-tags-section {
            margin-top: 1.9375rem;
        }

        .kd-tags-section h2 {
            margin-bottom: 0.625rem;
        }

        .kd-tags {
            padding: 0 0.375rem;
            margin-bottom: 0.75rem;
        }

        .kd-tag-card-grid .kd-card-image {
            min-height: 22.5rem;
        }

        .kd-load-more {
            display: flex;
            justify-content: center;
            width: fit-content;
            margin: 2.125rem 0 0.9375rem;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            margin-left: auto;
            margin-right: auto;
        }

        .kd-load-more svg {
            width: 4rem;
            height: 3.5rem;
        }
        @media (max-width: 74.9375rem) {

            .kd-toolbar,
            .kd-top-grid {
                grid-template-columns: 1fr;
            }

            .kd-search {
                justify-content: flex-start;
            }

            #main-knowledge-document-wrapper .kd-list-toolbar {
                align-items: flex-start !important;
            }

            .kd-most {
                padding-top: 0;
            }

            .kd-latest-grid,
            .kd-tag-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .kd-latest-mosaic {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .kd-wide {
                grid-column: 1 / -1;
                grid-row: auto;
            }
        }

        @media (max-width: 47.9375rem) {
            .kd-page {
                padding: 0.625rem 0.75rem 0;
            }

            #main-knowledge-document-wrapper .header-actions-wrapper {
                align-items: flex-start !important;
                gap: 0.75rem;
                flex-wrap: wrap;
                padding-right: 0.75rem !important;
            }

            #main-knowledge-document-wrapper .kd-list-toolbar {
                gap: 0.625rem !important;
            }

            #main-knowledge-document-wrapper .kd-toolbar-search,
            #main-knowledge-document-wrapper .amg-refresh-btn {
                width: 100%;
            }

            #main-knowledge-document-wrapper .kd-page h2 {
                font-size: 1.4375rem;
            }

            .kd-search-group {
                grid-template-columns: minmax(10rem, 1fr) 2.25rem 2.25rem 2.25rem 2.25rem;
                height: 2.25rem;
            }

            .kd-top-grid,
            .kd-latest-grid,
            .kd-latest-mosaic,
            .kd-tag-card-grid {
                grid-template-columns: 1fr;
            }

            .kd-side-card a {
                grid-template-columns: 44% 1fr;
            }

            .kd-hero {
                min-height: 14.6875rem;
            }

            .kd-stack .kd-card-image,
            .kd-tag-card-grid .kd-card-image {
                min-height: 17.8125rem;
            }

            .kd-wide .kd-card-image {
                min-height: 14.375rem;
            }

            .kd-small .kd-card-image {
                min-height: 9.6875rem;
            }

            .kd-red-rule {
                margin-left: -0.75rem;
                margin-right: -0.75rem;
            }
        }

        @media (max-width: 30rem) {
            .kd-side-card a {
                grid-template-columns: 1fr;
            }

            .kd-side-image {
                min-height: 9.0625rem;
            }

            .kd-meta {
                flex-direction: column;
                gap: 0.25rem;
            }
        }      
        /* Bottom overlay effect */
        .kd-stack .kd-card-image::after{
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background:
                linear-gradient(
                    to top,
                    rgba(0,0,0,0.88) 0%,
                    rgba(9, 0, 0, 0.55) 25%,
                    rgba(0,0,0,0.15) 55%,
                    rgba(0,0,0,0) 75%
                );
            z-index: 1;
        }

        /* Optional red glow at bottom */
        .kd-stack .kd-card-image::before{
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 120px;
            z-index: 1;
        }

        .amg-list-searchbar{
            border: none;
        }
        /* a:hover h3 {
            color: rgb(10, 10, 10) !important;
        } */
         .kd-more-departments {
            position: relative;
            display: inline-block;
        }

        .kd-department-popover {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            margin-top: 5px;
            min-width: 310px;
            max-width: 350px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        [data-bs-theme="dark"] .kd-department-popover {
            background: #171616;
            border: 1px solid #141414;
            border-radius: 8px;
            padding: 8px;
            margin-top: 5px;
            min-width: 180px;
            max-width: 260px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .kd-more-departments.is-open .kd-department-popover {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* .kd-department-popover .department-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
        } */
        body .dropdown-fontsize {
            max-height: 200px !important;
            overflow-y: auto !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript">
        if (window.jQuery && !jQuery.fn.pagination) {
            jQuery.fn.pagination = function(methodOrOptions, value) {
                if (typeof methodOrOptions === 'string') {
                    if (methodOrOptions === 'getCurrentPage') {
                        return this.data('currentPage') || 1;
                    }
                    if (methodOrOptions === 'drawPage') {
                        this.data('currentPage', value || 1);
                    }
                    if (methodOrOptions === 'updateItems') {
                        this.data('totalItems', value || 0);
                    }
                    return this;
                }
                this.data('currentPage', 1);
                this.data('paginationOptions', methodOrOptions || {});
                return this;
            };
        }
    </script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script src="{{ CommonHelper::asset('assets/js/plugins/moment.min.js') }}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/lord-icon/lord-icon-2.0.2.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/knowledge-document/document.js') !!}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.placeholder = {};
            config.title = {};
            config.url.site_url = "{{ url('/') }}";
            config.url.create_document = "{{ url('knowledge_document/document') }}";
            config.url.document = "{{ url('knowledge_document/document/create') }}";
            config.url.delete = "{{ url('knowledge_document/document/delete') }}";
            config.url.edit = "{{ url('knowledge_document/document/edit') }}";
            config.url.update = "{{ url('knowledge_document/document/update') }}";
            config.url.tag = "{{ url('knowledge_document/document/tag') }}";
            config.url.create_category = "{{ url('knowledge_document/document/category') }}";
            config.url.delete_category = "{{ url('knowledge_document/document/delete_category') }}";
            config.url.edit_category = "{{ url('knowledge_document/document/edit_category') }}";
            config.url.update_category = "{{ url('knowledge_document/document/update_category') }}";
            config.url.getTagDetails = "{{ url('knowledge_document/document/getTagDetails') }}";
            config.url.create_parent_category = "{{ url('knowledge_document/document/parentcategory') }}";
            config.url.delete_parent_category = "{{ url('knowledge_document/document/delete_parent_category') }}";
            config.url.edit_parent_category = "{{ url('knowledge_document/document/edit_parent_category') }}";
            config.url.update_parent_category = "{{ url('knowledge_document/document/update_parent_category') }}";
            config.url.document_list = "{{ url('knowledge_document/document/jx-document-list') }}";
            config.url.article_list = "{{ url('knowledge_document/document/jx-article-list') }}";
            config.url.category_list = "{{ url('knowledge_document/document/jx-category-list') }}";
            config.url.view = "{{ url('knowledge_document/document/view') }}";
            config.url.view_article = "{{ url('knowledge_document/article/view') }}";
            config.url.articleImagePath = "{{ url('uploads/article') }}";
            config.url.articleImageNull = "{{ url('uploads/article/devicebg.png') }}";
            config.url.kd_attachment_add = "{{ url('knowledge_document/attachment/kd_attachment') }}";
            config.url.kd_attachment_remove = "{{ url('knowledge_document/attachment/kd_attachment_remove') }}";
            config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage') }}";
            // config.url.departments_with_company = "{{ url('getDepartmentsWithCompanyByQuery') }}";
            config.url.departments_with_company = "{{ url('tickets/departments') }}";
            config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
            config.url.problem_categories_by_dept = "{{ url('kd/problem-categories/by-dept') }}";
            config.url.problem_categories = "{{ url('kd/problem-categories') }}";
            config.current_id = {!! json_encode(Auth::user()->id) !!};
            config.permissions = {!! json_encode($permissionArray) !!};
            config.tag_id = "{{ isset($_GET['tag_id']) ? $_GET['tag_id'] : '' }}";
            config.url.staring = "{{ url('knowledge_document/article/staring') }}";
            config.url.fetch_category_by_ajax = "{{ url('knowledge_document/fetch_category_by_ajax') }}";
            config.url.fetch_subcategory_by_ajax = "{{ url('knowledge_document/fetch_subcategory_by_ajax') }}";
            config.translations = {
                Edit_Article: '{{ trans('content.knowledge_document.Edit_Article') }}',
                Edit_Category: '{{ trans('content.knowledge_document.Edit_Category') }}',
                are_you_want_Delete: '{{ trans('content.knowledge_document.are_you_want_Delete') }}',
                are_you_want_Delete_cat: '{{ trans('content.knowledge_document.are_you_want_Delete_cat') }}',
                Available_Records: '{{ trans('content.knowledge_document.Available_Records') }}',
                No_records_Found: '{{ trans('content.knowledge_document.No_records_Found') }}',
                Read_More: '{{ trans('content.knowledge_document.Read_More') }}',
                Reload: '{{ trans('content.user_fields.Reload') }}',
                filter_by_category: '{{ trans('tkt_knowledge_document.filter_by_category') }}',
                filter_by_department: '{{ trans('tkt_knowledge_document.filter_by_department') }}',
                Filter_By_Problem_Category: '{{ trans('tkt_knowledge_document.filter_by_problem_category') }}',
                Filter_By_Sub_Category: '{{ trans('tkt_knowledge_document.filter_by_sub_category') }}',
                No_Filter: '{{ trans('tkt_knowledge_document.no_filter') }}',
                No_ParentCategory: '{{ trans('content.knowledge_document.No_ParentCategory') }}',
                select_the_department: '{{ trans('content.knowledge_document.select_the_department') }}',
                Select_Problem_Category: '{{ trans('content.knowledge_document.Select_Problem_Category') }}',
                Select_Sub_Category: '{{ trans('content.knowledge_document.Select_Sub_Category') }}',
                connect: '{{ trans('content.knowledge_document.connect') }}',
                are_you_star: '{{ trans('content.service_ticket_fields.are_you_star') }}',
                serach_option: '{{ trans('content.service_ticket_fields.serach_option') }}',
                tag: '{{ trans('tkt_knowledge_document.filter_by_tag') }}',
                Article_delete: '{{ trans('content.knowledge_document.article_delete') }}',
                article_star: '{{ trans('content.knowledge_document.article_star') }}',
                filter_article_star: '{{ trans('tkt_knowledge_document.filter_by_star') }}',
                filter_article_status: '{{ trans('tkt_knowledge_document.filter_article_status') }}',
                filter_by_date: '{{ trans('tkt_knowledge_document.filter_by_date') }}',
                popular_articles: '{{ trans('tkt_knowledge_document.popular_articles') }}',
                most_viewed: '{{ trans('tkt_knowledge_document.most_viewed') }}',
                latest_articles: '{{ trans('tkt_knowledge_document.latest_articles') }}',
                tags: '{{ trans('tkt_knowledge_document.tags') }}',
                Parent_category: '{{ trans('content.service_ticket_fields.Select_Parent_Category') }}',
                Sub_Category: '{{ trans('content.service_ticket_fields.Select_Sub_Category') }}',
                upload_file: '{{ trans('content.service_ticket_fields.upload_file') }}',
                something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                select_company: '{{ trans('config.kd_category_fields.mdl_company') }}',
                select_department: '{{ trans('content.knowledge_document.select_department') }}',
                select_sub_catgeory: '{{ trans('content.knowledge_document.select_sub_catgeory') }}',
                select_status: '{{ trans('content.knowledge_document.select_status') }}',
                Enter_content: '{{ trans('content.knowledge_document.Enter_content') }}',
                filter_by_company: '{{ trans('content.filter_heading.filter_by_company') }}',
                view: '{{ trans('config.kd_category_fields.view') }}',
                load_more: '{{ trans('config.kd_category_fields.load_more') }}',
            };
            config.token = "{{ csrf_token() }}";
            config.permissions = {!! json_encode($permissionArray) !!};
            config.category = {!! json_encode($category) !!};
            config.tag = {!! json_encode($tag) !!};
            config.user = {!! json_encode(Auth::user()->only('company_id')) !!};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': config.token
                }
            });
            new MyApp(config);
        });
    </script>
@endpush
