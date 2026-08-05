 <div class="ticker">
     <div class="announcement-title">
         <h5>
             <svg width="25" height="25" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path
                     d="M16.5312 27.3125L20.8741 18.7626C20.8975 18.7165 20.9447 18.6875 20.9964 18.6875C21.0495 18.6875 21.0978 18.7181 21.1205 18.7661L25.1562 27.3125M18.6875 25.1562H22.6406M29.1094 27.3125V18.6875M38.4531 28.75H40.25M17.25 38.4531V40.25"
                     stroke="white" stroke-width="2" stroke-linecap="round" />
                 <path d="M7.54688 28.75H5.75" stroke="white" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" />
                 <path d="M17.25 7.1875V5.39062" stroke="white" stroke-width="2" stroke-linecap="round" />
                 <path d="M38.4531 16.8906H40.25" stroke="white" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" />
                 <path
                     d="M28.75 38.4531V40.25M38.4531 23H40.25M22.6406 38.4531V40.25M7.54688 23H5.75M22.6406 7.1875V5.39062"
                     stroke="white" stroke-width="2" stroke-linecap="round" />
                 <path d="M7.54688 16.8906H5.75" stroke="white" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" />
                 <path d="M28.75 7.1875V5.39062" stroke="white" stroke-width="2" stroke-linecap="round" />
                 <path
                     d="M31.2656 11.1406H14.7344C12.7496 11.1406 11.1406 12.7496 11.1406 14.7344V31.2656C11.1406 33.2504 12.7496 34.8594 14.7344 34.8594H31.2656C33.2504 34.8594 34.8594 33.2504 34.8594 31.2656V14.7344C34.8594 12.7496 33.2504 11.1406 31.2656 11.1406Z"
                     stroke="white" stroke-width="2" />
             </svg>
             Announcement
         </h5>
         @if (Auth::user()->roles->pluck('name')->contains('SuperAdmin'))
             <div class="speed-controls">
                 <button class="icon-btn" onclick="increaseSpeed()">
                     <i class="bi bi-plus" data-toggle="tooltip" data-original-title="Increase Speed"
                         data-placement="right"></i>
                 </button>
                 <button class="icon-btn" onclick="decreaseSpeed()">
                     <i class="bi bi-dash" data-toggle="tooltip" data-original-title="Decrease Speed"
                         data-placement="right"></i>
                 </button>
             </div>
         @endif
     </div>
     <div class="announcement">
         {{-- <marquee behavior="scroll" class="announcement-content" width="100%"  scrollamount="5"  id="marquee" onMouseOver="this.stop()" onMouseOut="this.start()"></marquee> --}}

         <div class="marquee" id="marqueeWrapper">
             <div class="marquee-content" id="marqueeContent"></div>
         </div>

     </div>
 </div>
 <style type="text/css">
     .ticker {
        position: relative;
        top: 48px;
        padding-left: 90px;
        left: 0;
        width: 100%;
        height: 34px;
        z-index: 999;
        display: none;
        align-items: center;
        flex-wrap: nowrap;
        background: #000000;
        overflow: hidden;
        transition: height 0.4s ease, opacity 0.3s ease, padding-left 0.3s ease;
    }

    .ticker.expanded {
        padding-left: 310px;
    }

     .announcement-title {
        display: flex;
        align-items: center;
        justify-content: center;
        /* width: 200px; */
        background: #000000;
        position: absolute;
        z-index: 4;
        font-size: 17px;
        gap: 10px;
        font-family: inter;
        height: 30px;
        padding: 0 5px;
        align-items: center;
        margin-top:1px;
    }

     .announcement-title h5 {
         font-size: 14px;
         color: #fff;
         display: flex;
         align-items: center;
         margin: 0;
         font-weight: 500;
     }

     .announcement-title svg {
         margin-right: 6px;
     }

     .announcement-title .icon-btn {
         background: transparent;
         border: none;
         color: #fff;
         font-size: 12px;
         cursor: pointer;
         margin-left: 5px;
         border: 1px solid #2A2A2D;
     }

     .announcement-title .icon-btn:hover {
         color: #ccc;
     }

     .announcement {
         flex: 1;
         height: 30px;
         display: flex;
         align-items: center;
         overflow: hidden;
     }

     .marquee {
         width: 100%;
         overflow: hidden;
         position: relative;
         display: block;
     }

     .marquee-content {
         display: inline-block;
         white-space: nowrap;
         min-width: max-content;
         will-change: transform;
         animation: scrollText linear infinite;
     }

     .announcement-item {
         display: inline-block;
         color: #fff;
         margin-right: 40px;
         font-size: 14px;
         font-family: 'Inter', sans-serif;
     }

     .announcement-icon,
     .meeting-icon {
         margin-right: 6px;
         color: #fff;
     }

     .icon-btn i {
         font-size: 14px;
     }

     .marquee:hover .marquee-content {
         animation-play-state: paused;
     }

     .speed-controls {
         display: flex;
         align-items: center;
         gap: 5px;
     }

     @keyframes scrollText {
         from {
             transform: translateX(100vw);
         }

         to {
             transform: translateX(-100%);
         }
     }

     .announcement-title,
     .announcement,
     .marquee-content {
         white-space: nowrap;
     }
    .icon-btn:disabled,
    .icon-btn:disabled:hover {
        cursor: not-allowed !important;
    }
 </style>

