{{-- 
* ------------------------------------------------------------
* File: reset.blade.php
* Module: Login Module (Reset Password)
* LOG/26/03
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Hrishikesh Pandey
* Page ID: #003
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Fix the UI of Error Msg and Hide the CompanyLogo Div when it have not logo (Hrishikesh Pandey)
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ 
--}}
@extends('layouts.auth')
@section('title',trans('login.login.reset_password'))


@section('style')

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        /* ══ LEFT PANEL ══ */
         .left-section {
            position: relative;
            background: linear-gradient(323.5deg, #0041A2 0.04%, #005CE6 99.96%);
            color: white;
            /* padding: 20px; */
            text-align: center;
        }




        .shadow-gradient {
            background: linear-gradient(178.92deg, rgba(0, 65, 162, 0) 0.88%, #0146AE 45.02%);
            position: absolute;
            height: 60%;
            width: 98%;
            display: flex;
            bottom: 0;
            align-items: center;
            justify-content: center;
        }

        .slogan-text {
            width: 50%;
            position: relative;
            top: 20%;
        }

        .slogan-text h2 {
            color: white;
            font-size: 20px;
        }

        .slogan-text p {
            color: white;
            font-size: 12px;
            font-weight: 500;
        }
        .left-section img {
            max-width: 70%;
        }

        /* ══ RIGHT PANEL ══ */
        .auth-right {
            min-height: 100vh;
            background: #f0f4fb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: clamp(20px, 3vh, 32px) clamp(14px, 3vw, 32px) clamp(14px, 2vh, 22px);
        }

        .top-logo-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-logo-img {
            height: 56px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }

        /* login card */
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: clamp(20px, 3vh, 34px) clamp(18px, 3.5vw, 44px) clamp(16px, 2.5vh, 28px);
            width: 100%;
            max-width: 480px;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.08);
            animation: cardIn 0.7s cubic-bezier(.22, 1, .36, 1) both 0.1s;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        .client-brand {
            text-align: center;
            margin-bottom: clamp(10px, 2vh, 20px);
        }

        .client-brand-img {
            height: clamp(32px, 5vh, 48px);
            width: auto;
            object-fit: contain;
            max-width: 220px;
        }

        .login-card h3 {
            font-size: clamp(1.05rem, 1.8vw, 1.35rem);
            font-weight: 700;
            color: #111;
            margin-bottom: 3px;
        }

        .login-card .sub {
            font-size: clamp(0.73rem, 1vw, 0.82rem);
            color: #999;
            margin-bottom: clamp(10px, 2vh, 20px);
        }

        /* inputs */
        .auth-input {
            border-radius: 50px !important;
            background: #f6f0f0 !important;
            border: none !important;
            padding: 11px 20px !important;
            font-size: 0.87rem;
            color: #333;
            box-shadow: none !important;
            font-family: 'Poppins', sans-serif;
            transition: background .2s;
        }

        .auth-input:focus {
            background: #f0e8e8 !important;
        }

        .auth-input::placeholder {
            color: #bbb;
        }

        .auth-input.is-invalid {
            border: 1.5px solid #dc3545 !important;
        }

        /* readonly username state */
        .auth-input[readonly] {
            background: #ede9e9 !important;
            color: #888;
            cursor: default;
        }

        .pwd-wrap {
            position: relative;
            margin-bottom: 10px;
        }

        .pwd-wrap .eye-btn {
            position: absolute;
            right: 16px;
            top: 22px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #bbb;
            padding: 0;
            display: flex;
            z-index: 5;
            transition: color .2s;
        }

        .pwd-wrap .eye-btn:hover {
            color: #666;
        }

        .form-check-input:checked {
            background-color: #F12F35;
            border-color: #F12F35;
        }

        .forgot-link {
            font-size: 0.78rem;
            color: #555;
            text-decoration: none;
            transition: color .2s;
        }

        .forgot-link:hover {
            color: #e0272e;
        }

        .remember-lbl {
            font-size: 0.78rem;
            color: #333;
            cursor: pointer;
        }

        /* validate error message */
        .validateUserMsg {
            display: none;
            color: #dc3545;
            font-size: 0.76rem;
            padding-left: 14px;
            margin-top: 4px;
        }
        label.error {
            color: #dc3545 !important;
            font-size: 0.76rem;
            padding-left: 14px;
            margin-top: 4px;
            display: block;
        }

        /* password & email field — hidden until username validated */
        .passwordDiv,
        .emailDiv {
            display: none;
        }

        /* remember / forgot row — hidden until password shown */
        .remember-row {
            display: none;
        }

        /* buttons */
        .btn-signin,
        .btn-continue {
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-size: 0.93rem;
            font-weight: 600;
            color: #fff;
            width: 100%;
            letter-spacing: 0.3px;
            transition: opacity .2s, transform .15s;
        }

        .btn-continue {
            background: linear-gradient(90deg, #EB0000, #EB0000);

        }

        .btn-signin {
            background: linear-gradient(90deg, #f08080, #e06060);
            box-shadow: 0 6px 18px rgba(224, 96, 96, 0.36);
            display: none; /* shown after username validated */
        }

        .btn-signin:hover,
        .btn-continue:hover {
            opacity: .9;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-signin:active,
        .btn-continue:active {
            transform: translateY(0);
            color: #fff;
        }

        /* spinner inside buttons */
        .btn-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.5);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ccc;
            font-size: 0.78rem;
            margin: clamp(8px, 1.4vh, 14px) 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ebebeb;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            border: 1.5px solid #e4e4e4;
            border-radius: 50px;
            background: #fff;
            font-size: 0.86rem;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .2s, box-shadow .2s, color .2s;
        }

        .btn-social:hover {
            border-color: #bbb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            color: #111;
        }

        .ms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        .ms-grid span { display: block; border-radius: 1px; }
        .ms-r { background: #f35325; }
        .ms-g { background: #81bc06; }
        .ms-b { background: #05a6f0; }
        .ms-y { background: #ffba08; }

        .card-links a {
            font-size: 0.77rem;
            color: #aaa;
            text-decoration: none;
            transition: color .2s;
        }

        .card-links a:hover { color: #555; }
        .card-links .sep { color: #ddd; margin: 0 6px; }

        .auth-copyright {
            font-size: 0.72rem;
            color: #aaa;
            text-align: center;
        }

        /* ══ RESPONSIVE ══ */
        @media (min-width: 992px) {
            html, body { overflow: hidden; height: 100vh; }
            .auth-left, .auth-right { height: 100vh; overflow: hidden; }
        }

        @media (max-width: 991px) {
            html, body { height: auto; overflow: auto; }
            .auth-right { min-height: auto; padding: 28px 20px 24px; gap: 20px; }
        }

        @media (max-width: 575px) {
            .login-card { padding: 20px 16px 18px; border-radius: 16px; }
            .login-card h3 { font-size: 1.05rem; }
            .auth-right { padding: 18px 10px 14px; gap: 14px; }
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .left-section img {
                max-width: 95%;
            }
        }

        @media (max-width: 770px) {
            .slogan-text {
                width: 80%;
            }
        }

        @media (max-width: 990px) {
            .left-section {
                display: none !important;
            }

            .right-section {
                width: 100% !important;
                height: 100vh !important;
                position: absolute;
                left: 0;
                top: 0;
                overflow: auto;
            }

            .version {
                position: static !important;
            }

            .microsoft-button {
                margin-top: 20px;
            }
        }

        @media (max-width: 600px) {

            .remember a,
            .remember div {
                font-size: 12px;
            }
        }

        @media screen and (min-width: 320px) and (max-width: 736px) {
            .text-left {
                width: 200px;
            }

            .form-section {
                width: 90%;
            }
        }

    </style>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-6 col-12 col-lg-6 col-xl-7 col-xxl-6 left-section">
            <div class="mt-2">
                <svg width="180" height="43" viewBox="0 0 226 63" fill="none" >
                    <path
                        d="M28.3145 15.1592L24.6592 18.7773C22.346 16.3281 19.6494 15.1035 16.5693 15.1035C13.8851 15.1035 11.6338 15.9941 9.81543 17.7754C8.00944 19.5566 7.10645 21.7214 7.10645 24.2695C7.10645 26.9043 8.04655 29.137 9.92676 30.9678C11.807 32.7985 14.1263 33.7139 16.8848 33.7139C18.6784 33.7139 20.1875 33.3366 21.4121 32.582C22.6491 31.8275 23.6449 30.6462 24.3994 29.0381H16.4951V24.1582H30.2256L30.2627 25.3086C30.2627 27.696 29.6442 29.9596 28.4072 32.0996C27.1702 34.2396 25.5684 35.8724 23.6016 36.998C21.6348 38.1237 19.3278 38.6865 16.6807 38.6865C13.848 38.6865 11.3184 38.0742 9.0918 36.8496C6.8776 35.6126 5.12109 33.8561 3.82227 31.5801C2.53581 29.304 1.89258 26.8486 1.89258 24.2139C1.89258 20.6019 3.08626 17.46 5.47363 14.7881C8.30632 11.609 11.9925 10.0195 16.5322 10.0195C18.9072 10.0195 21.1276 10.4587 23.1934 11.3369C24.9375 12.0791 26.6445 13.3532 28.3145 15.1592ZM37.2838 10.7061H42.7945C45.8128 10.7061 47.9589 10.9782 49.233 11.5225C50.5195 12.0544 51.5523 12.945 52.3316 14.1943C53.1109 15.4437 53.5006 16.9219 53.5006 18.6289C53.5006 20.4225 53.0676 21.9255 52.2018 23.1377C51.3482 24.3376 50.0556 25.2467 48.3238 25.8652L54.7809 38H49.1031L42.9615 26.4404H42.4791V38H37.2838V10.7061ZM42.4791 21.375H44.1119C45.7695 21.375 46.9075 21.1585 47.526 20.7256C48.1568 20.2926 48.4723 19.5752 48.4723 18.5732C48.4723 17.9795 48.3176 17.4661 48.0084 17.0332C47.6992 16.5879 47.2848 16.2725 46.7652 16.0869C46.2457 15.889 45.2932 15.79 43.9078 15.79H42.4791V21.375ZM60.8928 10.7061H75.7922V15.79H66.051V20.7256H75.7922V25.7168H66.051V32.8975H75.7922V38H60.8928V10.7061ZM82.5721 10.7061H97.4715V15.79H87.7303V20.7256H97.4715V25.7168H87.7303V32.8975H97.4715V38H82.5721V10.7061ZM104.251 10.7061H109.224L120.932 28.667V10.7061H126.127V38H121.136L109.447 20.0947V38H104.251V10.7061ZM189.755 15.5117L186.118 18.9814C183.644 16.3714 180.861 15.0664 177.769 15.0664C175.159 15.0664 172.957 15.957 171.163 17.7383C169.382 19.5195 168.491 21.7152 168.491 24.3252C168.491 26.1436 168.887 27.7578 169.679 29.168C170.471 30.5781 171.59 31.6852 173.037 32.4893C174.485 33.2933 176.093 33.6953 177.862 33.6953C179.371 33.6953 180.75 33.417 181.999 32.8604C183.249 32.2913 184.622 31.2646 186.118 29.7803L189.644 33.4541C187.627 35.4209 185.723 36.7878 183.929 37.5547C182.135 38.3092 180.088 38.6865 177.787 38.6865C173.544 38.6865 170.069 37.3444 167.36 34.6602C164.663 31.9635 163.315 28.5124 163.315 24.3066C163.315 21.5853 163.927 19.167 165.152 17.0518C166.389 14.9365 168.151 13.2357 170.44 11.9492C172.74 10.6628 175.214 10.0195 177.862 10.0195C180.113 10.0195 182.278 10.4958 184.356 11.4482C186.446 12.4007 188.246 13.7552 189.755 15.5117ZM209.115 10.0195C212.974 10.0195 216.29 11.4173 219.06 14.2129C221.844 17.0085 223.235 20.4163 223.235 24.4365C223.235 28.4196 221.862 31.7904 219.116 34.5488C216.382 37.3073 213.061 38.6865 209.152 38.6865C205.058 38.6865 201.656 37.2702 198.947 34.4375C196.238 31.6048 194.884 28.2402 194.884 24.3438C194.884 21.7337 195.514 19.334 196.776 17.1445C198.038 14.9551 199.77 13.2233 201.971 11.9492C204.186 10.6628 206.567 10.0195 209.115 10.0195ZM209.059 15.1035C206.536 15.1035 204.415 15.9818 202.695 17.7383C200.976 19.4948 200.116 21.7275 200.116 24.4365C200.116 27.4548 201.198 29.8421 203.363 31.5986C205.045 32.9717 206.975 33.6582 209.152 33.6582C211.614 33.6582 213.71 32.7676 215.442 30.9863C217.174 29.2051 218.04 27.0094 218.04 24.3994C218.04 21.8018 217.168 19.6061 215.424 17.8125C213.679 16.0065 211.558 15.1035 209.059 15.1035Z"
                        fill="white" />
                    <path
                        d="M133.891 10.7061H139.049V38H133.891V10.7061ZM144.122 10.7061H159.207V15.8271H154.234V38H148.964V15.8271H144.122V10.7061Z"
                        fill="#06ADF5" />
                    <path
                        d="M43.146 50.6626H48.3066V52.4146H46.6055V60H44.8027V52.4146H43.146V50.6626ZM56.9039 57.0483H51.337C51.4174 57.5392 51.6311 57.9307 51.9781 58.2227C52.3294 58.5104 52.7758 58.6543 53.3175 58.6543C53.9649 58.6543 54.5214 58.4279 54.9869 57.9751L56.4469 58.6606C56.0829 59.1769 55.6471 59.5599 55.1393 59.8096C54.6314 60.055 54.0284 60.1777 53.3302 60.1777C52.2468 60.1777 51.3645 59.8371 50.6832 59.1558C50.0019 58.4702 49.6612 57.6133 49.6612 56.585C49.6612 55.5312 49.9998 54.6574 50.6769 53.9634C51.3582 53.2651 52.2109 52.916 53.235 52.916C54.3225 52.916 55.207 53.2651 55.8883 53.9634C56.5696 54.6574 56.9103 55.5757 56.9103 56.7183L56.9039 57.0483ZM55.1646 55.6836C55.0504 55.2985 54.824 54.9854 54.4854 54.7441C54.1511 54.5029 53.7618 54.3823 53.3175 54.3823C52.8351 54.3823 52.4119 54.5177 52.0479 54.7886C51.8194 54.9578 51.6078 55.2562 51.4132 55.6836H55.1646ZM65.7233 54.4902L64.2824 55.2837C64.0116 55.0002 63.7429 54.8034 63.4763 54.6934C63.2139 54.5833 62.905 54.5283 62.5495 54.5283C61.9021 54.5283 61.3773 54.723 60.9753 55.1123C60.5775 55.4974 60.3786 55.9925 60.3786 56.5977C60.3786 57.1859 60.5712 57.6662 60.9563 58.0386C61.3413 58.411 61.847 58.5972 62.4733 58.5972C63.2478 58.5972 63.8508 58.3327 64.2824 57.8037L65.6472 58.7368C64.9066 59.6974 63.8614 60.1777 62.5114 60.1777C61.2969 60.1777 60.3448 59.818 59.655 59.0986C58.9694 58.3792 58.6267 57.5371 58.6267 56.5723C58.6267 55.9036 58.7938 55.2879 59.1281 54.7251C59.4624 54.1623 59.9279 53.7201 60.5246 53.3984C61.1255 53.0768 61.7963 52.916 62.5368 52.916C63.2224 52.916 63.8381 53.0535 64.384 53.3286C64.9299 53.5994 65.3763 53.9867 65.7233 54.4902ZM67.9476 50.4277H69.6678V53.792C70.0063 53.5 70.347 53.2821 70.6897 53.1382C71.0325 52.9901 71.3795 52.916 71.7308 52.916C72.4163 52.916 72.9939 53.153 73.4637 53.627C73.8657 54.0374 74.0667 54.6405 74.0667 55.436V60H72.3592V56.9722C72.3592 56.1724 72.3211 55.6307 72.2449 55.3472C72.1688 55.0636 72.0376 54.8521 71.8514 54.7124C71.6694 54.5728 71.443 54.5029 71.1722 54.5029C70.8209 54.5029 70.5184 54.6193 70.2645 54.8521C70.0148 55.0848 69.8413 55.4022 69.7439 55.8042C69.6932 56.0116 69.6678 56.4855 69.6678 57.2261V60H67.9476V50.4277ZM76.3988 53.0938H78.1254V53.7983C78.5189 53.4683 78.8744 53.2397 79.1918 53.1128C79.5134 52.9816 79.8414 52.916 80.1757 52.916C80.8612 52.916 81.4431 53.1551 81.9213 53.6333C82.3233 54.0396 82.5243 54.6405 82.5243 55.436V60H80.8104V56.9722C80.8104 56.147 80.7724 55.599 80.6962 55.3281C80.6242 55.0573 80.4952 54.8521 80.309 54.7124C80.127 54.5685 79.9006 54.4966 79.6298 54.4966C79.2785 54.4966 78.976 54.6151 78.7221 54.8521C78.4724 55.0848 78.2989 55.4085 78.2016 55.8232C78.1508 56.0391 78.1254 56.5067 78.1254 57.2261V60H76.3988V53.0938ZM88.1001 52.916C88.7518 52.916 89.3633 53.0789 89.9346 53.4048C90.5101 53.7306 90.9587 54.1729 91.2803 54.7314C91.6019 55.29 91.7627 55.8931 91.7627 56.5405C91.7627 57.1922 91.5998 57.8016 91.2739 58.3687C90.9523 58.9357 90.5122 59.38 89.9536 59.7017C89.395 60.019 88.7793 60.1777 88.1064 60.1777C87.1162 60.1777 86.2699 59.8265 85.5674 59.124C84.8691 58.4173 84.52 57.5604 84.52 56.5532C84.52 55.4741 84.9157 54.5749 85.707 53.8555C86.401 53.2292 87.1987 52.916 88.1001 52.916ZM88.1255 54.5474C87.5881 54.5474 87.1395 54.7357 86.7798 55.1123C86.4243 55.4847 86.2466 55.9629 86.2466 56.5469C86.2466 57.1478 86.4222 57.6344 86.7734 58.0068C87.1289 58.3792 87.5775 58.5654 88.1191 58.5654C88.6608 58.5654 89.1115 58.3771 89.4712 58.0005C89.8309 57.6239 90.0107 57.1393 90.0107 56.5469C90.0107 55.9544 89.833 55.4741 89.4775 55.106C89.1263 54.7336 88.6756 54.5474 88.1255 54.5474ZM93.6505 50.4277H95.3834V60H93.6505V50.4277ZM100.851 52.916C101.503 52.916 102.114 53.0789 102.686 53.4048C103.261 53.7306 103.71 54.1729 104.031 54.7314C104.353 55.29 104.514 55.8931 104.514 56.5405C104.514 57.1922 104.351 57.8016 104.025 58.3687C103.703 58.9357 103.263 59.38 102.705 59.7017C102.146 60.019 101.53 60.1777 100.858 60.1777C99.8674 60.1777 99.021 59.8265 98.3186 59.124C97.6203 58.4173 97.2712 57.5604 97.2712 56.5532C97.2712 55.4741 97.6669 54.5749 98.4582 53.8555C99.1522 53.2292 99.9499 52.916 100.851 52.916ZM100.877 54.5474C100.339 54.5474 99.8907 54.7357 99.531 55.1123C99.1755 55.4847 98.9978 55.9629 98.9978 56.5469C98.9978 57.1478 99.1734 57.6344 99.5246 58.0068C99.8801 58.3792 100.329 58.5654 100.87 58.5654C101.412 58.5654 101.863 58.3771 102.222 58.0005C102.582 57.6239 102.762 57.1393 102.762 56.5469C102.762 55.9544 102.584 55.4741 102.229 55.106C101.877 54.7336 101.427 54.5474 100.877 54.5474ZM111.689 53.0938H113.416V59.0098C113.416 60.1777 113.181 61.0368 112.711 61.5869C112.081 62.3317 111.131 62.7041 109.861 62.7041C109.184 62.7041 108.615 62.6195 108.154 62.4502C107.692 62.2809 107.303 62.0312 106.986 61.7012C106.668 61.3753 106.433 60.9775 106.281 60.5078H108.192C108.361 60.7025 108.579 60.8485 108.846 60.9458C109.112 61.0474 109.427 61.0981 109.791 61.0981C110.257 61.0981 110.631 61.0262 110.915 60.8823C111.198 60.7384 111.397 60.5522 111.512 60.3237C111.63 60.0952 111.689 59.7017 111.689 59.1431C111.385 59.4478 111.065 59.6678 110.731 59.8032C110.396 59.9344 110.018 60 109.595 60C108.668 60 107.885 59.6657 107.246 58.9971C106.607 58.3285 106.287 57.4821 106.287 56.458C106.287 55.362 106.626 54.4818 107.303 53.8174C107.917 53.2165 108.649 52.916 109.499 52.916C109.897 52.916 110.272 52.9901 110.623 53.1382C110.978 53.2821 111.334 53.5212 111.689 53.8555V53.0938ZM109.893 54.5347C109.347 54.5347 108.896 54.7188 108.541 55.0869C108.185 55.4508 108.008 55.91 108.008 56.4644C108.008 57.0399 108.19 57.5117 108.554 57.8799C108.917 58.248 109.374 58.4321 109.925 58.4321C110.462 58.4321 110.904 58.2523 111.251 57.8926C111.603 57.5329 111.778 57.061 111.778 56.4771C111.778 55.9015 111.603 55.4339 111.251 55.0742C110.9 54.7145 110.447 54.5347 109.893 54.5347ZM116.497 50.25C116.802 50.25 117.062 50.36 117.278 50.5801C117.498 50.8001 117.608 51.0667 117.608 51.3799C117.608 51.6888 117.5 51.9533 117.284 52.1733C117.068 52.3892 116.81 52.4971 116.51 52.4971C116.201 52.4971 115.936 52.387 115.716 52.167C115.5 51.9427 115.392 51.6719 115.392 51.3545C115.392 51.0498 115.5 50.7896 115.716 50.5737C115.932 50.3579 116.192 50.25 116.497 50.25ZM115.627 53.0938H117.367V60H115.627V53.0938ZM126.51 57.0483H120.943C121.023 57.5392 121.237 57.9307 121.584 58.2227C121.935 58.5104 122.382 58.6543 122.923 58.6543C123.571 58.6543 124.127 58.4279 124.593 57.9751L126.053 58.6606C125.689 59.1769 125.253 59.5599 124.745 59.8096C124.237 60.055 123.634 60.1777 122.936 60.1777C121.853 60.1777 120.97 59.8371 120.289 59.1558C119.608 58.4702 119.267 57.6133 119.267 56.585C119.267 55.5312 119.606 54.6574 120.283 53.9634C120.964 53.2651 121.817 52.916 122.841 52.916C123.928 52.916 124.813 53.2651 125.494 53.9634C126.175 54.6574 126.516 55.5757 126.516 56.7183L126.51 57.0483ZM124.771 55.6836C124.656 55.2985 124.43 54.9854 124.091 54.7441C123.757 54.5029 123.368 54.3823 122.923 54.3823C122.441 54.3823 122.018 54.5177 121.654 54.7886C121.425 54.9578 121.214 55.2562 121.019 55.6836H124.771ZM132.796 54.0586L131.724 55.1313C131.288 54.6997 130.892 54.4839 130.537 54.4839C130.342 54.4839 130.19 54.5262 130.08 54.6108C129.97 54.6912 129.915 54.7928 129.915 54.9155C129.915 55.0086 129.949 55.0954 130.016 55.1758C130.088 55.252 130.262 55.3577 130.537 55.4932L131.171 55.8105C131.84 56.1406 132.299 56.4771 132.549 56.8198C132.799 57.1626 132.923 57.5646 132.923 58.0259C132.923 58.6395 132.697 59.1515 132.244 59.562C131.796 59.9725 131.193 60.1777 130.435 60.1777C129.428 60.1777 128.624 59.7842 128.023 58.9971L129.089 57.8354C129.293 58.0724 129.53 58.265 129.8 58.4131C130.075 58.557 130.319 58.6289 130.53 58.6289C130.759 58.6289 130.943 58.5739 131.083 58.4639C131.222 58.3538 131.292 58.2269 131.292 58.083C131.292 57.8164 131.04 57.5562 130.537 57.3022L129.953 57.0103C128.836 56.4474 128.277 55.7428 128.277 54.8965C128.277 54.3506 128.486 53.8851 128.905 53.5C129.329 53.1107 129.868 52.916 130.524 52.916C130.973 52.916 131.394 53.0155 131.787 53.2144C132.185 53.409 132.521 53.6904 132.796 54.0586ZM139.422 50.6626H141.307C142.327 50.6626 143.061 50.7578 143.51 50.9482C143.963 51.1344 144.318 51.4391 144.576 51.8623C144.839 52.2812 144.97 52.7848 144.97 53.373C144.97 54.0247 144.799 54.5664 144.456 54.998C144.117 55.4297 143.656 55.7301 143.072 55.8994C142.729 55.9967 142.105 56.0454 141.2 56.0454V60H139.422V50.6626ZM141.2 54.3125H141.764C142.209 54.3125 142.518 54.2808 142.691 54.2173C142.865 54.1538 143 54.0501 143.097 53.9062C143.199 53.7581 143.25 53.5804 143.25 53.373C143.25 53.0133 143.11 52.751 142.831 52.5859C142.628 52.4632 142.251 52.4019 141.701 52.4019H141.2V54.3125ZM146.433 53.0938H148.197L149.943 57.1689L151.682 53.0938H153.44L150.501 60H149.378L146.433 53.0938ZM155.322 50.5483H157.048V53.0938H158.077V54.5854H157.048V60H155.322V54.5854H154.433V53.0938H155.322V50.5483ZM164.144 50.6626H165.921V58.3052H168.511V60H164.144V50.6626ZM170.538 50.5483H172.265V53.0938H173.293V54.5854H172.265V60H170.538V54.5854H169.65V53.0938H170.538V50.5483ZM179.98 50.4277H181.706V60H179.98V59.27C179.641 59.5916 179.301 59.8244 178.958 59.9683C178.619 60.1079 178.251 60.1777 177.853 60.1777C176.961 60.1777 176.188 59.8328 175.537 59.1431C174.885 58.4491 174.559 57.5879 174.559 56.5596C174.559 55.4932 174.874 54.6193 175.505 53.938C176.135 53.2567 176.901 52.916 177.803 52.916C178.217 52.916 178.607 52.9943 178.971 53.1509C179.335 53.3075 179.671 53.5423 179.98 53.8555V50.4277ZM178.158 54.5156C177.621 54.5156 177.174 54.7061 176.819 55.0869C176.463 55.4635 176.286 55.9481 176.286 56.5405C176.286 57.1372 176.465 57.6281 176.825 58.0132C177.189 58.3983 177.635 58.5908 178.164 58.5908C178.71 58.5908 179.163 58.4025 179.523 58.0259C179.883 57.645 180.062 57.1478 180.062 56.5342C180.062 55.9333 179.883 55.4466 179.523 55.0742C179.163 54.7018 178.708 54.5156 178.158 54.5156Z"
                        fill="#EBEBEB" />
                </svg>
            </div>
            </br>
            <img src="{{ asset('imgs/auth/hero-woman.png') }}" alt="" srcset="">
            <div class="shadow-gradient">
                <div class="slogan-text">
                    <h2>{{ trans('login.login.login_heading') }}</h2>
                    <p>{{ trans('login.login.tagline') }}</p>

                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-5 col-xxl-6 auth-right">

            @if (session()->has('msg'))
                @php $type = session('msg')['type'] ?? 'danger'; @endphp
                <div class="alert alert-{{ $type }} text-center mb-3">
                    {{ session('msg')['msg'] }}
                </div>
            @endif

            <div class="top-logo-bar">
                <a href="https://itassetmanagement.in/" target="_blank">
                    <img src="{{ asset('imgs/auth/logo.png') }}" alt="Asset Management Global" class="top-logo-img" />
                </a>
            </div>

            <div class="login-card">

                <div class="client-brand">
                     <img id="loginCompanyLogo" class="client-brand-img"  src="{{ CommonHelper::companyLogoById() }}" />
                </div>
                <hr>

                <h5>{{ trans('login.login.new_pass_title') }}</h5>

                <form id="psrst" name="user" role="form" action="#" method="POST">
                    @csrf
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}" />
                    <input type="hidden" name="reset_id" value="{{ $reset_id}}"/>
                    <div class="mb-2 pwd-wrap">
                        <input type="password" name="password"  id="password"  class="form-control auth-input" placeholder="Enter Your New Password" required />
                        <button type="button" class="eye-btn" onclick="togglePwd('password')" aria-label="Toggle password visibility">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <div class="help-block with-errors"></div>
                    <div class="mb-2 pwd-wrap">
                        <input type="password" name="cpassword" id="cpassword" placeholder="Enter Again Same New Password" class="form-control auth-input @error('password') is-invalid @enderror" autocomplete="current-password" />
                        <button type="button" class="eye-btn" onclick="togglePwd('cpassword')" aria-label="Toggle password visibility">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <div class="help-block with-errors"></div>

                    <button type="submit" class="btn-continue  mb-2" value="sign_in" id="btnSubmit" type="submit">
                        <span class="btn-spinner" id="continueSpinner"></span>
                        <span id="continueTxt">{{ trans('login.login.continue') }}</span>
                    </button>
                    <div class="d-flex justify-content-end  align-items-center mb-3 mt-1 remember-row">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ url('login') }}">{{ trans('login.b_login.Login') }}</a>
                        </div>
                    </div>
                </form>
                <h3 id="result" class="text-success text-center" style="display: none;"></h3>
                <div class="card-links text-center mt-2">
                    <a href="https://greenitco.com/terms-for-use" target="_blank">{{ trans('login.login.terms&condition') }}</a>
                    <span class="sep">|</span>
                    <a href="https://greenitco.com/contact-us" target="_blank">{{ trans('login.login.contact-us') }}</a>
                </div>

            </div>

            <div class="auth-copyright mt-4">
                &copy; {{ date('Y') }} <a href="https://itassetmanagement.in/" target="_blank">{{ trans('login.login.copy_right_company') }} </a>| {{ trans('login.login.all_right') }}
            </div>

        </div>
    </div>
@endsection


@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $.validator.addMethod("strongPassword", function(value, element) {
                return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&^#()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/.test(value);
            }, "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
            var httpCall = 0,
                http = '',
                psrst = $('#psrst'),
                result = $('#result');

            var psrstVal = psrst.validate({
                onsubmit: false,
                errorClass: "error",
                highlight: function (element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid");
                },       
                rules: {
                    reset_id: {
                        required: true,
                        digits: true
                    },
                    token: {
                        required: true,
                        digits: true
                    },
                    password: {
                        required: true,
                        minlength: 4,
                        maxlength: 20,
                        strongPassword: true,
                    },
                    cpassword: {
                        required: true,
                        equalTo: "#password"
                    }
                }
            });

            psrst.on('submit', function(e) {
                e.preventDefault();
                if (httpCall == 1) {
                    return;
                }

                if (psrstVal.form() === false) {
                    return false;
                }

                httpCall = 1;
                http = $.post("{{ url('jx-password-update') }}", psrst.serialize());
                http.done(function(data) {
                    if (typeof data == 'object' && data.job === 'success') {
                        localStorage.setItem('pass_reset_msg', JSON.stringify({
                            type: 'success',
                            msg: data.msg
                        }));
                        window.location.href = "{{ url('login') }}";
                    } else {
                        alert(data.msg);
                    }
                });
                http.fail(function() {
                    alert('Invalid submission. Try again');
                });
                http.always(function() {
                    http = '';
                    httpCall = 0;
                });
            });
        });
        function togglePwd(id) {
            var input = document.getElementById(id);
            var icon = event.currentTarget.querySelector('svg');

            if (input.type === "password") {
                input.type = "text";

                icon.innerHTML = `
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-5.94M9.9 4.24A10.93 10.93 0 0 1 12 4c7 0 11 8 11 8a21.77 21.77 0 0 1-2.16 3.19M1 1l22 22"/>
                `;
            } else {
                input.type = "password";

                icon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }
    </script>
@endpush
