{{-- @page-meta
{
"page_no": "TKTS-05-26",
"file": "sentiment_analysis.blade.php",
"versions": [
{
"version": "1.0",
"writer": "Shivam Kumar",
"from": "2026-05",
"reviewer": null,
"description": "Initial setup – ticket sentiment modal"
}
]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="ticketSentiment" tabindex="-1"
    aria-labelledby="ticketSentimentLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content sentiment-modal border-0 overflow-hidden">

            <!-- Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h3 class="modal-title sentiment-title">
                    {{ trans('ticket.update_status.customer_sentiment_analysis') }}
                </h3>

                <button type="button" data-bs-dismiss="modal" class="btn-close shadow-none"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 pb-4">

                <!-- Sentiment Status -->
                <!-- STATUS -->
                <div class="sentiment-status text-center">

                    <div class="emoji-wrap" id="sentimentIcon">
                        😡
                    </div>

                    <h4 class="negative-text mt-3 mb-0" id="sentimentText">
                        {{ trans('ticket.update_status.negative_sentiment_detected') }} 
                    </h4>

                    <p class="sentiment-sub-text" id="sentimentSubText">
                        {{ trans('ticket.update_status.customer_message_reflects') }} 
                    </p>
                </div>

                <!-- Score Card -->
                <div class="score-card">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                        <div class="score-left">
                            <span class="score-label">{{ trans('ticket.update_status.sentiment_score') }} </span>

                            <div class="score-value" id="scoreValue">
                                -0.99
                            </div>
                        </div>

                        <div class="score-badge negative" id="scoreBadge">
                            {{ trans('ticket.update_status.strong_negative') }}
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="progress-wrapper">

                        <div class="sentiment-progress">

                            <div class="progress-indicator" id="sentimentIndicator"></div>

                        </div>
        
                        <div class="progress-labels">
                            <span class="negative-label">{{ trans('ticket.update_status.negative') }}</span>
                            <span class="neutral-label">{{ trans('ticket.update_status.neutral') }}</span>
                            <span class="positive-label">{{ trans('ticket.update_status.positive') }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="score-footer">
                        <span id="sentimentScore">
                            Score: -0.99
                        </span>

                        <span class="footer-status" id="footerStatus">
                            {{ trans('ticket.update_status.very_unfavorable') }}
                        </span>
                    </div>

                </div>

                <!-- Explanation -->
                <div class="explanation-card">

                    <div class="section-title">
                        <div class="icon-circle">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <span>{{ trans('ticket.update_status.explanation') }}</span>
                    </div>

                    <div class="explanation-content" id="explanationText">
                        {{ trans('ticket.update_status.explanation_here') }}
                    </div>

                </div>

                <!-- Tone -->
                <div class="tone-card">

                    <div class="tone-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div class="tone-content">

                        <div class="tone-title">
                            {{ trans('ticket.update_status.overall_tone') }}
                        </div>

                        <div class="tone-description" id="takenAction">
                            {{ trans('ticket.update_status.hostile_frustrated_esclated') }}
                        </div>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center">

                <button type="button" class="btn sentiment-close-btn" data-bs-dismiss="modal">
                    {{ trans('ticket.update_status.close') }}
                </button>

            </div>

        </div>
    </div>
</div>