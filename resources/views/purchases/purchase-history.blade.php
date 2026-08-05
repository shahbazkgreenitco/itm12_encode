@php
    $hisObj = json_decode($history->history, false);
@endphp
<style>
    .history-timeline {
        max-height: 65vh;
        overflow-y: auto;
        padding-right: 6px;
    }

    .history-timeline-item {
        display: flex;
        gap: 16px;
        margin-bottom: 22px;
    }

    .history-icon-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ICON CIRCLE */
    .history-timeline-icon {
        width: 40px;
        height: 40px;
        background: #f1f3f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    /* SVG SIZE */
    .timeline-svg {
        width: 18px;
        height: 18px;
    }

    /* VERTICAL LINE */
    .history-timeline-line {
        width: 2px;
        flex: 1;
        background: #dee2e6;
        margin-top: 6px;
    }

    .card-style {
        flex: 1;
        padding: 14px 16px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        position: relative;
        transition: all 0.2s ease;
    }

    /* RED LEFT BORDER */
    .card-style::before {
        content: "";
        position: absolute;
        left: 0;
        top: 10px;
        bottom: 10px;
        width: 4px;
        background: #dc3545;
        border-radius: 4px;
    }

    /* HOVER EFFECT */
    .card-style:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .history-detail-info-time-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #868e96;
        margin-bottom: 6px;
    }

    .history-detail-info-modified-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    /* AVATAR */
    .history-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
    }

    /* USER NAME */
    .history-user-name {
        font-weight: 600;
        font-size: 14px;
        color: #212529;
    }

    /* ACTION TEXT (BLUE) */
    .history-action {
        font-size: 13px;
        color: #0d6efd;
        cursor: pointer;
    }

    .history-detail-info-changed {
        font-size: 13px;
        color: #495057;
    }

    .history-detail-info-changed b {
        font-weight: 600;
        color: #212529;
    }

    .accordion-toggle {
        cursor: pointer;
        color: #adb5bd;
        transition: 0.2s;
    }

    .accordion-toggle:hover {
        color: #495057;
    }

    .accordion-content {
        display: none;
        margin-top: 10px;
    }

    /* NOTES BOX */
    .history-notes {
        background: #f1f3f5;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
    }

    .history-timeline::-webkit-scrollbar {
        width: 6px;
    }

    .history-timeline::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 10px;
    }

    .history-timeline::-webkit-scrollbar-track {
        background: transparent;
    }
</style>



<div class="history-timeline">

    @foreach($hisObj as $en)

        @php
            if (is_string($en)) {
                $en = json_decode($en);
            }

            $user = \App\Models\User::find($en->user_id);
            $date = \Carbon\Carbon::parse($en->entry_at ?? now());
        @endphp

        <div class="history-timeline-item">

            <!-- LEFT ICON -->
            <div class="history-icon-container">
                {{-- <div class="history-timeline-icon">
                    <i class="bi bi-clock-history"></i>
                </div> --}}
                <div class="history-timeline-icon">
                    <svg class="timeline-svg" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path
                                d="M7.69591 13.9222L5.94103 13.9222C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14412 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.0725 8.14014"
                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />

                            <path d="M7.17251 10.6118H3.89835" stroke="currentColor" stroke-width="1.2"
                                stroke-linecap="round" />

                            <path d="M0.624176 5.61816H16.9949" stroke="currentColor" stroke-width="1.2"
                                stroke-linecap="round" />

                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.7869 9.48873C16.0401 9.38536 16.3292 9.5068 16.4325 9.75996C17.2594 11.7853 16.2879 14.0975 14.2626 14.9244C13.0936 15.4017 11.8287 15.2793 10.8106 14.7025C10.5727 14.5677 10.4891 14.2655 10.6239 14.0276C10.7587 13.7897 11.0608 13.7061 11.2987 13.8409C12.0643 14.2747 13.0119 14.3654 13.8883 14.0076C15.4072 13.3874 16.1359 11.6533 15.5157 10.1343C15.4123 9.88111 15.5338 9.59209 15.7869 9.48873Z"
                                fill="currentColor" />
                        </g>
                    </svg>
                </div>
                <div class="history-timeline-line"></div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="history-detail-info card-style">

                <!-- TIME + TOGGLE -->
                <div class="history-detail-info-time-row">
                    <span>
                        <i class="bi bi-clock"></i>
                        {{ $date->format('d M Y h:i A') }}
                    </span>

                    <span class="accordion-toggle">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </div>

                <!-- USER -->
                <div class="history-detail-info-modified-status">
                    <div class="d-flex align-items-center gap-2">
                        <img src="https://itassetmanagementsoftware.com/rolepermission/imgs/profile-75.jpg"
                            class="history-avatar">

                        {{-- <strong>
                            {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                        </strong> --}}
                        <span class="history-user-name">
                            {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                        </span>
                    </div>

                    {{-- <span>•</span> --}}

                    {{-- <span class="text-muted">
                        {{ $en->action_detail ?? 'Updated' }}
                    </span> --}}
                    <span class="history-action">
                        • {{ $en->action_detail ?? 'Updated' }}
                    </span>
                </div>

                <!-- CHANGE -->
                <div class="history-detail-info-changed">

                    @if(!empty($en->invoice_no))
                        <div>Invoice → <b>{{ $en->invoice_no }}</b></div>
                    @endif

                    @if(!empty($en->supplier))
                        <div>Supplier → <b>{{ $en->supplier }}</b></div>
                    @endif

                    @if(!empty($en->qty))
                        <div>Qty → <b>{{ $en->qty }}</b></div>
                    @endif

                    @if(!empty($en->bill_amount))
                        <div>Amount → <b>{{ $en->bill_amount }}</b></div>
                    @endif

                </div>

                <!-- EXPAND -->
                <div class="accordion-content">
                    @if(!empty($en->notes))
                        <div class="history-notes">
                            {{ $en->notes }}
                        </div>
                    @endif
                </div>

            </div>

        </div>

    @endforeach

</div>

<script>
    $(document).on("click", ".accordion-toggle", function () {

        let card = $(this).closest(".card-style");
        let content = card.find(".accordion-content");
        let icon = $(this).find("i");

        $(".accordion-content").not(content).slideUp(150);
        $(".accordion-toggle i").not(icon)
            .removeClass("bi-chevron-up")
            .addClass("bi-chevron-down");

        content.slideToggle(150);

        icon.toggleClass("bi-chevron-down bi-chevron-up");
    });

    $(document).ready(function () {
        let first = $(".history-timeline-item").first();

        first.find(".accordion-content").show();
        first.find(".accordion-toggle i")
            .removeClass("bi-chevron-down")
            .addClass("bi-chevron-up");
    });
</script>