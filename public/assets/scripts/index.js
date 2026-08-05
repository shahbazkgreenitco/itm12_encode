// document.addEventListener('DOMContentLoaded', () => {

//     const miniIcons = document.querySelectorAll('.mini-icon.desktop-only, .mini-icon:not(.mobile-only)');
//     const expandedSidebarsDesktop = document.querySelectorAll('.expanded-sidebar.desktop-only');
//     const expandedSidebarsMobile = document.querySelectorAll('.expanded-sidebar.mobile-only');
//     const mainContent = document.getElementById('mainContent');
//     const mobileOverlay = document.getElementById('mobileOverlay');

//     // Check if we're on mobile
//     function isMobile() {
//         return window.innerWidth <= 768;
//     }

//     function updateMainContentMargin() {
//         if (isMobile()) {
//             // mainContent.style.marginLeft = '0';
//             return;
//         }

//         // Check if any expanded sidebar is visible
//         const hasExpandedSidebar = Array.from(expandedSidebarsDesktop).some(
//             sidebar => !sidebar.classList.contains('hidden')
//         );

//         console.log("hasExpandedSidebar:", hasExpandedSidebar);

//         if (hasExpandedSidebar) {
//             mainContent.classList.add('expanded');
//         } else {
//             mainContent.classList.remove('expanded');
//         }
//         moveExtenderToActiveIcon();
//     }

//     // Add click event to each mini icon
//     // miniIcons.forEach(icon => {
//     //     icon.addEventListener('click', function (e) {
//     //         e.preventDefault();
//     //         const section = this.getAttribute('data-section');
//     //         const isDirect = this.getAttribute('data-direct') === 'true';

//     //         // If it's a direct link (no expanded sidebar), just close all sidebars and navigate
//     //         if (isDirect) {
//     //             closeAllSidebars();
//     //             return;
//     //         }

//     //         if (section) {
//     //             if (isMobile()) {
//     //                 handleMobileClick(section, this);
//     //             } else {
//     //                 handleDesktopClick(section, this);
//     //             }
//     //         }
//     //     });
//     // });

//     miniIcons.forEach(icon => {
//         icon.addEventListener('click', function (e) {

//             const href = this.getAttribute('href');
//             const isDirect = this.getAttribute('data-direct') === 'true';

//             if (href && href !== '#' && !isDirect) {
//                 return; // do NOT preventDefault
//             }

//             e.preventDefault();

//             const section = this.getAttribute('data-section');

//             if (isDirect) {
//                 closeAllSidebars();
//                 return;
//             }

//             if (section) {
//                 if (isMobile()) {
//                     handleMobileClick(section, this);
//                 } else {
//                     handleDesktopClick(section, this);
//                 }
//             }
//         });
//     });



//     function handleDesktopClick(section, clickedIcon) {
//         // Save last opened section
//         lastOpenedSection = section;

//         // Remove active class from all mini icons
//         miniIcons.forEach(i => i.classList.remove('active'));

//         // Add active class to clicked icon
//         clickedIcon.classList.add('active');

//         // Hide all expanded sidebars
//         expandedSidebarsDesktop.forEach(sidebar => {
//             sidebar.classList.add('hidden');
//         });

//         // Show the corresponding expanded sidebar
//         const targetSidebar = document.getElementById(`expanded-${section}`);
//         if (targetSidebar) {
//             targetSidebar.classList.remove('hidden');
//         }

//         updateMainContentMargin();
//         moveExtenderToActiveIcon();
//     }

//     // Handle mobile clicks
//     function handleMobileClick(section, clickedIcon) {
//         const targetSidebar = document.getElementById(`expanded-${section}-mobile`);

//         if (!targetSidebar) return;

//         const isCurrentlyOpen = !targetSidebar.classList.contains('hidden');

//         if (isCurrentlyOpen) {
//             // Close the sidebar if it's already open
//             closeMobileSidebars();
//         } else {
//             // Close all other sidebars first
//             closeMobileSidebars();

//             // Remove active from all icons
//             miniIcons.forEach(i => i.classList.remove('active'));

//             // Add active to clicked icon
//             clickedIcon.classList.add('active');

//             // Open the clicked sidebar
//             targetSidebar.classList.remove('hidden');
//             mobileOverlay.classList.add('show');
//         }
//     }

//     // Close all mobile sidebars
//     function closeMobileSidebars() {
//         expandedSidebarsMobile.forEach(sidebar => {
//             sidebar.classList.add('hidden');
//         });
//         mobileOverlay.classList.remove('show');
//     }

