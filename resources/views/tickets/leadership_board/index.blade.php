{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Technician Leadership Board
* TLB/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}

@extends('layouts.layout1')
@section('title', trans("ticket.leaderboard.technician_leaderboard"))
@section('content')
<section class="content">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text">
            {{ trans("ticket.leaderboard.technician_leaderboard") }}
        </h3>        
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="lbd-sub-header">
                <span class="title">
                    Technician Performance Analytics 
                    <span class="date" id="date-ranges"></span>
                </span>
            </div>
        </div>
        <div class="lbd-body">
            <div class="row g-3">
                <div class="col-md-4">
                    {{-- ══ LEFT PANEL (dynamic) ══ --}}
                    <div class="lbd-left">
                        <div class="lbd-toolbar d-flex align-items-center gap-2">                                
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"/>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input" id="searchInput" placeholder="Search Technician">
                            </div>                       
                            <button class="header-icon-btn-only header-icon-btn-only-sm btn-refresh" type="button" data-bs-toggle="tooltip" title="Refresh">
                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none">
                                    <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"/>
                                </svg>   
                            </button>
                           <button class="header-icon-btn-only header-icon-btn-only-sm btn-filter" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" title="Filter">
                                <svg viewBox="0 0 20 18" fill="none" width="18" height="16">
                                    <path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor"/>
                                </svg>
                            </button>
                            <button class="header-icon-btn-only header-icon-btn-only-sm btn-sort" type="button" id="short-list" data-bs-toggle="tooltip" title="Sort">
                                <svg width="18" height="15" viewBox="0 0 18 15" fill="none">
                                    <path d="M8.25 6.75C8.25 6.94891 8.17098 7.13968 8.03033 7.28033C7.88968 7.42098 7.69891 7.5 7.5 7.5H0.75C0.551088 7.5 0.360322 7.42098 0.21967 7.28033C0.0790178 7.13968 0 6.94891 0 6.75C0 6.55109 0.0790178 6.36032 0.21967 6.21967C0.360322 6.07902 0.551088 6 0.75 6H7.5C7.69891 6 7.88968 6.07902 8.03033 6.21967C8.17098 6.36032 8.25 6.55109 8.25 6.75ZM0.75 1.5H13.5C13.6989 1.5 13.8897 1.42098 14.0303 1.28033C14.171 1.13968 14.25 0.948912 14.25 0.75C14.25 0.551088 14.171 0.360322 14.0303 0.21967C13.8897 0.0790178 13.6989 0 13.5 0H0.75C0.551088 0 0.360322 0.0790178 0.21967 0.21967C0.0790178 0.360322 0 0.551088 0 0.75C0 0.948912 0.0790178 1.13968 0.21967 1.28033C0.360322 1.42098 0.551088 1.5 0.75 1.5ZM6 12H0.75C0.551088 12 0.360322 12.079 0.21967 12.2197C0.0790178 12.3603 0 12.5511 0 12.75C0 12.9489 0.0790178 13.1397 0.21967 13.2803C0.360322 13.421 0.551088 13.5 0.75 13.5H6C6.19891 13.5 6.38968 13.421 6.53033 13.2803C6.67098 13.1397 6.75 12.9489 6.75 12.75C6.75 12.5511 6.67098 12.3603 6.53033 12.2197C6.38968 12.079 6.19891 12 6 12ZM17.7806 9.96937C17.711 9.89964 17.6283 9.84432 17.5372 9.80658C17.4462 9.76884 17.3486 9.74941 17.25 9.74941C17.1514 9.74941 17.0538 9.76884 16.9628 9.80658C16.8717 9.84432 16.789 9.89964 16.7194 9.96937L14.25 12.4397V5.25C14.25 5.05109 14.171 4.86032 14.0303 4.71967C13.8897 4.57902 13.6989 4.5 13.5 4.5C13.3011 4.5 13.1103 4.57902 12.9697 4.71967C12.829 4.86032 12.75 5.05109 12.75 5.25V12.4397L10.2806 9.96937C10.1399 9.82864 9.94902 9.74958 9.75 9.74958C9.55098 9.74958 9.36011 9.82864 9.21937 9.96937C9.07864 10.1101 8.99958 10.301 8.99958 10.5C8.99958 10.699 9.07864 10.8899 9.21937 11.0306L12.9694 14.7806C13.039 14.8504 13.1217 14.9057 13.2128 14.9434C13.3038 14.9812 13.4014 15.0006 13.5 15.0006C13.5986 15.0006 13.6962 14.9812 13.7872 14.9434C13.8783 14.9057 13.961 14.8504 14.0306 14.7806L17.7806 11.0306C17.8504 10.961 17.9057 10.8783 17.9434 10.7872C17.9812 10.6962 18.0006 10.5986 18.0006 10.5C18.0006 10.4014 17.9812 10.3038 17.9434 10.2128C17.9057 10.1217 17.8504 10.039 17.7806 9.96937Z" fill="#7F7F7F"/>
                                </svg>
                            </button>                              
                        </div>
                        <div id="techListLoader" style="display:none; text-align:center; padding:20px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                        <div id="noTechFound"  style="text-align:center; padding:20px;">No technicians found</div>
                        <div class="lbd-list" id="lbdList">                            
                        </div>                        
                    </div>
                </div>
                <div class="col-md-8">
                    {{-- ══ RIGHT PANEL (dynamic details) ══ --}}
                    <div class="lbd-right position-relative">
                        <div id="techDetailsLoader" class="tech-details-overlay">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2">Loading Details...</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-2 gap-2">
                           <button class="amg-refresh-btn btn-refresh">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                                </svg>
                                <span>Refresh</span>
                            </button>
                           <button class="amg-refresh-btn print-list"  id="print-list">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                                </svg>          
                                Print
                            </button>
                            
                        </div>
                        {{-- Profile card --}}
                        <div class="lbd-profile">
                            <div class="lbd-profile-left">
                                <div class="lbd-profile-av" id="profileAvatarInit">AD</div>
                                <div>
                                    {{-- <div class="lbd-profile-name" id="detail_name">-</div> --}}
                                    <div class="lbd-profile-name d-flex align-items-center gap-2">
                                        <span id="detail_name">-</span>

                                        <span id="topPerformerBadge" class="role-badge role-badge-user d-none">
                                            <i class="bi bi-star-fill me-1"></i>Top Performer
                                        </span>
                                    </div>
                                    <div class="lbd-meta-row">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                       <span id="detail_tech_id">-</span>
                                    </div>
                                    <div class="lbd-meta-row">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <span id="detail_email">-</span>
                                    </div>
                                    <div class="lbd-meta-row">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span id="detail_phone">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="lbd-rank-hex">
                                <div class="lbd-hex-wrap">
                                    <img src="{{ asset('assets/images/badges/badge.png') }}" alt="Badge">

                                    <div class="lbd-hex-content">
                                        <span class="lbd-hex-num" id="detail_rank">#</span>
                                        <span class="lbd-hex-lbl">Rank</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Performance Summary Stats --}}
                        <div>
                            <p class="lbd-sec-title">Performance Summary</p>
                            <div class="lbd-stats-3">
                                <div class="lbd-stat-card">
                                    <div><div class="lbd-stat-lbl">Feedback Score</div><div class="lbd-stat-val" id="stat_feedback">0.00</div></div>
                                    <div class="lbd-sico si-red"><svg viewBox="0 0 24 24" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                                </div>
                                <div class="lbd-stat-card">
                                    <div><div class="lbd-stat-lbl">Escalations</div><div class="lbd-stat-val" id="stat_escalations">0</div></div>
                                    <div class="lbd-sico si-green"><svg viewBox="0 0 24 24" width="18" height="18"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                                </div>
                                <div class="lbd-stat-card">
                                    <div><div class="lbd-stat-lbl">SLA Breached</div><div class="lbd-stat-val" id="stat_sla">0</div></div>
                                    <div class="lbd-sico si-blue"><svg viewBox="0 0 24 24" width="18" height="18"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                                </div>
                            </div>
                            <div class="lbd-stats-2">
                                <div class="lbd-stat-card">
                                    <div><div class="lbd-stat-lbl">Avg Response</div><div class="lbd-stat-val" id="stat_response">0 hrs</div></div>
                                    <div class="lbd-sico si-orange"><svg viewBox="0 0 24 24" width="18" height="18"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                                </div>
                                <div class="lbd-stat-card">
                                    <div><div class="lbd-stat-lbl">Not Responded Ticket</div><div class="lbd-stat-val" id="stat_ticket_not_responded">0</div></div>
                                    <div class="lbd-sico si-purple"><svg viewBox="0 0 24 24" width="18" height="18"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                                </div>
                            </div>
                        </div>

                        {{-- AI Summary --}}
                        <div class="lbd-ai-card">
                            <div class="lbd-ai-title">AI Performance Summary</div>
                            <p class="lbd-ai-body" id="summary_text">Select a technician to view details</p>
                        </div>

                        {{-- Tabs Section --}}
                        
                        <div class="tab-bar">
                            <ul class="nav nav-underline" id="myTab" role="tablist">

                                <!-- Tickets -->
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active"   id="tickets-tab" data-bs-toggle="tab" data-techId="" href="#tab_tickets"
                                    role="tab"
                                    aria-controls="tab_tickets"
                                    aria-selected="true">
                                        <span>{{trans('ticket.leaderboard.tickets')}}</span>
                                    </a>
                                </li>

                                <!-- Escalations -->
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link"
                                    id="escalations-tab"
                                    data-bs-toggle="tab"
                                    data-techId=""
                                    href="#tab_escalations"
                                    role="tab"
                                    aria-controls="tab_escalations"
                                    aria-selected="false">
                                        <span>{{trans('ticket.leaderboard.escalations')}}</span>
                                    </a>
                                </li>

                                <!-- Feedback -->
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link"
                                    id="feedback-tab"
                                    data-bs-toggle="tab"
                                    data-techId=""
                                    href="#tab_feedback"
                                    role="tab"
                                    aria-controls="tab_feedback"
                                    aria-selected="false">
                                        <span>{{trans('ticket.leaderboard.feedback')}}</span>
                                    </a>
                                </li>

                                <!-- SLA Breached -->
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link"
                                    id="sla-tab"
                                    data-bs-toggle="tab"
                                    data-techId=""
                                    href="#sla_breache"
                                    role="tab"
                                    aria-controls="sla_breache"
                                    aria-selected="false">
                                        <span>SLA Breached</span>
                                    </a>
                                </li>

                                <!-- Overall -->
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link"
                                    id="overall-tab"
                                    data-bs-toggle="tab"
                                    data-techId=""
                                    href="#handler_wise"
                                    role="tab"
                                    aria-controls="handler_wise"
                                    aria-selected="false">
                                        <span>{{trans('ticket.leaderboard.overall')}}</span>
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div class="tab-content tabcontent-border p-3" id="myTabContent">          
                            <div class="tab-pane fade show active" id="tab_tickets" role="tabpanel" aria-labelledby="tab_tickets">
                                <div class="row">
                                    <!-- Total Tickets -->
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="lbd-stat-card">
                                            <div>
                                                <div class="lbd-stat-lbl">{{trans('ticket.leaderboard.total_tickets')}}</div>
                                                <div id="ticket_total">0</div>
                                            </div>
                                            <div class="lbd-sico si-blue">
                                                <!-- Ticket Icon -->
                                                <svg viewBox="0 0 21 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.25 5.25C20.4489 5.25 20.6397 5.17098 20.7803 5.03033C20.921 4.88968 21 4.69891 21 4.5V1.5C21 1.10218 20.842 0.720644 20.5607 0.43934C20.2794 0.158035 19.8978 0 19.5 0H1.5C1.10218 0 0.720644 0.158035 0.43934 0.43934C0.158035 0.720644 0 1.10218 0 1.5V4.5C0 4.69891 0.0790176 4.88968 0.21967 5.03033C0.360322 5.17098 0.551088 5.25 0.75 5.25C1.34674 5.25 1.91903 5.48705 2.34099 5.90901C2.76295 6.33097 3 6.90326 3 7.5C3 8.09674 2.76295 8.66903 2.34099 9.09099C1.91903 9.51295 1.34674 9.75 0.75 9.75C0.551088 9.75 0.360322 9.82902 0.21967 9.96967C0.0790176 10.1103 0 10.3011 0 10.5V13.5C0 13.8978 0.158035 14.2794 0.43934 14.5607C0.720644 14.842 1.10218 15 1.5 15H19.5C19.8978 15 20.2794 14.842 20.5607 14.5607C20.842 14.2794 21 13.8978 21 13.5V10.5C21 10.3011 20.921 10.1103 20.7803 9.96967C20.6397 9.82902 20.4489 9.75 20.25 9.75C19.6533 9.75 19.081 9.51295 18.659 9.09099C18.2371 8.66903 18 8.09674 18 7.5C18 6.90326 18.2371 6.33097 18.659 5.90901C19.081 5.48705 19.6533 5.25 20.25 5.25ZM1.5 11.175C2.34772 11.0029 3.10986 10.543 3.65728 9.87319C4.20471 9.20343 4.50376 8.36502 4.50376 7.5C4.50376 6.63498 4.20471 5.79657 3.65728 5.12681C3.10986 4.45705 2.34772 3.99714 1.5 3.825V1.5H6.75V13.5H1.5V11.175ZM19.5 11.175V13.5H8.25V1.5H19.5V3.825C18.6523 3.99714 17.8901 4.45705 17.3427 5.12681C16.7953 5.79657 16.4962 6.63498 16.4962 7.5C16.4962 8.36502 16.7953 9.20343 17.3427 9.87319C17.8901 10.543 18.6523 11.0029 19.5 11.175Z" fill="#7F7F7F"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resolved -->
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="lbd-stat-card">
                                            <div>
                                                <div class="lbd-stat-lbl">{{trans('ticket.leaderboard.resolved')}}</div>
                                                <div id="ticket_resolved">0</div>
                                            </div>
                                            <div class="lbd-sico si-green">
                                                <!-- Check Icon -->
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Closed -->
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="lbd-stat-card">
                                            <div>
                                                <div class="lbd-stat-lbl">{{trans('ticket.leaderboard.closed')}}</div>
                                                <div id="ticket_pending">0</div>
                                            </div>
                                            <div class="lbd-sico si-orange">
                                                <!-- Clock Icon -->
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Open -->
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="lbd-stat-card">
                                            <div>
                                                <div class="lbd-stat-lbl">{{trans('ticket.leaderboard.open')}}</div>
                                                <div id="ticket_open">0</div>
                                            </div>
                                            <div class="lbd-sico si-red">
                                                <!-- Folder Icon -->
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v1H3V7z" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M3 10h18l-2 7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l-2-7z" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                            </div>
                                        </div>                                            
                                    </div>
                                </div>
                                <div class="chart-container">
                                    {{-- <canvas id="ticketChart"></canvas> --}}
                                    <div id="ticketChart"></div>

                                </div>

                            </div>
                            
                            <div class="tab-pane fade show" id="tab_escalations" role="tabpanel" aria-labelledby="tab_escalations">
                                <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                    <div class="col-auto">
                                        <select id="tab_escalations-page-length" class="amg-table-pagination-dropdown userModulePageLenth" aria-label="Rows per page">
                                            <option value="10" selected>Show(10)</option>
                                            <option value="15">Show(15)</option>
                                            <option value="25">Show(25)</option>
                                            <option value="50">Show(50)</option>
                                        </select>
                                    </div>

                                    <!-- spacer -->
                                    <div class="flex-grow-1"></div>
                                    <div>
                                        <div class="amg-list-searchbar" id="tab_escalations-list-search">
                                            <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                            </svg>
                                            <input type="text" class="amg-list-searchbar__input tab_escalations-list-search" id="tab_escalations-searchbox" placeholder="Search...">
                                        </div>
                                    </div>
                                    <!-- Refresh -->
                                    <button class="amg-refresh-btn tab_escalations-btn-reload"   title="{{ trans('config.department_fields.refresh_list') }}">
                                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                                        </svg>
                                        <span>{{ trans('config.department_fields.refresh_list') }}</span>
                                    </button>                                        
                                </div>

                                <div class="table-responsive" style="overflow-y:hidden;">
                                    <table name="escalation-table" id="escalation-table" class="table display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><h4>{{ trans('ticket.leaderboard.ticket_id') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.subject') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.status') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.created_at') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.updated_at') }}</h4></th>                                                                               
                                            </tr>
                                        </thead>
                                        <tbody id="escalationTable"></tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade show" id="tab_feedback" role="tabpanel" aria-labelledby="tab_escalations">
                                <div id="feedbackList" class="feedback-scroll"></div>
                            </div>

                            <div class="tab-pane fade show" id="sla_breache" role="tabpanel" aria-labelledby="sla_breache">
                                <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                    <div class="col-auto">
                                        <select id="sla_breache-page-length" class="amg-table-pagination-dropdown userModulePageLenth js-user-page-length" aria-label="Rows per page">
                                            <option value="10" selected>Show(10)</option>
                                            <option value="15">Show(15)</option>
                                            <option value="25">Show(25)</option>
                                            <option value="50">Show(50)</option>
                                        </select>
                                    </div>

                                    <!-- spacer -->
                                    <div class="flex-grow-1"></div>
                                    <div>
                                        <div class="amg-list-searchbar" id="sla_breache-list-search">
                                            <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                            </svg>
                                            <input type="text" class="amg-list-searchbar__input sla_breache-list-search" id="sla_breache-searchbox" placeholder="Search...">
                                        </div>
                                    </div>
                                    <!-- Refresh -->
                                    <button class="amg-refresh-btn sla_breache-btn-reload"   title="{{ trans('config.department_fields.refresh_list') }}">
                                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                                        </svg>
                                        <span>Refresh</span>
                                    </button>                                        
                                </div>

                                <div class="table-responsive" style="overflow-y:hidden;">
                                    <table name="sla-table" id="sla-table" class="table display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><h4>{{ trans('ticket.leaderboard.ticket_id') }}</h4></th>
                                                <th><h4>{{trans('ticket.leaderboard.subject')}}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.status') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.created_at') }}</h4></th>
                                                <th><h4>{{ trans('ticket.leaderboard.updated_at') }}</h4></th>                                                                               
                                            </tr>
                                        </thead>
                                        <tbody id="slaTable"></tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade show" id="handler_wise" role="tabpanel" aria-labelledby="handler_wise">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive gtable-cover">
                                            <table id="handlerTbl" class="mytable table table-striped">
                                                <thead>
                                                    <tr id="handlerHeaderRow"></tr>
                                                </thead>
                                                <colgroup id="handlerColGroup"></colgroup>
                                                <tbody id="handlerBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="row mar-top">
                                        <div class="col-lg-7">
                                            <div id="handler-pagebtns"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>                                
                        </div>                            
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- Filter Modal --}}
    <div class="amg-modal modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;">
            <div class="modal-content rounded-4 bg-white">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">Filter Options</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>           
                <form id="filterForm">
                    <div class="modal-body">
                        
                            <div class="row g-3 px-4">

                                <!-- Date Filter -->
                                <div class="col-12 col-lg-4 my-2 mb-xl-0">
                                    <label>{{trans('ticket.leaderboard.base_on')}}</label>
                                    <select name="based_on" id="based_on" class="form-control">
                                        <option value="null">{{trans('ticket.leaderboard.base_on')}}</option>
                                        <option value="1">Created At</option>
                                        <option value="2">Updated At</option>
                                    </select>
                                </div>

                                <!-- Date Range -->
                                <div class="col-12 col-lg-4 my-2 mb-xl-0">
                                    <label>{{trans('ticket.leaderboard.date_range')}}</label>
                                    <div id="reportrange" class="form-control d-flex align-items-center justify-content-between" style="cursor:pointer;">
                                        <span></span>
                                        <i class="bi bi-calendar"></i>
                                        <input type="hidden" name="daterange" id="daterange">
                                    </div>
                                </div>                       
                            
                                <!-- Department -->
                                <div class="col-12 col-lg-4 my-2 mb-xl-0">
                                    <label>{{trans('ticket.leaderboard.department')}}</label>
                                    <select class="form-control" id="department" name="department" multiple>
                                        <option value="">{{trans('ticket.leaderboard.select_department')}}</option>
                                    </select>
                                </div>

                                <!-- category -->
                                <div class="col-12 col-lg-4 my-2 mb-xl-0">
                                    <label>{{trans('ticket.leaderboard.category')}}</label>
                                    <select class="form-control" id="category" name="category" multiple>
                                        <option value="">{{trans('ticket.leaderboard.select_category')}}</option>
                                    </select>
                                </div>
                                <!-- subcategory -->
                                <div class="col-12 col-lg-4 my-2 mb-xl-0">
                                    <label>{{trans('ticket.leaderboard.subcategory')}}</label>
                                    <select class="form-control" id="subcategory" name="subcategory" multiple>
                                        <option value="">{{trans('ticket.leaderboard.select_subcategories')}}</option>
                                    </select>
                                </div>                        
                            </div>
                    </div>
                    <div class="modal-footer justify-content-start py-4 border-top">
                        <div class="amg-btn-group gap-3 px-4">
                            <button type="button" class="amg-btn amg-btn-secondary b1-text bg-black text-white btn-clear-filter">
                            {{trans('ticket.leaderboard.reset')}}
                            </button>
                            <button type="button" class="amg-btn amg-btn-primary b1-text btn-filter text-white">
                            {{trans('ticket.leaderboard.apply')}}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>


