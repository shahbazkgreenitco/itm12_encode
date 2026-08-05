
{{-- @page-meta
{
  "page_no": "LYNVB-03",
  "file": "navbar.blade.php",
  "versions": [
    {
      "version": "1.5",
      "writer": "Hrishikesh Pandey",
      "from": "2026-04",
      "reviewer": null,
      "description": "Technicien Status Remark & Ticket Log in Workin Stage"
    }
    {
      "version": "1.4",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Redesign Navbar to maintain screen ratio"
    }
  ]
}
--}}
<style>
    #amg-company-select+.select2-container--default .select2-selection--single {
        height: 26px !important; 
        border: 1px solid #F0F0F0;
        border-radius: 40px;
        background: #fff;
        display: flex;
        align-items: center;
        padding: 0 5px;
    }
    

    [data-bs-theme="dark"] #amg-company-select+.select2-container--default .select2-selection--single {
        border: none;
        background: #2A2A2A !important;
    }

    #amg-company-select+.select2-container--custom .select2-selection--single .select2-selection__rendered {
        color: #7F7F7F;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    [data-bs-theme="dark"] #amg-company-select+.select2-container--custom .select2-selection--single .select2-selection__rendered {
        color: #7F7F7F;
    }

    #select2-amg-company-select-results .select2-results__option {
        /* display: flex; */
        align-items: center;
        gap: 10px;
        padding: 8px 12px !important;
        /* border-radius: 8px; */
        font-size: 14px;
        font-weight: 400;
        color: #111;
        cursor: pointer;
        transition: background 0.1s;
        border-bottom: 1px solid #F0F0F0;
    }

    [data-bs-theme="dark"] #select2-amg-company-select-results .select2-results__option {
        border-bottom: 1px solid #191919;
    }

    #select2-amg-company-select-results .select2-results__option--selectable.select2-results__option--highlighted {
        background: #EFF2FA !important;
        color: #111 !important;
    }

    [data-bs-theme="dark"] #select2-amg-company-select-results .select2-results__option--selectable.select2-results__option--highlighted {
        background: #191919 !important;
        color: #111 !important;
    }

    [data-bs-theme="dark"] #select2-amg-company-select-results .select2-results__option--selectable.select2-results__option--highlighted {
        background: #292929 !important;
        color: #fff !important;
    }

    [data-bs-theme=dark] .select2-container--custom .select2-results__option:not(:last-child),
    [data-bs-theme=dark] .select2-container--custom .select2-results__options:not(:last-child) {
        background-color: #191919 !important;
        color: #959595 !important;
        border-bottom: 1px solid #2A2A2D !important;
    }

    [data-bs-theme=dark] .select2-container--custom .select2-results__option:last-child {
        background-color: #191919 !important;
        color: #959595 !important;
        border-bottom: none !important;
    }

    #select2-amg-company-select-results .select2-results__option--selectable.select2-results__option--selected {
        background: #eef2ff !important;
        color: #111 !important;
    }

    [data-bs-theme="dark"] #select2-amg-company-select-results .select2-results__option--selectable.select2-results__option--selected {
        background: #292929 !important;
        color: #fff !important;
    }

    #select2-amg-company-select-results .company-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }

    #select2-amg-company-select-results .select2-results__option--group {
        padding: 0px !important;
    }

    #amg-company-select+.select2-container--custom .select2-selection--single .select2-selection__arrow {
        content: "";
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100% !important;
        width: 20px;
        right: 10px;
        top: 0;
        position: absolute;
    }

    #amg-company-select+.select2-container--custom .select2-selection--single .select2-selection__arrow b {
        content: "";
        display: block;
        width: 0;
        height: 0;
        border-style: solid;
        border-color: #6b7280 transparent transparent transparent;
        border-width: 7px 6px 0 6px;
    }

    #amg-company-select+.select2-container--default .select2-selection__placeholder{
        font-weight: 300;
        font-size: 12px;
    }

    #amg-company-select+.select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #888 transparent transparent transparent;
        border-style: solid;
        border-width: 7.9px 7.8px 0 7.8px;
        height: 0;
        border-radius: 2px;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        position: absolute;
        top: 50%;
        width: 0;
    }
    #amg-company-select+.select2-container--custom.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #6b7280 transparent;
        border-width: 0 6px 7px 6px;
        /* was 0 4px 5px 4px */
    }

    #amg-company-select+.select2-container--default{
        margin-left: 16px;
    }

    #select2-amg-company-select-results .select2-results__group {
        font-size: 15px;
        font-weight: 500;
        color: #111;
        padding: 10px 12px 6px;
        text-transform: capitalize;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #f0f0f0;
        display: block;
    }

    [data-bs-theme="dark"] #select2-amg-company-select-results .select2-results__group {
        color: #fff;
        border-bottom: 1px solid #2A2A2D;
        background: #2a2a2a;
    }

    #select2-amg-company-select-results .select2-container--custom .select2-results__option--selectable:hover {
        background: red !important;
        color: #111 !important;
    }

    #amg-company-select+.select2-container--custom .select2-results__option--selected {
        background: #eef2ff !important;
        color: #111 !important;
    }

    #amg-company-select+.select2-container--custom .select2-results__option--highlighted.select2-results__option--selected {
        background: #eef2ff !important;
        color: #111 !important;
    }
    #amg-company-select+.select2-container--default #select2-amg-company-select-container{
        line-height: 40px !important;
    }


    /* header-profile avatar start */
    .profile-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 400;
        font-size: 22px;
        letter-spacing: 0.5px;
    }

    .initials-avatar {
        background: linear-gradient(135deg, #9CA3AF, #4B5563);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* header-profile avatar end */
</style>
<nav class="top-navbar app-header">
    <div class="d-flex align-items-center justify-content-between h-100">
        <div class="d-flex align-items-center gap-3">

            <div class="brand_logo">
                {{-- <a href="{{ url('') }}" class="navbar-brand">
                    <img src="{{ asset('main_asset/assets/images/amg-light.png') }}" class="light-logo"
                        alt="{{ __('header.header.brand_alt') }}">
                </a>
                <a href="{{ url('') }}" class="navbar-brand">
                    <img src="{{ asset('main_asset/assets/images/amg-dark.png') }}" class="dark-logo"
                        alt="{{ __('header.header.brand_alt') }}">
                </a> --}}
                <a href="{{ url('') }}" class="navbar-brand">
                    @if(CommonHelper::settings()->brand == 3)
                        <img src="{{ CommonHelper::CompLogo() }}" alt="ITM Logo" class="light-logo">
                    @elseif(CommonHelper::settings()->brand == 2)
                        <img src="{{ CommonHelper::CompLogo() }}" alt="ITM Logo">   
                    @else
                        <div class="brand-title site-name-only">
                            <span class="brand-text">{{ CommonHelper::settings()->site_name }}</span>
                            <div class="single-char-title">{{ CommonHelper::settings()->auto_increment_prefix }}</div>
                        </div>
                    @endif
                </a>
            </div>
            @if(in_array(config('app.client') , ['rolepermission', 'grdemo', 'ril', 'greenitco']))
                <select id="amg-company-select" style="margin-left: 20px;width: 230px;">
                    <optgroup label="Companies"></optgroup>
                </select>
            @endif
            <a href="{{ route('nl-query.index') }}" class="mati-ai-btn">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path
                        d="M5.84365 2.87333C6.34198 1.415 8.35698 1.37083 8.94782 2.74083L8.99782 2.87417L9.67032 4.84083C9.82444 5.29186 10.0735 5.70459 10.4007 6.05119C10.7279 6.39778 11.1256 6.67018 11.567 6.85L11.7478 6.9175L13.7145 7.58917C15.1728 8.0875 15.217 10.1025 13.8478 10.6933L13.7145 10.7433L11.7478 11.4158C11.2966 11.5699 10.8837 11.8189 10.537 12.146C10.1903 12.4732 9.91773 12.871 9.73782 13.3125L9.67032 13.4925L8.99865 15.46C8.50032 16.9183 6.48531 16.9625 5.89531 15.5933L5.84365 15.46L5.17198 13.4933C5.01796 13.0422 4.76896 12.6293 4.44177 12.2825C4.11457 11.9358 3.71681 11.6632 3.27532 11.4833L3.09531 11.4158L1.12865 10.7442C-0.330519 10.2458 -0.374685 8.23083 0.995315 7.64083L1.12865 7.58917L3.09531 6.9175C3.54634 6.76338 3.95907 6.51433 4.30567 6.18714C4.65226 5.85995 4.92466 5.46224 5.10448 5.02083L5.17198 4.84083L5.84365 2.87333ZM14.0878 1.50573e-07C14.2437 -1.96643e-07 14.3965 0.0437319 14.5288 0.126226C14.6611 0.208721 14.7676 0.326669 14.8362 0.466667L14.8761 0.564167L15.1678 1.41917L16.0236 1.71083C16.1799 1.76391 16.3168 1.86218 16.4172 1.99318C16.5175 2.12418 16.5767 2.28202 16.5872 2.44668C16.5977 2.61135 16.5592 2.77544 16.4763 2.91816C16.3935 3.06087 16.2702 3.17578 16.122 3.24833L16.0236 3.28833L15.1686 3.58L14.877 4.43583C14.8238 4.59202 14.7255 4.72892 14.5944 4.82916C14.4634 4.92941 14.3055 4.98849 14.1409 4.99894C13.9762 5.00938 13.8121 4.9707 13.6695 4.88782C13.5268 4.80493 13.412 4.68156 13.3395 4.53333L13.2995 4.43583L13.0078 3.58083L12.152 3.28917C11.9957 3.23609 11.8588 3.13782 11.7585 3.00682C11.6581 2.87582 11.599 2.71799 11.5884 2.55332C11.5779 2.38865 11.6165 2.22456 11.6993 2.08184C11.7821 1.93913 11.9054 1.82422 12.0536 1.75167L12.152 1.71167L13.007 1.42L13.2986 0.564167C13.3548 0.399521 13.4612 0.256591 13.6027 0.155416C13.7442 0.0542408 13.9138 -0.000104401 14.0878 1.50573e-07Z"
                        fill="url(#paint0_linear_812_204)" />
                    <defs>
                        <linearGradient id="paint0_linear_812_204" x1="1.26315" y1="-2.54709e-07" x2="13.7556"
                            y2="17.6965" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FA080C" />
                            <stop offset="1" stop-color="#1083FF" />
                        </linearGradient>
                    </defs>
                </svg>
                <span class="mati-ai-text b5-text">Ask {{ __('header.header.mati_ai') }}</span>
            </a>
        </div>
        <!-- navbar actions -->
        <div class="d-flex align-items-center gap-3 nav-user-actions">
            @if(config("services.service_ticket.enabled") && Auth::user()->hasPermission("service_tickets"))
                <label class="status-row">
                    <span class="switch">
                        <input type="checkbox" id="techAvability" checked>
                        <span class="slider"></span>
                    </span>
                    <span class="status-label">{{ __('header.header.status_available') }}</span>
                </label>
            @endif
            <div class="d-flex align-items-center gap-2 ms-1">
                <!-- <a class="top-icon-btn"  href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.9635 12.0235C9.61561 13.1004 7.90657 13.6203 6.18734 13.4765C4.46812 13.3327 2.86921 12.5361 1.71901 11.2502C0.568806 9.9643 -0.045372 8.28682 0.00261415 6.56226C0.0506003 4.83769 0.757107 3.19696 1.97703 1.97703C3.19696 0.757107 4.83769 0.0506003 6.56226 0.00261415C8.28682 -0.045372 9.9643 0.568806 11.2502 1.71901C12.5361 2.86921 13.3327 4.46812 13.4765 6.18735C13.6203 7.90657 13.1004 9.61561 12.0235 10.9635L17.1795 16.1185C17.2531 16.1871 17.3122 16.2699 17.3532 16.3619C17.3942 16.4539 17.4163 16.5532 17.418 16.6539C17.4198 16.7546 17.4013 16.8547 17.3636 16.9481C17.3259 17.0414 17.2697 17.1263 17.1985 17.1975C17.1273 17.2687 17.0424 17.3249 16.9491 17.3626C16.8557 17.4003 16.7556 17.4188 16.6549 17.417C16.5542 17.4153 16.4549 17.3932 16.3629 17.3522C16.2709 17.3112 16.1881 17.2521 16.1195 17.1785L10.9635 12.0235ZM3.03746 10.4615C2.3035 9.72742 1.80361 8.79228 1.60097 7.77423C1.39833 6.75617 1.50203 5.70089 1.89897 4.74176C2.29591 3.78262 2.96827 2.96268 3.83107 2.38556C4.69388 1.80844 5.7084 1.50004 6.74643 1.49934C7.78445 1.49864 8.79939 1.80567 9.66297 2.38163C10.5266 2.95759 11.2 3.77663 11.5982 4.73523C11.9965 5.69383 12.1016 6.74897 11.9003 7.7673C11.6991 8.78563 11.2004 9.72143 10.4675 10.4565L10.4625 10.4615L10.4575 10.4655C9.47255 11.4481 8.13788 11.9996 6.74663 11.9988C5.35537 11.9981 4.0213 11.4451 3.03746 10.4615Z" fill="#7F7F7F"/>
                    </svg>
                </a>

                <button class="top-icon-btn" data-bs-placement="bottom" data-bs-toggle="tooltip"
                    title="{{ __('header.header.track_parcel') }}">             
                    <svg viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.72 4.69975L10.47 0.185686C10.2496 0.0638879 10.0018 0 9.75 0C9.49816 0 9.25043 0.0638879 9.03 0.185686L0.78 4.70162C0.544395 4.83053 0.347722 5.02034 0.210517 5.25121C0.0733127 5.48208 0.000609617 5.74556 0 6.01412V14.9804C0.000609617 15.2489 0.0733127 15.5124 0.210517 15.7433C0.347722 15.9742 0.544395 16.164 0.78 16.2929L9.03 20.8088C9.25043 20.9306 9.49816 20.9945 9.75 20.9945C10.0018 20.9945 10.2496 20.9306 10.47 20.8088L18.72 16.2929C18.9556 16.164 19.1523 15.9742 19.2895 15.7433C19.4267 15.5124 19.4994 15.2489 19.5 14.9804V6.01506C19.4999 5.74602 19.4274 5.48196 19.2902 5.25054C19.153 5.01913 18.956 4.82889 18.72 4.69975ZM9.75 1.49819L17.2819 5.62319L14.4909 7.15131L6.95813 3.02631L9.75 1.49819ZM9.75 9.74819L2.21812 5.62319L5.39625 3.88319L12.9281 8.00819L9.75 9.74819ZM1.5 6.93569L9 11.0401V19.0829L1.5 14.9813V6.93569ZM18 14.9776L10.5 19.0829V11.0438L13.5 9.40225V12.7482C13.5 12.9471 13.579 13.1379 13.7197 13.2785C13.8603 13.4192 14.0511 13.4982 14.25 13.4982C14.4489 13.4982 14.6397 13.4192 14.7803 13.2785C14.921 13.1379 15 12.9471 15 12.7482V8.581L18 6.93569V14.9766V14.9776Z" fill="currentColor"/>
                    </svg>
                </button>

                <button class="top-icon-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('header.header.calendar_tooltip') }}">
                    <svg viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5 1.5H14.25V0.75C14.25 0.551088 14.171 0.360322 14.0303 0.21967C13.8897 0.0790176 13.6989 0 13.5 0C13.3011 0 13.1103 0.0790176 12.9697 0.21967C12.829 0.360322 12.75 0.551088 12.75 0.75V1.5H5.25V0.75C5.25 0.551088 5.17098 0.360322 5.03033 0.21967C4.88968 0.0790176 4.69891 0 4.5 0C4.30109 0 4.11032 0.0790176 3.96967 0.21967C3.82902 0.360322 3.75 0.551088 3.75 0.75V1.5H1.5C1.10218 1.5 0.720644 1.65804 0.43934 1.93934C0.158035 2.22064 0 2.60218 0 3V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H16.5C16.8978 19.5 17.2794 19.342 17.5607 19.0607C17.842 18.7794 18 18.3978 18 18V3C18 2.60218 17.842 2.22064 17.5607 1.93934C17.2794 1.65804 16.8978 1.5 16.5 1.5ZM3.75 3V3.75C3.75 3.94891 3.82902 4.13968 3.96967 4.28033C4.11032 4.42098 4.30109 4.5 4.5 4.5C4.69891 4.5 4.88968 4.42098 5.03033 4.28033C5.17098 4.13968 5.25 3.94891 5.25 3.75V3H12.75V3.75C12.75 3.94891 12.829 4.13968 12.9697 4.28033C13.1103 4.42098 13.3011 4.5 13.5 4.5C13.6989 4.5 13.8897 4.42098 14.0303 4.28033C14.171 4.13968 14.25 3.94891 14.25 3.75V3H16.5V6H1.5V3H3.75ZM16.5 18H1.5V7.5H16.5V18ZM10.125 10.875C10.125 11.0975 10.059 11.315 9.9354 11.5C9.81179 11.685 9.63608 11.8292 9.43052 11.9144C9.22495 11.9995 8.99875 12.0218 8.78052 11.9784C8.56229 11.935 8.36184 11.8278 8.2045 11.6705C8.04717 11.5132 7.94002 11.3127 7.89662 11.0945C7.85321 10.8762 7.87549 10.65 7.96064 10.4445C8.04578 10.2389 8.18998 10.0632 8.37498 9.9396C8.55999 9.81598 8.7775 9.75 9 9.75C9.29837 9.75 9.58452 9.86853 9.79549 10.0795C10.0065 10.2905 10.125 10.5766 10.125 10.875ZM14.25 10.875C14.25 11.0975 14.184 11.315 14.0604 11.5C13.9368 11.685 13.7611 11.8292 13.5555 11.9144C13.35 11.9995 13.1238 12.0218 12.9055 11.9784C12.6873 11.935 12.4868 11.8278 12.3295 11.6705C12.1722 11.5132 12.065 11.3127 12.0216 11.0945C11.9782 10.8762 12.0005 10.65 12.0856 10.4445C12.1708 10.2389 12.315 10.0632 12.5 9.9396C12.685 9.81598 12.9025 9.75 13.125 9.75C13.4234 9.75 13.7095 9.86853 13.9205 10.0795C14.1315 10.2905 14.25 10.5766 14.25 10.875ZM6 14.625C6 14.8475 5.93402 15.065 5.8104 15.25C5.68679 15.435 5.51109 15.5792 5.30552 15.6644C5.09995 15.7495 4.87375 15.7718 4.65552 15.7284C4.43729 15.685 4.23684 15.5778 4.0795 15.4205C3.92217 15.2632 3.81502 15.0627 3.77162 14.8445C3.72821 14.6262 3.75049 14.4 3.83564 14.1945C3.92078 13.9889 4.06498 13.8132 4.24998 13.6896C4.43499 13.566 4.6525 13.5 4.875 13.5C5.17337 13.5 5.45952 13.6185 5.6705 13.8295C5.88147 14.0405 6 14.3266 6 14.625ZM10.125 14.625C10.125 14.8475 10.059 15.065 9.9354 15.25C9.81179 15.435 9.63608 15.5792 9.43052 15.6644C9.22495 15.7495 8.99875 15.7718 8.78052 15.7284C8.56229 15.685 8.36184 15.5778 8.2045 15.4205C8.04717 15.2632 7.94002 15.0627 7.89662 14.8445C7.85321 14.6262 7.87549 14.4 7.96064 14.1945C8.04578 13.9889 8.18998 13.8132 8.37498 13.6896C8.55999 13.566 8.7775 13.5 9 13.5C9.29837 13.5 9.58452 13.6185 9.79549 13.8295C10.0065 14.0405 10.125 14.3266 10.125 14.625ZM14.25 14.625C14.25 14.8475 14.184 15.065 14.0604 15.25C13.9368 15.435 13.7611 15.5792 13.5555 15.6644C13.35 15.7495 13.1238 15.7718 12.9055 15.7284C12.6873 15.685 12.4868 15.5778 12.3295 15.4205C12.1722 15.2632 12.065 15.0627 12.0216 14.8445C11.9782 14.6262 12.0005 14.4 12.0856 14.1945C12.1708 13.9889 12.315 13.8132 12.5 13.6896C12.685 13.566 12.9025 13.5 13.125 13.5C13.4234 13.5 13.7095 13.6185 13.9205 13.8295C14.1315 14.0405 14.25 14.3266 14.25 14.625Z" fill="#7F7F7F"/>
                    </svg>
                </button> -->
                @if( (config("services.service_ticket.enabled")) )
                    <button class="top-icon-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('header.header.log_ticket') }}" onclick="window.location.href='{{ route('allTicketLists') }}?redirect=dash'">
                        <svg viewBox="0 0 21 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.25 5.25C20.4489 5.25 20.6397 5.17098 20.7803 5.03033C20.921 4.88968 21 4.69891 21 4.5V1.5C21 1.10218 20.842 0.720644 20.5607 0.43934C20.2794 0.158035 19.8978 0 19.5 0H1.5C1.10218 0 0.720644 0.158035 0.43934 0.43934C0.158035 0.720644 0 1.10218 0 1.5V4.5C0 4.69891 0.0790176 4.88968 0.21967 5.03033C0.360322 5.17098 0.551088 5.25 0.75 5.25C1.34674 5.25 1.91903 5.48705 2.34099 5.90901C2.76295 6.33097 3 6.90326 3 7.5C3 8.09674 2.76295 8.66903 2.34099 9.09099C1.91903 9.51295 1.34674 9.75 0.75 9.75C0.551088 9.75 0.360322 9.82902 0.21967 9.96967C0.0790176 10.1103 0 10.3011 0 10.5V13.5C0 13.8978 0.158035 14.2794 0.43934 14.5607C0.720644 14.842 1.10218 15 1.5 15H19.5C19.8978 15 20.2794 14.842 20.5607 14.5607C20.842 14.2794 21 13.8978 21 13.5V10.5C21 10.3011 20.921 10.1103 20.7803 9.96967C20.6397 9.82902 20.4489 9.75 20.25 9.75C19.6533 9.75 19.081 9.51295 18.659 9.09099C18.2371 8.66903 18 8.09674 18 7.5C18 6.90326 18.2371 6.33097 18.659 5.90901C19.081 5.48705 19.6533 5.25 20.25 5.25ZM1.5 11.175C2.34772 11.0029 3.10986 10.543 3.65728 9.87319C4.20471 9.20343 4.50376 8.36502 4.50376 7.5C4.50376 6.63498 4.20471 5.79657 3.65728 5.12681C3.10986 4.45705 2.34772 3.99714 1.5 3.825V1.5H6.75V13.5H1.5V11.175ZM19.5 11.175V13.5H8.25V1.5H19.5V3.825C18.6523 3.99714 17.8901 4.45705 17.3427 5.12681C16.7953 5.79657 16.4962 6.63498 16.4962 7.5C16.4962 8.36502 16.7953 9.20343 17.3427 9.87319C17.8901 10.543 18.6523 11.0029 19.5 11.175Z" fill="#7F7F7F"/>
                        </svg>
                    </button>
                @endif

                <button class="top-icon-btn" data-theme-toggle data-bs-placement="bottom" data-bs-toggle="tooltip"
                    title="{{ __('header.header.dark_mode') }}">
                    <svg  viewBox="0 0 22 22" fill="none">
                        <path
                            d="M10.655 20.8101C16.2635 20.8101 20.8101 16.2635 20.8101 10.655C20.8101 5.04657 16.2635 0.5 10.655 0.5C5.04657 0.5 0.5 5.04657 0.5 10.655C0.5 16.2635 5.04657 20.8101 10.655 20.8101Z"
                            stroke="currentColor" />
                        <path
                            d="M15.7229 5.58653C14.3785 4.24211 12.555 3.48682 10.6537 3.48682C8.75244 3.48682 6.929 4.24211 5.58458 5.58653C4.24015 6.93096 3.48486 8.75439 3.48486 10.6557C3.48486 12.557 4.24015 14.3804 5.58458 15.7249L10.6537 10.6557L15.7229 5.58653Z"
                            fill="currentColor" />
                    </svg>
                </button>
                <?php
                $configPermissionCount = (count(Auth::user()->getAllPermissions()->where('module_id', 23)) > 0) ? Auth::user()->getAllPermissions()->where('module_id', 23)->count() : 0;
                ?>
                @if($configPermissionCount > 0 && !Auth::user()->hasRole('User'))
                    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('header.header.config') }}">
                        <button class="top-icon-btn" id="configurationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.5016 5.25194C9.61163 5.25194 8.7416 5.51586 8.00158 6.01032C7.26156 6.50479 6.68478 7.20759 6.34419 8.02986C6.00359 8.85213 5.91448 9.75693 6.08811 10.6298C6.26174 11.5028 6.69033 12.3046 7.31966 12.9339C7.949 13.5633 8.75082 13.9918 9.62374 14.1655C10.4967 14.3391 11.4015 14.25 12.2237 13.9094C13.046 13.5688 13.7488 12.992 14.2433 12.252C14.7377 11.512 15.0016 10.642 15.0016 9.75194C15.0004 8.55884 14.5259 7.41497 13.6823 6.57133C12.8386 5.72768 11.6947 5.25318 10.5016 5.25194ZM10.5016 12.7519C9.9083 12.7519 9.32828 12.576 8.83493 12.2463C8.34159 11.9167 7.95707 11.4482 7.73 10.9C7.50294 10.3518 7.44353 9.74861 7.55929 9.16666C7.67504 8.58472 7.96076 8.05017 8.38032 7.63062C8.79988 7.21106 9.33443 6.92533 9.91637 6.80958C10.4983 6.69382 11.1015 6.75323 11.6497 6.9803C12.1979 7.20736 12.6664 7.59188 12.9961 8.08523C13.3257 8.57857 13.5016 9.15859 13.5016 9.75194C13.5016 10.5476 13.1856 11.3106 12.623 11.8733C12.0604 12.4359 11.2973 12.7519 10.5016 12.7519ZM20.8085 7.80287C20.7876 7.69726 20.7442 7.59738 20.6813 7.51003C20.6184 7.42267 20.5374 7.34989 20.4438 7.29662L17.6473 5.70287L17.636 2.551C17.6357 2.44245 17.6118 2.33527 17.566 2.23685C17.5202 2.13844 17.4535 2.05115 17.3707 1.981C16.3563 1.1229 15.1881 0.46531 13.9282 0.0431852C13.829 0.00960381 13.7238 -0.00282378 13.6195 0.00670288C13.5152 0.0162295 13.4141 0.0474992 13.3226 0.0984977L10.5016 1.67537L7.67789 0.0956852C7.58635 0.0443994 7.48506 0.0128937 7.38058 0.00320439C7.2761 -0.00648488 7.17074 0.00585798 7.07133 0.0394351C5.81241 0.464646 4.64554 1.12475 3.63258 1.98475C3.54986 2.0548 3.48331 2.14194 3.43751 2.24018C3.39171 2.33842 3.36774 2.44542 3.36727 2.55381L3.35321 5.7085L0.556643 7.30225C0.463082 7.35551 0.382085 7.4283 0.319157 7.51565C0.256229 7.60301 0.212846 7.70288 0.191956 7.8085C-0.0639853 9.09464 -0.0639853 10.4186 0.191956 11.7047C0.212846 11.8104 0.256229 11.9102 0.319157 11.9976C0.382085 12.085 0.463082 12.1577 0.556643 12.211L3.35321 13.8047L3.36446 16.9566C3.3648 17.0652 3.38869 17.1724 3.4345 17.2708C3.48031 17.3692 3.54693 17.4565 3.62977 17.5266C4.64421 18.3847 5.81241 19.0423 7.07227 19.4644C7.17148 19.498 7.27663 19.5104 7.38094 19.5009C7.48525 19.4914 7.5864 19.4601 7.67789 19.4091L10.5016 17.8285L13.3254 19.4082C13.4371 19.4704 13.5631 19.5027 13.691 19.5019C13.7729 19.5019 13.8543 19.4886 13.932 19.4626C15.1907 19.0379 16.3576 18.3785 17.3707 17.5191C17.4534 17.4491 17.52 17.3619 17.5658 17.2637C17.6116 17.1655 17.6355 17.0585 17.636 16.9501L17.6501 13.7954L20.4466 12.2016C20.5402 12.1484 20.6212 12.0756 20.6841 11.9882C20.7471 11.9009 20.7904 11.801 20.8113 11.6954C21.0658 10.4103 21.0649 9.08762 20.8085 7.80287ZM19.4023 11.0757L16.7238 12.5991C16.6065 12.6659 16.5093 12.763 16.4426 12.8804C16.3882 12.9741 16.331 13.0735 16.2729 13.1672C16.1985 13.2855 16.1589 13.4222 16.1585 13.5619L16.1445 16.5854C15.4245 17.1507 14.6225 17.6028 13.766 17.926L11.0641 16.4204C10.952 16.3583 10.8258 16.326 10.6976 16.3266H10.6798C10.5663 16.3266 10.452 16.3266 10.3385 16.3266C10.2044 16.3233 10.0717 16.3556 9.95414 16.4204L7.25039 17.9297C6.39212 17.6091 5.58785 17.1592 4.86539 16.5957L4.85508 13.5769C4.85462 13.437 4.815 13.2999 4.74071 13.1813C4.68258 13.0876 4.62539 12.9938 4.57196 12.8944C4.5057 12.7753 4.40854 12.6762 4.29071 12.6076L1.60946 11.0804C1.47071 10.2027 1.47071 9.30867 1.60946 8.431L4.28321 6.90475C4.40056 6.83802 4.49773 6.74085 4.56446 6.6235C4.61883 6.52975 4.67602 6.43037 4.73414 6.33662C4.80855 6.2184 4.84818 6.08162 4.84852 5.94194L4.86258 2.9185C5.58251 2.35313 6.38457 1.90105 7.24102 1.57787L9.93914 3.0835C10.0566 3.14859 10.1893 3.18096 10.3235 3.17725C10.437 3.17725 10.5513 3.17725 10.6648 3.17725C10.7989 3.1806 10.9316 3.14825 11.0491 3.0835L13.7529 1.57412C14.6112 1.89482 15.4154 2.34469 16.1379 2.90819L16.1482 5.92693C16.1487 6.0669 16.1883 6.20394 16.2626 6.32256C16.3207 6.41631 16.3779 6.51006 16.4313 6.60944C16.4976 6.7286 16.5947 6.8277 16.7126 6.89631L19.3938 8.4235C19.5344 9.30185 19.536 10.1968 19.3985 11.0757H19.4023Z"
                                    fill="currentColor" />
                            </svg>
                        </button>

                        <div class="dropdown-menu dropdown-menu-nav amg-global-dropdown dropdown-menu-animate-up"
                            aria-labelledby="configurationDropdown">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-3 pt-3">
                                        <div class="border-bottom"
                                            style="max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="position-relative">
                                                        @can("ManufactureRead")
                                                            <a href="{{ url('manufactures') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/manufacturer.png') }}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.manufactures') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_manufacturers') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("CategoryRead")
                                                            <a href="{{ url('categories') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/category.png') }}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.categories') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.browse_categories') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("ModelRead")
                                                            <a href="{{ url('models') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle  round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/model.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.models') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.view_all_models') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("LocationRead")
                                                            <a href="{{ url('locations') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/location.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.locations') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_locations') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("AllocationTypeRead")
                                                            <a href="{{ url('asset_allocation_type') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/allocation-type.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">
                                                                        {{ trans('config.config_menu.asset_allocation_type') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.asset_allocation_type') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can ("DepreciationRead")
                                                            <a href="{{ url('depreciations') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/deprications.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.depreciation') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.depreciation') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("DeviceTypeRead")
                                                            <a href="{{ url('device-type') }}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/device-type.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.device_type') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.device_type') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("ProcureAccountTypeRead")
                                                            <a href="{{ url('procure-account-type') }}" class="d-flex align-items-center pb-9 position-relative">
                                                            <div class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                <img src="{{ asset('assets/images/image-icons/procurement-account-type.png')}}" alt="MaterialM-img" class="img-fluid" width="28" height="28" />
                                                            </div>
                                                            <div class="flex-fill">
                                                                <h6 class="mb-1">
                                                                    {{ trans('config.config_menu.procurement_account_type') }}
                                                                </h6>
                                                                <span
                                                                    class="fs-2 d-block text-muted">{{ trans('config.config_menu.procurement_account_type') }}</span>
                                                            </div>
                                                        </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="position-relative">
                                                        @can("AnnouncementRead")
                                                            <a href="{{ url('announcements') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/svgs/icon-dd-chat.svg')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.announcements') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.view_announcements') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("HolidaysRead")
                                                            <a href="{{ url('holidays') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/svgs/icon-dd-date.svg')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.holidays') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_holidays')}}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("CompanyView")
                                                            <a href="{{ url('companies') }}"
                                                                    class="d-flex align-items-center pb-9 position-relative">
                                                                    <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/company.png')}}"
                                                                    alt="MaterialM-img" class="img-fluid" width="28"
                                                                    height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.companies') }}
                                                                    </h6>
                                                                    <span
                                                                    class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_companies') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("PlaceRead")
                                                            <a href="{{ url('internal-places') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/place.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">
                                                                        {{ trans('config.config_menu.internal_places') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.view_internal_places') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("DepartmentRead")
                                                            <a href="{{ url('departments') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/department.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.departments') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_departments') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("CustomFieldRead")
                                                            <a href="{{url('custom-fields')}}" class="d-flex align-items-center pb-9 position-relative">
                                                            <div class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                <img src="{{ asset('assets/images/image-icons/custom-fields.png')}}" alt="MaterialM-img" class="img-fluid" width="40" height="40" />
                                                            </div>
                                                            <div class="flex-fill">
                                                                <h6 class="mb-1">{{ trans('config.config_menu.custom_fields') }}
                                                                </h6>
                                                                <span
                                                                    class="fs-2 d-block text-muted">{{ trans('config.config_menu.custom_fields') }}</span>
                                                            </div>
                                                        </a>
                                                        @endcan
                                                        @can("ContractAgreementRead")
                                                            <a href="{{ url('lease-agreements')}}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/contract-agreement.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">
                                                                        {{ trans('config.config_menu.contract_agreements') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.contract_agreements') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("UserLocationRead")
                                                            <a href="{{ url('locations/config') }}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/user-location.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">
                                                                        {{ trans('config.config_menu.user_locations') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.user_locations') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="position-relative">
                                                        @can("SupplierRead")
                                                            <a href="{{ url('suppliers') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/supplier.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.suppliers') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.view_suppliers') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan

                                                        @can("PurchaseRead")
                                                            <a href="{{ url('purchases') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/svgs/icon-dd-invoice.svg')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.purchases') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.manage_purchases') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("SettingRead")
                                                            <a href="{{ url('settings') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/settings.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.settings') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.settings') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("CustomTaxRead")
                                                            <a href="{{ url('custom_tax') }}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/custom-tax.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.custom_tax') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.custom_tax') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("ProjectRead")
                                                            <a href="{{ url('projects/list') }}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/projects.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.projects') }}</h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.projects') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("StatusLabelRead")
                                                            <a href="{{ url('status-labels') }}"
                                                                class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/status-labels.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.status_labels') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.status_labels') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                        @can("ThresholdRead")
                                                            <a href="{{ url('threshold') }}" class="d-flex align-items-center pb-9 position-relative">
                                                                <div
                                                                    class="text-bg-light amg-dropdown-nav-image rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center flex-shrink-0">
                                                                    <img src="{{ asset('assets/images/image-icons/threshold.png')}}"
                                                                        alt="MaterialM-img" class="img-fluid" width="28"
                                                                        height="28" />
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <h6 class="mb-1">{{ trans('config.config_menu.threshold') }}
                                                                    </h6>
                                                                    <span
                                                                        class="fs-2 d-block text-muted">{{ trans('config.config_menu.threshold') }}</span>
                                                                </div>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row align-items-center py-3">
                                            <div class="col-8">
                                                <a class="fw-semibold text-dark d-flex align-items-center lh-1 bg-hover-primary"
                                                    href="https://contactservicedesk.com" target="_blank">
                                                    <svg class="me-1" width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                                                        <path d="M10.5 8.67709C10.8665 8.26188 11.4027 8 12 8C13.1046 8 14 8.89543 14 10C14 10.9337 13.3601 11.718 12.4949 11.9383C12.2273 12.0064 12 12.2239 12 12.5V12.5V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Frequently Asked Questions
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex justify-content-end pe-4">
                                                    <button class="btn btn-primary rounded-pill">Check</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- <button class="top-icon-btn notification-badge" data-bs-placement="bottom" data-bs-toggle="tooltip"
                    title="{{ __('header.header.notifications_tooltip') }}">
                    <svg width="20" height="22" viewBox="0 0 20 22" fill="none">
                        <path
                            d="M16.499 8.46V7.755C16.499 3.886 13.475 0.75 9.74896 0.75C6.02296 0.75 2.99896 3.886 2.99896 7.755V8.46C3.00017 9.30155 2.76001 10.1258 2.30696 10.835L1.19896 12.56C0.187964 14.135 0.959963 16.276 2.71896 16.774C7.31507 18.077 12.1829 18.077 16.779 16.774C18.538 16.276 19.31 14.135 18.299 12.561L17.191 10.836C16.7376 10.1269 16.4971 9.30265 16.498 8.461L16.499 8.46Z"
                            stroke="currentColor" stroke-opacity="1" stroke-width="1.5" />
                        <path opacity="1"
                            d="M5.24902 17.75C5.90402 19.498 7.67102 20.75 9.74902 20.75C11.827 20.75 13.594 19.498 14.249 17.75M9.74902 4.75V8.75"
                            stroke="currentColor" stroke-opacity="1" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </button>-->

                <button class="top-icon-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('header.header.language_tooltip') }}">
                    <a href="javascript:void(0)" id="roles-table-action-dropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none">
                            <path
                                d="M19.25 10.3854C19.25 7.82995 18.2755 5.37914 16.5407 3.57215C14.806 1.76516 12.4533 0.75 10 0.75M19.25 10.3854H0.75M19.25 10.3854C19.25 12.9409 18.2755 15.3917 16.5407 17.1987C14.806 19.0057 12.4533 20.0208 10 20.0208M10 0.75C7.54675 0.75 5.19397 1.76516 3.45926 3.57215C1.72455 5.37914 0.75 7.82995 0.75 10.3854M10 0.75C9.5 0.75 6 5.06354 6 10.3854C6 15.7073 9.5 20.0208 10 20.0208M10 0.75C10.5 0.75 14 5.06354 14 10.3854C14 15.7073 10.5 20.0208 10 20.0208M0.75 10.3854C0.75 12.9409 1.72455 15.3917 3.45926 17.1987C5.19397 19.0057 7.54675 20.0208 10 20.0208"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>

                    <ul class="dropdown-menu action-dropdown-menu action-dropdown-menu-v2 language-selector-dropdown dropdown-menu-end dropdown-menu-animate-up"
                        aria-labelledby="roles-table-action-dropdown">
                        <li><a class="dropdown-header" href="#"><span
                                    class="b1-text fw-semibold">{{ __('header.header.language_select') }}</span></a></li>
                        @foreach (CommonHelper::availableLocale() as $locale => $img)
                            <li>
                                <a class="dropdown-item set-locale" href="#" data-locale="{{ $locale }}">
                                    <img height="20" width="20" src="{{ asset($img) }}" alt="{{ $locale }}">
                                    <span class="b3-text">{{ strtoupper($locale) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </button>
            </div>

            <a class="user-actions" href="javascript:void(0)" id="roles-table-action-dropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex align-items-center gap-1 user-profile-actions">
                    <div class="user-avatar">
                        @if(Auth::user()->getProfileImg(true) != null)
                            <img height="32px" width="32px" src="{!! Auth::user()->getProfileImg() !!}"
                                alt="{{ __('header.header.user_profile') }}">
                        @else
                            <div class="userInitials">{{ Auth::user()->getInitials() }}</div>
                        @endif
                    </div>

                    @php
                        $name = Auth::user()->getGuranteedNameText(true);
                        $parts = explode(' ', $name);
                        $formatted = $parts[0] . (isset($parts[1]) ? ' ' . strtoupper($parts[1][0]) : '');
                    @endphp

                    <span class="b5-text">{{ $formatted }}</span>

                    <svg width="11" height="5" viewBox="0 0 11 6" fill="none">
                        <path
                            d="M10.8541 0.85375L5.85414 5.85375C5.80771 5.90024 5.75256 5.93712 5.69186 5.96228C5.63116 5.98744 5.5661 6.00039 5.50039 6.00039C5.43469 6.00039 5.36962 5.98744 5.30892 5.96228C5.24822 5.93712 5.19308 5.90024 5.14664 5.85375L0.146644 0.85375C0.0766381 0.783823 0.0289543 0.694696 0.00962913 0.597654C-0.00969606 0.500611 0.000206247 0.400016 0.0380825 0.308605C0.0759587 0.217193 0.140106 0.139075 0.222403 0.08414C0.3047 0.0292046 0.401446 -7.77138e-05 0.500394 1.549e-07H10.5004C10.5993 -7.77138e-05 10.6961 0.0292046 10.7784 0.08414C10.8607 0.139075 10.9248 0.217193 10.9627 0.308605C11.0006 0.400016 11.0105 0.500611 10.9912 0.597654C10.9718 0.694696 10.9241 0.783823 10.8541 0.85375Z"
                            fill="currentColor" />
                    </svg>
                </div>
            </a>
            <ul class="dropdown-menu action-dropdown-menu action-dropdown-menu-v2 user-actions-dropdown dropdown-menu-end dropdown-menu-animate-up"
                aria-labelledby="roles-table-action-dropdown">
                <li>
                    <div class="profile-wrapper">
                        <!-- Avatar Area -->
                        <div class="avatar-box position-relative d-inline-block">
                            @if(Auth::user()->getProfileImg(true) != null)
                                <img src="{!! Auth::user()->getProfileImg() !!}" class="profile-avatar"
                                    alt="{{ __('header.header.user_profile') }}">
                            @else
                                <div class="profile-avatar initials-avatar">
                                    {{ strtoupper(Auth::user()->getInitials()) }}
                                </div>
                            @endif
                            <!-- Edit Button -->
                            <a href="{{ url('profile') }}">
                                <button class="edit-avatar-btn">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path
                                            d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"
                                            fill="black" />
                                    </svg>
                                </button>
                            </a>
                        </div>
                        <h3 class="h3-text mb-0">{{ Auth::user()->getGuranteedNameText(true) }}</h3>
                        <!-- Role -->
                        <!-- <span class="b4-text role-pill">@foreach(Auth::user()->roles->pluck('name') as $rolename) {{$rolename}} @endforeach</span> -->
                        <span class="b4-text role-pill">
                            @foreach(Auth::user()->roles->pluck('name') as $rolename)
                                {{ preg_replace('/(?<!^)([A-Z])/', ' $1', $rolename) }}
                            @endforeach
                        </span>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('profile') }}">
                        <svg width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path
                                d="M16.1735 14.6896C14.9836 12.6326 13.15 11.1576 11.0102 10.4584C12.0686 9.82826 12.891 8.86812 13.351 7.7254C13.8109 6.58268 13.8831 5.32056 13.5563 4.13287C13.2296 2.94518 12.522 1.89759 11.5422 1.15097C10.5624 0.404356 9.36466 0 8.13284 0C6.90102 0 5.70325 0.404356 4.72349 1.15097C3.74372 1.89759 3.03612 2.94518 2.70936 4.13287C2.3826 5.32056 2.45474 6.58268 2.91471 7.7254C3.37467 8.86812 4.19703 9.82826 5.2555 10.4584C3.11565 11.1568 1.28206 12.6318 0.0922159 14.6896C0.0485822 14.7608 0.0196403 14.8399 0.0070978 14.9225C-0.00544473 15.005 -0.00133381 15.0892 0.019188 15.1701C0.0397098 15.251 0.0762269 15.3269 0.126585 15.3935C0.176942 15.46 0.24012 15.5158 0.312392 15.5576C0.384663 15.5993 0.464563 15.6262 0.547378 15.6365C0.630193 15.6469 0.714246 15.6406 0.794576 15.6179C0.874907 15.5953 0.949888 15.5568 1.0151 15.5047C1.08031 15.4526 1.13442 15.388 1.17425 15.3146C2.64612 12.7709 5.24768 11.2521 8.13284 11.2521C11.018 11.2521 13.6196 12.7709 15.0914 15.3146C15.1313 15.388 15.1854 15.4526 15.2506 15.5047C15.3158 15.5568 15.3908 15.5953 15.4711 15.6179C15.5514 15.6406 15.6355 15.6469 15.7183 15.6365C15.8011 15.6262 15.881 15.5993 15.9533 15.5576C16.0256 15.5158 16.0887 15.46 16.1391 15.3935C16.1895 15.3269 16.226 15.251 16.2465 15.1701C16.267 15.0892 16.2711 15.005 16.2586 14.9225C16.246 14.8399 16.2171 14.7608 16.1735 14.6896ZM3.75784 5.62713C3.75784 4.76183 4.01443 3.91597 4.49516 3.19651C4.97589 2.47704 5.65917 1.91629 6.4586 1.58515C7.25803 1.25402 8.13769 1.16738 8.98636 1.33619C9.83503 1.505 10.6146 1.92168 11.2264 2.53353C11.8383 3.14539 12.255 3.92494 12.4238 4.77361C12.5926 5.62227 12.5059 6.50194 12.1748 7.30137C11.8437 8.10079 11.2829 8.78407 10.5635 9.2648C9.844 9.74554 8.99813 10.0021 8.13284 10.0021C6.9729 10.0009 5.86082 9.53955 5.04062 8.71935C4.22042 7.89915 3.75908 6.78707 3.75784 5.62713Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b3-text">{{ __('profile.profile.menu_profile') }}</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{!! CommonHelper::asset('public/user_guide.pdf') !!}"
                        target="_blank">
                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none">
                            <path
                                d="M4.62422 11.75C4.6899 11.7994 4.76468 11.8353 4.84426 11.8557C4.92385 11.8762 5.00669 11.8807 5.08803 11.8691C5.16937 11.8575 5.24763 11.83 5.31832 11.7881C5.38901 11.7462 5.45075 11.6908 5.5 11.625C5.87841 11.1205 6.36909 10.7109 6.9332 10.4289C7.4973 10.1468 8.11932 10 8.75 10C9.38068 10 10.0027 10.1468 10.5668 10.4289C11.1309 10.7109 11.6216 11.1205 12 11.625C12.0492 11.6907 12.1109 11.746 12.1816 11.7878C12.2522 11.8296 12.3304 11.8571 12.4116 11.8687C12.4929 11.8803 12.5756 11.8758 12.6551 11.8554C12.7346 11.8351 12.8093 11.7992 12.875 11.75C12.9407 11.7008 12.996 11.6391 13.0378 11.5684C13.0796 11.4978 13.1071 11.4196 13.1187 11.3384C13.1303 11.2571 13.1258 11.1744 13.1054 11.0949C13.0851 11.0154 13.0492 10.9407 13 10.875C12.4466 10.133 11.7086 9.54881 10.8594 9.18047C11.3248 8.75558 11.6508 8.19991 11.7947 7.5864C11.9387 6.97289 11.8938 6.3302 11.6659 5.74267C11.438 5.15514 11.0379 4.65023 10.5179 4.2942C9.99796 3.93816 9.38251 3.74765 8.75234 3.74765C8.12218 3.74765 7.50673 3.93816 6.98678 4.2942C6.46682 4.65023 6.06666 5.15514 5.83879 5.74267C5.61093 6.3302 5.56602 6.97289 5.70995 7.5864C5.85388 8.19991 6.17993 8.75558 6.64531 9.18047C5.7944 9.54811 5.05472 10.1324 4.5 10.875C4.40046 11.0075 4.35763 11.1741 4.38092 11.3382C4.40422 11.5023 4.49173 11.6504 4.62422 11.75ZM6.875 6.875C6.875 6.50416 6.98497 6.14165 7.19099 5.83331C7.39702 5.52496 7.68986 5.28464 8.03247 5.14273C8.37508 5.00081 8.75208 4.96368 9.1158 5.03603C9.47951 5.10837 9.8136 5.28695 10.0758 5.54917C10.338 5.8114 10.5166 6.14549 10.589 6.50921C10.6613 6.87292 10.6242 7.24992 10.4823 7.59253C10.3404 7.93514 10.1 8.22798 9.79169 8.43401C9.48335 8.64003 9.12084 8.75 8.75 8.75C8.25272 8.75 7.77581 8.55246 7.42417 8.20083C7.07254 7.84919 6.875 7.37228 6.875 6.875ZM14.375 0H3.125C2.79348 0 2.47554 0.131696 2.24112 0.366116C2.0067 0.600537 1.875 0.918479 1.875 1.25V3.125H0.625C0.45924 3.125 0.300268 3.19085 0.183058 3.30806C0.065848 3.42527 0 3.58424 0 3.75C0 3.91576 0.065848 4.07473 0.183058 4.19194C0.300268 4.30915 0.45924 4.375 0.625 4.375H1.875V7.5H0.625C0.45924 7.5 0.300268 7.56585 0.183058 7.68306C0.065848 7.80027 0 7.95924 0 8.125C0 8.29076 0.065848 8.44973 0.183058 8.56694C0.300268 8.68415 0.45924 8.75 0.625 8.75H1.875V11.875H0.625C0.45924 11.875 0.300268 11.9408 0.183058 12.0581C0.065848 12.1753 0 12.3342 0 12.5C0 12.6658 0.065848 12.8247 0.183058 12.9419C0.300268 13.0592 0.45924 13.125 0.625 13.125H1.875V15C1.875 15.3315 2.0067 15.6495 2.24112 15.8839C2.47554 16.1183 2.79348 16.25 3.125 16.25H14.375C14.7065 16.25 15.0245 16.1183 15.2589 15.8839C15.4933 15.6495 15.625 15.3315 15.625 15V1.25C15.625 0.918479 15.4933 0.600537 15.2589 0.366116C15.0245 0.131696 14.7065 0 14.375 0ZM14.375 15H3.125V1.25H14.375V15Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b3-text">{{ __('profile.profile.menu_user_guide') }}</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('logout') }}">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path
                                d="M6.25 14.375C6.25 14.5408 6.18415 14.6997 6.06694 14.8169C5.94973 14.9342 5.79076 15 5.625 15H0.625C0.45924 15 0.300269 14.9342 0.183058 14.8169C0.0658481 14.6997 0 14.5408 0 14.375V0.625C0 0.45924 0.0658481 0.300269 0.183058 0.183058C0.300269 0.0658481 0.45924 0 0.625 0H5.625C5.79076 0 5.94973 0.0658481 6.06694 0.183058C6.18415 0.300269 6.25 0.45924 6.25 0.625C6.25 0.79076 6.18415 0.949731 6.06694 1.06694C5.94973 1.18415 5.79076 1.25 5.625 1.25H1.25V13.75H5.625C5.79076 13.75 5.94973 13.8158 6.06694 13.9331C6.18415 14.0503 6.25 14.2092 6.25 14.375ZM14.8172 7.05781L11.6922 3.93281C11.5749 3.81554 11.4159 3.74965 11.25 3.74965C11.0841 3.74965 10.9251 3.81554 10.8078 3.93281C10.6905 4.05009 10.6247 4.20915 10.6247 4.375C10.6247 4.54085 10.6905 4.69991 10.8078 4.81719L12.8664 6.875H5.625C5.45924 6.875 5.30027 6.94085 5.18306 7.05806C5.06585 7.17527 5 7.33424 5 7.5C5 7.66576 5.06585 7.82473 5.18306 7.94194C5.30027 8.05915 5.45924 8.125 5.625 8.125H12.8664L10.8078 10.1828C10.6905 10.3001 10.6247 10.4591 10.6247 10.625C10.6247 10.7909 10.6905 10.9499 10.8078 11.0672C10.9251 11.1845 11.0841 11.2503 11.25 11.2503C11.4159 11.2503 11.5749 11.1845 11.6922 11.0672L14.8172 7.94219C14.8753 7.88414 14.9214 7.81521 14.9529 7.73934C14.9843 7.66346 15.0005 7.58213 15.0005 7.5C15.0005 7.41787 14.9843 7.33654 14.9529 7.26066C14.9214 7.18479 14.8753 7.11586 14.8172 7.05781Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b3-text">{{ __('profile.profile.menu_logout') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
@include("tickets.technicians.headerlogactivity")


 <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                        <div class="modal-content rounded">
                            <div class="modal-header border-bottom">
                                <input type="search" class="form-control fs-3" placeholder="Search here" id="search" />
                                <a href="javascript:void(0)" data-bs-dismiss="modal" class="lh-1">
                                {{-- <i class="ti ti-x fs-5 ms-3"></i> --}}
                                <i class="bi bi-x fs-7 ms-3"></i>
                                </a>
                            </div>
                            <div class="modal-body message-body" data-simplebar="">
                                <h5 class="mb-0 fs-5 p-1">Quick Page Links</h5>
                                <ul class="list mb-0 py-2">
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Analytics</span>
                                    <span class="fs-2 d-block text-body-secondary">/dashboards/dashboard1</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">eCommerce</span>
                                    <span class="fs-2 d-block text-body-secondary">/dashboards/dashboard2</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">CRM</span>
                                    <span class="fs-2 d-block text-body-secondary">/dashboards/dashboard3</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Contacts</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/contacts</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Posts</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/blog/posts</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Detail</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/blog/detail/streaming-video-way-before-it-was-cool-go-dark-tomorrow</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Shop</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/ecommerce/shop</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Modern</span>
                                    <span class="fs-2 d-block text-body-secondary">/dashboards/dashboard1</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Dashboard</span>
                                    <span class="fs-2 d-block text-body-secondary">/dashboards/dashboard2</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Contacts</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/contacts</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Posts</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/blog/posts</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Detail</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/blog/detail/streaming-video-way-before-it-was-cool-go-dark-tomorrow</span>
                                    </a>
                                </li>
                                <li class="p-1 mb-1 bg-hover-light-black rounded px-2">
                                    <a href="javascript:void(0)">
                                    <span class="fs-3 text-dark fw-normal d-block">Shop</span>
                                    <span class="fs-2 d-block text-body-secondary">/apps/ecommerce/shop</span>
                                    </a>
                                </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        // ---- Global AJAX setup (CSRF) ----
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ---- Company Selector (unchanged) ----
        const DEFAULT_COMPANY_ID = @json($dashboardCompanyId);
        const COMPANY_KEY = 'Default_Company';
        const $companySelect = $("#amg-company-select");
        let isInitializing = true;
        function getInitials(name) {
            if (!name) return '';
            let words = name.trim().split(' ');
            return words.length === 1
                ? words[0][0].toUpperCase()
                : (words[0][0] + words[words.length - 1][0]).toUpperCase();
        }
        function updateHeaderLogo(company) {
            let $logo = $("#loginCompanyLogo");

            if (company.logo) {
                if (!$logo.is('img')) {
                    $logo.replaceWith(`<img id="loginCompanyLogo" class="client-brand-img"/>`);
                    $logo = $("#loginCompanyLogo");
                }
                $logo.attr("src", `${company.logo}`);
            } else {
                let initials = company.id == 0 ? 'ALL' : getInitials(company.text);
                if ($logo.is('img')) {
                    $logo.replaceWith(`
                        <div id="loginCompanyLogo" class="client-brand-img"
                            style="width:40px;height:40px;border-radius:50%;
                            background:#e3eafc;color:#2c3e50;
                            display:flex;align-items:center;justify-content:center;
                            font-weight:bold;">
                            ${initials}
                        </div>
                    `);
                } else {
                    $logo.text(initials);
                }
            }
        }
        function formatOption(option) {
            if (!option.id) return option.text;
            let data = option.element ? $(option.element).data('data') : option;
            if (data.id == 0) {
                return $(`
                    <span style="display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;border-radius:50%;
                            background:#dfe6e9;display:flex;align-items:center;
                            justify-content:center;font-size:12px;font-weight:bold;">
                            ALL
                        </span>
                        <span>${data.text}</span>
                    </span>
                `);
            }
            let initials = getInitials(data.text);
            let logoHtml = data.logo
                ? `<img src="${data.logo}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" />`
                : `<span style="width:28px;height:28px;border-radius:50%;
                    background:#e3eafc;color:#2c3e50;font-size:12px;
                    font-weight:bold;display:flex;align-items:center;
                    justify-content:center;">
                    ${initials}
                </span>`;
            return $(`
                <span style="display:flex;align-items:center;gap:10px;">
                    ${logoHtml}
                    <span>${data.text}</span>
                </span>
            `);
        }
        function formatSelected(option) {
            let data = option.element ? $(option.element).data('data') : option;
            if (data.id == 0) return "All";
            return data.text || option.text;
        }
        $companySelect.select2({
            width: '260px',
            placeholder: "Select Company",
            minimumResultsForSearch: Infinity,
            templateResult: formatOption,
            templateSelection: formatSelected
        });
        $.ajax({
            type: 'GET',
            url: "{{ url('getCompanyByUserAccess') }}",
            success: function (response) {
                if (!response.results?.length) return;
                $companySelect.empty();
                let storedCompany = JSON.parse(localStorage.getItem(COMPANY_KEY) || 'null');
                let selectedCompany = null;
                let selectedId = (storedCompany && storedCompany.id !== undefined) ? storedCompany.id : DEFAULT_COMPANY_ID;
                let isAllSelected = (selectedId == 0 || !selectedId);
        
                let allOption = new Option("All", 0, isAllSelected, isAllSelected);
                $(allOption).data('data', { id: 0, text: "All", logo: null });
                $companySelect.append(allOption);

                if (isAllSelected) {
                    selectedCompany = { id: 0, text: "All", logo: null };
                }
                response.results.forEach(company => {
                    let isSelected = (company.id == selectedId);
                    if (isSelected) selectedCompany = company;
                    let option = new Option(company.text, company.id, isSelected, isSelected);
                    $(option).data('data', company);
                    $companySelect.append(option);
                });
                $companySelect.trigger('change');
                if (selectedId == 0) {
                    $companySelect.val(0).trigger('change');
                }
                isInitializing = false;
                if (selectedCompany) {
                    localStorage.setItem(COMPANY_KEY, JSON.stringify(selectedCompany));
                    updateHeaderLogo(selectedCompany);
                }
            }
        });
        $companySelect.on('change', function () {
            if (isInitializing) return;
            let data = $(this).select2('data')[0];
            if (data.id === undefined || data.id === null) return;
            let fullData = data.element ? $(data.element).data('data') : data;
            let previous = JSON.parse(localStorage.getItem(COMPANY_KEY) || '{}');
            if (String(previous.id) === String(fullData.id)) return;
            localStorage.setItem(COMPANY_KEY, JSON.stringify(fullData));
            updateHeaderLogo(fullData);
            $.post("{{ route('user.store.default.company') }}", {
                _token: "{{ csrf_token() }}",
                company_id: fullData.id,
                time: Date.now()
            }).done(() => location.reload())
            .fail(err => console.error('Save failed', err));
        });
        window.addEventListener('storage', function (e) {
            if (e.key === COMPANY_KEY && e.newValue) {
                location.reload();
            }
        });

        // ---- Technician Availability ----
        var tech = "{{ $isTechnician }}";
        if (tech == 1) {
            loadActivities();
            getTechCurrentstatus();
        }

        function loadActivities() {
            $.ajax({
                url: "{{ route('getLogActivities') }}",
                method: "GET",
                success: function (res) {
                    if (res.status == 'success') {
                        $('#activity_ids').html('<option value="">{{ trans('technician_status.select_activity') }}</option>');
                        $.each(res.data, function (i, k) {
                            $('#activity_ids').append(new Option(k.name, k.id));
                        });
                    }
                }
            });
        }

        function updateAvailabilityLabel() {
            var isChecked = $('#techAvability').is(':checked');
            var labelText = isChecked ? '{{ __('header.header.status_available') }}' : '{{ __('header.header.status_unavailable') }}';
            $('.status-label').text(labelText);
            if (isChecked) {
                $('.left__arrow-btn').removeClass('hide');
                $('.right__arrow-btn').addClass('hide');
            } else {
                $('.left__arrow-btn').addClass('hide');
                $('.right__arrow-btn').removeClass('hide');
            }
        }

        function getTechCurrentstatus() {
            $.ajax({
                url: "{{ route('getTechCurrentStatus') }}",
                method: "GET",
                success: function(res){
                    if(res && res.status == 'success') {
                        // Initially hide arrows as per your design
                        $('.left__arrow-btn').addClass('hide');
                        $('.right__arrow-btn').removeClass('hide');
                        if(res.is_logged_in == true) {
                            $('#techAvability').prop('checked', true).trigger('change', true);
                            $('.left__arrow-btn').removeClass('hide');
                            $('.right__arrow-btn').addClass('hide');
                        } else {
                            $('#techAvability').prop('checked', false);
                        }
                        updateAvailabilityLabel();
                    }
                }
            });
        }

        var activitySubmitted = false;
        $('#headertechncianActivitiesModal').on('show.bs.modal', function () {
            activitySubmitted = false;
        });
        $('#headertechncianActivitiesModal').on('hidden.bs.modal', function () {
            if (!activitySubmitted) {
                $('#techAvability').prop('checked', true).trigger('change', false);
                updateAvailabilityLabel();
            }
            activitySubmitted = false;
        });

        // ---- Toggle change handler ----
        $('#techAvability').on('change', function (e, ep = undefined) {
            if (ep === false) {
                return false; 
            }
            var isAvailable = $(this).is(':checked');
            updateAvailabilityLabel();

            if (!isAvailable) {
                $('#activity_comments').val('');
                $('#headertechncianActivitiesModal').modal("show");
            } else {           
                $.ajax({
                    url: "{{ route('logUserActivity') }}",
                    method: "POST",
                    success: function (res) {
                    },
                    error: function() {
                    }
                });
            }
        });

        let isSubmitting = false;
        $(document).on('click', '.headersaveActivityModal', function () {
            if (isSubmitting) return;
            isSubmitting = true;

            var data = {
                activity: $('#activity_ids').val(),
                activity_comment: $('#activity_comments').val(),
                logActivityForOtherUser: $('#logActivityForOtherUser').val() || null,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if (!data.activity) {
                alert('Please select activity');
                isSubmitting = false;
                return false;
            }

            $.ajax({
                url: "{{ url('technician/log-activity') }}/" + (data.logActivityForOtherUser ?? ''),
                method: "POST",
                data: data,
                success: function (res) {
                    if (res.status == 'success') {
                        activitySubmitted = true;
                        $('#headertechncianActivitiesModal').modal('hide');
                        $('#techAvability').prop('checked', false);
                        updateAvailabilityLabel();
                        sweetAlert('center', 'success', res);
                        $(document).trigger('technicianActivitySaved');
                    } else {
                        $('#techAvability').prop('checked', true);
                        updateAvailabilityLabel();
                        sweetAlert('center', 'error', res);
                    }
                    isSubmitting = false;
                },
                error: function () {
                    $('#techAvability').prop('checked', true);
                    updateAvailabilityLabel();
                    isSubmitting = false;
                    sweetAlert('center', 'error', { message: 'Something went wrong.' });
                }
            });
        });

        $(".closeActivityModal").on('click', function(e){
            $('#headertechncianActivitiesModal').modal('hide');
        });

        $(".saveTechLogActivity").click(function() {
            var data = {
                activity: $('#activity_id').val(),
                activity_comment: $('#activity_comment').val(),
                logActivityForOtherUser: $('#logActivityForOtherUser').val() == '' ? null : $('#logActivityForOtherUser').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            $.ajax({
                url: "{{ url('technician/log-activity') }}/" + data.logActivityForOtherUser,
                method: 'post',
                data: data,
                success: function(res) {
                    if(res.status == "success") {
                        sweetAlert('center', 'success', res);
                        $("#techncianLogActivitiesModal").modal("hide");
                        $(document).trigger('technicianActivitySaved');
                    } else {
                        sweetAlert('center', 'error', res);
                    }
                }
            });
        });

        // ---- Light/Dark mode & locale (unchanged) ----
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);

    document.addEventListener('click', function (e) {
        const localeLink = e.target.closest('.set-locale');
        if (!localeLink) return;
        e.preventDefault();
        const locale = localeLink.dataset.locale;
        if (!locale) return;
        let url = "{{ route('setLocale', [':locale']) }}";
        url = url.replace(':locale', locale);
        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true || data.status === 'true') {
                    window.location.reload();
                }
            });
    });

    // ---- Header profile dropdown (unchanged) ----
    $(document).on('click', '.status-wrapper', function (e) {
        e.stopPropagation();
    });

    $(function () {
        $('#statusBtn').on('click', function (e) {
            e.stopPropagation();
            const isOpen = $('#statusMenu').toggleClass('show').hasClass('show');
            $(this).toggleClass('open', isOpen);
            $('#chevron').toggleClass('rotated', isOpen);
        });

        $('.status-option').on('click', function (e) {
            e.stopPropagation();
            const dot = $(this).data('dot');
            const val = $(this).data('value');
            $('.status-option').removeClass('active');
            $(this).addClass('active');
            $('#currentDot').attr('class', 'status-dot ' + dot);
            $('#currentLabel').text('(' + val + ')');
            $('#statusMenu').removeClass('show');
            $('#statusBtn').removeClass('open');
            $('#chevron').removeClass('rotated');
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.status-wrapper').length) {
                $('#statusMenu').removeClass('show');
                $('#statusBtn').removeClass('open');
                $('#chevron').removeClass('rotated');
            }
        });
    });
    });
</script>