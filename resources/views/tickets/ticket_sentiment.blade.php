<div class="backdrop" id="modalSentiment" style="display: none;">
    <div class="sentiment-modal">
        <div class="modal-header-custom">
            <h4>Customer Sentiment Analysis</h4>
            <button class="btn-close close-sentimate">&times;</button>
        </div>
        
        <div class="modal-body-custom">
            <div class="sentiment-badge" id="sentimentBadge">
                <div class="sentiment-icon-wrapper">
                    <div class="sentiment-icon" id="sentimentIcon">😟</div>
                    <div class="sentiment-text" id="sentimentText">No Sentiment Detected</div>
                </div>

                <div class="sentimate-main-container">
                <div class="sentiment-bar-container">
                    <div class="sentiment-indicator" id="sentimentIndicator"></div>
                </div>
                <div class="sentiment-score" id="sentimentScore">Score: 0 (None)</div>
                </div>
            </div>
            
            <div class="explanation-section">
                <div class="explanation-header">
                    <div class="info-icon">i</div>
                    <h5 class="explanation-title">Explanation:</h5>
                </div>
                <div class="explanation-content">
                    <div class="bullet-icon">i</div>
                    <div id="explanationText">
                        Detected strong frustration in the message with phrases like "still no response" and "very upset."
                    </div>
                </div>
            </div>

            {{--<div class="explanation-section">
                <div class="explanation-header">
                    <div class="info-icon">i</div>
                    <h5 class="explanation-title">Action Taken:</h5>
                </div>
                <div class="explanation-content">
                    <div class="bullet-icon">i</div>
                    <div id="explanationText">
                        Detected strong frustration in the message with phrases like "still no response" and "very upset."
                    </div>
                </div>
            </div>--}}

        </div>
        
        <div class="modal-footer-custom">
            <button class="btn btn-custom btn-close close-sentimate">Close</button>
            <!-- <button class="btn btn-custom btn-escalate" onclick="escalateTicket()">Escalate Ticket</button> -->
        </div>
    </div>
</div>