//     function closeAllSidebars() {
//         miniIcons.forEach(i => i.classList.remove('active'));
//         expandedSidebarsDesktop.forEach(sidebar => {
//             sidebar.classList.add('hidden');
//         });
//         expandedSidebarsMobile.forEach(sidebar => {
//             sidebar.classList.add('hidden');
//         });
//         mobileOverlay.classList.remove('show');

//         // Update main content margin
//         updateMainContentMargin();
//     }

//     function restoreDesktopSidebar() {
//         if (!isMobile()) {
//             // Find the first active mini icon
//             const activeIcon = document.querySelector('.mini-icon.active');
//             if (activeIcon) {
//                 const section = activeIcon.getAttribute('data-section');
//                 const targetSidebar = document.getElementById(`expanded-${section}`);
//                 if (targetSidebar) {
//                     targetSidebar.classList.remove('hidden');
//                     updateMainContentMargin();
//                 }
//             }
//         }
//     }



//     // Close mobile sidebar when clicking overlay
//     mobileOverlay.addEventListener('click', function () {
//         closeMobileSidebars();
//     });


//     document.querySelector('.sidebar-extender')
//         .addEventListener('click', () => {
//             document.body.classList.toggle('sidebar-expanded');
//         });

//     let resizeTimer;

//     let wasDesktop = !isMobile();

//     window.addEventListener('resize', function () {
//         clearTimeout(resizeTimer);
//         resizeTimer = setTimeout(function () {
//             const isCurrentlyMobile = isMobile();

//             // Only close sidebars if switching between mobile/desktop
//             if (wasDesktop !== !isCurrentlyMobile) {
//                 closeAllSidebars();
//             }

//             // Update the state
//             wasDesktop = !isCurrentlyMobile;

//             // Update main content margin
//             updateMainContentMargin();
//         }, 250);
//     });


//     // search bar css start

//     // Dropdown functionality
//     const dropdownButton = document.getElementById('dropdownButton');
//     const dropdownMenu = document.getElementById('dropdownMenu');
//     const chevronIcon = document.getElementById('chevronIcon');
//     const selectedFilter = document.getElementById('selectedFilter');
//     const dropdownItems = document.querySelectorAll('.dropdown-item');

//     dropdownButton.addEventListener('click', () => {
//         dropdownMenu.classList.toggle('open');
//         chevronIcon.classList.toggle('open');
//     });

//     dropdownItems.forEach(item => {
//         item.addEventListener('click', () => {
//             selectedFilter.textContent = item.dataset.value;
//             dropdownMenu.classList.remove('open');
//             chevronIcon.classList.remove('open');
//         });
//     });

//     // Close dropdown when clicking outside
//     document.addEventListener('click', (e) => {
//         if (!e.target.closest('.dropdown-section')) {
//             dropdownMenu.classList.remove('open');
//             chevronIcon.classList.remove('open');
//         }
//     });

//     document.querySelector('.sidebar-extender')
//         .addEventListener('click', () => {
//             toggleExpandedSidebar();
//         });

//     function toggleExpandedSidebar() {
//         if (isMobile()) return;

//         const openSidebar = Array.from(expandedSidebarsDesktop)
//             .find(sidebar => !sidebar.classList.contains('hidden'));

//         if (openSidebar) {
//             // 🔒 Collapse sidebar
//             expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
//             document.body.classList.remove('sidebar-expanded');
//             updateMainContentMargin();
//             return;
//         }

//         // 🔓 Re-open last section (fallback to home)
//         const sectionToOpen = lastOpenedSection || 'home';

//         const iconToActivate = document.querySelector(
//             `.mini-icon[data-section="${sectionToOpen}"]`
//         );

//         const sidebarToOpen = document.getElementById(
//             `expanded-${sectionToOpen}`
//         );

//         if (iconToActivate && sidebarToOpen) {
//             miniIcons.forEach(i => i.classList.remove('active'));
//             iconToActivate.classList.add('active');

//             expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
//             sidebarToOpen.classList.remove('hidden');

//             document.body.classList.add('sidebar-expanded');
//             updateMainContentMargin();
//         }

//         moveExtenderToActiveIcon();
//     }

//     // function moveExtenderToActiveIcon() {
//     //     if (isMobile()) return;

//     //     const activeIcon = document.querySelector('.mini-icon.active');
//     //     const extender = document.querySelector('.sidebar-extender');
//     //     const sidebar = document.querySelector('.mini-sidebar.desktop-only');

//     //     if (!activeIcon || !extender || !sidebar) return;

//     //     const iconRect = activeIcon.getBoundingClientRect();
//     //     const sidebarRect = sidebar.getBoundingClientRect();

