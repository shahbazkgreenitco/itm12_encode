document.addEventListener('DOMContentLoaded', () => {
    let lastOpenedSection;

    const miniIcons = document.querySelectorAll('.mini-sidebar .mini-icon');
    const expandedSidebarsDesktop = document.querySelectorAll('.expanded-sidebar.desktop-only');
    const expandedSidebarsMobile = document.querySelectorAll('.expanded-sidebar.mobile-only');
    const mainContent = document.getElementById('mainContent');
    const appFooter = document.querySelector('.app-footer');
    const headerActionsWrapper = document.querySelector('.header-actions-wrapper');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const ticker = document.querySelector('.ticker');
    let hoverTimeout = null;
    let pinnedSection = null;

    function saveSidebarState(section) {
        try {
            if (section) {
                localStorage.setItem('sidebar_open_section', section);
                localStorage.setItem('sidebar_manually_closed', '0');
            } else {
                localStorage.setItem('sidebar_manually_closed', '1');
            }
        } catch (e) {}
    }

    // Sidebar search
    document.querySelectorAll('.sidebar-search-input').forEach(input => {
        console.log("calling...");
        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const targetId = this.getAttribute('data-target');
            const menu = document.querySelector(`#${targetId} .expanded-menu`);
            if (!menu) return;

            menu.querySelectorAll(':scope > li').forEach(li => {
                const isSubmenuParent = li.classList.contains('has-submenu');
                const directText = isSubmenuParent
                    ? '' 
                    : li.textContent.toLowerCase();

                if (isSubmenuParent) {
                    const subItems = li.querySelectorAll('.submenu li');
                    let anySubMatch = false;
                    subItems.forEach(subLi => {
                        const match = subLi.textContent.toLowerCase().includes(query);
                        subLi.classList.toggle('search-hidden', query && !match);
                        if (match) anySubMatch = true;
                    });
                    const parentTextMatch = li.querySelector(':scope > a').textContent.toLowerCase().includes(query);
                    const shouldHide = query && !anySubMatch && !parentTextMatch;
                    li.classList.toggle('search-hidden', shouldHide);

                    if (query && anySubMatch) li.classList.add('active');
                } else {
                    li.classList.toggle('search-hidden', query && !directText.includes(query));
                }
            });
        });
    });

    // ---- Global Sidebar Search ----
    const searchTriggerBtn = document.getElementById('globalSearchTrigger');
    const searchOverlay = document.getElementById('globalSearchOverlay');
    const searchCloseBtn = document.getElementById('globalSearchClose');
    const globalSearchInput = document.getElementById('globalSidebarSearch');
    const globalSearchResults = document.getElementById('globalSearchResults');

    function openSearchOverlay() {
        searchOverlay.classList.remove('hidden');
        globalSearchInput.value = '';
        globalSearchResults.classList.add('hidden');
        globalSearchResults.innerHTML = '';
        setTimeout(() => globalSearchInput.focus(), 50);
    }

    function closeSearchOverlay() {
        searchOverlay.classList.add('hidden');
    }

    if (searchTriggerBtn) {
        searchTriggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (searchOverlay.classList.contains('hidden')) {
                openSearchOverlay();
            } else {
                closeSearchOverlay();
            }
        });
    }

    if (searchCloseBtn) {
        searchCloseBtn.addEventListener('click', closeSearchOverlay);
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.global-sidebar-search')) {
            closeSearchOverlay();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSearchOverlay();
    });

    function buildSearchIndex() {
        const items = [];
        document.querySelectorAll('.expanded-sidebar.desktop-only').forEach(sidebar => {
            const sectionId = sidebar.id.replace('expanded-', '');
            const headerEl = sidebar.querySelector('.expanded-sidebar-header');
            const sectionLabel = headerEl ? headerEl.textContent.trim() : sectionId;

            sidebar.querySelectorAll('.expanded-menu a').forEach(link => {
                const text = link.textContent.trim();
                if (!text) return;
                items.push({
                    text,
                    section: sectionId,
                    sectionLabel,
                    href: link.getAttribute('href'),
                    el: link
                });
            });
        });
        return items;
    }

    let searchIndex = null;

    function renderSearchResults(query) {
        if (!searchIndex) searchIndex = buildSearchIndex();

        const q = query.trim().toLowerCase();
        if (!q) {
            globalSearchResults.classList.add('hidden');
            globalSearchResults.innerHTML = '';
            return;
        }

        const matches = searchIndex.filter(item => item.text.toLowerCase().includes(q)).slice(0, 20);

        if (matches.length === 0) {
            globalSearchResults.innerHTML = '<div class="global-search-no-results">No results found</div>';
        } else {
            globalSearchResults.innerHTML = matches.map((item, i) => `
                <div class="global-search-result-item" data-index="${i}">
                    <span>${item.text}</span>
                    <span class="global-search-result-section">${item.sectionLabel}</span>
                </div>
            `).join('');

            globalSearchResults.querySelectorAll('.global-search-result-item').forEach((el, i) => {
                el.addEventListener('click', () => goToSearchResult(matches[i]));
            });
        }

        globalSearchResults.classList.remove('hidden');
    }

    function goToSearchResult(item) {
        const href = item.href;
        const isRealLink = href && href !== '#';

        const targetSidebar = document.getElementById(`expanded-${item.section}`);
        if (targetSidebar) {
            miniIcons.forEach(i => i.classList.remove('active'));
            const iconToActivate = document.querySelector(`.mini-sidebar.desktop-only .mini-icon[data-section="${item.section}"]`);
            if (iconToActivate) iconToActivate.classList.add('active');

            expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
            targetSidebar.classList.remove('hidden');
            document.querySelector('.sidebar-extender')?.classList.add('open');

            lastOpenedSection = item.section;
            pinnedSection = item.section;
            saveSidebarState(item.section);

            updateMainContentMargin();
            moveExtenderToActiveIcon();

            const parentSubmenu = item.el.closest('.has-submenu');
            if (parentSubmenu) parentSubmenu.classList.add('active');

            item.el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            item.el.classList.add('search-flash-highlight');
            setTimeout(() => item.el.classList.remove('search-flash-highlight'), 1200);
        }

        globalSearchInput.value = '';
        globalSearchResults.classList.add('hidden');

        if (isRealLink) {
            window.location.href = href;
        }
    }

    if (globalSearchInput) {
        globalSearchInput.addEventListener('input', () => renderSearchResults(globalSearchInput.value));
        globalSearchInput.addEventListener('focus', () => {
            if (globalSearchInput.value.trim()) renderSearchResults(globalSearchInput.value);
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.global-sidebar-search')) {
            globalSearchResults.classList.add('hidden');
        }
    });

    function getSavedSidebarState() {
        try {
            return {
                section: localStorage.getItem('sidebar_open_section'),
                manuallyClosed: localStorage.getItem('sidebar_manually_closed') === '1'
            };
        } catch (e) {
            return { section: null, manuallyClosed: false };
        }
    }

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function updateMainContentMargin() {
        if (!mainContent) return;

        if (isMobile()) {
            return;
        }


        const hasExpandedSidebar = Array.from(expandedSidebarsDesktop).some(
            sidebar => !sidebar.classList.contains('hidden')
        );

        if (hasExpandedSidebar) {
            mainContent.classList.add('expanded');
            headerActionsWrapper.classList.add('expanded');
            appFooter.classList.add('expanded');
            if (ticker) ticker.classList.add('expanded');

        } else {
            mainContent.classList.remove('expanded');
            headerActionsWrapper.classList.remove('expanded');
            appFooter.classList.remove('expanded');
            if (ticker) ticker.classList.remove('expanded');
        }

        moveExtenderToActiveIcon();
    }

    miniIcons.forEach(icon => {
        icon.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            const isDirect = this.getAttribute('data-direct') === 'true';

            if (href && href !== '#' && !isDirect) {
                return; 
            }

            e.preventDefault();

            const section = this.getAttribute('data-section');

            if (isDirect) {
                closeAllSidebars();
                return;
            }

            if (section) {
                if (isMobile()) {
                    handleMobileClick(section, this);
                } else {
                    handleDesktopClick(section, this);
                }
            }
        });
    });

    function showSidebarForSection(section) {
        if (isMobile()) return;
        const targetSidebar = document.getElementById(`expanded-${section}`);
        if (!targetSidebar) return;

        expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
        targetSidebar.classList.remove('hidden');
        document.querySelector('.sidebar-extender')?.classList.add('open');

        updateMainContentMargin();
        moveExtenderToActiveIcon();
    }

    function revertToPinnedState() {
        if (isMobile()) return;

        if (pinnedSection) {
            showSidebarForSection(pinnedSection);
        } else {
            expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
            document.querySelector('.sidebar-extender')?.classList.remove('open');
            updateMainContentMargin();
            moveExtenderToActiveIcon();
        }
    }

    miniIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function () {
            if (isMobile()) return;
            const section = this.getAttribute('data-section');
            if (!section || !document.getElementById(`expanded-${section}`)) return;
            clearTimeout(hoverTimeout);
            showSidebarForSection(section);
        });

        icon.addEventListener('mouseleave', function () {
            if (isMobile()) return;
            hoverTimeout = setTimeout(revertToPinnedState, 200);
        });
    });

    expandedSidebarsDesktop.forEach(panel => {
        panel.addEventListener('mouseenter', () => clearTimeout(hoverTimeout));
        panel.addEventListener('mouseleave', () => {
            hoverTimeout = setTimeout(revertToPinnedState, 200);
        });
    });

    function handleDesktopClick(section, clickedIcon) {
        const extender = document.querySelector('.sidebar-extender');
        lastOpenedSection = section;

        miniIcons.forEach(i => i.classList.remove('active'));
        clickedIcon.classList.add('active');

        expandedSidebarsDesktop.forEach(sidebar => {
            sidebar.classList.add('hidden');
        });

        const targetSidebar = document.getElementById(`expanded-${section}`);
        if (targetSidebar) {
            targetSidebar.classList.remove('hidden');
            extender?.classList.add('open');  
        }

        updateMainContentMargin();
        moveExtenderToActiveIcon();
        saveSidebarState(section);
        pinnedSection = section;
    }

    function handleMobileClick(section, clickedIcon) {
        const targetSidebar = document.getElementById(`expanded-${section}-mobile`);

        if (!targetSidebar) return;

        const isCurrentlyOpen = !targetSidebar.classList.contains('hidden');

        if (isCurrentlyOpen) {
            closeMobileSidebars();
        } else {
            closeMobileSidebars();

            miniIcons.forEach(i => i.classList.remove('active'));
            clickedIcon.classList.add('active');

            targetSidebar.classList.remove('hidden');
            mobileOverlay.classList.add('show');
        }
    }

    function closeMobileSidebars() {
        expandedSidebarsMobile.forEach(sidebar => {
            sidebar.classList.add('hidden');
        });
        mobileOverlay.classList.remove('show');
    }

    function closeAllSidebars() {
        miniIcons.forEach(i => i.classList.remove('active'));
        expandedSidebarsDesktop.forEach(sidebar => {
            sidebar.classList.add('hidden');
        });
        expandedSidebarsMobile.forEach(sidebar => {
            sidebar.classList.add('hidden');
        });
        mobileOverlay.classList.remove('show');
        updateMainContentMargin();
    }

    function restoreDesktopSidebar() {
        if (isMobile()) return;

        const activeIcon = document.querySelector('.mini-sidebar .mini-icon.active');
        if (activeIcon) {
            const section = activeIcon.getAttribute('data-section');
            if (section) {
                lastOpenedSection = section;
                const targetSidebar = document.getElementById(`expanded-${section}`);
                if (targetSidebar) {
                    expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
                    targetSidebar.classList.remove('hidden');
                }
            }
        }

        updateMainContentMargin();
        moveExtenderToActiveIcon();
    }

    function toggleExpandedSidebar() {
        if (isMobile()) return;

        const extender = document.querySelector('.sidebar-extender');
        const openSidebar = Array.from(expandedSidebarsDesktop)
            .find(sidebar => !sidebar.classList.contains('hidden'));

        if (openSidebar) {
            expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
            extender?.classList.remove('open');
            updateMainContentMargin();
            moveExtenderToActiveIcon();
            saveSidebarState(null);
            pinnedSection = null;
            return;
        }

        const activeIcon = document.querySelector('.mini-sidebar.desktop-only .mini-icon.active');
        const sectionToOpen = (activeIcon ? activeIcon.getAttribute('data-section') : null) || lastOpenedSection;

        if (!sectionToOpen) return;

        const sidebarToOpen = document.getElementById(`expanded-${sectionToOpen}`);
        if (!sidebarToOpen) return;

        miniIcons.forEach(i => i.classList.remove('active'));
        const iconToActivate = document.querySelector(
            `.mini-sidebar.desktop-only .mini-icon[data-section="${sectionToOpen}"]`
        );
        if (iconToActivate) iconToActivate.classList.add('active');

        expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
        sidebarToOpen.classList.remove('hidden');
        extender?.classList.add('open');  
        lastOpenedSection = sectionToOpen;
        saveSidebarState(sectionToOpen);

        updateMainContentMargin();
        moveExtenderToActiveIcon();
        pinnedSection = sectionToOpen;
    }

    function moveExtenderToActiveIcon() {
        if (isMobile()) return;

        const extender = document.querySelector('.sidebar-extender');
        const sidebar = document.querySelector('.mini-sidebar.desktop-only');
        const activeIcon = document.querySelector('.mini-sidebar .mini-icon.active');

        if (!extender || !sidebar) return;

        if (!activeIcon) {
            extender.style.opacity = '0';
            extender.style.pointerEvents = 'none';
            return;
        }

        const section = activeIcon.getAttribute('data-section');
        const hasExpandedSidebar = document.getElementById(`expanded-${section}`);

        if (!hasExpandedSidebar) {
            extender.style.opacity = '0';
            extender.style.pointerEvents = 'none';
            return;
        }

        extender.style.opacity = '1';
        extender.style.pointerEvents = 'auto';

        const iconRect = activeIcon.getBoundingClientRect();
        const sidebarRect = sidebar.getBoundingClientRect();

        const offsetY =
            iconRect.top -
            sidebarRect.top +
            iconRect.height / 2 -
            extender.offsetHeight / 2;

        extender.style.transform = `translateY(${offsetY}px)`;
    }

    const extenderBtn = document.querySelector('.sidebar-extender');
    if (extenderBtn) {
        extenderBtn.addEventListener('click', () => {
            toggleExpandedSidebar();
        });
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
            closeMobileSidebars();
        });
    }

    let resizeTimer;
    let wasDesktop = !isMobile();

    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            const isCurrentlyMobile = isMobile();

            if (wasDesktop !== !isCurrentlyMobile) {
                closeAllSidebars();
            }

            wasDesktop = !isCurrentlyMobile;
            updateMainContentMargin();
        }, 250);
    });

    const miniSidebarScroll = document.querySelector('.mini-sidebar-scroll');
    let scrollRAF = null;

    if (miniSidebarScroll) {
        miniSidebarScroll.addEventListener('scroll', () => {
            if (scrollRAF) return;
            scrollRAF = requestAnimationFrame(() => {
                moveExtenderToActiveIcon();
                scrollRAF = null;
            });
        });
    }

    const submenuParents = document.querySelectorAll(
        '.expanded-sidebar.desktop-only .has-submenu > a'
    );

    submenuParents.forEach(parentLink => {
        parentLink.addEventListener('click', function (e) {
            e.preventDefault();

            const parentLi = this.closest('.has-submenu');

            submenuParents.forEach(link => {
                const li = link.closest('.has-submenu');
                if (li !== parentLi) {
                    li.classList.remove('active');
                }
            });

            parentLi.classList.toggle('active');
        });
    });

    // Scroll indicators
    const scrollSidebar = document.querySelector('.mini-sidebar-scroll');
    const scrollUp = document.querySelector('.scroll-up');
    const scrollDown = document.querySelector('.scroll-down');

    function updateScrollIndicators() {
        if (!scrollSidebar || !scrollUp || !scrollDown) return;
        const { scrollTop, scrollHeight, clientHeight } = scrollSidebar;

        if (scrollTop > 5) {
            scrollUp.classList.remove('hidden');
        } else {
            scrollUp.classList.add('hidden');
        }

        if (scrollTop + clientHeight < scrollHeight - 5) {
            scrollDown.classList.remove('hidden');
        } else {
            scrollDown.classList.add('hidden');
        }
    }

    if (scrollUp) {
        scrollUp.addEventListener('click', () => {
            scrollSidebar.scrollBy({ top: -120, behavior: 'smooth' });
        });
    }

    if (scrollDown) {
        scrollDown.addEventListener('click', () => {
            scrollSidebar.scrollBy({ top: 120, behavior: 'smooth' });
        });
    }

    if (scrollSidebar) {
        scrollSidebar.addEventListener('scroll', updateScrollIndicators);
    }

    window.addEventListener('load', updateScrollIndicators);

    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const chevronIcon = document.getElementById('chevronIcon');
    const selectedFilter = document.getElementById('selectedFilter');
    const dropdownItems = document.querySelectorAll('.dropdown-item');

    if (dropdownButton) {
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('open');
            chevronIcon.classList.toggle('open');
        });
    }

    dropdownItems.forEach(item => {
        item.addEventListener('click', () => {
            selectedFilter.textContent = item.dataset.value;
            dropdownMenu.classList.remove('open');
            chevronIcon.classList.remove('open');
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown-section')) {
            if (dropdownMenu) dropdownMenu.classList.remove('open');
            if (chevronIcon) chevronIcon.classList.remove('open');
        }
    });

    const statusRow = document.querySelector('.status-row');
    if (statusRow) {
        const checkbox = statusRow.querySelector('input');
        const label = statusRow.querySelector('.status-label');
        if (checkbox && label) {
            checkbox.addEventListener('change', () => {
                label.textContent = checkbox.checked ? 'Available' : 'Unavailable';
            });
        }
    }

    // Tooltips
    const tooltipTriggerList = Array.from(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.forEach((el) => {
        new bootstrap.Tooltip(el);
    });

    // amg page-length dropdown style fix
    document.addEventListener('DOMContentLoaded', function () {
        $('.amg-table-pagination-dropdown').select2({
            width: '100%'
        });
    });

    const popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    popoverTriggerList.map((el) => new bootstrap.Popover(el));

    document.querySelectorAll('.expanded-menu .submenu li.active-link').forEach(activeLi => {
        const parent = activeLi.closest('.has-submenu');
        if (parent) parent.classList.add('active');
    });

    const saved = getSavedSidebarState();
    const activeIcon = document.querySelector('.mini-sidebar.desktop-only .mini-icon.active');
    const activeSection = activeIcon ? activeIcon.getAttribute('data-section') : null;

    if (!isMobile() && activeSection && !saved.manuallyClosed) {
        lastOpenedSection = activeSection;
        pinnedSection = activeSection;
        const targetSidebar = document.getElementById(`expanded-${activeSection}`);
        if (targetSidebar) {
            expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
            targetSidebar.classList.remove('hidden');
            document.querySelector('.sidebar-extender')?.classList.add('open');
        }
    } else {
        expandedSidebarsDesktop.forEach(s => s.classList.add('hidden'));
        document.querySelector('.sidebar-extender')?.classList.remove('open');
    }

    updateMainContentMargin();
    moveExtenderToActiveIcon();
    updateScrollIndicators();
});