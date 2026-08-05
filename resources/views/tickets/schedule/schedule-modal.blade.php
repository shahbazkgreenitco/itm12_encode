{{-- @page-meta
{
  "page_no": "TS01C-26",
  "file": "schedule-modal.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@php
    $textIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor" /></svg>
SVG;
    $companyIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="currentColor" /></svg>
SVG;
    $userIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 10C11.0878 10 12.1512 9.67676 13.0557 9.07295C13.9601 8.46914 14.6651 7.61238 15.0813 6.60607C15.4976 5.59977 15.6065 4.49389 15.3943 3.42719C15.1821 2.36049 14.6583 1.38128 13.8891 0.610873C13.1199 -0.159534 12.1407 -0.683425 11.0741 -0.895666C10.0074 -1.10791 8.90154 -0.998935 7.89523 -0.582696C6.88893 -0.166456 6.03217 0.538614 5.42836 1.44306C4.82455 2.3475 4.5013 3.41083 4.5013 4.49866C4.50285 5.95481 5.0826 7.35101 6.11258 8.38099C7.14257 9.41097 8.53877 9.99072 9.99491 9.99228L10 10ZM10 1.24866C10.8407 1.24866 11.6629 1.49838 12.3612 1.96618C13.0596 2.43398 13.6031 3.0988 13.9237 3.87407C14.2443 4.64934 14.3276 5.50203 14.1631 6.32476C13.9986 7.14749 13.5937 7.90316 12.9993 8.49757C12.4049 9.09197 11.6492 9.49685 10.8265 9.66137C10.0038 9.82589 9.15107 9.74258 8.3758 9.42199C7.60053 9.10141 6.93571 8.55789 6.46791 7.85951C6.00011 7.16112 5.75039 6.33896 5.75039 5.49823C5.75169 4.3712 6.19959 3.29081 6.9985 2.49189C7.79741 1.69298 8.87781 1.24508 10.0048 1.24378L10 1.24866ZM17.5 18.75V17.4988C17.5 15.8409 16.8415 14.2513 15.6694 13.0793C14.4973 11.9072 12.9076 11.2487 11.2498 11.2487H8.75019C7.09237 11.2487 5.50268 11.9072 4.33061 13.0793C3.15854 14.2513 2.5 15.8409 2.5 17.4988V18.75H3.75V17.4988C3.75 16.1728 4.27672 14.9009 5.21434 13.9633C6.15196 13.0257 7.42393 12.499 8.75019 12.499H11.2498C12.5761 12.499 13.848 13.0257 14.7857 13.9633C15.7233 14.9009 16.25 16.1728 16.25 17.4988V18.75H17.5Z" fill="currentColor" /></svg>
SVG;
    $listIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M17.5 5H7.5C7.33424 5 7.17527 4.93415 7.05806 4.81694C6.94085 4.69973 6.875 4.54076 6.875 4.375C6.875 4.20924 6.94085 4.05027 7.05806 3.93306C7.17527 3.81585 7.33424 3.75 7.5 3.75H17.5C17.6658 3.75 17.8247 3.81585 17.9419 3.93306C18.0592 4.05027 18.125 4.20924 18.125 4.375C18.125 4.54076 18.0592 4.69973 17.9419 4.81694C17.8247 4.93415 17.6658 5 17.5 5ZM17.5 10.625H7.5C7.33424 10.625 7.17527 10.5592 7.05806 10.4419C6.94085 10.3247 6.875 10.1658 6.875 10C6.875 9.83424 6.94085 9.67527 7.05806 9.55806C7.17527 9.44085 7.33424 9.375 7.5 9.375H17.5C17.6658 9.375 17.8247 9.44085 17.9419 9.55806C18.0592 9.67527 18.125 9.83424 18.125 10C18.125 10.1658 18.0592 10.3247 17.9419 10.4419C17.8247 10.5592 17.6658 10.625 17.5 10.625ZM17.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H17.5C17.6658 15 17.8247 15.0658 17.9419 15.1831C18.0592 15.3003 18.125 15.4592 18.125 15.625C18.125 15.7908 18.0592 15.9497 17.9419 16.0669C17.8247 16.1842 17.6658 16.25 17.5 16.25ZM3.75 5.625C3.5025 5.625 3.26041 5.55169 3.05419 5.41434C2.84798 5.27698 2.68675 5.08176 2.59114 4.85335C2.49554 4.62495 2.47005 4.37361 2.51779 4.13114C2.56554 3.88867 2.68434 3.66587 2.85901 3.49121C3.03367 3.31654 3.25647 3.19773 3.49894 3.14999C3.74141 3.10225 3.99275 3.12774 4.22115 3.22334C4.44956 3.31894 4.64478 3.48018 4.78214 3.68639C4.91949 3.8926 4.9928 4.13469 4.9928 4.38219C4.9928 4.71369 4.86119 5.03176 4.62759 5.26536C4.394 5.49895 4.07594 5.62957 3.74444 5.62957L3.75 5.625ZM3.75 11.25C3.5025 11.25 3.26041 11.1767 3.05419 11.0393C2.84798 10.902 2.68675 10.7068 2.59114 10.4784C2.49554 10.2499 2.47005 9.99861 2.51779 9.75614C2.56554 9.51367 2.68434 9.29087 2.85901 9.11621C3.03367 8.94154 3.25647 8.82273 3.49894 8.77499C3.74141 8.72725 3.99275 8.75274 4.22115 8.84834C4.44956 8.94394 4.64478 9.10518 4.78214 9.31139C4.91949 9.5176 4.9928 9.75969 4.9928 10.0072C4.9928 10.3387 4.86119 10.6568 4.62759 10.8904C4.394 11.1239 4.07594 11.2546 3.74444 11.2546L3.75 11.25ZM3.75 16.875C3.5025 16.875 3.26041 16.8017 3.05419 16.6643C2.84798 16.527 2.68675 16.3318 2.59114 16.1034C2.49554 15.8749 2.47005 15.6236 2.51779 15.3811C2.56554 15.1387 2.68434 14.9159 2.85901 14.7412C3.03367 14.5665 3.25647 14.4477 3.49894 14.4C3.74141 14.3522 3.99275 14.3777 4.22115 14.4733C4.44956 14.5689 4.64478 14.7302 4.78214 14.9364C4.91949 15.1426 4.9928 15.3847 4.9928 15.6322C4.9928 15.9637 4.86119 16.2818 4.62759 16.5154C4.394 16.7489 4.07594 16.8796 3.74444 16.8796L3.75 16.875Z" fill="currentColor" /></svg>
SVG;
    $clockIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 1.875C8.39303 1.875 6.82214 2.35152 5.486 3.24431C4.14985 4.1371 3.10844 5.40605 2.49348 6.8907C1.87852 8.37535 1.71762 10.009 2.03112 11.5851C2.34463 13.1612 3.11846 14.6089 4.25476 15.7452C5.39107 16.8815 6.8388 17.6554 8.4149 17.9689C9.99099 18.2824 11.6247 18.1215 13.1093 17.5065C14.594 16.8916 15.8629 15.8502 16.7557 14.514C17.6485 13.1779 18.125 11.607 18.125 10C18.1226 7.84581 17.2657 5.78051 15.7427 4.25727C14.2196 2.73403 12.1543 1.87738 10 1.875ZM10 16.875C8.64026 16.875 7.31105 16.4718 6.18046 15.7164C5.04987 14.9609 4.16868 13.8872 3.64833 12.6309C3.12798 11.3747 2.99183 9.99237 3.2571 8.65875C3.52238 7.32513 4.17716 6.10013 5.13864 5.13864C6.10013 4.17716 7.32514 3.52237 8.65876 3.2571C9.99237 2.99183 11.3747 3.12798 12.6309 3.64833C13.8872 4.16868 14.9609 5.04987 15.7164 6.18046C16.4718 7.31105 16.875 8.64026 16.875 10C16.8729 11.8227 16.1479 13.5702 14.8591 14.8591C13.5702 16.1479 11.8227 16.8729 10 16.875ZM13.5672 12.6828C13.6842 12.8001 13.7498 12.9591 13.7498 13.125C13.7498 13.2909 13.6842 13.4499 13.5672 13.5672C13.4499 13.6842 13.2909 13.7498 13.125 13.7498C12.9591 13.7498 12.8001 13.6842 12.6828 13.5672L9.55781 10.4422C9.44111 10.3249 9.37537 10.1658 9.375 10V6.25C9.375 6.08424 9.44085 5.92527 9.55806 5.80806C9.67527 5.69085 9.83424 5.625 10 5.625C10.1658 5.625 10.3247 5.69085 10.4419 5.80806C10.5592 5.92527 10.625 6.08424 10.625 6.25V9.74141L13.5672 12.6828Z" fill="currentColor" /></svg>
SVG;
    $desktopIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M17.5 2.5H2.5C2.16848 2.5 1.85054 2.6317 1.61612 2.86612C1.3817 3.10054 1.25 3.41848 1.25 3.75V13.75C1.25 14.0815 1.3817 14.3995 1.61612 14.6339C1.85054 14.8683 2.16848 15 2.5 15H8.125V16.875H6.25C6.08424 16.875 5.92527 16.9408 5.80806 17.0581C5.69085 17.1753 5.625 17.3342 5.625 17.5C5.625 17.6658 5.69085 17.8247 5.80806 17.9419C5.92527 18.0592 6.08424 18.125 6.25 18.125H13.75C13.9158 18.125 14.0747 18.0592 14.1919 17.9419C14.3092 17.8247 14.375 17.6658 14.375 17.5C14.375 17.3342 14.3092 17.1753 14.1919 17.0581C14.0747 16.9408 13.9158 16.875 13.75 16.875H11.875V15H17.5C17.8315 15 18.1495 14.8683 18.3839 14.6339C18.6183 14.3995 18.75 14.0815 18.75 13.75V3.75C18.75 3.41848 18.6183 3.10054 18.3839 2.86612C18.1495 2.6317 17.8315 2.5 17.5 2.5ZM10.625 16.875H9.375V15H10.625V16.875ZM17.5 13.75H2.5V3.75H17.5V13.75Z" fill="currentColor" /></svg>
SVG;
    $calendarIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H15V1.875C15 1.70924 14.9342 1.55027 14.8169 1.43306C14.6997 1.31585 14.5408 1.25 14.375 1.25C14.2092 1.25 14.0503 1.31585 13.9331 1.43306C13.8158 1.55027 13.75 1.70924 13.75 1.875V2.5H6.25V1.875C6.25 1.70924 6.18415 1.55027 6.06694 1.43306C5.94973 1.31585 5.79076 1.25 5.625 1.25C5.45924 1.25 5.30027 1.31585 5.18306 1.43306C5.06585 1.55027 5 1.70924 5 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5 3.75V4.375C5 4.54076 5.06585 4.69973 5.18306 4.81694C5.30027 4.93415 5.45924 5 5.625 5C5.79076 5 5.94973 4.93415 6.06694 4.81694C6.18415 4.69973 6.25 4.54076 6.25 4.375V3.75H13.75V4.375C13.75 4.54076 13.8158 4.69973 13.9331 4.81694C14.0503 4.93415 14.2092 5 14.375 5C14.5408 5 14.6997 4.93415 14.8169 4.81694C14.9342 4.69973 15 4.54076 15 4.375V3.75H16.25V6.25H3.75V3.75H5ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="currentColor" /></svg>
SVG;
    $mailIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.875 3.75H3.125C2.79348 3.75 2.47554 3.8817 2.24112 4.11612C2.0067 4.35054 1.875 4.66848 1.875 5V15C1.875 15.3315 2.0067 15.6495 2.24112 15.8839C2.47554 16.1183 2.79348 16.25 3.125 16.25H16.875C17.2065 16.25 17.5245 16.1183 17.7589 15.8839C17.9933 15.6495 18.125 15.3315 18.125 15V5C18.125 4.66848 17.9933 4.35054 17.7589 4.11612C17.5245 3.8817 17.2065 3.75 16.875 3.75ZM15.7422 5L10 9.71875L4.25781 5H15.7422ZM3.125 15V5.64063L9.60156 10.9609C9.71631 11.0576 9.86075 11.1105 10.0098 11.1105C10.1589 11.1105 10.3033 11.0576 10.418 10.9609L16.875 5.64063V15H3.125Z" fill="currentColor" /></svg>
SVG;
    $attachIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.5625 9.05781L9.64687 15.9734C8.76042 16.8599 7.55875 17.3581 6.30469 17.3581C5.05062 17.3581 3.84895 16.8599 2.9625 15.9734C2.07605 15.087 1.57788 13.8853 1.57788 12.6312C1.57788 11.3772 2.07605 10.1755 2.9625 9.28906L9.87812 2.37344C10.4718 1.7797 11.2765 1.44531 12.1156 1.44531C12.9547 1.44531 13.7594 1.7797 14.3531 2.37344C14.9469 2.96719 15.2812 3.77187 15.2812 4.61094C15.2812 5.45 14.9469 6.25469 14.3531 6.84844L7.43 13.764C7.13313 14.0609 6.7308 14.2281 6.31156 14.2281C5.89233 14.2281 5.49 14.0609 5.19312 13.764C4.89625 13.4672 4.72906 13.0648 4.72906 12.6456C4.72906 12.2264 4.89625 11.824 5.19312 11.5272L11.5781 5.14219" stroke="#f12f35" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
SVG;
@endphp

<div class="amg-modal amg-form-modal modal fade" id="scheduleModal"  tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="schedule-mdl-frm" name="scheduleMdlForm" method="post" action="#" class="form-horizontal w-100 amg-form-theme" enctype="multipart/form-data" onsubmit="return false;" autocomplete="off">
            @csrf
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="for_action" name="for_action" value="">
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">Create Scheduled</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div id="not_show_in_custom_action">
                            <div class="row my-4 px-4">
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label for="company_id" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.company") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $companyIcon !!}</span>
                                            <select name="company_id" id="company_id" class="form-select"></select>
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label for="department_id" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.department") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $companyIcon !!}</span>
                                            <select name="department_id" id="department_id" class="form-select"></select>
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: User Ticket & Problem Category --}}
                            <div class="row mb-4 px-4">
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label for="creator_id" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.User_Ticket") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $userIcon !!}</span>
                                            <select name="creator_id" id="creator_id" class="form-select"></select>
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label for="problem_category_id" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.Problem_Category") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $listIcon !!}</span>
                                            <select name="problem_category_id" id="problem_category_id" class="form-select"></select>
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 3: Sub Category & Priority + TAT --}}
                            <div class="row mb-4 px-4" id="sub_category_id_cvr">
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label id="sub_category_id_lbl" for="sub_category_id" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.sub_category") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $listIcon !!}</span>
                                            <select name="sub_category_id" id="sub_category_id" class="form-select"></select>
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 priority">
                                    <div class="amg-form-field d-flex align-items-center gap-2">
                                        {{-- Priority --}}
                                        <div class="flex-grow-1">
                                            <label for="priority_id" class="form-label b1-text mb-1 required">{{ trans("content.service_ticket_fields.Priority") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{!! $listIcon !!}</span>
                                                <select name="priority_id" id="priority_id" class="form-select" @if(!empty($action_controls) && $action_controls['ctrl_priority'] !=1) disabled @endif></select>
                                            </div>
                                            <div class="amg-form-error-wrap ms-0 w-100"></div>
                                        </div>
                                        {{-- TAT --}}
                                        <div class="flex-grow-1">
                                            <label for="tat" class="form-label b1-text mb-1 required">{{ trans("content.service_ticket_fields.TAT_Hrs") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{!! $clockIcon !!}</span>
                                                <input type="text" name="tat" id="tat" class="form-control" placeholder="0" @if(!empty($action_controls) && $action_controls['ctrl_tat'] !=1) disabled @endif />
                                                <span class="input-group-text">Hrs</span>
                                            </div>
                                            <div class="amg-form-error-wrap ms-0 w-100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end not_show_in_custom_action --}}

                        {{-- Row 4: Device --}}
                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="device_id" class="form-label b1-text me-2 mb-0 ">{{ trans("content.service_ticket_fields.Device") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $desktopIcon !!}</span>
                                        <select name="device_id" id="device_id" class="form-select" placeholder="{{ trans('content.service_ticket_fields.Choose_Device') }}"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Row 5: Recurrence Pattern --}}
                        <div class="row mb-4 px-4" id="recurrent_wrapper">
                            <div class="col-md-12">
                                <fieldset class="scheduler-border border rounded-3 p-3">
                                    <legend class="scheduler-border float-none w-auto px-2 fs-6 fw-semibold">
                                        {!! $calendarIcon !!} {{ trans('ticket_schedular.schedular_listing_fields.recurrence_pattern') }}
                                    </legend>
                                    <div class="row">
                                        {{-- Left: Radio Tabs --}}
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" checked name="inlineRadioOptions" id="optDaily" value="option1" type="radio" data-target="#daily">
                                                    <label class="form-check-label" for="optDaily">{{ trans('ticket_schedular.schedular_listing_fields.daily') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" name="inlineRadioOptions" id="optWeekly" value="option2" type="radio" data-target="#weekly">
                                                    <label class="form-check-label" for="optWeekly">{{ trans('ticket_schedular.schedular_listing_fields.weekly') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" name="inlineRadioOptions" id="optMonthly" value="option3" type="radio" data-target="#monthly">
                                                    <label class="form-check-label" for="optMonthly">{{ trans('ticket_schedular.schedular_listing_fields.monthly') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" name="inlineRadioOptions" id="optYearly" value="option4" type="radio" data-target="#yearly">
                                                    <label class="form-check-label" for="optYearly">{{ trans('ticket_schedular.schedular_listing_fields.yearly') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right: Tab Content --}}
                                        <div class="col-md-9">
                                            <div class="tab-content">

                                                {{-- Daily --}}
                                                <div class="tab-pane active" id="daily">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" checked type="radio" name="eday" id="daily_all" value="everyday">
                                                        <label class="form-check-label" for="daily_all"> {{ trans('ticket_schedular.schedular_listing_fields.everyday') }}</label>
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="radio" name="eday" id="daily_weekday" value="weekday">
                                                        <label class="form-check-label" for="daily_weekday">{{ trans('ticket_schedular.schedular_listing_fields.every_weekday') }}</label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="b1-text">{{ trans('ticket_schedular.schedular_listing_fields.starts_at') }}</span>
                                                        <select class="form-select w-auto" name="daily_hour" id="daily_hour">
                                                            @for ($daily_hour = 0; $daily_hour < 24; $daily_hour++)
                                                                <option value="{{ $daily_hour }}" @if($daily_hour==12) selected @endif>{{ str_pad($daily_hour, 2, '0', STR_PAD_LEFT) }}</option>
                                                            @endfor
                                                        </select>
                                                        <span>:</span>
                                                        <select class="form-select w-auto" name="daily_minute" id="daily_minute">
                                                            @for ($daily_minute = 0; $daily_minute <= 59; $daily_minute++)
                                                                @if( $daily_minute==30)
                                                                <option value="{{ $daily_minute }}" selected="selected">{{ strlen($daily_minute) == 1 ? '0'.$daily_minute : $daily_minute }}</option>
                                                                @else
                                                                <option value="{{ $daily_minute }}">{{ strlen($daily_minute) == 1 ? '0'.$daily_minute : $daily_minute }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Weekly --}}
                                                <div class="tab-pane" id="weekly">
                                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                                        <div class="checkboxes">
                                                        @foreach(['Sunday'=>0,'Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6] as $day=>$val)
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox" name="days[]" value="{{ $val }}" id="day_{{ $val }}">
                                                                <label class="form-check-label" for="day_{{ $val }}">{{ $day }}</label>
                                                            </div>
                                                        @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="b1-text">{{ trans('ticket_schedular.schedular_listing_fields.starts_at') }}</span>
                                                        <select class="form-select w-auto" name="weekly_hour" id="weekly_hour">
                                                            @for ($weekly_hour = 0; $weekly_hour < 24; $weekly_hour++)
                                                                @if( $weekly_hour==12)
                                                                <option value="{{ $weekly_hour }}" selected="selected">{{ strlen($weekly_hour) == 1 ? '0'.$weekly_hour : $weekly_hour }}</option>
                                                                @else
                                                                <option value="{{ $weekly_hour }}">{{ strlen($weekly_hour) == 1 ? '0'.$weekly_hour : $weekly_hour }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                        <span>:</span>
                                                        <select class="form-select w-auto" name="weekly_minute" id="weekly_minute">
                                                            @for ($weekly_minute = 0; $weekly_minute <= 59; $weekly_minute++)
                                                                @if( $weekly_minute==30)
                                                                <option value="{{ $weekly_minute }}" selected="selected">{{ strlen($weekly_minute) == 1 ? '0'.$weekly_minute : $weekly_minute }}</option>
                                                                @else
                                                                <option value="{{ $weekly_minute }}">{{ strlen($weekly_minute) == 1 ? '0'.$weekly_minute : $weekly_minute }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Monthly --}}
                                                <div class="tab-pane" id="monthly">
                                                   <div class="d-flex align-items-center gap-2 mb-1 amg-form-field-row">
                                                        <input class="form-check-input" type="radio" name="monthly" id="monthly_once" value="month" checked>
                                                        <label class="form-check-label" for="monthly_once">
                                                            {{ trans('ticket_schedular.schedular_listing_fields.day') }}
                                                            <span class="d-inline-block position-relative align-middle">
                                                                <input type="text" class="form-control d-inline-block mx-1" style="width:60px;" value="1" name="monthly_date">
                                                                <div class="monthly-date-error"></div>
                                                            </span>
                                                            {{ trans('ticket_schedular.schedular_listing_fields.of_every') }}
                                                            <select class="form-select d-inline-block mx-1 w-auto" name="monthly_numbers" id="monthly_numbers">
                                                                <option value="1" selected>1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="6">6</option>
                                                            </select>
                                                            {{ trans('ticket_schedular.schedular_listing_fields.months') }}
                                                        </label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <input class="form-check-input" type="radio" name="monthly" id="monthly_condition" value="week">
                                                        <label class="form-check-label" for="monthly_condition">
                                                            {{ trans('ticket_schedular.schedular_listing_fields.the') }}
                                                            <select class="form-select d-inline-block mx-1 w-auto" name="monthly_week" id="monthly_week">
                                                                <option value="1" selected>{{ trans('ticket_schedular.schedular_listing_fields.first') }}</option>
                                                                <option value="2">{{ trans('ticket_schedular.schedular_listing_fields.second') }}</option>
                                                                <option value="3">{{ trans('ticket_schedular.schedular_listing_fields.third') }}</option>
                                                                <option value="4">{{ trans('ticket_schedular.schedular_listing_fields.fourth') }}</option>
                                                            </select>

                                                            <select class="form-select d-inline-block mx-1 w-auto" name="monthly_day" id="monthly_day">
                                                                <option value="MON" selected>{{ trans('ticket_schedular.schedular_listing_fields.monday') }}</option>
                                                                <option value="TUE">{{ trans('ticket_schedular.schedular_listing_fields.tuesday') }}</option>
                                                                <option value="WED">{{ trans('ticket_schedular.schedular_listing_fields.wednesday') }}</option>
                                                                <option value="THU">{{ trans('ticket_schedular.schedular_listing_fields.thursday') }}</option>
                                                                <option value="FRI">{{ trans('ticket_schedular.schedular_listing_fields.friday') }}</option>
                                                                <option value="SAT">{{ trans('ticket_schedular.schedular_listing_fields.saturday') }}</option>
                                                                <option value="SUN">{{ trans('ticket_schedular.schedular_listing_fields.sunday') }}</option>
                                                            </select>
                                                            {{ trans('ticket_schedular.schedular_listing_fields.of') }} {{ trans('ticket_schedular.schedular_listing_fields.every') }}
                                                            <select class="form-select d-inline-block mx-1 w-auto" name="monthly_month" id="monthly_month">
                                                                <option value="1" selected>1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="6">6</option>
                                                            </select>
                                                            month(s)
                                                        </label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="b1-text">{{ trans('ticket_schedular.schedular_listing_fields.starts_at') }}</span>
                                                        <select class="form-select w-auto" name="monthly_hour" id="monthly_hour">
                                                           @for ($monthly_hour = 0; $monthly_hour < 24; $monthly_hour++)
                                                                @if( $monthly_hour==12)
                                                                <option value="{{ $monthly_hour }}" selected="selected">{{ strlen($monthly_hour) == 1 ? '0'.$monthly_hour : $monthly_hour }}</option>
                                                                @else
                                                                <option value="{{ $monthly_hour }}">{{ strlen($monthly_hour) == 1 ? '0'.$monthly_hour : $monthly_hour }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                        <span>:</span>
                                                        <select class="form-select w-auto" name="monthly_minute" id="monthly_minute">
                                                            @for ($monthly_minute = 0; $monthly_minute <= 59; $monthly_minute++)
                                                                @if( $monthly_minute==30)
                                                                <option value="{{ $monthly_minute }}" selected="selected">{{ strlen($monthly_minute) == 1 ? '0'.$monthly_minute : $monthly_minute }}</option>
                                                                @else
                                                                <option value="{{ $monthly_minute }}">{{ strlen($monthly_minute) == 1 ? '0'.$monthly_minute : $monthly_minute }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Yearly --}}
                                                <div class="tab-pane" id="yearly">
                                                  <div class="yearly-date-container amg-form-field-row">
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <input class="form-check-input" type="radio" name="yearly" id="yearly_once" value="radio2" checked>
                                                            <label class="form-check-label" for="yearly_once">
                                                                {{ trans('ticket_schedular.schedular_listing_fields.every') }}
                                                                <select class="form-select d-inline-block mx-1 w-auto" name="yearly_months" id="yearly_months">
                                                                    <option value="1" selected>{{ trans('ticket_schedular.schedular_listing_fields.january') }}</option>
                                                                    <option value="2">{{ trans('ticket_schedular.schedular_listing_fields.february') }}</option>
                                                                    <option value="3">{{ trans('ticket_schedular.schedular_listing_fields.march') }}</option>
                                                                    <option value="4">{{ trans('ticket_schedular.schedular_listing_fields.april') }}</option>
                                                                    <option value="5">{{ trans('ticket_schedular.schedular_listing_fields.may') }}</option>
                                                                    <option value="6">{{ trans('ticket_schedular.schedular_listing_fields.june') }}</option>
                                                                    <option value="7">{{ trans('ticket_schedular.schedular_listing_fields.july') }}</option>
                                                                    <option value="8">{{ trans('ticket_schedular.schedular_listing_fields.august') }}</option>
                                                                    <option value="9">{{ trans('ticket_schedular.schedular_listing_fields.september') }}</option>
                                                                    <option value="10">{{ trans('ticket_schedular.schedular_listing_fields.october') }}</option>
                                                                    <option value="11">{{ trans('ticket_schedular.schedular_listing_fields.november') }}</option>
                                                                    <option value="12">{{ trans('ticket_schedular.schedular_listing_fields.december') }}</option>
                                                                </select>
                                                                <input type="text" class="form-control d-inline-block mx-1" style="width:60px;" value="1" name="yearly_day">
                                                            </label>
                                                        </div>
                                                        <div class="yearly-date-error"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <input class="form-check-input" type="radio" name="yearly" id="yearly_condition" value="radio3">
                                                        <label class="form-check-label" for="yearly_condition">
                                                            {{ trans('ticket_schedular.schedular_listing_fields.the') }}
                                                            <select class="form-select d-inline-block mx-1 w-auto" name="yearly_months_week" id="yearly_months_week">
                                                                <option value="1" selected>{{ trans('ticket_schedular.schedular_listing_fields.first') }}</option>
                                                                <option value="2">{{ trans('ticket_schedular.schedular_listing_fields.second') }}</option>
                                                                <option value="3">{{ trans('ticket_schedular.schedular_listing_fields.third') }}</option>
                                                                <option value="4">{{ trans('ticket_schedular.schedular_listing_fields.fourth') }}</option>
                                                            </select>

                                                            <select class="form-select d-inline-block mx-1 w-auto" name="yearly_months_week_day" id="yearly_months_week_day">
                                                                <option value="MON" selected>{{ trans('ticket_schedular.schedular_listing_fields.monday') }}</option>
                                                                <option value="TUE">{{ trans('ticket_schedular.schedular_listing_fields.tuesday') }}</option>
                                                                <option value="WED">{{ trans('ticket_schedular.schedular_listing_fields.wednesday') }}</option>
                                                                <option value="THU">{{ trans('ticket_schedular.schedular_listing_fields.thursday') }}</option>
                                                                <option value="FRI">{{ trans('ticket_schedular.schedular_listing_fields.friday') }}</option>
                                                                <option value="SAT">{{ trans('ticket_schedular.schedular_listing_fields.saturday') }}</option>
                                                                <option value="SUN">{{ trans('ticket_schedular.schedular_listing_fields.sunday') }}</option>
                                                            </select>

                                                            {{ trans('ticket_schedular.schedular_listing_fields.of') }}

                                                            <select class="form-select d-inline-block mx-1 w-auto" name="yearly_months2" id="yearly_months2">
                                                                <option value="1" selected>{{ trans('ticket_schedular.schedular_listing_fields.january') }}</option>
                                                                <option value="2">{{ trans('ticket_schedular.schedular_listing_fields.february') }}</option>
                                                                <option value="3">{{ trans('ticket_schedular.schedular_listing_fields.march') }}</option>
                                                                <option value="4">{{ trans('ticket_schedular.schedular_listing_fields.april') }}</option>
                                                                <option value="5">{{ trans('ticket_schedular.schedular_listing_fields.may') }}</option>
                                                                <option value="6">{{ trans('ticket_schedular.schedular_listing_fields.june') }}</option>
                                                                <option value="7">{{ trans('ticket_schedular.schedular_listing_fields.july') }}</option>
                                                                <option value="8">{{ trans('ticket_schedular.schedular_listing_fields.august') }}</option>
                                                                <option value="9">{{ trans('ticket_schedular.schedular_listing_fields.september') }}</option>
                                                                <option value="10">{{ trans('ticket_schedular.schedular_listing_fields.october') }}</option>
                                                                <option value="11">{{ trans('ticket_schedular.schedular_listing_fields.november') }}</option>
                                                                <option value="12">{{ trans('ticket_schedular.schedular_listing_fields.december') }}</option>
                                                            </select>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="b1-text">{{ trans('ticket_schedular.schedular_listing_fields.starts_at') }}</span>
                                                        <select class="form-select w-auto" name="yearly_hour" id="yearly_hour">
                                                            @for ($yearly_hour = 0; $yearly_hour < 24; $yearly_hour++)
                                                                @if( $yearly_hour==12)
                                                                <option value="{{ $yearly_hour }}" selected="selected">{{ strlen($yearly_hour) == 1 ? '0'.$yearly_hour : $yearly_hour }}</option>
                                                                @else
                                                                <option value="{{ $yearly_hour }}">{{ strlen($yearly_hour) == 1 ? '0'.$yearly_hour : $yearly_hour }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                        <span>:</span>
                                                        <select class="form-select w-auto" name="yearly_minute" id="yearly_minute">
                                                            @for ($yearly_minute = 0; $yearly_minute <= 59; $yearly_minute++)
                                                                @if( $yearly_minute==30)
                                                                <option value="{{ $yearly_minute }}" selected="selected">{{ strlen($yearly_minute) == 1 ? '0'.$yearly_minute : $yearly_minute }}</option>
                                                                @else
                                                                <option value="{{ $yearly_minute }}">{{ strlen($yearly_minute) == 1 ? '0'.$yearly_minute : $yearly_minute }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        {{-- Row 6: Reply To --}}
                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="ac_email_id" class="form-label b1-text me-2 mb-0 ">{{ trans("content.service_ticket_fields.reply_to") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $mailIcon !!}</span>
                                        <select name="ac_email_id" id="ac_email_id" class="form-select" placeholder="{{ trans('content.service_ticket_fields.Choose_Reply_Account') }}"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>
                       
                        {{-- Row 8: Seat No (conditional) --}}
                        <div class="row mb-4 px-4">
                            @if(config('app.client') == "ril" || config('app.client') == "rolepermission")
                                <div class="col-md-6">
                                    <div class="amg-form-field d-flex align-items-center">
                                        <label for="seat_no" class="form-label b1-text me-2 mb-0 ">{{ trans("content.user_fields.seat_no") }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $textIcon !!}</span>
                                            <input type="text" autocomplete="off" name="seat_no" id="seat_no" class="form-control error_input" placeholder="{{ trans('content.user_fields.Enter_Seat_No') }}">
                                        </div>
                                        <div class="amg-form-error-wrap"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="subject" class="form-label b1-text me-2 mb-0 required">{{ trans("content.service_ticket_fields.Subject") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input type="text" autocomplete="off" name="subject" id="subject" class="form-control error_input" placeholder="{{ trans('content.service_ticket_fields.enter_subject') }}" ">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Row 9: Content (Summernote) --}}
                        <div class="row mb-4 px-4">
                            <div class="col-md-12">
                                <div class="amg-form-field d-flex align-items-start">
                                    <label for="content" class="form-label b1-text me-2 mb-0  required">{{ trans("content.service_ticket_fields.Content") }}</label>
                                    <div class="flex-grow-1">
                                        <textarea name="content" id="content" class="amg-summernote summernote w-100"></textarea>
                                        <div id="shows_error" class="amg-form-error-wrap ms-0 w-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Row 10: Attachment --}}
                        <div class="row mb-4 px-4">
                            <div class="col-md-12">
                                <div id="attachment-dropper-cover"
                                    class="amg-uploader"
                                    data-amg-uploader
                                    data-multiple="true"
                                    data-auto-upload="true"
                                    data-max-files="10"
                                    data-max-size="10"
                                    data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                    data-upload-url="{{ url('tickets/attachment/schedular_attachment') }}"
                                    data-remove-url="{{ url('tickets/attachment/schedular_attachment_remove') }}"
                                    data-token="{{ csrf_token() }}"
                                    data-record-input="#schedule-mdl-frm #id"
                                    data-upload-field="attachment">
                                    <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>
                                    <div id="attachment-dropper" class="amg-uploader__dropzone">
                                        <div class="amg-uploader__message">
                                            <span class="amg-uploader__icon-wrap">{!! $attachIcon !!}</span>
                                            <span>
                                                {{ trans("content.service_ticket_fields.upload_note") }}
                                                {{ trans("content.service_ticket_fields.or") }}
                                                <span class="amg-uploader__hint">Drop files here</span>
                                            </span>
                                        </div>
                                        <button type="button" id="manual_file_trigger" name="manual_file_trigger" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">
                                            {{ trans("content.service_ticket_fields.Add_Attachment") }}
                                        </button>
                                    </div>
                                    <div id="attachments" class="amg-uploader__preview"></div>
                                    <div class="amg-uploader__error"></div>
                                </div>
                                <input type="hidden" name="form_type" id="form_type" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="amg-form-footer modal-footer d-flex justify-content-end row pb-4 py-0">
                    <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-ghost bg-black text-white amg-btn-md col-md-2">{{ trans('ticket_schedular.schedular_listing_fields.cancel') }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block amg-btn-md col-md-2 js-act-create">Save</button>
                </div>

            </div>
        </form>
    </div>
</div>