//     //     // Center extender vertically with the icon
//     //     const offsetY =
//     //         iconRect.top -
//     //         sidebarRect.top +
//     //         iconRect.height / 2 -
//     //         extender.offsetHeight / 2;

//     //     extender.style.transform = `translateY(${offsetY}px)`;
//     // }

//     function moveExtenderToActiveIcon() {
//         if (isMobile()) return;

//         const extender = document.querySelector('.sidebar-extender');
//         const sidebar = document.querySelector('.mini-sidebar.desktop-only');
//         const activeIcon = document.querySelector('.mini-icon.active');

//         if (!extender || !sidebar || !activeIcon) return;

//         const section = activeIcon.getAttribute('data-section');
//         const hasExpandedSidebar = document.getElementById(`expanded-${section}`);

//         // ❌ No expanded sidebar → hide extender
//         if (!hasExpandedSidebar) {
//             extender.style.opacity = '0';
//             extender.style.pointerEvents = 'none';
//             return;
//         }

//         // ✅ Has expanded sidebar → show & align
//         extender.style.opacity = '1';
//         extender.style.pointerEvents = 'auto';

//         const iconRect = activeIcon.getBoundingClientRect();
//         const sidebarRect = sidebar.getBoundingClientRect();

//         const offsetY =
//             iconRect.top -
//             sidebarRect.top +
//             iconRect.height / 2 -
//             extender.offsetHeight / 2;

//         extender.style.transform = `translateY(${offsetY}px)`;
//     }


//     const miniSidebarScroll = document.querySelector('.mini-sidebar-scroll');

//     let scrollRAF = null;

//     if (miniSidebarScroll) {
//         miniSidebarScroll.addEventListener('scroll', () => {
//             if (scrollRAF) return;

//             scrollRAF = requestAnimationFrame(() => {
//                 moveExtenderToActiveIcon();
//                 scrollRAF = null;
//             });
//         });
//     }

//     const submenuParents = document.querySelectorAll(
//         '.expanded-sidebar.desktop-only .has-submenu > a'
//     );

//     submenuParents.forEach(parentLink => {
//         parentLink.addEventListener('click', function (e) {
//             e.preventDefault(); // prevent navigation

//             const parentLi = this.closest('.has-submenu');

//             // Close other submenus (accordion behavior)
//             submenuParents.forEach(link => {
//                 const li = link.closest('.has-submenu');
//                 if (li !== parentLi) {
//                     li.classList.remove('active');
//                 }
//             });

//             // Toggle current submenu
//             parentLi.classList.toggle('active');
//         });
//     });

//     moveExtenderToActiveIcon();

//     // scroll indicators for mini sidebar
//     const sidebar = document.querySelector('.mini-sidebar-scroll');
//     const scrollUp = document.querySelector('.scroll-up');
//     const scrollDown = document.querySelector('.scroll-down');

//     function updateScrollIndicators() {
//         const { scrollTop, scrollHeight, clientHeight } = sidebar;

//         // show up arrow if not at top
//         if (scrollTop > 5) {
//             scrollUp.classList.remove('hidden');
//         } else {
//             scrollUp.classList.add('hidden');
//         }

//         // show down arrow if not at bottom
//         if (scrollTop + clientHeight < scrollHeight - 5) {
//             scrollDown.classList.remove('hidden');
//         } else {
//             scrollDown.classList.add('hidden');
//         }
//     }

//     scrollUp.addEventListener('click', () => {
//         sidebar.scrollBy({ top: -120, behavior: 'smooth' });
//     });

//     scrollDown.addEventListener('click', () => {
//         sidebar.scrollBy({ top: 120, behavior: 'smooth' });
//     });

//     // listen to scroll
//     sidebar.addEventListener('scroll', updateScrollIndicators);

//     // initial check (important)
//     window.addEventListener('load', updateScrollIndicators);


//     // user status toggle

//     const row = document.querySelector('.status-row');
//     const checkbox = row.querySelector('input');
//     const label = row.querySelector('.status-label');

//     checkbox.addEventListener('change', () => {
//         label.textContent = checkbox.checked ? 'Available' : 'Unavailable';
//     });

//     // =================================
//     // Tooltip
//     // =================================
//     const tooltipTriggerList = Array.from(
//         document.querySelectorAll('[data-bs-toggle="tooltip"]')
//     );
//     tooltipTriggerList.forEach((tooltipTriggerEl) => {
//         new bootstrap.Tooltip(tooltipTriggerEl);
//     });

//     // =================================
//     // Popover
//     // =================================
//     var popoverTriggerList = [].slice.call(
//         document.querySelectorAll('[data-bs-toggle="popover"]')
//     );
//     var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
//         return new bootstrap.Popover(popoverTriggerEl);
//     });

// });