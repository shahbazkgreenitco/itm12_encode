{{-- @page-meta
{
  "page_no": "TKD-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "2.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-05",
      "reviewer": null,
      "description": "Refined Ticket Details UI to match Figma design, optimized and reduced custom CSS, migrated maximum possible styles to Bootstrap 5 utility classes, improved layout consistency and responsiveness, integrated Select2 and Summernote components, and cleaned unnecessary styling/code."
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', 'Ticket Details')
@section('content')
    <div class="ticket-detail-wrapper">
        <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
            <button type="button" onclick="window.history.back()"
                class="d-flex gap-3 align-items-center bg-transparent border-0 text-decoration-none p-0">
                <svg width="20" height="20" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                        fill="currentColor"></path>
                </svg>
                <h3 class="h3-text mb-0">
                    Service Ticket Details - #SR3927
                </h3>
            </button>
        </div>

        
        <main class="main-content" id="mainContent">
            <div class="tkd-action-bar d-flex align-items-center gap-2 flex-wrap">
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m-2.715 5.933a.5.5 0 0 1-.183-.683A4.5 4.5 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.5 3.5 0 0 0 8 10.5a3.5 3.5 0 0 0-3.032 1.75.5.5 0 0 1-.683.183M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8"/>
                    </svg>
                    <span>Star</span>
                </button>
                <button class="tkd-action-btn" type="button">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                        <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/>
                    </svg>
                    <span>Star</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                    </svg>
                    <span>Edit</span>
                </button>
                <button class="tkd-action-btn" type="button">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5"/>
                    </svg>
                    <span>Transfer</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                        <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    <span>Assign To</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check" viewBox="0 0 16 16">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                        <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                    </svg>
                    <span>Self Assign</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                    </svg>
                    <span>Delete</span>
                </button>
                <button class="tkd-action-btn" type="button">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-x" viewBox="0 0 16 16">
                        <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                        <path d="M6.146 5.146a.5.5 0 0 1 .708 0L8 6.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 7l1.147 1.146a.5.5 0 0 1-.708.708L8 7.707 6.854 8.854a.5.5 0 1 1-.708-.708L7.293 7 6.146 5.854a.5.5 0 0 1 0-.708"/>
                    </svg>
                    <span>Spam</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    <span>History</span>
                </button>
                <button class="tkd-action-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                        <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                    </svg>
                    <span>Calender</span>
                </button>
               
            </div>
            <div class="tkd-body">
                <div class="tkd-main-card">
                    <div class="tkd-status-strip py-2 px-4 border-bottom d-flex align-items-center gap-2 flex-wrap">
                        <span class="amg-badge badge-high">In Progress</span>
                        <span class="amg-badge badge-icon ">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 12L12 3L21.5 12H15.5V21H8.5V12H2.5Z" fill="#FF0A0E" stroke="#FF0A0E"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            High
                        </span>
                        <span class="amg-badge badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                    <path
                                        d="M12 2.25C10.0716 2.25 8.18657 2.82183 6.58319 3.89317C4.97982 4.96451 3.73013 6.48726 2.99218 8.26884C2.25422 10.0504 2.06114 12.0108 2.43735 13.9021C2.81355 15.7934 3.74215 17.5307 5.10571 18.8943C6.46928 20.2579 8.20656 21.1865 10.0979 21.5627C11.9892 21.9389 13.9496 21.7458 15.7312 21.0078C17.5127 20.2699 19.0355 19.0202 20.1068 17.4168C21.1782 15.8134 21.75 13.9284 21.75 12C21.7473 9.41498 20.7192 6.93661 18.8913 5.10872C17.0634 3.28084 14.585 2.25273 12 2.25ZM17.25 12.75H12C11.8011 12.75 11.6103 12.671 11.4697 12.5303C11.329 12.3897 11.25 12.1989 11.25 12V6.75C11.25 6.55109 11.329 6.36032 11.4697 6.21967C11.6103 6.07902 11.8011 6 12 6C12.1989 6 12.3897 6.07902 12.5303 6.21967C12.671 6.36032 12.75 6.55109 12.75 6.75V11.25H17.25C17.4489 11.25 17.6397 11.329 17.7803 11.4697C17.921 11.6103 18 11.8011 18 12C18 12.1989 17.921 12.3897 17.7803 12.5303C17.6397 12.671 17.4489 12.75 17.25 12.75Z"
                                        fill="black" />
                                </g>
                            </svg>
                            SLA: 3h 20m Left
                        </span>
                        <span class="amg-badge">
                            TAT: 48 Hrs
                        </span>
                    </div>

                    <div class="tkd-meta">
                        <div class="py-3 px-4">
                            <div class="meta-row">
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.5">
                                            <path
                                                d="M2.55 11.8489C2.62879 11.908 2.71845 11.951 2.81386 11.9755C2.90927 11.9999 3.00856 12.0053 3.10607 11.9914C3.20357 11.9775 3.29737 11.9445 3.38212 11.8943C3.46687 11.8441 3.54091 11.7777 3.6 11.6989C4.05409 11.0935 4.64291 10.602 5.31983 10.2636C5.99675 9.92513 6.74318 9.74892 7.5 9.74892C8.25682 9.74892 9.00325 9.92513 9.68017 10.2636C10.3571 10.602 10.9459 11.0935 11.4 11.6989C11.5195 11.8579 11.6972 11.9629 11.8941 11.9909C12.091 12.0188 12.291 11.9674 12.45 11.848C12.5067 11.8058 12.5569 11.7556 12.5991 11.6989C13.0532 11.0935 13.642 10.602 14.3189 10.2636C14.9958 9.92513 15.7422 9.74892 16.4991 9.74892C17.2559 9.74892 18.0023 9.92513 18.6792 10.2636C19.3561 10.602 19.945 11.0935 20.3991 11.6989C20.5185 11.858 20.6963 11.9632 20.8933 11.9912C21.0903 12.0193 21.2904 11.9679 21.4495 11.8484C21.6087 11.729 21.7138 11.5512 21.7419 11.3542C21.7699 11.1572 21.7185 10.9571 21.5991 10.798C20.9352 9.90798 20.0499 9.20726 19.0312 8.76548C19.5897 8.25561 19.981 7.58882 20.1537 6.8526C20.3264 6.11639 20.2725 5.34516 19.9991 4.64013C19.7256 3.93509 19.2454 3.3292 18.6215 2.90196C17.9976 2.47472 17.259 2.24609 16.5028 2.24609C15.7466 2.24609 15.0081 2.47472 14.3841 2.90196C13.7602 3.3292 13.28 3.93509 13.0066 4.64013C12.7331 5.34516 12.6792 6.11639 12.8519 6.8526C13.0247 7.58882 13.4159 8.25561 13.9744 8.76548C13.2389 9.08353 12.5707 9.53884 12.0056 10.107C11.4406 9.53884 10.7724 9.08353 10.0369 8.76548C10.5953 8.25561 10.9866 7.58882 11.1593 6.8526C11.332 6.11639 11.2781 5.34516 11.0047 4.64013C10.7313 3.93509 10.2511 3.3292 9.62712 2.90196C9.00317 2.47472 8.26464 2.24609 7.50844 2.24609C6.75224 2.24609 6.0137 2.47472 5.38976 2.90196C4.76581 3.3292 4.28561 3.93509 4.01218 4.64013C3.73874 5.34516 3.68484 6.11639 3.85756 6.8526C4.03028 7.58882 4.42154 8.25561 4.98 8.76548C3.95686 9.20582 3.06722 9.90699 2.4 10.7989C2.34091 10.8777 2.29791 10.9674 2.27346 11.0628C2.24902 11.1582 2.24361 11.2575 2.25754 11.355C2.27147 11.4525 2.30446 11.5463 2.35464 11.631C2.40483 11.7158 2.47121 11.7898 2.55 11.8489ZM16.5 3.74892C16.945 3.74892 17.38 3.88088 17.75 4.12811C18.12 4.37534 18.4084 4.72675 18.5787 5.13788C18.749 5.54901 18.7936 6.00141 18.7068 6.43787C18.62 6.87433 18.4057 7.27524 18.091 7.58991C17.7763 7.90458 17.3754 8.11887 16.939 8.20569C16.5025 8.2925 16.0501 8.24794 15.639 8.07765C15.2278 7.90735 14.8764 7.61896 14.6292 7.24895C14.382 6.87894 14.25 6.44393 14.25 5.99892C14.25 5.40218 14.4871 4.82988 14.909 4.40793C15.331 3.98597 15.9033 3.74892 16.5 3.74892ZM7.5 3.74892C7.94501 3.74892 8.38002 3.88088 8.75003 4.12811C9.12004 4.37534 9.40843 4.72675 9.57873 5.13788C9.74903 5.54901 9.79358 6.00141 9.70677 6.43787C9.61995 6.87433 9.40566 7.27524 9.09099 7.58991C8.77632 7.90458 8.37541 8.11887 7.93895 8.20569C7.5025 8.2925 7.0501 8.24794 6.63896 8.07765C6.22783 7.90735 5.87643 7.61896 5.62919 7.24895C5.38196 6.87894 5.25 6.44393 5.25 5.99892C5.25 5.40218 5.48705 4.82988 5.90901 4.40793C6.33097 3.98597 6.90326 3.74892 7.5 3.74892ZM19.0312 18.5155C19.5897 18.0056 19.981 17.3388 20.1537 16.6026C20.3264 15.8664 20.2725 15.0952 19.9991 14.3901C19.7256 13.6851 19.2454 13.0792 18.6215 12.652C17.9976 12.2247 17.259 11.9961 16.5028 11.9961C15.7466 11.9961 15.0081 12.2247 14.3841 12.652C13.7602 13.0792 13.28 13.6851 13.0066 14.3901C12.7331 15.0952 12.6792 15.8664 12.8519 16.6026C13.0247 17.3388 13.4159 18.0056 13.9744 18.5155C13.2389 18.8335 12.5707 19.2888 12.0056 19.857C11.4406 19.2888 10.7724 18.8335 10.0369 18.5155C10.5953 18.0056 10.9866 17.3388 11.1593 16.6026C11.332 15.8664 11.2781 15.0952 11.0047 14.3901C10.7313 13.6851 10.2511 13.0792 9.62712 12.652C9.00317 12.2247 8.26464 11.9961 7.50844 11.9961C6.75224 11.9961 6.0137 12.2247 5.38976 12.652C4.76581 13.0792 4.28561 13.6851 4.01218 14.3901C3.73874 15.0952 3.68484 15.8664 3.85756 16.6026C4.03028 17.3388 4.42154 18.0056 4.98 18.5155C3.95686 18.9558 3.06722 19.657 2.4 20.5489C2.34091 20.6277 2.29791 20.7174 2.27346 20.8128C2.24902 20.9082 2.24361 21.0075 2.25754 21.105C2.27147 21.2025 2.30446 21.2963 2.35464 21.381C2.40483 21.4658 2.47121 21.5398 2.55 21.5989C2.62879 21.658 2.71845 21.701 2.81386 21.7255C2.90927 21.7499 3.00856 21.7553 3.10607 21.7414C3.20357 21.7275 3.29737 21.6945 3.38212 21.6443C3.46687 21.5941 3.54091 21.5277 3.6 21.4489C4.05409 20.8435 4.64291 20.352 5.31983 20.0136C5.99675 19.6751 6.74318 19.4989 7.5 19.4989C8.25682 19.4989 9.00325 19.6751 9.68017 20.0136C10.3571 20.352 10.9459 20.8435 11.4 21.4489C11.5195 21.6079 11.6972 21.7129 11.8941 21.7409C12.091 21.7688 12.291 21.7174 12.45 21.598C12.5067 21.5558 12.5569 21.5056 12.5991 21.4489C13.0532 20.8435 13.642 20.352 14.3189 20.0136C14.9958 19.6751 15.7422 19.4989 16.4991 19.4989C17.2559 19.4989 18.0023 19.6751 18.6792 20.0136C19.3561 20.352 19.945 20.8435 20.3991 21.4489C20.5185 21.608 20.6963 21.7132 20.8933 21.7412C21.0903 21.7693 21.2904 21.7179 21.4495 21.5984C21.6087 21.479 21.7138 21.3012 21.7419 21.1042C21.7699 20.9072 21.7185 20.7071 21.5991 20.548C20.9352 19.658 20.0499 18.9573 19.0312 18.5155ZM7.5 13.4989C7.94501 13.4989 8.38002 13.6309 8.75003 13.8781C9.12004 14.1253 9.40843 14.4767 9.57873 14.8879C9.74903 15.299 9.79358 15.7514 9.70677 16.1879C9.61995 16.6243 9.40566 17.0252 9.09099 17.3399C8.77632 17.6546 8.37541 17.8689 7.93895 17.9557C7.5025 18.0425 7.0501 17.9979 6.63896 17.8276C6.22783 17.6573 5.87643 17.369 5.62919 16.999C5.38196 16.6289 5.25 16.1939 5.25 15.7489C5.25 15.1522 5.48705 14.5799 5.90901 14.1579C6.33097 13.736 6.90326 13.4989 7.5 13.4989ZM16.5 13.4989C16.945 13.4989 17.38 13.6309 17.75 13.8781C18.12 14.1253 18.4084 14.4767 18.5787 14.8879C18.749 15.299 18.7936 15.7514 18.7068 16.1879C18.62 16.6243 18.4057 17.0252 18.091 17.3399C17.7763 17.6546 17.3754 17.8689 16.939 17.9557C16.5025 18.0425 16.0501 17.9979 15.639 17.8276C15.2278 17.6573 14.8764 17.369 14.6292 16.999C14.382 16.6289 14.25 16.1939 14.25 15.7489C14.25 15.1522 14.4871 14.5799 14.909 14.1579C15.331 13.736 15.9033 13.4989 16.5 13.4989Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                    Department
                                </span>
                                <span
                                    class="d-flex align-items-center gap-1 b4-text fw-normal opacity-70">{{ 'AM Global' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.5">
                                            <path
                                                d="M12.57 14.5116C12.4996 14.4294 12.4122 14.3634 12.3139 14.3182C12.2156 14.273 12.1087 14.2496 12.0005 14.2496C11.8923 14.2496 11.7853 14.273 11.687 14.3182C11.5887 14.3634 11.5013 14.4294 11.4309 14.5116L6.93094 19.7616C6.8375 19.8704 6.77726 20.0038 6.75735 20.1459C6.73743 20.288 6.75869 20.4328 6.81859 20.5631C6.87849 20.6935 6.97453 20.804 7.0953 20.8814C7.21608 20.9588 7.35653 21 7.5 21H16.5C16.6435 21 16.7839 20.9588 16.9047 20.8814C17.0255 20.804 17.1215 20.6935 17.1814 20.5631C17.2413 20.4328 17.2626 20.288 17.2427 20.1459C17.2227 20.0038 17.1625 19.8704 17.0691 19.7616L12.57 14.5116ZM9.13031 19.5L12 16.1522L14.8697 19.5H9.13031ZM21.75 6V16.5C21.75 17.0967 21.5129 17.669 21.091 18.091C20.669 18.5129 20.0967 18.75 19.5 18.75H18.75C18.5511 18.75 18.3603 18.671 18.2197 18.5303C18.079 18.3897 18 18.1989 18 18C18 17.8011 18.079 17.6103 18.2197 17.4697C18.3603 17.329 18.5511 17.25 18.75 17.25H19.5C19.6989 17.25 19.8897 17.171 20.0303 17.0303C20.171 16.8897 20.25 16.6989 20.25 16.5V6C20.25 5.80109 20.171 5.61032 20.0303 5.46967C19.8897 5.32902 19.6989 5.25 19.5 5.25H4.5C4.30109 5.25 4.11032 5.32902 3.96967 5.46967C3.82902 5.61032 3.75 5.80109 3.75 6V16.5C3.75 16.6989 3.82902 16.8897 3.96967 17.0303C4.11032 17.171 4.30109 17.25 4.5 17.25H5.25C5.44891 17.25 5.63968 17.329 5.78033 17.4697C5.92098 17.6103 6 17.8011 6 18C6 18.1989 5.92098 18.3897 5.78033 18.5303C5.63968 18.671 5.44891 18.75 5.25 18.75H4.5C3.90326 18.75 3.33097 18.5129 2.90901 18.091C2.48705 17.669 2.25 17.0967 2.25 16.5V6C2.25 5.40326 2.48705 4.83097 2.90901 4.40901C3.33097 3.98705 3.90326 3.75 4.5 3.75H19.5C20.0967 3.75 20.669 3.98705 21.091 4.40901C21.5129 4.83097 21.75 5.40326 21.75 6Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                    Category
                                </span>
                                <span
                                    class="d-flex align-items-center gap-1 b4-text fw-normal opacity-70">{{ 'Product Design' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.5">
                                            <path
                                                d="M12 6C11.2583 6 10.5333 6.21993 9.91661 6.63199C9.29993 7.04404 8.81928 7.62971 8.53545 8.31494C8.25162 9.00016 8.17736 9.75416 8.32205 10.4816C8.46675 11.209 8.8239 11.8772 9.34835 12.4017C9.8728 12.9261 10.541 13.2833 11.2684 13.4279C11.9958 13.5726 12.7498 13.4984 13.4351 13.2145C14.1203 12.9307 14.706 12.4501 15.118 11.8334C15.5301 11.2167 15.75 10.4917 15.75 9.75C15.75 8.75544 15.3549 7.80161 14.6517 7.09835C13.9484 6.39509 12.9946 6 12 6ZM12 12C11.555 12 11.12 11.868 10.75 11.6208C10.38 11.3736 10.0916 11.0222 9.92127 10.611C9.75097 10.1999 9.70642 9.7475 9.79323 9.31105C9.88005 8.87459 10.0943 8.47368 10.409 8.15901C10.7237 7.84434 11.1246 7.63005 11.561 7.54323C11.9975 7.45642 12.4499 7.50097 12.861 7.67127C13.2722 7.84157 13.6236 8.12996 13.8708 8.49997C14.118 8.86998 14.25 9.30499 14.25 9.75C14.25 10.3467 14.0129 10.919 13.591 11.341C13.169 11.7629 12.5967 12 12 12ZM12 1.5C9.81273 1.50248 7.71575 2.37247 6.16911 3.91911C4.62247 5.46575 3.75248 7.56273 3.75 9.75C3.75 12.6938 5.11031 15.8138 7.6875 18.7734C8.84552 20.1108 10.1489 21.3151 11.5734 22.3641C11.6995 22.4524 11.8498 22.4998 12.0037 22.4998C12.1577 22.4998 12.308 22.4524 12.4341 22.3641C13.856 21.3147 15.1568 20.1104 16.3125 18.7734C18.8859 15.8138 20.25 12.6938 20.25 9.75C20.2475 7.56273 19.3775 5.46575 17.8309 3.91911C16.2843 2.37247 14.1873 1.50248 12 1.5ZM12 20.8125C10.4503 19.5938 5.25 15.1172 5.25 9.75C5.25 7.95979 5.96116 6.2429 7.22703 4.97703C8.4929 3.71116 10.2098 3 12 3C13.7902 3 15.5071 3.71116 16.773 4.97703C18.0388 6.2429 18.75 7.95979 18.75 9.75C18.75 15.1153 13.5497 19.5938 12 20.8125Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                    Location
                                </span>
                                <span
                                    class="d-flex align-items-center gap-1 b4-text fw-normal opacity-70">{{ 'Mumbai' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.5">
                                            <path
                                                d="M21.6489 19.8736C20.2211 17.4052 18.0208 15.6352 15.4529 14.7961C16.7231 14.04 17.7099 12.8878 18.2619 11.5166C18.8139 10.1453 18.9004 8.63077 18.5083 7.20554C18.1162 5.78031 17.2671 4.5232 16.0914 3.62726C14.9156 2.73132 13.4783 2.24609 12.0001 2.24609C10.5219 2.24609 9.08463 2.73132 7.90891 3.62726C6.73318 4.5232 5.88406 5.78031 5.49195 7.20554C5.09984 8.63077 5.18641 10.1453 5.73837 11.5166C6.29033 12.8878 7.27716 14.04 8.54732 14.7961C5.97951 15.6343 3.77919 17.4043 2.35138 19.8736C2.29902 19.959 2.26429 20.054 2.24924 20.153C2.23419 20.2521 2.23912 20.3531 2.26375 20.4502C2.28837 20.5472 2.33219 20.6384 2.39262 20.7183C2.45305 20.7981 2.52887 20.8651 2.61559 20.9152C2.70232 20.9653 2.7982 20.9975 2.89758 21.0099C2.99695 21.0224 3.09782 21.0148 3.19421 20.9876C3.29061 20.9604 3.38059 20.9142 3.45884 20.8517C3.53709 20.7892 3.60203 20.7117 3.64982 20.6236C5.41607 17.5711 8.53794 15.7486 12.0001 15.7486C15.4623 15.7486 18.5842 17.5711 20.3504 20.6236C20.3982 20.7117 20.4632 20.7892 20.5414 20.8517C20.6197 20.9142 20.7097 20.9604 20.806 20.9876C20.9024 21.0148 21.0033 21.0224 21.1027 21.0099C21.2021 20.9975 21.2979 20.9653 21.3847 20.9152C21.4714 20.8651 21.5472 20.7981 21.6076 20.7183C21.6681 20.6384 21.7119 20.5472 21.7365 20.4502C21.7611 20.3531 21.7661 20.2521 21.751 20.153C21.736 20.054 21.7012 19.959 21.6489 19.8736ZM6.75013 8.99864C6.75013 7.96029 7.05804 6.94526 7.63492 6.0819C8.21179 5.21854 9.03173 4.54564 9.99104 4.14828C10.9504 3.75092 12.006 3.64695 13.0244 3.84952C14.0428 4.05209 14.9782 4.55211 15.7124 5.28633C16.4467 6.02056 16.9467 6.95602 17.1493 7.97442C17.3518 8.99282 17.2479 10.0484 16.8505 11.0077C16.4531 11.967 15.7802 12.787 14.9169 13.3639C14.0535 13.9407 13.0385 14.2486 12.0001 14.2486C10.6082 14.2472 9.27371 13.6936 8.28947 12.7093C7.30522 11.7251 6.75162 10.3906 6.75013 8.99864Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                    Creator
                                </span>
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal opacity-70">
                                    <span
                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-indigo">BG</span>
                                    {{ 'Bharat Gupta' }}
                                </span>
                            </div>
                            <div class="meta-row">
                                <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.5">
                                            <path
                                                d="M19.5 3H17.25V2.25C17.25 2.05109 17.171 1.86032 17.0303 1.71967C16.8897 1.57902 16.6989 1.5 16.5 1.5C16.3011 1.5 16.1103 1.57902 15.9697 1.71967C15.829 1.86032 15.75 2.05109 15.75 2.25V3H8.25V2.25C8.25 2.05109 8.17098 1.86032 8.03033 1.71967C7.88968 1.57902 7.69891 1.5 7.5 1.5C7.30109 1.5 7.11032 1.57902 6.96967 1.71967C6.82902 1.86032 6.75 2.05109 6.75 2.25V3H4.5C4.10218 3 3.72064 3.15804 3.43934 3.43934C3.15804 3.72064 3 4.10218 3 4.5V19.5C3 19.8978 3.15804 20.2794 3.43934 20.5607C3.72064 20.842 4.10218 21 4.5 21H19.5C19.8978 21 20.2794 20.842 20.5607 20.5607C20.842 20.2794 21 19.8978 21 19.5V4.5C21 4.10218 20.842 3.72064 20.5607 3.43934C20.2794 3.15804 19.8978 3 19.5 3ZM6.75 4.5V5.25C6.75 5.44891 6.82902 5.63968 6.96967 5.78033C7.11032 5.92098 7.30109 6 7.5 6C7.69891 6 7.88968 5.92098 8.03033 5.78033C8.17098 5.63968 8.25 5.44891 8.25 5.25V4.5H15.75V5.25C15.75 5.44891 15.829 5.63968 15.9697 5.78033C16.1103 5.92098 16.3011 6 16.5 6C16.6989 6 16.8897 5.92098 17.0303 5.78033C17.171 5.63968 17.25 5.44891 17.25 5.25V4.5H19.5V7.5H4.5V4.5H6.75ZM19.5 19.5H4.5V9H19.5V19.5ZM13.5 14.25C13.5 14.5467 13.412 14.8367 13.2472 15.0834C13.0824 15.33 12.8481 15.5223 12.574 15.6358C12.2999 15.7494 11.9983 15.7791 11.7074 15.7212C11.4164 15.6633 11.1491 15.5204 10.9393 15.3107C10.7296 15.1009 10.5867 14.8336 10.5288 14.5426C10.4709 14.2517 10.5006 13.9501 10.6142 13.676C10.7277 13.4019 10.92 13.1676 11.1666 13.0028C11.4133 12.838 11.7033 12.75 12 12.75C12.3978 12.75 12.7794 12.908 13.0607 13.1893C13.342 13.4706 13.5 13.8522 13.5 14.25Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                    Created at
                                </span>
                                <span
                                    class="d-flex align-items-center gap-2 b4-text fw-normal opacity-70">{{ '12 Dec 2025, 10.28 AM' }}</span>
                            </div>
                        </div>

                        <div class="py-3 px-4 d-flex align-items-end">
                            <div class="tkd-assignment-card card rounded-1 py-2 px-3  shadow-none border bg-none w-75">
                                <div class="meta-row mb-1" style="grid-template-columns:100px 1fr;">
                                    <span class="d-flex align-items-center gap-1 b4-text fw-normal">Assigned
                                        to</span>
                                    <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                        <span
                                            class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AN</span>
                                        <span class="opacity-70">Ananth</span>
                                    </span>
                                </div>
                                <div class="meta-row mb-0" style="grid-template-columns:100px 1fr;">
                                    <span class="d-flex align-items-center gap-1 b4-text fw-normal">Updated at</span>
                                    <span class="d-flex align-items-center gap-1 b4-text fw-normal">
                                        <span
                                            class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AN</span>
                                        <span class="b4-text opacity-70 fw-normal">12 Dec 2025, 12.10pm</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tkd-conversation">
                        <div class="d-flex align-items-center justify-content-between mt-1 mb-3">
                            <p class="b2-text tkd-section-title mb-0">Conversation</p>
                        </div>
                        <div class="tkd-conversation-list">
                            <div class="tkd-conversation-card d-flex gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-24 av-teal"
                                            style="margin-top:2px;">AS</div>
                                        <span class="b4-text pe-2 border-end border-2 fw-bold">Ananth S</span>
                                        <span class="b4-text fw-normal opacity-60">December 29, 2025, 10:08 AM</span>

                                        <button class="tkd-icon-plain tkd-comment-toggle ms-auto" type="button"
                                            aria-label="Collapse conversation" aria-expanded="true">
                                            <i class="bi bi-arrows-angle-contract"></i>
                                        </button>
                                    </div>

                                    <div class="tkd-comment-body d-flex align-items-center gap-2 mb-1 flex-wrap ms-4 mt-1 ps-2">
                                        <p class="b4-text tkd-message-body opacity-70 fw-normal">
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tkd-conversation-list">
                            <div class="tkd-conversation-card d-flex gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-24 av-teal"
                                            style="margin-top:2px;">AS</div>
                                        <span class="b4-text pe-2 border-end border-2 fw-bold">Ananth S</span>
                                        <span class="b4-text fw-normal opacity-60">December 29, 2025, 10:08 AM</span>

                                        <button class="tkd-icon-plain tkd-comment-toggle ms-auto" type="button"
                                            aria-label="Collapse conversation" aria-expanded="true">
                                            <i class="bi bi-arrows-angle-contract"></i>
                                        </button>
                                    </div>

                                    <div class="tkd-comment-body d-flex align-items-center gap-2 mb-1 flex-wrap ms-4 mt-1 ps-2">
                                        <p class="b4-text tkd-message-body opacity-70 fw-normal">
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tkd-conversation-list">
                            <div class="tkd-conversation-card d-flex gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-24 av-teal"
                                            style="margin-top:2px;">AS</div>
                                        <span class="b4-text pe-2 border-end border-2 fw-bold">Ananth S</span>
                                        <span class="b4-text fw-normal opacity-60">December 29, 2025, 10:08 AM</span>

                                        <button class="tkd-icon-plain tkd-comment-toggle ms-auto" type="button"
                                            aria-label="Collapse conversation" aria-expanded="true">
                                            <i class="bi bi-arrows-angle-contract"></i>
                                        </button>
                                    </div>

                                    <div class="tkd-comment-body d-flex align-items-center gap-2 mb-1 flex-wrap ms-4 mt-1 ps-2">
                                        <p class="b4-text tkd-message-body opacity-70 fw-normal">
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                            I think before jumping into solutions, we should really understand the
                                            user's pain points. What problem are they actually facing?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tkd-reply">
                        <label class="tkd-message-label b5-text fw-bold mb-2">Message <span>*</span></label>
                        <input type="file" id="tkd-file-input" multiple
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip">

                        <div id="reply-summernote-wrapper">
                            <textarea id="reply-summernote"></textarea>

                            <div class="custom-toolbar" id="reply-custom-toolbar">
                                <button title="Attach file" id="tkd-attach">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.6">
                                            <path
                                                d="M19.6558 11.4697C19.7255 11.5393 19.7808 11.622 19.8186 11.7131C19.8563 11.8041 19.8757 11.9017 19.8757 12.0003C19.8757 12.0989 19.8563 12.1964 19.8186 12.2875C19.7808 12.3785 19.7255 12.4613 19.6558 12.5309L11.9636 20.2184C10.9788 21.203 9.6433 21.7561 8.25076 21.756C6.85821 21.756 5.52274 21.2027 4.53812 20.2179C3.5535 19.2332 3.0004 17.8977 3.00049 16.5051C3.00058 15.1126 3.55385 13.7771 4.53859 12.7925L13.8442 3.34998C14.5472 2.6462 15.5011 2.25053 16.4958 2.25C17.4906 2.24947 18.4448 2.64414 19.1486 3.34717C19.8524 4.0502 20.248 5.00401 20.2486 5.99877C20.2491 6.99353 19.8544 7.94776 19.1514 8.65154L9.8439 18.094C9.42121 18.5167 8.84792 18.7542 8.25015 18.7542C7.65238 18.7542 7.07909 18.5167 6.6564 18.094C6.23371 17.6714 5.99625 17.0981 5.99625 16.5003C5.99625 15.9025 6.23371 15.3292 6.6564 14.9065L14.4658 6.97342C14.5342 6.90044 14.6165 6.84188 14.7079 6.8012C14.7993 6.76051 14.8979 6.73851 14.9979 6.7365C15.0979 6.73448 15.1973 6.7525 15.2902 6.78947C15.3832 6.82645 15.4678 6.88165 15.539 6.95181C15.6103 7.02198 15.6669 7.10569 15.7053 7.19804C15.7438 7.29038 15.7634 7.38948 15.7629 7.4895C15.7625 7.58953 15.7421 7.68846 15.7029 7.78048C15.6636 7.87249 15.6064 7.95573 15.5345 8.02529L7.72421 15.9669C7.65428 16.0362 7.5987 16.1187 7.56065 16.2096C7.52259 16.3004 7.5028 16.3979 7.50241 16.4964C7.50202 16.5949 7.52103 16.6925 7.55836 16.7836C7.5957 16.8748 7.65062 16.9577 7.71999 17.0276C7.78937 17.0976 7.87184 17.1531 7.96269 17.1912C8.05355 17.2293 8.15101 17.249 8.24951 17.2494C8.34801 17.2498 8.44563 17.2308 8.53678 17.1935C8.62794 17.1562 8.71085 17.1012 8.78078 17.0319L18.0873 7.59404C18.51 7.17222 18.7478 6.59977 18.7485 6.00261C18.7491 5.40545 18.5124 4.83251 18.0906 4.40982C17.6688 3.98713 17.0963 3.74932 16.4992 3.74871C15.902 3.74809 15.3291 3.98472 14.9064 4.40654L5.60265 13.8453C5.25411 14.1933 4.97753 14.6065 4.78869 15.0614C4.59985 15.5162 4.50246 16.0039 4.50207 16.4964C4.50167 16.9889 4.59829 17.4767 4.78641 17.9318C4.97452 18.387 5.25045 18.8007 5.59843 19.1492C5.94641 19.4977 6.35964 19.7743 6.81451 19.9632C7.26938 20.152 7.757 20.2494 8.24951 20.2498C8.74202 20.2502 9.22979 20.1536 9.68496 19.9654C10.1401 19.7773 10.5538 19.5014 10.9023 19.1534L18.5955 11.4659C18.7366 11.3259 18.9276 11.2476 19.1264 11.2483C19.3252 11.249 19.5156 11.3286 19.6558 11.4697Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                </button>
                                <button title="Text formatting" id="reply-btn-format">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="0.6">
                                            <path
                                                d="M8.17882 4.93094C8.11825 4.80209 8.02226 4.69315 7.90207 4.61684C7.78187 4.54052 7.64244 4.5 7.50007 4.5C7.3577 4.5 7.21826 4.54052 7.09807 4.61684C6.97788 4.69315 6.88189 4.80209 6.82132 4.93094L0.821318 17.6809C0.779398 17.7701 0.755444 17.8666 0.750825 17.965C0.746206 18.0634 0.761012 18.1617 0.794398 18.2544C0.861823 18.4415 1.00083 18.5943 1.18085 18.6789C1.36087 18.7636 1.56714 18.7733 1.7543 18.7058C1.94145 18.6384 2.09416 18.4994 2.17882 18.3194L3.74069 15.0006H11.2594L12.8213 18.3194C12.8632 18.4085 12.9223 18.4885 12.9951 18.5548C13.068 18.6211 13.1532 18.6724 13.2458 18.7058C13.3385 18.7392 13.4369 18.754 13.5352 18.7494C13.6336 18.7448 13.7302 18.7208 13.8193 18.6789C13.9084 18.637 13.9884 18.5779 14.0547 18.5051C14.121 18.4322 14.1724 18.3471 14.2057 18.2544C14.2391 18.1617 14.2539 18.0634 14.2493 17.965C14.2447 17.8666 14.2207 17.7701 14.1788 17.6809L8.17882 4.93094ZM4.44663 13.5006L7.50007 7.01219L10.5535 13.5006H4.44663ZM18.7501 9.00063C17.5538 9.00063 16.6191 9.32594 15.9723 9.96813C15.8368 10.1093 15.7619 10.2978 15.7635 10.4935C15.7651 10.6891 15.8431 10.8763 15.9808 11.0153C16.1186 11.1542 16.3051 11.2339 16.5007 11.2372C16.6963 11.2405 16.8855 11.1673 17.0279 11.0331C17.3841 10.6797 17.9654 10.5006 18.7501 10.5006C19.9904 10.5006 21.0001 11.3444 21.0001 12.3756V12.6775C20.3345 12.2322 19.5508 11.9965 18.7501 12.0006C16.6819 12.0006 15.0001 13.5147 15.0001 15.3756C15.0001 17.2366 16.6819 18.7506 18.7501 18.7506C19.5512 18.7541 20.3349 18.5174 21.0001 18.0709C21.0094 18.2699 21.0974 18.4569 21.2446 18.591C21.3918 18.725 21.5863 18.7951 21.7852 18.7858C21.9841 18.7765 22.1712 18.6885 22.3053 18.5413C22.4393 18.394 22.5094 18.1995 22.5001 18.0006V12.3756C22.5001 10.5147 20.8182 9.00063 18.7501 9.00063ZM18.7501 17.2506C17.5098 17.2506 16.5001 16.4069 16.5001 15.3756C16.5001 14.3444 17.5098 13.5006 18.7501 13.5006C19.9904 13.5006 21.0001 14.3444 21.0001 15.3756C21.0001 16.4069 19.9904 17.2506 18.7501 17.2506Z"
                                                fill="black" />
                                        </g>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="tkd-chips" id="tkd-chips"></div>

                        <div class="d-flex align-items-center justify-content-start mt-3">
                            <button class="amg-btn amg-btn-primary amg-btn-sm px-5" type="button">
                                <span>Save Comment</span>
                            </button>
                        </div>
                        <label class="tkd-internal-note d-flex align-items-center gap-2 mt-3 mb-0">
                            <input type="checkbox" checked>
                            <span class="b7-text opacity-70">Make it note for internal purpose.</span>
                        </label>
                    </div>

                </div>

                <div class="d-flex flex-column gap-3">

                    <div id="updateTicket" class="border rounded-2 overflow-hidden">
                        <div class="tkd-update-header px-4 py-2 text-start">
                            <span class="b4-text text-white ps-4 fw-light">Update Ticket</span>
                        </div>
                        <div class="d-flex flex-column gap-3 px-3 py-3">
                            <div>
                                <label class="d-block b5-text mb-1 opacity-70">Change Status</label>
                                <select class="tkd-select2" id="select-status">
                                    <option value="inprogress" selected>In Progress</option>
                                    <option value="open">Open</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <span class="b7-text fst-italic fw-regular opacity-70">You are working on this
                                    ticket</span>
                            </div>

                            <div>
                                <label class="d-block b5-text mb-1 opacity-70">Change Priority</label>
                                <select class="tkd-select2" id="select-priority">
                                    <option value="high" data-icon="▲" data-color="#ef4444" selected>High</option>
                                    <option value="medium" data-icon="●" data-color="#f59e0b">Medium</option>
                                    <option value="low" data-icon="▼" data-color="#22c55e">Low</option>
                                </select>
                            </div>

                            <div>
                                <span class="d-block b5-text mb-1 opacity-70">Comment</span>

                                <div id="summernote-wrapper">
                                    <div>
                                        <textarea id="summernote"></textarea>
                                    </div>

                                    <div class="custom-toolbar" id="custom-toolbar">
                                        <button title="Attach file" id="btn-attach">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g opacity="0.6">
                                                    <path
                                                        d="M19.6558 11.4697C19.7255 11.5393 19.7808 11.622 19.8186 11.7131C19.8563 11.8041 19.8757 11.9017 19.8757 12.0003C19.8757 12.0989 19.8563 12.1964 19.8186 12.2875C19.7808 12.3785 19.7255 12.4613 19.6558 12.5309L11.9636 20.2184C10.9788 21.203 9.6433 21.7561 8.25076 21.756C6.85821 21.756 5.52274 21.2027 4.53812 20.2179C3.5535 19.2332 3.0004 17.8977 3.00049 16.5051C3.00058 15.1126 3.55385 13.7771 4.53859 12.7925L13.8442 3.34998C14.5472 2.6462 15.5011 2.25053 16.4958 2.25C17.4906 2.24947 18.4448 2.64414 19.1486 3.34717C19.8524 4.0502 20.248 5.00401 20.2486 5.99877C20.2491 6.99353 19.8544 7.94776 19.1514 8.65154L9.8439 18.094C9.42121 18.5167 8.84792 18.7542 8.25015 18.7542C7.65238 18.7542 7.07909 18.5167 6.6564 18.094C6.23371 17.6714 5.99625 17.0981 5.99625 16.5003C5.99625 15.9025 6.23371 15.3292 6.6564 14.9065L14.4658 6.97342C14.5342 6.90044 14.6165 6.84188 14.7079 6.8012C14.7993 6.76051 14.8979 6.73851 14.9979 6.7365C15.0979 6.73448 15.1973 6.7525 15.2902 6.78947C15.3832 6.82645 15.4678 6.88165 15.539 6.95181C15.6103 7.02198 15.6669 7.10569 15.7053 7.19804C15.7438 7.29038 15.7634 7.38948 15.7629 7.4895C15.7625 7.58953 15.7421 7.68846 15.7029 7.78048C15.6636 7.87249 15.6064 7.95573 15.5345 8.02529L7.72421 15.9669C7.65428 16.0362 7.5987 16.1187 7.56065 16.2096C7.52259 16.3004 7.5028 16.3979 7.50241 16.4964C7.50202 16.5949 7.52103 16.6925 7.55836 16.7836C7.5957 16.8748 7.65062 16.9577 7.71999 17.0276C7.78937 17.0976 7.87184 17.1531 7.96269 17.1912C8.05355 17.2293 8.15101 17.249 8.24951 17.2494C8.34801 17.2498 8.44563 17.2308 8.53678 17.1935C8.62794 17.1562 8.71085 17.1012 8.78078 17.0319L18.0873 7.59404C18.51 7.17222 18.7478 6.59977 18.7485 6.00261C18.7491 5.40545 18.5124 4.83251 18.0906 4.40982C17.6688 3.98713 17.0963 3.74932 16.4992 3.74871C15.902 3.74809 15.3291 3.98472 14.9064 4.40654L5.60265 13.8453C5.25411 14.1933 4.97753 14.6065 4.78869 15.0614C4.59985 15.5162 4.50246 16.0039 4.50207 16.4964C4.50167 16.9889 4.59829 17.4767 4.78641 17.9318C4.97452 18.387 5.25045 18.8007 5.59843 19.1492C5.94641 19.4977 6.35964 19.7743 6.81451 19.9632C7.26938 20.152 7.757 20.2494 8.24951 20.2498C8.74202 20.2502 9.22979 20.1536 9.68496 19.9654C10.1401 19.7773 10.5538 19.5014 10.9023 19.1534L18.5955 11.4659C18.7366 11.3259 18.9276 11.2476 19.1264 11.2483C19.3252 11.249 19.5156 11.3286 19.6558 11.4697Z"
                                                        fill="black" />
                                                </g>
                                            </svg>
                                        </button>

                                        <button title="Text formatting" id="btn-format">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g opacity="0.6">
                                                    <path
                                                        d="M8.17882 4.93094C8.11825 4.80209 8.02226 4.69315 7.90207 4.61684C7.78187 4.54052 7.64244 4.5 7.50007 4.5C7.3577 4.5 7.21826 4.54052 7.09807 4.61684C6.97788 4.69315 6.88189 4.80209 6.82132 4.93094L0.821318 17.6809C0.779398 17.7701 0.755444 17.8666 0.750825 17.965C0.746206 18.0634 0.761012 18.1617 0.794398 18.2544C0.861823 18.4415 1.00083 18.5943 1.18085 18.6789C1.36087 18.7636 1.56714 18.7733 1.7543 18.7058C1.94145 18.6384 2.09416 18.4994 2.17882 18.3194L3.74069 15.0006H11.2594L12.8213 18.3194C12.8632 18.4085 12.9223 18.4885 12.9951 18.5548C13.068 18.6211 13.1532 18.6724 13.2458 18.7058C13.3385 18.7392 13.4369 18.754 13.5352 18.7494C13.6336 18.7448 13.7302 18.7208 13.8193 18.6789C13.9084 18.637 13.9884 18.5779 14.0547 18.5051C14.121 18.4322 14.1724 18.3471 14.2057 18.2544C14.2391 18.1617 14.2539 18.0634 14.2493 17.965C14.2447 17.8666 14.2207 17.7701 14.1788 17.6809L8.17882 4.93094ZM4.44663 13.5006L7.50007 7.01219L10.5535 13.5006H4.44663ZM18.7501 9.00063C17.5538 9.00063 16.6191 9.32594 15.9723 9.96813C15.8368 10.1093 15.7619 10.2978 15.7635 10.4935C15.7651 10.6891 15.8431 10.8763 15.9808 11.0153C16.1186 11.1542 16.3051 11.2339 16.5007 11.2372C16.6963 11.2405 16.8855 11.1673 17.0279 11.0331C17.3841 10.6797 17.9654 10.5006 18.7501 10.5006C19.9904 10.5006 21.0001 11.3444 21.0001 12.3756V12.6775C20.3345 12.2322 19.5508 11.9965 18.7501 12.0006C16.6819 12.0006 15.0001 13.5147 15.0001 15.3756C15.0001 17.2366 16.6819 18.7506 18.7501 18.7506C19.5512 18.7541 20.3349 18.5174 21.0001 18.0709C21.0094 18.2699 21.0974 18.4569 21.2446 18.591C21.3918 18.725 21.5863 18.7951 21.7852 18.7858C21.9841 18.7765 22.1712 18.6885 22.3053 18.5413C22.4393 18.394 22.5094 18.1995 22.5001 18.0006V12.3756C22.5001 10.5147 20.8182 9.00063 18.7501 9.00063ZM18.7501 17.2506C17.5098 17.2506 16.5001 16.4069 16.5001 15.3756C16.5001 14.3444 17.5098 13.5006 18.7501 13.5006C19.9904 13.5006 21.0001 14.3444 21.0001 15.3756C21.0001 16.4069 19.9904 17.2506 18.7501 17.2506Z"
                                                        fill="black" />
                                                </g>
                                            </svg>
                                        </button>

                                        <input type="file" id="file-input" style="display:none" multiple />
                                    </div>
                                </div>
                            </div>

                            <button class="amg-btn amg-btn-primary amg-btn-sm" type="button">
                                <span>Resolve Ticket</span>
                            </button>
                        </div>
                    </div>

                    <div class="tkd-panel">
                        <a class="d-flex align-items-center px-3 py-2 border-bottom rounded-top tkd-panel-head gap-2"
                            data-bs-toggle="collapse" href="#tkdTimeline" role="button" aria-expanded="true"
                            aria-controls="tkdTimeline">
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <polyline points="18 15 12 9 6 15" />
                            </svg>
                            <span class="b4-text tkd-panel-title">Ticket Timeline</span>

                        </a>
                        <div id="tkdTimeline" class="collapse show">
                            <div class="p-3">
                                <div class="tkd-tl d-flex flex-column overflow-auto ps-1">
                                    <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                        <div class="tkd-tl-dot"></div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="b6-text">Created by</div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AS</span>
                                                    <span class="b6-text fw-normal">Ananth S</span>
                                                </div>
                                            </div>
                                            <div class="b7-text fw-regular fst-italic opacity-70">12 December 2025 at,
                                                12:45 PM
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                        <div class="tkd-tl-dot"></div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="b6-text">Updated by</div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AS</span>
                                                    <span class="b6-text fw-normal">Ananth S</span>
                                                </div>
                                            </div>
                                            <div class="b7-text fw-regular fst-italic opacity-70">12 December 2025 at,
                                                12:45 PM
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                        <div class="tkd-tl-dot"></div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="b6-text fw-">Created by</div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AS</span>
                                                    <span class="b6-text fw-normal">Ananth S</span>
                                                </div>
                                            </div>
                                            <div class="b7-text fw-regular fst-italic opacity-70">12 December 2025 at,
                                                12:45 PM
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                        <div class="tkd-tl-dot"></div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="b6-text fw-">Created by</div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AS</span>
                                                    <span class="b6-text fw-normal">Ananth S</span>
                                                </div>
                                            </div>
                                            <div class="b7-text fw-regular fst-italic opacity-70">12 December 2025 at,
                                                12:45 PM
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tkd-tl-item d-flex gap-2 align-items-start position-relative">
                                        <div class="tkd-tl-dot"></div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="b6-text fw-">Updated by</div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AS</span>
                                                    <span class="b6-text fw-normal">Ananth S</span>
                                                </div>
                                            </div>
                                            <div class="b7-text fw-regular fst-italic opacity-70">12 December 2025 at,
                                                12:45 PM
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tkd-panel">
                        <a class="d-flex align-items-center px-3 py-2 border-bottom rounded-top tkd-panel-head  gap-2"
                            data-bs-toggle="collapse" href="#tkdPeople" role="button" aria-expanded="true"
                            aria-controls="tkdPeople">
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <polyline points="18 15 12 9 6 15" />
                            </svg>
                            <span class="b4-text tkd-panel-title">People</span>

                        </a>
                        <div id="tkdPeople" class="collapse show">
                            <div class="py-2 px-3 d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-start gap-2">
                                    <span class="b6-text">Assigned to</span>
                                    <span class="d-flex align-items-center gap-1">
                                        <span
                                            class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-teal">AN</span>
                                        <span class="b6-text fw-normal">Ananth S</span>
                                    </span>
                                </div>


                                <div class="d-flex align-items-center justify-content-start gap-2">
                                    <span class="b6-text">Creator</span>
                                    <span class="d-flex align-items-center gap-1">
                                        <span
                                            class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white  av-16 av-indigo">BG</span>
                                        <span class="b6-text fw-normal">Bharat Gupta</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>
        .tkd-body {
            display: grid;
            grid-template-columns: 1fr 285px;
            gap: 16px;
            padding: 16px 20px 80px;
        }

        .tkd-main-card {
            background: var(--bs-body-bg);
            border: 1px solid #E5E5E5;
            border-radius: 10px;
            overflow: hidden;
        }

        .tkd-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            margin-bottom: 9px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        .av-24 {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }

        .av-16 {
            width: 16px;
            height: 16px;
            font-weight: 400;
            font-size: 6px;
        }

        .av-teal {
            background: linear-gradient(135deg, #14b8a6, #0891b2);
        }

        .av-indigo {
            background: linear-gradient(135deg, #6366f1, #4338ca);
        }

        .tkd-conversation {
            background: #faeeef70;
            padding: 16px;
        }

        .tkd-reply {
            padding: 12px 16px 14px;
            background: #faeeef70;
        }

        .tkd-reply .note-minibar i {
            font-size: 18px;
        }

        #tkd-file-input {
            display: none;
        }

        /* Chips */
        .tkd-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .tkd-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f0f4ff;
            border: 1px solid #c7d2fe;
            border-radius: 20px;
            padding: 2px 10px 2px 8px;
            font-size: 11px;
            color: #3730a3 !important;
            max-width: 180px;
        }

        .tkd-chip span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tkd-chip button {
            background: none;
            border: none;
            color: #6366f1 !important;
            padding: 0;
            font-size: 14px;
            line-height: 1;
        }

        [data-bs-theme="dark"] .tkd-chip {
            background: #1e1b4b;
            border-color: #4338ca;
            color: #a5b4fc !important;
        }

        /* ── RIGHT SIDEBAR ───────────────────────────────────────── */

        .tkd-update-header {
            background: #001B51;
        }

        .tkd-panel {
            background: var(--bs-body-bg);
            border: 1px solid #E5E5E5;
            border-radius: 10px;
            overflow: hidden;
        }

        .tkd-panel-head {
            background: #F4F6FF;
            cursor: pointer;
            border-bottom: 1px solid #E5E5E5;
        }

        .tkd-panel-head .chevron {
            width: 20px;
            height: 20px;
            color: var(--bs-secondary-color) !important;
            transition: transform .2s;
        }

        .tkd-panel-head[aria-expanded="false"] .chevron {
            transform: rotate(180deg);
        }

        /* Timeline */
        .tkd-tl {
            max-height: 70px;
        }

        .tkd-tl::-webkit-scrollbar {
            width: 3px;
        }

        .tkd-tl::-webkit-scrollbar-thumb {
            background: var(--bs-border-color);
            border-radius: 4px;
        }

        .tkd-tl-item {
            padding-bottom: 12px;
        }

        .tkd-tl-item:last-child {
            padding-bottom: 0;
        }

        .tkd-tl-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 7px;
            bottom: 8px;
            width: 1px;
            background: #EBEBEB;
            z-index: 0;
        }

        .tkd-tl-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #EBEBEB;
            flex-shrink: 0;
            margin-top: 1px;
            position: relative;
            z-index: 1;
        }

        .people-row+.people-row {
            margin-top: 8px;
        }

        /* comment summernote  start ===================================== */

        .tkd-body .note-editor.note-frame {
            border: 1.5px solid #E5E5E5 !important;
            border-radius: 5px;
            box-shadow: none !important;
            overflow: hidden;
        }

        .tkd-body .note-editor.note-frame .note-toolbar {
            display: none !important;
        }

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            padding: 12px 14px 44px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            caret-color: #2563eb;
        }

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable[contenteditable="true"]:empty:before {
            color: #9ca3af;
            font-weight: 500;
        }

        .tkd-body .note-editor.note-frame .note-statusbar {
            display: none !important;
        }

        /* ── Custom bottom toolbar ── */
        .tkd-body .custom-toolbar {
            display: flex;
            align-items: center;
            gap: 0px;
            padding: 3px 8px;
            border-top: 1.5px solid #e5e7eb;
            background: #fafafa;
            border-radius: 0 0 9px 9px;
            margin-top: -2px;
        }

        .tkd-body .custom-toolbar button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.15s, color 0.15s;
        }

        .tkd-body .custom-toolbar button:hover {
            background: #f3f4f6;
            color: #111827;
        }


        .tkd-body .note-popover {
            display: none !important;
        }

        .tkd-body .note-minibar {
            display: flex !important;
            gap: 4px !important;
            padding: 0px !important;
            border: none !important;
            border-radius: 8px !important;
            margin-bottom: 6px !important;
            box-shadow: none !important;
            flex-wrap: wrap !important;
            border-color: #d1d5db !important;
        }

        .tkd-body .note-minibar button {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 3px !important;
            padding: 3px 7px !important;
            font-size: 13px !important;
            cursor: pointer !important;
            color: #374151 !important;
            /* your custom overrides below */
        }

        .tkd-body .note-minibar button:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        .tkd-body .note-editor.note-airframe .note-placeholder,
        .tkd-body .note-editor.note-frame .note-placeholder {
            color: #000000;
            padding: 8px 16px !important;
        }


        .tkd-body .note-editor.note-airframe .note-editing-area .note-editable,
        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            height: 60px !important;
            word-wrap: break-word;
            overflow: auto;
            padding: 10px;
        }

        /* comment summernote end =======================================================*/

        /* reply summernote start =======================================================*/

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            border: 1px solid #E5E5E5;
            border-radius: 8px;
            box-shadow: none !important;
        }

        []

        .tkd-body #reply-summernote-wrapper .note-editing-area .note-editable {
            min-height: 150px;
            padding: 10px 14px;
            font-size: 12.5px;
            color: var(--bs-body-color);
            background: var(--bs-body-bg);
        }

        .tkd-body #reply-summernote-wrapper .custom-toolbar {
            display: flex;
            align-items: center;
            gap: 0px;
            padding: 14px 20px;
            border-top: 1.5px solid #e5e7eb;
            background: #fafafa;
            border-radius: 0 0 9px 9px;
            margin-top: -2px;
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame .note-placeholder {
            color: #000000;
            padding: 16px 20px !important;
        }

        /* reply summernote end =======================================================*/


        .tkd-body #summernote-wrapper,
        .tkd-body #reply-summernote-wrapper {
            visibility: hidden;
            min-height: 80px;
        }

        .tkd-body #summernote-wrapper.sn-ready,
        .tkd-body #reply-summernote-wrapper.sn-ready {
            visibility: visible;
        }

        #updateTicket .select2-container {
            width: 100% !important;
        }

        #updateTicket .select2-container--default .select2-selection--single {
            height: 32px;
            border-radius: 5px;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000000
        }

        #updateTicket .select2-container--default .select2-selection--single {
            height: 32px !important;
            border: 1px solid #E5E5E5 !important;
            border-radius: 5px !important;
            background: #fff !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 10px !important;
            position: relative !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0 !important;
            padding-right: 30px !important;
            /* room for arrow */
            font-size: 12.5px !important;
            color: #000 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute !important;
            /* ← critical */
            top: 0 !important;
            right: 6px !important;
            width: 24px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: none !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__arrow::after {
            content: '' !important;
            display: block !important;
            width: 16px !important;
            height: 16px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: contain !important;
            transition: transform 0.2s ease !important;
        }

        #updateTicket .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow::after {
            transform: rotate(180deg) !important;
        }

        .select2-dropdown {
            border: 1px solid #E5E5E5;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            font-size: 12.5px;
            overflow: hidden;
        }

        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: var(--bs-body-color);
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: #f0f4ff;
            color: #111C2D;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #eef2ff;
            color: #4338ca;
        }

        #select-status,
        #select-priority {
            display: none;
        }

        .s2-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        @media (max-width: 1199px) {
            .tkd-body {
                grid-template-columns: 1fr 260px;
                padding: 12px 14px 80px;
            }
        }

        @media (max-width: 991px) {
            .tkd-body {
                grid-template-columns: 1fr;
                padding: 12px 12px 90px;
            }

            .tkd-meta {
                grid-template-columns: 1fr;
            }

            .tkd-resolve-desktop {
                display: none !important;
            }
        }

        @media (max-width: 575px) {
            .tkd-fav-text {
                display: none;
            }

            .meta-row {
                grid-template-columns: 90px 1fr;
                font-size: 12px;
            }

            .tkd-body {
                padding: 8px 8px 90px;
                gap: 10px;
            }
        }

        /* Figma aligned ticket details overrides */
        .tkd-page-header {
            min-height: 58px;
            /* padding-left: 22px; */
            background: #FFF0E7 !important;
            border-bottom: 1px solid #F2DFD5;
        }

        .tkd-page-header svg {
            width: 18px;
            height: 18px;
            color: #8D8D8D;
        }

        .tkd-page-header .h3-text {
            color: #444444;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .tkd-action-bar {
            min-height: 54px;
            padding: 12px 20px;
            background: #F5F7FB;
            border-bottom: 1px solid #E7EBF2;
        }

        .tkd-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 30px;
            padding: 0 12px;
            border: 1px solid #E1E5EC;
            border-radius: 5px;
            background: #FFFFFF;
            color: #8A8F98;
            font-size: 0.75rem;
            font-weight: 400;
            line-height: 1;
        }

        .tkd-action-btn svg {
            font-size: 0.75rem;
            color: #A4A9B1;
        }

        .tkd-body {
            --tkd-page: #FFFFFF;
            --tkd-surface: #FFFFFF;
            --tkd-soft: #F4F6FF;
            --tkd-softer: #F7F8FB;
            --tkd-border: #E5E5E5;
            --tkd-text: #111C2D;
            --tkd-muted: #6B7280;
            --tkd-toolbar: #EDF2FA;
            --tkd-danger: #FF0A0E;
            gap: 24px;
            padding: 16px 20px 28px;
            background: var(--tkd-page);
        }

        .tkd-main-card {
            background: var(--tkd-surface);
            border-color: var(--tkd-border);
            border-radius: 6px;
        }

        .tkd-status-strip {
            min-height: 58px;
            background: var(--tkd-surface);
            border-color: var(--tkd-border) !important;
        }

        .tkd-body .amg-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 33px;
            padding: 0 12px;
            border: 1px solid var(--tkd-border);
            border-radius: 4px;
            background: var(--tkd-surface);
            color: var(--tkd-text);
            font-size: .75rem;
            font-weight: 500;
            line-height: 1;
        }

        .tkd-body .amg-badge svg {
            width: 18px;
            height: 18px;
        }

        .tkd-body .badge-high {
            color: #6B4F4F;
            background: #FFF7F5;
            font-weight: 500;
        }

        .tkd-meta {
            grid-template-columns: 1fr 0.78fr;
            min-height: 210px;
            border-bottom: 1px solid var(--tkd-border);
        }

        .meta-row svg path,
        .tkd-body .custom-toolbar svg path {
            fill: currentColor;
        }

        .tkd-assignment-card {
            width: 76% !important;
            min-width: 19.063rem;
            margin-left: auto;
            background: var(--tkd-surface) !important;
            border-color: var(--tkd-border) !important;
        }

        .tkd-conversation {
            position: relative;
            min-height: 250px;
            background: var(--tkd-surface);
            border-bottom: 1px solid var(--tkd-border);
        }

        .tkd-reply {
            padding: 18px 24px 22px;
            background: var(--tkd-surface);
        }

        .tkd-section-title,
        .tkd-panel-title {
            color: var(--tkd-text) !important;
            font-size: 1rem !important;
            color: #111C2D;
        }

        .tkd-icon-plain {
            width: 26px;
            height: 26px;
            border: 0;
            background: transparent;
            color: var(--tkd-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .tkd-conversation-list {
            max-height: 185px;
            overflow-y: auto;
            padding: 9px 5px 2px 6px;
            scrollbar-width: none;  
        }

        .tkd-conversation-card {
            padding: 12px 14px;
            margin-bottom: 12px;
            border: 1px solid var(--tkd-border);
            border-radius: 6px;
            background: var(--tkd-surface);
            box-shadow: 0 4px 14px rgba(17, 28, 45, .08);
        }

        .tkd-conversation-card:last-child {
            margin-bottom: 0;
        }

        .tkd-conversation-card>div {
            width: 100%;
        }

        .tkd-comment-body {
            overflow: hidden;
            max-height: 1000px;
            opacity: 1;
            transition: max-height .22s ease, opacity .18s ease, margin .18s ease, padding .18s ease;
        }

        .tkd-conversation-card.is-collapsed .tkd-comment-body {
            max-height: 0;
            opacity: 0;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .tkd-comment-toggle i {
            pointer-events: none;
        }

        .tkd-conversation-list::-webkit-scrollbar {
            width: 5px;
        }

        .tkd-conversation-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .tkd-conversation-list::-webkit-scrollbar-thumb {
            background: #D6DAE1;
            border-radius: 10px;
        }

        .tkd-message-body {
            width: min(760px, 100%);
            margin-bottom: 0;
            line-height: 1.5;
            color: var(--tkd-muted) !important;
        }

        .tkd-message-label {
            display: block;
            color: var(--tkd-text);
        }

        .tkd-message-label span {
            color: var(--tkd-danger);
        }

        .tkd-internal-note input {
            width: 14px;
            height: 14px;
            accent-color: #FF3B3F;
        }

        .tkd-panel {
            background: var(--tkd-surface);
            border-color: var(--tkd-border);
            border-radius: 9px;
        }

        .tkd-panel-head {
            background: var(--tkd-soft);
            border-bottom-color: var(--tkd-border) !important;
            text-decoration: none;
        }

        .tkd-tl-item:not(:last-child)::before,
        .tkd-tl-dot {
            background: var(--tkd-border);
        }

        .tkd-body .note-editor.note-frame {
            border-color: var(--tkd-border) !important;
            background: var(--tkd-surface);
        }

        .tkd-body .note-editor.note-frame .note-editing-area .note-editable {
            color: var(--tkd-text);
            background: var(--tkd-surface);
        }

        .tkd-body .custom-toolbar {
            border-color: var(--tkd-border);
            background: var(--tkd-softer);
        }

        .tkd-body .custom-toolbar button {
            color: var(--tkd-muted);
        }

        .tkd-body .note-editor.note-airframe .note-placeholder,
        .tkd-body .note-editor.note-frame .note-placeholder {
            color: var(--tkd-muted);
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame {
            /* display: flex !important;
            flex-direction: column; */
            border-color: var(--tkd-border) !important;
            border-radius: 5px;
        }

        .tkd-body #reply-summernote-wrapper .note-editing-area .note-editable {
            height: 142px !important;
            min-height: 142px;
            color: var(--tkd-text);
            background: var(--tkd-surface);
        }

        .tkd-body #reply-summernote-wrapper .custom-toolbar {
            order: -1;
            padding: 7px 12px;
            border-top: 0;
            border-bottom: 1px solid var(--tkd-border);
            background: var(--tkd-toolbar);
            border-radius: 5px 5px 0 0;
            margin-top: 0;
        }

        .tkd-body #reply-summernote-wrapper .note-editor.note-frame .note-placeholder {
            color: var(--tkd-muted);
            /* padding: 52px 14px 14px !important; */
        }

        #updateTicket {
            background: var(--tkd-surface);
            border-color: var(--tkd-border) !important;
        }

        #updateTicket .select2-container--default .select2-selection--single {
            border-color: var(--tkd-border) !important;
            background: var(--tkd-surface) !important;
        }

        #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--tkd-text) !important;
        }

        .select2-dropdown {
            border-color: var(--tkd-border);
            background: var(--tkd-surface);
        }

        .select2-container--default .select2-results__option {
            color: var(--tkd-text);
        }

        [data-bs-theme="dark"] .tkd-page-header {
            background: #2B211C !important;
            border-bottom-color: var(--dark-border, #2a2a2d);
        }

        [data-bs-theme="dark"] .tkd-page-header .h3-text,
        [data-bs-theme="dark"] .tkd-page-header svg {
            color: var(--text-primary, #ffffff);
        }

        [data-bs-theme="dark"] .tkd-action-bar {
            background: var(--dark-primary, #191919);
            border-bottom-color: var(--dark-border, #2a2a2d);
        }

        [data-bs-theme="dark"] .tkd-action-btn {
            background: var(--dark-secondary, #2a2a2a);
            border-color: var(--dark-border, #2a2a2d);
            color: var(--text-muted, #b7b7b7);
        }

        [data-bs-theme="dark"] .tkd-body {
            --tkd-page: var(--dark-primary, #191919);
            --tkd-surface: var(--dark-secondary, #2a2a2a);
            --tkd-soft: #202434;
            --tkd-softer: #1F1F1F;
            --tkd-border: var(--dark-border, #2a2a2d);
            --tkd-text: var(--text-primary, #ffffff);
            --tkd-muted: var(--text-muted, #b7b7b7);
            --tkd-toolbar: #242A36;
        }

        [data-bs-theme="dark"] .tkd-body .amg-badge,
        [data-bs-theme="dark"] .tkd-assignment-card,
        [data-bs-theme="dark"] #updateTicket {
            background: var(--tkd-surface) !important;
            border-color: var(--tkd-border) !important;
            color: var(--tkd-text) !important;
        }

        [data-bs-theme="dark"] .tkd-body .badge-high {
            background: #2D201F !important;
            color: #FFD0CB !important;
        }

        [data-bs-theme="dark"] .tkd-conversation-card {
            box-shadow: 0 5px 16px rgba(0, 0, 0, .35);
        }

        [data-bs-theme="dark"] .tkd-body .opacity-70,
        [data-bs-theme="dark"] .tkd-body .opacity-60 {
            color: var(--tkd-muted) !important;
            opacity: 1 !important;
        }

        [data-bs-theme="dark"] .tkd-body .border-bottom,
        [data-bs-theme="dark"] .tkd-body .border,
        [data-bs-theme="dark"] .tkd-body .border-end {
            border-color: var(--tkd-border) !important;
        }

        [data-bs-theme="dark"] .tkd-body .custom-toolbar button,
        [data-bs-theme="dark"] .tkd-body .note-minibar button {
            color: var(--tkd-muted) !important;
            background: transparent !important;
            border-color: var(--tkd-border) !important;
        }

        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame .note-placeholder,
        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame .note-editable,
        [data-bs-theme="dark"] #updateTicket .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--tkd-text) !important;
        }

        [data-bs-theme="dark"] .tkd-body #reply-summernote-wrapper .note-editor.note-frame{
            border-color: var( --tkd-muted) !important;
        } 
        [data-bs-theme="dark"] .tkd-body .note-editor.note-frame{
            border-color: var( --tkd-muted) !important;
        }

        @media (max-width: 1199px) {
            .tkd-body {
                grid-template-columns: 1fr 270px;
                gap: 18px;
                padding: 14px 14px 28px;
            }
        }

        @media (max-width: 991px) {

            .tkd-body,
            .tkd-meta {
                grid-template-columns: 1fr;
            }

            .tkd-assignment-card {
                width: 100% !important;
                min-width: 0;
            }
        }

        @media (max-width: 575px) {
            .tkd-page-header {
                padding-left: 14px;
            }

            .tkd-page-header .h3-text {
                font-size: 14px;
            }

            .tkd-action-bar {
                padding: 10px 12px;
            }

            .tkd-body {
                padding: 10px 10px 24px;
            }

            .tkd-reply {
                padding: 16px;
            }
        }
    </style>
@endpush


@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.addEventListener('click', function(event) {
                var button = event.target.closest('.tkd-comment-toggle');
                if (!button) return;

                var card = button.closest('.tkd-conversation-card');
                if (!card) return;

                var isCollapsed = card.classList.toggle('is-collapsed');
                button.setAttribute('aria-expanded', String(!isCollapsed));
                button.setAttribute('aria-label', isCollapsed ? 'Expand conversation' : 'Collapse conversation');
                button.innerHTML = isCollapsed ?
                    '<i class="bi bi-arrows-angle-expand"></i>' :
                    '<i class="bi bi-arrows-angle-contract"></i>';
            });

            document.querySelectorAll('.collapse').forEach(function(el) {
                var trig = document.querySelector('[href="#' + el.id + '"], [data-bs-target="#' + el.id +
                    '"]');
                if (!trig) return;
                el.addEventListener('show.bs.collapse', function() {
                    trig.setAttribute('aria-expanded', 'true');
                    trig.classList.add('border-0');
                });
                el.addEventListener('hide.bs.collapse', function() {
                    trig.setAttribute('aria-expanded', 'false');
                    trig.classList.remove('border-0');
                });
            });

        });

        $(function() {

            var $wrapper = $('#summernote-wrapper');
            $wrapper.find('.note-minibar').remove();

            $('#summernote').summernote({
                placeholder: 'Add a comment',
                tabsize: 2,
                height: 60,
                toolbar: [],
                popover: {
                    image: [],
                    link: [],
                    air: []
                },
                callbacks: {
                    onInit: function() {
                        $wrapper.find('.note-editor.note-frame').append($('#custom-toolbar'));
                        $wrapper.addClass('sn-ready'); // ← add this
                    }
                }
            });

            $('#btn-attach').on('click', function() {
                $('#file-input').click();
            });

            $('#file-input').on('change', function() {
                if (!this.files.length) return;
                var names = Array.from(this.files).map(function(f) {
                    return f.name;
                }).join(', ');
                $('#summernote').summernote('insertText', '[Attached: ' + names + '] ');
            });

            var $miniBar = null;
            $('#btn-format').on('click', function() {
                if ($miniBar) {
                    $miniBar.remove();
                    $miniBar = null;
                    return;
                }
                $miniBar = $('<div class="note-minibar"></div>');
                [{
                        cmd: 'bold',
                        label: '<i class="bi bi-type-bold"></i>'
                    },
                    {
                        cmd: 'italic',
                        label: '<i class="bi bi-type-italic"></i>'
                    },
                    {
                        cmd: 'underline',
                        label: '<i class="bi bi-type-underline"></i>'
                    },
                    {
                        cmd: 'strikethrough',
                        label: '<i class="bi bi-type-strikethrough"></i>'
                    },
                    {
                        cmd: 'insertUnorderedList',
                        label: '<i class="bi bi-list-ul"></i>'
                    },
                    {
                        cmd: 'insertOrderedList',
                        label: '<i class="bi bi-list-ol"></i>'
                    },
                ].forEach(function(b) {
                    var $b = $('<button></button>').html(b.label);
                    $b.on('click', function(e) {
                        e.preventDefault();
                        $('#summernote').summernote(b.cmd);
                    });
                    $miniBar.append($b);
                });
                $wrapper.find('.note-editor.note-frame').before($miniBar);
            });

            var $replyWrapper = $('#reply-summernote-wrapper');
            $replyWrapper.find('.note-minibar').remove();

            $('#reply-summernote').summernote({
                placeholder: 'Share your message...',
                tabsize: 2,
                height: 100,
                toolbar: [],
                popover: {
                    image: [],
                    link: [],
                    air: []
                },
                callbacks: {
                    onInit: function() {
                        $replyWrapper.find('.note-editor.note-frame').append($(
                            '#reply-custom-toolbar'));
                        $replyWrapper.addClass('sn-ready');
                    }
                }
            });

            $('#tkd-attach').on('click', function() {
                $('#tkd-file-input').click();
            });

            $('#tkd-file-input').on('change', function() {
                if (!this.files.length) return;
                var names = Array.from(this.files).map(function(f) {
                    return f.name;
                }).join(', ');
                $('#reply-summernote').summernote('insertText', '[Attached: ' + names + '] ');
            });

            var $replyMiniBar = null;
            $('#reply-btn-format').on('click', function() {
                if ($replyMiniBar) {
                    $replyMiniBar.remove();
                    $replyMiniBar = null;
                    return;
                }
                $replyMiniBar = $('<div class="note-minibar"></div>');
                [{
                        cmd: 'bold',
                        label: '<i class="bi bi-type-bold"></i>'
                    },
                    {
                        cmd: 'italic',
                        label: '<i class="bi bi-type-italic"></i>'
                    },
                    {
                        cmd: 'underline',
                        label: '<i class="bi bi-type-underline"></i>'
                    },
                    {
                        cmd: 'strikethrough',
                        label: '<i class="bi bi-type-strikethrough"></i>'
                    },
                    {
                        cmd: 'insertUnorderedList',
                        label: '<i class="bi bi-list-ul"></i>'
                    },
                    {
                        cmd: 'insertOrderedList',
                        label: '<i class="bi bi-list-ol"></i>'
                    },
                ].forEach(function(b) {
                    var $b = $('<button></button>').html(b.label);
                    $b.on('click', function(e) {
                        e.preventDefault();
                        $('#reply-summernote').summernote(b.cmd);
                    });
                    $replyMiniBar.append($b);
                });
                $replyWrapper.find('.note-editor.note-frame').before($replyMiniBar);
            });


            $('#select-status').select2({
                minimumResultsForSearch: Infinity,
                dropdownParent: $('#updateTicket')
            }).next('.select2-container').css('display', 'block');

            function priorityTemplate(option) {
                if (!option.id) return option.text;

                var color = $(option.element).data('color') || '#6b7280';

                var icon = `
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path 
                            d="M2.5 12L12 3L21.5 12H15.5V21H8.5V12H2.5Z"
                            fill="${color}"
                            stroke="${color}"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                        </path>
                    </svg>
                `;

                return $(
                    '<span class="d-flex align-items-center gap-2">' +
                    '<span class="s2-icon d-flex align-items-center justify-content-center">' + icon +
                    '</span>' +
                    '<span>' + option.text + '</span>' +
                    '</span>'
                );
            }

            $('#select-priority').select2({
                minimumResultsForSearch: Infinity,
                dropdownParent: $('#updateTicket'),
                templateResult: priorityTemplate,
                templateSelection: priorityTemplate
            }).next('.select2-container').css('display', 'block');

        });
    </script>
@endpush
