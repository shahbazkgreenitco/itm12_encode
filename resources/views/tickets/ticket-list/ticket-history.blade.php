{{-- @page-meta
{
  "page_no": "TKT01L-04",
  "file": "ticket-history.blade.php",
  "versions": [
    {
      "version": "1.2",
      "writer": "Priya Maru",
      "from": "2026-05",
      "reviewer": null,
      "description": "Added Design for Ticket History"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal modal-lg fade" id="ticketHistoryModal" tabindex="-1"
    aria-labelledby="ticketHistoryModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog">
        <form id="ticket-history-mdl-frm" class="form-horizontal w-100 amg-form-theme" autocomplete="off"
            onsubmit="return false;">
            @csrf
            <input type="hidden" name="tab_action" value="2">
            <input type="hidden" name="id" id="editid">
            <div class="modal-content rounded-5">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s2-text fw-semibold px-2" id="modalTitle">{{ trans('ticket.ticket_history.ticket_history') }}</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-2" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body px-0">
                    <div class="container p-4 py-2" style="max-width: 760px;">

                        <!-- Header card -->
                        <div class="header-card d-flex flex-column flex-sm-row mb-0">
                            <div class="header-left py-2 px-3 d-flex flex-column gap-1">
                                <div class="lbl">
                                    <svg width="13" height="13" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.25 8.00235C0.315661 8.0516 0.390379 8.08743 0.469887 8.1078C0.549395 8.12817 0.632137 8.13268 0.713388 8.12107C0.794639 8.10946 0.872809 8.08197 0.943433 8.04015C1.01406 7.99833 1.07575 7.94301 1.125 7.87735C1.50341 7.37281 1.99409 6.96329 2.55819 6.68124C3.12229 6.39919 3.74432 6.25235 4.375 6.25235C5.00568 6.25235 5.6277 6.39919 6.1918 6.68124C6.7559 6.96329 7.24659 7.37281 7.625 7.87735C7.72457 8.00984 7.87269 8.09735 8.03678 8.12065C8.20086 8.14394 8.36749 8.10111 8.5 8.00157C8.54724 7.96644 8.58909 7.92459 8.62422 7.87735C9.00263 7.37281 9.49331 6.96329 10.0574 6.68124C10.6215 6.39919 11.2435 6.25235 11.8742 6.25235C12.5049 6.25235 13.1269 6.39919 13.691 6.68124C14.2551 6.96329 14.7458 7.37281 15.1242 7.87735C15.2238 8.00996 15.3719 8.09759 15.5361 8.12096C15.7003 8.14433 15.867 8.10152 15.9996 8.00196C16.1322 7.9024 16.2198 7.75424 16.2432 7.59007C16.2666 7.42591 16.2238 7.25918 16.1242 7.12657C15.571 6.3849 14.8333 5.80097 13.9844 5.43282C14.4498 5.00793 14.7758 4.45227 14.9197 3.83876C15.0637 3.22525 15.0188 2.58255 14.7909 1.99503C14.563 1.4075 14.1629 0.902585 13.6429 0.546552C13.123 0.190518 12.5075 0 11.8773 0C11.2472 0 10.6317 0.190518 10.1118 0.546552C9.59182 0.902585 9.19165 1.4075 8.96379 1.99503C8.73593 2.58255 8.69101 3.22525 8.83495 3.83876C8.97888 4.45227 9.30493 5.00793 9.77031 5.43282C9.15738 5.69786 8.60057 6.07729 8.12969 6.55079C7.6588 6.07729 7.102 5.69786 6.48906 5.43282C6.95444 5.00793 7.28049 4.45227 7.42443 3.83876C7.56836 3.22525 7.52345 2.58255 7.29558 1.99503C7.06772 1.4075 6.66755 0.902585 6.1476 0.546552C5.62764 0.190518 5.0122 0 4.38203 0C3.75186 0 3.13642 0.190518 2.61646 0.546552C2.09651 0.902585 1.69634 1.4075 1.46848 1.99503C1.24062 2.58255 1.1957 3.22525 1.33964 3.83876C1.48357 4.45227 1.80962 5.00793 2.275 5.43282C1.42238 5.79977 0.681016 6.38408 0.125 7.12735C0.0757543 7.19301 0.0399237 7.26773 0.0195539 7.34724C-0.000815824 7.42675 -0.00532572 7.50949 0.0062816 7.59074C0.0178889 7.67199 0.0453863 7.75016 0.0872035 7.82079C0.129021 7.89141 0.184339 7.95311 0.25 8.00235ZM11.875 1.25235C12.2458 1.25235 12.6083 1.36232 12.9167 1.56835C13.225 1.77438 13.4654 2.06721 13.6073 2.40982C13.7492 2.75243 13.7863 3.12943 13.714 3.49315C13.6416 3.85686 13.463 4.19096 13.2008 4.45318C12.9386 4.7154 12.6045 4.89398 12.2408 4.96633C11.8771 5.03867 11.5001 5.00154 11.1575 4.85963C10.8149 4.71771 10.522 4.47739 10.316 4.16905C10.11 3.86071 10 3.49819 10 3.12735C10 2.63007 10.1975 2.15316 10.5492 1.80153C10.9008 1.4499 11.3777 1.25235 11.875 1.25235ZM4.375 1.25235C4.74584 1.25235 5.10835 1.36232 5.41669 1.56835C5.72504 1.77438 5.96536 2.06721 6.10727 2.40982C6.24919 2.75243 6.28632 3.12943 6.21397 3.49315C6.14162 3.85686 5.96305 4.19096 5.70082 4.45318C5.4386 4.7154 5.10451 4.89398 4.74079 4.96633C4.37708 5.03867 4.00008 5.00154 3.65747 4.85963C3.31486 4.71771 3.02202 4.47739 2.81599 4.16905C2.60997 3.86071 2.5 3.49819 2.5 3.12735C2.5 2.63007 2.69754 2.15316 3.04917 1.80153C3.4008 1.4499 3.87772 1.25235 4.375 1.25235ZM13.9844 13.5578C14.4498 13.1329 14.7758 12.5773 14.9197 11.9638C15.0637 11.3502 15.0188 10.7076 14.7909 10.12C14.563 9.5325 14.1629 9.02758 13.6429 8.67155C13.123 8.31552 12.5075 8.125 11.8773 8.125C11.2472 8.125 10.6317 8.31552 10.1118 8.67155C9.59182 9.02758 9.19165 9.5325 8.96379 10.12C8.73593 10.7076 8.69101 11.3502 8.83495 11.9638C8.97888 12.5773 9.30493 13.1329 9.77031 13.5578C9.15738 13.8229 8.60057 14.2023 8.12969 14.6758C7.6588 14.2023 7.102 13.8229 6.48906 13.5578C6.95444 13.1329 7.28049 12.5773 7.42443 11.9638C7.56836 11.3502 7.52345 10.7076 7.29558 10.12C7.06772 9.5325 6.66755 9.02758 6.1476 8.67155C5.62764 8.31552 5.0122 8.125 4.38203 8.125C3.75186 8.125 3.13642 8.31552 2.61646 8.67155C2.09651 9.02758 1.69634 9.5325 1.46848 10.12C1.24062 10.7076 1.1957 11.3502 1.33964 11.9638C1.48357 12.5773 1.80962 13.1329 2.275 13.5578C1.42238 13.9248 0.681016 14.5091 0.125 15.2523C0.0757543 15.318 0.0399237 15.3927 0.0195539 15.4722C-0.000815824 15.5517 -0.00532572 15.6345 0.0062816 15.7157C0.0178889 15.797 0.0453863 15.8752 0.0872035 15.9458C0.129021 16.0164 0.184339 16.0781 0.25 16.1273C0.315661 16.1766 0.390379 16.2124 0.469887 16.2328C0.549395 16.2532 0.632137 16.2577 0.713388 16.2461C0.794639 16.2345 0.872809 16.207 0.943433 16.1651C1.01406 16.1233 1.07575 16.068 1.125 16.0023C1.50341 15.4978 1.99409 15.0883 2.55819 14.8062C3.12229 14.5242 3.74432 14.3774 4.375 14.3774C5.00568 14.3774 5.6277 14.5242 6.1918 14.8062C6.7559 15.0883 7.24659 15.4978 7.625 16.0023C7.72457 16.1348 7.87269 16.2224 8.03678 16.2456C8.20086 16.2689 8.36749 16.2261 8.5 16.1266C8.54724 16.0914 8.58909 16.0496 8.62422 16.0023C9.00263 15.4978 9.49331 15.0883 10.0574 14.8062C10.6215 14.5242 11.2435 14.3774 11.8742 14.3774C12.5049 14.3774 13.1269 14.5242 13.691 14.8062C14.2551 15.0883 14.7458 15.4978 15.1242 16.0023C15.2238 16.135 15.3719 16.2226 15.5361 16.246C15.7003 16.2693 15.867 16.2265 15.9996 16.127C16.1322 16.0274 16.2198 15.8792 16.2432 15.7151C16.2666 15.5509 16.2238 15.3842 16.1242 15.2516C15.571 14.5099 14.8333 13.926 13.9844 13.5578ZM4.375 9.37735C4.74584 9.37735 5.10835 9.48732 5.41669 9.69335C5.72504 9.89937 5.96536 10.1922 6.10727 10.5348C6.24919 10.8774 6.28632 11.2544 6.21397 11.6181C6.14162 11.9819 5.96305 12.316 5.70082 12.5782C5.4386 12.8404 5.10451 13.019 4.74079 13.0913C4.37708 13.1637 4.00008 13.1265 3.65747 12.9846C3.31486 12.8427 3.02202 12.6024 2.81599 12.294C2.60997 11.9857 2.5 11.6232 2.5 11.2524C2.5 10.7551 2.69754 10.2782 3.04917 9.92653C3.4008 9.5749 3.87772 9.37735 4.375 9.37735ZM11.875 9.37735C12.2458 9.37735 12.6083 9.48732 12.9167 9.69335C13.225 9.89937 13.4654 10.1922 13.6073 10.5348C13.7492 10.8774 13.7863 11.2544 13.714 11.6181C13.6416 11.9819 13.463 12.316 13.2008 12.5782C12.9386 12.8404 12.6045 13.019 12.2408 13.0913C11.8771 13.1637 11.5001 13.1265 11.1575 12.9846C10.8149 12.8427 10.522 12.6024 10.316 12.294C10.11 11.9857 10 11.6232 10 11.2524C10 10.7551 10.1975 10.2782 10.5492 9.92653C10.9008 9.5749 11.3777 9.37735 11.875 9.37735Z" fill="#7F7F7F"/>
                                    </svg>
                                    <span class="b5-text">Subject</span>
                                </div>
                                <div class="lbl">
                                    <svg width="13" height="13" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.6 8.96797C8.54133 8.89949 8.46853 8.84452 8.3866 8.80683C8.30468 8.76914 8.21557 8.74963 8.12539 8.74963C8.03521 8.74963 7.9461 8.76914 7.86418 8.80683C7.78225 8.84452 7.70946 8.89949 7.65078 8.96797L3.90078 13.343C3.82292 13.4337 3.77272 13.5449 3.75612 13.6632C3.73953 13.7816 3.75724 13.9023 3.80716 14.011C3.85707 14.1196 3.9371 14.2116 4.03775 14.2762C4.1384 14.3407 4.25544 14.375 4.375 14.375H11.875C11.9946 14.375 12.1116 14.3407 12.2122 14.2762C12.3129 14.2116 12.3929 14.1196 12.4428 14.011C12.4928 13.9023 12.5105 13.7816 12.4939 13.6632C12.4773 13.5449 12.4271 13.4337 12.3492 13.343L8.6 8.96797ZM5.73359 13.125L8.125 10.3352L10.5164 13.125H5.73359ZM16.25 1.875V10.625C16.25 11.1223 16.0525 11.5992 15.7008 11.9508C15.3492 12.3025 14.8723 12.5 14.375 12.5H13.75C13.5842 12.5 13.4253 12.4342 13.3081 12.3169C13.1908 12.1997 13.125 12.0408 13.125 11.875C13.125 11.7092 13.1908 11.5503 13.3081 11.4331C13.4253 11.3158 13.5842 11.25 13.75 11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V1.875C15 1.70924 14.9342 1.55027 14.8169 1.43306C14.6997 1.31585 14.5408 1.25 14.375 1.25H1.875C1.70924 1.25 1.55027 1.31585 1.43306 1.43306C1.31585 1.55027 1.25 1.70924 1.25 1.875V10.625C1.25 10.7908 1.31585 10.9497 1.43306 11.0669C1.55027 11.1842 1.70924 11.25 1.875 11.25H2.5C2.66576 11.25 2.82473 11.3158 2.94194 11.4331C3.05915 11.5503 3.125 11.7092 3.125 11.875C3.125 12.0408 3.05915 12.1997 2.94194 12.3169C2.82473 12.4342 2.66576 12.5 2.5 12.5H1.875C1.37772 12.5 0.900806 12.3025 0.549175 11.9508C0.197544 11.5992 0 11.1223 0 10.625V1.875C0 1.37772 0.197544 0.900806 0.549175 0.549175C0.900806 0.197544 1.37772 0 1.875 0H14.375C14.8723 0 15.3492 0.197544 15.7008 0.549175C16.0525 0.900806 16.25 1.37772 16.25 1.875Z" fill="#7F7F7F"/>
                                    </svg>
                                    <span class="b5-text">Ticket ID</span>
                                </div>
                                <div class="lbl">
                                    <svg width="13" height="13" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 16H16M6 5.16667H6.83333M6 8.5H6.83333M6 11.8333H6.83333M10.1667 5.16667H11M10.1667 8.5H11M10.1667 11.8333H11M2.66667 16V2.66667C2.66667 2.22464 2.84226 1.80072 3.15482 1.48816C3.46738 1.17559 3.89131 1 4.33333 1H12.6667C13.1087 1 13.5326 1.17559 13.8452 1.48816C14.1577 1.80072 14.3333 2.22464 14.3333 2.66667V16" stroke="#7F7F7F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="b5-text">Company</span>
                                </div>
                                 <div class="lbl related-device-row" style="display:none;">
                                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 3.5C2 2.67 2.67 2 3.5 2h9c.83 0 1.5.67 1.5 1.5v6c0 .83-.67 1.5-1.5 1.5H9v1h2a.5.5 0 010 1H5a.5.5 0 010-1h2v-1H3.5A1.5 1.5 0 012 9.5v-6z" stroke="#7F7F7F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="b5-text">Device</span>
                                </div>

                                <div class="lbl">
                                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2" width="5" height="5" rx="1" stroke="#7F7F7F" stroke-width="1.4"/>
                                        <rect x="9" y="2" width="5" height="5" rx="1" stroke="#7F7F7F" stroke-width="1.4"/>
                                        <rect x="2" y="9" width="5" height="5" rx="1" stroke="#7F7F7F" stroke-width="1.4"/>
                                        <rect x="9" y="9" width="5" height="5" rx="1" stroke="#7F7F7F" stroke-width="1.4"/>
                                    </svg>
                                    <span class="b5-text">Category</span>
                                </div>

                                <div class="lbl subcategory-row" style="display:none;">
                                   <svg width="13" height="13" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3h4l1 2h5v7H3V3z" stroke="#7F7F7F" stroke-width="1.4" stroke-linejoin="round"/>
                                        <circle cx="5" cy="9" r="0.8" fill="#7F7F7F"/>
                                        <circle cx="11" cy="9" r="0.8" fill="#7F7F7F"/>
                                        <path d="M5.8 9H10.2" stroke="#7F7F7F" stroke-width="1.2"/>
                                    </svg>
                                    <span class="b5-text">Sub Category</span>
                                </div>
                            </div>
                            <div class="header-right px-3 py-2 d-flex flex-column gap-2 w-100">
                                <div class="lead-line b5-text" id="ticket_details_subject" data-bs-toggle="tooltip"></div>
                                <div class="lead-line b5-text" id="ticket_details_id"></div>
                                <div class="lead-line b5-text" id="ticket_details_company"></div>
                                <div class="lead-line b5-text" id="ticket_details_device" style="display:none;"></div>
                                <div class="lead-line b5-text" id="ticket_details_category"></div>
                                <div class="lead-line b5-text subcategory-row" id="ticket_details_subcategory" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="header-to-timeline"></div>

                        <!-- Timeline -->
                        <div class="timeline-wrap">
                            <div class="timeline-rail"></div>
                            <div id="ticket_history_container"></div>
                            <div class="ticket-history-loader d-none text-center py-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-2">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
  #ticket_history_container{
      max-height: 350px;
      overflow-y: auto;
  }
  .header-card {
    border-radius: 12px;
    overflow: hidden;
    border: 1px dashed #7F7F7F;
  }
  .header-left {
    background: #0e1d4d;
    color: #fff;
    min-width: 182px;
  }
  .header-left .lbl {
    font-size: .85rem;
    font-weight: 500;
    opacity: .95;
  }
  .header-left .lbl i { width: 18px; }
  .header-right {
    color: #212529;
  }

  .header-to-timeline {
    width: 2px;
    height: 26px;
    background: #000000;
    margin-left: 182px; 
  }
  @media (max-width: 480px) {
    .header-to-timeline { display: none; }
  }

  /* Timeline */
  .timeline-wrap {
    position: relative;
  }
  .timeline-rail {
    position: absolute;
    left: 182px; 
    top: 0px;
    bottom: 18px;
    width: 2px;
    background: #000000;
  }
  .timeline-rail::after {
    content: "";
    position: absolute;
    bottom: -9px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 9px solid #000000;
  }

  .t-item {
    position: relative;
    display: grid;
    grid-template-columns: 150px 64px 1fr; 
    align-items: center;
    margin-bottom: .85rem;
  }
  .t-item:last-child { margin-bottom: 0; }

  .t-actor {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding-top: 3px;
    min-width: 0;
  }
  .t-actor img, .t-actor .avatar-fallback {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    line-height: 12px;
    font-weight: 600;
    color: rgb(255, 255, 255);
    background: #E6E6E6;
  }
  .t-actor a {
    color: #F12F35;
    font-weight: 400;
    line-height: 16px;
    font-size: 14px;
    text-decoration: none;
    border-bottom: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .t-actor a:hover { border-bottom: 1px solid #F12F35; }

  .t-rail-col {
    position: relative;
    align-self: start;
    height: 100%;
  }
  .t-rail-col::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    border-top: 1.5px dashed #000000;
  }
  .rail-cap {
    position: absolute;
    top: 19px;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
  }
  .rail-cap.start {
    top: 50%;
    left: -1px;
    border-right: 5px solid #000000;
  }
  .rail-cap.end {
    top: 50%;
    right: -1px;
    border-left: 5px solid #000000;
  }

  .t-card {
    display: flex;
    border-radius: 10px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    border: 1px solid #7F7F7F; 
    margin-left: 10px;
  }

  .t-card.tone-reopen .t-body .t-text p {
    color: #fff;
  }

  .t-card.tone-reopen .t-time,
  .t-card.tone-reopen .t-time i {
      color: #fff !important;
  }

  [data-bs-theme=dark] .t-card,[data-bs-theme=dark] .header-card{
     background: #252525;
  }
  [data-bs-theme=dark] .header-to-timeline,[data-bs-theme=dark] .timeline-rail{
   background: #fff;
  }
  [data-bs-theme=dark] .rail-cap.start{
    border-right: 5px solid #fff;
  }
  [data-bs-theme=dark] .rail-cap.end {
    border-left: 5px solid #fff;
  }
  [data-bs-theme=dark] .t-rail-col::before{
    border-top: 1.5px dashed #fff
  }
  [data-bs-theme=dark] .timeline-rail::after {
    border-top: 9px solid #fff;
  }
  @media (max-width: 480px) {
    .t-card { margin-left: 0; }
  }

  .t-body {
    flex: 1;
    padding: .85rem 1.1rem;
  }
  .t-time {
    font-size: .78rem;
    color: #7F7F7F;
    margin-bottom: .15rem;
  }
  .t-time i { margin-right: .3rem; }
  .t-text {
    font-size: .92rem;
    color: #212529;
  }
  .t-text p { margin: 0; }
  .t-text .extra { color: #9aa2b1; font-size: .85rem; margin-top: .15rem; }

  .t-ribbon {
    width: 108px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: .25rem;
    font-size: .78rem;
    font-weight: 600;
    text-align: center;
    border-left: 1px solid #7F7F7F;
  }
  .t-ribbon i { font-size: 1rem; }

  .tone-newticket { border-color: #8e2f9c; }
  .tone-newticket .t-ribbon { background: #8e2f9c; color: #fff; }

  .tone-open { border-color: #12a78d; }
  .tone-open .t-ribbon { background: #12a78d; color: #fff; }

  .tone-reopen { border-color: #d9392f; }
  .tone-reopen .t-card {border-color: #d9392f; }
  .tone-reopen .t-body { color: #fff;background: #d9392f}
  .tone-reopen .t-time { color: rgba(255,255,255,.85); }
  .tone-reopen .t-ribbon { background: #fff; color: #d9392f; }

  .tone-waiting { border-color: #2f5fd9; }
  .tone-waiting .t-ribbon { background: #2f5fd9; color: #fff; }

  .tone-hold { border-color: #9aa2b1; }
  .tone-hold .t-ribbon { background: #c7ccd6; color: #333; }

  .tone-resolved { border-color: #e08a1c; }
  .tone-resolved .t-ribbon {
    background: #e08a1c;
    color: #fff;
  }
  @media (max-width: 480px) {
    .timeline-rail { display: none; }
    .t-item {
      grid-template-columns: 1fr;
      row-gap: .4rem;
    }
    .t-rail-col { display: none; }
    .t-actor { padding-top: 0; }
    .t-card { margin-left: 0; }
  }
</style>