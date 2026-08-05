<div class="modal fade" id="userSearchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('user.user_filter.search_label') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="filter_search" class="form-control filter-input" placeholder="{{ trans('user.user_filter.search_placeholder') }}">
            </div>
            <div class="modal-footer justify-content-start gap-2">
                <button type="button" class="amg-btn amg-btn-primary px-5 btn-search-apply">{{ trans('user.user_filter.apply') }}</button>
                <button type="button" class="amg-btn amg-btn-ghost bg-black text-white hover-opacity-80 px-4 btn-search-clear">{{ trans('user.user_filter.clear') }}</button>
            </div>
        </div>
    </div>
</div>