@endsection
@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
    <style>
        /* ══════════════════════════════════════════════════════════
        LEADERBOARD  –  lbd-*
        Dark mode vars from modetheme.css:
        --dark-primary   #191919   deepest bg
        --dark-secondary #2a2a2a   card surface
        --dark-border    #2a2a2d   all borders
        --dark-hover     #262626   hover bg
        --text-primary   #ffffff   main text
        --text-secondary #e5e7eb   body text
        --text-muted     #757575   secondary text
        --app-bg         #141414   page bg
        --app-surface    #111827   card surface light
        ══════════════════════════════════════════════════════════ */        
        /* ── Sub header ───────────────────────────────────────── */
        .lbd-sub-header {
        padding      : 10px 16px;
        font-size    : 13px; font-weight:600;
        color        : var(--bs-body-color);
        display      : flex; align-items:center; gap:6px;
        border-bottom: 1px solid var(--bs-border-color);
        background   : var(--app-surface,var(--bs-body-bg));
        }
        [data-bs-theme="dark"] .lbd-sub-header {
        background  : var(--dark-primary,#191919) !important;
        border-color: var(--dark-border,#2a2a2d) !important;
        color       : var(--text-primary,#fff) !important;
        }
        .lbd-date-range { font-size:12px; font-weight:400; color:var(--bs-secondary-color); }
        [data-bs-theme="dark"] .lbd-date-range { color:var(--text-muted,#757575) !important; }

        /* ── Body layout ──────────────────────────────────────── */
        .lbd-body {
            padding: 14px 16px;
            gap                  : 16px;
            padding              : 14px 16px calc(var(--footer-height,30px) + 16px);
            background           : var(--app-bg,#EFF2FA);
            align-items          : start;
            width                : 100%;
            box-sizing           : border-box;
            min-width            : 0;
        }
        .lbd-body .row {
            display: flex;
            align-items: stretch;
        }
        [data-bs-theme="dark"] .lbd-body {
        background: var(--app-bg,#141414) !important;
        }

        /* ══════════════════════════════════════════════════════
        LEFT PANEL  — white card on grey page bg
        ══════════════════════════════════════════════════════ */
        .lbd-left {
            height: calc(100vh - 140px);
            background   : #EFF2FA;
            border       : 1px solid #e8ecf0;
            border-radius: 12px;
            overflow     : hidden;
            box-shadow   : 0 1px 4px rgba(0,0,0,.05);
            display      : flex;
            flex-direction: column;
            min-width    : 0;
        }
        [data-bs-theme="dark"] .lbd-left {
        background  : var(--dark-secondary,#2a2a2a) !important;
        border-color: var(--dark-border,#2a2a2d) !important;
        box-shadow  : 0 2px 8px rgba(0,0,0,.5) !important;
        }
        [data-bs-theme="dark"] .lbd-item {
            background  : var(--dark-primary,#191919) !important;
            border-color: var(--dark-border,#2a2a2d) !important;
        }
        [data-bs-theme="dark"] .lbd-item.top3:hover  { box-shadow:0 3px 14px rgba(0,0,0,.4) !important; }
        [data-bs-theme="dark"] .amg-list-searchbar input { color:var(--text-primary,#fff) !important; }
        [data-bs-theme="dark"] .amg-list-searchbar input::placeholder { color:var(--text-muted,#757575) !important; }


        /* Toolbar */
        .lbd-toolbar {
        display      : flex;
        align-items  : center;
        /* gap          : 7px; */
        padding      : 6px 12px;
        border-bottom: 1px solid var(--bs-border-color);
        }
        [data-bs-theme="dark"] .lbd-toolbar { 
            background  : var(--dark-primary,#191919) !important;
            border-color:var(--dark-border,#2a2a2d) !important;
         }
        .lbd-list {
            flex: 1;
            overflow-y: auto;
            max-height: 600px;
            padding     : 10px 10px 0;
            display     : flex;
            flex-direction: column;
            gap         : 6px;
        }       
        
        .lbd-list::-webkit-scrollbar {
            width: 11px;
        }

        .lbd-rc-default {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            color: white;
        }
        .lbd-rank-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 700;
            color: #fff !important;
            flex-shrink: 0;
        }

        .lbd-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Dark mode */
        [data-bs-theme="dark"] .lbd-list::-webkit-scrollbar-thumb {
            background  : var(--dark-primary,#2a2a2a) !important;
        }
        .ldb-list{
            display: flex;
            height: 92px;
            padding: 20px 16px 10px;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            align-self: stretch;
         }
        .lbd-list::-webkit-scrollbar       { width:3px; }
        .lbd-list::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:3px; }
        [data-bs-theme="dark"] .lbd-list::-webkit-scrollbar-thumb { background:var(--dark-border,#2a2a2d); }
        [data-bs-theme="dark"] .lbd-list-divider { background:var(--dark-border,#2a2a2d) !important; }

        /* TOP 3 items — white card with shadow */
        .lbd-item {
            display      : flex;
            align-items  : center;
            gap          : 10px;
            padding: 8px 16px ;
            cursor       : pointer;
            border-radius: 10px;
            transition   : box-shadow .15s, background .15s;
            position     : relative;
            background   : #fff;
            border       : 1px solid #f0f0f0;
            margin       : 5px
        }
        .lbd-item.top3 {
        box-shadow: 0 1px 6px rgba(0,0,0,.08);
        }
        .lbd-item.top3:hover {
        box-shadow: 0 3px 14px rgba(0,0,0,.10);
        }     

        .lbd-item.active {
            border-left  : 3px solid #ef4444 !important;
            padding-left : 9px !important;
            background   : #fff !important;
            box-shadow   : 0 2px 10px rgba(239,68,68,.10) !important;
        }
        
        [data-bs-theme="dark"] .lbd-item.active {
            background  : var(--dark-primary,#191919) !important;
            border-left : 3px solid #ef4444 !important;
            box-shadow  : 0 2px 10px rgba(239,68,68,.2) !important;
        }
        /* Rank circle */
        .lbd-rank-circle {
        width:26px; height:26px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-size:10.5px; font-weight:700; color:#fff !important;
        flex-shrink:0;
        }
        /* Per-rank colours matching screenshot */
        .lbd-rc-1 { background:linear-gradient(135deg,#f59e0b,#d97706); } /* gold */
        .lbd-rc-2 { background:linear-gradient(135deg,#22c55e,#16a34a); } /* green */
        .lbd-rc-3 { background:linear-gradient(135deg,#6366f1,#4338ca); } /* indigo */
        .lbd-rc-5 { background:#d1d5db; color:#6b7280 !important; }
        .lbd-rc-6 { background:linear-gradient(135deg,#14b8a6,#0891b2); } /* teal */
        .lbd-rc-7 { background:#d1d5db; color:#6b7280 !important; }

        /* Avatar */
        .lbd-av {
        width:25px; height:25px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:14px; font-weight:700; color:#fff !important; overflow:hidden;
        }

        /* Name / id */
        .lbd-name { font-size:13px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .lbd-tid  { font-size:11px; color:#9ca3af; }
        [data-bs-theme="dark"] .lbd-name { color:var(--text-primary,#fff) !important; }
        [data-bs-theme="dark"] .lbd-tid  { color:var(--text-muted,#757575) !important; }

        /* Score */
        .lbd-score-wrap { margin-left:auto; display:flex; flex-direction:column; align-items:flex-end; gap:2px; flex-shrink:0; }
        .lbd-score-pill {
        background:#f0fdf4; color:#16a34a !important;
        border:1px solid #bbf7d0; border-radius:5px;
        font-size:11.5px; font-weight:600; padding:2px 8px;
        }
        [data-bs-theme="dark"] .lbd-score-pill {
        background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important;
        }
        .lbd-score-chg {
        display:flex; align-items:center; gap:2px;
        font-size:11px; color:#ef4444 !important;
        }
        .lbd-score-chg svg { width:10px; height:10px; }

        /* ══════════════════════════════════════════════════════
        RIGHT PANEL  — detail
        ══════════════════════════════════════════════════════ */
        .lbd-right {
            height: calc(100vh - 140px);
            overflow-y: auto;
            padding-right: 6px;
        }

        .lbd-right::-webkit-scrollbar {
            width: 6px;
        }
        .lbd-right::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .tab-content {
            max-height: 350px; /* adjust as needed */
            overflow-y: auto;
        }

        /* better UX */
        .tab-content::-webkit-scrollbar {
            width: 5px;
        }
        .tab-content::-webkit-scrollbar-thumb {
            background: #bdbdbd;
            border-radius: 10px;
        }

        .tab-bar {
            position: sticky;
            top: 0;
            background: inherit;
            z-index: 5;
        }

        /* Handler Wise Table Scroll + Column Width */
        #handler_wise .gtable-cover {
            max-height: 420px;          
            overflow: auto;         
        }

        /* Force fixed layout so widths are respected */
        #handler_wise table {
            table-layout: fixed;
            min-width: fit-content;        
            /* white-space: nowrap; */
        }

        /* Column width control */
        #handler_wise th,
        #handler_wise td {
            /* min-width: 80px;
            max-width: 80px;
            width:80px; */
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* Optional: Sticky header while vertical scrolling */
        #handler_wise thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #fff;
            word-wrap: break-word;
        }

        .escalation-table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
        }

        /** table of the grid design */
        .vertical-matrix {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            width: fit-content;
        }

        .vm-cell {
            padding: 8px 10px;
            font-size: 13px;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
        }

        .vm-label {
            font-weight: 600;
            background: #f9fafb;
            color: #000000;
        }
        [data-bs-theme="dark"] .vm-label{
            background: var(--dark-primary,#191919) !important;
        }
        [data-bs-theme="dark"] .vm-value {
            background: var(--dark-secondary,#2a2a2a) !important;
        }

        .vm-value {
            background: #fff;
        }

        .vertical-matrix .vm-cell:nth-child(6n) {
            border-right: none;
        }

        .vertical-matrix .vm-cell:nth-last-child(-n+6) {
            border-bottom: none;
        }


        /* Profile card */
        .lbd-profile {
        background   : #ffffff;
        border       : 1px solid #e8ecf0;
        border-radius: 12px;
        padding      : 3px 20px;
        display      : flex;
        align-items  : center;
        justify-content: space-between;
        gap          : 16px;
        flex-wrap    : wrap;
        box-shadow   : 0 1px 4px rgba(0,0,0,.04);
        }
        [data-bs-theme="dark"] .lbd-profile {
        background  : var(--dark-secondary,#2a2a2a) !important;
        border-color: var(--dark-border,#2a2a2d) !important;
        box-shadow  : none !important;
        }
        .lbd-profile-left { display:flex; align-items:center; gap:14px; }
        .lbd-profile-av {
        width:68px; height:68px; border-radius:50%; flex-shrink:0;
        background:linear-gradient(135deg,#6366f1,#4338ca);
        border:3px solid #e5e7eb;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; font-weight:700; color:#fff !important; overflow:hidden;
        }
        [data-bs-theme="dark"] .lbd-profile-av { border-color:var(--dark-border,#2a2a2d) !important; }

        .lbd-profile-name {
        font-size:17px; font-weight:700; color:#111827;
        display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;
        }
        [data-bs-theme="dark"] .lbd-profile-name { color:var(--text-primary,#fff) !important; }

        .lbd-top-badge {
        display:inline-flex; align-items:center; gap:4px;
        background:#f0fdf4; color:#16a34a !important;
        border:1px solid #bbf7d0; border-radius:20px;
        font-size:11px; font-weight:600; padding:2px 10px;
        }
        .lbd-top-badge svg { width:11px; height:11px; }
        [data-bs-theme="dark"] .lbd-top-badge {
        background:#052e16 !important; color:#4ade80 !important; border-color:#166534 !important;
        }

        .lbd-meta-row {
        display:flex; align-items:center; gap:6px;
        font-size:12.5px; color:#6b7280; margin-bottom:3px;
        }
        .lbd-meta-row:last-child { margin-bottom:0; }
        .lbd-meta-row svg { width:13px; height:13px; flex-shrink:0; }
        [data-bs-theme="dark"] .lbd-meta-row { color:var(--text-muted,#757575) !important; }

        /* Gold hexagon rank badge — pure SVG */
        .lbd-rank-hex {
        display        : flex;
        flex-direction : column;
        align-items    : center;
        gap            : 4px;
        flex-shrink    : 0;
        }
        .lbd-hex-wrap {
        position : relative;
        width    : 90px;
        height   : 104px;
        display  : flex;
        align-items    : center;
        justify-content: center;
        }
        .lbd-hex-wrap svg.hex-bg {
        position: absolute;
        inset   : 0;
        width   : 100%;
        height  : 100%;
        }
        .lbd-hex-content {
        position       : relative;
        z-index        : 2;
        display        : flex;
        flex-direction : column;
        align-items    : center;
        justify-content: center;
        gap            : 1px;
        margin-top     : 6px;
        }
        .lbd-hex-num { font-size:22px; font-weight:900; color:#5a2d00 !important; line-height:1; }
        .lbd-hex-lbl { font-size:10px; font-weight:700; color:#7c3d00 !important; }
        .lbd-ribbon  { display:flex; align-items:center; justify-content:center; margin-top:-2px; }

        /* Section title */
        .lbd-sec-title { font-size:14px; font-weight:700; color:#111827; margin:0 0 12px; }
        [data-bs-theme="dark"] .lbd-sec-title { color:var(--text-primary,#fff) !important; }

        /* Stats grid */
        .lbd-stats-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:10px; }
        .lbd-stats-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:14px; }

        .lbd-stat-card {
        background   : #ffffff;
        border       : 1px solid #e8ecf0;
        border-radius: 10px;
        padding      : 14px 16px;
        display      : flex;
        align-items  : flex-start;
        justify-content: space-between;
        gap          : 8px;
        transition   : box-shadow .15s;
        box-shadow   : 0 1px 3px rgba(0,0,0,.04);
        }
        .lbd-stat-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.07); }
        [data-bs-theme="dark"] .lbd-stat-card {
        background  : var(--dark-secondary,#2a2a2a) !important;
        border-color: var(--dark-border,#2a2a2d) !important;
        box-shadow  : none !important;
        }
        [data-bs-theme="dark"] .lbd-stat-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.5) !important; }

        .lbd-stat-lbl { font-size:12px; color:#6b7280; margin-bottom:6px; }
        .lbd-stat-val { font-size:18px; font-weight:700; color:#111827; line-height:1.2; }
        [data-bs-theme="dark"] .lbd-stat-lbl { color:var(--text-muted,#757575) !important; }
        [data-bs-theme="dark"] .lbd-stat-val { color:var(--text-primary,#fff) !important; }

        /* Stat icon */
        .lbd-sico {
        width:36px; height:36px; border-radius:8px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        }
        .lbd-sico svg { width:18px; height:18px; fill:none; stroke-width:1.8; }
        .si-red    { background:#fef2f2; } .si-red    svg { stroke:#ef4444; }
        .si-green  { background:#f0fdf4; } .si-green  svg { stroke:#22c55e; }
        .si-blue   { background:#eff6ff; } .si-blue   svg { stroke:#3b82f6; }
        .si-orange { background:#fff7ed; } .si-orange svg { stroke:#f97316; }
        .si-purple { background:#faf5ff; } .si-purple svg { stroke:#a855f7; }
        [data-bs-theme="dark"] .si-red    { background:#2d0f0e !important; }
        [data-bs-theme="dark"] .si-green  { background:#052e16 !important; }
        [data-bs-theme="dark"] .si-blue   { background:#0a1a3a !important; }
        [data-bs-theme="dark"] .si-orange { background:#431407 !important; }
        [data-bs-theme="dark"] .si-purple { background:#2d1b69 !important; }

        /* AI card */
        .lbd-ai-card {
        background   : #ffffff;
        border       : 1px solid #e8ecf0;
        border-radius: 10px;
        padding      : 14px 16px;
        box-shadow   : 0 1px 3px rgba(0,0,0,.04);
        }
        [data-bs-theme="dark"] .lbd-ai-card {
        background  : var(--dark-secondary,#2a2a2a) !important;
        border-color: var(--dark-border,#2a2a2d) !important;
        box-shadow  : none !important;
        }
        .lbd-ai-title { font-size:14px; font-weight:700; color:#111827; margin-bottom:10px; }
        .lbd-ai-body  { font-size:13px !important; color:#4b5563; line-height:1.72; margin:0; }
        [data-bs-theme="dark"] .lbd-ai-title { color:var(--text-primary,#fff) !important; }
        [data-bs-theme="dark"] .lbd-ai-body  { color:var(--text-muted,#757575) !important; }

        /* ── Responsive ───────────────────────────────────────── */
        @media (max-width:1100px) {
        .lbd-body   { grid-template-columns:260px 1fr; padding:10px 12px; }
        .lbd-stats-3{ grid-template-columns:1fr 1fr; }
        }
        @media (max-width:991px) {
        .lbd-body   { grid-template-columns:1fr; padding:10px; gap:12px; }
        .lbd-list   { max-height:280px; }
        .lbd-stats-3{ grid-template-columns:1fr 1fr; }
        .lbd-stats-2{ grid-template-columns:1fr 1fr; }
        }
        @media (max-width:767px) {
        .lbd-header { padding-right:.75rem !important; min-height:52px; }
        .lbd-page-title { font-size:14px; }
        .lbd-profile { position:relative; }
        .lbd-rank-hex { position:absolute; top:14px; right:14px; }
        .lbd-stats-3{ grid-template-columns:1fr 1fr; gap:8px; }
        .lbd-stats-2{ grid-template-columns:1fr 1fr; }
        .lbd-stat-val { font-size:16px; }
        }
        @media (max-width:480px) {
        .lbd-body   { padding:8px; }
        .lbd-stats-3{ grid-template-columns:1fr; }
        .lbd-stats-2{ grid-template-columns:1fr; }
        }
        .lbd-hex-wrap {
            position: relative;
            display: inline-block;
        }

        .lbd-hex-wrap img {
            width: 90px;
        }

        .lbd-hex-content {
            position: absolute;
            top: 43%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
        }

        .lbd-hex-num {
            display: block;
            font-size: 20px;
            /* font-weight: 700; */
            color: #333;
            line-height: 1;
        }

        .lbd-hex-lbl {
            display: block;
            font-size: 18px;
            color: #333;
            margin-top: 6px;
        }

        .lbd-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #b59df4;
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .amg-list-searchbar{
            width: 289px;
        }

        .lbd-ai-card :is(h1, h2, h3, h4, h5, h6) {
            font-size: 13px !important;
        }

        .tech-details-overlay{
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.8);
            z-index: 9999;
            display: none;
        }

        .tech-details-overlay.show{
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tech-details-overlay .loader-content{
            text-align: center;
        }
        

        #feedbackList .card {
            background: #f8f9fa;
        }

        #feedbackList .bi-star-fill,
        #feedbackList .bi-star {
            font-size: 16px;
        }

        #feedbackList h5 {
            font-size: 18px;
        }

        #feedbackList p {
            font-size: 16px;
            margin-bottom: 12px;
        }
    </style>
@endpush
@push('scripts')

   
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/leadership_board/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var config = new Object;
            config.url = new Object;
            config.urlBase = "{{ config('app.url') }}";
            config.user = {!! json_encode(Auth::user()->only("id", "first_name", "last_name", "username", "company_id")) !!};
            config.url.fetchLeaderboard = "{{ url('fetchLeaderboard') }}";
            config.url.leaderboardCalculate = "{{ url('leaderboard-calculate') }}";
            config.url.fetchLeaderboardCache = "{{ url('fetchLeaderboardCache') }}";
            config.url.leaderboardDetails = "{{ url('leaderboard') }}";
            config.url.getTechnicianDetailsCache = "{{ url('getTechnicianDetailsCache') }}";
            config.url.leaderboard_tickets = "{{ url('leaderboard/tickets') }}";
            config.url.leaderboard_escalations = "{{ url('leaderboard/escalations') }}";
            config.url.leaderboard_feedback = "{{ url('leaderboard/feedback') }}";
            config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage')}}";
            config.url.get_tickets = "{{ url('reports/tickets/jx-handler-wise') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.print_tech_performance = "{{ url('print-tech-performance') }}";
            config.url.leaderboard_sla_table = "{{ url('leaderboard/sla_table') }}";
            config.url.ticket_info = "{{ url('ticket') }}";
            config.url.user_info = "{{ url('user/info')}}";
            config.url.ticket_list = "{{ url('tickets/newlist') }}";
            config.url.esclate_tickets ="{{ url('reports/tickets/esclate-tickets') }}";
            config.base_url = "{{asset('storage/avatar')}}";
            config.default_image = "{{asset('imgs/profile-75.jpg')}}";
            config.token = "{{ csrf_token() }}";
            config.permissions = {!! json_encode($permissionArray) !!};
            config.translations = {
            press_enter_with_Search:'{{ trans('mailroom.press_enter_with_Search') }}',
        };
            new LeadershipBoard(config);
        });
    </script>
@endpush