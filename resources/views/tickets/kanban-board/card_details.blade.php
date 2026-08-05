<dialog id="cardDialog">
    <div class="dialog-content">
        <div class="card-header">
            <span class="timezone card-id"></span>
            <div class="header-actions">
                <button class="icon-btn card-details-close" title="Close">
                    <svg fill="currentColor" width="18px" height="18px" viewBox="0 0 24 24" id="cross" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                        <path id="primary" d="M19,19,5,5M19,5,5,19" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Title Section -->
            <div id="title-section" class="section title-section">
                <h1 class="card-title" style="margin-top: 0px;"></h1>
                <div class="card-meta">
                    <span id="card-priority" class="priority-badge priority-high"></span>
                    <span id="card-status-dot">•</span>
                    <span id="card-status"></span>
                    <span id="due-date-dot">•</span>
                    <span class="due-date"></span>
                </div>
            </div>

            <!-- Content Section -->
            <div id="content-section" class="section">
                <h2 class="content-title">Description</h2>
                <p class="content-text" style="margin: 0px;"></p>
            </div>

            <!-- Last Updated Section -->
            <div id="last-updated-section" class="section">
                <h2 class="content-title">Last Updated By</h2>
                <div class="update-info">
                    <div class="avatar"></div>
                    <div class="update-details">
                        <div class="update-name"></div>
                        <div class="update-time"></div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div id="comment-section" class="section">
                <h2 class="content-title">Last Comment</h2>
                <div class="comments-list"></div>
            </div>


            <!-- Attachments Section -->
            <div id="attachment-section" class="section">
                <h2 class="content-title">Attachments</h2>
                <div class="attachments-list">
                </div>
            </div>
            <div id="no-data-section" style="display:none; padding:10px; text-align:center; color:#777;">No data available</div>
        </div>
    </div>
</dialog>