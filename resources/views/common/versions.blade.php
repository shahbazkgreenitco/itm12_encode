@extends('layouts.layout1')
@section('title', 'Version Details')
@section('content')
    <style>
        .version-accordion {
            border: 1px solid #d8dde3;
        }
        .version-item {
            border: 0;
            border-bottom: 1px solid #d8dde3;
        }
        .version-header {
            background: #f5f7fa;
            font-weight: 600;
            font-size: 18px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .version-header:not(.collapsed) {
            background: #eef2f6;
        }
        .version-number {
            color: #2b3a4b;
        }
        .version-toggle {
            width: 26px;
            height: 26px;
            border: 1px solid #cfd6dd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
        .accordion-button::after {
            display: none;
        }
        .version-body {
            background: #fafbfd;
            padding: 20px 25px;
        }
        .version-list {
            padding-left: 20px;
        }
        .version-list li {
            margin-bottom: 6px;
            font-size: 14px;
        }
    </style>
    <!-- section header -->
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">Vesions Deatils</h3>
    </div>
    <main class="main-content">
        <div class="version-accordion accordion" id="versionAccordion">
            @foreach ($versionArray as $index => $version)
                <div class="accordion-item version-item" id="versions">
                    <h2 class="accordion-header">
                        <button class="accordion-button version-header {{ $index != 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#generalAccordian{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                            <span class="version-number">
                                {{ $version->title }}
                            </span>
                            <span class="version-toggle">
                                <i class="version-plus d-flex justify-content-center align-items-center">+</i>
                            </span>
                        </button>
                    </h2>
                    <div id="generalAccordian{{ $index }}"
                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                        data-bs-parent="#versionAccordion">
                        <div class="accordion-body version-body">
                            <ul class="version-list">
                                @foreach ($version->events as $t)
                                    <li>{{ $t }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.accordion-collapse').removeClass('show');
            $('.accordion-button').addClass('collapsed');
            $('.accordion-collapse').on('show.bs.collapse', function() {
                let btn = $(this).prev('.accordion-header').find('.accordion-button');
                let icon = btn.find('.version-plus');
                icon.removeClass('rotate-minus').addClass('rotate-plus');
                setTimeout(function() {
                    icon.text('−');
                }, 150);
            });
            $('.accordion-collapse').on('hide.bs.collapse', function() {
                let btn = $(this).prev('.accordion-header').find('.accordion-button');
                let icon = btn.find('.version-plus');
                icon.removeClass('rotate-plus').addClass('rotate-minus');
                setTimeout(function() {
                    icon.text('+');
                }, 150);

            });
        });
    </script>
@endpush
